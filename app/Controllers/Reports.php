<?php

namespace App\Controllers;

class Reports extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_team_members();
    }

    public function index() {
        $redirect_to = "";
        $reports = get_reports_topbar(true);
        $count = 1;
        foreach ($reports as $report) {
            if ($count == 1) {
                if (get_array_value($report, "single_button") == 1) {
                    $redirect_to = get_array_value($report, "url");
                } else {
                    foreach (get_array_value($report, "dropdown_item") as $sub_page) {
                        if ($count == 1) {
                            $redirect_to = get_array_value($sub_page, "url");
                        }
                        $count++;
                    }
                }
            } else {
                continue;
            }
            $count++;
        }

        $view_data["redirect_to"] = $redirect_to;
        return $this->template->rander("reports/index", $view_data);
    }

    public function lead_conversion() {
        $this->_validate_lead_conversion_access();

        $view_data["owners_dropdown"] = json_encode($this->_get_lead_conversion_owners_dropdown());
        $view_data["regions_dropdown"] = json_encode($this->_get_lead_conversion_regions_dropdown());
        $view_data["sources_dropdown"] = json_encode($this->_get_lead_conversion_sources_dropdown());
        $view_data["statuses_dropdown"] = json_encode($this->_get_lead_conversion_status_dropdown());

        return $this->template->rander("reports/lead_conversion", $view_data);
    }

    public function lead_conversion_data() {
        $this->_validate_lead_conversion_access();

        $options = array(
            "owner_id" => $this->request->getPost("owner_id"),
            "region_id" => $this->request->getPost("region_id"),
            "source_value" => $this->request->getPost("source_value"),
            "lead_status_id" => $this->request->getPost("lead_status_id"),
            "created_start_date" => $this->request->getPost("created_start_date"),
            "created_end_date" => $this->request->getPost("created_end_date"),
            "migration_start_date" => $this->request->getPost("migration_start_date"),
            "migration_end_date" => $this->request->getPost("migration_end_date")
        );

        $list_data = $this->Clients_model->get_lead_conversion_report_details($options)->getResult();

        $result = array();
        foreach ($list_data as $data) {
            $total_leads = floatval($data->total_leads);
            $conversions = floatval($data->conversions);

            if (!$total_leads && !$conversions) {
                continue;
            }

            $result[] = $this->_make_lead_conversion_row($data, $total_leads, $conversions);
        }

        echo json_encode(array("data" => $result));
    }

    private function _get_lead_conversion_owners_dropdown() {
        $team_members = $this->Users_model->get_all_where(array("user_type" => "staff", "deleted" => 0, "status" => "active"))->getResult();
        $dropdown = array(array("id" => "", "text" => "- " . app_lang("owner") . " -"));

        foreach ($team_members as $member) {
            $dropdown[] = array("id" => $member->id, "text" => trim($member->first_name . " " . $member->last_name));
        }

        return $dropdown;
    }

    private function _get_lead_conversion_regions_dropdown() {
        $regions = $this->Lead_source_model->get_details()->getResult();
        $dropdown = array(array("id" => "", "text" => "- " . app_lang("region") . " -"));

        foreach ($regions as $region) {
            $dropdown[] = array("id" => $region->id, "text" => $region->title);
        }

        return $dropdown;
    }

    private function _get_lead_conversion_sources_dropdown() {
        $sources = $this->Clients_model->get_lead_conversion_source_values()->getResult();
        $dropdown = array(array("id" => "", "text" => "- " . app_lang("source") . " -"));

        foreach ($sources as $source) {
            $dropdown[] = array("id" => $source->value, "text" => $source->value);
        }

        return $dropdown;
    }

    private function _get_lead_conversion_status_dropdown() {
        $statuses = $this->Lead_status_model->get_details()->getResult();
        $dropdown = array(array("id" => "", "text" => "- " . app_lang("lead_status") . " -"));

        foreach ($statuses as $status) {
            $dropdown[] = array("id" => $status->id, "text" => $status->title);
        }

        return $dropdown;
    }

    private function _make_lead_conversion_row($data, $total_leads, $conversions) {
        $source = $data->source_value ? $data->source_value : app_lang("unknown");

        $owner_name = trim($data->owner_name);
        if ($data->owner_id) {
            $owner = get_team_member_profile_link($data->owner_id, $owner_name ? $owner_name : app_lang("unknown"));
        } else {
            $owner = app_lang("unknown");
        }

        $region = $data->region_name ? $data->region_name : app_lang("unknown");

        $conversion_rate = 0;
        if ($total_leads > 0) {
            $conversion_rate = ($conversions / $total_leads) * 100;
        }

        $average_time = "-";
        if ($data->avg_conversion_time !== null && $data->avg_conversion_time !== "") {
            $average_time = to_decimal_format(floatval($data->avg_conversion_time)) . " " . app_lang("days");
        }

        return array(
            $source,
            $owner,
            $region,
            to_decimal_format($total_leads),
            to_decimal_format($conversions),
            to_decimal_format($conversion_rate) . "%",
            $average_time
        );
    }

    private function _validate_lead_conversion_access() {
        if (get_setting("module_lead") != "1") {
            app_redirect("forbidden");
        }

        $lead_permission = get_array_value($this->login_user->permissions, "lead");
        if (!($this->login_user->is_admin || $lead_permission === "all")) {
            app_redirect("forbidden");
        }
    }
}

/* End of file Reports.php */
/* Location: ./app/controllers/Reports.php */
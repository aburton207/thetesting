<?php

namespace App\Controllers;

class Lead_conversion_reports extends Security_Controller {

    public function __construct() {
        parent::__construct();
        $this->access_only_team_members();
    }

    public function index() {
        $this->_validate_lead_conversion_access();

        $filters = $this->_get_lead_conversion_filters();

        $view_data["owners_dropdown"] = json_encode($this->_get_lead_conversion_owners_dropdown(get_array_value($filters, "owner_id")));
        $view_data["regions_dropdown"] = json_encode($this->_get_lead_conversion_regions_dropdown(get_array_value($filters, "region_id")));
        $view_data["sources_dropdown"] = json_encode($this->_get_lead_conversion_sources_dropdown(get_array_value($filters, "source_value")));
        $view_data["statuses_dropdown"] = json_encode($this->_get_lead_conversion_status_dropdown(get_array_value($filters, "lead_status_id")));

        return $this->template->rander("lead_conversion_reports/index", $view_data);
    }

    public function data() {
        $this->_validate_lead_conversion_access();

        $options = $this->_get_lead_conversion_filters();

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

    public function client_timeline() {
        $this->_validate_lead_conversion_access();

        $filters = $this->_get_lead_conversion_filters();

        $timeline_data = $this->Clients_model->get_client_conversion_timeline($filters);

        if ($this->request->getPost("datatable")) {
            $rows = array();
            $clients = get_array_value($timeline_data, "clients");

            if ($clients) {
                foreach ($clients as $client) {
                    $rows[] = $this->_make_client_timeline_row($client);
                }
            }

            $timeline = get_array_value($timeline_data, "timeline");
            if (!$timeline) {
                $timeline = array(
                    "labels" => array(),
                    "values" => array(),
                    "cumulative" => array()
                );
            }

            echo json_encode(array(
                "data" => $rows,
                "timeline" => $timeline
            ));
            return;
        }

        $timeline = get_array_value($timeline_data, "timeline");
        if (!$timeline) {
            $timeline = array(
                "labels" => array(),
                "values" => array(),
                "cumulative" => array()
            );
        }

        $labels = ($timeline && isset($timeline["labels"])) ? $timeline["labels"] : array();
        $values = ($timeline && isset($timeline["values"])) ? $timeline["values"] : array();
        $cumulative = ($timeline && isset($timeline["cumulative"])) ? $timeline["cumulative"] : array();

        $view_data["owners_dropdown"] = json_encode($this->_get_lead_conversion_owners_dropdown(get_array_value($filters, "owner_id")));
        $view_data["regions_dropdown"] = json_encode($this->_get_lead_conversion_regions_dropdown(get_array_value($filters, "region_id")));
        $view_data["sources_dropdown"] = json_encode($this->_get_lead_conversion_sources_dropdown(get_array_value($filters, "source_value")));
        $view_data["statuses_dropdown"] = json_encode($this->_get_lead_conversion_status_dropdown(get_array_value($filters, "lead_status_id")));
        $view_data["timeline_labels"] = json_encode($labels);
        $view_data["timeline_values"] = json_encode($values);
        $view_data["timeline_cumulative"] = json_encode($cumulative);

        return $this->template->rander("lead_conversion_reports/client_timeline", $view_data);
    }

    public function rep_conversion_rates() {
        $this->_validate_lead_conversion_access();

        $filters = $this->_get_lead_conversion_filters();

        if ($this->request->getPost("datatable")) {
            $report_data = $this->_get_rep_conversion_rates_data($filters);

            echo json_encode(array(
                "data" => get_array_value($report_data, "rows", array()),
                "chart" => get_array_value($report_data, "chart", array())
            ));
            return;
        }

        $initial_report = $this->_get_rep_conversion_rates_data($filters);
        $chart = get_array_value($initial_report, "chart", array());

        $view_data["owners_dropdown"] = json_encode($this->_get_lead_conversion_owners_dropdown(get_array_value($filters, "owner_id")));
        $view_data["regions_dropdown"] = json_encode($this->_get_lead_conversion_regions_dropdown(get_array_value($filters, "region_id")));
        $view_data["sources_dropdown"] = json_encode($this->_get_lead_conversion_sources_dropdown(get_array_value($filters, "source_value")));
        $view_data["statuses_dropdown"] = json_encode($this->_get_lead_conversion_status_dropdown(get_array_value($filters, "lead_status_id")));
        $view_data["chart_labels"] = json_encode(get_array_value($chart, "labels", array()));
        $view_data["chart_rates"] = json_encode(get_array_value($chart, "rates", array()));
        $view_data["chart_conversions"] = json_encode(get_array_value($chart, "conversions", array()));
        $view_data["chart_total_leads"] = json_encode(get_array_value($chart, "total_leads", array()));

        return $this->template->rander("lead_conversion_reports/rep_conversion_rates", $view_data);
    }

    private function _get_lead_conversion_filters() {
        return array(
            "owner_id" => $this->_get_filter_value("owner_id"),
            "region_id" => $this->_get_filter_value("region_id"),
            "source_value" => $this->_get_filter_value("source_value"),
            "lead_status_id" => $this->_get_filter_value("lead_status_id"),
            "created_start_date" => $this->_get_filter_value("created_start_date"),
            "created_end_date" => $this->_get_filter_value("created_end_date"),
            "migration_start_date" => $this->_get_filter_value("migration_start_date"),
            "migration_end_date" => $this->_get_filter_value("migration_end_date")
        );
    }

    private function _get_filter_value($key) {
        $value = $this->request->getPost($key);

        if ($value === null) {
            $value = $this->request->getGet($key);
        }

        return $value;
    }

    private function _get_lead_conversion_owners_dropdown($selected_owner_id = null) {
        $team_members = $this->Users_model->get_all_where(array("user_type" => "staff", "deleted" => 0, "status" => "active"))->getResult();
        $dropdown = array(array(
            "id" => "",
            "text" => "- " . app_lang("owner") . " -",
            "isSelected" => ($selected_owner_id === null || $selected_owner_id === "")
        ));

        foreach ($team_members as $member) {
            $dropdown[] = array(
                "id" => $member->id,
                "text" => trim($member->first_name . " " . $member->last_name),
                "isSelected" => ($selected_owner_id !== null && $selected_owner_id !== "" && intval($selected_owner_id) === intval($member->id))
            );
        }

        return $dropdown;
    }

    private function _get_lead_conversion_regions_dropdown($selected_region_id = null) {
        $regions = $this->Lead_source_model->get_details()->getResult();
        $dropdown = array(array(
            "id" => "",
            "text" => "- " . app_lang("region") . " -",
            "isSelected" => ($selected_region_id === null || $selected_region_id === "")
        ));

        foreach ($regions as $region) {
            $dropdown[] = array(
                "id" => $region->id,
                "text" => $region->title,
                "isSelected" => ($selected_region_id !== null && $selected_region_id !== "" && intval($selected_region_id) === intval($region->id))
            );
        }

        return $dropdown;
    }

    private function _get_lead_conversion_sources_dropdown($selected_source_value = null) {
        $sources = $this->Clients_model->get_lead_conversion_source_values()->getResult();
        $selected_source_value = $selected_source_value !== null ? trim($selected_source_value) : $selected_source_value;

        $dropdown = array(array(
            "id" => "",
            "text" => "- " . app_lang("campaign") . " -",
            "isSelected" => ($selected_source_value === null || $selected_source_value === "")
        ));

        foreach ($sources as $source) {
            $value = trim($source->value);

            $dropdown[] = array(
                "id" => $value,
                "text" => $value,
                "isSelected" => ($selected_source_value !== null && $selected_source_value !== "" && $selected_source_value === $value)
            );
        }

        return $dropdown;
    }

    private function _get_lead_conversion_status_dropdown($selected_status_id = null) {
        $statuses = $this->Lead_status_model->get_details()->getResult();
        $dropdown = array(array(
            "id" => "",
            "text" => "- " . app_lang("lead_status") . " -",
            "isSelected" => ($selected_status_id === null || $selected_status_id === "")
        ));

        foreach ($statuses as $status) {
            $dropdown[] = array(
                "id" => $status->id,
                "text" => $status->title,
                "isSelected" => ($selected_status_id !== null && $selected_status_id !== "" && intval($selected_status_id) === intval($status->id))
            );
        }

        return $dropdown;
    }

    private function _make_lead_conversion_row($data, $total_leads, $conversions) {
        $owner_name = trim($data->owner_name);
        if ($data->owner_id) {
            $owner = get_team_member_profile_link($data->owner_id, $owner_name ? $owner_name : app_lang("unknown"));
        } else {
            $owner = app_lang("unknown");
        }

        $conversion_rate = 0;
        if ($total_leads > 0) {
            $conversion_rate = ($conversions / $total_leads) * 100;
        }

        $average_time = "-";
        if ($data->avg_conversion_time !== null && $data->avg_conversion_time !== "") {
            $average_time = to_decimal_format(floatval($data->avg_conversion_time)) . " " . app_lang("days");
        }

        return array(
            $owner,
            to_decimal_format($total_leads),
            to_decimal_format($conversions),
            to_decimal_format($conversion_rate) . "%",
            $average_time
        );
    }

    private function _make_client_timeline_row($data) {
        $client_name = $data->company_name ? $data->company_name : app_lang("unknown");
        if ($data->id) {
            $client_name = anchor(get_uri("clients/view/" . $data->id), $client_name);
        }

        $migration_date = $data->client_migration_date ? format_to_date($data->client_migration_date, false) : "-";

        $owner_name = trim($data->owner_name);
        if ($data->owner_id) {
            $owner = get_team_member_profile_link($data->owner_id, $owner_name ? $owner_name : app_lang("unknown"));
        } else {
            $owner = app_lang("unknown");
        }

        $region = $data->region_name ? $data->region_name : app_lang("unknown");
        $source = $data->source_value ? $data->source_value : app_lang("unknown");
        $status = $data->status_title ? $data->status_title : app_lang("unknown");

        return array(
            $client_name,
            $migration_date,
            $owner,
            $region,
            $source,
            $status
        );
    }

    private function _get_rep_conversion_rates_data($filters = array()) {
        $report = array(
            "rows" => array(),
            "chart" => array(
                "labels" => array(),
                "rates" => array(),
                "conversions" => array(),
                "total_leads" => array()
            )
        );

        $list = $this->Clients_model->get_rep_conversion_rates($filters)->getResult();

        foreach ($list as $data) {
            $total_leads = floatval($data->total_leads);
            $conversions = floatval($data->conversions);

            if (!$total_leads && !$conversions) {
                continue;
            }

            $conversion_rate = floatval($data->conversion_rate);

            $owner_name = trim($data->owner_name);
            if (!$owner_name) {
                $owner_name = app_lang("unknown");
            }

            $owner = $owner_name;
            if ($data->owner_id) {
                $owner = get_team_member_profile_link($data->owner_id, $owner_name);
            }

            $average_time = "-";
            if ($data->avg_conversion_time !== null && $data->avg_conversion_time !== "") {
                $average_time = to_decimal_format(floatval($data->avg_conversion_time)) . " " . app_lang("days");
            }

            $report["rows"][] = array(
                $owner,
                to_decimal_format($total_leads),
                to_decimal_format($conversions),
                to_decimal_format($conversion_rate) . "%",
                $average_time
            );

            $report["chart"]["labels"][] = $owner_name;
            $report["chart"]["rates"][] = round($conversion_rate, 2);
            $report["chart"]["conversions"][] = $conversions;
            $report["chart"]["total_leads"][] = $total_leads;
        }

        return $report;
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

/* End of file Lead_conversion_reports.php */
/* Location: ./app/controllers/Lead_conversion_reports.php */

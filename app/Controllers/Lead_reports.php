<?php

namespace App\Controllers;

class Lead_reports extends Security_Controller {

    public function __construct() {
        parent::__construct();
        $this->access_only_team_members();
    }

    public function lead_label_summary() {
        $this->_validate_lead_access();

        $selected_source = $this->_get_filter_value("source_value");
        $view_data["sources_dropdown"] = json_encode($this->_get_sources_dropdown($selected_source));

        return $this->template->rander("lead_reports/lead_label_summary", $view_data);
    }

    public function lead_label_summary_list() {
        $this->_validate_lead_access();

        $filters = array(
            "source_value" => $this->_get_filter_value("source_value"),
            "start_date" => $this->_get_filter_value("start_date"),
            "end_date" => $this->_get_filter_value("end_date")
        );

        $summary = $this->Clients_model->get_lead_label_summary($filters);
        $label_counts = get_array_value($summary, "label_counts", array());
        $no_label_count = intval(get_array_value($summary, "no_label_count", 0));

        $rows = array();
        if ($label_counts) {
            foreach ($label_counts as $label_info) {
                $total = isset($label_info->total_count) ? intval($label_info->total_count) : 0;

                $rows[] = array(
                    $label_info->label_title,
                    to_decimal_format($total)
                );
            }
        }

        $rows[] = array(app_lang("no_label"), to_decimal_format($no_label_count));

        echo json_encode(array("data" => $rows));
    }

    private function _get_sources_dropdown($selected_source_value = null) {
        $sources = $this->Clients_model->get_lead_conversion_source_values(238)->getResult();
        $selected_source_value = $selected_source_value !== null ? trim($selected_source_value) : $selected_source_value;

        $dropdown = array(array(
            "id" => "",
            "text" => "- " . app_lang("source") . " -",
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

    private function _get_filter_value($key) {
        $value = $this->request->getPost($key);

        if ($value === null) {
            $value = $this->request->getGet($key);
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        return $value;
    }

    private function _validate_lead_access() {
        if (get_setting("module_lead") != "1") {
            app_redirect("forbidden");
        }

        $lead_permission = get_array_value($this->login_user->permissions, "lead");
        if (!($this->login_user->is_admin || $lead_permission === "all")) {
            app_redirect("forbidden");
        }
    }
}

/* End of file Lead_reports.php */
/* Location: ./app/controllers/Lead_reports.php */

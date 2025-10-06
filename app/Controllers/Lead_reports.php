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
        $view_data["sources_dropdown"] = json_encode($this->_get_sources_dropdown(
                        $selected_source,
                        "source",
                        \App\Models\Clients_model::LEAD_SOURCE_CUSTOM_FIELD_ID
        ));

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
        $no_label_all_time_count = intval(get_array_value($summary, "no_label_all_time_count", 0));

        $rows = array();
        $filtered_total_sum = 0;
        $all_time_total_sum = 0;

        if ($label_counts) {
            foreach ($label_counts as $label_info) {
                $total = isset($label_info->total_count) ? intval($label_info->total_count) : 0;
                $all_time_total = isset($label_info->all_time_count) ? intval($label_info->all_time_count) : 0;

                if ($total === 0 && $all_time_total === 0) {
                    continue;
                }

                $rows[] = array(
                    $label_info->label_title,
                    to_decimal_format($total),
                    to_decimal_format($all_time_total)
                );

                $filtered_total_sum += $total;
                $all_time_total_sum += $all_time_total;
            }
        }

        if ($no_label_count > 0 || $no_label_all_time_count > 0) {
            $rows[] = array(
                app_lang("no_label"),
                to_decimal_format($no_label_count),
                to_decimal_format($no_label_all_time_count)
            );

            $filtered_total_sum += $no_label_count;
            $all_time_total_sum += $no_label_all_time_count;
        }

        $response = array(
            "data" => $rows,
            "total_filtered" => to_decimal_format($filtered_total_sum),
            "total_all_time" => to_decimal_format($all_time_total_sum),
            "date_range_label" => $this->_get_date_range_label($filters["start_date"], $filters["end_date"])
        );

        echo json_encode($response);
    }

    public function campaign_pipeline() {
        $this->_validate_lead_access();

        $selected_campaign = $this->_get_filter_value("campaign");
        $campaign_field_ids = array(
            \App\Models\Clients_model::LEAD_SOURCE_CUSTOM_FIELD_ID,
            \App\Models\Clients_model::CLIENT_SOURCE_CUSTOM_FIELD_ID
        );

        $view_data["campaigns_dropdown"] = json_encode($this->_get_sources_dropdown(
                                $selected_campaign,
                                "campaign",
                                $campaign_field_ids
        ));

        return $this->template->rander("lead_reports/campaign_pipeline", $view_data);
    }

    public function campaign_pipeline_summary_list() {
        $this->_validate_lead_access();

        $campaign = $this->_get_filter_value("campaign");
        $summary = $this->Clients_model->get_campaign_pipeline_summary(array(
                    "campaign" => $campaign
        ));

        $rows = array();
        $status_rows = get_array_value($summary, "rows", array());

        if ($status_rows) {
            foreach ($status_rows as $status_row) {
                $lead_count = intval(get_array_value($status_row, "leads_count", 0));
                $client_count = intval(get_array_value($status_row, "clients_count", 0));
                $total = $lead_count + $client_count;

                if ($total === 0) {
                    continue;
                }

                $rows[] = array(
                    get_array_value($status_row, "status_title", app_lang("not_specified")),
                    to_decimal_format($lead_count),
                    to_decimal_format($client_count),
                    to_decimal_format($total)
                );
            }
        }

        $no_status = get_array_value($summary, "no_status", array());
        $no_status_leads = intval(get_array_value($no_status, "leads", 0));
        $no_status_clients = intval(get_array_value($no_status, "clients", 0));

        if ($no_status_leads > 0 || $no_status_clients > 0) {
            $rows[] = array(
                app_lang("not_specified"),
                to_decimal_format($no_status_leads),
                to_decimal_format($no_status_clients),
                to_decimal_format($no_status_leads + $no_status_clients)
            );
        }

        $totals = get_array_value($summary, "totals", array());
        $total_leads = intval(get_array_value($totals, "leads", 0));
        $total_clients = intval(get_array_value($totals, "clients", 0));

        $response = array(
            "data" => $rows,
            "total_leads" => to_decimal_format($total_leads),
            "total_clients" => to_decimal_format($total_clients),
            "total_combined" => to_decimal_format($total_leads + $total_clients)
        );

        echo json_encode($response);
    }

    public function campaign_pipeline_breakdown_list() {
        $this->_validate_lead_access();

        $campaign = $this->_get_filter_value("campaign");
        $results = $this->Clients_model->get_campaign_pipeline_breakdown(array(
                    "campaign" => $campaign
        ))->getResult();

        $rows = array();
        $total_leads = 0;
        $total_clients = 0;

        if ($results) {
            foreach ($results as $result) {
                $lead_count = isset($result->leads_count) ? intval($result->leads_count) : 0;
                $client_count = isset($result->clients_count) ? intval($result->clients_count) : 0;
                $total = $lead_count + $client_count;

                if ($total === 0) {
                    continue;
                }

                $campaign_label = trim(get_array_value((array) $result, "campaign", ""));
                $campaign_label = $campaign_label !== "" ? $campaign_label : app_lang("not_specified");

                $owner_name = trim(trim(get_array_value((array) $result, "first_name", "")) . " " . trim(get_array_value((array) $result, "last_name", "")));
                $owner_name = $owner_name !== "" ? $owner_name : app_lang("not_specified");

                $status_title = get_array_value((array) $result, "status_title");
                $status_title = $status_title ? $status_title : app_lang("not_specified");

                $rows[] = array(
                    $campaign_label,
                    $owner_name,
                    $status_title,
                    to_decimal_format($lead_count),
                    to_decimal_format($client_count),
                    to_decimal_format($total)
                );

                $total_leads += $lead_count;
                $total_clients += $client_count;
            }
        }

        $response = array(
            "data" => $rows,
            "total_leads" => to_decimal_format($total_leads),
            "total_clients" => to_decimal_format($total_clients),
            "total_combined" => to_decimal_format($total_leads + $total_clients)
        );

        echo json_encode($response);
    }

    private function _get_date_range_label($start_date, $end_date) {
        $start_date = $start_date ? trim($start_date) : "";
        $end_date = $end_date ? trim($end_date) : "";

        if ($start_date && $end_date) {
            return \format_to_date($start_date, false) . " - " . \format_to_date($end_date, false);
        }

        return "";
    }

    private function _get_sources_dropdown($selected_source_value = null, $placeholder_lang_key = "source", $field_ids = null) {
        $sources = $this->Clients_model->get_lead_conversion_source_values($field_ids)->getResult();
        $selected_source_value = $selected_source_value !== null ? trim($selected_source_value) : $selected_source_value;

        $dropdown = array(array(
            "id" => "",
            "text" => "- " . app_lang($placeholder_lang_key) . " -",
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

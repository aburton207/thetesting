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

        $selected_campaigns = $this->_normalize_campaign_filter_values($this->_get_filter_value("campaign"));
        $campaign_field_ids = array(
            \App\Models\Clients_model::LEAD_SOURCE_CUSTOM_FIELD_ID,
            \App\Models\Clients_model::CLIENT_SOURCE_CUSTOM_FIELD_ID
        );

        $view_data["campaigns_dropdown"] = json_encode($this->_get_sources_dropdown(
                                $selected_campaigns,
                                "campaign",
                                $campaign_field_ids
        ));

        $view_data["campaign_status_columns"] = json_encode($this->Clients_model->get_campaign_pipeline_status_definitions());
        $view_data["selected_campaigns"] = json_encode($selected_campaigns);

        return $this->template->rander("lead_reports/campaign_pipeline", $view_data);
    }

    public function campaign_pipeline_summary_list() {
        $this->_validate_lead_access();

        $campaigns = $this->_normalize_campaign_filter_values($this->_get_filter_value("campaign"));
        $summary = $this->Clients_model->get_campaign_pipeline_summary(array(
                    "campaign" => $campaigns
        ));

        $status_definitions = get_array_value($summary, "status_definitions", array());
        $campaign_rows = get_array_value($summary, "campaigns", array());

        $rows = array();

        if ($campaign_rows) {
            foreach ($campaign_rows as $campaign_row) {
                $row = array(get_array_value($campaign_row, "label", app_lang("not_specified")));

                if ($status_definitions) {
                    foreach ($status_definitions as $definition) {
                        $counts = get_array_value($campaign_row, "counts", array());
                        $value = intval(get_array_value($counts, get_array_value($definition, "key"), 0));
                        $row[] = to_decimal_format($value);
                    }
                }

                $row[] = to_decimal_format(intval(get_array_value($campaign_row, "total", 0)));
                $rows[] = $row;
            }
        }

        $response = array(
            "data" => $rows
        );

        echo json_encode($response);
    }

    public function campaign_pipeline_breakdown_list() {
        $this->_validate_lead_access();

        $campaigns = $this->_normalize_campaign_filter_values($this->_get_filter_value("campaign"));
        $response = $this->Clients_model->get_campaign_pipeline_breakdown(array(
                    "campaign" => $campaigns
        ));

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

        $selected_values = array();
        if (is_array($selected_source_value)) {
            foreach ($selected_source_value as $value) {
                if ($value === null) {
                    continue;
                }
                $selected_values[] = trim($value);
            }
        } elseif ($selected_source_value !== null && $selected_source_value !== "") {
            $selected_values[] = trim($selected_source_value);
        }

        $selected_values = array_unique($selected_values);

        $dropdown = array(array(
            "id" => "",
            "text" => "- " . app_lang($placeholder_lang_key) . " -",
            "isSelected" => empty($selected_values)
        ));

        foreach ($sources as $source) {
            $value = trim($source->value);
            $dropdown[] = array(
                "id" => $value,
                "text" => $value,
                "isSelected" => in_array($value, $selected_values, true)
            );
        }

        return $dropdown;
    }

    private function _get_filter_value($key) {
        $value = $this->request->getPost($key);

        if ($value === null) {
            $value = $this->request->getGet($key);
        }

        if ($value === null && $key !== "filter_params") {
            $filter_params = $this->_get_filter_params_array();
            if (array_key_exists($key, $filter_params)) {
                $value = $filter_params[$key];
            }
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        return $value;
    }

    private function _get_filter_params_array() {
        $filter_params = $this->request->getPost("filter_params");

        if ($filter_params === null) {
            $filter_params = $this->request->getGet("filter_params");
        }

        if ($filter_params === null) {
            $filter_params = $this->request->getPost("filterParams");
        }

        if ($filter_params === null) {
            $filter_params = $this->request->getGet("filterParams");
        }

        if ($filter_params === null || $filter_params === "") {
            return array();
        }

        if (is_array($filter_params)) {
            return $filter_params;
        }

        if (is_string($filter_params)) {
            $filter_params = trim($filter_params);
        }

        $decoded = json_decode($filter_params, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        return array();
    }

    private function _normalize_campaign_filter_values($value) {
        $normalized = array();

        $append_value = function ($item) use (&$normalized, &$append_value) {
            if ($item === null) {
                return;
            }

            if (is_array($item)) {
                foreach ($item as $sub_item) {
                    $append_value($sub_item);
                }
                return;
            }

            if (is_string($item)) {
                $item = trim($item);

                if ($item === '') {
                    return;
                }

                $decoded = json_decode($item, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $append_value($decoded);
                    return;
                }

                if (strpos($item, ',') !== false) {
                    $parts = array_map('trim', explode(',', $item));
                    $append_value($parts);
                    return;
                }

                $normalized[] = $item;
                return;
            }

            if ($item === '') {
                return;
            }

            $normalized[] = (string) $item;
        };

        $append_value($value);

        if (empty($normalized)) {
            return array();
        }

        $normalized = array_map(function ($item) {
            return is_string($item) ? $item : (string) $item;
        }, $normalized);

        return array_values(array_unique($normalized));
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

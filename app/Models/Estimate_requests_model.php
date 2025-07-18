<?php

namespace App\Models;

class Estimate_requests_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'estimate_requests';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $estimate_requests_table = $this->db->prefixTable('estimate_requests');
        $estimate_forms_table = $this->db->prefixTable('estimate_forms');
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $estimate_requests_table.id=$id";
        }

        $client_id = $this->_get_clean_value($options, "client_id");
        if ($client_id) {
            $where .= " AND $estimate_requests_table.client_id=$client_id";
        }

        $lead_id = $this->_get_clean_value($options, "lead_id");
        if ($lead_id) {
            $where .= " AND $estimate_requests_table.lead_id=$lead_id";
        }

        $assigned_to = $this->_get_clean_value($options, "assigned_to");
        if ($assigned_to) {
            $where .= " AND $estimate_requests_table.assigned_to=$assigned_to";
        }

        $status = $this->_get_clean_value($options, "status");
        if ($status) {
            $where .= " AND $estimate_requests_table.status='$status'";
        }

        $form_id = $this->_get_clean_value($options, "estimate_form_id");
        if ($form_id) {
            $where .= " AND $estimate_requests_table.estimate_form_id=$form_id";
        }

        $start_date = $this->_get_clean_value($options, "start_date");
        if ($start_date) {
            $where .= " AND DATE($estimate_requests_table.created_at) >= '$start_date'";
        }
        $end_date = $this->_get_clean_value($options, "end_date");
        if ($end_date) {
            $where .= " AND DATE($estimate_requests_table.created_at) <= '$end_date'";
        }

        $clients_only = $this->_get_clean_value($options, "clients_only");
        if ($clients_only) {
            $where .= " AND $estimate_requests_table.client_id IN(SELECT $clients_table.id FROM $clients_table WHERE $clients_table.deleted=0 AND $clients_table.is_lead=0)";
        }

        $sql = "SELECT $estimate_requests_table.*,
              $clients_table.company_name, $clients_table.address, $clients_table.state, $clients_table.zip, $clients_table.phone,
              $estimate_forms_table.title AS form_title, $clients_table.is_lead,
              CONCAT($users_table.first_name, ' ', $users_table.last_name) AS assigned_to_user, $users_table.image as assigned_to_avatar, $clients_table.is_lead
        FROM $estimate_requests_table
        LEFT JOIN $clients_table ON $clients_table.id = $estimate_requests_table.client_id
        LEFT JOIN $users_table ON $users_table.id = $estimate_requests_table.assigned_to
        LEFT JOIN $estimate_forms_table ON $estimate_forms_table.id = $estimate_requests_table.estimate_form_id
        WHERE $estimate_requests_table.deleted=0 $where";

        return $this->db->query($sql);
    }
function get_pdf_details($id) {
        $estimate_requests_table = $this->db->prefixTable('estimate_requests');
        $estimate_forms_table = $this->db->prefixTable('estimate_forms');
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');

        $sql = "SELECT $estimate_requests_table.*, 
                       $clients_table.company_name, $clients_table.currency, $clients_table.currency_symbol,
                       $estimate_forms_table.title AS form_title,
                       CONCAT($users_table.first_name, ' ', $users_table.last_name) AS assigned_to_user
                FROM $estimate_requests_table
                LEFT JOIN $clients_table ON $clients_table.id = $estimate_requests_table.client_id
                LEFT JOIN $users_table ON $users_table.id = $estimate_requests_table.assigned_to
                LEFT JOIN $estimate_forms_table ON $estimate_forms_table.id = $estimate_requests_table.estimate_form_id
                WHERE $estimate_requests_table.deleted = 0 AND $estimate_requests_table.id = $id";

        $result = $this->db->query($sql)->getRow();
        if ($result) {
            $data['estimate_request_info'] = $result;
            $data['client_info'] = (object) [
                'company_name' => $result->company_name,
                'currency' => $result->currency,
                'currency_symbol' => $result->currency_symbol
            ];
            return $data;
        }
        return null;
    }
    function download_pdf($estimate_request_id = 0) {
        if (!$estimate_request_id) {
            show_404();
        }

    validate_numeric_value($estimate_request_id);

    // Check permissions
    $model_info = $this->Estimate_requests_model->get_one($estimate_request_id);
    if ($model_info) {
        $this->access_only_allowed_members_or_client_contact($model_info->client_id);
    } else {
        show_404();
    }

    // Fetch estimate request data
    $estimate_request_data = $this->Estimate_requests_model->get_pdf_details($estimate_request_id);
    if (!$estimate_request_data) {
        log_message('error', 'No data returned from get_pdf_details for estimate_request_id: ' . $estimate_request_id);
        show_404();
    }

    // Log data for debugging
    log_message('debug', 'Estimate request data: ' . print_r($estimate_request_data, true));

    // Generate PDF
    prepare_estimate_request_pdf($estimate_request_data, "download");
}

    //get counts of yes for selected custom fields within a form
    function get_custom_field_summary($form_id) {
        $estimate_requests_table = $this->db->prefixTable('estimate_requests');
        $cf_table = $this->db->prefixTable('custom_field_values');

        $counts = [267 => 0, 268 => 0, 269 => 0, 270 => 0];

        $sql = "SELECT cf.custom_field_id, COUNT(cf.id) AS total
                FROM $estimate_requests_table er
                INNER JOIN $cf_table cf ON cf.related_to_type='estimate_request' AND cf.related_to_id=er.id AND cf.deleted=0
                WHERE er.deleted=0 AND er.estimate_form_id=$form_id AND cf.custom_field_id IN (267,268,269,270) AND LOWER(cf.value)='yes'
                GROUP BY cf.custom_field_id";
        $result = $this->db->query($sql)->getResult();
        foreach ($result as $row) {
            $counts[$row->custom_field_id] = intval($row->total);
        }

        $total_requests = $this->db->query("SELECT COUNT(id) AS total FROM $estimate_requests_table WHERE deleted=0 AND estimate_form_id=$form_id")->getRow()->total ?? 0;

        $yes_requests = $this->db->query("SELECT COUNT(DISTINCT er.id) AS total
                FROM $estimate_requests_table er
                INNER JOIN $cf_table cf ON cf.related_to_type='estimate_request' AND cf.related_to_id=er.id AND cf.deleted=0 AND cf.custom_field_id IN (267,268,269,270) AND LOWER(cf.value)='yes'
                WHERE er.deleted=0 AND er.estimate_form_id=$form_id")->getRow()->total ?? 0;

        $summary = [
            'counts' => $counts,
            'unhappy' => intval($total_requests) - intval($yes_requests)
        ];

        return $summary;
    }
}

<?php

namespace App\Models;

class Clients_model extends Crud_model {

    protected $table = null;
    private const LEAD_SOURCE_CUSTOM_FIELD_ID = 238;
    private const CLIENT_SOURCE_CUSTOM_FIELD_ID = 265;

    function __construct() {
        $this->table = 'clients';
        parent::__construct($this->table);
    }

function get_details($options = array()) {
    $clients_table = $this->db->prefixTable('clients');
    $projects_table = $this->db->prefixTable('projects');
    $users_table = $this->db->prefixTable('users');
    $invoices_table = $this->db->prefixTable('invoices');
    $invoice_payments_table = $this->db->prefixTable('invoice_payments');
    $client_groups_table = $this->db->prefixTable('client_groups');
    $lead_status_table = $this->db->prefixTable('lead_status');
    $lead_source_table = $this->db->prefixTable('lead_source');
    $estimates_table = $this->db->prefixTable('estimates');
    $estimate_requests_table = $this->db->prefixTable('estimate_requests');
    $tickets_table = $this->db->prefixTable('tickets');
    $orders_table = $this->db->prefixTable('orders');
    $proposals_table = $this->db->prefixTable('proposals');

    $where = "";
    $id = $this->_get_clean_value($options, "id");
    if ($id) {
        $where .= " AND $clients_table.id=$id";
    }

    $custom_field_type = "clients";
    $sum_volume_only = get_array_value($options, "sum_volume_only");

    $leads_only = $this->_get_clean_value($options, "leads_only");
    if ($leads_only) {
        $custom_field_type = "leads";
        $where .= " AND $clients_table.is_lead=1";
    }

    $status = $this->_get_clean_value($options, "status");
    if ($status) {
        $where .= " AND $clients_table.lead_status_id='$status'";
    }

    $source = $this->_get_clean_value($options, "source");
    if ($source) {
        $where .= " AND $clients_table.lead_source_id='$source'";
    }

    $owner_id = $this->_get_clean_value($options, "owner_id");
    if ($owner_id) {
        $where .= " AND $clients_table.owner_id=$owner_id";
    }

    $created_by = $this->_get_clean_value($options, "created_by");
    if ($created_by) {
        $where .= " AND $clients_table.created_by=$created_by";
    }

    $show_own_clients_only_user_id = $this->_get_clean_value($options, "show_own_clients_only_user_id");
    if ($show_own_clients_only_user_id) {
        $where .= " AND ($clients_table.created_by=$show_own_clients_only_user_id OR $clients_table.owner_id=$show_own_clients_only_user_id)";
    }

   
     if (!$id && !$leads_only) {
         $where .= " AND $clients_table.is_lead=0";
     }

    $group_id = $this->_get_clean_value($options, "group_id");
    if ($group_id) {
        $where .= " AND FIND_IN_SET('$group_id', $clients_table.group_ids)";
    }

    $account_type = $this->_get_clean_value($options, "account_type");
    if ($account_type) {
        $where .= " AND $clients_table.type='$account_type'";
    }

    $quick_filter = $this->_get_clean_value($options, "quick_filter");
    if ($quick_filter) {
        $where .= $this->make_quick_filter_query($quick_filter, $clients_table, $projects_table, $invoices_table, $invoice_payments_table, $estimates_table, $estimate_requests_table, $tickets_table, $orders_table, $proposals_table);
    }

    $start_date = $this->_get_clean_value($options, "start_date");
    if ($start_date) {
        $where .= " AND DATE($clients_table.created_date)>='$start_date'";
    }
    $end_date = $this->_get_clean_value($options, "end_date");
    if ($end_date) {
        $where .= " AND DATE($clients_table.created_date)<='$end_date'";
    }

    // Range filter for Estimated Close Date custom field (id 167)
    $ec_start_date = $this->_get_clean_value($options, "ec_start_date");
    if ($ec_start_date) {
        $where .= " AND DATE(cfvt_167.value)>='$ec_start_date'";
    }
    $ec_end_date = $this->_get_clean_value($options, "ec_end_date");
    if ($ec_end_date) {
        $where .= " AND DATE(cfvt_167.value)<='$ec_end_date'";
    }

    // Range filter for Closed Date custom field (id 272)
    $closed_start_date = $this->_get_clean_value($options, "closed_start_date");
    if ($closed_start_date) {
        $where .= " AND DATE(cfvt_272.value)>='$closed_start_date'";
    }
    $closed_end_date = $this->_get_clean_value($options, "closed_end_date");
    if ($closed_end_date) {
        $where .= " AND DATE(cfvt_272.value)<='$closed_end_date'";
    }

    $label_id = $this->_get_clean_value($options, "label_id");
    if ($label_id) {
        $where .= " AND (FIND_IN_SET('$label_id', $clients_table.labels)) ";
    }

    $select_labels_data_query = $this->get_labels_data_query();

    $client_groups = $this->_get_clean_value($options, "client_groups");
    $where .= $this->prepare_allowed_client_groups_query($clients_table, $client_groups);

    // Prepare custom field binding query
    $custom_fields = get_array_value($options, "custom_fields");
    $custom_field_filter = get_array_value($options, "custom_field_filter");
    $custom_field_query_info = $this->prepare_custom_field_query_string($custom_field_type, $custom_fields, $clients_table, $custom_field_filter);
    $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
    $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");
    $custom_fields_where = get_array_value($custom_field_query_info, "where_string");

    $this->db->query('SET SQL_BIG_SELECTS=1');

    $limit_offset = "";
    $limit = $this->_get_clean_value($options, "limit");
    if ($limit) {
        $skip = $this->_get_clean_value($options, "skip");
        $offset = $skip ? $skip : 0;
        $limit_offset = " LIMIT $limit OFFSET $offset ";
    }

    $available_order_by_list = array(
        "id" => $clients_table . ".id",
        "company_name" => $clients_table . ".company_name",
        "created_date" => $clients_table . ".created_date",
        "account_type" => $clients_table . ".type",
        "primary_contact" => $users_table . ".first_name",
        "status" => "lead_status_title",
        "owner_name" => "owner_details.owner_name",
        "primary_contact" => "primary_contact",
        "client_groups" => "client_groups",
        "lead_source_title" => "lead_source_title",
        "address" => $clients_table . ".address",
        "city" => $clients_table . ".city",
        "state" => $clients_table . ".state",
        "zip" => $clients_table . ".zip"
    );

    $order_by = get_array_value($available_order_by_list, $this->_get_clean_value($options, "order_by"));

    $order = "";
    if ($order_by) {
        $order_dir = $this->_get_clean_value($options, "order_dir");
        $order = " ORDER BY $order_by $order_dir ";
    }

    $search_by = $this->_get_clean_value($options, "search_by");
    if ($search_by) {
        $search_by = $this->db->escapeLikeString($search_by);
        $labels_table = $this->db->prefixTable("labels");

        $where .= " AND (";
        $where .= " $clients_table.id LIKE '%$search_by%' ESCAPE '!' ";
        $where .= " OR $clients_table.company_name LIKE '%$search_by%' ESCAPE '!' ";
        $where .= " OR CONCAT($users_table.first_name, ' ', $users_table.last_name) LIKE '%$search_by%' ESCAPE '!' ";
        $where .= " OR (SELECT GROUP_CONCAT($labels_table.title, ', ') FROM $labels_table WHERE FIND_IN_SET($labels_table.id, $clients_table.labels)) LIKE '%$search_by%' ESCAPE '!' ";

        $where .= " OR owner_details.owner_name LIKE '%$search_by%' ESCAPE '!' ";
        $where .= " OR $lead_status_table.title LIKE '%$search_by%' ESCAPE '!' ";
        $where .= " OR $clients_table.type LIKE '%$search_by%' ESCAPE '!' ";
        $where .= $this->get_custom_field_search_query($clients_table, $custom_field_type, $search_by);

        $where .= " )";
    }

    if ($sum_volume_only) {
        $custom_field_values_table = $this->db->prefixTable('custom_field_values');
        $volume_alias = "cfvt_273";
        $additional_volume_join = "";

        if (strpos($join_custom_fieds, "cfvt_273") === false) {
            $volume_alias = "volume_cf";
            $additional_volume_join = " LEFT JOIN $custom_field_values_table AS $volume_alias ON $volume_alias.related_to_type='$custom_field_type' AND $volume_alias.related_to_id=$clients_table.id AND $volume_alias.deleted=0 AND $volume_alias.custom_field_id=273";
        }

        $sum_sql = "SELECT SUM(CAST(IFNULL($volume_alias.value, 0) AS DECIMAL(15,4))) AS total_volume
                FROM $clients_table
                $join_custom_fieds
                $additional_volume_join
                WHERE $clients_table.deleted=0 $where $custom_fields_where";

        $sum_row = $this->db->query($sum_sql)->getRow();
        return $sum_row ? floatval($sum_row->total_volume) : 0;
    }

    $sql = "SELECT SQL_CALC_FOUND_ROWS $clients_table.*,
                   $clients_table.type AS account_type,
                   CONCAT($users_table.first_name, ' ', $users_table.last_name) AS primary_contact,
                   $users_table.id AS primary_contact_id, 
                   $users_table.image AS contact_avatar, 
                   project_table.total_projects, 
                   IFNULL(invoice_details.payment_received,0) AS payment_received $select_custom_fieds,
                   IFNULL(invoice_details.invoice_value,0) AS invoice_value,
                   (SELECT $users_table.phone FROM $users_table WHERE $users_table.client_id = $clients_table.id AND $users_table.deleted=0 AND $users_table.is_primary_contact=1) AS primary_contact_phone,
                   (SELECT GROUP_CONCAT($client_groups_table.title) FROM $client_groups_table WHERE FIND_IN_SET($client_groups_table.id, $clients_table.group_ids)) AS client_groups,
                   $lead_status_table.title AS lead_status_title,
                   $lead_status_table.color AS lead_status_color,
                   $lead_source_table.title AS lead_source_title,
                   owner_details.owner_name,
                   owner_details.owner_avatar,
                   $select_labels_data_query
            FROM $clients_table
            LEFT JOIN $users_table ON $users_table.client_id = $clients_table.id AND $users_table.deleted=0 AND $users_table.is_primary_contact=1
            LEFT JOIN (SELECT client_id, COUNT(id) AS total_projects FROM $projects_table WHERE deleted=0 AND project_type='client_project' GROUP BY client_id) AS project_table ON project_table.client_id= $clients_table.id
            LEFT JOIN (SELECT client_id, SUM(payments_table.payment_received) as payment_received, SUM($invoices_table.invoice_total) AS invoice_value 
                       FROM $invoices_table
                       LEFT JOIN (SELECT invoice_id, SUM(amount) AS payment_received FROM $invoice_payments_table WHERE deleted=0 GROUP BY invoice_id) AS payments_table ON payments_table.invoice_id=$invoices_table.id 
                       WHERE $invoices_table.deleted=0 AND $invoices_table.status='not_paid'
                       GROUP BY $invoices_table.client_id) AS invoice_details ON invoice_details.client_id= $clients_table.id 
            LEFT JOIN $lead_status_table ON $clients_table.lead_status_id = $lead_status_table.id
            LEFT JOIN $lead_source_table ON $clients_table.lead_source_id = $lead_source_table.id
            LEFT JOIN (SELECT $users_table.id, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS owner_name, $users_table.image AS owner_avatar
                       FROM $users_table WHERE $users_table.deleted=0 AND $users_table.user_type='staff') AS owner_details ON owner_details.id=$clients_table.owner_id
            $join_custom_fieds
            WHERE $clients_table.deleted=0 $where $custom_fields_where  
            $order $limit_offset";

    $raw_query = $this->db->query($sql);

    $total_rows = $this->db->query("SELECT FOUND_ROWS() as found_rows")->getRow();

    if ($limit) {
        return array(
            "data" => $raw_query->getResult(),
            "recordsTotal" => $total_rows->found_rows,
            "recordsFiltered" => $total_rows->found_rows,
        );
    } else {
        return $raw_query;
    }
}

    private function make_quick_filter_query($filter, $clients_table, $projects_table, $invoices_table, $invoice_payments_table, $estimates_table, $estimate_requests_table, $tickets_table, $orders_table, $proposals_table) {
        $query = "";
        $tolarance = get_paid_status_tolarance();
        if ($filter == "has_open_projects" || $filter == "has_completed_projects" || $filter == "has_any_hold_projects" || $filter == "has_canceled_projects") {
            $status_id = 1;
            if ($filter == "has_completed_projects") {
                $status_id = 2;
            } else if ($filter == "has_any_hold_projects") {
                $status_id = 3;
            } else if ($filter == "has_canceled_projects") {
                $status_id = 4;
            }

            $query = " AND $clients_table.id IN(SELECT $projects_table.client_id FROM $projects_table WHERE $projects_table.deleted=0 AND $projects_table.project_type='client_project' AND $projects_table.status_id='$status_id') ";
        } else if ($filter == "has_unpaid_invoices" || $filter == "has_overdue_invoices" || $filter == "has_partially_paid_invoices") {
            $now = get_my_local_time("Y-m-d");

            $invoice_where = " AND $invoices_table.status ='not_paid' AND IFNULL(payments_table.payment_received,0)<=0"; //has_unpaid_invoices
            if ($filter == "has_overdue_invoices") {
                $invoice_where = " AND $invoices_table.status ='not_paid' AND $invoices_table.due_date<'$now' AND TRUNCATE(IFNULL(payments_table.payment_received,0),2)<$invoices_table.invoice_total-$tolarance";
            } else if ($filter == "has_partially_paid_invoices") {
                $invoice_where = " AND IFNULL(payments_table.payment_received,0)>0 AND IFNULL(payments_table.payment_received,0)<$invoices_table.invoice_total-$tolarance";
            }

            $query = " AND $clients_table.id IN(
                            SELECT $invoices_table.client_id FROM $invoices_table 
                               LEFT JOIN (SELECT invoice_id, SUM(amount) AS payment_received FROM $invoice_payments_table WHERE deleted=0 GROUP BY invoice_id) AS payments_table ON payments_table.invoice_id = $invoices_table.id  
                            WHERE $invoices_table.deleted=0 $invoice_where
                    ) ";
        } else if ($filter == "has_open_estimates" || $filter == "has_accepted_estimates") {
            $status = "sent";
            if ($filter == "has_accepted_estimates") {
                $status = "accepted";
            }

            $query = " AND $clients_table.id IN(SELECT $estimates_table.client_id FROM $estimates_table WHERE $estimates_table.deleted=0 AND $estimates_table.status='$status') ";
        } else if ($filter == "has_new_estimate_requests" || $filter == "has_estimate_requests_in_progress") {
            $status = "new";
            if ($filter == "has_estimate_requests_in_progress") {
                $status = "processing";
            }

            $query = " AND $clients_table.id IN(SELECT $estimate_requests_table.client_id FROM $estimate_requests_table WHERE $estimate_requests_table.deleted=0 AND $estimate_requests_table.status='$status') ";
        } else if ($filter == "has_open_tickets") {
            $query = " AND $clients_table.id IN(SELECT $tickets_table.client_id FROM $tickets_table WHERE $tickets_table.deleted=0 AND $tickets_table.status!='closed') ";
        } else if ($filter == "has_new_orders") {
            $query = " AND $clients_table.id IN(SELECT $orders_table.client_id FROM $orders_table WHERE $orders_table.deleted=0 AND $orders_table.status_id='1') ";
        } else if ($filter == "has_open_proposals" || $filter == "has_accepted_proposals" || $filter == "has_rejected_proposals") {
            $status = "sent";
            if ($filter == "has_accepted_proposals") {
                $status = "accepted";
            } else if ($filter == "has_rejected_proposals") {
                $status = "declined";
            }

            $query = " AND $clients_table.id IN(SELECT $proposals_table.client_id FROM $proposals_table WHERE $proposals_table.deleted=0 AND $proposals_table.status='$status') ";
        }

        return $query;
    }

    function get_primary_contact($client_id = 0, $info = false) {
        $users_table = $this->db->prefixTable('users');
        $client_id = $this->_get_clean_value($client_id);

        $sql = "SELECT $users_table.id, $users_table.first_name, $users_table.last_name
        FROM $users_table
        WHERE $users_table.deleted=0 AND $users_table.client_id=$client_id AND $users_table.is_primary_contact=1";
        $result = $this->db->query($sql);
        if ($result->resultID->num_rows) {
            if ($info) {
                return $result->getRow();
            } else {
                return $result->getRow()->id;
            }
        }
    }

    function add_remove_star($client_id, $user_id, $type = "add") {
        $clients_table = $this->db->prefixTable('clients');

        $client_id = $this->_get_clean_value($client_id);
        $user_id = $this->_get_clean_value($user_id);

        $action = " CONCAT($clients_table.starred_by,',',':$user_id:') ";
        $where = " AND FIND_IN_SET(':$user_id:',$clients_table.starred_by) = 0"; //don't add duplicate

        if ($type != "add") {
            $action = " REPLACE($clients_table.starred_by, ',:$user_id:', '') ";
            $where = "";
        }

        $sql = "UPDATE $clients_table SET $clients_table.starred_by = $action
        WHERE $clients_table.id=$client_id $where";
        return $this->db->query($sql);
    }

    function get_starred_clients($user_id, $client_groups = "") {
        $clients_table = $this->db->prefixTable('clients');
        $user_id = $this->_get_clean_value($user_id);
        $client_groups = $this->_get_clean_value($client_groups);

        $where = $this->prepare_allowed_client_groups_query($clients_table, $client_groups);

        $sql = "SELECT $clients_table.id,  $clients_table.company_name
        FROM $clients_table
        WHERE $clients_table.deleted=0 AND FIND_IN_SET(':$user_id:',$clients_table.starred_by) $where
        ORDER BY $clients_table.company_name ASC";
        return $this->db->query($sql);
    }

    function delete_client_and_sub_items($client_id) {
        $clients_table = $this->db->prefixTable('clients');
        $general_files_table = $this->db->prefixTable('general_files');
        $users_table = $this->db->prefixTable('users');

        $client_id = $this->_get_clean_value($client_id);

        //get client files info to delete the files from directory 
        $client_files_sql = "SELECT * FROM $general_files_table WHERE $general_files_table.deleted=0 AND $general_files_table.client_id=$client_id; ";
        $client_files = $this->db->query($client_files_sql)->getResult();

        //delete the client and sub items
        //delete client
        $delete_client_sql = "UPDATE $clients_table SET $clients_table.deleted=1 WHERE $clients_table.id=$client_id; ";
        $this->db->query($delete_client_sql);

        //delete contacts
        $delete_contacts_sql = "UPDATE $users_table SET $users_table.deleted=1 WHERE $users_table.client_id=$client_id; ";
        $this->db->query($delete_contacts_sql);

        //delete the project files from directory
        $file_path = get_general_file_path("client", $client_id);
        foreach ($client_files as $file) {
            delete_app_files($file_path, array(make_array_of_file($file)));
        }

        return true;
    }

    function is_duplicate_company_name($company_name, $id = 0) {

        $result = $this->get_all_where(array("company_name" => $company_name, "is_lead" => 0, "deleted" => 0));
        if (count($result->getResult()) && $result->getRow()->id != $id) {
            return $result->getRow();
        } else {
            return false;
        }
    }

    function get_leads_kanban_details($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $lead_source_table = $this->db->prefixTable('lead_source');
        $users_table = $this->db->prefixTable('users');
        $events_table = $this->db->prefixTable('events');
        $notes_table = $this->db->prefixTable('notes');
        $estimates_table = $this->db->prefixTable('estimates');
        $general_files_table = $this->db->prefixTable('general_files');
        $estimate_requests_table = $this->db->prefixTable('estimate_requests');

        $where = "";

        $status = $this->_get_clean_value($options, "status");
        if ($status) {
            $where .= " AND $clients_table.lead_status_id='$status'";
        }

        $owner_id = $this->_get_clean_value($options, "owner_id");
        if ($owner_id) {
            $where .= " AND $clients_table.owner_id='$owner_id'";
        }

        $source = $this->_get_clean_value($options, "source");
        if ($source) {
            $where .= " AND $clients_table.lead_source_id='$source'";
        }

        $search = $this->_get_clean_value($options, "search");
        if ($search) {
            $search = $this->db->escapeLikeString($search);
            $where .= " AND $clients_table.company_name LIKE '%$search%' ESCAPE '!'";
        }

        $label_id = $this->_get_clean_value($options, "label_id");
        if ($label_id) {
            $where .= " AND (FIND_IN_SET('$label_id', $clients_table.labels)) ";
        }

        $custom_field_filter = get_array_value($options, "custom_field_filter");
        $custom_field_query_info = $this->prepare_custom_field_query_string("leads", "", $clients_table, $custom_field_filter);
        $custom_fields_where = get_array_value($custom_field_query_info, "where_string");

        $users_where = "$users_table.client_id=$clients_table.id AND $users_table.deleted=0 AND $users_table.user_type='lead'";

        $select_labels_data_query = $this->get_labels_data_query();

        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql = "SELECT $clients_table.id, $clients_table.company_name, $clients_table.sort, IF($clients_table.sort!=0, $clients_table.sort, $clients_table.id) AS new_sort, $clients_table.lead_status_id, $clients_table.owner_id,
                (SELECT $users_table.image FROM $users_table WHERE $users_where AND $users_table.is_primary_contact=1) AS primary_contact_avatar,
                (SELECT COUNT($users_table.id) FROM $users_table WHERE $users_where) AS total_contacts_count,
                (SELECT COUNT($events_table.id) FROM $events_table WHERE $events_table.deleted=0 AND $events_table.client_id=$clients_table.id) AS total_events_count,
                (SELECT COUNT($notes_table.id) FROM $notes_table WHERE $notes_table.deleted=0 AND $notes_table.client_id=$clients_table.id) AS total_notes_count,
                (SELECT COUNT($estimates_table.id) FROM $estimates_table WHERE $estimates_table.deleted=0 AND $estimates_table.client_id=$clients_table.id) AS total_estimates_count,
                (SELECT COUNT($general_files_table.id) FROM $general_files_table WHERE $general_files_table.deleted=0 AND $general_files_table.client_id=$clients_table.id) AS total_files_count,
                (SELECT COUNT($estimate_requests_table.id) FROM $estimate_requests_table WHERE $estimate_requests_table.deleted=0 AND $estimate_requests_table.client_id=$clients_table.id) AS total_estimate_requests_count,
                $lead_source_table.title AS lead_source_title,
                CONCAT($users_table.first_name, ' ', $users_table.last_name) AS owner_name, $users_table.image AS owner_avatar,
                $select_labels_data_query
        FROM $clients_table 
        LEFT JOIN $lead_source_table ON $clients_table.lead_source_id = $lead_source_table.id 
        LEFT JOIN $users_table ON $users_table.id = $clients_table.owner_id AND $users_table.deleted=0 AND $users_table.user_type='staff' 
        WHERE $clients_table.deleted=0 AND $clients_table.is_lead=1 $where $custom_fields_where
        ORDER BY new_sort ASC";

        return $this->db->query($sql);
    }

    function get_clients_kanban_details($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $lead_source_table = $this->db->prefixTable('lead_source');
        $users_table = $this->db->prefixTable('users');
        $events_table = $this->db->prefixTable('events');
        $notes_table = $this->db->prefixTable('notes');
        $estimates_table = $this->db->prefixTable('estimates');
        $general_files_table = $this->db->prefixTable('general_files');
        $estimate_requests_table = $this->db->prefixTable('estimate_requests');

        $where = "";

        $status = $this->_get_clean_value($options, "status");
        if ($status) {
            $where .= " AND $clients_table.lead_status_id='$status'";
        }

        $owner_id = $this->_get_clean_value($options, "owner_id");
        if ($owner_id) {
            $where .= " AND $clients_table.owner_id='$owner_id'";
        }

        $source = $this->_get_clean_value($options, "source");
        if ($source) {
            $where .= " AND $clients_table.lead_source_id='$source'";
        }

        $search = $this->_get_clean_value($options, "search");
        if ($search) {
            $search = $this->db->escapeLikeString($search);
            $where .= " AND $clients_table.company_name LIKE '%$search%' ESCAPE '!'";
        }

        $label_id = $this->_get_clean_value($options, "label_id");
        if ($label_id) {
            $where .= " AND (FIND_IN_SET('$label_id', $clients_table.labels)) ";
        }

        $custom_field_filter = get_array_value($options, "custom_field_filter");
        $custom_field_query_info = $this->prepare_custom_field_query_string("clients", "", $clients_table, $custom_field_filter);
        $custom_fields_where = get_array_value($custom_field_query_info, "where_string");

        $users_where = "$users_table.client_id=$clients_table.id AND $users_table.deleted=0 AND $users_table.user_type='client'";

        $select_labels_data_query = $this->get_labels_data_query();

        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql = "SELECT $clients_table.id, $clients_table.company_name, $clients_table.sort, IF($clients_table.sort!=0, $clients_table.sort, $clients_table.id) AS new_sort, $clients_table.lead_status_id, $clients_table.owner_id,
                (SELECT $users_table.image FROM $users_table WHERE $users_where AND $users_table.is_primary_contact=1) AS primary_contact_avatar,
                (SELECT COUNT($users_table.id) FROM $users_table WHERE $users_where) AS total_contacts_count,
                (SELECT COUNT($events_table.id) FROM $events_table WHERE $events_table.deleted=0 AND $events_table.client_id=$clients_table.id) AS total_events_count,
                (SELECT COUNT($notes_table.id) FROM $notes_table WHERE $notes_table.deleted=0 AND $notes_table.client_id=$clients_table.id) AS total_notes_count,
                (SELECT COUNT($estimates_table.id) FROM $estimates_table WHERE $estimates_table.deleted=0 AND $estimates_table.client_id=$clients_table.id) AS total_estimates_count,
                (SELECT COUNT($general_files_table.id) FROM $general_files_table WHERE $general_files_table.deleted=0 AND $general_files_table.client_id=$clients_table.id) AS total_files_count,
                (SELECT COUNT($estimate_requests_table.id) FROM $estimate_requests_table WHERE $estimate_requests_table.deleted=0 AND $estimate_requests_table.client_id=$clients_table.id) AS total_estimate_requests_count,
                $lead_source_table.title AS lead_source_title,
                CONCAT($users_table.first_name, ' ', $users_table.last_name) AS owner_name, $users_table.image AS owner_avatar,
                $select_labels_data_query
        FROM $clients_table
        LEFT JOIN $lead_source_table ON $clients_table.lead_source_id = $lead_source_table.id
        LEFT JOIN $users_table ON $users_table.id = $clients_table.owner_id AND $users_table.deleted=0 AND $users_table.user_type='staff'
        WHERE $clients_table.deleted=0 AND $clients_table.is_lead=0 $where $custom_fields_where
        ORDER BY new_sort ASC";

        return $this->db->query($sql);
    }

    function get_search_suggestion($search = "", $options = array()) {
        $clients_table = $this->db->prefixTable('clients');

        $where = "";
        $show_own_clients_only_user_id = $this->_get_clean_value($options, "show_own_clients_only_user_id");
        if ($show_own_clients_only_user_id) {
            $where .= " AND ($clients_table.created_by=$show_own_clients_only_user_id OR $clients_table.owner_id=$show_own_clients_only_user_id)";
        }

        $search = $this->_get_clean_value($search);
        if ($search) {
            $search = $this->db->escapeLikeString($search);
            $where .= " AND $clients_table.company_name LIKE '%$search%' ESCAPE '!' ";
        }

        $client_groups = $this->_get_clean_value($options, "client_groups");
        $where .= $this->prepare_allowed_client_groups_query($clients_table, $client_groups);

        $sql = "SELECT $clients_table.id, $clients_table.company_name AS title
        FROM $clients_table  
        WHERE $clients_table.deleted=0 AND $clients_table.is_lead=0 $where
        ORDER BY $clients_table.company_name ASC
        LIMIT 0, 10";

        return $this->db->query($sql);
    }

    function count_total_clients($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $tickets_table = $this->db->prefixTable('tickets');
        $invoices_table = $this->db->prefixTable('invoices');
        $invoice_payments_table = $this->db->prefixTable('invoice_payments');
        $projects_table = $this->db->prefixTable('projects');
        $estimates_table = $this->db->prefixTable('estimates');
        $estimate_requests_table = $this->db->prefixTable('estimate_requests');
        $orders_table = $this->db->prefixTable('orders');
        $proposals_table = $this->db->prefixTable('proposals');

        $where = "";

        $show_own_clients_only_user_id = $this->_get_clean_value($options, "show_own_clients_only_user_id");
        if ($show_own_clients_only_user_id) {
            $where .= " AND $clients_table.created_by=$show_own_clients_only_user_id";
        }

        $filter = $this->_get_clean_value($options, "filter");
        if ($filter) {
            $where .= $this->make_quick_filter_query($filter, $clients_table, $projects_table, $invoices_table, $invoice_payments_table, $estimates_table, $estimate_requests_table, $tickets_table, $orders_table, $proposals_table);
        }

        $client_groups = $this->_get_clean_value($options, "client_groups");
        $where .= $this->prepare_allowed_client_groups_query($clients_table, $client_groups);

        $sql = "SELECT COUNT($clients_table.id) AS total
        FROM $clients_table 
        WHERE $clients_table.deleted=0 AND $clients_table.is_lead=0 $where";
        return $this->db->query($sql)->getRow()->total;
    }

    function get_conversion_rate_with_currency_symbol() {
        $clients_table = $this->db->prefixTable('clients');

        $sql = "SELECT $clients_table.currency_symbol, $clients_table.currency
        FROM $clients_table 
        WHERE $clients_table.deleted=0 AND $clients_table.currency!='' AND $clients_table.currency IS NOT NULL
        GROUP BY $clients_table.currency";
        return $this->db->query($sql);
    }

    function count_total_leads($options = array()) {
        $clients_table = $this->db->prefixTable('clients');

        $where = "";
        $show_own_leads_only_user_id = $this->_get_clean_value($options, "show_own_leads_only_user_id");
        if ($show_own_leads_only_user_id) {
            $where .= " AND $clients_table.owner_id=$show_own_leads_only_user_id";
        }

        $sql = "SELECT COUNT($clients_table.id) AS total
        FROM $clients_table 
        WHERE $clients_table.deleted=0 AND $clients_table.is_lead=1 $where";
        return $this->db->query($sql)->getRow()->total;
    }

    function get_lead_statistics($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $lead_status_table = $this->db->prefixTable('lead_status');

        try {
            $this->db->query("SET sql_mode = ''");
        } catch (\Exception $e) {
        }
        $where = "";

        $show_own_leads_only_user_id = $this->_get_clean_value($options, "show_own_leads_only_user_id");
        if ($show_own_leads_only_user_id) {
            $where .= " AND ($clients_table.owner_id=$show_own_leads_only_user_id)";
        }

        $converted_to_client = "SELECT COUNT($clients_table.id) AS total
        FROM $clients_table
        WHERE $clients_table.deleted=0 AND $clients_table.is_lead=0 AND $clients_table.lead_status_id!=0 $where";

        $lead_statuses = "SELECT COUNT($clients_table.id) AS total, $clients_table.lead_status_id, $lead_status_table.title, $lead_status_table.color
        FROM $clients_table
        LEFT JOIN $lead_status_table ON $lead_status_table.id = $clients_table.lead_status_id
        WHERE $clients_table.deleted=0 AND $clients_table.is_lead=1 $where
        GROUP BY $clients_table.lead_status_id
        ORDER BY $lead_status_table.sort ASC";

        $info = new \stdClass();
        $info->converted_to_client = $this->db->query($converted_to_client)->getRow()->total;
        $info->lead_statuses = $this->db->query($lead_statuses)->getResult();

        return $info;
    }

    function is_currency_editable($client_id) {
        $invoices_table = $this->db->prefixTable('invoices');
        $estimates_table = $this->db->prefixTable('estimates');
        $orders_table = $this->db->prefixTable('orders');
        $proposals_table = $this->db->prefixTable('proposals');
        $contracts_table = $this->db->prefixTable('contracts');
        $subscriptions_table = $this->db->prefixTable('subscriptions');

        $client_id = $this->_get_clean_value(array("client_id" => $client_id), "client_id");

        $invoices_sql = "SELECT $invoices_table.id
                        FROM $invoices_table
                        WHERE $invoices_table.deleted=0 
                        AND $invoices_table.client_id=$client_id AND $invoices_table.status!='draft' AND $invoices_table.status!='cancelled'
                        ORDER BY $invoices_table.id DESC LIMIT 1";

        $invoices_count = $this->db->query($invoices_sql)->getRow();
        $invoices_count = $invoices_count ? $invoices_count->id : 0;
        if ($invoices_count) {
            return false;
        }

        $estimates_sql = "SELECT $estimates_table.id 
                        FROM $estimates_table
                        WHERE $estimates_table.deleted=0 
                        AND $estimates_table.client_id=$client_id AND $estimates_table.status!='draft' AND $estimates_table.status!='declined'
                        ORDER BY $estimates_table.id DESC LIMIT 1";

        $estimates_count = $this->db->query($estimates_sql)->getRow();
        $estimates_count = $estimates_count ? $estimates_count->id : 0;
        if ($estimates_count) {
            return false;
        }

        $orders_sql = "SELECT $orders_table.id 
                        FROM $orders_table
                        WHERE $orders_table.deleted=0 AND $orders_table.client_id=$client_id
                        ORDER BY $orders_table.id DESC LIMIT 1";

        $orders_count = $this->db->query($orders_sql)->getRow();
        $orders_count = $orders_count ? $orders_count->id : 0;
        if ($orders_count) {
            return false;
        }

        $proposals_sql = "SELECT $proposals_table.id 
                        FROM $proposals_table
                        WHERE $proposals_table.deleted=0
                        AND $proposals_table.client_id=$client_id AND $proposals_table.status!='draft' AND $proposals_table.status!='declined'
                        ORDER BY $proposals_table.id DESC LIMIT 1";

        $proposals_count = $this->db->query($proposals_sql)->getRow();
        $proposals_count = $proposals_count ? $proposals_count->id : 0;
        if ($proposals_count) {
            return false;
        }

        $contracts_sql = "SELECT $contracts_table.id 
                        FROM $contracts_table
                        WHERE $contracts_table.deleted=0
                        AND $contracts_table.client_id=$client_id AND $contracts_table.status!='draft' AND $contracts_table.status!='declined'
                        ORDER BY $contracts_table.id DESC LIMIT 1";

        $contracts_count = $this->db->query($contracts_sql)->getRow();
        $contracts_count = $contracts_count ? $contracts_count->id : 0;
        if ($contracts_count) {
            return false;
        }

        $subscriptions_sql = "SELECT $subscriptions_table.id 
                        FROM $subscriptions_table
                        WHERE $subscriptions_table.deleted=0
                        AND $subscriptions_table.client_id=$client_id AND $subscriptions_table.status!='draft' AND $subscriptions_table.status!='cancelled'
                        ORDER BY $subscriptions_table.id DESC LIMIT 1";

        $subscriptions_count = $this->db->query($subscriptions_sql)->getRow();
        $subscriptions_count = $subscriptions_count ? $subscriptions_count->id : 0;
        if ($subscriptions_count) {
            return false;
        }

        //have nothing, the currency is editable
        return true;
    }

    function get_leads_team_members_summary($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');

        $clients_where = "";
        $is_lead = $this->_get_clean_value($options, "is_lead");
        if ($is_lead === "" || $is_lead === null) {
            $is_lead = 1;
        }
        $created_date_from = $this->_get_clean_value($options, "created_date_from");
        $created_date_to = $this->_get_clean_value($options, "created_date_to");
        if ($created_date_from && $created_date_to) {
            $clients_where .= " AND ($clients_table.created_date BETWEEN '$created_date_from' AND '$created_date_to') ";
        }

        $source_id = $this->_get_clean_value($options, "source_id");
        if ($source_id) {
            $clients_where .= " AND $clients_table.lead_source_id='$source_id'";
        }


        $label_id = $this->_get_clean_value($options, "label_id");
        if ($label_id) {
            $clients_where .= " AND (FIND_IN_SET('$label_id', $clients_table.labels)) ";
        }

        $sql = "SELECT $users_table.id AS team_member_id, CONCAT($users_table.first_name, ' ',$users_table.last_name) AS team_member_name, $users_table.image, leads_details.status_total_meta";
        if ($is_lead) {
            $sql .= ", leads_migrated.converted_to_client";
        }

        $sql .= "
                FROM $users_table
                INNER JOIN(
                    SELECT leads_group_table.owner_id, GROUP_CONCAT(CONCAT(leads_group_table.lead_status_id,'_',leads_group_table.total_leads)) AS status_total_meta
                    FROM (SELECT $clients_table.owner_id, $clients_table.lead_status_id, COUNT(1) AS total_leads FROM $clients_table WHERE $clients_table.is_lead=$is_lead AND $clients_table.deleted=0 $clients_where GROUP BY $clients_table.owner_id, $clients_table.lead_status_id) AS leads_group_table
                    GROUP BY leads_group_table.owner_id
                ) AS leads_details ON leads_details.owner_id = $users_table.id";

        if ($is_lead) {
            $sql .= "
                LEFT JOIN (SELECT $clients_table.owner_id, COUNT(1) AS converted_to_client FROM $clients_table WHERE $clients_table.is_lead=0 AND $clients_table.deleted=0 AND $clients_table.client_migration_date > '2000-01-01' $clients_where GROUP BY $clients_table.owner_id) as leads_migrated ON leads_migrated.owner_id = $users_table.id";
        }

        $sql .= "
                WHERE $users_table.deleted=0 AND $users_table.status='active' AND $users_table.user_type='staff'
                GROUP BY $users_table.id";
        return $this->db->query($sql);
    }

    function get_leads_team_members_volume_summary($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');
        $cf_table = $this->db->prefixTable('custom_field_values');

        $clients_where = "";
        $is_lead = $this->_get_clean_value($options, "is_lead");
        if ($is_lead === "" || $is_lead === null) {
            $is_lead = 1;
        }
        $created_date_from = $this->_get_clean_value($options, "created_date_from");
        $created_date_to = $this->_get_clean_value($options, "created_date_to");
        if ($created_date_from && $created_date_to) {
            $clients_where .= " AND ($clients_table.created_date BETWEEN '$created_date_from' AND '$created_date_to') ";
        }

        $source_id = $this->_get_clean_value($options, "source_id");
        if ($source_id) {
            $clients_where .= " AND $clients_table.lead_source_id='$source_id'";
        }

        $label_id = $this->_get_clean_value($options, "label_id");
        if ($label_id) {
            $clients_where .= " AND (FIND_IN_SET('$label_id', $clients_table.labels)) ";
        }

        $sql = "SELECT $users_table.id AS team_member_id, CONCAT($users_table.first_name, ' ',$users_table.last_name) AS team_member_name, $users_table.image, leads_details.status_total_meta";
        if ($is_lead) {
            $sql .= ", leads_migrated.converted_to_client";
        }

        $sql .= "
                FROM $users_table
                INNER JOIN(
                    SELECT leads_group_table.owner_id, GROUP_CONCAT(CONCAT(leads_group_table.lead_status_id,'_',leads_group_table.total_volume)) AS status_total_meta
                    FROM (SELECT $clients_table.owner_id, $clients_table.lead_status_id, SUM(IFNULL(volume.value,0)) AS total_volume FROM $clients_table LEFT JOIN $cf_table AS volume ON volume.custom_field_id=273 AND volume.related_to_type='clients' AND volume.related_to_id=$clients_table.id AND volume.deleted=0 WHERE $clients_table.is_lead=$is_lead AND $clients_table.deleted=0 $clients_where GROUP BY $clients_table.owner_id, $clients_table.lead_status_id) AS leads_group_table
                    GROUP BY leads_group_table.owner_id
                ) AS leads_details ON leads_details.owner_id = $users_table.id";

        if ($is_lead) {
            $sql .= "
                LEFT JOIN (SELECT $clients_table.owner_id, COUNT(1) AS converted_to_client FROM $clients_table WHERE $clients_table.is_lead=0 AND $clients_table.deleted=0 AND $clients_table.client_migration_date > '2000-01-01' $clients_where GROUP BY $clients_table.owner_id) as leads_migrated ON leads_migrated.owner_id = $users_table.id";
        }

        $sql .= "
                WHERE $users_table.deleted=0 AND $users_table.status='active' AND $users_table.user_type='staff'
                GROUP BY $users_table.id";
        return $this->db->query($sql);
    }

    function get_converted_to_client_statistics($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');
        $lead_source_table = $this->db->prefixTable('lead_source');

        $where = "";

        $date_range_type = $this->_get_clean_value($options, "date_range_type");

        $start_date = $this->_get_clean_value($options, "start_date");
        $end_date = $this->_get_clean_value($options, "end_date");

        $date_group_by_field = "$clients_table.created_date";

        if ($start_date && $end_date && $date_range_type == "created_date_wise") {
            $where .= " AND ($clients_table.created_date BETWEEN '$start_date' AND '$end_date') ";
        } else if ($start_date && $end_date) {
            $where .= " AND ($clients_table.client_migration_date BETWEEN '$start_date' AND '$end_date') ";
            $date_group_by_field = "$clients_table.client_migration_date";
        }

        $owner_id = $this->_get_clean_value($options, "owner_id");
        if ($owner_id) {
            $where .= " AND $clients_table.owner_id=$owner_id";
        }

        $source_id = $this->_get_clean_value($options, "source_id");
        if ($source_id) {
            $where .= " AND $clients_table.lead_source_id=$source_id";
        }

        $group_by = $this->_get_clean_value($options, "group_by");

        $sql = "";

        if ($group_by == "created_date") {
            $sql = "SELECT DATE_FORMAT($date_group_by_field,'%d') AS day, SUM(1) total_converted
                FROM $clients_table 
                WHERE $clients_table.is_lead=0 AND $clients_table.deleted=0 AND $clients_table.client_migration_date > '2000-01-01' $where
                GROUP BY DATE($date_group_by_field)";
        } else if ($group_by == "owner_id") {
            $sql = "SELECT $clients_table.owner_id, SUM(1) total_converted, CONCAT($users_table.first_name, ' ' ,$users_table.last_name) AS owner_name  
                FROM $clients_table 
                LEFT JOIN $users_table ON $users_table.id = $clients_table.owner_id
                WHERE $clients_table.is_lead=0 AND $clients_table.deleted=0 AND $clients_table.client_migration_date > '2000-01-01' $where
                GROUP BY $clients_table.owner_id";
        } else if ($group_by == "source_id") {
            $sql = "SELECT $clients_table.lead_source_id, SUM(1) total_converted, $lead_source_table.title
                FROM $clients_table 
                LEFT JOIN $lead_source_table ON $lead_source_table.id = $clients_table.lead_source_id
                WHERE $clients_table.is_lead=0 AND $clients_table.deleted=0 AND $clients_table.client_migration_date > '2000-01-01' $where
                GROUP BY $clients_table.lead_source_id";
        }

        return $this->db->query($sql);
    }

    function get_lead_conversion_report_details($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');
        $lead_source_table = $this->db->prefixTable('lead_source');
        $custom_field_values_table = $this->db->prefixTable('custom_field_values');

        $lead_source_custom_field_id = self::LEAD_SOURCE_CUSTOM_FIELD_ID;
        $client_source_custom_field_id = self::CLIENT_SOURCE_CUSTOM_FIELD_ID;

        $builder = $this->db->table("$clients_table AS c");
        $builder->select("
            c.owner_id,
            CONCAT_WS(' ', u.first_name, u.last_name) AS owner_name,
            c.lead_source_id AS region_id,
            ls.title AS region_name,
            SUM(CASE WHEN c.is_lead = 1 THEN 1 ELSE 0 END) AS total_leads,
            SUM(CASE WHEN c.is_lead = 0 AND c.client_migration_date IS NOT NULL AND c.client_migration_date > '2000-01-01' THEN 1 ELSE 0 END) AS conversions,
            AVG(CASE WHEN c.is_lead = 0 AND c.client_migration_date IS NOT NULL AND c.client_migration_date > '2000-01-01' THEN TIMESTAMPDIFF(DAY, c.created_date, c.client_migration_date) END) AS avg_conversion_time
        ", false);

        $builder->join("$users_table AS u", "u.id = c.owner_id", "left");
        $builder->join("$lead_source_table AS ls", "ls.id = c.lead_source_id", "left");
        $builder->join("$custom_field_values_table AS lead_cf", "lead_cf.related_to_id = c.id AND lead_cf.custom_field_id = $lead_source_custom_field_id AND lead_cf.related_to_type = 'leads' AND lead_cf.deleted = 0", "left");
        $builder->join("$custom_field_values_table AS client_cf", "client_cf.related_to_id = c.id AND client_cf.custom_field_id = $client_source_custom_field_id AND client_cf.related_to_type = 'clients' AND client_cf.deleted = 0", "left");

        $builder->where("c.deleted", 0);

        $owner_id = $this->_get_clean_value($options, "owner_id");
        if ($owner_id) {
            $builder->where("c.owner_id", $owner_id);
        }

        $region_id = $this->_get_clean_value($options, "region_id");
        if ($region_id) {
            $builder->where("c.lead_source_id", $region_id);
        }

        $lead_status_id = $this->_get_clean_value($options, "lead_status_id");
        if ($lead_status_id) {
            $builder->where("c.lead_status_id", $lead_status_id);
        }

        $source_value = $this->_get_clean_value($options, "source_value");
        if ($source_value) {
            $builder->groupStart();
            $builder->where("lead_cf.value", $source_value);
            $builder->orWhere("client_cf.value", $source_value);
            $builder->groupEnd();
        }

        $created_start_date = $this->_get_clean_value($options, "created_start_date");
        if ($created_start_date) {
            $builder->where("c.created_date >=", $created_start_date . " 00:00:00");
        }

        $created_end_date = $this->_get_clean_value($options, "created_end_date");
        if ($created_end_date) {
            $builder->where("c.created_date <=", $created_end_date . " 23:59:59");
        }

        $migration_start_date = $this->_get_clean_value($options, "migration_start_date");
        $migration_end_date = $this->_get_clean_value($options, "migration_end_date");
        if ($migration_start_date || $migration_end_date) {
            $builder->groupStart();
            $builder->where("c.is_lead", 1);
            $builder->orGroupStart();
            $builder->where("c.is_lead", 0);
            if ($migration_start_date) {
                $builder->where("c.client_migration_date >=", $migration_start_date . " 00:00:00");
            }
            if ($migration_end_date) {
                $builder->where("c.client_migration_date <=", $migration_end_date . " 23:59:59");
            }
            $builder->groupEnd();
            $builder->groupEnd();
        }

        $builder->groupBy("c.owner_id");
        $builder->groupBy("c.lead_source_id");

        $builder->orderBy("owner_name", "ASC");
        $builder->orderBy("region_name", "ASC");

        return $builder->get();
    }

    function get_rep_conversion_rates($filters = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');
        $custom_field_values_table = $this->db->prefixTable('custom_field_values');

        $total_leads_expression = "SUM(CASE WHEN c.is_lead = 1 THEN 1 ELSE 0 END)";
        $conversions_expression = "SUM(CASE WHEN c.is_lead = 0 AND c.client_migration_date IS NOT NULL AND c.client_migration_date > '2000-01-01' THEN 1 ELSE 0 END)";
        $conversion_rate_expression = "COALESCE(({$conversions_expression} / NULLIF({$total_leads_expression}, 0)) * 100, 0)";

        $lead_source_custom_field_id = self::LEAD_SOURCE_CUSTOM_FIELD_ID;
        $client_source_custom_field_id = self::CLIENT_SOURCE_CUSTOM_FIELD_ID;

        $builder = $this->db->table("$clients_table AS c");
        $builder->select("
            c.owner_id,
            CONCAT_WS(' ', u.first_name, u.last_name) AS owner_name,
            $total_leads_expression AS total_leads,
            $conversions_expression AS conversions,
            $conversion_rate_expression AS conversion_rate,
            AVG(CASE WHEN c.is_lead = 0 AND c.client_migration_date IS NOT NULL AND c.client_migration_date > '2000-01-01' THEN TIMESTAMPDIFF(DAY, c.created_date, c.client_migration_date) END) AS avg_conversion_time
        ", false);

        $builder->join("$users_table AS u", "u.id = c.owner_id", "left");
        $builder->join("$custom_field_values_table AS lead_cf", "lead_cf.related_to_id = c.id AND lead_cf.custom_field_id = $lead_source_custom_field_id AND lead_cf.related_to_type = 'leads' AND lead_cf.deleted = 0", "left");
        $builder->join("$custom_field_values_table AS client_cf", "client_cf.related_to_id = c.id AND client_cf.custom_field_id = $client_source_custom_field_id AND client_cf.related_to_type = 'clients' AND client_cf.deleted = 0", "left");

        $builder->where("c.deleted", 0);

        $owner_id = $this->_get_clean_value($filters, "owner_id");
        if ($owner_id) {
            $builder->where("c.owner_id", $owner_id);
        }

        $region_id = $this->_get_clean_value($filters, "region_id");
        if ($region_id) {
            $builder->where("c.lead_source_id", $region_id);
        }

        $lead_status_id = $this->_get_clean_value($filters, "lead_status_id");
        if ($lead_status_id) {
            $builder->where("c.lead_status_id", $lead_status_id);
        }

        $source_value = $this->_get_clean_value($filters, "source_value");
        if ($source_value) {
            $builder->groupStart();
            $builder->where("lead_cf.value", $source_value);
            $builder->orWhere("client_cf.value", $source_value);
            $builder->groupEnd();
        }

        $created_start_date = $this->_get_clean_value($filters, "created_start_date");
        if ($created_start_date) {
            $builder->where("c.created_date >=", $created_start_date . " 00:00:00");
        }

        $created_end_date = $this->_get_clean_value($filters, "created_end_date");
        if ($created_end_date) {
            $builder->where("c.created_date <=", $created_end_date . " 23:59:59");
        }

        $migration_start_date = $this->_get_clean_value($filters, "migration_start_date");
        $migration_end_date = $this->_get_clean_value($filters, "migration_end_date");
        if ($migration_start_date || $migration_end_date) {
            $builder->groupStart();
            $builder->where("c.is_lead", 1);
            $builder->orGroupStart();
            $builder->where("c.is_lead", 0);
            if ($migration_start_date) {
                $builder->where("c.client_migration_date >=", $migration_start_date . " 00:00:00");
            }
            if ($migration_end_date) {
                $builder->where("c.client_migration_date <=", $migration_end_date . " 23:59:59");
            }
            $builder->groupEnd();
            $builder->groupEnd();
        }

        $builder->groupBy("c.owner_id");
        $builder->orderBy("owner_name", "ASC");

        return $builder->get();
    }

    function get_client_conversion_timeline($filters = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');
        $lead_source_table = $this->db->prefixTable('lead_source');
        $lead_status_table = $this->db->prefixTable('lead_status');
        $custom_field_values_table = $this->db->prefixTable('custom_field_values');

        $source_expression = "COALESCE(NULLIF(lead_cf.value, ''), NULLIF(client_cf.value, ''))";

        $lead_source_custom_field_id = self::LEAD_SOURCE_CUSTOM_FIELD_ID;
        $client_source_custom_field_id = self::CLIENT_SOURCE_CUSTOM_FIELD_ID;

        $builder = $this->db->table("$clients_table AS c");
        $builder->select("
            c.id,
            c.company_name,
            c.client_migration_date,
            c.owner_id,
            CONCAT_WS(' ', u.first_name, u.last_name) AS owner_name,
            c.lead_source_id AS region_id,
            ls.title AS region_name,
            c.lead_status_id,
            st.title AS status_title,
            $source_expression AS source_value
        ", false);

        $builder->join("$users_table AS u", "u.id = c.owner_id", "left");
        $builder->join("$lead_source_table AS ls", "ls.id = c.lead_source_id", "left");
        $builder->join("$lead_status_table AS st", "st.id = c.lead_status_id", "left");
        $builder->join("$custom_field_values_table AS lead_cf", "lead_cf.related_to_id = c.id AND lead_cf.custom_field_id = $lead_source_custom_field_id AND lead_cf.related_to_type = 'leads' AND lead_cf.deleted = 0", "left");
        $builder->join("$custom_field_values_table AS client_cf", "client_cf.related_to_id = c.id AND client_cf.custom_field_id = $client_source_custom_field_id AND client_cf.related_to_type = 'clients' AND client_cf.deleted = 0", "left");

        $builder->where("c.deleted", 0);
        $builder->where("c.is_lead", 0);
        $builder->where("c.client_migration_date >", "2000-01-01 00:00:00");

        $owner_id = $this->_get_clean_value($filters, "owner_id");
        if ($owner_id) {
            $builder->where("c.owner_id", $owner_id);
        }

        $region_id = $this->_get_clean_value($filters, "region_id");
        if ($region_id) {
            $builder->where("c.lead_source_id", $region_id);
        }

        $lead_status_id = $this->_get_clean_value($filters, "lead_status_id");
        if ($lead_status_id) {
            $builder->where("c.lead_status_id", $lead_status_id);
        }

        $source_value = $this->_get_clean_value($filters, "source_value");
        if ($source_value) {
            $builder->groupStart();
            $builder->where("lead_cf.value", $source_value);
            $builder->orWhere("client_cf.value", $source_value);
            $builder->groupEnd();
        }

        $created_start_date = $this->_get_clean_value($filters, "created_start_date");
        if ($created_start_date) {
            $builder->where("c.created_date >=", $created_start_date . " 00:00:00");
        }

        $created_end_date = $this->_get_clean_value($filters, "created_end_date");
        if ($created_end_date) {
            $builder->where("c.created_date <=", $created_end_date . " 23:59:59");
        }

        $migration_start_date = $this->_get_clean_value($filters, "migration_start_date");
        if ($migration_start_date) {
            $builder->where("c.client_migration_date >=", $migration_start_date . " 00:00:00");
        }

        $migration_end_date = $this->_get_clean_value($filters, "migration_end_date");
        if ($migration_end_date) {
            $builder->where("c.client_migration_date <=", $migration_end_date . " 23:59:59");
        }

        $builder->orderBy("c.client_migration_date", "ASC");
        $builder->orderBy("c.company_name", "ASC");

        $clients = $builder->get()->getResult();

        $timeline_counts = array();
        foreach ($clients as $client) {
            if ($client->client_migration_date) {
                $timestamp = strtotime($client->client_migration_date);
                if ($timestamp) {
                    $key = date("Y-m", $timestamp);
                    if (!isset($timeline_counts[$key])) {
                        $timeline_counts[$key] = 0;
                    }
                    $timeline_counts[$key]++;
                }
            }
        }

        ksort($timeline_counts);

        $labels = array();
        $values = array();
        $cumulative = array();
        $running_total = 0;

        foreach ($timeline_counts as $key => $count) {
            $labels[] = date("M Y", strtotime($key . "-01"));
            $values[] = $count;
            $running_total += $count;
            $cumulative[] = $running_total;
        }

        return array(
            "clients" => $clients,
            "timeline" => array(
                "labels" => $labels,
                "values" => $values,
                "cumulative" => $cumulative
            )
        );
    }

    function get_lead_conversion_source_values($field_ids = null) {
        if (!$field_ids) {
            $field_ids = array(
                self::LEAD_SOURCE_CUSTOM_FIELD_ID,
                self::CLIENT_SOURCE_CUSTOM_FIELD_ID
            );
        }

        if (!is_array($field_ids)) {
            $field_ids = array($field_ids);
        }

        $field_ids = array_unique(array_map('intval', $field_ids));

        $custom_field_values_table = $this->db->prefixTable('custom_field_values');

        $builder = $this->db->table($custom_field_values_table);
        $builder->distinct();
        $builder->select('TRIM(value) AS value', false);
        $builder->where('deleted', 0);
        if ($field_ids && is_array($field_ids)) {
            $builder->whereIn('custom_field_id', $field_ids);
        }
        $builder->where('value IS NOT NULL', null, false);
        $builder->where("TRIM(value) != ''", null, false);
        $builder->orderBy('value', 'ASC');

        return $builder->get();
    }

    function get_client_status_statistics($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $lead_status_table = $this->db->prefixTable('lead_status');

        $where = "";

        $start_date = $this->_get_clean_value($options, "start_date");
        $end_date = $this->_get_clean_value($options, "end_date");

        if ($start_date && $end_date) {
            $where .= " AND ($clients_table.created_date BETWEEN '$start_date' AND '$end_date') ";
        }

        $owner_id = $this->_get_clean_value($options, "owner_id");
        if ($owner_id) {
            $where .= " AND $clients_table.owner_id=$owner_id";
        }

        $source_id = $this->_get_clean_value($options, "source_id");
        if ($source_id) {
            $where .= " AND $clients_table.lead_source_id=$source_id";
        }

        $show_own_leads_only_user_id = $this->_get_clean_value($options, "show_own_leads_only_user_id");
        if ($show_own_leads_only_user_id) {
            $where .= " AND $clients_table.owner_id=$show_own_leads_only_user_id";
        }

        $sql = "SELECT COUNT($clients_table.id) AS total, $lead_status_table.id AS lead_status_id, $lead_status_table.title, $lead_status_table.color
                FROM $lead_status_table
                LEFT JOIN $clients_table ON $lead_status_table.id = $clients_table.lead_status_id
                    AND $clients_table.deleted=0 AND $clients_table.is_lead=0 $where
                GROUP BY $lead_status_table.id
                ORDER BY $lead_status_table.sort ASC";

        return $this->db->query($sql);
    }

    //get statistics based on all clients
    function get_client_statistics($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');
        $lead_source_table = $this->db->prefixTable('lead_source');

        $where = "";

        $start_date = $this->_get_clean_value($options, "start_date");
        $end_date = $this->_get_clean_value($options, "end_date");

        if ($start_date && $end_date) {
            $where .= " AND ($clients_table.created_date BETWEEN '$start_date' AND '$end_date') ";
        }

        $owner_id = $this->_get_clean_value($options, "owner_id");
        if ($owner_id) {
            $where .= " AND $clients_table.owner_id=$owner_id";
        }

        $source_id = $this->_get_clean_value($options, "source_id");
        if ($source_id) {
            $where .= " AND $clients_table.lead_source_id=$source_id";
        }

        $group_by = $this->_get_clean_value($options, "group_by");

        if ($group_by == "created_date") {
            $sql = "SELECT DATE($clients_table.created_date) AS date, SUM(1) total_clients
                FROM $clients_table
                WHERE $clients_table.is_lead=0 AND $clients_table.deleted=0 $where
                GROUP BY DATE($clients_table.created_date)";
        } else if ($group_by == "owner_id") {
            $sql = "SELECT $clients_table.owner_id, SUM(1) total_clients, CONCAT($users_table.first_name, ' ' ,$users_table.last_name) AS owner_name
                FROM $clients_table
                LEFT JOIN $users_table ON $users_table.id = $clients_table.owner_id
                WHERE $clients_table.is_lead=0 AND $clients_table.deleted=0 $where
                GROUP BY $clients_table.owner_id";
        } else if ($group_by == "source_id") {
            $sql = "SELECT $clients_table.lead_source_id, SUM(1) total_clients, $lead_source_table.title
                FROM $clients_table
                LEFT JOIN $lead_source_table ON $lead_source_table.id = $clients_table.lead_source_id
                WHERE $clients_table.is_lead=0 AND $clients_table.deleted=0 $where
                GROUP BY $clients_table.lead_source_id";
        }

        return $this->db->query($sql);
    }

    function get_clients_id_and_name($options = array()) {
        $clients_table = $this->db->prefixTable('clients');

        $where = "";
        $limit_offset = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $clients_table.id=$id";
        }

        $ids = $this->_get_clean_value($options, "ids");
        if ($ids) {
            $where .= " AND (FIND_IN_SET('$ids', $clients_table.id)";
        }

        $limit = $this->_get_clean_value($options, "limit");
        if ($limit) {
            $limit_offset = " LIMIT $limit OFFSET 0 ";
        }


        $sql = "SELECT $clients_table.id, $clients_table.company_name AS name
        FROM $clients_table
        WHERE $clients_table.deleted=0 AND $clients_table.is_lead=0 $where $limit_offset";
        return $this->db->query($sql);
    }

    function get_dashboard_summary($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $custom_field_values_table = $this->db->prefixTable('custom_field_values');

        $owner_id = get_array_value($options, "owner_id");
        $owner_where = "";
        if ($owner_id) {
            $owner_where = " AND $clients_table.owner_id=$owner_id";
        }

        $sql = "SELECT $clients_table.id, $clients_table.created_date, volume.value AS volume, margin.value AS margin, $clients_table.lead_status_id
                FROM $clients_table
                LEFT JOIN $custom_field_values_table AS volume ON volume.custom_field_id=273 AND volume.related_to_type='clients' AND volume.related_to_id=$clients_table.id AND volume.deleted=0
                LEFT JOIN $custom_field_values_table AS margin ON margin.custom_field_id=241 AND margin.related_to_type='clients' AND margin.related_to_id=$clients_table.id AND margin.deleted=0
                WHERE $clients_table.deleted=0 AND $clients_table.is_lead=0 $owner_where";

        $rows = $this->db->query($sql)->getResult();

        $summary = new \stdClass();
        $summary->total_clients = 0;
        $summary->total_volume = 0;
        $summary->potential_margin = 0;
        $summary->weighted_forecast = 0;

        $summary->trend = new \stdClass();
        $summary->trend->current_clients = 0;
        $summary->trend->previous_clients = 0;
        $summary->trend->current_volume = 0;
        $summary->trend->previous_volume = 0;
        $summary->trend->current_margin = 0;
        $summary->trend->previous_margin = 0;
        $summary->trend->current_forecast = 0;
        $summary->trend->previous_forecast = 0;

        $months = array();
        $month_clients = array();
        $month_volume = array();
        $month_margin = array();
        $month_forecast = array();

        for ($i = 5; $i >= 0; $i--) {
            $m = date("Y-m", strtotime("-$i month"));
            $months[$m] = date("M", strtotime("-$i month"));
            $month_clients[$m] = 0;
            $month_volume[$m] = 0;
            $month_margin[$m] = 0;
            $month_forecast[$m] = 0;
        }

        $now = strtotime(get_current_utc_time());
        $current_start = strtotime("-30 days", $now);
        $previous_start = strtotime("-60 days", $now);

        $probability_mappings = [
            1 => 15,
            2 => 40,
            3 => 50,
            10 => 70,
            6 => 100,
            8 => 0,
            9 => 0
        ];

        foreach ($rows as $row) {
            $volume = $row->volume ? floatval($row->volume) : 0;
            $margin = $row->margin ? floatval($row->margin) : 0;
            $prob = isset($probability_mappings[$row->lead_status_id]) ? $probability_mappings[$row->lead_status_id] : 0;

            $summary->total_clients += 1;
            $summary->total_volume += $volume;
            $summary->potential_margin += $margin * $volume;
            $summary->weighted_forecast += ($margin * $volume) * ($prob / 100);

            $created = strtotime($row->created_date);
            if ($created >= $current_start) {
                $summary->trend->current_clients += 1;
                $summary->trend->current_volume += $volume;
                $summary->trend->current_margin += $margin * $volume;
                $summary->trend->current_forecast += ($margin * $volume) * ($prob / 100);
            } elseif ($created >= $previous_start && $created < $current_start) {
                $summary->trend->previous_clients += 1;
                $summary->trend->previous_volume += $volume;
                $summary->trend->previous_margin += $margin * $volume;
                $summary->trend->previous_forecast += ($margin * $volume) * ($prob / 100);
            }

            $m = date("Y-m", $created);
            if (isset($month_clients[$m])) {
                $month_clients[$m] += 1;
                $month_volume[$m] += $volume;
                $month_margin[$m] += $margin * $volume;
                $month_forecast[$m] += ($margin * $volume) * ($prob / 100);
            }
        }

        $summary->trend->clients_percent = $summary->trend->previous_clients ? (($summary->trend->current_clients - $summary->trend->previous_clients) / $summary->trend->previous_clients) * 100 : 0;
        $summary->trend->volume_percent = $summary->trend->previous_volume ? (($summary->trend->current_volume - $summary->trend->previous_volume) / $summary->trend->previous_volume) * 100 : 0;
        $summary->trend->margin_percent = $summary->trend->previous_margin ? (($summary->trend->current_margin - $summary->trend->previous_margin) / $summary->trend->previous_margin) * 100 : 0;
        $summary->trend->forecast_percent = $summary->trend->previous_forecast ? (($summary->trend->current_forecast - $summary->trend->previous_forecast) / $summary->trend->previous_forecast) * 100 : 0;

        $summary->months = array_values($months);
        $summary->monthly_clients = array_values($month_clients);
        $summary->monthly_volume = array_values($month_volume);
        $summary->monthly_margin = array_values($month_margin);
        $summary->monthly_forecast = array_values($month_forecast);

        return $summary;
    }

    //get potential margin and volume grouped by lead status
    function get_potential_margin_volume_by_stage($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $cf_table = $this->db->prefixTable('custom_field_values');
        $lead_status_table = $this->db->prefixTable('lead_status');

        $where = "";

        $owner_id = $this->_get_clean_value($options, "owner_id");
        if ($owner_id) {
            $where .= " AND $clients_table.owner_id=$owner_id";
        }

        $group_id = $this->_get_clean_value($options, "group_id");
        if ($group_id) {
            $where .= " AND FIND_IN_SET('$group_id', $clients_table.group_ids)";
        }

        $account_type = $this->_get_clean_value($options, "account_type");
        if ($account_type) {
            $where .= " AND $clients_table.type='$account_type'";
        }

        $source_id = $this->_get_clean_value($options, "source_id");
        if ($source_id) {
            $where .= " AND $clients_table.lead_source_id='$source_id'";
        }

        $status_ids = get_array_value($options, "status_ids");
        if ($status_ids && is_array($status_ids)) {
            $status_ids = array_map('intval', $status_ids);
            $where .= " AND $clients_table.lead_status_id IN(" . implode(',', $status_ids) . ")";
        }

        $start_date = $this->_get_clean_value($options, "start_date");
        if ($start_date) {
            $where .= " AND DATE($clients_table.created_date)>='$start_date'";
        }
        $end_date = $this->_get_clean_value($options, "end_date");
        if ($end_date) {
            $where .= " AND DATE($clients_table.created_date)<='$end_date'";
        }

        $ec_start_date = $this->_get_clean_value($options, "estimated_close_start_date");
        if ($ec_start_date) {
            $where .= " AND DATE(ec.value)>='$ec_start_date'";
        }
        $ec_end_date = $this->_get_clean_value($options, "estimated_close_end_date");
        if ($ec_end_date) {
            $where .= " AND DATE(ec.value)<='$ec_end_date'";
        }

        $closed_start_date = $this->_get_clean_value($options, "closed_start_date");
        if ($closed_start_date) {
            $where .= " AND DATE(cd.value)>='$closed_start_date'";
        }
        $closed_end_date = $this->_get_clean_value($options, "closed_end_date");
        if ($closed_end_date) {
            $where .= " AND DATE(cd.value)<='$closed_end_date'";
        }

        $client_groups = $this->_get_clean_value($options, "client_groups");
        $where .= $this->prepare_allowed_client_groups_query($clients_table, $client_groups);

        $sql = "SELECT $clients_table.lead_status_id, $lead_status_table.title AS status_title,
                       $lead_status_table.color AS status_color,
                       SUM(IFNULL(volume.value,0)) AS volume,
                       SUM(IFNULL(margin.value,0) * IFNULL(volume.value,0)) AS potential_margin
                FROM $clients_table
                LEFT JOIN $cf_table AS volume ON volume.custom_field_id=273 AND volume.related_to_type='clients' AND volume.related_to_id=$clients_table.id AND volume.deleted=0
                LEFT JOIN $cf_table AS margin ON margin.custom_field_id=241 AND margin.related_to_type='clients' AND margin.related_to_id=$clients_table.id AND margin.deleted=0
                LEFT JOIN $cf_table AS ec ON ec.custom_field_id=167 AND ec.related_to_type='clients' AND ec.related_to_id=$clients_table.id AND ec.deleted=0
                LEFT JOIN $cf_table AS cd ON cd.custom_field_id=272 AND cd.related_to_type='clients' AND cd.related_to_id=$clients_table.id AND cd.deleted=0
                LEFT JOIN $lead_status_table ON $lead_status_table.id=$clients_table.lead_status_id
                WHERE $clients_table.deleted=0 AND $clients_table.is_lead=0 $where
                GROUP BY $clients_table.lead_status_id
                ORDER BY $lead_status_table.sort ASC";

        return $this->db->query($sql)->getResult();
    }

    //get total volume grouped by lead source
    function get_volume_by_source($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $cf_table = $this->db->prefixTable('custom_field_values');
        $lead_source_table = $this->db->prefixTable('lead_source');

        $where = "";

        $owner_id = $this->_get_clean_value($options, "owner_id");
        if ($owner_id) {
            $where .= " AND $clients_table.owner_id=$owner_id";
        }

        $group_id = $this->_get_clean_value($options, "group_id");
        if ($group_id) {
            $where .= " AND FIND_IN_SET('$group_id', $clients_table.group_ids)";
        }

        $account_type = $this->_get_clean_value($options, "account_type");
        if ($account_type) {
            $where .= " AND $clients_table.type='$account_type'";
        }

        $source_id = $this->_get_clean_value($options, "source_id");
        if ($source_id) {
            $where .= " AND $clients_table.lead_source_id='$source_id'";
        }

        $status_ids = get_array_value($options, "status_ids");
        if ($status_ids && is_array($status_ids)) {
            $status_ids = array_map('intval', $status_ids);
            $where .= " AND $clients_table.lead_status_id IN(" . implode(',', $status_ids) . ")";
        }

        $start_date = $this->_get_clean_value($options, "start_date");
        if ($start_date) {
            $where .= " AND DATE($clients_table.created_date)>='$start_date'";
        }
        $end_date = $this->_get_clean_value($options, "end_date");
        if ($end_date) {
            $where .= " AND DATE($clients_table.created_date)<='$end_date'";
        }

        $ec_start_date = $this->_get_clean_value($options, "estimated_close_start_date");
        if ($ec_start_date) {
            $where .= " AND DATE(ec.value)>='$ec_start_date'";
        }
        $ec_end_date = $this->_get_clean_value($options, "estimated_close_end_date");
        if ($ec_end_date) {
            $where .= " AND DATE(ec.value)<='$ec_end_date'";
        }

        $closed_start_date = $this->_get_clean_value($options, "closed_start_date");
        if ($closed_start_date) {
            $where .= " AND DATE(cd.value)>='$closed_start_date'";
        }
        $closed_end_date = $this->_get_clean_value($options, "closed_end_date");
        if ($closed_end_date) {
            $where .= " AND DATE(cd.value)<='$closed_end_date'";
        }

        $client_groups = $this->_get_clean_value($options, "client_groups");
        $where .= $this->prepare_allowed_client_groups_query($clients_table, $client_groups);

        $sql = "SELECT $clients_table.lead_source_id, $lead_source_table.title AS source_title,
                       SUM(IFNULL(volume.value,0)) AS volume
                FROM $clients_table
                LEFT JOIN $cf_table AS volume ON volume.custom_field_id=273 AND volume.related_to_type='clients' AND volume.related_to_id=$clients_table.id AND volume.deleted=0
                LEFT JOIN $cf_table AS ec ON ec.custom_field_id=167 AND ec.related_to_type='clients' AND ec.related_to_id=$clients_table.id AND ec.deleted=0
                LEFT JOIN $cf_table AS cd ON cd.custom_field_id=272 AND cd.related_to_type='clients' AND cd.related_to_id=$clients_table.id AND cd.deleted=0
                LEFT JOIN $lead_source_table ON $lead_source_table.id=$clients_table.lead_source_id
                WHERE $clients_table.deleted=0 AND $clients_table.is_lead=0 $where
                GROUP BY $clients_table.lead_source_id
                ORDER BY $lead_source_table.title ASC";

        return $this->db->query($sql)->getResult();
    }

    //get total volume grouped by lead status
    function get_volume_by_status($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $cf_table = $this->db->prefixTable('custom_field_values');
        $lead_status_table = $this->db->prefixTable('lead_status');

        $where = "";

        $owner_id = $this->_get_clean_value($options, "owner_id");
        if ($owner_id) {
            $where .= " AND $clients_table.owner_id=$owner_id";
        }

        $group_id = $this->_get_clean_value($options, "group_id");
        if ($group_id) {
            $where .= " AND FIND_IN_SET('$group_id', $clients_table.group_ids)";
        }

        $account_type = $this->_get_clean_value($options, "account_type");
        if ($account_type) {
            $where .= " AND $clients_table.type='$account_type'";
        }

        $source_id = $this->_get_clean_value($options, "source_id");
        if ($source_id) {
            $where .= " AND $clients_table.lead_source_id='$source_id'";
        }

        $status_ids = get_array_value($options, "status_ids");
        if ($status_ids && is_array($status_ids)) {
            $status_ids = array_map('intval', $status_ids);
            $where .= " AND $clients_table.lead_status_id IN(" . implode(',', $status_ids) . ")";
        }

        $start_date = $this->_get_clean_value($options, "start_date");
        if ($start_date) {
            $where .= " AND DATE($clients_table.created_date)>='$start_date'";
        }
        $end_date = $this->_get_clean_value($options, "end_date");
        if ($end_date) {
            $where .= " AND DATE($clients_table.created_date)<='$end_date'";
        }

        $ec_start_date = $this->_get_clean_value($options, "estimated_close_start_date");
        if ($ec_start_date) {
            $where .= " AND DATE(ec.value)>='$ec_start_date'";
        }
        $ec_end_date = $this->_get_clean_value($options, "estimated_close_end_date");
        if ($ec_end_date) {
            $where .= " AND DATE(ec.value)<='$ec_end_date'";
        }

        $closed_start_date = $this->_get_clean_value($options, "closed_start_date");
        if ($closed_start_date) {
            $where .= " AND DATE(cd.value)>='$closed_start_date'";
        }
        $closed_end_date = $this->_get_clean_value($options, "closed_end_date");
        if ($closed_end_date) {
            $where .= " AND DATE(cd.value)<='$closed_end_date'";
        }

        $client_groups = $this->_get_clean_value($options, "client_groups");
        $where .= $this->prepare_allowed_client_groups_query($clients_table, $client_groups);

        $sql = "SELECT $clients_table.lead_status_id, $lead_status_table.title AS status_title,
                       $lead_status_table.color AS status_color,
                       SUM(IFNULL(volume.value,0)) AS volume
                FROM $clients_table
                LEFT JOIN $cf_table AS volume ON volume.custom_field_id=273 AND volume.related_to_type='clients' AND volume.related_to_id=$clients_table.id AND volume.deleted=0
                LEFT JOIN $cf_table AS ec ON ec.custom_field_id=167 AND ec.related_to_type='clients' AND ec.related_to_id=$clients_table.id AND ec.deleted=0
                LEFT JOIN $cf_table AS cd ON cd.custom_field_id=272 AND cd.related_to_type='clients' AND cd.related_to_id=$clients_table.id AND cd.deleted=0
                LEFT JOIN $lead_status_table ON $lead_status_table.id=$clients_table.lead_status_id
                WHERE $clients_table.deleted=0 AND $clients_table.is_lead=0 $where
                GROUP BY $clients_table.lead_status_id
                ORDER BY $lead_status_table.sort ASC";

        return $this->db->query($sql)->getResult();
    }

    function get_fill_the_funnel_leaderboard($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');
        $cf_table = $this->db->prefixTable('custom_field_values');

        $where = " AND $clients_table.is_lead=0 AND $clients_table.deleted=0";

        //only include clients which have the custom field value 273
        $where .= " AND cf_273.value IS NOT NULL";

        $july_21 = date('Y') . "-07-21";

        $sql = "SELECT $users_table.id AS staff_id,
                        CONCAT($users_table.first_name, ' ', $users_table.last_name) AS sales_rep_name,
                        $users_table.address AS roc,
                        SUM(IF(DATE($clients_table.created_date) >= '$july_21',1,0)) AS new_opportunities,
                        SUM(IF(DATE(cf_272.value) >= '$july_21',1,0)) AS closed_deals
                FROM $clients_table
                LEFT JOIN $cf_table AS cf_273 ON cf_273.custom_field_id=273 AND cf_273.related_to_type='clients' AND cf_273.related_to_id=$clients_table.id AND cf_273.deleted=0
                LEFT JOIN $cf_table AS cf_272 ON cf_272.custom_field_id=272 AND cf_272.related_to_type='clients' AND cf_272.related_to_id=$clients_table.id AND cf_272.deleted=0
                LEFT JOIN $users_table ON $users_table.id=$clients_table.owner_id
                WHERE $users_table.deleted=0 AND $users_table.status='active' AND $users_table.user_type='staff' $where
                GROUP BY $users_table.id";

        return $this->db->query($sql);
    }

    function get_fill_the_funnel_region_leaderboard($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');
        $cf_table = $this->db->prefixTable('custom_field_values');

        $where = " AND $clients_table.is_lead=0 AND $clients_table.deleted=0";

        //only include clients which have the custom field value 273
        $where .= " AND cf_273.value IS NOT NULL";

        $july_21 = date('Y') . "-07-21";

        $sql = "SELECT
                    CASE
                        WHEN LOWER($users_table.address) LIKE '%atlantic%' THEN 'Atlantic'
                        WHEN LOWER($users_table.address) LIKE '%quebec%' THEN 'Quebec'
                        WHEN LOWER($users_table.address) LIKE '%ontario%' THEN 'Ontario'
                        WHEN LOWER($users_table.address) LIKE '%pacific%' THEN 'Pacific'
                        WHEN LOWER($users_table.address) LIKE '%prairies%' THEN 'Prairies'
                        ELSE 'Other'
                    END AS roc,
                    SUM(IF(DATE($clients_table.created_date) >= '$july_21',1,0)) AS new_opportunities,
                    SUM(IF(DATE(cf_272.value) >= '$july_21',1,0)) AS closed_deals
                FROM $clients_table
                LEFT JOIN $cf_table AS cf_273 ON cf_273.custom_field_id=273 AND cf_273.related_to_type='clients' AND cf_273.related_to_id=$clients_table.id AND cf_273.deleted=0
                LEFT JOIN $cf_table AS cf_272 ON cf_272.custom_field_id=272 AND cf_272.related_to_type='clients' AND cf_272.related_to_id=$clients_table.id AND cf_272.deleted=0
                LEFT JOIN $users_table ON $users_table.id=$clients_table.owner_id
                WHERE $users_table.deleted=0 AND $users_table.status='active' AND $users_table.user_type='staff' $where
                GROUP BY roc";

        return $this->db->query($sql);
    }

    function get_leaderboard($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');
        $cf_table = $this->db->prefixTable('custom_field_values');

        $where = " AND $clients_table.is_lead=0 AND $clients_table.deleted=0 AND $clients_table.lead_status_id=6";
        $where .= " AND $users_table.role_id IN (1,7)";

        $owner_id = get_array_value($options, "owner_id");
        if ($owner_id) {
            $where .= " AND $users_table.id=" . $this->db->escape($owner_id);
        }

        $roc = get_array_value($options, "roc");
        if ($roc) {
            $where .= " AND $users_table.address=" . $this->db->escape($roc);
        }

        $role_id = get_array_value($options, "role_id");
        if ($role_id) {
            $where .= " AND $users_table.role_id=" . $this->db->escape($role_id);
        }

        $start_date = get_array_value($options, "start_date");
        if ($start_date) {
            $where .= " AND DATE(cf_272.value) >= " . $this->db->escape($start_date);
        }

        $end_date = get_array_value($options, "end_date");
        if ($end_date) {
            $where .= " AND DATE(cf_272.value) <= " . $this->db->escape($end_date);
        }

        $sql = "SELECT $users_table.id AS staff_id,
                       CONCAT($users_table.first_name, ' ', $users_table.last_name) AS sales_rep_name,
                       $users_table.role_id,
                       $users_table.address AS roc,
                       COUNT($clients_table.id) AS closed_won,
                       SUM(IFNULL(cf_273.value,0)) AS total_volume,
                       SUM(IFNULL(cf_273.value,0) * IFNULL(cf_241.value,0)) AS total_margin
                FROM $clients_table
                LEFT JOIN $cf_table AS cf_272 ON cf_272.custom_field_id=272 AND cf_272.related_to_type='clients' AND cf_272.related_to_id=$clients_table.id AND cf_272.deleted=0
                LEFT JOIN $cf_table AS cf_273 ON cf_273.custom_field_id=273 AND cf_273.related_to_type='clients' AND cf_273.related_to_id=$clients_table.id AND cf_273.deleted=0
                LEFT JOIN $cf_table AS cf_241 ON cf_241.custom_field_id=241 AND cf_241.related_to_type='clients' AND cf_241.related_to_id=$clients_table.id AND cf_241.deleted=0
                LEFT JOIN $users_table ON $users_table.id=$clients_table.owner_id
                WHERE $users_table.deleted=0 AND $users_table.status='active' AND $users_table.user_type='staff' $where
                GROUP BY $users_table.id";

        return $this->db->query($sql);
    }
}
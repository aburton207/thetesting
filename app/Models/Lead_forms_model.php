<?php

namespace App\Models;

class Lead_forms_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'lead_forms';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $lead_forms_table = $this->db->prefixTable('lead_forms');

        $where = "";
        $id = $this->_get_clean_value($options, 'id');
        if ($id) {
            $where .= " AND $lead_forms_table.id=$id";
        }

        $sql = "SELECT $lead_forms_table.*
        FROM $lead_forms_table
        WHERE $lead_forms_table.deleted=0 $where";

        return $this->db->query($sql);
    }
}


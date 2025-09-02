<?php

namespace Polls\Models;

class Nps_surveys_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'nps_surveys';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $nps_surveys_table = $this->db->prefixTable('nps_surveys');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $nps_surveys_table.id=$id";
        }

        $status = get_array_value($options, "status");
        if ($status) {
            $where .= " AND $nps_surveys_table.status='$status'";
        }

        $sql = "SELECT $nps_surveys_table.*
                FROM $nps_surveys_table
                WHERE 1=1 $where";
        return $this->db->query($sql);
    }
}


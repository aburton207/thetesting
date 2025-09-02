<?php

namespace Polls\Models;

class Nps_responses_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'nps_responses';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $responses_table = $this->db->prefixTable('nps_responses');

        $where = "";
        $survey_id = get_array_value($options, "survey_id");
        if ($survey_id) {
            $where .= " AND $responses_table.survey_id=$survey_id";
        }

        $token = get_array_value($options, "token");
        if ($token) {
            $where .= " AND $responses_table.token='$token'";
        }

        $sql = "SELECT $responses_table.*
                FROM $responses_table
                WHERE 1=1 $where";
        return $this->db->query($sql);
    }

    function save_score($data) {
        return $this->ci_save($data);
    }
}


<?php

namespace Polls\Models;

class Nps_questions_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'nps_questions';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $questions_table = $this->db->prefixTable('nps_questions');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $questions_table.id=$id";
        }

        $survey_id = get_array_value($options, "survey_id");
        if ($survey_id) {
            $where .= " AND $questions_table.survey_id=$survey_id";
        }

        $sql = "SELECT $questions_table.*
                FROM $questions_table
                WHERE 1=1 $where
                ORDER BY $questions_table.sort_order ASC";
        return $this->db->query($sql);
    }
}


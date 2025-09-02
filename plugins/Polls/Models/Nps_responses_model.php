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

    function get_summary($survey_id) {
        $responses_table = $this->db->prefixTable('nps_responses');
        $questions_table = $this->db->prefixTable('nps_questions');

        $survey_id = $this->_get_clean_value($survey_id);

        $sql = "SELECT $responses_table.question_id, $responses_table.score, COUNT($responses_table.id) AS total, $questions_table.title
                FROM $responses_table
                LEFT JOIN $questions_table ON $questions_table.id = $responses_table.question_id
                WHERE $responses_table.survey_id=$survey_id
                GROUP BY $responses_table.question_id, $responses_table.score, $questions_table.title
                ORDER BY $responses_table.question_id ASC, $responses_table.score ASC";
        return $this->db->query($sql);
    }

    function save_score($data) {
        return $this->ci_save($data);
    }
}


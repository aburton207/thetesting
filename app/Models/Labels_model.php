<?php

namespace App\Models;

class Labels_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'labels';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $labels_table = $this->db->prefixTable('labels');

        $where = "";

        $context = $this->_get_clean_value($options, "context");
        if ($context) {
            $where .= " AND $labels_table.context='$context'";
        }

        $user_id = $this->_get_clean_value($options, "user_id");
        if ($user_id) {
            $where .= " AND $labels_table.user_id=$user_id";
        }

        $label_ids = $this->_get_clean_value($options, "label_ids");
        if ($label_ids) {
            $where .= " OR $labels_table.id IN($label_ids)";
        }

        $sql = "SELECT $labels_table.*
        FROM $labels_table
        WHERE $labels_table.deleted=0 $where 
        ORDER BY $labels_table.title ASC";

        return $this->db->query($sql);
    }

    function label_group_list($label_ids = "") {
        $label_ids = $this->_get_clean_value($label_ids);
        
        if (preg_match('/[A-Za-z]/', $label_ids)) {
            //strings found, prepare class object with values
            $result = new \stdClass();
            $result->label_group_name = $label_ids;
            return $result;
        } else {
            $labels_table = $this->db->prefixTable('labels');

            $sql = "SELECT GROUP_CONCAT(' ', $labels_table.title) AS label_group_name
            FROM $labels_table
            WHERE FIND_IN_SET($labels_table.id, '$label_ids')";
            return $this->db->query($sql)->getRow();
        }
    }

    function is_label_exists($id = 0, $type = "") {
        if ($id && $type) {
            $id = $this->_get_clean_value($id);
            $type = $this->_get_clean_value($type);

            $table = $this->db->prefixTable($type);

            $sql = "SELECT COUNT($table.id) AS existing_labels FROM $table WHERE $table.deleted=0 AND FIND_IN_SET('$id', $table.labels)";

            return $this->db->query($sql)->getRow()->existing_labels;
        }
    }

    /**
     * Return label titles for the supplied ids.
     *
     * @param string|array $label_ids Comma separated string or array of ids
     * @return array List of label titles ordered by the incoming ids
     */
    function get_titles_by_ids($label_ids = array()) {
        if (!$label_ids) {
            return array();
        }

        if (!is_array($label_ids)) {
            if (is_string($label_ids)) {
                $label_ids = preg_split('/[,-]/', $label_ids, -1, PREG_SPLIT_NO_EMPTY);
            } else {
                $label_ids = array($label_ids);
            }
        }

        $clean_ids = array();
        foreach ($label_ids as $id) {
            $id = trim($id);
            if ($id === "") {
                continue;
            }
            if (is_numeric($id)) {
                $clean_ids[] = (int) $id;
            }
        }

        $clean_ids = array_values(array_unique($clean_ids));

        if (!$clean_ids) {
            return array();
        }

        $labels_table = $this->db->prefixTable('labels');
        $builder = $this->db->table($labels_table);
        $builder->select('id, title');
        $builder->where('deleted', 0);
        $builder->whereIn('id', $clean_ids);
        $result = $builder->get()->getResult();

        if (!$result) {
            return array();
        }

        $title_map = array();
        foreach ($result as $row) {
            $title_map[$row->id] = $row->title;
        }

        $titles = array();
        foreach ($clean_ids as $id) {
            if (isset($title_map[$id])) {
                $titles[] = $title_map[$id];
            }
        }

        return $titles;
    }

}

<?php

namespace App\Controllers;

class Lead_forms extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("lead");
    }

    function index() {
        $this->access_only_allowed_members();
        return $this->template->rander("collect_leads/lead_forms");
    }

    function modal_form() {
        $this->access_only_admin_or_settings_admin();
        $view_data["model_info"] = $this->Lead_forms_model->get_one($this->request->getPost("id"));

        // prepare owners dropdown
        $owners_dropdown = array("" => "-" . app_lang("owner") . "-");
        $owners = $this->Users_model->get_all_where(array("user_type" => "staff", "deleted" => 0, "status" => "active"))->getResult();
        foreach ($owners as $owner) {
        $owners_dropdown[$owner->id] = $owner->first_name . " " . $owner->last_name;
    }
    $view_data["owners_dropdown"] = $owners_dropdown;

        // prepare lead sources dropdown
        $sources_dropdown = array();
        $sources = $this->Lead_source_model->get_details()->getResult();
        foreach ($sources as $source) {
            $sources_dropdown[] = array("id" => $source->id, "text" => $source->title);
        }
        $view_data["sources_dropdown"] = $sources_dropdown;

        // prepare labels dropdown
        $labels_dropdown = array();
        $labels = $this->Labels_model->get_details(array("context" => "client"))->getResult();
        foreach ($labels as $label) {
            $labels_dropdown[$label->id] = $label->title;
        }
        $view_data["labels_dropdown"] = $labels_dropdown;

        // prepare custom fields dropdown
        $custom_fields_dropdown = array();
        $custom_fields = $this->Custom_fields_model->get_details(array("related_to" => "leads", "show_in_embedded_form" => true))->getResult();
        foreach ($custom_fields as $field) {
            $field_title = $field->title_language_key ? app_lang($field->title_language_key) : $field->title;
            $custom_fields_dropdown[$field->id] = $field_title;
        }
        $view_data["custom_fields_dropdown"] = $custom_fields_dropdown;

        return $this->template->view("collect_leads/lead_form_modal_form", $view_data);
    }

    function save() {
        $this->access_only_admin_or_settings_admin();
        $id = $this->request->getPost("id");
        $data = array(
            "title" => $this->request->getPost("title"),
            "owner_id" => $this->request->getPost("owner_id"),
            "lead_source_id" => $this->request->getPost("lead_source_id"),
            "labels" => is_array($this->request->getPost("labels")) ? implode(",", $this->request->getPost("labels")) : $this->request->getPost("labels"),
            "custom_fields" => is_array($this->request->getPost("custom_fields")) ? implode(",", $this->request->getPost("custom_fields")) : $this->request->getPost("custom_fields")
        );
        $save_id = $this->Lead_forms_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "id" => $save_id, "data" => $this->_row_data($save_id), "message" => app_lang("record_saved")));
        } else {
            echo json_encode(array("success" => false, "message" => app_lang("error_occurred")));
        }
    }

    function delete() {
        $this->access_only_admin_or_settings_admin();
        $id = $this->request->getPost("id");
        if ($this->Lead_forms_model->delete($id)) {
            echo json_encode(array("success" => true, "message" => app_lang("record_deleted")));
        } else {
            echo json_encode(array("success" => false, "message" => app_lang("record_cannot_be_deleted")));
        }
    }

    function list_data() {
        $this->access_only_allowed_members();
        $list_data = $this->Lead_forms_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _row_data($id) {
        $data = $this->Lead_forms_model->get_one($id);
        return $this->_make_row($data);
    }

    private function _make_row($data) {
        $owner = "-";
        if ($data->owner_id) {
            $owner_info = $this->Users_model->get_one($data->owner_id);
            if ($owner_info) {
                $owner = get_team_member_profile_link($data->owner_id, $owner_info->first_name . " " . $owner_info->last_name);
            }
        }

        $source = "-";
        if ($data->lead_source_id) {
            $source_info = $this->Lead_source_model->get_one($data->lead_source_id);
            if ($source_info) {
                $source = $source_info->title;
            }
        }

        $options = modal_anchor(get_uri("lead_forms/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit'), "data-post-id" => $data->id))
                . js_anchor("<i data-feather='x' class='icon-16'></i>", array("title" => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("lead_forms/delete"), "data-action" => "delete"));

        return array($data->title, $owner, $source, $data->labels, $options);
    }
}

/* End of file Lead_forms.php */
/* Location: ./app/controllers/Lead_forms.php */

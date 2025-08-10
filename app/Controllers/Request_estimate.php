<?php

namespace App\Controllers;

use App\Libraries\ReCAPTCHA;

class Request_estimate extends App_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        if (!get_setting("module_estimate_request")) {
            show_404();
        }

        $view_data['topbar'] = "includes/public/topbar";
        $view_data['left_menu'] = false;

        $view_data["estimate_forms"] = $this->Estimate_forms_model->get_all_where(array("status" => "active", "public" => "1", "deleted" => 0))->getResult();
        return $this->template->rander("request_estimate/index", $view_data);
    }

    function form($id = 0, $embedded = 0, $all_fields = 0) {
        if (!get_setting("module_estimate_request")) {
            show_404();
        }

        if (!$id) {
            app_redirect("request_estimate");
        }

        validate_numeric_value($id);

        if ($embedded) {
            $view_data['topbar'] = false;
        } else {
            $view_data['topbar'] = "includes/public/topbar";
        }

        $view_data['left_menu'] = false;

        $view_data['embedded'] = clean_data($embedded);
        $view_data['all_fields'] = clean_data($all_fields);

        $model_info = $this->Estimate_forms_model->get_one_where(array("id" => $id, "public" => "1", "status" => "active", "deleted" => 0));

        if (get_setting("module_estimate_request") && $model_info->id) {
            $view_data['model_info'] = $model_info;
            return $this->template->rander('request_estimate/estimate_request_form', $view_data);
        } else {
            show_404();
        }
    }

    //save estimate request from client
function save_estimate_request() {
    if (!get_setting("module_estimate_request")) {
        show_404();
    }

    $form_id = $this->request->getPost('form_id');
    $assigned_to = $this->request->getPost('assigned_to');

    // Validate required fields, email is no longer required
    $this->validate_submitted_data(array(
        "form_id" => "required|numeric",
        "first_name" => "required",
        "last_name" => "required"
    ));

    // Check reCAPTCHA if enabled
    $ReCAPTCHA = new ReCAPTCHA();
    $ReCAPTCHA->validate_recaptcha();

    $options = array("related_to" => "estimate_form-" . $form_id);
    $form_fields = $this->Custom_fields_model->get_details($options)->getResult();

    $target_path = get_setting("timeline_file_path");
    $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "estimate");

    // Retrieve standard form data, handle null values
    $form_data = array(
        "first_name" => $this->request->getPost('first_name') ? trim($this->request->getPost('first_name')) : "",
        "last_name" => $this->request->getPost('last_name') ? trim($this->request->getPost('last_name')) : "",
        "email" => $this->request->getPost('email') ? trim($this->request->getPost('email')) : "",
        "company_name" => $this->request->getPost('company_name') ? trim($this->request->getPost('company_name')) : "",
        "address" => $this->request->getPost('address') ? trim($this->request->getPost('address')) : "",
        "city" => $this->request->getPost('city') ? trim($this->request->getPost('city')) : "",
        "state" => $this->request->getPost('state') ? trim($this->request->getPost('state')) : "",
        "zip" => $this->request->getPost('zip') ? trim($this->request->getPost('zip')) : "",
        "country" => $this->request->getPost('country') ? trim($this->request->getPost('country')) : "",
        "phone" => $this->request->getPost('phone') ? trim($this->request->getPost('phone')) : ""
    );

    // Generate default email if none provided or invalid
    $email = $form_data['email'];
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Sanitize first_name and last_name for email
        $sanitized_first_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $form_data['first_name']));
        $sanitized_last_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $form_data['last_name']));
        $base_email = $sanitized_first_name . '_' . $sanitized_last_name . '@avenirenergy.work';

        // Check for email uniqueness
        $email_count = $this->Users_model->get_one_where(array("email" => $base_email, "deleted" => 0));
        if ($email_count->id) {
            $timestamp = time();
            $email = $sanitized_first_name . '_' . $sanitized_last_name . '_' . $timestamp . '@avenirenergy.work';
        } else {
            $email = $base_email;
        }
        $form_data['email'] = $email;
    }

    // Match with existing user
    $user_info = $this->Users_model->get_one_where(array("email" => $email, "deleted" => 0));

    if ($user_info->client_id) {
        // Created by existing client/lead
        $request_data = array(
            "estimate_form_id" => $form_id,
            "created_by" => $user_info->id,
            "created_at" => get_current_utc_time(),
            "client_id" => $user_info->client_id,
            "lead_id" => 0,
            "assigned_to" => $assigned_to ? $assigned_to : 0,
            "status" => "new"
        );
    } else {
        // Unknown client, create a lead
        $leads_data = array(
            "company_name" => $form_data['first_name'] . " " . $form_data['last_name'],
            "address" => $form_data['address'],
            "city" => $form_data['city'],
            "state" => $form_data['state'],
            "zip" => $form_data['zip'],
            "country" => $form_data['country'],
            "phone" => $form_data['phone'],
            "is_lead" => 1,
            "lead_status_id" => $this->Lead_status_model->get_first_status(),
            "created_date" => get_current_utc_time(),
            "owner_id" => $assigned_to ? $assigned_to : 0,
            "type" => "organization"
        );

        $leads_data = clean_data($leads_data);

        $lead_id = $this->Clients_model->ci_save($leads_data);

        if ($lead_id && ($form_data['first_name'] || $form_data['last_name'] || $email)) {
            // Lead created, create a contact
            $lead_contact_data = array(
                "first_name" => $form_data['first_name'],
                "last_name" => $form_data['last_name'],
                "client_id" => $lead_id,
                "user_type" => "lead",
                "email" => $email,
                "created_at" => get_current_utc_time(),
                "is_primary_contact" => 1
            );

            $lead_contact_data = clean_data($lead_contact_data);
            $lead_contact_id = $this->Users_model->ci_save($lead_contact_data);
        }

        $request_data = array(
            "estimate_form_id" => $form_id,
            "created_by" => $lead_contact_id,
            "created_at" => get_current_utc_time(),
            "client_id" => $lead_id,
            "lead_id" => 0,
            "assigned_to" => $assigned_to ? $assigned_to : 0,
            "status" => "new"
        );
    }

    $request_data = clean_data($request_data);
    $request_data["files"] = $files_data; // Don't clean serialized data

    $save_id = $this->Estimate_requests_model->ci_save($request_data);
    if ($save_id) {
        // Save custom field values and collect them for notification
        $custom_field_values = array();
        foreach ($form_fields as $field) {
            $value = $this->request->getPost("custom_field_" . $field->id);
            if ($value) {
                $field_value_data = array(
                    "related_to_type" => "estimate_request",
                    "related_to_id" => $save_id,
                    "custom_field_id" => $field->id,
                    "value" => $value
                );

                $field_value_data = clean_data($field_value_data);
                $this->Custom_field_values_model->ci_save($field_value_data);

                // Store custom field value with its title
                $field_title = $field->title_language_key ? app_lang($field->title_language_key) : $field->title;
                $custom_field_values[] = array(
                    "title" => $field_title,
                    "value" => $value
                );
            }
        }

        $user_id = $user_info->id ? $user_info->id : $lead_contact_id;

        // Prepare notification data
        $notification_data = array(
            "estimate_request_id" => $save_id,
            "user_id" => $user_id,
            "assigned_to" => $assigned_to ? $assigned_to : 0,
            "form_data" => $form_data,
            "custom_field_values" => $custom_field_values,
            "files_data" => $files_data
        );

        // Create notification including all data at once
        $notification_id = log_notification("estimate_request_received", array(
            "estimate_request_id" => $save_id,
            "user_id" => $user_id,
            "assigned_to" => $assigned_to ? $assigned_to : 0,
            "description" => json_encode($notification_data)
        ));

        echo json_encode(array("success" => true, 'message' => app_lang('estimate_submission_message')));
    } else {
        echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
    }
}

    //prepare data for datatable for estimate form's field list
    function estimate_form_filed_list_data($id = 0, $all_fields = 0) {
        validate_numeric_value($id);

        $options = array("related_to" => "estimate_form-" . $id);
        if (!$all_fields) {
            $options["show_in_embedded_form"] = true;
        }
        $list_data = $this->Custom_fields_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_form_field_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    //prepare a row of estimates form's field list
    private function _make_form_field_row($data) {

        $required = "";
        if ($data->required) {
            $required = "*";
        }

        $title = "";
        if ($data->title_language_key) {
            $title = app_lang($data->title_language_key);
        } else {
            $title = $data->title;
        }

        $placeholder = "";
        if ($data->placeholder_language_key) {
            $placeholder = app_lang($data->placeholder_language_key);
        } else {
            $placeholder = $data->placeholder;
        }

        $field = "<label for='custom_field_$data->id' data-id='$data->id' class='field-row text-break-space'>$title $required</label>";

        $field .= "<div class='form-group'>" . $this->template->view("custom_fields/input_" . $data->field_type, array("field_info" => $data, "placeholder" => $placeholder)) . "</div>";

        //extract estimate id from related_to field. 2nd index should be the id
        $estimate_form_id = get_array_value(explode("-", $data->related_to), 1);

        return array(
            $field,
            $data->sort,
            modal_anchor(get_uri("estimate_requests/estimate_form_field_modal_form/" . $estimate_form_id), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_form'), "data-post-id" => $data->id))
                . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("estimate_requests/estimate_form_field_delete"), "data-action" => "delete"))
        );
    }
}

/* End of file quotations.php */
/* Location: ./app/controllers/quotations.php */
<?php

namespace App\Controllers;

use App\Libraries\ReCAPTCHA;

class Collect_leads extends App_Controller {

    private const SOURCE_CUSTOM_FIELD_ID = 265;

    function __construct() {
        parent::__construct();
    }

    function index($source_id = 0, $ownder_id = 0) {
        // Check if embedded form is enabled
        if (!get_setting("enable_embedded_form_to_get_leads")) {
            show_404();
        }

        validate_numeric_value($source_id);
        validate_numeric_value($ownder_id);

        $view_data['topbar'] = false;
        $view_data['left_menu'] = false;

        $view_data["currency_dropdown"] = $this->_get_currency_dropdown_select2_data();

        //get lead-specific custom fields only
        $custom_fields = $this->Custom_fields_model->get_details(array("show_in_embedded_form" => true, "related_to" => "leads"))->getResult();

        $custom_source_value = $this->request->getGet('custom_field_' . self::SOURCE_CUSTOM_FIELD_ID);
        if ($custom_source_value !== null && is_string($custom_source_value)) {
            $custom_source_value = trim($custom_source_value);
        }
        $view_data["custom_field_" . self::SOURCE_CUSTOM_FIELD_ID . "_value"] = $custom_source_value !== null ? $custom_source_value : "";
        $view_data["custom_fields"] = $this->filterCustomSourceField($custom_fields, $custom_source_value);
        $view_data["lead_source_id"] = $source_id;
        $view_data["lead_owner_id"] = $ownder_id;

        return $this->template->rander("collect_leads/index", $view_data);
    }

    //load embedded form by predefined lead form
    function form($id = 0) {
        // Check if embedded form is enabled
        if (!get_setting("enable_embedded_form_to_get_leads")) {
            show_404();
        }

        validate_numeric_value($id);

        $model_info = $this->Lead_forms_model->get_one($id);
        if (!$model_info || $model_info->deleted) {
            show_404();
        }

        $view_data['topbar'] = false;
        $view_data['left_menu'] = false;

        $view_data["currency_dropdown"] = $this->_get_currency_dropdown_select2_data();

        $selected_custom_fields = array();
        if ($model_info->custom_fields) {
            $ids = explode(',', $model_info->custom_fields);
            $custom_fields = $this->Custom_fields_model->get_details(array("related_to" => "leads", "show_in_embedded_form" => true))->getResult();
            foreach ($custom_fields as $field) {
                if (in_array($field->id, $ids)) {
                    $selected_custom_fields[] = $field;
                }
            }
        }

        $custom_source_override = $this->request->getGet('custom_field_' . self::SOURCE_CUSTOM_FIELD_ID);
        if ($custom_source_override !== null && is_string($custom_source_override)) {
            $custom_source_override = trim($custom_source_override);
        }
        $custom_source_value = $custom_source_override !== null ? $custom_source_override : $model_info->custom_source_value;
        if ($custom_source_value === null) {
            $custom_source_value = "";
        }

        $view_data["custom_fields"] = $this->filterCustomSourceField($selected_custom_fields, $custom_source_value);
        $view_data["lead_source_id"] = $model_info->lead_source_id;
        $view_data["lead_owner_id"] = $model_info->owner_id;
        $view_data["lead_labels"] = $model_info->labels;
        $view_data["custom_field_" . self::SOURCE_CUSTOM_FIELD_ID . "_value"] = $custom_source_value;

        return $this->template->rander("collect_leads/index", $view_data);
    }

    //save external lead
    function save() {
        if (!get_setting("can_create_lead_from_public_form")) {
            show_404();
        }

        $this->validate_submitted_data(array(
            "lead_source_id" => "numeric",
            "lead_owner_id" => "numeric",
            "email" => "valid_email"
        ));

        $is_embedded_form = $this->request->getPost("is_embedded_form");

        //check if there reCaptcha is enabled
        //if reCaptcha is enabled, check the validation
        if ($is_embedded_form) {
            $ReCAPTCHA = new ReCAPTCHA();
            $ReCAPTCHA->validate_recaptcha();
        }

        $email = $this->request->getPost('email');

        //validate duplicate email address
     //   if ($email && $this->Users_model->is_email_exists(trim($email))) {
       //     echo json_encode(array("success" => false, 'message' => app_lang('duplicate_email')));
     //       exit();
      //  }

        $company_name = $this->request->getPost('company_name');
        $first_name = $this->request->getPost('first_name');
        $last_name = $this->request->getPost('last_name');
        $source_id = (int)$this->request->getPost('lead_source_id');
        $lead_labels_input = $this->request->getPost('lead_labels');

        validate_list_of_numbers($lead_labels_input);

        $lead_label_ids_array = array();
        if ($lead_labels_input) {
            $raw_label_ids = preg_split('/[,-]/', $lead_labels_input, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($raw_label_ids as $raw_id) {
                $raw_id = trim($raw_id);
                if ($raw_id === "") {
                    continue;
                }
                if (is_numeric($raw_id)) {
                    $lead_label_ids_array[] = (int) $raw_id;
                }
            }
        }

        $lead_label_ids_array = array_values(array_unique($lead_label_ids_array));
        $lead_label_ids_string = $lead_label_ids_array ? implode(',', $lead_label_ids_array) : "";

        $lead_source_title = "";
        if ($source_id) {
            $lead_source = $this->Lead_source_model->get_one($source_id);
            if ($lead_source && $lead_source->id) {
                $lead_source_title = $lead_source->title;
            }
        }

        $label_titles = $this->Labels_model->get_titles_by_ids($lead_label_ids_array);
        $lead_labels_text = $label_titles ? implode(', ', $label_titles) : "";

        //fallback to first + last name if company name is empty
        if (!$company_name) {
            $company_name = trim($first_name . " " . $last_name);
        }

        $leads_data = array(
            "company_name" => $company_name,
            "address" => $this->request->getPost('address'),
            "city" => $this->request->getPost('city'),
            "state" => $this->request->getPost('state'),
            "zip" => $this->request->getPost('zip'),
            "country" => $this->request->getPost('country'),
            "phone" => $this->request->getPost('phone'),
            "is_lead" => 1,
            "lead_status_id" => $this->Lead_status_model->get_first_status(),
            "lead_source_id" => $source_id,
            "labels" => $lead_label_ids_string,
            "created_date" => get_current_utc_time(),
            "owner_id" => $this->request->getPost("lead_owner_id") ? $this->request->getPost("lead_owner_id") : 1 //if no owner is selected, add default admin
        );

        //determine type based on whether a company name was explicitly provided
        $leads_data["type"] = $this->request->getPost('company_name') ? "organization" : "person";

        $leads_data = clean_data($leads_data);

        $lead_id = $this->Clients_model->ci_save($leads_data);
        if (!$lead_id) {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }

        save_custom_fields("leads", $lead_id, 1, "staff");

        //lead created, create a contact on that lead
        //if first name or last name or email is not provided, don't create a contact
        if ($first_name || $last_name || $email) {
            $lead_contact_data = array(
                "first_name" => $first_name,
                "last_name" => $last_name,
                "client_id" => $lead_id,
                "user_type" => "lead",
                "email" => trim($email),
                "created_at" => get_current_utc_time(),
                "is_primary_contact" => 1
            );

            $lead_contact_data = clean_data($lead_contact_data);
            $lead_contact_id = $this->Users_model->ci_save($lead_contact_data);
            if (!$lead_contact_id) {
                echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            }
        }

        //prepare standard form data for notification
        $form_data = array(
            "company_name" => $company_name,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "email" => $email,
            "address" => $this->request->getPost('address'),
            "city" => $this->request->getPost('city'),
            "state" => $this->request->getPost('state'),
            "zip" => $this->request->getPost('zip'),
            "country" => $this->request->getPost('country'),
            "phone" => $this->request->getPost('phone')
        );

        if ($lead_source_title) {
            $form_data['lead_source'] = $lead_source_title;
        }

        if ($lead_labels_text) {
            $form_data['lead_labels'] = $lead_labels_text;
        }

        //collect custom field values
        $custom_field_values = array();
        $form_fields = $this->Custom_fields_model->get_details(array("related_to" => "leads", "show_in_embedded_form" => true))->getResult();
        foreach ($form_fields as $field) {
            $value = $this->request->getPost("custom_field_" . $field->id);
            if ($value !== null && $value !== "") {
                $field_title = $field->title_language_key ? app_lang($field->title_language_key) : $field->title;
                $custom_field_values[] = array(
                    "title" => $field_title,
                    "value" => $value
                );
            }
        }

        $notification_data = array(
            "lead_id" => $lead_id,
            "form_data" => $form_data,
            "custom_field_values" => $custom_field_values,
            "files_data" => array(),
            "lead_source" => $lead_source_title,
            "lead_source_id" => $source_id,
            "lead_labels" => $lead_labels_text,
            "lead_label_ids" => $lead_label_ids_string
        );

        // Log the notification with details
        log_notification(
            "lead_created",
            array(
                "lead_id" => $lead_id,
                "description" => json_encode($notification_data)
            ),
            "0"
        );

$after_submit_action_of_public_lead_form = get_setting("after_submit_action_of_public_lead_form");
$after_submit_action_of_public_lead_form_redirect_url = get_setting("after_submit_action_of_public_lead_form_redirect_url");

// JSON case
if ($is_embedded_form || $after_submit_action_of_public_lead_form === "json") {
    echo json_encode([
        "success" => true,
        "message" => "    <h2>Thank you!</h2>
        <p>Your request has been recieved.</p>"
    ]);
}
// TEXT case: override it to actually send HTML
else if ($after_submit_action_of_public_lead_form === "text") {

    // Force the response to be interpreted as HTML:
    header("Content-Type: text/html; charset=UTF-8");

    echo "
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Thank You</title>
    </head>
    <body>
        <h2>Your request has been received. </h2>
     
    </body>
    </html>
    ";
}
// Redirect case
else if ($after_submit_action_of_public_lead_form === "redirect" && $after_submit_action_of_public_lead_form_redirect_url) {
    app_redirect($after_submit_action_of_public_lead_form_redirect_url, true);
}
// Default/HTML case


    }

    private function _lead_html_form_code($form_id = 0, $source_id = 0, $owner_id = 0, $custom_source_value = null) {
        $overrideProvided = $custom_source_value !== null;
        if ($overrideProvided && is_string($custom_source_value)) {
            $custom_source_value = trim($custom_source_value);
        }
        if (!$overrideProvided) {
            $custom_source_value = "";
        }

        $view_data = [
            "lead_source_id" => $source_id,
            "lead_owner_id" => $owner_id,
            "lead_labels" => "",
            "custom_fields" => [],
            "custom_field_" . self::SOURCE_CUSTOM_FIELD_ID . "_value" => $custom_source_value
        ];

        $custom_fields = [];

        if ($form_id) {
            $model_info = $this->Lead_forms_model->get_one($form_id);
            if ($model_info && !$model_info->deleted) {
                $view_data["lead_source_id"] = $model_info->lead_source_id;
                $view_data["lead_owner_id"] = $model_info->owner_id;
                $view_data["lead_labels"] = $model_info->labels;

                if (!$overrideProvided) {
                    $custom_source_value = $model_info->custom_source_value ? trim($model_info->custom_source_value) : "";
                    $view_data["custom_field_" . self::SOURCE_CUSTOM_FIELD_ID . "_value"] = $custom_source_value;
                }

                if ($model_info->custom_fields) {
                    $ids = explode(',', $model_info->custom_fields);
                    $fields = $this->Custom_fields_model->get_details(array("related_to" => "leads", "show_in_embedded_form" => true))->getResult();
                    foreach ($fields as $field) {
                        if (in_array($field->id, $ids)) {
                            $custom_fields[] = $field;
                        }
                    }
                }
            }
        }

        if (!$custom_fields) {
            $custom_fields = $this->Custom_fields_model->get_details(array("related_to" => "leads", "show_in_embedded_form" => true))->getResult();
        }

        $view_data["custom_fields"] = $this->filterCustomSourceField($custom_fields, $custom_source_value);
        $view_data["custom_field_" . self::SOURCE_CUSTOM_FIELD_ID . "_value"] = $custom_source_value;

        return view("collect_leads/lead_html_form_code", $view_data);
    }

    function lead_html_form_code_modal_form() {
        $view_data['lead_html_form_code'] = $this->_lead_html_form_code();
        $view_data['sources'] = $this->Lead_source_model->get_details()->getResult();
        $view_data['owners'] = $this->Users_model->get_all_where(array("user_type" => "staff", "deleted" => 0, "status" => "active"))->getResult();
        $view_data['lead_forms'] = $this->Lead_forms_model->get_all_where(array("deleted" => 0))->getResult();

        $custom_field_meta = $this->getSourceCustomFieldMeta();
        $view_data['source_custom_field_label'] = $custom_field_meta['label'] ?? '';
        $view_data['source_custom_field_options'] = $custom_field_meta['options'] ?? [];
        $view_data['source_custom_field_has_options'] = $custom_field_meta['has_options'] ?? false;
        $view_data['lead_forms_source_map'] = $this->getLeadFormsSourceMap($view_data['lead_forms']);

        return $this->template->view('collect_leads/lead_html_form_code_modal_form', $view_data);
    }

    function get_lead_html_form_code() {
        $form_id = $this->request->getPost("lead_form_id");
        $source_id = $this->request->getPost("lead_source_id");
        $owner_id = $this->request->getPost("lead_owner_id");
        $custom_source_value = $this->request->getPost('custom_field_' . self::SOURCE_CUSTOM_FIELD_ID);
        if ($custom_source_value !== null && is_string($custom_source_value)) {
            $custom_source_value = trim($custom_source_value);
        }

        echo $this->_lead_html_form_code($form_id, $source_id, $owner_id, $custom_source_value);
    }

    function embedded_code_modal_form() {
        $embedded_code = "<iframe width='768' height='360' src='" . get_uri("collect_leads") . "' frameborder='0'></iframe>";
        $view_data['embedded'] = $embedded_code;
        $view_data['sources'] = $this->Lead_source_model->get_details()->getResult();
        $view_data['owners'] = $this->Users_model->get_all_where(array("user_type" => "staff", "deleted" => 0, "status" => "active"))->getResult();
        $view_data['lead_forms'] = $this->Lead_forms_model->get_all_where(array("deleted" => 0))->getResult();

        $custom_field_meta = $this->getSourceCustomFieldMeta();
        $view_data['source_custom_field_label'] = $custom_field_meta['label'] ?? '';
        $view_data['source_custom_field_options'] = $custom_field_meta['options'] ?? [];
        $view_data['source_custom_field_has_options'] = $custom_field_meta['has_options'] ?? false;
        $view_data['lead_forms_source_map'] = $this->getLeadFormsSourceMap($view_data['lead_forms']);

        return $this->template->view('collect_leads/embedded_code_modal_form', $view_data);
    }

    private function filterCustomSourceField($custom_fields, $custom_source_value) {
        if (!is_array($custom_fields) || !$this->hasCustomSourceValue($custom_source_value)) {
            return is_array($custom_fields) ? $custom_fields : [];
        }

        return array_values(array_filter($custom_fields, function ($field) {
            return (int) $field->id !== self::SOURCE_CUSTOM_FIELD_ID;
        }));
    }

    private function hasCustomSourceValue($value): bool {
        if ($value === null) {
            return false;
        }

        if (is_string($value)) {
            return trim($value) !== '';
        }

        return $value !== "";
    }

    private function getSourceCustomFieldMeta(): array {
        $field = $this->Custom_fields_model->get_one(self::SOURCE_CUSTOM_FIELD_ID);
        if (!$field || !$field->id) {
            return [];
        }

        $label = $field->title_language_key ? app_lang($field->title_language_key) : $field->title;

        $options = [];
        if (!empty($field->options)) {
            $raw_options = preg_split('/\r\n|\r|\n|,/', $field->options);
            if (is_array($raw_options)) {
                foreach ($raw_options as $option) {
                    $option = trim($option);
                    if ($option !== '') {
                        $options[$option] = $option;
                    }
                }
            }
        }

        $dropdown = [];
        if (!empty($options)) {
            $dropdown = array('' => "- " . $label . " -") + $options;
        }

        return [
            'label' => $label,
            'options' => $dropdown,
            'has_options' => !empty($options),
        ];
    }

    private function getLeadFormsSourceMap($lead_forms): array {
        $map = [];

        if (!is_array($lead_forms)) {
            return $map;
        }

        foreach ($lead_forms as $form) {
            if (!isset($form->id)) {
                continue;
            }

            $value = '';
            if (isset($form->custom_source_value) && $form->custom_source_value !== null) {
                $value = is_string($form->custom_source_value) ? trim($form->custom_source_value) : $form->custom_source_value;
            }

            $map[$form->id] = $value === null ? '' : (string) $value;
        }

        return $map;
    }
}

/* End of file Collect_leads.php */
/* Location: ./app/controllers/Collect_leads.php */
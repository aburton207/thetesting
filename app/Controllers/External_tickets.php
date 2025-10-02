<?php

namespace App\Controllers;

use App\Libraries\ReCAPTCHA;

class External_tickets extends App_Controller {

    function __construct() {
        parent::__construct();
    }

    function index($ticket_type_id = 0, $assignee_id = 0, $label_ids = "") { //embedded
        if (!get_setting("enable_embedded_form_to_get_tickets")) {
            show_404();
        }

        validate_numeric_value($ticket_type_id);
        validate_numeric_value($assignee_id);

        $label_ids = $this->sanitizeLabelIds($label_ids);
        $custom_field_ids = $this->sanitizeIdList($this->request->getGet('custom_fields'));

        $view_data['topbar'] = false;
        $view_data['left_menu'] = false;

        $where = array();
        $ticket_types_dropdown = $this->Ticket_types_model->get_dropdown_list(array("title"), "id", $where);
        $view_data['ticket_types_dropdown'] = $ticket_types_dropdown;

        $view_data["custom_fields"] = $this->filterCustomFieldsForEmbed($this->getEmbeddableTicketCustomFields(), $custom_field_ids);

        $view_data['selected_ticket_type_id'] = $ticket_type_id;
        $view_data['selected_assignee_id'] = $assignee_id;
        $view_data['selected_label_ids'] = $label_ids;
        $view_data['selected_custom_field_ids'] = $custom_field_ids;

        return $this->template->rander("external_tickets/index", $view_data);
    }

    //save external ticket
    function save() {
        if (!get_setting("enable_embedded_form_to_get_tickets")) {
            show_404();
        }

        $this->validate_submitted_data(array(
            "description" => "required",
            "email" => "required|valid_email",
            "first_name" => "required",
            "last_name" => "required",
            "address" => "required",
            "city" => "required",
            "state" => "required",
            "zip" => "required",
            "phone" => "required|min_length[10]",
            "lead_source_id" => "required|numeric"
        ));

        $is_embedded_form = $this->request->getPost('is_embedded_form');
        $redirect_url = $this->request->getPost('redirect_to');

        if ($redirect_url) {
            $redirect_url = clean_data($redirect_url);
        }

        $first_name = trim((string) $this->request->getPost('first_name'));
        $last_name = trim((string) $this->request->getPost('last_name'));
        $company_name = trim((string) $this->request->getPost('company_name'));
        $address = trim((string) $this->request->getPost('address'));
        $city = trim((string) $this->request->getPost('city'));
        $state = trim((string) $this->request->getPost('state'));
        $zip = trim((string) $this->request->getPost('zip'));
        $phone = trim((string) $this->request->getPost('phone'));
        $lead_source_id = (int) $this->request->getPost('lead_source_id');

        validate_numeric_value($lead_source_id);

        //check if there reCaptcha is enabled
        //if reCaptcha is enabled, check the validation
        if (!$is_embedded_form) {
            $ReCAPTCHA = new ReCAPTCHA();
            $ReCAPTCHA->validate_recaptcha();
        }

        $now = get_current_utc_time();

        $labels = $this->request->getPost('labels');
        $labels = $labels ? $labels : "";
        validate_list_of_numbers($labels);

        $assigned_to = (int) $this->request->getPost('assigned_to');
        $auto_assign_owner_input = $this->request->getPost('auto_assign_owner');
        $auto_assign_owner_input = is_null($auto_assign_owner_input) ? '' : trim((string) $auto_assign_owner_input);
        $should_auto_assign_owner = $auto_assign_owner_input === '0' ? false : true;

        $lead_source_owner_map = array(
            2 => 254,
            3 => 253,
            4 => 251,
            5 => 3827,
            6 => 252
        );

        $has_manual_assignee = $assigned_to ? true : false;

        if ($should_auto_assign_owner && !$has_manual_assignee && isset($lead_source_owner_map[$lead_source_id])) {
            $assigned_to = $lead_source_owner_map[$lead_source_id];
        }
        $ticket_type_id = (int) $this->request->getPost('ticket_type_id');

        $ticket_type_title = '';
        if ($ticket_type_id) {
            $ticket_type = $this->Ticket_types_model->get_one($ticket_type_id);
            if ($ticket_type && $ticket_type->id) {
                $ticket_type_title = (string) $ticket_type->title;
            }
        }

        $ticket_data = array(
            "title" => $this->request->getPost('title'),
            "created_at" => $now,
            "last_activity_at" => $now,
            "ticket_type_id" => $ticket_type_id,
            "labels" => $labels,
            "assigned_to" => $assigned_to ? $assigned_to : 0
        );

        $submitted_custom_fields = $this->collectSubmittedCustomFieldValues();

        //match with the existing client
        $email = $this->request->getPost('email');
        $contact_info = $this->Users_model->get_one_where(array("email" => $email, "user_type" => "client", "deleted" => 0));

        if ($contact_info->id) {
            //created by existing client
            $ticket_data["client_id"] = $contact_info->client_id;
            $ticket_data["created_by"] = $contact_info->id;
            $ticket_data["requested_by"] = $contact_info->id;
        } else {
            //unknown client
            $ticket_data["creator_email"] = $email;
            $ticket_data["client_id"] = 0;
            $ticket_data["created_by"] = 0;
            $ticket_data["requested_by"] = 0;
            $combined_name = trim($first_name . " " . $last_name);
            $ticket_data["creator_name"] = $combined_name ? $combined_name : "";
        }

        $ticket_data = clean_data($ticket_data);

        $ticket_id = $this->Tickets_model->ci_save($ticket_data);

        if ($ticket_id) {

            save_custom_fields("tickets", $ticket_id, 0, "client");

            //save ticket's comment
            $description = (string) $this->request->getPost('description');

            $form_data = array();

            if ($ticket_type_title) {
                $form_data['request_type'] = $ticket_type_title;
            }

            $form_data = $form_data + array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'company_name' => $company_name,
                'email' => $email,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'zip' => $zip,
                'phone' => $phone
            );

            if ($lead_source_id) {
                $lead_source_label = "";
                $lead_source = $this->Lead_source_model->get_one($lead_source_id);
                if ($lead_source && $lead_source->id) {
                    $lead_source_label = $lead_source->title;
                }

                $form_data['lead_source'] = $lead_source_label ? $lead_source_label : $lead_source_id;
            }

            $form_data = $this->filterEmptyFormData($form_data);

            $contact_labels = array(
                'first_name' => app_lang('first_name'),
                'last_name' => app_lang('last_name'),
                'company_name' => app_lang('company_name'),
                'email' => app_lang('email'),
                'address' => app_lang('address'),
                'city' => app_lang('city'),
                'state' => app_lang('state'),
                'zip' => app_lang('zip'),
                'phone' => app_lang('phone'),
                'lead_source' => app_lang('lead_source')
            );

            if ($ticket_type_title) {
                $request_type_label = app_lang('request_type');
                if ($request_type_label === 'request_type') {
                    $request_type_label = 'Request Type';
                }

                $contact_labels = array('request_type' => $request_type_label) + $contact_labels;
            }

            $contact_lines = array();
            foreach ($contact_labels as $key => $label) {
                if (!isset($form_data[$key])) {
                    continue;
                }

                $value = $form_data[$key];
                $contact_lines[] = "<strong>" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . ":</strong> " . nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
            }

            $sections = array();
            if (!empty($contact_lines)) {
                $contact_heading = app_lang('contact_information');
                if ($contact_heading === 'contact_information') {
                    $contact_heading = 'Contact Information';
                }

                $contact_block = "<div><strong>" . htmlspecialchars($contact_heading, ENT_QUOTES, 'UTF-8') . "</strong></div><div>" . implode("</div><div>", $contact_lines) . "</div>";
                $sections[] = $contact_block;
            }

            if (!empty($submitted_custom_fields)) {
                $custom_lines = array();
                foreach ($submitted_custom_fields as $field) {
                    $custom_lines[] = "<strong>" . htmlspecialchars($field['label'], ENT_QUOTES, 'UTF-8') . ":</strong> " . nl2br(htmlspecialchars($field['value'], ENT_QUOTES, 'UTF-8'));
                }

                if (!empty($custom_lines)) {
                    $custom_heading = app_lang('additional_information');
                    if ($custom_heading === 'additional_information') {
                        $custom_heading = 'Additional Information';
                    }

                    $custom_block = "<div><strong>" . htmlspecialchars($custom_heading, ENT_QUOTES, 'UTF-8') . "</strong></div><div>" . implode("</div><div>", $custom_lines) . "</div>";
                    $sections[] = $custom_block;
                }
            }

            if (!empty($sections)) {
                $description = implode("<br /><br />", $sections) . "<br /><br />" . $description;
            }

            $comment_data = array(
                "description" => $description,
                "ticket_id" => $ticket_id,
                "created_by" => $contact_info->id ? $contact_info->id : 0,
                "created_at" => $now
            );

            $comment_data = clean_data($comment_data);

            $target_path = get_setting("timeline_file_path");
            $comment_data["files"] = move_files_from_temp_dir_to_permanent_dir($target_path, "ticket");

            $ticket_comment_id = $this->Ticket_comments_model->ci_save($comment_data);

            if ($ticket_id && $ticket_comment_id) {
                $notification_payload = array();
                if (!empty($form_data)) {
                    $notification_payload['form_data'] = $form_data;
                }

                if (!empty($submitted_custom_fields)) {
                    $custom_field_values = array();
                    foreach ($submitted_custom_fields as $field) {
                        $custom_field_values[] = array(
                            'title' => $field['label'],
                            'value' => $field['value']
                        );
                    }

                    if (!empty($custom_field_values)) {
                        $notification_payload['custom_field_values'] = $custom_field_values;
                    }
                }

                if ($ticket_type_id || $ticket_type_title !== '') {
                    $notification_payload['request_type'] = array(
                        'id' => $ticket_type_id,
                        'title' => $ticket_type_title
                    );
                }

                add_auto_reply_to_ticket($ticket_id);

                $notification_options = array(
                    "ticket_id" => $ticket_id,
                    "ticket_comment_id" => $ticket_comment_id,
                    "exclude_ticket_creator" => true
                );

                if (!empty($notification_payload)) {
                    $notification_options['description'] = json_encode($notification_payload, JSON_UNESCAPED_UNICODE);
                }

                log_notification("ticket_created", $notification_options, $contact_info->id ? $contact_info->id : "0");

                if ($redirect_url && !$this->request->isAJAX()) {
                    return redirect()->to($redirect_url);
                }

                $response = array("success" => true, 'message' => app_lang('ticket_submission_message'));

                if ($redirect_url) {
                    $response['redirect_url'] = $redirect_url;
                }

                echo json_encode($response);

                return true;
            }
        }

        echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
    }

    function embedded_code_modal_form() {
        $embedded_code = "<iframe width='768' height='840' src='" . get_uri("external_tickets") . "' frameborder='0'></iframe>";

        if (get_setting("enable_embedded_form_to_get_tickets")) {
            $view_data['embedded'] = $embedded_code;
        } else {
            $view_data['embedded'] = "Please save the settings first to see the code.";
        }

        $view_data['ticket_types'] = $this->Ticket_types_model->get_all_where(array("deleted" => 0))->getResult();
        $view_data['assignees'] = $this->Users_model->get_all_where(array("user_type" => "staff", "deleted" => 0, "status" => "active"))->getResult();
        $view_data['labels'] = $this->Labels_model->get_details(array("context" => "ticket"))->getResult();
        $view_data['custom_fields'] = $this->getEmbeddableTicketCustomFields();

        return $this->template->view('external_tickets/embedded_code_modal_form', $view_data);
    }

    function ticket_html_form_code_modal_form() {
        $view_data['ticket_html_form_code'] = $this->_ticket_html_form_code();
        $view_data['ticket_types'] = $this->Ticket_types_model->get_all_where(array("deleted" => 0))->getResult();
        $view_data['assignees'] = $this->Users_model->get_all_where(array("user_type" => "staff", "deleted" => 0, "status" => "active"))->getResult();
        $view_data['labels'] = $this->Labels_model->get_details(array("context" => "ticket"))->getResult();
        $view_data['custom_fields'] = $this->getEmbeddableTicketCustomFields();

        return $this->template->view('external_tickets/ticket_html_form_code_modal_form', $view_data);
    }

    function get_ticket_html_form_code() {
        $ticket_type_id = (int) $this->request->getPost('ticket_type_id');
        $assignee_id = (int) $this->request->getPost('assigned_to');

        validate_numeric_value($ticket_type_id);
        validate_numeric_value($assignee_id);
        $labels_input = $this->request->getPost('labels');
        $custom_fields_input = $this->request->getPost('custom_fields');

        $label_ids = $this->sanitizeLabelIds($labels_input);
        $custom_field_ids = $this->sanitizeIdList($custom_fields_input);

        echo $this->_ticket_html_form_code($ticket_type_id, $assignee_id, $label_ids, $custom_field_ids);
    }

    private function _ticket_html_form_code($ticket_type_id = 0, $assignee_id = 0, $label_ids = "", array $custom_field_ids = array()) {
        validate_numeric_value($ticket_type_id);
        validate_numeric_value($assignee_id);

        $view_data = array(
            'selected_ticket_type_id' => $ticket_type_id,
            'selected_assignee_id' => $assignee_id,
            'selected_label_ids' => $label_ids,
            'custom_fields' => $this->filterCustomFieldsForEmbed($this->getEmbeddableTicketCustomFields(), $custom_field_ids),
            'ticket_types' => $this->Ticket_types_model->get_all_where(array("deleted" => 0))->getResult()
        );

        return view('external_tickets/ticket_html_form_code', $view_data);
    }

    private function sanitizeLabelIds($label_ids = "") {
        if (!$label_ids) {
            return "";
        }

        if (is_array($label_ids)) {
            $ids = $label_ids;
        } else {
            $ids = preg_split('/[,-]/', $label_ids, -1, PREG_SPLIT_NO_EMPTY);
        }
        $clean = array();

        foreach ($ids as $id) {
            $id = trim($id);
            if ($id === "") {
                continue;
            }
            if (is_numeric($id)) {
                $clean[] = (int) $id;
            }
        }

        $clean = array_values(array_unique($clean));

        return $clean ? implode(',', $clean) : "";
    }

    private function sanitizeIdList($value): array {
        if (!$value) {
            return array();
        }

        if (is_array($value)) {
            $raw_ids = $value;
        } else {
            $raw_ids = preg_split('/[,]/', (string) $value, -1, PREG_SPLIT_NO_EMPTY);
        }

        $clean = array();

        foreach ($raw_ids as $id) {
            if (is_numeric($id)) {
                $clean[] = (int) $id;
            }
        }

        return array_values(array_unique($clean));
    }

    private function getEmbeddableTicketCustomFields(): array {
        $fields = $this->Custom_fields_model->get_combined_details("tickets", 0, 0, "client")->getResult();

        if (!is_array($fields)) {
            return array();
        }

        $unique = array();

        foreach ($fields as $field) {
            if (!isset($field->id)) {
                continue;
            }

            $unique[(int) $field->id] = $field;
        }

        return array_values($unique);
    }

    private function filterCustomFieldsForEmbed($fields, array $selected_ids = array()) {
        if (!is_array($fields)) {
            return array();
        }

        $filtered = array();
        $has_selection = !empty($selected_ids);
        $selected_lookup = array();

        if ($has_selection) {
            foreach ($selected_ids as $id) {
                $selected_lookup[(int) $id] = true;
            }
        }

        foreach ($fields as $field) {
            if (!isset($field->id)) {
                continue;
            }

            $field_id = (int) $field->id;
            $is_selected = $has_selection && isset($selected_lookup[$field_id]);
            $allow_in_embed = !isset($field->show_in_embedded_form) || (int) $field->show_in_embedded_form === 1;

            if ($has_selection) {
                if (!$is_selected) {
                    continue;
                }
            } elseif (!$allow_in_embed) {
                continue;
            }

            $filtered[] = $field;
        }

        return $filtered;
    }

    private function collectSubmittedCustomFieldValues(): array {
        $fields = $this->getEmbeddableTicketCustomFields();

        if (!is_array($fields) || empty($fields)) {
            return array();
        }

        $values = array();

        foreach ($fields as $field) {
            if (!isset($field->id)) {
                continue;
            }

            $field_name = 'custom_field_' . $field->id;
            $raw_value = $this->request->getPost($field_name);

            if ($raw_value === null) {
                continue;
            }

            $normalized = $this->normalizeCustomFieldValue($raw_value);

            if ($normalized === '') {
                continue;
            }

            $label = $field->title_language_key ? app_lang($field->title_language_key) : $field->title;

            if ($label === '') {
                $label = 'Field ' . $field->id;
            }

            $values[] = array(
                'label' => $label,
                'value' => $normalized
            );
        }

        return $values;
    }

    private function normalizeCustomFieldValue($value): string {
        if (is_array($value)) {
            $pieces = array();
            foreach ($value as $item) {
                $normalized = $this->normalizeCustomFieldValue($item);
                if ($normalized !== '') {
                    $pieces[] = $normalized;
                }
            }

            return $pieces ? implode(', ', $pieces) : '';
        }

        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? app_lang('yes') : app_lang('no');
        }

        if (is_scalar($value)) {
            return trim((string) $value);
        }

        return '';
    }

    private function filterEmptyFormData(array $form_data): array {
        $filtered = array();

        foreach ($form_data as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_bool($value)) {
                $filtered[$key] = $value ? app_lang('yes') : app_lang('no');
                continue;
            }

            if (is_scalar($value)) {
                $string_value = (string) $value;
                $trimmed = trim($string_value);

                if ($trimmed === '' && $string_value !== '0') {
                    continue;
                }

                $filtered[$key] = $trimmed;
            }
        }

        return $filtered;
    }
}

/* End of file External_tickets.php */
/* Location: ./app/controllers/External_tickets.php */
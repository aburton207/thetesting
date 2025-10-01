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

        $all_custom_fields = $this->Custom_fields_model->get_combined_details("tickets", 0, 0, "client")->getResult();
        $view_data["custom_fields"] = $this->filterCustomFieldsForEmbed($all_custom_fields, $custom_field_ids);

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
            "title" => "required",
            "description" => "required",
            "email" => "required|valid_email"
        ));

        //check if there reCaptcha is enabled
        //if reCaptcha is enabled, check the validation
        $ReCAPTCHA = new ReCAPTCHA();
        $ReCAPTCHA->validate_recaptcha();

        $now = get_current_utc_time();

        $labels = $this->request->getPost('labels');
        $labels = $labels ? $labels : "";
        validate_list_of_numbers($labels);

        $assigned_to = (int) $this->request->getPost('assigned_to');
        $ticket_type_id = (int) $this->request->getPost('ticket_type_id');

        $ticket_data = array(
            "title" => $this->request->getPost('title'),
            "created_at" => $now,
            "last_activity_at" => $now,
            "ticket_type_id" => $ticket_type_id,
            "labels" => $labels,
            "assigned_to" => $assigned_to ? $assigned_to : 0
        );

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
            $ticket_data["creator_name"] = $this->request->getPost('name') ? $this->request->getPost('name') : "";
        }

        $ticket_data = clean_data($ticket_data);

        $ticket_id = $this->Tickets_model->ci_save($ticket_data);

        if ($ticket_id) {

            save_custom_fields("tickets", $ticket_id, 0, "client");

            //save ticket's comment
            $comment_data = array(
                "description" => $this->request->getPost('description'),
                "ticket_id" => $ticket_id,
                "created_by" => $contact_info->id ? $contact_info->id : 0,
                "created_at" => $now
            );

            $comment_data = clean_data($comment_data);

            $target_path = get_setting("timeline_file_path");
            $comment_data["files"] = move_files_from_temp_dir_to_permanent_dir($target_path, "ticket");

            $ticket_comment_id = $this->Ticket_comments_model->ci_save($comment_data);

            if ($ticket_id && $ticket_comment_id) {
                add_auto_reply_to_ticket($ticket_id);

                log_notification("ticket_created", array("ticket_id" => $ticket_id, "ticket_comment_id" => $ticket_comment_id, "exclude_ticket_creator" => true), $contact_info->id ? $contact_info->id : "0");

                echo json_encode(array("success" => true, 'message' => app_lang('ticket_submission_message')));

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
        $view_data['custom_fields'] = $this->Custom_fields_model->get_details(array("related_to" => "tickets", "show_in_embedded_form" => true))->getResult();

        return $this->template->view('external_tickets/embedded_code_modal_form', $view_data);
    }

    private function sanitizeLabelIds($label_ids = "") {
        if (!$label_ids) {
            return "";
        }

        $ids = preg_split('/[,-]/', $label_ids, -1, PREG_SPLIT_NO_EMPTY);
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

    private function filterCustomFieldsForEmbed($fields, array $selected_ids = array()) {
        if (!is_array($fields)) {
            return array();
        }

        $filtered = array();
        $has_selection = !empty($selected_ids);

        foreach ($fields as $field) {
            if (!isset($field->id)) {
                continue;
            }

            $allow_in_embed = isset($field->show_in_embedded_form) ? (int) $field->show_in_embedded_form === 1 : true;

            if (!$allow_in_embed) {
                continue;
            }

            if ($has_selection && !in_array((int) $field->id, $selected_ids)) {
                continue;
            }

            $filtered[] = $field;
        }

        return $filtered;
    }
}

/* End of file External_tickets.php */
/* Location: ./app/controllers/External_tickets.php */
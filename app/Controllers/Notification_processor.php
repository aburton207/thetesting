<?php

namespace App\Controllers;

class Notification_processor extends App_Controller {

    function __construct() {
        parent::__construct();
        helper('notifications');
    }

    //don't show anything here
    function index() {
        app_redirect("forbidden");
    }

    function create_notification($data = array()) {
        ini_set('max_execution_time', 300); //300 seconds 

        if (!get_setting("log_direct_notifications")) {
            $data = $_POST;
        }

        $event = decode_id(get_array_value($data, "event"), "notification");

        if (!$event) {
            die("Access Denied!");
        }

        $notification_data = get_notification_config($event);

        if (!is_array($notification_data)) {
            die("Access Denied!!");
        }

        $user_id = get_array_value($data, "user_id");
        $activity_log_id = get_array_value($data, "activity_log_id");
        $invoice_id = get_array_value($data, "invoice_id");

        $options = array(
            "project_id" => get_array_value($data, "project_id"),
            "task_id" => get_array_value($data, "task_id"),
            "project_comment_id" => get_array_value($data, "project_comment_id"),
            "ticket_id" => get_array_value($data, "ticket_id"),
            "ticket_comment_id" => get_array_value($data, "ticket_comment_id"),
            "project_file_id" => get_array_value($data, "project_file_id"),
            "leave_id" => get_array_value($data, "leave_id"),
            "post_id" => get_array_value($data, "post_id"),
            "to_user_id" => get_array_value($data, "to_user_id"),
            "activity_log_id" => get_array_value($data, "activity_log_id"),
            "client_id" => get_array_value($data, "client_id"),
            "invoice_payment_id" => get_array_value($data, "invoice_payment_id"),
            "invoice_id" => $invoice_id,
            "estimate_id" => get_array_value($data, "estimate_id"),
            "order_id" => get_array_value($data, "order_id"),
            "estimate_request_id" => get_array_value($data, "estimate_request_id"),
            "actual_message_id" => get_array_value($data, "actual_message_id"),
            "parent_message_id" => get_array_value($data, "parent_message_id"),
            "event_id" => get_array_value($data, "event_id"),
            "announcement_id" => get_array_value($data, "announcement_id"),
            "exclude_ticket_creator" => get_array_value($data, "exclude_ticket_creator"),
            "notification_multiple_tasks" => get_array_value($data, "notification_multiple_tasks"),
            "contract_id" => get_array_value($data, "contract_id"),
            "lead_id" => get_array_value($data, "lead_id"),
            "proposal_id" => get_array_value($data, "proposal_id"),
            "estimate_comment_id" => get_array_value($data, "estimate_comment_id"),
            "subscription_id" => get_array_value($data, "subscription_id"),
            "expense_id" => get_array_value($data, "expense_id"),
            "proposal_comment_id" => get_array_value($data, "proposal_comment_id"),
            "reminder_log_id" => get_array_value($data, "reminder_log_id"),
            "description" => get_array_value($data, "description")
        );

        // Fetch custom fields for estimate request notifications
       if ($event === "estimate_request_submitted"
    && $options["estimate_request_id"]) {

    // Pull every custom field for this request in one shot
    $cf_rows = $this->Custom_field_values_model->get_details([
        "related_to_type" => "estimate_request",
        "related_to_id"   => $options["estimate_request_id"],
    ])->getResult();

    foreach ($cf_rows as $row) {
        $id    = $row->custom_field_id;          // e.g. 246
        $key   = "CUSTOM_FIELD_$id";             // placeholder for value
        $label = "CUSTOM_FIELD_{$id}_LABEL";     // placeholder for label

        // Render the value exactly the way RISE does in grids
        $options[$key] = $this->template->view(
                            "custom_fields/output_" . $row->custom_field_type,
                            ["value" => $row->value],
                            true           // return HTML string
                        );

        // Grab the field’s title once for its label
        $field_info         = $this->Custom_fields_model->get_one($id);
        $options[$label]    = $field_info->title;   // plain text
    }
}

        //get data from plugin by parsing 'plugin_'
        foreach ($data as $key => $value) {
            if (strpos($key, 'plugin_') !== false) {
                $options[$key] = $value;
            }
        }

        //classify the task modification parts
        if ($event == "project_task_updated" || $event == "general_task_updated") {
            $notify_to_array = $this->_clasified_task_modification($event, $options, $activity_log_id);

            if (is_array($notify_to_array)) {
                if (!get_array_value($notify_to_array, array_search("all", $notify_to_array))) {
                    $options["notify_to_admins_only"] = true;
                }
            }
        }

        //get reminder tasks
        $reminder_tasks = null;
        if (get_array_value($options, "notification_multiple_tasks")) {
            $reminder_tasks = $this->get_reminder_tasks($event);
            if (!$reminder_tasks) {
                return;
            }

            $notification_multiple_tasks_data = get_notification_multiple_tasks_data($reminder_tasks, $event);
            $notification_multiple_tasks_notify_to_user_ids = get_array_value($notification_multiple_tasks_data, "notify_to_user_ids");
            $options["multiple_tasks_notify_to_user_ids"] = $notification_multiple_tasks_notify_to_user_ids ? implode(',', $notification_multiple_tasks_notify_to_user_ids) : "";
            $options["multiple_tasks_user_wise"] = get_array_value($notification_multiple_tasks_data, "user_wise_tasks");
        }

        //save reminder date
        $this->_save_reminder_date($event, $invoice_id, $reminder_tasks);

        $this->_update_notification_status_of_reminder($event, $options);

        $this->Notifications_model->create_notification($event, $user_id, $options);
    }

    private function get_reminder_tasks($event) {
        $reminder_date = get_setting($event);
        if ($reminder_date) {
            $date = get_today_date();
            $start_date = add_period_to_date($date, $reminder_date, "days");
            $todo_status_id = $this->Task_status_model->get_one_where(array("key_name" => "done", "deleted" => 0));

            if ($event == "project_task_deadline_overdue_reminder") {
                $start_date = subtract_period_from_date($date, $reminder_date, "days");
            } else if ($event == "project_task_reminder_on_the_day_of_deadline") {
                $start_date = $date;
            }

            return $this->Tasks_model->get_details(array(
                "exclude_status_id" => $todo_status_id->id,
                "start_date" => $start_date,
                "deadline" => $start_date,
                "exclude_reminder_date" => $date,
                "context" => "project",
                "sort_by_project" => true
            ))->getResult();
        }
    }

    private function _clasified_task_modification(&$event, &$options, $activity_log_id = 0) {
        if ($activity_log_id) {
            $activity = $this->Activity_logs_model->get_one($activity_log_id);
            if ($activity && $activity->changes) {
                $notify_to_array = get_change_logs_array($activity->changes, $activity->log_type, $activity->action, true);
                $changes = unserialize($activity->changes);

                if (is_array($changes) && count($changes) == 1 && get_array_value($changes, "assigned_to")) {
                    if ($event == "project_task_updated") {
                        $event = "project_task_assigned";
                    } else if ($event == "general_task_updated") {
                        $event = "general_task_assigned";
                    }

                    $assigned_to = get_array_value($changes, "assigned_to");
                    $new_assigned_to = get_array_value($assigned_to, "to");

                    $options["to_user_id"] = $new_assigned_to;
                    $options["activity_log_id"] = "";
                }

                if (is_array($changes) && get_array_value($changes, "status_id")) {
                    $status = get_array_value($changes, "status_id");
                    $new_status = get_array_value($status, "to");

                    if ($event == "project_task_updated") {
                        if ($new_status == "1") {
                            $event = "project_task_reopened";
                            $options["activity_log_id"] = "";
                        } else if ($new_status == "2") {
                            $event = "project_task_started";
                            $options["activity_log_id"] = "";
                        } else if ($new_status == "3") {
                            $event = "project_task_finished";
                            $options["activity_log_id"] = "";
                        } else {
                            $event = "project_task_updated";
                        }
                    } else if ($event == "general_task_updated") {
                        if ($new_status == "1") {
                            $event = "general_task_reopened";
                            $options["activity_log_id"] = "";
                        } else if ($new_status == "2") {
                            $event = "general_task_started";
                            $options["activity_log_id"] = "";
                        } else if ($new_status == "3") {
                            $event = "general_task_finished";
                            $options["activity_log_id"] = "";
                        } else {
                            $event = "project_task_updated";
                        }
                    }
                }

                return $notify_to_array;
            }
        }
    }

    private function _save_reminder_date(&$event, $invoice_id = 0, $notification_multiple_tasks = array()) {
        if ($invoice_id) {
            $invoice_reminder_date = array();
            if ($event == "invoice_due_reminder_before_due_date" || $event == "invoice_overdue_reminder") {
                $invoice_reminder_date["due_reminder_date"] = get_my_local_time();
            }
            if ($event == "recurring_invoice_creation_reminder") {
                $invoice_reminder_date["recurring_reminder_date"] = get_my_local_time();
            }
            if (count($invoice_reminder_date)) {
                $this->Invoices_model->ci_save($invoice_reminder_date, $invoice_id);
            }
        }

        if ($notification_multiple_tasks) {
            foreach ($notification_multiple_tasks as $task_info) {
                $data["reminder_date"] = get_my_local_time();
                $this->Tasks_model->save_reminder_date($data, $task_info->id);
            }
        }
    }

    private function _update_notification_status_of_reminder($event, $options) {
        if ($event !== "subscription_renewal_reminder") {
            return false;
        }

        $reminder_log_id = get_array_value($options, "reminder_log_id");
        if (!$reminder_log_id) {
            return false;
        }

        $reminder_status_data["notification_status"] = "completed";
        $this->Reminder_logs_model->ci_save($reminder_status_data, $reminder_log_id);
    }
}
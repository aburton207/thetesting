<?php

use App\Controllers\Security_Controller;
use App\Controllers\App_Controller;

/*
 * Define who are allowed to receive notifications
 * Using following terms:
 * team_members, team,
 * project_members, client_primary_contact, client_all_contacts, task_assignee, task_collaborators, comment_creator, leave_applicant, ticket_creator, ticket_assignee, post_creator
 */

if (!function_exists('get_notification_config')) {

    function get_notification_config($event = "", $key = "", $info_options = array()) {

        $task_link = function ($options) {

            $url = "";
            $ajax_url = "";
            $id = "";

            if (isset($options->task_id)) {
                $ajax_url = get_uri("tasks/view/");
                $id = $options->task_id;
                $url = get_uri("tasks/view/" . $id);
            }

            if ((isset($options->task_id) && $options->task_id) || (isset($options->project_id) && $options->project_id)) {
                return array("url" => $url, "ajax_modal_url" => $ajax_url, "large_modal" => "1", "id" => $id);
            } else {
                //return all tasks link for reminder notifications
                return array("url" => get_uri("tasks/all_tasks"));
            }
        };

        $project_link = function ($options) {
            $url = "";
            if (isset($options->project_id)) {
                $url = get_uri("projects/view/" . $options->project_id);

                if ($options->event == "project_customer_feedback_added" || $options->event == "project_customer_feedback_replied") {
                    $url .= "/customer_feedback";
                } else if ($options->event == "project_comment_added" || $options->event == "project_comment_replied") {
                    $url .= "/comment";
                }
            }

            return array("url" => $url);
        };

        $project_file_link = function ($options) {

            $url = "";
            $app_modal_url = "";
            $id = "";

            if (isset($options->project_id)) {
                $url = get_uri("projects/view/" . $options->project_id . "/files");
            }

            if (isset($options->project_file_id)) {
                $app_modal_url = get_uri("projects/view_file/" . $options->project_file_id);
                $id = $options->project_file_id;
            }

            return array("url" => $url, "app_modal_url" => $app_modal_url, "id" => $id);
        };

        $client_link = function ($options) {
            $url = "";
            if (isset($options->client_id)) {
                $url = get_uri("clients/view/" . $options->client_id);
            }

            return array("url" => $url);
        };

        $leave_link = function ($options) {
            $url = "";
            $ajax_url = "";
            $id = "";

            if (isset($options->leave_id)) {
                $url = get_uri("dashboard");
                $ajax_url = get_uri("leaves/application_details");
                $id = $options->leave_id;
            }

            return array("url" => $url, "ajax_modal_url" => $ajax_url, "id" => $id);
        };

        $ticket_link = function ($options) {
            $url = "";
            if (isset($options->ticket_id)) {
                $url = get_uri("tickets/view/" . $options->ticket_id);
            }

            return array("url" => $url);
        };

        $invoice_link = function ($options) {
            $url = "";
            if (isset($options->invoice_id)) {
                $url = get_uri("invoices/preview/" . $options->invoice_id);
            }

            return array("url" => $url);
        };

        $estimate_link = function ($options) {
            $url = "";
            if (isset($options->estimate_id)) {
                $url = get_uri("estimates/preview/" . $options->estimate_id);
            }

            return array("url" => $url);
        };

        $order_link = function ($options) {
            $url = "";
            if (isset($options->order_id)) {
                $url = get_uri("store/order_preview/" . $options->order_id . "/1");
            }

            return array("url" => $url);
        };

        $estimate_request_link = function ($options) {
            $url = "";
            if (isset($options->estimate_request_id)) {
                $url = get_uri("estimate_requests/view_estimate_request/" . $options->estimate_request_id);
            }

            return array("url" => $url);
        };

        $message_link = function ($options) {
            $url = "";
            if (isset($options->actual_message_id)) {
                $message_id = isset($options->parent_message_id) && $options->parent_message_id ? $options->parent_message_id : $options->actual_message_id;
                $url = get_uri("messages/inbox/" . $message_id);
            }

            return array("url" => $url);
        };

        $announcement_link = function ($options) {
            $url = "";
            if (isset($options->announcement_id)) {
                $url = get_uri("announcements/view/" . $options->announcement_id);
            }

            return array("url" => $url);
        };

        $event_link = function ($options) {
            $url = "";
            $id = "";

            if (isset($options->event_id)) {
                $id = encode_id($options->event_id, "event_id");
                $url = get_uri("events/index/" . $id);
            }

            if (isset($options->task_id)) {
                $ajax_url = get_uri("events/view");
            }

            return array("url" => $url, "ajax_modal_url" => $ajax_url, "id" => $id);
        };

        $lead_link = function ($options) {
            $url = "";
            if (isset($options->lead_id)) {
                $url = get_uri("leads/view/" . $options->lead_id);
            }

            return array("url" => $url);
        };

        $contract_link = function ($options) {
            $url = "";
            $public_url = "";
            if (isset($options->contract_id)) {
                $url = get_uri("contracts/preview/" . $options->contract_id . "/1");
                $public_url = get_uri("contract/preview/" . $options->contract_id);
            }

            return array("url" => $url, "public_url" => $public_url);
        };

        $proposal_link = function ($options) {
            $url = "";
            $public_url = "";
            if (isset($options->proposal_id)) {
                $url = get_uri("proposals/preview/" . $options->proposal_id . "/1");
                $public_url = get_uri("offer/preview/" . $options->proposal_id);
            }

            return array("url" => $url, "public_url" => $public_url);
        };

        $timeline_link = function ($options) {
            $url = "";
            if (isset($options->post_id)) {
                $url = get_uri("timeline/post/" . $options->post_id);
            }
            return array("url" => $url);
        };

        $subscription_link = function ($options) {
            $url = "";
            if (isset($options->subscription_id)) {
                $url = get_uri("subscriptions/preview/" . $options->subscription_id);
            }

            return array("url" => $url);
        };

        $events = array(
            "project_created" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "team_members", "team"),
                "info" => $project_link
            ),
            "project_completed" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "client_assigned_contacts", "team_members", "team"),
                "info" => $project_link
            ),
            "project_deleted" => array(
                "notify_to" => array("project_members", "client_primary_contact", "client_all_contacts", "client_assigned_contacts", "team_members", "team")
            ),
            "project_task_created" => array(
                "notify_to" => array("project_members", "client_primary_contact", "client_all_contacts", "client_assigned_contacts", "task_assignee", "task_collaborators", "team_members", "team"),
                "info" => $task_link
            ),
            "project_task_updated" => array(
                "notify_to" => array("project_members", "client_primary_contact", "client_all_contacts", "client_assigned_contacts", "task_assignee", "task_collaborators", "team_members", "team"),
                "info" => $task_link
            ),
            "project_task_assigned" => array(
                "notify_to" => array("project_members", "task_assignee", "task_collaborators", "team_members", "team"),
                "info" => $task_link
            ),
            "project_task_started" => array(
                "notify_to" => array("project_members", "client_primary_contact", "client_all_contacts", "client_assigned_contacts", "team_members", "team"),
                "info" => $task_link
            ),
            "project_task_finished" => array(
                "notify_to" => array("project_members", "client_primary_contact", "client_all_contacts", "client_assigned_contacts", "team_members", "team"),
                "info" => $task_link
            ),
            "project_task_reopened" => array(
                "notify_to" => array("project_members", "client_primary_contact", "client_all_contacts", "client_assigned_contacts", "team_members", "team"),
                "info" => $task_link
            ),
            "project_task_commented" => array(
                "notify_to" => array("mentioned_members", "project_members", "client_primary_contact", "client_all_contacts", "client_assigned_contacts", "task_assignee", "task_collaborators", "team_members", "team"),
                "info" => $task_link
            ),
            "project_task_deleted" => array(
                "notify_to" => array("project_members", "client_primary_contact", "client_all_contacts", "client_assigned_contacts", "task_assignee", "task_collaborators", "team_members", "team"),
            ),
            "project_member_added" => array(
                "notify_to" => array("project_members", "team_members", "team"),
                "info" => $project_link
            ),
            "project_member_deleted" => array(
                "notify_to" => array("project_members", "team_members", "team")
            ),
            "project_file_added" => array(
                "notify_to" => array("project_members", "client_primary_contact", "client_all_contacts", "client_assigned_contacts", "team_members", "team"),
                "info" => $project_file_link
            ),
            "project_file_deleted" => array(
                "notify_to" => array("project_members", "client_primary_contact", "client_all_contacts", "client_assigned_contacts", "team_members", "team")
            ),
            "project_file_commented" => array(
                "notify_to" => array("mentioned_members", "project_members", "client_primary_contact", "client_all_contacts", "client_assigned_contacts", "team_members", "team"),
                "info" => $project_file_link
            ),
            "project_comment_added" => array(
                "notify_to" => array("mentioned_members", "project_members", "team_members", "team"),
                "info" => $project_link
            ),
            "project_comment_replied" => array(
                "notify_to" => array("mentioned_members", "project_members", "comment_creator", "team_members", "team"),
                "info" => $project_link
            ),
            "project_customer_feedback_added" => array(
                "notify_to" => array("mentioned_members", "project_members", "client_primary_contact", "client_all_contacts", "client_assigned_contacts", "team_members", "team"),
                "info" => $project_link
            ),
            "project_customer_feedback_replied" => array(
                "notify_to" => array("mentioned_members", "project_members", "client_primary_contact", "client_all_contacts", "client_assigned_contacts", "comment_creator", "team_members", "team"),
                "info" => $project_link
            ),
            "client_signup" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $client_link
            ),
            "client_contact_requested_account_removal" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $client_link
            ),
            "invoice_online_payment_received" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $invoice_link
            ),
            "invoice_payment_confirmation" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts"),
                "info" => $invoice_link
            ),
            "recurring_invoice_created_vai_cron_job" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "team_members", "team"),
                "info" => $invoice_link
            ),
            "invoice_due_reminder_before_due_date" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "team_members", "team"),
                "info" => $invoice_link
            ),
            "invoice_overdue_reminder" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "team_members", "team"),
                "info" => $invoice_link
            ),
            "recurring_invoice_creation_reminder" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "team_members", "team"),
                "info" => $invoice_link
            ),
            "leave_application_submitted" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $leave_link
            ),
            "leave_approved" => array(
                "notify_to" => array("leave_applicant", "team_members", "team"),
                "info" => $leave_link
            ),
            "leave_assigned" => array(
                "notify_to" => array("leave_applicant", "team_members", "team"),
                "info" => $leave_link
            ),
            "leave_rejected" => array(
                "notify_to" => array("leave_applicant", "team_members", "team"),
                "info" => $leave_link
            ),
            "leave_canceled" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $leave_link
            ),
            "ticket_created" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "ticket_creator", "ticket_assignee", "team_members", "team"),
                "info" => $ticket_link
            ),
            "ticket_commented" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "ticket_creator", "ticket_assignee", "team_members", "team"),
                "info" => $ticket_link
            ),
            "ticket_closed" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "ticket_creator", "ticket_assignee", "team_members", "team"),
                "info" => $ticket_link
            ),
            "ticket_reopened" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "ticket_creator", "ticket_assignee", "team_members", "team"),
                "info" => $ticket_link
            ),
            "estimate_request_received" => array(
                "notify_to" => array("team_members", "team", "assigned_to"),
            "info" => $estimate_request_link
            ),
            "estimate_accepted" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $estimate_link
            ),
            "estimate_rejected" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $estimate_link
            ),
            "new_message_sent" => array(
                "notify_to" => array("recipient"),
                "info" => $message_link
            ),
            "message_reply_sent" => array(
                "notify_to" => array("recipient"),
                "info" => $message_link
            ),
            "new_event_added_in_calendar" => array(
                "notify_to" => array("recipient"),
                "info" => $event_link
            ),
            "calendar_event_modified" => array(
                "notify_to" => array("recipient"),
                "info" => $event_link
            ),
            "new_announcement_created" => array(
                "notify_to" => array("recipient"),
                "info" => $announcement_link
            ),
            "bitbucket_push_received" => array(
                "notify_to" => array("project_members", "client_primary_contact", "client_all_contacts", "task_assignee", "task_collaborators", "team_members", "team"),
                "info" => $task_link
            ),
            "github_push_received" => array(
                "notify_to" => array("project_members", "client_primary_contact", "client_all_contacts", "task_assignee", "task_collaborators", "team_members", "team"),
                "info" => $task_link
            ),
            "project_task_deadline_pre_reminder" => array(
                "notify_to" => array("task_assignee", "project_members", "team_members", "team"),
                "info" => $task_link
            ),
            "project_task_deadline_overdue_reminder" => array(
                "notify_to" => array("task_assignee", "project_members", "team_members", "team"),
                "info" => $task_link
            ),
            "project_task_reminder_on_the_day_of_deadline" => array(
                "notify_to" => array("task_assignee", "project_members", "team_members", "team"),
                "info" => $task_link
            ),
            "recurring_task_created_via_cron_job" => array(
                "notify_to" => array("project_members", "client_primary_contact", "client_all_contacts", "task_assignee", "task_collaborators", "team_members", "team"),
                "info" => $task_link
            ),
            "lead_created" => array(
                "notify_to" => array("owner", "team_members", "team"),
                "info" => $lead_link
            ),
            "client_created_from_lead" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $client_link
            ),
            "contract_accepted" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $contract_link
            ),
            "contract_rejected" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $contract_link
            ),
            "proposal_accepted" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $proposal_link
            ),
            "proposal_rejected" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $proposal_link
            ),
            "new_order_received" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $order_link
            ),
            "order_status_updated" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "order_creator_contact", "team_members", "team"),
                "info" => $order_link
            ),
            "timeline_post_commented" => array(
                "notify_to" => array("post_creator", "team_members", "team"),
                "info" => $timeline_link
            ),
            "created_a_new_post" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $timeline_link
            ),
            "invited_client_contact_signed_up" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $client_link
            ),
            "ticket_assigned" => array(
                "notify_to" => array("ticket_assignee", "team_members", "team"),
                "info" => $ticket_link
            ),
            "invoice_manual_payment_added" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts"),
                "info" => $invoice_link
            ),
            "estimate_commented" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "estimate_creator", "team_members", "team"),
                "info" => $estimate_link
            ),
            "subscription_request_sent" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "team_members", "team"),
                "info" => $subscription_link
            ),
            "general_task_created" => array(
                "notify_to" => array("task_assignee", "task_collaborators", "team_members", "team"),
                "info" => $task_link
            ),
            "general_task_updated" => array(
                "notify_to" => array("task_assignee", "task_collaborators", "team_members", "team"),
                "info" => $task_link
            ),
            "general_task_assigned" => array(
                "notify_to" => array("task_assignee", "task_collaborators", "team_members", "team"),
                "info" => $task_link
            ),
            "general_task_started" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $task_link
            ),
            "general_task_finished" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $task_link
            ),
            "general_task_reopened" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $task_link
            ),
            "general_task_deleted" => array(
                "notify_to" => array("task_assignee", "task_collaborators", "team_members", "team"),
                "info" => $task_link
            ),
            "general_task_commented" => array(
                "notify_to" => array("mentioned_members", "task_assignee", "task_collaborators", "team_members", "team"),
                "info" => $task_link
            ),
            "subscription_started" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "team_members", "team"),
                "info" => $subscription_link
            ),
            "subscription_invoice_created_via_cron_job" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "team_members", "team"),
                "info" => $invoice_link
            ),
            "subscription_cancelled" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "team_members", "team"),
                "info" => $subscription_link
            ),
            "proposal_commented" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "proposal_creator", "team_members", "team"),
                "info" => $proposal_link
            ),
            "proposal_preview_opened" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $proposal_link
            ),
            "proposal_email_opened" => array(
                "notify_to" => array("team_members", "team"),
                "info" => $proposal_link
            ),
            "subscription_renewal_reminder" => array(
                "notify_to" => array("client_primary_contact", "client_all_contacts", "team_members", "team"),
                "info" => $subscription_link
            )
        );

        //get config data from hook
        try {
            $events = app_hooks()->apply_filters('app_filter_notification_config', $events);
        } catch (\Exception $ex) {
            log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
        }

        if ($event) {
            $result = get_array_value($events, $event);
            if ($key && $result) {
                $key_result = get_array_value($result, $key);
                if ($info_options && $key_result) {
                    return $key_result($info_options);
                } else {
                    return $key_result;
                }
            } else {
                return $result;
            }
        } else {
            return $events;
        }
    }
}



/*
 * Send notification emails
 */
if (!function_exists('send_notification_emails')) {

    function send_notification_emails($notification_id, $email_notify_to = array(), $extra_data = array()) {

        $ci = new App_Controller();

        $notification = $ci->Notifications_model->get_email_notification($notification_id);

        if (!$notification) {
            return false;
        }

        $url = get_uri();
        $parser_data = array();
        $info = get_notification_config($notification->event, "info", $notification);

        $email_options = array();
        $attachement_url = null;

        if (is_array($info) && get_array_value($info, "url")) {
            $url = get_array_value($info, "url");
        }

        $public_url = "";
        if (is_array($info) && get_array_value($info, "public_url")) {
            $public_url = get_array_value($info, "public_url");
        }

        $parser_data["APP_TITLE"] = get_setting("app_title");

        $Company_model = model('App\Models\Company_model');
        $company_info = $Company_model->get_one_where(array("is_default" => true));
        $parser_data["COMPANY_NAME"] = $company_info->name;

        $template_name = "";

        $notification_multiple_tasks_user_wise = get_array_value($extra_data, "notification_multiple_tasks_user_wise");

        if ($notification->category == "ticket" && $notification->event !== "ticket_assigned") {
            $template_name = $notification->event;

            $parser_data["TICKET_ID"] = $notification->ticket_id;
            $parser_data["TICKET_TITLE"] = $notification->ticket_title;
            $parser_data["USER_NAME"] = $notification->user_name;
            $parser_data["TICKET_CONTENT"] = custom_nl2br($notification->ticket_comment_description ? $notification->ticket_comment_description : "");
            $parser_data["TICKET_URL"] = $url;

            //add attachment
            if ($notification->ticket_comment_id) {
                $comments_options = array("id" => $notification->ticket_comment_id);
                $comment_info = $ci->Ticket_comments_model->get_details($comments_options)->getRow();
                if ($comment_info->files) {
                    $email_options["attachments"] = prepare_attachment_of_files(get_setting("timeline_file_path"), $comment_info->files);
                }
            }

            //add imap email as reply-to email address, if it's enabled
            if (get_setting("enable_email_piping") && get_setting("imap_authorized")) {
                $imap_email = get_setting("imap_email");
                if (get_setting('imap_type') === "microsoft_outlook") {
                    $imap_email = get_setting("outlook_imap_email");
                }

                $email_options["reply_to"] = $imap_email;
            }

            //add custom variable data
            $custom_variables_data = get_custom_variables_data("tickets", $notification->ticket_id);
            if ($custom_variables_data) {
                $parser_data = array_merge($parser_data, $custom_variables_data);
            }
        } else if ($notification->event == "invoice_payment_confirmation" || $notification->event == "invoice_manual_payment_added") {
            if ($notification->event == "invoice_payment_confirmation") {
                $template_name = "invoice_payment_confirmation";
            } else if ($notification->event == "invoice_manual_payment_added") {
                $template_name = "invoice_manual_payment_added";

                $parser_data["ADDED_BY"] = $notification->user_name;
                $parser_data["PAYMENT_NOTE"] = $notification->manual_payment_note;
            }

            $parser_data["PAYMENT_AMOUNT"] = to_currency($notification->payment_amount, $notification->client_currency_symbol);
            $parser_data["INVOICE_ID"] = $notification->payment_invoice_display_id;
            $parser_data["INVOICE_FULL_ID"] = $notification->payment_invoice_display_id;
            $parser_data["INVOICE_URL"] = $url;
        } else if ($notification->event == "new_message_sent" || $notification->event == "message_reply_sent") {
            $template_name = "message_received";

            $message_info = $ci->Messages_model->get_details(array("id" => $notification->actual_message_id))->row;
            $parser_data["SUBJECT"] = $message_info->subject;

            //reply? find the subject from the parent meessage
            if ($notification->event == "message_reply_sent") {
                $main_message_info = $ci->Messages_model->get_details(array("id" => $message_info->message_id))->row;
                $parser_data["SUBJECT"] = $main_message_info->subject;
            }

            $parser_data["USER_NAME"] = $message_info->user_name;
            $parser_data["MESSAGE_CONTENT"] = nl2br($message_info->message ? $message_info->message : "");
            $parser_data["MESSAGE_URL"] = $url;

            if ($message_info->files) {
                $email_options["attachments"] = prepare_attachment_of_files(get_setting("timeline_file_path"), $message_info->files);
            }
        } else if ($notification->event == "recurring_invoice_created_vai_cron_job" || $notification->event == "invoice_due_reminder_before_due_date" || $notification->event == "invoice_overdue_reminder" || $notification->event == "recurring_invoice_creation_reminder") {

            //get the specific email template
            if ($notification->event == "recurring_invoice_created_vai_cron_job") {
                $template_name = "send_invoice";

                $default_bcc = get_setting('send_bcc_to');
                if ($default_bcc) {
                    $email_options["bcc"] = $default_bcc;
                }
            } else if ($notification->event == "invoice_due_reminder_before_due_date") {
                $template_name = "invoice_due_reminder_before_due_date";
            } else if ($notification->event == "invoice_overdue_reminder") {
                $template_name = "invoice_overdue_reminder";
            } else if ($notification->event == "recurring_invoice_creation_reminder") {
                $template_name = "recurring_invoice_creation_reminder";
            }

            $invoice_data = get_invoice_making_data($notification->invoice_id);
            $invoice_info = get_array_value($invoice_data, "invoice_info");
            $invoice_total_summary = get_array_value($invoice_data, "invoice_total_summary");

            $primary_contact = $ci->Clients_model->get_primary_contact($invoice_info->client_id, true);

            $parser_data["INVOICE_ID"] = $notification->invoice_id;
            $parser_data["CONTACT_FIRST_NAME"] = isset($primary_contact->first_name) ? $primary_contact->first_name : "";
            $parser_data["CONTACT_LAST_NAME"] = isset($primary_contact->last_name) ? $primary_contact->last_name : "";
            $parser_data["BALANCE_DUE"] = to_currency($invoice_total_summary->balance_due, $invoice_total_summary->currency_symbol);
            $parser_data["DUE_DATE"] = format_to_date($invoice_info->due_date, false);
            $parser_data["PROJECT_TITLE"] = $invoice_info->project_title;
            $parser_data["INVOICE_URL"] = $url;
            $parser_data["INVOICE_FULL_ID"] = $notification->invoice_display_id;

            $attachement_url = prepare_invoice_pdf($invoice_data, "send_email");
            $email_options["attachments"] = array(array("file_path" => $attachement_url));

            if ($notification->event == "recurring_invoice_creation_reminder") {
                $parser_data["NEXT_RECURRING_DATE"] = format_to_date($invoice_info->next_recurring_date, false);
            }

            //if invoice is sending to client, change the invoice status and last email sent date.
            $notify_to_terms = get_array_value($extra_data, "notify_to_terms");
            if (array_search("client_all_contacts", $notify_to_terms) !== false || array_search("client_primary_contact", $notify_to_terms) !== false) {
                $invoice_status_data = array("status" => "not_paid");

                //chenge last email sending time, if there is any email to client
                if (get_array_value($extra_data, "email_sending_to_client")) {
                    $invoice_status_data["last_email_sent_date"] = get_my_local_time();
                }

                $ci->Invoices_model->ci_save($invoice_status_data, $notification->invoice_id);
            }
        } else if ($notification->category == "estimate") {
            if ($notification->event == "estimate_commented") {
                $template_name = "estimate_commented";

                $parser_data["ESTIMATE_ID"] = $notification->estimate_id;
                $parser_data["USER_NAME"] = $notification->user_name;
                $parser_data["COMMENT_CONTENT"] = custom_nl2br($notification->estimate_comment_description ? $notification->estimate_comment_description : "");
                $parser_data["ESTIMATE_URL"] = $url;
            } else if ($notification->event == "estimate_request_received") {
                $template_name = "estimate_request_received";

                $estimate_request_info = $ci->Estimate_requests_model->get_one($notification->estimate_request_id);
                $primary_contact = $ci->Clients_model->get_primary_contact($estimate_request_info->client_id, true);

                $parser_data["CONTACT_FIRST_NAME"] = isset($primary_contact->first_name) ? $primary_contact->first_name : "";
                $parser_data["CONTACT_LAST_NAME"] = isset($primary_contact->last_name) ? $primary_contact->last_name : "";

                $parser_data["ESTIMATE_REQUEST_ID"] = $notification->estimate_request_id;
                $parser_data["ESTIMATE_REQUEST_URL"] = $url;

                //add custom fields and form data if available
                if ($notification->description) {
                    $extra_data = json_decode($notification->description, true);

                    if (!$extra_data && json_last_error() !== JSON_ERROR_NONE) {
                        $extra_data = json_decode(stripslashes($notification->description), true);
                    }

                    if (is_array($extra_data)) {
                        $parser_data['FORM_DATA'] = get_array_value($extra_data, 'form_data');
                        $parser_data['CUSTOM_FIELD_VALUES'] = get_array_value($extra_data, 'custom_field_values');
                        $parser_data['FILES_DATA'] = get_array_value($extra_data, 'files_data');
                        $parser_data['SITE_URL'] = get_uri();
                    }
                }
            } else {
                //attach a pdf copy of estimate
                $estimate_data = get_estimate_making_data($notification->estimate_id);
                $attachement_url = prepare_estimate_pdf($estimate_data, "send_email");
                $email_options["attachments"] = array(array("file_path" => $attachement_url));

                if ($notification->event == "estimate_rejected") {
                    $template_name = "estimate_rejected";
                } else if ($notification->event == "estimate_accepted") {
                    $template_name = "estimate_accepted";
                }

                $parser_data["ESTIMATE_ID"] = $notification->estimate_id;
                $parser_data["ESTIMATE_URL"] = $url;
            }
        } else if ($notification->category == "contract") {
            if ($notification->event == "contract_rejected") {
                $template_name = "contract_rejected";
            } else if ($notification->event == "contract_accepted") {
                $template_name = "contract_accepted";
            }

            $parser_data["CONTRACT_ID"] = $notification->contract_id;
            $parser_data["CONTRACT_URL"] = $url;
            $parser_data["PUBLIC_CONTRACT_URL"] = $public_url . "/" . $notification->contract_public_key;

            $contract_options = array("id" => $notification->contract_id);
            $contract_info = $ci->Contracts_model->get_details($contract_options)->getRow();
            $parser_data["PROJECT_TITLE"] = $contract_info->project_title;
        } else if ($notification->event == "proposal_rejected" || $notification->event == "proposal_accepted" || $notification->event == "proposal_commented") {
            if ($notification->event == "proposal_rejected") {
                $template_name = "proposal_rejected";
            } else if ($notification->event == "proposal_accepted") {
                $template_name = "proposal_accepted";
            } else if ($notification->event == "proposal_commented") {
                $template_name = "proposal_commented";

                $parser_data["USER_NAME"] = $notification->user_name;
                $parser_data["COMMENT_CONTENT"] = custom_nl2br($notification->proposal_comment_description ? $notification->proposal_comment_description : "");
            }

            $parser_data["PROPOSAL_ID"] = $notification->proposal_id;
            $parser_data["PROPOSAL_URL"] = $url;
            $parser_data["PUBLIC_PROPOSAL_URL"] = $public_url . "/" . $notification->proposal_public_key;
        } else if ($notification->category == "order") {
            if ($notification->event == "new_order_received") {
                $template_name = "new_order_received";
            } else {
                $template_name = "order_status_updated";
            }

            $user_info = $ci->Users_model->get_one($notification->user_id);
            if (isset($user_info) && $user_info->user_type == "client") {
                $parser_data["CONTACT_FIRST_NAME"] = $user_info->first_name;
                $parser_data["CONTACT_LAST_NAME"] = $user_info->last_name;
            } else {
                $order_info = $ci->Orders_model->get_one($notification->order_id);
                $primary_contact = $ci->Clients_model->get_primary_contact($order_info->client_id, true);

                if (isset($primary_contact) && $primary_contact) {
                    $parser_data["CONTACT_FIRST_NAME"] = $primary_contact->first_name;
                    $parser_data["CONTACT_LAST_NAME"] = $primary_contact->last_name;
                }
            }

            $parser_data["ORDER_ID"] = $notification->order_id;
            $parser_data["ORDER_URL"] = $url;

            //attach a pdf copy of order
            $order_data = get_order_making_data($notification->order_id);
            $attachement_url = prepare_order_pdf($order_data, "send_email");
            $email_options["attachments"] = array(array("file_path" => $attachement_url));
        } else if ($notification->event == "project_completed") {
            $template_name = "project_completed";

            $parser_data["PROJECT_ID"] = $notification->project_id;
            $parser_data["PROJECT_TITLE"] = $notification->project_title;
            $parser_data["USER_NAME"] = $notification->user_name;
            $parser_data["PROJECT_URL"] = $url;
        } else if ($notification->category == "subscription") {
            $template_name = "subscription_request_sent";

            if ($notification->event == "subscription_started" || $notification->event == "subscription_invoice_created_via_cron_job") {
                if ($notification->event == "subscription_started") {
                    $template_name = "subscription_started";
                } else {
                    $template_name = "subscription_invoice_created_via_cron_job";
                    $invoice_data = get_invoice_making_data($notification->invoice_id);
                    $invoice_info = get_array_value($invoice_data, "invoice_info");
                    $invoice_total_summary = get_array_value($invoice_data, "invoice_total_summary");
                    $parser_data["INVOICE_ID"] = $notification->invoice_id;
                    $parser_data["INVOICE_FULL_ID"] = $notification->invoice_display_id;
                    $parser_data["BALANCE_DUE"] = to_currency($invoice_total_summary->balance_due, $invoice_total_summary->currency_symbol);
                    $parser_data["DUE_DATE"] = format_to_date($invoice_info->due_date, false);
                    $parser_data["INVOICE_URL"] = $url;

                    $default_bcc = get_setting('send_bcc_to');
                    if ($default_bcc) {
                        $email_options["bcc"] = $default_bcc;
                    }

                    $attachement_url = prepare_invoice_pdf($invoice_data, "send_email");
                    $email_options["attachments"] = array(array("file_path" => $attachement_url));
                }

                //if invoice is sending to client, change the invoice last email sent date.
                $notify_to_terms = get_array_value($extra_data, "notify_to_terms");
                if ($notification->invoice_id && (array_search("client_all_contacts", $notify_to_terms) !== false || array_search("client_primary_contact", $notify_to_terms) !== false)) {
                    if (get_array_value($extra_data, "email_sending_to_client")) {
                        $invoice_status_data["last_email_sent_date"] = get_my_local_time();
                    }

                    $ci->Invoices_model->ci_save($invoice_status_data, $notification->invoice_id);
                }
            } else if ($notification->event == "subscription_cancelled") {
                $template_name = "subscription_cancelled";

                $parser_data["CANCELLED_BY"] = $notification->user_name;
            } else if ($notification->event == "subscription_renewal_reminder") {
                $template_name = "subscription_renewal_reminder";

                $parser_data["NEXT_RENEW_DATE"] = format_to_date($notification->subscription_next_renewal_date, false);
            }

            $subscription_info = $ci->Subscriptions_model->get_one($notification->subscription_id);
            $primary_contact = $ci->Clients_model->get_primary_contact($subscription_info->client_id, true);

            $parser_data["CONTACT_FIRST_NAME"] = isset($primary_contact->first_name) ? $primary_contact->first_name : "";
            $parser_data["CONTACT_LAST_NAME"] = isset($primary_contact->last_name) ? $primary_contact->last_name : "";

            $parser_data["SUBSCRIPTION_ID"] = $notification->subscription_id;
            $parser_data["SUBSCRIPTION_TITLE"] = $notification->subscription_title;
            $parser_data["SUBSCRIPTION_URL"] = $url;
        } else if ($notification->event == "project_task_created" || $notification->event == "project_task_assigned" || $notification->event == "project_task_commented" || $notification->event == "project_task_updated" || $notification->event == "project_task_started" || $notification->event == "project_task_finished" || $notification->event == "project_task_reopened" || $notification->event == "project_task_deleted" || $notification->event == "general_task_created" || $notification->event == "general_task_assigned" || $notification->event == "general_task_commented" || $notification->event == "general_task_updated" || $notification->event == "general_task_started" || $notification->event == "general_task_finished" || $notification->event == "general_task_reopened" || $notification->event == "general_task_deleted") {
            if ($notification->event == "project_task_commented" || $notification->event == "general_task_commented") {
                $template_name = "task_commented";

                $parser_data["TASK_COMMENT"] = convert_mentions(convert_comment_link($notification->project_comment_title, false), false);
            } else if ($notification->event == "project_task_assigned" || $notification->event == "general_task_assigned") {
                $template_name = "task_assigned";
            } else {
                $template_name = "task_general";

                $parser_data["EVENT_TITLE"] = "<b>" . $notification->user_name . "</b> " . sprintf(app_lang("notification_" . $notification->event));
            }

            $task_info = $ci->Tasks_model->get_details(array("id" => $notification->task_id))->getRow();
            $context_label = $task_info->context;
            $context_tilte = "";

            if ($context_label == "project" || $context_label == "contract" || $context_label == "subscription" || $context_label == "expense" || $context_label == "ticket") {
                $context_tilte = $task_info->{$task_info->context . "_title"};
            } else if ($context_label == "invoice") {
                $context_tilte = $task_info->invoice_display_id;
            } else if ($context_label == "estimate") {
                $context_tilte = get_estimate_id($task_info->{$task_info->context . "_id"});
            } else if ($context_label == "order") {
                $context_tilte = get_order_id($task_info->{$task_info->context . "_id"});
            } else if ($context_label == "proposal") {
                $context_tilte = get_proposal_id($task_info->{$task_info->context . "_id"});
            } else if ($context_label == "client" || $context_label == "lead") {
                $context_tilte = $task_info->company_name;
            }

            $parser_data["CONTEXT_LABEL"] = app_lang($context_label);
            $parser_data["CONTEXT_TITLE"] = $context_tilte;

            $parser_data["TASK_ID"] = $notification->task_id;
            $parser_data["TASK_TITLE"] = $notification->task_title;
            $parser_data["TASK_DESCRIPTION"] = $notification->task_description;
            $parser_data["USER_NAME"] = $notification->user_name;
            $parser_data["ASSIGNED_TO_USER_NAME"] = $notification->to_user_name;
            $parser_data["TASK_URL"] = $url;
        } else if ($notification->event == "new_announcement_created") {
            $template_name = "announcement_created";

            $parser_data["ANNOUNCEMENT_TITLE"] = $notification->announcement_title;
            $parser_data["ANNOUNCEMENT_CONTENT"] = $notification->announcement_content;
            $parser_data["USER_NAME"] = $notification->user_name;
            $parser_data["ANNOUNCEMENT_URL"] = $url;
        } else {
            $template_name = "general_notification";

            $parser_data["EVENT_TITLE"] = "<b>" . $notification->user_name . "</b> " . sprintf(app_lang("notification_" . $notification->event), $notification->to_user_name);
            $parser_data["NOTIFICATION_URL"] = $url;
            $parser_data["TO_USER_NAME"] = $notification->to_user_name;

            $view_data["notification"] = $notification;
            $parser_data["EVENT_DETAILS"] = view("notifications/notification_description", $view_data);
        }

        $email_template = $ci->Email_templates_model->get_final_template($template_name, true);

        $parser_data["SIGNATURE"] = get_array_value($email_template, "signature_default");
        $parser_data["LOGO_URL"] = get_logo_url();
        $parser = \Config\Services::parser();
        $message = $parser->setData($parser_data)->renderString(get_array_value($email_template, "message_default"));

        $parser_data["EVENT_TITLE"] = $notification->user_name . " " . sprintf(app_lang("notification_" . $notification->event), $notification->to_user_name);
        $subject = $parser->setData($parser_data)->renderString(get_array_value($email_template, "subject_default"));

        // error_log("event: " . $notification->event . PHP_EOL, 3, "notification.txt");
        // error_log("subject: " . $subject . PHP_EOL, 3, "notification.txt");
        // error_log("message: " . $message . PHP_EOL, 3, "notification.txt");
        // 
        //for task reminder notifications, we've to send different emails to different users
        if ($notification_multiple_tasks_user_wise && ($notification->event == "project_task_deadline_pre_reminder" || $notification->event == "project_task_reminder_on_the_day_of_deadline" || $notification->event == "project_task_deadline_overdue_reminder")) {
            //task reminders
            $email_template = $ci->Email_templates_model->get_final_template("project_task_deadline_reminder", true);

            //get the deadline
            //all deadlines are same
            $task_deadline = reset($notification_multiple_tasks_user_wise); //get first user's tasks
            $task_deadline = get_array_value($task_deadline, 0); //first task
            $task_deadline = get_array_value($task_deadline, "task_id"); //task id
            $task_deadline = $ci->Tasks_model->get_one($task_deadline)->deadline;
            $parser_data["DEADLINE"] = format_to_date($task_deadline, false);

            foreach ($notification_multiple_tasks_user_wise as $user_id => $tasks) {
                //prepare all tasks of this user
                $table = view("tasks/notification_multiple_tasks_table", array("tasks" => $tasks));

                $user_info = $ci->Users_model->get_one($user_id);
                $user_email_address = $user_info->email;
                $user_language = $user_info->language;

                $parser_data["RECIPIENTS_EMAIL_ADDRESS"] = $user_email_address;
                $parser_data["SIGNATURE"] = get_array_value($email_template, "signature_$user_language") ? get_array_value($email_template, "signature_$user_language") : get_array_value($email_template, "signature_default");

                $parser_data["TASKS_LIST"] = $table;
                $message = get_array_value($email_template, "message_$user_language") ? get_array_value($email_template, "message_$user_language") : get_array_value($email_template, "message_default");
                $message = $parser->setData($parser_data)->renderString($message);
                $parser_data["EVENT_TITLE"] = $notification->user_name . " " . sprintf(app_lang("notification_" . $notification->event), $notification->to_user_name);
                $subject = get_array_value($email_template, "subject_$user_language") ? get_array_value($email_template, "subject_$user_language") : get_array_value($email_template, "subject_default");
                $subject = $parser->setData($parser_data)->renderString($subject);
                $message = parse_email_template($message, $parser_data);

                if ($user_email_address) {
                    send_app_mail($user_email_address, $subject, $message, $email_options);
                }
            }
        } else {
            if ($email_notify_to && is_array($email_notify_to)) {
                foreach ($email_notify_to as $user) {
                    if (is_string($user)) {
                        $user_email_address = $user;
                        $user_language = "";
                    } else {
                        $user_email_address = $user->email;
                        $user_language = $user->language;
                    }
                    $parser_data["RECIPIENTS_EMAIL_ADDRESS"] = $user_email_address;
                    $parser_data["SIGNATURE"] = get_array_value($email_template, "signature_$user_language") ? get_array_value($email_template, "signature_$user_language") : get_array_value($email_template, "signature_default");

                    $message = get_array_value($email_template, "message_$user_language") ? get_array_value($email_template, "message_$user_language") : get_array_value($email_template, "message_default");
                    $subject = get_array_value($email_template, "subject_$user_language") ? get_array_value($email_template, "subject_$user_language") : get_array_value($email_template, "subject_default");

                    if ($notification->event == "recurring_invoice_created_vai_cron_job") {
                        $invoice_data = get_invoice_making_data($notification->invoice_id);
                        $invoice_info = get_array_value($invoice_data, "invoice_info");
                        $contact_id = $user->id;
                        //add public pay invoice url 
                        if (get_setting("client_can_pay_invoice_without_login") && strpos($message, "PUBLIC_PAY_INVOICE_URL")) {
                            $code = make_random_string();
                            $verification_data = array(
                                "type" => "invoice_payment",
                                "code" => $code,
                                "params" => serialize(array(
                                    "invoice_id" => $notification->invoice_id,
                                    "client_id" => $invoice_info->client_id,
                                    "contact_id" => $contact_id
                                ))
                            );
                            $ci->Verification_model->ci_save($verification_data);
                            $parser_data["PUBLIC_PAY_INVOICE_URL"] = get_uri("pay_invoice/index/" . $code);
                        }
                    }

                    $message = $parser->setData($parser_data)->renderString($message);
                    $subject = $parser->setData($parser_data)->renderString($subject);
                    $message = parse_email_template($message, $parser_data);

                    try {
                        //it'll be used for specific notifications for plugins individually
                        $email_notification_info_of_hook = app_hooks()->apply_filters("app_filter_send_email_notification", array(
                            "notification" => $notification,
                            "parser_data" => $parser_data,
                            "user_language" => $user_language,
                        ));

                        if ($email_notification_info_of_hook && is_array($email_notification_info_of_hook)) {
                            $subject = get_array_value($email_notification_info_of_hook, "subject") ? get_array_value($email_notification_info_of_hook, "subject") : $subject;
                            $message = get_array_value($email_notification_info_of_hook, "message") ? get_array_value($email_notification_info_of_hook, "message") : $message;
                            $email_options = get_array_value($email_notification_info_of_hook, "email_options") ? get_array_value($email_notification_info_of_hook, "email_options") : $email_options;
                            $attachement_url = get_array_value($email_notification_info_of_hook, "attachement_url") ? get_array_value($email_notification_info_of_hook, "attachement_url") : $attachement_url;
                        }
                    } catch (\Exception $ex) {
                        log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
                    }

                    $parser_data["LOGO_URL"] = get_logo_url();
                    $parser_data["EVENT_TITLE"] = $notification->user_name . " " . sprintf(app_lang("notification_" . $notification->event), $notification->to_user_name);

                    send_app_mail($user_email_address, $subject, $message, $email_options);
                }
            } else if ($email_notify_to) { //keep previous method
                try {
                    //it'll be used for specific notifications for plugins individually
                    $email_notification_info_of_hook = app_hooks()->apply_filters("app_filter_send_email_notification", array(
                        "notification" => $notification,
                        "parser_data" => $parser_data
                    ));

                    if ($email_notification_info_of_hook && is_array($email_notification_info_of_hook)) {
                        $subject = get_array_value($email_notification_info_of_hook, "subject") ? get_array_value($email_notification_info_of_hook, "subject") : $subject;
                        $message = get_array_value($email_notification_info_of_hook, "message") ? get_array_value($email_notification_info_of_hook, "message") : $message;
                        $email_options = get_array_value($email_notification_info_of_hook, "email_options") ? get_array_value($email_notification_info_of_hook, "email_options") : $email_options;
                        $attachement_url = get_array_value($email_notification_info_of_hook, "attachement_url") ? get_array_value($email_notification_info_of_hook, "attachement_url") : $attachement_url;
                    }
                } catch (\Exception $ex) {
                    log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
                }

                $email_notify_to_array = explode(",", $email_notify_to);
                foreach ($email_notify_to_array as $email_address) {
                    send_app_mail($email_address, $subject, $message, $email_options);
                }
            }
        }

        // delete the temp attachment
        if ($attachement_url && file_exists($attachement_url)) {
            unlink($attachement_url);
        }
    }
}
function parse_email_template($template, $data) {
    $parser = \Config\Services::parser();

    // Detect whether template already has placeholders for these arrays
    $has_form_data_placeholder = preg_match('/{FORM_DATA}|{NO_FORM_DATA}|{FORM_DATA}.*?{\\/FORM_DATA}/s', $template);
    $has_custom_field_placeholder = preg_match('/{CUSTOM_FIELD_VALUES}|{NO_CUSTOM_FIELDS}|{CUSTOM_FIELD_VALUES}.*?{\\/CUSTOM_FIELD_VALUES}/s', $template);
    $has_files_placeholder = preg_match('/{FILES_DATA}|{NO_FILES}|{FILES_DATA}.*?{\\/FILES_DATA}/s', $template);

    // Don't let the CI parser convert array values to the string "Array".
    // Remove placeholders which hold arrays before parsing, then handle them
    // manually after parsing the simple placeholders.
    $array_keys = array('FORM_DATA', 'CUSTOM_FIELD_VALUES', 'FILES_DATA');
    $simple_data = array_diff_key($data, array_flip($array_keys));

    // Replace simple placeholders
    $template = $parser->setData($simple_data)->renderString($template);

    // Handle FORM_DATA loop
    if (isset($data['FORM_DATA']) && is_array($data['FORM_DATA'])) {
        $form_data_rows = '';
        $custom_labels = [
            'state' => 'Province',
            'zip' => 'Postal Code'
        ];
        foreach ($data['FORM_DATA'] as $key => $value) {
            if ($value) {
                $label = isset($custom_labels[$key]) ? $custom_labels[$key] : ucfirst(str_replace('_', ' ', $key));
                $form_data_rows .= '<tr><td><strong>' . $label . '</strong></td><td>' . htmlspecialchars($value) . '</td></tr>';
            }
        }
        $template = preg_replace('/{FORM_DATA}.*?{\/FORM_DATA}/s', $form_data_rows, $template);
        $template = str_replace('{FORM_DATA}', $form_data_rows, $template);
        $template = str_replace('{NO_FORM_DATA}', '', $template);
        if (!$has_form_data_placeholder && $form_data_rows) {
            $template .= "<table>" . $form_data_rows . "</table>";
        }
    } else {
        $template = preg_replace('/{FORM_DATA}.*?{\/FORM_DATA}/s', '', $template);
        $template = str_replace('{FORM_DATA}', '', $template);
        $template = str_replace('{NO_FORM_DATA}', '<tr><td colspan="2">No standard fields provided.</td></tr>', $template);
    }

    // Handle CUSTOM_FIELD_VALUES loop
    if (isset($data['CUSTOM_FIELD_VALUES']) && is_array($data['CUSTOM_FIELD_VALUES'])) {
        $custom_field_rows = '';
        foreach ($data['CUSTOM_FIELD_VALUES'] as $field) {
            $value = $field['value'];
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $custom_field_rows .= '<tr><td><strong>' . htmlspecialchars($field['title']) . '</strong></td><td>' . htmlspecialchars($value) . '</td></tr>';
        }
        $template = preg_replace('/{CUSTOM_FIELD_VALUES}.*?{\/CUSTOM_FIELD_VALUES}/s', $custom_field_rows, $template);
        $template = str_replace('{CUSTOM_FIELD_VALUES}', $custom_field_rows, $template);
        $template = str_replace('{NO_CUSTOM_FIELDS}', '', $template);
        if (!$has_custom_field_placeholder && $custom_field_rows) {
            $template .= "<table>" . $custom_field_rows . "</table>";
        }
    } else {
        $template = preg_replace('/{CUSTOM_FIELD_VALUES}.*?{\/CUSTOM_FIELD_VALUES}/s', '', $template);
        $template = str_replace('{CUSTOM_FIELD_VALUES}', '', $template);
        $template = str_replace('{NO_CUSTOM_FIELDS}', '<tr><td colspan="2">No custom fields provided.</td></tr>', $template);
    }

    // Handle FILES_DATA loop
    if (isset($data['FILES_DATA']) && is_array($data['FILES_DATA'])) {
        $file_rows = '';
        foreach ($data['FILES_DATA'] as $file) {
            $file_rows .= '<p><a href="' . get_array_value($data, 'SITE_URL') . '/files/timeline/' . $file['file_name'] . '">' . $file['file_name'] . '</a></p>';
        }
        $template = preg_replace('/{FILES_DATA}.*?{\/FILES_DATA}/s', $file_rows, $template);
        $template = str_replace('{FILES_DATA}', $file_rows, $template);
        $template = str_replace('{NO_FILES}', '', $template);
        if (!$has_files_placeholder && $file_rows) {
            $template .= $file_rows;
        }
    } else {
        $template = preg_replace('/{FILES_DATA}.*?{\/FILES_DATA}/s', '', $template);
        $template = str_replace('{FILES_DATA}', '', $template);
        $template = str_replace('{NO_FILES}', '<p>No files attached.</p>', $template);
    }

    return $template;
}
/*
 * Send push notifications
 */
if (!function_exists('send_push_notifications')) {

    function send_push_notifications($event, $push_notify_to, $user_id = 0, $notification_id = 0) {
        $ci = new App_Controller();

        //get credentials
        $pusher_app_id = get_setting("pusher_app_id");
        $pusher_key = get_setting("pusher_key");
        $pusher_secret = get_setting("pusher_secret");
        $pusher_cluster = get_setting("pusher_cluster");

        if ($pusher_app_id && $pusher_key && $pusher_secret && $pusher_cluster) {
            require_once(APPPATH . "ThirdParty/Pusher/vendor/autoload.php");

            $options = array(
                'cluster' => $pusher_cluster,
                'useTLS' => true
            );

            //authorize pusher
            $pusher = new Pusher\Pusher(
                $pusher_key,
                $pusher_secret,
                $pusher_app_id,
                $options
            );

            //get notification message
            $message = app_lang("notification_" . $event);
            if ($notification_id) {
                $to_user_name = $ci->Notifications_model->get_to_user_name($notification_id);
                if ($to_user_name) {
                    $message = sprintf(app_lang("notification_" . $event), $to_user_name);
                }
            }

            //get notification url with indevudual attributes
            $url_attributes = "";
            if ($notification_id) {
                $notification_info = $ci->Notifications_model->get_one($notification_id);
                $url_attributes_array = get_notification_url_attributes($notification_info);
                $url_attributes = get_array_value($url_attributes_array, "url_attributes");
            }

            $user_info = $ci->Users_model->get_one($user_id);

            $data = array(
                "message" => $message,
                "title" => $user_id ? $user_info->first_name . " " . $user_info->last_name : get_setting('app_title'),
                "icon" => get_avatar($user_id ? $user_info->image : "system_bot"),
                "notification_id" => $notification_id,
                "url_attributes" => $url_attributes
            );

            $correct_credentials = false;

            //send events to pusher
            if ($push_notify_to) {
                $push_notify_to_array = explode(",", $push_notify_to);
                foreach ($push_notify_to_array as $user_id) {
                    if ($pusher->trigger('user_' . $user_id . '_channel', 'rise-pusher-event', $data)) {
                        $correct_credentials = true;
                    }
                }
            }

            return $correct_credentials;
        } else {
            return false;
        }
    }
}

/*
 * Get notification url attributes
 */
if (!function_exists('get_notification_url_attributes')) {

    function get_notification_url_attributes($notification) {
        $url = "#";
        $url_attributes = "href='$url'";

        $info = get_notification_config($notification->event, "info", $notification);
        if (is_array($info)) {
            $url = get_array_value($info, "url");
            $ajax_modal_url = get_array_value($info, "ajax_modal_url");
            $app_modal_url = get_array_value($info, "app_modal_url");
            $url_id = get_array_value($info, "id");

            if ($ajax_modal_url) {
                $ajax_modal_url = preg_replace('/\/$/', '', $ajax_modal_url);
                $url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_modal_url' data-post-id='$url_id' ";

                if (get_array_value($info, "large_modal")) {
                    $url_attributes .= " data-modal-lg = '1'";
                }
            } else if ($app_modal_url) {
                $url_attributes = "href='#' data-toggle='app-modal' data-url='$app_modal_url' ";
            } else {
                $url_attributes = "href='$url'";
            }
        }

        return array("url_attributes" => $url_attributes, "url" => $url);
    }
}

/*
 * Get notification multiple tasks
 */
if (!function_exists('get_notification_multiple_tasks_data')) {

    function get_notification_multiple_tasks_data($tasks, $event) {
        $ci = new App_Controller();
        $user_wise_tasks = array();

        //user whose are on the notify to team members or notify to team, will get all tasks
        //other users will get their assigned tasks if it enabled in notification setting
        $notify_to_users_from_settings = array();
        $notify_to_users_from_settings_result = $ci->Notification_settings_model->get_notify_to_users_of_event($event);
        foreach ($notify_to_users_from_settings_result->result as $notify_to_user_id) {
            array_push($notify_to_users_from_settings, $notify_to_user_id->id);
        }

        $notify_to_terms_array = explode(",", $notify_to_users_from_settings_result->notify_to_terms);

        $project_ids = array();
        foreach ($tasks as $task) {
            $task_data = array(
                "task_id" => $task->id,
                "task_title" => $task->title,
                "project_id" => $task->project_id,
                "project_title" => $task->project_title
            );

            //add all tasks to notify to users
            foreach ($notify_to_users_from_settings as $user_id) {
                $user_wise_tasks[$user_id][$task->id] = $task_data;
            }

            //add assigned task to related users
            if ($task->assigned_to && in_array("task_assignee", $notify_to_terms_array) && !in_array($task->assigned_to, $notify_to_users_from_settings)) {
                $user_wise_tasks[$task->assigned_to][$task->id] = $task_data;
            }

            //add project members 
            if (!in_array($task->project_id, $project_ids) && in_array("project_members", $notify_to_terms_array)) {
                $options = array("project_id" => $task->project_id);
                $project_members = $ci->Project_members_model->get_details($options)->getResult();
                foreach ($project_members as $project_member) {
                    $user_wise_tasks[$project_member->user_id][$task->id] = $task_data;
                }

                array_push($project_ids, $task->project_id);
            }
        }

        //prepare notify to user ids
        $notify_to_user_ids = array();
        foreach ($user_wise_tasks as $key => $value) {
            array_push($notify_to_user_ids, $key);
        }

        return array(
            "user_wise_tasks" => $user_wise_tasks,
            "notify_to_user_ids" => $notify_to_user_ids
        );
    }
}

if (!function_exists('send_slack_notification')) {

    function send_slack_notification($event, $user_id = 0, $notification_id = 0, $webhook_url = "") {
        if ($webhook_url) {
            $ci = new App_Controller();

            $message = app_lang("notification_" . $event);
            $notification_description = "";
            $url = "";

            if ($notification_id) {
                $to_user_name = $ci->Notifications_model->get_to_user_name($notification_id);
                if ($to_user_name) {
                    $message = sprintf(app_lang("notification_" . $event), $to_user_name);
                }

                //get notification url
                $notification_info = $ci->Notifications_model->get_email_notification($notification_id);
                $url_attributes_array = get_notification_url_attributes($notification_info);
                $url = get_array_value($url_attributes_array, "url");

                //prepare notification details
                $notification_description = view("notifications/notification_description_for_slack", array("notification" => $notification_info));
            }

            $user_info = $ci->Users_model->get_one($user_id);
            $title = $user_id ? ($user_info->first_name . " " . $user_info->last_name) : get_setting('app_title');
            $avatar = get_avatar($user_id ? $user_info->image : "system_bot");

            $data = array(
                "text" => "$title $message",
                "blocks" => array(
                    array(
                        "type" => "context",
                        "elements" => array(
                            array(
                                "type" => "image",
                                "image_url" => $avatar,
                                "alt_text" => $title
                            ),
                            array(
                                "type" => "mrkdwn",
                                "text" => "*$title* " . ($url ? "<$url|$message>" : $message)
                            )
                        )
                    )
                )
            );

            if ($notification_description) {
                //notification details
                $data["blocks"][] = array(
                    "type" => "context",
                    "elements" => array(
                        array(
                            "type" => "mrkdwn",
                            "text" => str_replace('<br />', '', $notification_description)
                        )
                    )
                );
            }

            $ch = curl_init($webhook_url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            if ($result == "ok") {
                return true;
            }
        }
    }
}
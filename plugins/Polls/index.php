<?php

//Prevent direct access
defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: Polls
  Description: Poll manager for RISE CRM.
  Version: 1.1
  Requires at least: 2.8
  Author: SketchCode
  Author URL: https://codecanyon.net/user/sketchcode
 */

use App\Controllers\Security_Controller;

app_hooks()->add_filter('app_filter_staff_left_menu', function ($sidebar_menu) {
    $poll_access_permission = get_poll_setting("access_all_members");
    $poll_view_permission = get_poll_setting("view_all_members");

    $access_poll_specific_permission = unserialize(get_poll_setting("access_poll_specific"));
    $view_poll_specific_permission = unserialize(get_poll_setting("view_poll_specific"));

    if (!$access_poll_specific_permission) {
        $access_poll_specific_permission = array();
    }
    if (!$view_poll_specific_permission) {
        $view_poll_specific_permission = array();
    }

    $poll_access_permission_specific = get_array_value($access_poll_specific_permission, "manage_polls_specific");
    $poll_access_specific = explode(',', $poll_access_permission_specific);

    $poll_view_permission_specific = get_array_value($view_poll_specific_permission, "view_polls_specific");
    $poll_view_specific = explode(',', $poll_view_permission_specific);

    $instance = new Security_Controller();
    if ($instance->login_user->is_admin || $poll_access_permission || $poll_view_permission || in_array($instance->login_user->id, $poll_access_specific) || in_array($instance->login_user->id, $poll_view_specific)) {
        $sidebar_menu["polls"] = array(
            "name" => "polls",
            "url" => "polls",
            "class" => "bar-chart-2",
            "position" => 6,
            "badge" => polls_count_active_polls(),
            "badge_class" => "bg-primary"
        );
    }

    return $sidebar_menu;
});

//add setting link to the plugin setting
app_hooks()->add_filter('app_filter_action_links_of_Polls', function ($action_links_array) {
    $action_links_array = array(
        anchor(get_uri("poll_settings"), app_lang("settings")),
        anchor(get_uri("polls"), app_lang("polls"))
    );

    return $action_links_array;
});

app_hooks()->add_filter('app_filter_admin_settings_menu', function ($settings_menu) {
    $settings_menu["plugins"][] = array("name" => "polls", "url" => "poll_settings");
    return $settings_menu;
});

//installation: install dependencies
register_installation_hook("Polls", function ($item_purchase_code) {
    include PLUGINPATH . "Polls/install/do_install.php";
});

//uninstallation: remove data from database
register_uninstallation_hook("Polls", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DROP TABLE `" . $dbprefix . "polls`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "poll_answers`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "poll_settings`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "poll_votes`;";
    $db->query($sql_query);

    $sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name`='polls_item_purchase_code';";
    $db->query($sql_query);

    $sql_query = "DELETE FROM `" . $dbprefix . "notification_settings` WHERE `" . $dbprefix . "notification_settings`.`event`='poll_created';";
    $db->query($sql_query);

    $sql_query = "DELETE FROM `" . $dbprefix . "notifications` WHERE `" . $dbprefix . "notifications`.`plugin_poll_id`!='0';";
    $db->query($sql_query);

    $sql_query = "ALTER TABLE `" . $dbprefix . "notifications` DROP `plugin_poll_id`;";
    $db->query($sql_query);

    $sql_query = "DELETE FROM `" . $dbprefix . "email_templates` WHERE `" . $dbprefix . "email_templates`.`template_name`='poll_created';";
    $db->query($sql_query);
});

//update plugin
use Polls\Controllers\Poll_Updates;

register_update_hook("Polls", function () {
    $update = new Poll_Updates();
    return $update->index();
});

//activation: activate notification settings and notifications
register_activation_hook("Polls", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "UPDATE `" . $dbprefix . "notification_settings` SET `deleted` = '0' WHERE `" . $dbprefix . "notification_settings`.`event`='poll_created';";
    $db->query($sql_query);

    $sql_query = "UPDATE `" . $dbprefix . "notifications` SET `deleted` = '0' WHERE `" . $dbprefix . "notifications`.`plugin_poll_id`!='0';";
    $db->query($sql_query);
});

//deactivation: deactivate notification settings and notifications
register_deactivation_hook("Polls", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "UPDATE `" . $dbprefix . "notification_settings` SET `deleted` = '1' WHERE `" . $dbprefix . "notification_settings`.`event`='poll_created';";
    $db->query($sql_query);

    $sql_query = "UPDATE `" . $dbprefix . "notifications` SET `deleted` = '1' WHERE `" . $dbprefix . "notifications`.`plugin_poll_id`!='0';";
    $db->query($sql_query);
});

//add notification category
app_hooks()->add_filter('app_filter_notification_category_suggestion', function ($category_suggestions) {
    $category_suggestions[] = array("id" => "poll", "text" => app_lang("poll"));

    return $category_suggestions;
});

//add notification config
app_hooks()->add_filter('app_filter_notification_config', function ($events_of_hook) {
    $poll_link = function ($options) {
        if (isset($options->plugin_poll_id) && $options->plugin_poll_id) {
            return array("url" => get_uri("polls/view/$options->plugin_poll_id"));
        } else {
            return array("url" => get_uri("polls"));
        }
    };

    $events_of_hook["poll_created"] = array(
        "notify_to" => array("team_members", "team"),
        "info" => $poll_link
    );

    return $events_of_hook;
});

//add create notification sql
app_hooks()->add_filter('app_filter_create_notification_where_query', function ($where_queries_from_hook, $data) {
    $event = get_array_value($data, "event");
    if (!($event == "poll_created")) {
        return $where_queries_from_hook;
    }

    return $where_queries_from_hook;
});

//add notification description
app_hooks()->add_filter('app_filter_notification_description', function ($notification_descriptions, $notification) {
    $notification_descriptions[] = view("Polls\Views\\notifications\\notification_description", array("notification" => $notification));
    return $notification_descriptions;
});

//add notification description for slack
app_hooks()->add_filter('app_filter_notification_description_for_slack', function ($notification_descriptions, $notification) {
    $notification_descriptions[] = view("Polls\Views\\notifications\\notification_description", array("notification" => $notification));
    return $notification_descriptions;
});

//add email template
app_hooks()->add_filter('app_filter_email_templates', function ($templates_array) {
    $templates_array["polls"]["poll_created"] = array("POLL_ID", "POLL_TITLE", "POLL_DESCRIPTION", "POLL_CREATED_BY", "POLL_EXPIRE_AT", "POLL_URL", "LOGO_URL", "SIGNATURE");

    return $templates_array;
});

//modify email notification
app_hooks()->add_filter('app_filter_send_email_notification', function ($data) {
    $notification = get_array_value($data, "notification");
    if (!(isset($notification->event) && ($notification->event === "poll_created"))) {
        return $data;
    }

    $Email_templates_model = model("App\Models\Email_templates_model");
    $Polls_model = new \Polls\Models\Polls_model();
    $poll_info = $Polls_model->get_details(array("id" => $notification->plugin_poll_id))->getRow();

    $email_template = $Email_templates_model->get_final_template("poll_created");

    $parser_data = get_array_value($data, "parser_data");
    $parser_data["SIGNATURE"] = $email_template->signature;
    $parser_data["POLL_ID"] = $poll_info->id;
    $parser_data["POLL_TITLE"] = $poll_info->title;
    $parser_data["POLL_DESCRIPTION"] = $poll_info->description ? $poll_info->description : "";
    $parser_data["POLL_EXPIRE_AT"] = format_to_date($poll_info->expire_at);
    $parser_data["POLL_CREATED_BY"] = $poll_info->created_by_user;
    $parser_data["POLL_URL"] = get_uri("polls/view/$poll_info->id");

    $parser = \Config\Services::parser();
    $message = $parser->setData($parser_data)->renderString($email_template->message);
    $subject = $parser->setData($parser_data)->renderString($email_template->subject);

    $info_array = array(
        "subject" => $subject,
        "message" => $message,
    );

    return $info_array;
});

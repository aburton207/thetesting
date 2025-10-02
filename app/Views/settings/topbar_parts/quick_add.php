<?php
//get the array of hidden menu
$hidden_menu = explode(",", get_setting("hidden_client_menus"));
$permissions = $login_user->permissions;

$client_permission = get_array_value($permissions, "client");
$can_manage_clients = $login_user->is_admin || ($login_user->user_type === "staff" && $client_permission && $client_permission !== "read_only" && $client_permission !== "no");

$lead_permission = get_array_value($permissions, "lead");
$can_manage_leads = get_setting("module_lead") == "1" && ($login_user->is_admin || ($login_user->user_type === "staff" && $lead_permission && $lead_permission !== "read_only" && $lead_permission !== "no"));

$links = "";
$mobile_primary_links = "";

if ($can_manage_clients) {
    $links .= modal_anchor(get_uri("clients/modal_form"), app_lang('add_client'), array(
        "class" => "dropdown-item clearfix quick-add-desktop-only",
        "title" => app_lang('add_client'),
        "id" => "js-quick-add-client",
        "data-modal-class" => "mobile-friendly-modal"
    ));

    $mobile_primary_links .= modal_anchor(get_uri("clients/modal_form"), app_lang('add_client'), array(
        "class" => "btn btn-info quick-add-mobile-btn w-100",
        "title" => app_lang('add_client'),
        "data-modal-class" => "mobile-friendly-modal"
    ));
}

if ($can_manage_leads) {
    $links .= modal_anchor(get_uri("leads/modal_form"), app_lang('add_lead'), array(
        "class" => "dropdown-item clearfix quick-add-desktop-only",
        "title" => app_lang('add_lead'),
        "id" => "js-quick-add-lead",
        "data-modal-class" => "mobile-friendly-modal"
    ));

    $mobile_primary_links = modal_anchor(get_uri("leads/modal_form"), app_lang('add_lead'), array(
        "class" => "btn btn-info quick-add-mobile-btn w-100",
        "title" => app_lang('add_lead'),
        "data-modal-class" => "mobile-friendly-modal"
    )) . $mobile_primary_links;
}

if (($login_user->user_type == "staff") || ($login_user->user_type == "client" && can_client_access($login_user->client_permissions, "project", false))) {
    //add tasks
    $links .= modal_anchor(get_uri("tasks/modal_form"), app_lang('add_task'), array("class" => "dropdown-item clearfix", "title" => app_lang('add_task'), "id" => "js-quick-add-task"));

    //add multiple tasks
    $links .= modal_anchor(get_uri("tasks/modal_form"), app_lang('add_multiple_tasks'), array("class" => "dropdown-item clearfix", "title" => app_lang('add_multiple_tasks'), "data-post-add_type" => "multiple", "id" => "js-quick-add-multiple-task"));
}

//add project time
if ($login_user->user_type == "staff" && get_setting("module_project_timesheet") == "1") {
    $links .= modal_anchor(get_uri("projects/timelog_modal_form"), app_lang('add_project_time'), array("class" => "dropdown-item clearfix", "title" => app_lang('add_project_time'), "id" => "js-quick-add-project-time"));
}

//add event
if (get_setting("module_event") == "1" && (($login_user->user_type == "client" && can_client_access($login_user->client_permissions, "event")) || $login_user->user_type == "staff")) {
    $links .= modal_anchor(get_uri("events/modal_form"), app_lang('add_event'), array("class" => "dropdown-item clearfix", "title" => app_lang('add_event'), "data-post-client_id" => $login_user->user_type == "client" ? $login_user->client_id : "", "id" => "js-quick-add-event"));
}

//add note
if (get_setting("module_note") == "1" && $login_user->user_type == "staff") {
    $links .= modal_anchor(get_uri("notes/modal_form"), app_lang('add_note'), array("class" => "dropdown-item clearfix", "title" => app_lang('add_note'), "id" => "js-quick-add-note"));
}

//add todo
if (get_setting("module_todo") == "1") {
    $links .= modal_anchor(get_uri("todo/modal_form"), app_lang("add_to_do"), array("class" => "dropdown-item clearfix", "title" => app_lang('add_to_do'), "id" => "js-quick-add-to-do"));
}

//add ticket
if (get_setting("module_ticket") == "1" && ($login_user->is_admin || get_array_value($permissions, "ticket"))) {
    $links .= modal_anchor(get_uri("tickets/modal_form"), app_lang('add_ticket'), array("class" => "dropdown-item clearfix", "title" => app_lang('add_ticket'), "id" => "js-quick-add-ticket"));
}

if ($links) {
    ?>
    <li id="quick-add-button" class="nav-item dropdown hidden-xs">
        <?php echo js_anchor("<i data-feather='plus-circle' class='icon'></i>", array("id" => "quick-add-icon", "class" => "nav-link dropdown-toggle", "data-bs-toggle" => "dropdown")); ?>

        <ul class="dropdown-menu dropdown-menu-end quick-add-dropdown">
            <?php if ($mobile_primary_links) { ?>
                <li class="quick-add-mobile-actions d-sm-none">
                    <?php echo $mobile_primary_links; ?>
                </li>
            <?php } ?>
            <li>
                <?php echo $links; ?></li>
        </ul>
    </li>

    <script type="text/javascript">
        $(document).ready(function () {
            if(isMobile()){
                $("#mobile-quick-add-button").html($("#quick-add-button").html());
            }
        });
    </script>

    <?php
} 

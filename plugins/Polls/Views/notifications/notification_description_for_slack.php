<?php

if ($notification->plugin_poll_id) {
    $Polls_model = new \Polls\Models\Polls_model();
    $poll_info = $Polls_model->get_one($notification->plugin_poll_id);
    echo "\n*" . app_lang("title") . ":* " . $poll_info->title . "</div>";
}

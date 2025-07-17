<?php

$client_statuses_dropdown = array(array("id" => "", "text" => "- " . app_lang("status") . " -"));
if (isset($statuses)) {
    foreach ($statuses as $status) {
        $client_statuses_dropdown[] = array("id" => $status->id, "text" => $status->title);
    }
}

echo json_encode($client_statuses_dropdown);
?>

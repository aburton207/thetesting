<?php
if ($value) {
    $file = @unserialize($value);
    if (is_array($file)) {
        $url = get_source_url_of_file($file, get_setting("timeline_file_path"), "thumbnail");
    } else {
        $url = $value;
    }
    if ($url) {
        echo "<img src='" . $url . "' style='max-height:80px;' />";
    }
}
?>

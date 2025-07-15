<?php
$value = isset($field_info->value) ? $field_info->value : "";
$preview = "";
if ($value) {
    $file = @unserialize($value);
    if (is_array($file)) {
        $preview = get_source_url_of_file($file, get_setting("timeline_file_path"), "thumbnail");
    }
}
?>
<div class="custom-field-image-upload">
    <div class="file-upload btn btn-default btn-sm">
        <span><i data-feather="upload" class="icon-14"></i> <?php echo app_lang('upload'); ?></span>
        <input id="custom_field_file_<?php echo $field_info->id; ?>" class="cropbox-upload upload" name="custom_field_file_<?php echo $field_info->id; ?>" type="file" data-height="200" data-width="200" data-preview-container="#custom_field_image_preview_<?php echo $field_info->id; ?>" data-input-field="#custom_field_<?php echo $field_info->id; ?>" />
    </div>
    <div class="mt10">
        <img id="custom_field_image_preview_<?php echo $field_info->id; ?>" src="<?php echo $preview; ?>" style="max-height:80px;" />
    </div>
    <?php
    echo form_input(array(
        "id" => "custom_field_" . $field_info->id,
        "name" => "custom_field_" . $field_info->id,
        "value" => $value,
        "type" => "hidden",
        "data-rule-required" => $field_info->required ? true : "false",
        "data-msg-required" => app_lang("field_required"),
    ));
    ?>
</div>

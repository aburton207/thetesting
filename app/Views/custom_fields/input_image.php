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
        <input id="custom_field_file_<?php echo $field_info->id; ?>" class="upload" name="custom_field_file_<?php echo $field_info->id; ?>" type="file" <?php echo $field_info->required ? 'required' : ''; ?> />
    </div>
    <div class="mt10">
        <img id="custom_field_image_preview_<?php echo $field_info->id; ?>" src="<?php echo $preview; ?>" style="max-height:80px;" />
    </div>
    <?php if ($preview) { ?>
        <input type="hidden" id="custom_field_<?php echo $field_info->id; ?>" name="custom_field_<?php echo $field_info->id; ?>" value="<?php echo $value; ?>" />
    <?php } ?>
</div>

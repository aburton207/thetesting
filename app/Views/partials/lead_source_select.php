<?php
// Reusable lead source dropdown
$selected = isset($selected) ? $selected : (isset($model_info) ? $model_info->lead_source_id : '');
$id = isset($id) ? $id : 'lead_source_id';

$dropdown_data = [];
if (isset($sources_dropdown)) {
    $dropdown_data = is_array($sources_dropdown) ? $sources_dropdown : json_decode($sources_dropdown, true);
} elseif (isset($lead_sources)) {
    foreach ($lead_sources as $source) {
        $dropdown_data[] = ["id" => $source->id, "text" => $source->title];
    }
}
?>
<select id="<?php echo $id; ?>" name="lead_source_id" class="form-control select2">
    <?php foreach ($dropdown_data as $item) { ?>
        <option value="<?php echo $item['id']; ?>" <?php echo ($selected == $item['id']) ? 'selected' : ''; ?>><?php echo $item['text']; ?></option>
    <?php } ?>
</select>
<script>
    $(function () {
        $('#<?php echo $id; ?>').select2();
    });
</script>

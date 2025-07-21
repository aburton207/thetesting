<?php
// Reusable lead source dropdown
$selected = isset($selected) ? $selected : (isset($model_info) ? $model_info->lead_source_id : '');

$dropdown_data = [];
if (isset($sources_dropdown)) {
    $dropdown_data = is_array($sources_dropdown) ? $sources_dropdown : json_decode($sources_dropdown, true);
} elseif (isset($lead_sources)) {
    foreach ($lead_sources as $source) {
        $dropdown_data[] = ["id" => $source->id, "text" => $source->title];
    }
}
?>
<select id="lead_source_id" name="lead_source_id" class="form-control select2"></select>
<script>
    $(function(){
        var data = <?php echo json_encode($dropdown_data); ?>;
        $('#lead_source_id').select2({data: data});
        <?php if ($selected) { ?>
        $('#lead_source_id').val('<?php echo $selected; ?>').trigger('change');
        <?php } ?>
    });
</script>

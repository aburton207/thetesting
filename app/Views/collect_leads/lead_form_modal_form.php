<?php echo form_open(get_uri("lead_forms/save"), array("id" => "lead-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
    <div class="form-group">
        <div class="row">
            <label for="title" class="col-md-3"><?php echo app_lang('title'); ?></label>
            <div class="col-md-9">
                <?php echo form_input(array("id" => "title", "name" => "title", "value" => $model_info->title, "class" => "form-control", "data-rule-required" => true, "data-msg-required" => app_lang('field_required'))); ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label for="owner_id" class="col-md-3"><?php echo app_lang('owner'); ?></label>
            <div class="col-md-9">
                <?php echo form_dropdown("owner_id", $owners_dropdown, $model_info->owner_id, "class='select2' id='owner_id'"); ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label for="lead_source_id" class="col-md-3"><?php echo app_lang('region'); ?></label>
            <div class="col-md-9">
                <?php echo form_input(array("id" => "lead_source_id", "name" => "lead_source_id", "value" => $model_info->lead_source_id, "class" => "form-control")); ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label for="labels" class="col-md-3"><?php echo app_lang('labels'); ?></label>
            <div class="col-md-9">
                <?php echo form_multiselect("labels[]", $labels_dropdown, ($model_info->labels ? explode(',', $model_info->labels) : array()), "class='select2' id='lead_form_labels'"); ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label for="custom_fields" class="col-md-3"><?php echo app_lang('custom_fields'); ?></label>
            <div class="col-md-9">
                <?php echo form_multiselect("custom_fields[]", $custom_fields_dropdown, ($model_info->custom_fields ? explode(',', $model_info->custom_fields) : array()), "class='select2' id='lead_form_custom_fields'"); ?>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#lead-form").appForm({
            onSuccess: function (result) {
                $("#lead-forms-table").appTable({newData: result.data, dataId: result.id});
            }
        });
        $(".select2").select2();
    });
</script>

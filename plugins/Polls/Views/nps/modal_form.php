<?php echo form_open(get_uri("nps/save"), array("id" => "nps-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
    <div class="form-group">
        <label><?php echo app_lang("title"); ?></label>
        <?php echo form_input(array("id" => "title", "name" => "title", "value" => $model_info->title, "class" => "form-control", "autofocus" => true, "required" => "required")); ?>
    </div>
    <div class="form-group">
        <label><?php echo app_lang("description"); ?></label>
        <?php echo form_textarea(array("id" => "description", "name" => "description", "value" => $model_info->description, "class" => "form-control")); ?>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo app_lang("close"); ?></button>
    <button type="submit" class="btn btn-primary"><?php echo app_lang("save"); ?></button>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#nps-form").appForm({
            onSuccess: function (result) {
                location.reload();
            }
        });
    });
</script>

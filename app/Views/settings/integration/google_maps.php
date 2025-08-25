<div class="card no-border clearfix mb0">
    <?php echo form_open(get_uri("settings/save_google_maps_settings"), array("id" => "google-maps-form", "class" => "general-form dashed-row", "role" => "form")); ?>

    <div class="card-body">
        <div class="form-group">
            <div class="row">
                <label for="google_maps_api_key" class="col-md-2"><?php echo app_lang('google_maps_api_key'); ?></label>
                <div class="col-md-10">
                    <?php
                    echo form_input(array(
                        "id" => "google_maps_api_key",
                        "name" => "google_maps_api_key",
                        "value" => get_setting('google_maps_api_key'),
                        "class" => "form-control",
                        "placeholder" => app_lang('google_maps_api_key'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang('field_required'),
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#google-maps-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });
    });
</script>


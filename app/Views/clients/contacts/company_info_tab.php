<div class="tab-content">
    <?php echo form_open(get_uri("clients/save/"), array("id" => "company-form", "class" => "general-form dashed-row white", "role" => "form")); ?>
    <div class="card rounded-top-0">
        <div class=" card-header">
            <?php if ($model_info->type == "person") { ?>
                <h4> <?php echo app_lang('contact_info'); ?></h4>
            <?php } else { ?>
                <h4> <?php echo app_lang('client_info'); ?></h4>
            <?php } ?>
        </div>
        <div class="card-body">
            <?php echo view("clients/client_form_fields"); ?>
        </div>
        <?php if ($can_edit_clients) { ?>
            <div class="card-footer rounded-bottom">
                <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
            </div>
        <?php } ?>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#company-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });

        if (window.initAddressAutocomplete) {
            window.initAddressAutocomplete('#company-form');
        }

        var $ownerField = $("#owner_id");
        var $leadSourceField = $("#client_lead_source_id");
        var leadSourceEndpoint = "<?php echo get_uri('clients/get_lead_source_by_owner'); ?>";

        function updateLeadSourceFromOwner(ownerId, forceReset) {
            if (!ownerId) {
                if (forceReset && $leadSourceField.find("option[value='']").length) {
                    $leadSourceField.val("");
                }
                return;
            }

            $.ajax({
                url: leadSourceEndpoint,
                type: "POST",
                dataType: "json",
                data: {owner_id: ownerId},
                success: function (response) {
                    if (!response || !response.success) {
                        return;
                    }

                    if (response.lead_source_id) {
                        $leadSourceField.val(response.lead_source_id);
                    } else if ($leadSourceField.find("option[value='']").length) {
                        $leadSourceField.val("");
                    }
                }
            });
        }

        if ($ownerField.length && $leadSourceField.length) {
            $ownerField.on("change", function () {
                updateLeadSourceFromOwner($(this).val(), true);
            });

            if (!$leadSourceField.val()) {
                updateLeadSourceFromOwner($ownerField.val(), false);
            }
        }
    });
</script>

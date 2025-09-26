<?php echo form_open(get_uri("leads/save"), array("id" => "lead-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <?php echo view("leads/lead_form_fields"); ?>
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
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                } else {
                    $("#lead-table").appTable({newData: result.data, dataId: result.id});
                    $("#reload-kanban-button:visible").trigger("click");
                }
            }
        });
        var $modal = $("#lead-form").closest(".modal");
        $modal.on("shown.bs.modal", function () {
            $("#company_name").focus();
            if (window.initAddressAutocomplete) {
                window.initAddressAutocomplete(this);
            }
        });
        if ($modal.hasClass("show")) {
            $("#company_name").focus();
            if (window.initAddressAutocomplete) {
                window.initAddressAutocomplete($modal[0]);
            }
        }

        var $ownerField = $("#owner_id");
        var $leadSourceField = $("#lead_lead_source_id");
        var leadSourceEndpoint = "<?php echo get_uri('leads/get_lead_source_by_owner'); ?>";

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

        $ownerField.on("change", function () {
            updateLeadSourceFromOwner($(this).val(), true);
        });

        if (!$leadSourceField.val()) {
            updateLeadSourceFromOwner($ownerField.val(), false);
        }
    });
    </script>


<?php echo form_open(get_uri("leads/save_from_excel_file"), array("id" => "import-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix import-modal-body">
    <div class="container-fluid">
        <div class="mb-3">
            <label class="form-label fw-bold"><?php echo app_lang('lead_import_format'); ?></label>
            <div class="form-check">
                <input type="radio" name="import_format_option" id="import-format-standard" class="form-check-input" value="default" checked="checked">
                <label class="form-check-label" for="import-format-standard"><?php echo app_lang('lead_import_format_standard'); ?></label>
            </div>
            <div class="form-check">
                <input type="radio" name="import_format_option" id="import-format-meta" class="form-check-input" value="meta">
                <label class="form-check-label" for="import-format-meta"><?php echo app_lang('lead_import_format_meta'); ?></label>
            </div>
        </div>

        <div id="meta-lead-type-wrapper" class="mb-3 hide">
            <label class="form-label fw-bold"><?php echo app_lang('lead_import_meta_lead_type'); ?></label>
            <div class="form-check">
                <input type="radio" name="meta_lead_type_option" id="meta-lead-type-person" class="form-check-input" value="person" <?php echo ($default_meta_lead_type ?? 'person') === 'person' ? 'checked="checked"' : ''; ?>>
                <label class="form-check-label" for="meta-lead-type-person"><?php echo app_lang('lead_import_meta_person'); ?></label>
            </div>
            <div class="form-check">
                <input type="radio" name="meta_lead_type_option" id="meta-lead-type-organization" class="form-check-input" value="organization" <?php echo ($default_meta_lead_type ?? 'person') === 'organization' ? 'checked="checked"' : ''; ?>>
                <label class="form-check-label" for="meta-lead-type-organization"><?php echo app_lang('lead_import_meta_organization'); ?></label>
            </div>
        </div>

        <div id="meta-import-help" class="alert alert-info hide">
            <i data-feather="info" class="icon-16"></i> <?php echo app_lang('lead_import_meta_help_text'); ?>
        </div>

        <div id="upload-area" class="mt-3">
            <?php
            echo view("includes/multi_file_uploader", array(
                "upload_url" => get_uri("leads/upload_excel_file"),
                "validation_url" => get_uri("leads/validate_import_file"),
                "max_files" => 1,
                "hide_description" => true,
                "disable_button_type" => true
            ));
            ?>
        </div>
        <input type="hidden" name="file_name" id="import_file_name" value="" />
        <input type="hidden" name="import_format" id="import_format" value="default" />
        <input type="hidden" name="meta_lead_type" id="meta_lead_type" value="<?php echo ($default_meta_lead_type ?? 'person'); ?>" />
        <div id="preview-area"></div>
    </div>
</div>

<div class="modal-footer">
    <?php echo anchor("leads/download_sample_excel_file", "<i data-feather='download' class='icon-16'></i> " . app_lang("download_sample_file"), array("title" => app_lang("download_sample_file"), "class" => "btn btn-default float-start")); ?>
    <button type="button" class="btn btn-default cancel-upload" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button id="form-previous" type="button" class="btn btn-default hide"><span data-feather="arrow-left-circle" class="icon-16"></span> <?php echo app_lang('back'); ?></button>
    <button id="form-next" type="button" disabled="true" class="btn btn-info text-white"><span data-feather="arrow-right-circle" class="icon-16"></span> <?php echo app_lang('next'); ?></button>
    <button id="form-submit" type="button" disabled="true" class="btn btn-primary start-upload hide"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('upload'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#import-form").appForm({
            onSuccess: function () {
                location.reload();
            }
        });

        var $uploadArea = $("#upload-area"),
                $previewArea = $("#preview-area"),
                $previousButton = $("#form-previous"),
                $nextButton = $("#form-next"),
                $modalBody = $(".import-modal-body"),
                $submitButton = $("#form-submit"),
                $ajaxModal = $("#ajaxModal"),
                $importFormat = $("#import_format"),
                $metaLeadType = $("#meta_lead_type"),
                $metaWrapper = $("#meta-lead-type-wrapper"),
                $metaHelp = $("#meta-import-help");

        removeLargeModal(); //remove app-modal credentials on loading modal

        function addLargeModal() {
            $ajaxModal.addClass("import-client-app-modal");
            $ajaxModal.addClass("app-modal");
            $ajaxModal.find("div.modal-dialog").addClass("app-modal-body mw100p");
            $ajaxModal.find("div.modal-content").addClass("h100p");
        }

        function removeLargeModal() {
            $ajaxModal.find("div.modal-dialog").removeClass("app-modal-body mw100p");
            $ajaxModal.find("div.modal-content").removeClass("h100p");
            $ajaxModal.removeClass("app-modal");
        }

        function toggleMetaControls() {
            if ($importFormat.val() === "meta") {
                $metaWrapper.removeClass("hide");
                $metaHelp.removeClass("hide");
            } else {
                $metaWrapper.addClass("hide");
                $metaHelp.addClass("hide");
            }
        }

        $("input[name='import_format_option']").on("change", function () {
            $importFormat.val($(this).val());
            toggleMetaControls();
        });

        $("input[name='meta_lead_type_option']").on("change", function () {
            $metaLeadType.val($(this).val());
        });

        toggleMetaControls();

        $submitButton.click(function () {
            $("#import-form").trigger("submit");
        });

        //validate the uploaded excel file by clicking next
        $nextButton.click(function () {
            var fileName = $("#uploaded-file-previews").find("input[type=hidden]:eq(1)").val();
            if (!fileName) {
                appAlert.error("<?php echo app_lang('something_went_wrong'); ?>");
                return false;
            }
            appLoader.show({container: ".import-modal-body", css: "left:0;"});
            var $button = $(this);
            $button.attr("disabled", true);

            $("#import_file_name").val(fileName);


            $.ajax({
                url: "<?php echo get_uri('leads/validate_import_file_data') ?>",
                type: 'POST',
                dataType: 'json',
                data: {
                    file_name: fileName,
                    import_format: $importFormat.val(),
                    meta_lead_type: $metaLeadType.val()
                },
                success: function (result) {
                    appLoader.hide();
                    $button.removeAttr('disabled');

                    if (result.success) {
                        $uploadArea.addClass("hide");
                        $nextButton.addClass("hide");
                        $previousButton.removeClass("hide");
                        $submitButton.removeClass("hide");
                        $previewArea.removeClass("hide");

                        $previewArea.html(result.table_data);
                        $modalBody.height($(window).height() - 165);
                        $modalBody.addClass("overflow-y-scroll");
                        addLargeModal();

                        if (result.got_error) {
                            $submitButton.prop("disabled", true);
                        } else {
                            $submitButton.prop("disabled", false);
                        }
                    }
                }
            });


        });

        $previousButton.click(function () {
            $(this).addClass("hide");
            $submitButton.addClass("hide");
            $uploadArea.removeClass("hide");
            $previewArea.addClass("hide");
            $nextButton.removeClass("hide");

            $modalBody.height($(window).height() - 230);
            removeLargeModal();
        });
    });
</script>

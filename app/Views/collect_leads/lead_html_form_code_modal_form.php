<div class="general-form">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <div class="form-group">
                <div class="row">
                    <label for="lead_form_id" class="col-md-3"><?php echo app_lang('form'); ?></label>
                    <div class="col-md-9">
                        <?php
                        $lead_form_dropdown = array('' => "- " . app_lang('form') . " -");
                        foreach ($lead_forms as $form) {
                            $lead_form_dropdown[$form->id] = $form->title;
                        }
                        echo form_dropdown('lead_form_id', $lead_form_dropdown, '', "class='select2' id='lead_form_id'");
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="lead_source_id" class="col-md-3"><?php echo app_lang('source'); ?></label>
                    <div class="col-md-9">
                        <?php
                        $lead_source = array('' => "- " . app_lang('source') . " -");
                        foreach ($sources as $source) {
                            $lead_source[$source->id] = $source->title;
                        }
                        echo form_dropdown('lead_source_id', $lead_source, '', "class='select2' id='lead_source_id'");
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="lead_owner_id" class="col-md-3"><?php echo app_lang('owner'); ?></label>
                    <div class="col-md-9">
                        <?php
                        $lead_owner = array('' => "- " . app_lang('owner') . " -");
                        foreach ($owners as $owner) {
                            $lead_owner[$owner->id] = $owner->first_name . " " . $owner->last_name;
                        }
                        echo form_dropdown('lead_owner_id', $lead_owner, '', "class='select2' id='lead_owner_id'");
                        ?>
                    </div>
                </div>
            </div>
            <?php if (!empty($source_custom_field_label)) { ?>
            <div class="form-group">
                <div class="row">
                    <label for="custom_field_265" class="col-md-3"><?php echo $source_custom_field_label; ?></label>
                    <div class="col-md-9">
                        <?php
                        if (!empty($source_custom_field_has_options)) {
                            echo form_dropdown('custom_field_265', $source_custom_field_options, '', "class='select2' id='custom_field_265'");
                        } else {
                            echo form_input(array(
                                "id" => "custom_field_265",
                                "name" => "custom_field_265",
                                "class" => "form-control",
                                "placeholder" => $source_custom_field_label
                            ));
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        echo form_textarea(array(
                            "id" => "lead-html-form-code",
                            "name" => "lead-html-form-code",
                            "value" => $lead_html_form_code,
                            "class" => "form-control"
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="button" id="copy-button" class="btn btn-primary"><span data-feather="copy" class="icon-16"></span> <?php echo app_lang('copy'); ?></button>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#lead-html-form-code").addClass("h370");

        $(".select2").select2();

        var sourceId = "";
        var ownerId = "";
        var formId = "";
        var customFieldValue = "";

        customFieldValue = $("#custom_field_265").val() || "";

        function updateHtmlCode() {
            $.ajax({
                url: "<?php echo get_uri('collect_leads/get_lead_html_form_code'); ?>",
                type: "POST",
                data: {lead_source_id: sourceId, lead_owner_id: ownerId, lead_form_id: formId, custom_field_265: customFieldValue},
                success: function (result) {
                    $("#lead-html-form-code").val(result);
                }
            });
        }

        updateHtmlCode();

        $("#lead_source_id").on("change", function () {
            sourceId = $(this).val();
            updateHtmlCode();
        });

        $("#lead_owner_id").on("change", function () {
            ownerId = $(this).val();
            updateHtmlCode();
        });

        $("#lead_form_id").on("change", function () {
            formId = $(this).val();
            updateHtmlCode();
        });

        $("#custom_field_265").on("change input", function () {
            customFieldValue = $(this).val() || "";
            updateHtmlCode();
        });

        $("#copy-button").click(function () {
            var copyTextarea = document.querySelector('#lead-html-form-code');
            copyTextarea.focus();
            copyTextarea.select();
            document.execCommand('copy');
        });
    });
</script>
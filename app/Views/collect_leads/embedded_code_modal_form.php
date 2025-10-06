<div class="general-form">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        echo form_textarea(array(
                            "id" => "embedded-code",
                            "name" => "embedded-code",
                            "value" => $embedded,
                            "class" => "form-control"
                        ));
                        ?>
                    </div>
                </div>
            </div>
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
                    <label for="source" class="col-md-3"><?php echo app_lang('source'); ?></label>
                    <div class="col-md-9">
                        <?php
                        $lead_source = array('' => "- " . app_lang("source") . " -");

                        foreach ($sources as $source) {
                            $lead_source[$source->id] = $source->title;
                        }

                        echo form_dropdown("lead_source_id", $lead_source, '', "class='select2' id='lead_source_id'");
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="owner" class="col-md-3"><?php echo app_lang('owner'); ?></label>
                    <div class="col-md-9">
                        <?php
                        $lead_owner = array('' => "- " . app_lang("owner") . " -");

                        foreach ($owners as $owner) {
                            $lead_owner[$owner->id] = $owner->first_name . " " . $owner->last_name;
                        }

                        echo form_dropdown("lead_owner_id", $lead_owner, '', "class='select2' id='lead_owner_id'");
                        ?>
                    </div>
                </div>
            </div>
            <?php if (!empty($source_custom_field_label)) { ?>
            <div class="form-group">
                <div class="row">
                    <label for="custom_field_138" class="col-md-3"><?php echo $source_custom_field_label; ?></label>
                    <div class="col-md-9">
                        <?php
                        if (!empty($source_custom_field_has_options)) {
                            echo form_dropdown("custom_field_138", $source_custom_field_options, '', "class='select2' id='custom_field_138'");
                        } else {
                            echo form_input(array(
                                "id" => "custom_field_138",
                                "name" => "custom_field_138",
                                "class" => "form-control",
                                "placeholder" => $source_custom_field_label
                            ));
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="button" id="copy-button" class="btn btn-primary"><span data-feather="copy" class="icon-16"></span> <?php echo app_lang('copy'); ?></button>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#copy-button").click(function() {
            var copyTextarea = document.querySelector('#embedded-code');
            copyTextarea.focus();
            copyTextarea.select();
            document.execCommand('copy');
        });

        $(".select2").select2();

        var leadFormsSourceMap = <?php echo json_encode($lead_forms_source_map ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        var sourceId = "";
        var ownerId = "";
        var formId = "";
        var customFieldValue = "";
        var customFieldOverride = false;
        var $customField = $("#custom_field_138");

        if ($customField.length) {
            customFieldValue = $customField.val() || "";
        }

        $("#lead_source_id").on("change", function() {
            sourceId = $(this).val();
            updateEmbeddedCode();
        });

        $("#lead_owner_id").on("change", function() {
            ownerId = $(this).val();
            updateEmbeddedCode();
        });

        $("#lead_form_id").on("change", function() {
            formId = $(this).val();
            var handled = applyCustomFieldValueFromForm(formId);
            if (!handled) {
                updateEmbeddedCode();
            }
        });

        $customField.on("change input", function(event) {
            customFieldValue = $(this).val() || "";
            customFieldOverride = event.programmaticTrigger ? false : true;
            updateEmbeddedCode();
        });

        function applyCustomFieldValueFromForm(formIdentifier) {
            var mappedValue = "";
            if (formIdentifier && Object.prototype.hasOwnProperty.call(leadFormsSourceMap, formIdentifier)) {
                mappedValue = leadFormsSourceMap[formIdentifier] || "";
            }

            if ($customField.length) {
                customFieldOverride = false;
                if ($customField.is('select')) {
                    var changeEvent = $.Event('change');
                    changeEvent.programmaticTrigger = true;
                    $customField.val(mappedValue).trigger(changeEvent);
                } else {
                    var inputEvent = $.Event('input');
                    inputEvent.programmaticTrigger = true;
                    $customField.val(mappedValue).trigger(inputEvent);
                }
                return true;
            }

            customFieldValue = mappedValue || "";
            customFieldOverride = false;
            return false;
        }

        function shouldIncludeCustomField() {
            return customFieldOverride || (!formId && customFieldValue !== "");
        }

        function updateEmbeddedCode() {
            var embeddedCode = "<?php echo $embedded; ?>";
            var includeCustomField = shouldIncludeCustomField();
            if (formId) {
                var iframeSrc = "<?php echo get_uri('collect_leads/form/'); ?>" + formId;
                if (includeCustomField) {
                    var separator = iframeSrc.indexOf('?') === -1 ? '?' : '&';
                    iframeSrc += separator + "custom_field_138=" + encodeURIComponent(customFieldValue);
                }
                var iframeHtml = "<iframe width='768' height='360' src='" + iframeSrc + "' frameborder='0'></iframe>";
                $("#embedded-code").val(iframeHtml);
            } else if (sourceId || ownerId || includeCustomField) {
                var src = "<?php echo get_uri('collect_leads') . '/index/'; ?>";
                var iframeSrc = src + (sourceId ? sourceId : "0") + "/" + (ownerId ? ownerId : "0");
                if (includeCustomField) {
                    var glue = iframeSrc.indexOf('?') === -1 ? '?' : '&';
                    iframeSrc += glue + "custom_field_138=" + encodeURIComponent(customFieldValue);
                }
                var iframeHtml = "<iframe width='768' height='360' src='" + iframeSrc + "' frameborder='0'></iframe>";
                $("#embedded-code").val(iframeHtml);
            } else {
                $("#embedded-code").val(embeddedCode);
            }
        }

    });
</script>


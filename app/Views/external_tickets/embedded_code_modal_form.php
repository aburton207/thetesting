<div class="general-form">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <div class="form-group">
                <div class="row">
                    <label for="embed_ticket_type_id" class="col-md-3"><?php echo app_lang('ticket_type'); ?></label>
                    <div class="col-md-9">
                        <?php
                        $ticket_type_dropdown = array('' => "- " . app_lang('ticket_type') . " -");
                        if (!empty($ticket_types)) {
                            foreach ($ticket_types as $type) {
                                $ticket_type_dropdown[$type->id] = $type->title;
                            }
                        }
                        echo form_dropdown('embed_ticket_type_id', $ticket_type_dropdown, '', "class='select2' id='embed_ticket_type_id'");
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="embed_assigned_to" class="col-md-3"><?php echo app_lang('assign_to'); ?></label>
                    <div class="col-md-9">
                        <?php
                        $assignee_dropdown = array('' => "- " . app_lang('assign_to') . " -");
                        if (!empty($assignees)) {
                            foreach ($assignees as $member) {
                                $full_name = trim($member->first_name . ' ' . $member->last_name);
                                $assignee_dropdown[$member->id] = $full_name ? $full_name : $member->first_name;
                            }
                        }
                        echo form_dropdown('embed_assigned_to', $assignee_dropdown, '', "class='select2' id='embed_assigned_to'");
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="embed_ticket_labels" class="col-md-3"><?php echo app_lang('labels'); ?></label>
                    <div class="col-md-9">
                        <select id="embed_ticket_labels" class="select2" multiple="multiple" style="width: 100%;">
                            <?php if (!empty($labels)) {
                                foreach ($labels as $label) {
                                    ?><option value="<?php echo htmlspecialchars($label->id); ?>"><?php echo htmlspecialchars($label->title); ?></option><?php
                                }
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="embed_ticket_custom_fields" class="col-md-3"><?php echo app_lang('custom_fields'); ?></label>
                    <div class="col-md-9">
                        <select id="embed_ticket_custom_fields" class="select2" multiple="multiple" style="width: 100%;">
                            <?php if (!empty($custom_fields)) {
                                foreach ($custom_fields as $field) {
                                    $field_title = $field->title_language_key ? app_lang($field->title_language_key) : $field->title;
                                    ?><option value="<?php echo htmlspecialchars($field->id); ?>"><?php echo htmlspecialchars($field_title); ?></option><?php
                                }
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        echo form_textarea(array(
                            'id' => 'embedded-code',
                            'name' => 'embedded-code',
                            'value' => $embedded,
                            'class' => 'form-control',
                            'data-rich-text-editor' => false
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
        var $ticketType = $("#embed_ticket_type_id");
        var $assignee = $("#embed_assigned_to");
        var $labels = $("#embed_ticket_labels");
        var $customFields = $("#embed_ticket_custom_fields");
        var $embedTextarea = $("#embedded-code");

        $(".select2").select2();

        function buildIframeSrc() {
            var ticketTypeId = $ticketType.val() || "";
            var assigneeId = $assignee.val() || "";
            var labelIds = $labels.val() || [];
            var customFieldIds = $customFields.val() || [];

            var segments = [];
            if (ticketTypeId || assigneeId || labelIds.length) {
                segments[0] = ticketTypeId ? ticketTypeId : 0;
                segments[1] = assigneeId ? assigneeId : 0;
                if (labelIds.length) {
                    segments[2] = encodeURIComponent(labelIds.join(','));
                }
            }

            var cleanedSegments = [];
            for (var i = 0; i < segments.length; i++) {
                if (typeof segments[i] !== "undefined") {
                    cleanedSegments.push(segments[i]);
                }
            }

            var iframeSrc = "<?php echo get_uri('external_tickets'); ?>";
            if (cleanedSegments.length) {
                iframeSrc = "<?php echo get_uri('external_tickets/index'); ?>" + "/" + cleanedSegments.join('/');
            }

            if (customFieldIds.length) {
                var query = "custom_fields=" + encodeURIComponent(customFieldIds.join(','));
                iframeSrc += (iframeSrc.indexOf('?') === -1 ? '?' : '&') + query;
            }

            return "<iframe width='768' height='840' src='" + iframeSrc + "' frameborder='0'></iframe>";
        }

        function updateEmbedCode() {
            $embedTextarea.val(buildIframeSrc());
        }

        $ticketType.on("change", updateEmbedCode);
        $assignee.on("change", updateEmbedCode);
        $labels.on("change", updateEmbedCode);
        $customFields.on("change", updateEmbedCode);

        updateEmbedCode();

        $("#copy-button").click(function () {
            var copyTextarea = $embedTextarea[0];
            copyTextarea.focus();
            copyTextarea.select();
            document.execCommand('copy');
        });
    });
</script>

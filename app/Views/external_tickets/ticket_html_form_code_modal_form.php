<div class="general-form">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <div class="form-group">
                <div class="row">
                    <label for="ticket_html_ticket_type_id" class="col-md-3"><?php echo app_lang('ticket_type'); ?></label>
                    <div class="col-md-9">
                        <?php
                        $ticket_type_dropdown = array('' => "- " . app_lang('ticket_type') . " -");
                        if (!empty($ticket_types)) {
                            foreach ($ticket_types as $type) {
                                $ticket_type_dropdown[$type->id] = $type->title;
                            }
                        }
                        echo form_dropdown('ticket_html_ticket_type_id', $ticket_type_dropdown, '', "class='select2' id='ticket_html_ticket_type_id'");
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="ticket_html_assigned_to" class="col-md-3"><?php echo app_lang('assign_to'); ?></label>
                    <div class="col-md-9">
                        <?php
                        $assignee_dropdown = array('' => "- " . app_lang('assign_to') . " -");
                        if (!empty($assignees)) {
                            foreach ($assignees as $member) {
                                $full_name = trim($member->first_name . ' ' . $member->last_name);
                                $assignee_dropdown[$member->id] = $full_name ? $full_name : $member->first_name;
                            }
                        }
                        echo form_dropdown('ticket_html_assigned_to', $assignee_dropdown, '', "class='select2' id='ticket_html_assigned_to'");
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="ticket_html_labels" class="col-md-3"><?php echo app_lang('labels'); ?></label>
                    <div class="col-md-9">
                        <select id="ticket_html_labels" class="select2" multiple="multiple" style="width: 100%;">
                            <?php if (!empty($labels)) {
                                foreach ($labels as $label) { ?>
                                    <option value="<?php echo htmlspecialchars($label->id); ?>"><?php echo htmlspecialchars($label->title); ?></option>
                                <?php }
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="ticket_html_custom_fields" class="col-md-3"><?php echo app_lang('custom_fields'); ?></label>
                    <div class="col-md-9">
                        <select id="ticket_html_custom_fields" class="select2" multiple="multiple" style="width: 100%;">
                            <?php if (!empty($custom_fields)) {
                                foreach ($custom_fields as $field) {
                                    $field_title = $field->title_language_key ? app_lang($field->title_language_key) : $field->title;
                                    ?>
                                    <option value="<?php echo htmlspecialchars($field->id); ?>"><?php echo htmlspecialchars($field_title); ?></option>
                                <?php }
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
                            'id' => 'ticket-html-form-code',
                            'name' => 'ticket-html-form-code',
                            'value' => $ticket_html_form_code,
                            'class' => 'form-control'
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="button" id="ticket-html-copy-button" class="btn btn-primary"><span data-feather="copy" class="icon-16"></span> <?php echo app_lang('copy'); ?></button>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#ticket-html-form-code").addClass("h370");
        $(".select2").select2();

        function collectPostData() {
            var data = {};

            var ticketType = $("#ticket_html_ticket_type_id").val();
            if (ticketType) {
                data.ticket_type_id = ticketType;
            }

            var assignee = $("#ticket_html_assigned_to").val();
            if (assignee) {
                data.assigned_to = assignee;
            }

            var labels = $("#ticket_html_labels").val();
            if (labels && labels.length) {
                data.labels = labels;
            }

            var customFields = $("#ticket_html_custom_fields").val();
            if (customFields && customFields.length) {
                data.custom_fields = customFields;
            }

            return data;
        }

        function updateHtmlCode() {
            $.ajax({
                url: "<?php echo get_uri('external_tickets/get_ticket_html_form_code'); ?>",
                type: "POST",
                data: collectPostData(),
                success: function (result) {
                    $("#ticket-html-form-code").val(result);
                }
            });
        }

        $("#ticket_html_ticket_type_id, #ticket_html_assigned_to").on("change", updateHtmlCode);
        $("#ticket_html_labels, #ticket_html_custom_fields").on("change", updateHtmlCode);

        updateHtmlCode();

        $("#ticket-html-copy-button").click(function () {
            var copyTextarea = document.querySelector('#ticket-html-form-code');
            copyTextarea.focus();
            copyTextarea.select();
            document.execCommand('copy');
        });
    });
</script>

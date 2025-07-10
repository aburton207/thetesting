<?php echo form_open(get_uri("estimate_requests/save_note"), array("id" => "note-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
        <input type="hidden" name="estimate_request_id" value="<?php echo $estimate_request_id; ?>" />
        <div class="form-group">
            <label for="title" class="col-md-3"><?php echo app_lang('title'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_input(array(
                    "id" => "title",
                    "name" => "title",
                    "value" => "",
                    "class" => "form-control",
                    "placeholder" => app_lang('title'),
                    "autofocus" => true
                ));
                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="description" class="col-md-3"><?php echo app_lang('description'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_textarea(array(
                    "id" => "description",
                    "name" => "description",
                    "value" => "",
                    "class" => "form-control",
                    "placeholder" => app_lang('description'),
                    "required" => true
                ));
                ?>
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
        $("#note-form").appForm({
            onSuccess: function (result) {
                $("#notes-list").html(result.notes_list);
                $("#note-form").closest(".modal").modal("hide");
            }
        });
    });
</script>
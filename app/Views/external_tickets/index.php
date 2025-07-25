<style type="text/css">
    .post-file-previews {
        border:none !important;
    }
    .client-info-section  .form-group {
        margin: 25px 15px;
    }
    #page-content.page-wrapper{
        padding: 10px !important
    }
    #content{
        margin-top: 15px !important
    }
</style>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,600;1,14..32,600&family=Poppins:wght@600&display=swap');
</style>
<style type="text/css">
    .lead-info-section .form-group {
        margin: 10px;
    }

    #page-content.page-wrapper {
        padding: 10px !important
    }

    #content {
        margin-top: 15px !important
    }
    
    body {
        background: #fff;
        background-color: #fff;
        color: #000;
        font-size: 15px;
         font-family: "Inter", sans-serif;
  font-optical-sizing: auto;
  font-weight: 600;
  color:#000;
  font-style: normal;
        }
        label,.company-address{
                        font-family: "Inter", sans-serif;
  font-optical-sizing: auto;
  font-weight: 600;
  color:#000;
  font-size:14px;
  font-style: normal;
        }
        .form-group-wrapper{margin-bottom:10px;}
        .general-form .form-control{
            border: 1px solid #000;
        }
        .general-form .form-control:focus {
  border: 2px solid #234b7c;
  outline: none;
}
    }
    label {
        color: #000;
    }
    .card {
        box-shadow: none;
    }
   #estimate-form-title{  font-family: "Poppins", sans-serif!important;
  font-weight: 600!important;
  font-style: normal;}
</style>
    <style>
@import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,600;1,14..32,600&family=Poppins:wght@600&display=swap');
</style>
<style type="text/css">

   h3{  font-family: "Poppins", sans-serif;
  font-weight: 600;
  PADDING-LEFT:0px;
  margin-left:0px;
  margin-bottom:25px;
  font-style: normal;}
       table.dataTable tbody th, table.dataTable tbody td {
    padding: 12px 0px 12px 0px !important;
}
table.dataTable tbody td:first-child {
    padding-left: 0px !important;
}
.client-info-section .form-group {
    margin: 15px 0px;
}
</style>
<div id="page-content" class="page-wrapper clearfix">
    <div id="external-ticket-form-container">

        <?php echo form_open(get_uri("external_tickets/save"), array("id" => "ticket-form", "class" => "general-form", "role" => "form")); ?>
        <div id="new-ticket-dropzone" class="card p15 no-border clearfix post-dropzone client-info-section" style="max-width: 100%; margin: auto;">


            <div class="form-group">
                <label for="title"><?php echo app_lang('title'); ?></label>
                <div>
                    <?php
                    echo form_input(array(
                        "id" => "title",
                        "name" => "title",
                        "value" => "",
                        "class" => "form-control",
                        "placeholder" => app_lang('title'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="ticket_type_id"><?php echo app_lang('ticket_type'); ?></label>
                <div>
                    <?php
                    echo form_dropdown("ticket_type_id", $ticket_types_dropdown, "", "class='select2'");
                    ?>
                </div>
            </div>
    <div class="form-group">
                <label for="email"><?php echo app_lang('your_email'); ?></label>
                <div>
                    <?php
                    echo form_input(array(
                        "id" => "email",
                        "name" => "email",
                        "class" => "form-control p10",
                        "autofocus" => true,
                        "placeholder" => app_lang('email'),
                        "data-rule-email" => true,
                        "data-msg-email" => app_lang("enter_valid_email"),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="name"><?php echo app_lang('your_name'); ?></label>
                <div>
                    <?php
                    echo form_input(array(
                        "id" => "name",
                        "name" => "name",
                        "value" => "",
                        "class" => "form-control",
                        "placeholder" => app_lang('name'),
                    ));
                    ?>
                </div>
            </div>
      

            <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "", "field_column" => "")); ?> 

              <div class="form-group">
                <label for="description"><?php echo app_lang('description'); ?></label>
                <div>
                    <?php
                    echo form_textarea(array(
                        "id" => "description",
                        "name" => "description",
                        "class" => "form-control",
                        "placeholder" => app_lang('description'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                        "data-rich-text-editor" => true
                    ));
                    ?>
                </div>
            </div>


            <div>
                <?php echo view("signin/re_captcha"); ?>
            </div>

            <div class="clearfix pl10 pr10 b-b">
                <?php echo view("includes/dropzone_preview"); ?>    
            </div>

            <div class="p15">
                <div class="float-start">
                    <?php echo view("includes/upload_button"); ?>
                </div>

                <button type="submit" class="btn btn-primary float-end"><span data-feather="send" class="icon-16"></span> <?php echo app_lang('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $("#ticket-form").appForm({
            isModal: false,
            onSubmit: function () {
                appLoader.show();
                $("#ticket-form").find('[type="submit"]').attr('disabled', 'disabled');
            },
            onSuccess: function (result) {
                appLoader.hide();
                $("#external-ticket-form-container").html("");
                appAlert.success(result.message, {container: "#external-ticket-form-container", animate: false});
                $('.scrollable-page').scrollTop(0); //scroll to top
            }
        });

        setTimeout(function () {
            $("#title").focus();
        }, 200);

        $("#ticket-form .select2").select2();
    });
</script>
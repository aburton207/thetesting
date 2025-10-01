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

        <?php
        $phone_minlength_message = app_lang('phone_minlength_error');
        if ($phone_minlength_message === 'phone_minlength_error') {
            $phone_minlength_message = "Phone number must be at least 10 digits.";
        }

        echo form_open(get_uri("external_tickets/save"), array("id" => "ticket-form", "class" => "general-form", "role" => "form"));
        ?>
        <div id="new-ticket-dropzone" class="card p15 no-border clearfix post-dropzone client-info-section" style="max-width: 100%; margin: auto;">


            <input type="hidden" name="is_embedded_form" value="1" />
            <input type="hidden" name="redirect_to" value="https://www.avenirenergy.ca/avenir-energy-thank-you" />
            <?php
            $default_assignee_id = !empty($selected_assignee_id) ? htmlspecialchars($selected_assignee_id) : "";
            ?>
            <input type="hidden" name="assigned_to" id="assigned_to" value="<?php echo $default_assignee_id; ?>" data-default-value="<?php echo $default_assignee_id; ?>" />
            <?php if (!empty($selected_label_ids)) { ?>
                <input type="hidden" name="labels" value="<?php echo htmlspecialchars($selected_label_ids); ?>" />
            <?php } ?>

            <div class="form-group">
                <label for="first_name"><?php echo app_lang('first_name'); ?>*</label>
                <div>
                    <?php
                    echo form_input(array(
                        "id" => "first_name",
                        "name" => "first_name",
                        "value" => "",
                        "class" => "form-control",
                        "placeholder" => app_lang('first_name'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="last_name"><?php echo app_lang('last_name'); ?>*</label>
                <div>
                    <?php
                    echo form_input(array(
                        "id" => "last_name",
                        "name" => "last_name",
                        "value" => "",
                        "class" => "form-control",
                        "placeholder" => app_lang('last_name'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="company_name"><?php echo app_lang('company_name'); ?></label>
                <div>
                    <?php
                    echo form_input(array(
                        "id" => "company_name",
                        "name" => "company_name",
                        "value" => "",
                        "class" => "form-control",
                        "placeholder" => app_lang('company_name')
                    ));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="email"><?php echo app_lang('email'); ?>*</label>
                <div>
                    <?php
                    echo form_input(array(
                        "id" => "email",
                        "name" => "email",
                        "class" => "form-control p10",
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
                <label for="address"><?php echo app_lang('address'); ?>*</label>
                <div>
                    <?php
                    echo form_input(array(
                        "id" => "address",
                        "name" => "address",
                        "class" => "form-control",
                        "placeholder" => app_lang('address'),
                        "autocomplete" => "off",
                        "data-autofill" => "new-address",
                        "aria-autocomplete" => "list",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="city"><?php echo app_lang('city'); ?>*</label>
                <div>
                    <?php
                    echo form_input(array(
                        "id" => "city",
                        "name" => "city",
                        "class" => "form-control",
                        "placeholder" => app_lang('city'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="state"><?php echo app_lang('state'); ?>*</label>
                <div>
                    <select id="state" name="state" class="form-control select2" data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>">
                        <option value="">- Select Province -</option>
                        <option value="New Brunswick">New Brunswick</option>
                        <option value="Nova Scotia">Nova Scotia</option>
                        <option value="Prince Edward Island">Prince Edward Island</option>
                        <option value="Quebec">Quebec</option>
                        <option value="Ontario">Ontario</option>
                        <option value="Manitoba">Manitoba</option>
                        <option value="Northwest Territories">Northwest Territories</option>
                        <option value="British Columbia">British Columbia</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="zip"><?php echo app_lang('zip'); ?>*</label>
                <div>
                    <?php
                    echo form_input(array(
                        "id" => "zip",
                        "name" => "zip",
                        "class" => "form-control",
                        "placeholder" => app_lang('zip'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="lead_source_id"><?php echo app_lang('lead_source'); ?>*</label>
                <div>
                    <select
                        name="lead_source_id"
                        id="lead_source_id"
                        class="form-control select2"
                        data-rule-required="true"
                        data-msg-required="<?php echo app_lang("field_required"); ?>"
                    >
                        <option value="">- Select -</option>
                        <option value="1">CA_Eastern ROC</option>
                        <option value="2">CA_Pacific</option>
                        <option value="3">CA_Prairies</option>
                        <option value="4">CA_Atlantic</option>
                        <option value="5">CA_Quebec</option>
                        <option value="6">CA_Ontario</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="phone"><?php echo app_lang('phone'); ?>*</label>
                <div>
                    <?php
                    echo form_input(array(
                        "id" => "phone",
                        "name" => "phone",
                        "class" => "form-control",
                        "placeholder" => app_lang('phone'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                        "data-rule-minlength" => 10,
                        "data-msg-minlength" => $phone_minlength_message,
                    ));
                    ?>
                </div>
            </div>

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
                    ));
                    ?>
                </div>
            </div>

            <?php if (!empty($selected_ticket_type_id)) { ?>
                <input type="hidden" name="ticket_type_id" value="<?php echo htmlspecialchars($selected_ticket_type_id); ?>" />
            <?php } else { ?>
                <div class="form-group">
                    <label for="ticket_type_id"><?php echo app_lang('ticket_type'); ?></label>
                    <div>
                        <?php
                        echo form_dropdown("ticket_type_id", $ticket_types_dropdown, "", "class='select2'");
                        ?>
                    </div>
                </div>
            <?php } ?>


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

                if (result.redirect_url) {
                    window.top.location.href = result.redirect_url;
                    return;
                }

                $("#external-ticket-form-container").html("");
                appAlert.success(result.message, {container: "#external-ticket-form-container", animate: false});
                $('.scrollable-page').scrollTop(0); //scroll to top
            }
        });

        setTimeout(function () {
            $("#first_name").focus();
        }, 200);

        $("#ticket-form .select2").select2();

        var addressInput = $("#address");
        addressInput.attr('autocomplete', 'new-address');
        addressInput.on('focus', function () {
            $(this).attr('autocomplete', 'new-address');
        });

        var ownerMap = {
            "2": "254",
            "3": "253",
            "4": "251",
            "5": "3827",
            "6": "252"
        };

        var $assignedTo = $("#assigned_to");
        var defaultAssignee = "";
        if ($assignedTo.length) {
            var storedDefault = $assignedTo.attr("data-default-value");
            defaultAssignee = typeof storedDefault !== "undefined" ? storedDefault : ($assignedTo.val() || "");
        }

        function updateAssignedOwner(leadSourceId) {
            if (!$assignedTo.length) {
                return;
            }

            var mappedOwner = ownerMap.hasOwnProperty(leadSourceId) ? ownerMap[leadSourceId] : "";
            if (mappedOwner) {
                $assignedTo.val(mappedOwner);
            } else {
                $assignedTo.val(defaultAssignee);
            }
        }

        $("#state, #city").on("change keyup", updateLeadSource);
        updateLeadSource();

        function updateLeadSource() {
            var provinceVal = $("#state").val();

            if (!provinceVal) {
                $("#lead_source_id").val("").trigger("change");
                updateAssignedOwner("");
                return;
            }

            var sourceMap = {
                "New Brunswick": 4,
                "Nova Scotia": 4,
                "Prince Edward Island": 4,
                "Quebec": 5,
                "Ontario": 6,
                "Manitoba": 3,
                "Northwest Territories": 3,
                "British Columbia": 3
            };

            if (provinceVal === "British Columbia") {
                var bcCitiesKeywords = [
                    "vancouver", "burnaby", "richmond", "surrey", "delta", "new westminster",
                    "langley", "white rock", "maple ridge", "pitt meadows", "coquitlam",
                    "port coquitlam", "port moody", "north vancouver", "west vancouver",
                    "belcarra", "anmore", "bowen island", "lions bay",
                    "abbotsford", "chilliwack", "mission", "kent", "agassiz",
                    "harrison hot springs", "hope",
                    "sechelt", "gibsons",
                    "victoria", "saanich", "oak bay", "esquimalt", "view royal", "colwood",
                    "langford", "metchosin", "highlands", "sooke", "central saanich", "north saanich",
                    "sidney", "duncan", "north cowichan", "lake cowichan", "ladysmith", "nanaimo",
                    "lantzville", "parksville", "qualicum beach", "port alberni", "tofino",
                    "ucluelet", "courtenay", "comox", "cumberland", "campbell river", "gold river",
                    "tahsis", "zeballos", "port hardy", "port mcneill", "port alice", "alert bay"
                ];

                var cityVal = $("#city").val().trim().toLowerCase();
                var matchedKeyword = bcCitiesKeywords.some(function (keyword) {
                    return cityVal.indexOf(keyword) !== -1;
                });

                if (matchedKeyword) {
                    $("#lead_source_id").val("2").trigger("change");
                    updateAssignedOwner("2");
                    return;
                }
            }

            var mappedSource = sourceMap[provinceVal] || "";
            $("#lead_source_id").val(mappedSource).trigger("change");
            updateAssignedOwner(mappedSource);
        }

        $("#lead_source_id").on("change", function () {
            updateAssignedOwner($(this).val());
        });

        updateAssignedOwner($("#lead_source_id").val());
    });
</script>

<?php $googleMapsApiKey = get_setting('google_maps_api_key'); ?>
<?php if ($googleMapsApiKey) { ?>
    <script src="<?php echo base_url('assets/js/google_address_autocomplete.js'); ?>"></script>
    <script>
        function googleMapsReady() {
            if (window.initAddressAutocomplete) {
                window.initAddressAutocomplete(document);
            }
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $googleMapsApiKey; ?>&libraries=places&v=beta&callback=googleMapsReady&loading=async"></script>
<?php } ?>

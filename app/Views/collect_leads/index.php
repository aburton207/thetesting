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
</style>


    <div id="external-lead-form-container">

        <?php echo form_open(get_uri("collect_leads/save"), array("id" => "lead-form", "class" => "general-form", "role" => "form")); ?>
        <div class=" p15 no-border clearfix lead-info-section" style="max-width: 1000px; margin: auto;">

            <!-- 1) Hidden field indicating embedded form -->
            <input type="hidden" name="is_embedded_form" value="1" />
            
            <!-- 2) Keep lead_owner_id hidden if needed -->
            <input type="hidden" name="lead_owner_id" value="<?php echo $lead_owner_id; ?>" />
            <?php if (!empty($lead_source_id)) { ?>
                <input type="hidden" name="lead_source_id" value="<?php echo $lead_source_id; ?>" />
            <?php } ?>
            <?php if (!empty($lead_labels)) { ?>
                <input type="hidden" name="lead_labels" value="<?php echo $lead_labels; ?>" />
            <?php } ?>
            <?php if (isset($custom_field_238_value) && $custom_field_238_value !== "") { ?>
                <input type="hidden" name="custom_field_238" value="<?php echo htmlspecialchars($custom_field_238_value); ?>" />
            <?php } ?>

            <!-- 3) Gather hidden fields list, if applicable -->
            <?php $hidden_fields = explode(",", get_setting("hidden_fields_on_lead_embedded_form")); ?>

            <!-- 4) Standard fields (first_name, last_name, etc.) -->
            <?php if (!in_array("first_name", $hidden_fields)) { ?>
                <div class="form-group">
                    <label for="first_name"><?php echo app_lang('first_name'); ?>*</label>
                    <div>
                        <?php
                        echo form_input(array(
                            "id" => "first_name",
                            "name" => "first_name",
                            "class" => "form-control",
                            "placeholder" => app_lang('first_name'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            <?php } ?>

            <?php if (!in_array("last_name", $hidden_fields)) { ?>
                <div class="form-group">
                    <label for="last_name"><?php echo app_lang('last_name'); ?>*</label>
                    <div>
                        <?php
                        echo form_input(array(
                            "id" => "last_name",
                            "name" => "last_name",
                            "class" => "form-control",
                            "placeholder" => app_lang('last_name'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            <?php } ?>

            <?php if (!in_array("company_name", $hidden_fields)) { ?>
                <div class="form-group">
                    <label for="company_name"><?php echo app_lang('company_name'); ?></label>
                    <div>
                        <?php
                        echo form_input(array(
                            "id" => "company_name",
                            "name" => "company_name",
                            "class" => "form-control",
                            "placeholder" => app_lang('company_name')
                        ));
                        ?>
                    </div>
                </div>
            <?php } ?>

         <?php if (!in_array("email", $hidden_fields)) { ?>
    <div class="form-group">
        <label for="email"><?php echo app_lang('email'); ?>*</label>
        <div>
            <?php
            echo form_input(array(
                "id" => "email",
                "name" => "email",
                "class" => "form-control",
                "placeholder" => app_lang('email'),
                "autocomplete" => "off",
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
                "data-rule-email" => true,
                "data-msg-email" => app_lang("enter_valid_email")
            ));
            ?>
        </div>
    </div>
<?php } ?>

 <?php if (!in_array("address", $hidden_fields)) { ?>
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
                "data-msg-required" => app_lang("field_required")
            ));
            ?>
        </div>
    </div>
<?php } ?>

     <?php if (!in_array("city", $hidden_fields)) { ?>
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
                "data-msg-required" => app_lang("field_required")
            ));
            ?>
        </div>
    </div>
<?php } ?>
   
            <!-- 5) Province Dropdown, with name="state" for DB column -->
       <?php if (!in_array("state", $hidden_fields)) { ?>
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
<?php } ?>
     <?php if (!in_array("zip", $hidden_fields)) { ?>
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
                "data-msg-required" => app_lang("field_required")
            ));
            ?>
        </div>
    </div>
<?php } ?>
            <?php if (!$lead_source_id) { ?>
            <div class="form-group">
                <label for="lead_source_id">Region</label>
                <div>
                    <select
                        name="lead_source_id"
                        id="lead_source_id"
                        class="form-control select2"
                        data-rule-required="true"
                        data-msg-required="<?php echo app_lang("field_required"); ?>"
                    >
                        <option value="">- Select -</option>
                        <option value="1" <?php echo ($lead_source_id == 1 ? 'selected' : ''); ?>>CA_Eastern ROC</option>
                        <option value="2" <?php echo ($lead_source_id == 2 ? 'selected' : ''); ?>>CA_Pacific</option>
                        <option value="3" <?php echo ($lead_source_id == 3 ? 'selected' : ''); ?>>CA_Prairies</option>
                        <option value="4" <?php echo ($lead_source_id == 4 ? 'selected' : ''); ?>>CA_</option>
                        <option value="5" <?php echo ($lead_source_id == 5 ? 'selected' : ''); ?>>CA_Quebec</option>
                        <option value="6" <?php echo ($lead_source_id == 6 ? 'selected' : ''); ?>>CA_Ontario</option>
                    </select>
                </div>
            </div>
            <?php } ?>

    

            <?php if (!in_array("country", $hidden_fields)) { ?>
                <div class="form-group">
                    <label for="country"><?php echo app_lang('country'); ?></label>
                    <div>
                        <?php
                        echo form_input(array(
                            "id" => "country",
                            "name" => "country",
                            "class" => "form-control",
                            "placeholder" => app_lang('country')
                        ));
                        ?>
                    </div>
                </div>
            <?php } ?>

 <?php if (!in_array("phone", $hidden_fields)) { ?>
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
                "data-msg-minlength" => app_lang('phone_minlength_error') ?: "Phone number must be at least 10 digits."
            ));
            ?>
        </div>
    </div>
<?php } ?>

            <!-- 6) Custom fields, recaptcha, & submit button -->
            <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "", "field_column" => "")); ?>

            <div>
                <?php echo view("signin/re_captcha"); ?>
            </div>

            <div class="p15">
                <button type="submit" class="btn btn-primary">
                    <span data-feather="send" class="icon-16"></span>
                    <?php echo app_lang('submit'); ?>
                </button>
            </div>

        </div>
        <?php echo form_close(); ?>

    </div>

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

<script type="text/javascript">
    $(document).ready(function() {
        // 1) Initialize appForm
        $("#lead-form").appForm({
            isModal: false,
            onSubmit: function() {
                appLoader.show();
                $("#lead-form").find('[type=\"submit\"]').attr('disabled', 'disabled');
            },
            onSuccess: function(result) {
                appLoader.hide();
                $("#external-lead-form-container").html("");
                appAlert.success(result.message, {
                    container: "#external-lead-form-container",
                    animate: false
                });
                $('.scrollable-page').scrollTop(0);
                window.parent.postMessage({ type: 'FORM_SUCCESS' }, '*');
            }
        });

        // 2) Optional focus
        setTimeout(function() {
            $("#title").focus();
        }, 200);

        // 3) Initialize select2
        $("#lead-form .select2").select2();

        // 4) Prevent browser autofill on address field
        var addressInput = $('#address');
        addressInput.attr('autocomplete', 'new-address');
        addressInput.on('focus', function() {
            $(this).attr('autocomplete', 'new-address');
        });

        // 5) Dynamic logic: override lead source
        $("#state, #city").on("change keyup", updateLeadSource);

        function updateLeadSource() {
            var cityVal = $("#city").val().trim().toLowerCase();
            var provinceVal = $("#state").val();

            if(!provinceVal) {
                $("#lead_source_id").val("").trigger("change");
                return;
            }

            var sourceMap = {
                "New Brunswick": 1,
                "Nova Scotia": 1,
                "Prince Edward Island": 1,
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

                for (var i = 0; i < bcCitiesKeywords.length; i++) {
                    if (cityVal.indexOf(bcCitiesKeywords[i]) !== -1) {
                        $("#lead_source_id").val("2").trigger("change");
                        return;
                    }
                }
                $("#lead_source_id").val("3").trigger("change");
                return;
            }

            var mappedId = sourceMap[provinceVal] ? sourceMap[provinceVal] : "";
            $("#lead_source_id").val(mappedId).trigger("change");
        }

        // 6) If company name is empty, populate it with first and last name on submit
        $('#lead-form').on('submit', function() {
            var company = $('#company_name');
            if (!company.val().trim()) {
                var first = $('#first_name').val().trim();
                var last = $('#last_name').val().trim();
                company.val((first + ' ' + last).trim());
            }
        });
    });

    // Google Places Autocomplete handled by assets/js/google_address_autocomplete.js
</script>


<!-- Listen for messages from the parent -->
<script>
window.addEventListener('message', function(event) {
    // Optional: check event.origin if you want security checks
    // if (event.origin !== 'https://YourParentDomain.com') return;

    var data = event.data;

    // 1) Hide specified fields
    if (data && data.hideIds && Array.isArray(data.hideIds)) {
        data.hideIds.forEach(function(id) {
            if (id && id.trim() !== "") {
                var selector = ".custom_field_" + id;
                var elements = document.querySelectorAll(selector);
                elements.forEach(function(el) {
                    el.style.display = 'none';
                });
            }
        });
    }

    // 2) If the parent wants to inject HTML into #embed_link
    if (data && data.embedHTML) {
        var embedDiv = document.getElementById("embed_link");
        if (embedDiv) {
            embedDiv.innerHTML = data.embedHTML;
        }
    }
});
</script>

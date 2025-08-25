<?php if ($embedded) { ?>
    <style type="text/css">
        .post-file-previews {
            border: none !important;
        }
        .client-info-section .form-group {
            margin: 25px 15px
        }
        #page-content.page-wrapper {
            padding: 0px !important;
        }
        .page-wrapper {
    padding: 25px;
}
        #content {
            margin-top: 15px !important
        }
        .card{width:100%;max-width:100%;}
    
    </style>
<?php } else { ?>
    <style type="text/css">
        body { background-color: #fff; }
            .scrollable-page{height:auto!important;}
        .post-file-previews {
            border: none !important;
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
            border-color:#000;
        }
        .general-form .form-control:focus {
  border: 2px solid #234b7c;
  outline: none;
}
        .client-info-section .form-group {
            margin: 25px 15px
        }
        td {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .general-form .field-row {
            flex: 0 0 auto;
            min-width: 200px;
            margin: 0;
        }
        .general-form .form-group {
            flex: 1;
            margin: 0;
        }
        .general-form .form-control {
            width: 100%;
        }

        .company-header {
            width: 100%;
        }
        .logo-address-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }
        .company-logo {
            height: 40px;
            width: auto;
        }
        .company-address {
            font-size: 14px;
            color: #fff;
            white-space: nowrap;
        }
      table.dataTable tbody th, table.dataTable tbody td {
    padding: 12px 0px 12px 0px !important;
}
table.dataTable tbody td:first-child {
    padding-left: 0px !important;
}
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
  .public-page-container{margin-top:0px;}
</style>
<?php } ?>
<style>.navbar { display: none; }
#estimate-form-preview{box-shadow:none;}
</style>



<div id="page-content" class="page-wrapper clearfix">
    <div id="estimate-form-container">
        <?php
        echo form_open(get_uri("request_estimate/save_estimate_request"), array("id" => "estimate-request-form", "class" => "general-form", "role" => "form"));
        echo "<input type='hidden' name='form_id' value='$model_info->id' />";
        echo "<input type='hidden' name='assigned_to' value='$model_info->assigned_to' />";
        ?>

        <div id="estimate-form-preview" class=" no-border clearfix post-dropzone" style="max-width:100%; margin: auto;">
  

            <!-- CLIENT FIELDS -->
            <div class="client-info-section">
                <?php $hidden_fields = explode(",", get_setting("hidden_client_fields_on_public_estimate_requests")); ?>
                <style>
                    .client-info-section .form-group {
                        display: flex;
                        align-items: center;
                        margin-bottom: 15px;
                    }
                    .client-info-section .form-group label {
                        flex: 0 0 120px;
                        margin-right: 10px;
                        margin-bottom: 0;
                    }
                    .client-info-section .form-control-wrapper {
                        flex: 1;
                    }
                    .client-info-section .form-control {
                        width: 100%;
                    }
                </style>

                <?php if (!in_array("first_name", $hidden_fields)) { ?>
                    <div class="form-group-wrapper">
                        <div class="form-group">
                            <label for="first_name"><?php echo app_lang('first_name'); ?>*</label>
                            <div class="form-control-wrapper">
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
                    </div>
                <?php } ?>

                <?php if (!in_array("last_name", $hidden_fields)) { ?>
                    <div class="form-group-wrapper">
                        <div class="form-group">
                            <label for="last_name"><?php echo app_lang('last_name'); ?>*</label>
                            <div class="form-control-wrapper">
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
                    </div>
                <?php } ?>

         

 <?php if (!in_array("email", $hidden_fields)) { ?>
    <div class="form-group-wrapper">
        <div class="form-group">
            <label for="email"><?php echo app_lang('email'); ?></label> <!-- Removed asterisk to indicate optional -->
            <div class="form-control-wrapper">
                <?php
                echo form_input(array(
                    "id" => "email",
                    "name" => "email",
                    "class" => "form-control",
                    "placeholder" => app_lang('email'),
                    "autofocus" => true,
                    "autocomplete" => "off"
                    // Removed: "data-rule-email" => true,
                    // Removed: "data-msg-email" => app_lang("enter_valid_email"),
                    // Removed: "data-rule-required" => true,
                    // Removed: "data-msg-required" => app_lang("field_required")
                ));
                ?>
            </div>
        </div>
    </div>
<?php } ?>

                <?php if (!in_array("address", $hidden_fields)) { ?>
                    <div class="form-group-wrapper">
                        <div class="form-group">
                            <label for="address"><?php echo app_lang('address'); ?>*</label>
                            <div class="form-control-wrapper">
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
                    </div>
                <?php } ?>

                <?php if (!in_array("city", $hidden_fields)) { ?>
                    <div class="form-group-wrapper">
                        <div class="form-group">
                            <label for="city"><?php echo app_lang('city'); ?>*</label>
                            <div class="form-control-wrapper">
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
                    </div>
                <?php } ?>

                <?php if (!in_array("state", $hidden_fields)) { ?>
                    <div class="form-group-wrapper">
                        <div class="form-group">
                            <label for="state"><?php echo app_lang('state'); ?>*</label>
                            <div class="form-control-wrapper">
                                <?php
                                echo form_input(array(
                                    "id" => "state",
                                    "name" => "state",
                                    "class" => "form-control",
                                    "placeholder" => app_lang('state'),
                                                   "data-rule-required" => true,
                                    "data-msg-required" => app_lang("field_required"),
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?php if (!in_array("zip", $hidden_fields)) { ?>
                    <div class="form-group-wrapper">
                        <div class="form-group">
                            <label for="zip"><?php echo app_lang('zip'); ?>*</label>
                            <div class="form-control-wrapper">
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
                    </div>
                <?php } ?>

            

                <?php if (!in_array("phone", $hidden_fields)) { ?>
                    <div class="form-group-wrapper">
                        <div class="form-group">
                            <label for="phone"><?php echo app_lang('phone'); ?>*</label>
                            <div class="form-control-wrapper">
                                <?php
                                echo form_input(array(
                                    "id" => "phone",
                                    "name" => "phone",
                                    "class" => "form-control",
                                    "placeholder" => app_lang('phone'),
                                                   "data-rule-required" => true,
                                    "data-msg-required" => app_lang("field_required"),
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="pt10 mt15">
                <div class="table-responsive general-form">
                    <table id="estimate-form-table" class="display b-t no-thead b-b-only no-hover" cellspacing="0" width="100%">
                    </table>
                </div>
                <div>
                    <div class=" "><?php echo custom_nl2br($model_info->description ? $model_info->description : ""); ?></div>
                    <div class="pl10 pr10"> <?php echo view("signin/re_captcha"); ?>  </div>
                </div>
            </div>

            <?php if ($model_info->enable_attachment) { ?>
                <div class="clearfix pl10 pr10 b-b">
                    <?php echo view("includes/dropzone_preview"); ?>
                </div>
            <?php } ?>
            <div class="p15">
                <div class="float-start">
                    <?php
                    if ($model_info->enable_attachment) {
                        echo view("includes/upload_button");
                    }
                    ?>
                </div>
                <button type="submit" class="btn btn-primary float-end"><i data-feather="send" class="icon-16"></i> <?php echo app_lang('request_an_estimate'); ?></button>
            </div>
        </div>

        <?php
        echo form_close();
        ?>
    </div>
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
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $googleMapsApiKey; ?>&libraries=places&callback=googleMapsReady&loading=async"></script>
<?php } ?>

<script type="text/javascript">
    $(document).ready(function() {
        // Initialize appTable
        $("#estimate-form-table").appTable({
            source: '<?php echo_uri("request_estimate/estimate_form_filed_list_data/" . $model_info->id . "/" . $all_fields) ?>',
            order: [[1, "asc"]],
            hideTools: true,
            displayLength: 100,
            columns: [
                {title: "<?php echo app_lang("title") ?>"},
                {visible: false},
                {visible: false}
            ],
            onInitComplete: function() {
                $(".dataTables_empty").hide();
            }
        });

        // Initialize appForm
        $("#estimate-request-form").appForm({
            isModal: false,
            onSubmit: function() {
                appLoader.show();
                $("#estimate-request-form").find('[type="submit"]').attr('disabled', 'disabled');
            },
            onSuccess: function(result) {
                appLoader.hide();
                $("#estimate-form-container").html("");
                appAlert.success(result.message, {
                    container: "#estimate-form-container",
                    animate: false
                });
                $('.scrollable-page').scrollTop(0);
            }
        });

        // Prevent browser autofill on address field
        var addressInput = $('#address');
        addressInput.attr('autocomplete', 'new-address');
        addressInput.on('focus', function() {
            $(this).attr('autocomplete', 'new-address');
        });
    });

    // Google Places Autocomplete Initialization
    function initAutocomplete() {
        var addressInput = document.getElementById('address');
        var autocomplete = new google.maps.places.PlaceAutocompleteElement();
        autocomplete.id = addressInput.id;
        autocomplete.name = addressInput.name;
        autocomplete.className = addressInput.className;
        autocomplete.setAttribute('placeholder', addressInput.placeholder || '');
        addressInput.parentNode.replaceChild(autocomplete, addressInput);

        autocomplete.addEventListener('gmp-placeselect', function(event) {
            var place = (event.detail && event.detail.place) ? event.detail.place : event.target.value;
            if (!place || !place.address_components) {
                return;
            }
            var addressComponents = place.address_components;

            autocomplete.value = '';
            $('#city').val('');
            $('#state').val('');
            $('#zip').val('');
            $('#country').val('');

            var streetNumber = '';
            var route = '';
            var city = '';
            var province = '';
            var postalCode = '';
            var country = '';

            for (var i = 0; i < addressComponents.length; i++) {
                var component = addressComponents[i];
                var types = component.types;

                if (types.includes('street_number')) {
                    streetNumber = component.short_name;
                }
                if (types.includes('route')) {
                    route = component.long_name;
                }
                if (types.includes('locality')) {
                    city = component.long_name;
                }
                if (types.includes('administrative_area_level_1')) {
                    province = component.long_name;
                }
                if (types.includes('postal_code')) {
                    postalCode = component.short_name;
                }
                if (types.includes('country')) {
                    country = component.long_name;
                }
            }

            var fullAddress = streetNumber && route ? streetNumber + ' ' + route : route;
            autocomplete.value = fullAddress;
            $('#city').val(city);
            $('#state').val(province);
            $('#zip').val(postalCode);
            $('#country').val(country);
        });
    }
</script>

<?php echo form_open(get_uri("clients/save"), array("id" => "client-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>" />
        <?php
            // Default the country to Canada when adding a new client
            if (!$model_info->country) {
                $model_info->country = "Canada";
            }
            echo view("clients/client_form_fields", ["hide_client_labels" => true]);
        ?>
    </div>
</div>

<div class="modal-footer">
    <div id="link-of-add-contact-modal" class="hide">
        <?php echo modal_anchor(get_uri("clients/add_new_contact_modal_form"), "", array()); ?>
    </div>

    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <?php if (!$model_info->id) { ?>
        <button type="button" id="save-and-continue-button" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save_and_continue'); ?></button>
    <?php } ?>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        var ticket_id = "<?php echo $ticket_id; ?>";

        window.clientForm = $("#client-form").appForm({
            closeModalOnSuccess: false,
            onSuccess: function (result) {
                var $addMultipleContactsLink = $("#link-of-add-contact-modal").find("a");

                if (result.view === "details" || ticket_id) {
                    if (window.showAddNewModal) {
                        $addMultipleContactsLink.attr("data-post-client_id", result.id);
                        $addMultipleContactsLink.attr("data-title", "<?php echo app_lang('add_multiple_contacts') ?>");
                        $addMultipleContactsLink.attr("data-post-add_type", "multiple");

                        $addMultipleContactsLink.trigger("click");
                    } else {
                        appAlert.success(result.message, {duration: 10000});
                        setTimeout(function () {
                            location.reload();
                        }, 500);
                    }
                } else if (window.showAddNewModal) {
                    $addMultipleContactsLink.attr("data-post-client_id", result.id);
                    $addMultipleContactsLink.attr("data-title", "<?php echo app_lang('add_multiple_contacts') ?>");
                    $addMultipleContactsLink.attr("data-post-add_type", "multiple");

                    $addMultipleContactsLink.trigger("click");

                    $("#client-table").appTable({newData: result.data, dataId: result.id});
                } else {
                    $("#client-table").appTable({newData: result.data, dataId: result.id});
                    window.clientForm.closeModal();
                }
            }
        });
        var $modal = $("#client-form").closest(".modal");
        $modal.on("shown.bs.modal", function () {
            $("#company_name").focus();
        });
        if ($modal.hasClass("show")) {
            $("#company_name").focus();
        }

        //save and open add new contact member modal
        window.showAddNewModal = false;

        $("#save-and-continue-button").click(function () {
            window.showAddNewModal = true;
            $(this).trigger("submit");
        });

        // Initialize Google Places Autocomplete on this form only.
        function initFormAddressAutocomplete(formSelector) {
            var form = document.querySelector(formSelector);
            if (!form || typeof google === 'undefined' || !google.maps || !google.maps.places) {
                return;
            }

            var addressInput = form.querySelector('#address');
            if (!addressInput) {
                return;
            }

            ['address', 'city', 'state', 'zip', 'country'].forEach(function (id) {
                var field = form.querySelector('#' + id);
                if (field) {
                    field.setAttribute('autocomplete', 'new-password');
                }
            });

            var autocomplete = new google.maps.places.Autocomplete(addressInput, {fields: ['address_components']});
            autocomplete.addListener('place_changed', function () {
                var place = autocomplete.getPlace();
                if (!place.address_components) {
                    return;
                }

                var address = '', city = '', state = '', zip = '', country = '';
                place.address_components.forEach(function (component) {
                    var types = component.types;
                    if (types.indexOf('street_number') > -1) {
                        address = component.long_name + (address ? ' ' + address : '');
                    }
                    if (types.indexOf('route') > -1) {
                        address = address ? address + ' ' + component.long_name : component.long_name;
                    }
                    if (types.indexOf('locality') > -1) {
                        city = component.long_name;
                    }
                    if (types.indexOf('administrative_area_level_1') > -1) {
                        state = component.short_name;
                    }
                    if (types.indexOf('postal_code') > -1) {
                        zip = component.long_name;
                    }
                    if (types.indexOf('country') > -1) {
                        country = component.long_name;
                    }
                });

                if (address) { form.querySelector('#address').value = address; }
                if (city) { form.querySelector('#city').value = city; }
                if (state) { form.querySelector('#state').value = state; }
                if (zip) { form.querySelector('#zip').value = zip; }
                if (country) { form.querySelector('#country').value = country; }
            });
        }

        initFormAddressAutocomplete('#client-form');
    });
</script>


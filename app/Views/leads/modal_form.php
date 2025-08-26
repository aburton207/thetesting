<?php echo form_open(get_uri("leads/save"), array("id" => "lead-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <?php echo view("leads/lead_form_fields"); ?>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#lead-form").appForm({
            onSuccess: function (result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                } else {
                    $("#lead-table").appTable({newData: result.data, dataId: result.id});
                    $("#reload-kanban-button:visible").trigger("click");
                }
            }
        });
        var $modal = $("#lead-form").closest(".modal");
        $modal.on("shown.bs.modal", function () {
            $("#company_name").focus();
        });
        if ($modal.hasClass("show")) {
            $("#company_name").focus();
        }

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

        initFormAddressAutocomplete('#lead-form');
    });
    </script>


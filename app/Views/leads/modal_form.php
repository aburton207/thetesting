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

<!-- Google Maps API Script -->
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

        setTimeout(function () {
            $("#company_name").focus();
        }, 200);

        // Prevent browser autofill on address field
        var addressInput = $('#address');
        addressInput.attr('autocomplete', 'new-address');
        addressInput.on('focus', function () {
            $(this).attr('autocomplete', 'new-address');
        });

        // Dynamic logic: override lead source based on location
        $("#state, #city").on("change keyup", updateLeadSource);

        loadMapsScript(initLeadModalAutocomplete);
    });

    function loadMapsScript(callback) {
        if (window.google && google.maps && google.maps.places) {
            callback();
            return;
        }
        var script = document.createElement('script');
        script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyCN7FS848BKLuWUjFlV6c7NKxDlcebCL_g&libraries=places";
        script.async = true;
        script.defer = true;
        script.onload = callback;
        document.head.appendChild(script);
    }
    function updateLeadSource() {
        var cityVal = $("#city").val().trim().toLowerCase();
        var provinceVal = $("#state").val();

        if (!provinceVal) {
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

    // Google Places Autocomplete Initialization
    function initLeadModalAutocomplete() {
        var addressInput = document.getElementById('address');
        var autocomplete = new google.maps.places.Autocomplete(addressInput, {
            types: ['address'],
            componentRestrictions: { country: ['ca'] },
            fields: ['address_components', 'geometry', 'formatted_address']
        });

        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            var addressComponents = place.address_components;

            $('#address').val('');
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
            $('#address').val(fullAddress);
            $('#city').val(city);
            $('#state').val(province).trigger('change');
            $('#zip').val(postalCode);
            $('#country').val(country);

            updateLeadSource();
        });
    }
</script>



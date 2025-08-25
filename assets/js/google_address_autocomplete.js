// Initialize Google Places Autocomplete for address fields
(function (window) {
    window.initAddressAutocomplete = function (formSelector) {
        var $form = $(formSelector);
        if (!$form.length || typeof google === "undefined" || !google.maps || !google.maps.places) {
            return;
        }

        var $address = $form.find('#address');
        if (!$address.length) {
            return;
        }

        var autocomplete = new google.maps.places.Autocomplete($address[0], { types: ['address'] });

        var fields = ['address', 'city', 'state', 'zip', 'country'];
        fields.forEach(function (field) {
            $form.find('#' + field).attr('autocomplete', 'off');
        });

        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            if (!place.address_components) {
                return;
            }

            var address = '';
            var city = '';
            var state = '';
            var zip = '';
            var country = '';

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

            if (address) {
                $form.find('#address').val(address);
            }
            if (city) {
                $form.find('#city').val(city);
            }
            if (state) {
                $form.find('#state').val(state);
            }
            if (zip) {
                $form.find('#zip').val(zip);
            }
            if (country) {
                $form.find('#country').val(country);
            }
        });
    };
})(window);


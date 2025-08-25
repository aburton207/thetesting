// Initialize Google Places Autocomplete for address fields.
// This script intentionally avoids using a MutationObserver; instead,
// delegated event handlers initialize autocomplete on demand when users
// focus an address field or when related modals are displayed.
(function (window) {
    window.initAddressAutocomplete = function (context) {
        var $root;
        if (context && context.target) {
            $root = $(context.target);
        } else {
            $root = $(context);
        }

        var $forms = $();
        if ($root && $root.length) {
            if ($root.is('#lead-form, #client-form')) {
                $forms = $root;
            } else {
                $forms = $root.closest('#lead-form, #client-form')
                    .add($root.find('#lead-form, #client-form'));
            }
        }

        if (!$forms.length) {
            return;
        }

        if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
            console.warn('Google Places API is not available.');
            return;
        }

        $forms.each(function () {
            var $form = $(this);
            var $addresses = $form.find('#address');
            if (!$addresses.length) {
                return;
            }

            $addresses.each(function () {
                var $address = $(this);
                if ($address.data('gplaces-init')) {
                    return;
                }
                $address.data('gplaces-init', true);

                var autocomplete = new google.maps.places.PlaceAutocompleteElement({
                    inputElement: $address[0]
                });
                $(autocomplete).insertAfter($address);

                var fields = ['address', 'city', 'state', 'zip', 'country'];
                fields.forEach(function (field) {
                    // Use a nonstandard autocomplete value to suppress Chrome's
                    // native suggestions so Google Places results remain visible.
                    $form.find('#' + field).attr('autocomplete', 'new-password');
                });

                autocomplete.addEventListener('place_changed', function () {
                    var place = autocomplete.place;
                    if (!place || !place.address_components) {
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
                        autocomplete.value = address;
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
            });
        });
    };
})(window);

// Initialize autocomplete when users interact with the page
$(document).on('focus', '#lead-form #address, #client-form #address', initAddressAutocomplete);
$(document).on('shown.bs.modal', '#lead-modal, #client-modal', function () {
    initAddressAutocomplete(this);
});

// Ensure existing address fields are enhanced once the page is fully loaded
window.addEventListener('load', function () {
    if (window.initAddressAutocomplete) {
        window.initAddressAutocomplete(document);
    }
});


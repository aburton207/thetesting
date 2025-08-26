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

                var fields = ['address', 'city', 'state', 'zip', 'country'];
                fields.forEach(function (field) {
                    // Use a nonstandard autocomplete value to suppress Chrome's
                    // native suggestions so Google Places results remain visible.
                    $form.find('#' + field).attr('autocomplete', 'new-password');
                });

                var handlePlace = function (place) {
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
                        $address.val(address).trigger('change');
                    }
                    if (city) {
                        $form.find('#city').val(city).trigger('change');
                    }
                    if (state) {
                        $form.find('#state').val(state).trigger('change');
                    }
                    if (zip) {
                        $form.find('#zip').val(zip).trigger('change');
                    }
                    if (country) {
                        $form.find('#country').val(country).trigger('change');
                    }
                };

                // Prefer the newer PlaceAutocompleteElement when available to
                // avoid reliance on the deprecated Autocomplete widget.
                if (google.maps.places.PlaceAutocompleteElement) {
                    var autocomplete = new google.maps.places.PlaceAutocompleteElement();
                    autocomplete.fields = ['addressComponents'];
                    autocomplete.inputElement = $address[0];
                    autocomplete.addEventListener('gmpx-placechange', function () {
                        handlePlace(autocomplete.getPlace());
                    });
                } else if (google.maps.places.Autocomplete) {
                    var autocomplete = new google.maps.places.Autocomplete($address[0], {
                        fields: ['address_components']
                    });
                    autocomplete.addListener('place_changed', function () {
                        handlePlace(autocomplete.getPlace());
                    });
                } else {
                    console.warn('Google Places Autocomplete is not available.');
                }
            });
        });
    };
})(window);

// Forms should call window.initAddressAutocomplete(form) after rendering


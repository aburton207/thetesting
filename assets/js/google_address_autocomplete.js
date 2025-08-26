// Initialize Google Places Autocomplete for address fields.
// Instead of relying on MutationObserver (which can throw
// "parameter 1 is not of type 'Node'" errors when the body
// element isn't ready yet), this file attaches light‑weight
// event handlers to initialise the autocomplete widget only
// when the relevant DOM nodes exist.
(function (window) {
    window.initAddressAutocomplete = function (context) {
        // Normalise the context so it can be an event, element or jQuery
        // object. When the function is called without arguments we simply
        // fall back to the document itself.
        context = context || document;
        var $root = $(context.target || context);

        var $forms = $();
        if ($root.length) {
            if ($root.is('form')) {
                $forms = $root;
            } else if ($root.is('input')) {
                $forms = $root.closest('form');
            } else {
                $forms = $root.find('form');
            }
        }

        $forms = $forms.filter(function () {
            return $(this).find('#address').length;
        });

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

                // Prefer the classic Autocomplete widget for broader
                // compatibility. Avoid the newer PlaceAutocompleteElement
                // since it relies on MutationObserver and can trigger errors
                // when inputs are initialized inside modals.
                if (google.maps.places.Autocomplete) {
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

// Initialize for existing forms on page load
$(function () {
    window.initAddressAutocomplete(document);
});

// Initialize when an address field receives focus
$(document).on('focus', 'form #address', function () {
    // Pass the parent form so the initializer can correctly wire up fields
    window.initAddressAutocomplete(this.form || $(this).closest('form')[0]);
});

// Initialize when modals containing forms are shown
$(document).on('shown.bs.modal', function (e) {
    window.initAddressAutocomplete(e.target);
});

// Initialize when new content is injected via AJAX
$(document).on('ajaxComplete', function () {
    window.initAddressAutocomplete(document);
});


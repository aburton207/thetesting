// Initialize Google Places Autocomplete for address fields.
// Retries automatically if the Google Places library is not yet available
// to avoid silent failures when the API loads slowly.
(function (window) {
    window.initAddressAutocomplete = function (form, attempt) {
        var $forms = $(form);
        if (!$forms.length) {
            return;
        }

        var retries = typeof attempt === 'number' ? attempt : 0;
        var maxRetries = 10;
        if (typeof google === "undefined" || !google.maps || !google.maps.places) {
            // Retry with incremental backoff until Google Places becomes available
            if (retries < maxRetries) {
                setTimeout(function () {
                    window.initAddressAutocomplete(form, retries + 1);
                }, 500);
            } else {
                console.warn("Google Places API not loaded after", maxRetries, "attempts");
            }
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
            });
        });
    };
})(window);

// Monitor DOM changes safely and re-initialize address autocompletion
(function () {
    var lastHref = window.location.href;

    function initForms(root) {
        if (typeof window.initAddressAutocomplete !== "function") {
            return;
        }
        var $root = root ? $(root) : $(document);
        $root.find('#lead-form, #client-form')
            .add($root.filter('#lead-form, #client-form'))
            .each(function () {
                window.initAddressAutocomplete(this);
            });
    }

    function startObserver() {
        var target = document.body || document.documentElement;

        // Ensure a valid DOM node is available before observing
        var nodeCtor = window.Node;
        if (!nodeCtor || !(target instanceof nodeCtor)) {
            return;
        }

        var MutationObs = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;
        if (!MutationObs) {
            return;
        }

        var observer = new MutationObs(function () {
            if (window.location.href !== lastHref) {
                lastHref = window.location.href;
                initForms();
            }
        });

        try {
            observer.observe(target, {
                childList: true,
                subtree: true
            });
        } catch (e) {
            console.error("MutationObserver failed", e);
        }
    }

    if (document.readyState === "loading") {
        window.addEventListener("DOMContentLoaded", startObserver);
    } else {
        startObserver();
    }
    // Re-initialize autocomplete when Bootstrap modals are shown
    if (typeof $ === 'function' && $.fn && $.fn.modal) {
        $(document).on('shown.bs.modal', function (e) {
            initForms(e.target);
        });
    }

    initForms();
})();


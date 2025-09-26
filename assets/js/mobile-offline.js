(function () {
    if (typeof window === 'undefined' || typeof navigator === 'undefined') {
        return;
    }

    var bannerId = 'mobile-offline-banner';
    var styleId = 'mobile-offline-banner-style';

    function ensureStyles() {
        if (document.getElementById(styleId)) {
            return;
        }

        var style = document.createElement('style');
        style.id = styleId;
        style.textContent = '
            #' + bannerId + ' {
                background: #f0ad4e;
                color: #1c2026;
                text-align: center;
                padding: 10px;
                font-weight: 600;
                position: sticky;
                top: 0;
                z-index: 2147483647;
            }
        ';
        document.head.appendChild(style);
    }

    function showOfflineBanner() {
        if (typeof isMobile !== 'function' || !isMobile()) {
            return;
        }

        ensureStyles();

        if (document.getElementById(bannerId)) {
            return;
        }

        var banner = document.createElement('div');
        banner.id = bannerId;
        banner.textContent = (window.AppLanugage && AppLanugage.offlineModeMessage) ? AppLanugage.offlineModeMessage : 'Offline mode: showing last synced data.';
        document.body.prepend(banner);
    }

    function hideOfflineBanner() {
        var banner = document.getElementById(bannerId);
        if (banner) {
            banner.remove();
        }
    }

    window.addEventListener('offline', showOfflineBanner);
    window.addEventListener('online', function () {
        hideOfflineBanner();
        if (window.appTables) {
            Object.keys(window.appTables).forEach(function (tableId) {
                var tableInstance = window.appTables[tableId];
                if (!tableInstance) {
                    return;
                }

                var $table = $('#' + tableId);
                if ($table.length && $table.closest('.dataTables_wrapper').length) {
                    $table.appTable({ reload: true });
                }
            });
        }
    });

    $(document).on('xhr.dt', function (event, settings, json) {
        if (json && json.offline) {
            showOfflineBanner();
        } else if (navigator.onLine) {
            hideOfflineBanner();
        }
    });
})();

# Offline capabilities overview

## Mobile app caching

The CRM now ships with an offline-first service worker (`assets/js/sw/sw.js`) that precaches the PWA shell assets and dynamically stores API responses for key mobile screens. When the device is offline the worker serves the most recent `leads/list_data` and `clients/list_data` payloads from the cache and annotates them so the UI can surface an "offline" banner. The mobile bootstrap (`assets/js/mobile-offline.js`) also reloads the affected tables once connectivity returns so fresh data is pulled from the network.

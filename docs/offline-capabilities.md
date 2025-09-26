# Offline capabilities overview

## Mobile app caching

The current CRM exposes a service worker at `assets/js/sw/sw.js`, but it is only used to intercept notification-related fetches and to open the web app when a push notification is tapped. The script does not precache application shell assets or API responses, nor does it use the Cache Storage API to persist data locally. As a result, the iOS-installed web app requires a live network connection to load leads, clients, or other records after login.

To support offline access in the future, you would need to extend the service worker to precache critical resources and cache API responses, and adjust the mobile experience to read from those caches when the network is unavailable.

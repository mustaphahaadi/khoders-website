// KHODERS WORLD - Service Worker for PWA capabilities
// Version 1.0.0

const CACHE_NAME = 'khoders-world-v1.0.0';
const urlsToCache = [
    '/',
    '/index.html',
    '/style.css',
    '/script.js',
    '/assets/qwe.png',
    '/assets/placeholder-images.js',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css',
    'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js'
];

// Install event - cache resources
self.addEventListener('install', (event) => {
    console.log('Service Worker: Installing...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('Service Worker: Caching files');
                return cache.addAll(urlsToCache);
            })
            .then(() => {
                console.log('Service Worker: Installed successfully');
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('Service Worker: Installation failed', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activating...');
    
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Service Worker: Deleting old cache', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            console.log('Service Worker: Activated successfully');
            return self.clients.claim();
        })
    );
});

// Fetch event - serve cached content when offline
self.addEventListener('fetch', (event) => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') {
        return;
    }
    
    // Skip external requests (different origin)
    if (!event.request.url.startsWith(self.location.origin) && 
        !event.request.url.startsWith('https://cdnjs.cloudflare.com')) {
        return;
    }
    
    event.respondWith(
        caches.match(event.request)
            .then((response) => {
                // Return cached version if available
                if (response) {
                    console.log('Service Worker: Serving from cache', event.request.url);
                    return response;
                }
                
                // Otherwise fetch from network
                console.log('Service Worker: Fetching from network', event.request.url);
                return fetch(event.request)
                    .then((response) => {
                        // Don't cache if not a valid response
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }
                        
                        // Clone the response
                        const responseToCache = response.clone();
                        
                        // Add to cache for future use
                        caches.open(CACHE_NAME)
                            .then((cache) => {
                                cache.put(event.request, responseToCache);
                            });
                        
                        return response;
                    })
                    .catch((error) => {
                        console.error('Service Worker: Fetch failed', error);
                        
                        // Return offline page for navigation requests
                        if (event.request.destination === 'document') {
                            return caches.match('/index.html');
                        }
                        
                        // Return a generic offline response for other requests
                        return new Response('Offline - Content not available', {
                            status: 503,
                            statusText: 'Service Unavailable',
                            headers: new Headers({
                                'Content-Type': 'text/plain'
                            })
                        });
                    });
            })
    );
});

// Background sync for form submissions
self.addEventListener('sync', (event) => {
    console.log('Service Worker: Background sync triggered', event.tag);
    
    if (event.tag === 'contact-form-sync') {
        event.waitUntil(syncContactForm());
    } else if (event.tag === 'newsletter-sync') {
        event.waitUntil(syncNewsletterForm());
    }
});

// Push notification handler
self.addEventListener('push', (event) => {
    console.log('Service Worker: Push notification received');
    
    const options = {
        body: event.data ? event.data.text() : 'New update from KHODERS!',
        icon: '/assets/qwe.png',
        badge: '/assets/qwe.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'Explore',
                icon: '/assets/qwe.png'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/assets/qwe.png'
            }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification('KHODERS World', options)
    );
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
    console.log('Service Worker: Notification clicked', event.action);
    
    event.notification.close();
    
    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Message handler for communication with main thread
self.addEventListener('message', (event) => {
    console.log('Service Worker: Message received', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: CACHE_NAME });
    }
});

// Helper functions for background sync
async function syncContactForm() {
    try {
        // Get pending contact form submissions from IndexedDB
        const pendingForms = await getPendingContactForms();
        
        for (const form of pendingForms) {
            try {
                const response = await fetch('/api/contact', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(form.data)
                });
                
                if (response.ok) {
                    await removePendingContactForm(form.id);
                    console.log('Service Worker: Contact form synced successfully');
                }
            } catch (error) {
                console.error('Service Worker: Failed to sync contact form', error);
            }
        }
    } catch (error) {
        console.error('Service Worker: Contact form sync failed', error);
    }
}

async function syncNewsletterForm() {
    try {
        // Get pending newsletter subscriptions from IndexedDB
        const pendingSubscriptions = await getPendingNewsletterSubscriptions();
        
        for (const subscription of pendingSubscriptions) {
            try {
                const response = await fetch('/api/newsletter', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(subscription.data)
                });
                
                if (response.ok) {
                    await removePendingNewsletterSubscription(subscription.id);
                    console.log('Service Worker: Newsletter subscription synced successfully');
                }
            } catch (error) {
                console.error('Service Worker: Failed to sync newsletter subscription', error);
            }
        }
    } catch (error) {
        console.error('Service Worker: Newsletter sync failed', error);
    }
}

// IndexedDB helper functions (simplified implementation)
async function getPendingContactForms() {
    // Implementation would use IndexedDB to retrieve pending forms
    return [];
}

async function removePendingContactForm(id) {
    // Implementation would remove the form from IndexedDB
    console.log('Removing pending contact form:', id);
}

async function getPendingNewsletterSubscriptions() {
    // Implementation would use IndexedDB to retrieve pending subscriptions
    return [];
}

async function removePendingNewsletterSubscription(id) {
    // Implementation would remove the subscription from IndexedDB
    console.log('Removing pending newsletter subscription:', id);
}

// Error handler
self.addEventListener('error', (event) => {
    console.error('Service Worker: Error occurred', event.error);
});

// Unhandled rejection handler
self.addEventListener('unhandledrejection', (event) => {
    console.error('Service Worker: Unhandled promise rejection', event.reason);
});

console.log('Service Worker: Script loaded successfully');
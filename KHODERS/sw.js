// KHODERS World Service Worker
// Provides offline functionality and caching

const CACHE_NAME = 'khoders-world-v1.0.1';
// Build cache URLs relative to the current scope so this works in subdirectories
const RELATIVE_URLS = [
  'index.html',
  'about.html',
  'services.html',
  'projects.html',
  'team.html',
  'events.html',
  'blog.html',
  'contact.html',
  'register.html',
  'style.css',
  'script.js',
  'assets/qwe.png',
  'assets/image-1.png',
  'assets/image-2.png',
  'manifest.json',
  '404.html'
];
const urlsToCache = RELATIVE_URLS.map(u => new URL(u, self.location).pathname);

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
        console.log('Service Worker: Cached all files successfully');
        return self.skipWaiting();
      })
      .catch((error) => {
        console.error('Service Worker: Cache failed', error);
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

  // Skip external requests
  if (!event.request.url.startsWith(self.location.origin)) {
    return;
  }

  event.respondWith(
    caches.match(event.request)
      .then((response) => {
        // Return cached version or fetch from network
        if (response) {
          console.log('Service Worker: Serving from cache', event.request.url);
          return response;
        }

        console.log('Service Worker: Fetching from network', event.request.url);
        return fetch(event.request).then((response) => {
          // Don't cache if not a valid response
          if (!response || response.status !== 200 || response.type !== 'basic') {
            return response;
          }

          // Clone the response
          const responseToCache = response.clone();

          caches.open(CACHE_NAME)
            .then((cache) => {
              cache.put(event.request, responseToCache);
            });

          return response;
        });
      })
      .catch(() => {
        // Return offline page for navigation requests
        if (event.request.destination === 'document') {
          return caches.match(new URL('404.html', self.location).pathname);
        }
      })
  );
});

// Background sync for form submissions
self.addEventListener('sync', (event) => {
  if (event.tag === 'contact-form') {
    event.waitUntil(syncContactForm());
  }
  if (event.tag === 'registration-form') {
    event.waitUntil(syncRegistrationForm());
  }
});

// Push notification handler
self.addEventListener('push', (event) => {
  const options = {
    body: event.data ? event.data.text() : 'New update from KHODERS!',
    icon: new URL('assets/qwe.png', self.location).pathname,
    badge: new URL('assets/qwe.png', self.location).pathname,
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    },
    actions: [
      {
        action: 'explore',
        title: 'Explore',
        icon: new URL('assets/qwe.png', self.location).pathname
      },
      {
        action: 'close',
        title: 'Close',
        icon: new URL('assets/qwe.png', self.location).pathname
      }
    ]
  };

  event.waitUntil(
    self.registration.showNotification('KHODERS World', options)
  );
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'explore') {
    event.waitUntil(
      clients.openWindow(new URL('index.html', self.location).href)
    );
  }
});

// Helper functions
async function syncContactForm() {
  try {
    const formData = await getStoredFormData('contact-form');
    if (formData) {
      const response = await fetch(new URL('api/contact.php', self.location).href, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
      });

      if (response.ok) {
        await clearStoredFormData('contact-form');
        console.log('Contact form synced successfully');
      }
    }
  } catch (error) {
    console.error('Failed to sync contact form:', error);
  }
}

async function syncRegistrationForm() {
  try {
    const formData = await getStoredFormData('registration-form');
    if (formData) {
      const response = await fetch(new URL('api/register.php', self.location).href, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
      });

      if (response.ok) {
        await clearStoredFormData('registration-form');
        console.log('Registration form synced successfully');
      }
    }
  } catch (error) {
    console.error('Failed to sync registration form:', error);
  }
}

async function getStoredFormData(key) {
  return new Promise((resolve) => {
    // This would typically use IndexedDB
    // For now, return null as we don't have form storage implemented
    resolve(null);
  });
}

async function clearStoredFormData(key) {
  return new Promise((resolve) => {
    // This would typically clear IndexedDB data
    resolve();
  });
}

// Update notification
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

console.log('Service Worker: Loaded successfully');
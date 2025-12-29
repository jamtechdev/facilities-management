/**
 * Firebase Cloud Messaging (FCM) Integration
 * Handles push notifications via Firebase
 */

(function() {
    'use strict';

    // Firebase configuration - should be loaded from config
    const firebaseConfig = {
        apiKey: window.firebaseConfig?.apiKey || '',
        authDomain: window.firebaseConfig?.authDomain || '',
        projectId: window.firebaseConfig?.projectId || '',
        storageBucket: window.firebaseConfig?.storageBucket || '',
        messagingSenderId: window.firebaseConfig?.messagingSenderId || '',
        appId: window.firebaseConfig?.appId || '',
    };

    let messaging = null;
    let currentToken = null;

    /**
     * Initialize Firebase and FCM
     */
    function initializeFirebase() {
        // Check if Firebase is available
        if (typeof firebase === 'undefined') {
            console.warn('Firebase SDK not loaded. Push notifications will not work.');
            return false;
        }

        try {
            // Initialize Firebase
            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }

            // Initialize Cloud Messaging and get a reference to the service
            messaging = firebase.messaging();

            // Request permission and get token
            requestPermission();

            // Handle token refresh
            messaging.onTokenRefresh(() => {
                messaging.getToken({ vapidKey: window.firebaseConfig?.vapidKey })
                    .then((token) => {
                        if (token) {
                            currentToken = token;
                            saveTokenToServer(token);
                        }
                    })
                    .catch((err) => {
                        console.error('Unable to retrieve refreshed token', err);
                    });
            });

            // Handle foreground messages
            messaging.onMessage((payload) => {
                console.log('Message received in foreground:', payload);
                showNotification(payload.notification);
            });

            return true;
        } catch (error) {
            console.error('Firebase initialization error:', error);
            return false;
        }
    }

    /**
     * Request notification permission
     */
    function requestPermission() {
        Notification.requestPermission().then((permission) => {
            if (permission === 'granted') {
                console.log('Notification permission granted.');
                getToken();
            } else {
                console.log('Notification permission denied.');
            }
        });
    }

    /**
     * Get FCM token
     */
    function getToken() {
        if (!messaging) {
            console.error('Messaging not initialized');
            return;
        }

        messaging.getToken({ vapidKey: window.firebaseConfig?.vapidKey })
            .then((token) => {
                if (token) {
                    currentToken = token;
                    saveTokenToServer(token);
                } else {
                    console.log('No registration token available.');
                }
            })
            .catch((err) => {
                console.error('An error occurred while retrieving token', err);
            });
    }

    /**
     * Save FCM token to server
     */
    function saveTokenToServer(token) {
        if (!token) return;

        fetch('/api/fcm-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ fcm_token: token })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('FCM token saved successfully');
            } else {
                console.error('Failed to save FCM token:', data.message);
            }
        })
        .catch(error => {
            console.error('Error saving FCM token:', error);
        });
    }

    /**
     * Show browser notification
     */
    function showNotification(notification) {
        if (!('Notification' in window)) {
            console.log('This browser does not support notifications');
            return;
        }

        if (Notification.permission === 'granted') {
            const notificationOptions = {
                body: notification.body,
                icon: notification.icon || '/images/logo.png',
                badge: '/images/badge.png',
                tag: notification.tag || 'default',
                requireInteraction: false,
                data: notification.data || {}
            };

            const n = new Notification(notification.title, notificationOptions);

            n.onclick = function(event) {
                event.preventDefault();
                window.focus();
                if (notification.data && notification.data.url) {
                    window.open(notification.data.url, '_blank');
                }
                n.close();
            };

            // Auto close after 5 seconds
            setTimeout(() => {
                n.close();
            }, 5000);
        }
    }

    /**
     * Initialize when DOM is ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            // Only initialize if user is authenticated
            if (document.body.classList.contains('authenticated')) {
                initializeFirebase();
            }
        });
    } else {
        if (document.body.classList.contains('authenticated')) {
            initializeFirebase();
        }
    }

    // Export functions for manual use
    window.FirebaseNotifications = {
        initialize: initializeFirebase,
        getToken: getToken,
        requestPermission: requestPermission
    };
})();

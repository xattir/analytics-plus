(function() {
    'use strict';
    
    // Configuration
    const API_URL = window.ANALYTICS_API_URL || '/api/analytics/track';
    const SESSION_TIMEOUT = 30 * 60 * 1000; // 30 minutes
    
    // State
    let sessionId = null;
    let deviceFingerprint = null;
    
    // Initialize
    function init() {
        // Get or create session ID
        sessionId = getSessionId();
        
        // Generate device fingerprint
        deviceFingerprint = generateFingerprint();
        
        // Track page view
        trackPageView();
    }
    
    // Get or create session ID
    function getSessionId() {
        let id = sessionStorage.getItem('analytics_session_id');
        const sessionTimestamp = sessionStorage.getItem('analytics_session_timestamp');
        
        // Check if session expired
        if (!id || !sessionTimestamp || (Date.now() - parseInt(sessionTimestamp)) > SESSION_TIMEOUT) {
            id = generateUUID();
            sessionStorage.setItem('analytics_session_id', id);
            sessionStorage.setItem('analytics_session_timestamp', Date.now().toString());
        }
        
        return id;
    }
    
    // Generate UUID
    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
    
    // Generate device fingerprint
    function generateFingerprint() {
        const components = [
            screen.width,
            screen.height,
            screen.colorDepth,
            new Date().getTimezoneOffset(),
            navigator.language,
            navigator.userAgent,
            navigator.platform,
            navigator.hardwareConcurrency || 0,
            navigator.deviceMemory || 0,
        ];
        
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        ctx.textBaseline = 'top';
        ctx.font = '14px Arial';
        ctx.fillText('Analytics fingerprint', 2, 2);
        components.push(canvas.toDataURL());
        
        const str = components.join('|');
        return hashString(str);
    }
    
    // Simple hash function
    function hashString(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32bit integer
        }
        return Math.abs(hash).toString(16);
    }
    
    // Track page view
    function trackPageView() {
        const data = {
            site_key: window.ANALYTICS_SITE_KEY,
            session_id: sessionId,
            path: window.location.pathname + window.location.search,
            domain: window.location.hostname,
            url: window.location.href,
            referrer: document.referrer,
            user_agent: navigator.userAgent,
            fingerprint: deviceFingerprint,
            screen_width: screen.width,
            screen_height: screen.height,
            viewport_width: window.innerWidth,
            viewport_height: window.innerHeight,
            device_pixel_ratio: window.devicePixelRatio || 1,
            language: navigator.language,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            network_type: getNetworkType(),
            rtt_ms: getRTT(),
            downlink_mbps: getDownlink(),
            utm_source: getUrlParameter('utm_source'),
            utm_medium: getUrlParameter('utm_medium'),
            utm_campaign: getUrlParameter('utm_campaign'),
        };
        
        sendTracking(data);
    }
    
    // Get URL parameter
    function getUrlParameter(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }
    
    // Get network type
    function getNetworkType() {
        if ('connection' in navigator) {
            return navigator.connection.effectiveType || null;
        }
        return null;
    }
    
    // Get RTT
    function getRTT() {
        if ('connection' in navigator && navigator.connection.rtt) {
            return navigator.connection.rtt;
        }
        return null;
    }
    
    // Get downlink
    function getDownlink() {
        if ('connection' in navigator && navigator.connection.downlink) {
            return navigator.connection.downlink;
        }
        return null;
    }
    
    // Send tracking data (always async)
    function sendTracking(data) {
        if (!window.ANALYTICS_SITE_KEY) {
            console.warn('Analytics: ANALYTICS_SITE_KEY is not set');
            return;
        }
        
        // Always use async fetch with keepalive for non-blocking requests
        fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
            keepalive: true,
            // Don't wait for response - fire and forget
        }).catch(function(error) {
            // Silently fail - don't block page rendering
            if (window.console && console.error) {
                console.error('Analytics tracking error:', error);
            }
        });
    }
    
    // Initialize when DOM is ready (async-safe)
    function initializeAnalytics() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            // Use requestIdleCallback if available for better performance
            if (window.requestIdleCallback) {
                requestIdleCallback(init, { timeout: 2000 });
            } else {
                // Fallback: use setTimeout for async execution
                setTimeout(init, 0);
            }
        }
    }
    
    // Start initialization
    initializeAnalytics();
    
    // Expose API
    window.Analytics = {
        track: trackPageView,
        getSessionId: function() { return sessionId; },
    };
})();

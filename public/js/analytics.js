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
        
        // Track page view (ONLY main event - no periodic updates)
        // Ads will be loaded from the response
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
    
    // Track page view (MAIN EVENT ONLY - no periodic updates)
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
            device_type: detectDeviceType(),
            country_code: getCountryCode(),
            // NO engagement metrics - only track initial page view
        };
        
        sendTracking(data);
    }
    
    // Send tracking data (always async)
    // Use FormData to make it a "simple request" and avoid CORS preflight (OPTIONS)
    // Simple requests (POST with FormData) don't require preflight OPTIONS requests
    function sendTracking(data) {
        if (!window.ANALYTICS_SITE_KEY) {
            console.warn('Analytics: ANALYTICS_SITE_KEY is not set');
            return;
        }
        
        // Ensure path is always included
        if (!data.path) {
            data.path = window.location.pathname + window.location.search;
        }
        
        // Use FormData to make it a "simple request" (no preflight OPTIONS needed)
        // POST with multipart/form-data (FormData) is considered a simple request
        // This eliminates the need for OPTIONS preflight requests
        const formData = new FormData();
        Object.keys(data).forEach(function(key) {
            const value = data[key];
            // Convert objects/arrays to JSON strings for FormData
            if (value !== null && value !== undefined) {
                if (typeof value === 'object') {
                    formData.append(key, JSON.stringify(value));
                } else {
                    formData.append(key, String(value));
                }
            }
        });
        
        // Simple request: POST with FormData (multipart/form-data)
        // No Content-Type header needed - browser sets it automatically with boundary
        // This makes it a "simple request" that doesn't require CORS preflight (OPTIONS)
        fetch(API_URL, {
            method: 'POST',
            body: formData,
            keepalive: true,
            // Don't set Content-Type header - browser will set it automatically as multipart/form-data
            // Simple requests don't trigger preflight OPTIONS requests
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(result) {
            // Handle ads from response
            if (result.success && result.ads && result.ads.length > 0) {
                result.ads.forEach(function(ad) {
                    // Check if selector exists in page before injecting
                    if (ad.selector && document.querySelector(ad.selector)) {
                        injectAd(ad);
                    }
                });
            }
        })
        .catch(function(error) {
            // Silently fail - don't block page rendering
            if (window.console && console.error) {
                console.error('Analytics tracking error:', error);
            }
        });
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
    
    // Load advertisements
    function loadAds() {
        if (!window.ANALYTICS_SITE_KEY) {
            return;
        }

        const selectors = findSelectorsInPage();
        if (selectors.length === 0) {
            return;
        }

        const data = {
            site_key: window.ANALYTICS_SITE_KEY,
            device_type: detectDeviceType(),
            country_code: getCountryCode(),
            url: window.location.href,
            selectors: selectors
        };

        // Get base URL and construct ads API URL
        const baseApiUrl = window.ANALYTICS_API_URL || '/api/analytics/track';
        // Extract base path - if it contains /api, use everything before /api + /api, otherwise use /api
        let basePath = '/api';
        if (baseApiUrl.includes('/api')) {
            const parts = baseApiUrl.split('/api');
            basePath = parts[0] + '/api';
        }
        const adsApiUrl = basePath + '/ads/get';

        fetch(adsApiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(result) {
            if (result.ads && result.ads.length > 0) {
                result.ads.forEach(function(ad) {
                    injectAd(ad);
                });
            }
        })
        .catch(function(error) {
            // Silently fail - don't block page rendering
            if (window.console && console.error) {
                console.error('Analytics ads error:', error);
            }
        });
    }

    // Find selectors in page
    function findSelectorsInPage() {
        const selectors = [];
        
        // Search for predefined selector tags (data attributes)
        document.querySelectorAll('[data-ad-selector]').forEach(function(el) {
            const selector = el.getAttribute('data-ad-selector');
            if (selector && selectors.indexOf(selector) === -1) {
                selectors.push(selector);
            }
        });

        // Also search for common selectors that might be in the page
        // This allows sites to use standard selectors without data attributes
        const commonSelectors = ['header', 'footer', '.sidebar', 'aside', '[data-sidebar]', 'main', '.content'];
        commonSelectors.forEach(function(selector) {
            if (document.querySelector(selector) && selectors.indexOf(selector) === -1) {
                selectors.push(selector);
            }
        });

        return selectors;
    }

    // Detect device type
    function detectDeviceType() {
        const width = window.innerWidth || screen.width;
        if (width < 768) {
            return 'mobile';
        } else if (width < 1024) {
            return 'tablet';
        }
        return 'desktop';
    }

    // Get country code (from cookie or session)
    function getCountryCode() {
        // Try to get from cookie
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            const cookie = cookies[i].trim();
            if (cookie.indexOf('country_code=') === 0) {
                return cookie.substring('country_code='.length);
            }
        }
        return null;
    }

    // Close ad popup function
    function closeAdPopup(btn) {
        const adContainer = btn.closest('.analytics-ad-pop-from-bottom, .analytics-ad-pop-from-top, .analytics-ad-interstitial');
        if (adContainer) {
            const adType = adContainer.classList.contains('analytics-ad-interstitial') ? 'Interstitial' : 
                          adContainer.classList.contains('analytics-ad-pop-from-bottom') ? 'pop_from_bottom' : 'pop_from_top';
            
            // Animate out
            if (adType === 'Interstitial') {
                adContainer.style.opacity = '0';
                setTimeout(function() {
                    adContainer.remove();
                }, 300);
            } else {
                adContainer.style.transform = adType === 'pop_from_bottom' ? 'translateY(100%)' : 'translateY(-100%)';
                setTimeout(function() {
                    adContainer.remove();
                }, 300);
            }
        }
    }

    // Inject ad into page
    function injectAd(ad) {
        if (!ad.content) {
            return;
        }

        try {
            // Special ad types (pop_from_bottom, pop_from_top, Interstitial) don't need selectors
            const specialTypes = ['pop_from_bottom', 'pop_from_top', 'Interstitial'];
            if (specialTypes.includes(ad.type)) {
                // Check interval period for Interstitial ads
                if (ad.type === 'Interstitial' && ad.interval_period) {
                    const lastShown = localStorage.getItem('analytics_ad_interstitial_' + ad.id);
                    if (lastShown) {
                        const timeSinceLastShown = (Date.now() - parseInt(lastShown)) / 1000;
                        if (timeSinceLastShown < parseInt(ad.interval_period)) {
                            return; // Don't show yet
                        }
                    }
                    // Store current time
                    localStorage.setItem('analytics_ad_interstitial_' + ad.id, Date.now().toString());
                }

                // Inject special ad types directly into body
                const adElement = document.createElement('div');
                adElement.innerHTML = ad.content;
                document.body.appendChild(adElement);

                // Animate in
                setTimeout(function() {
                    const adContainer = adElement.querySelector('.analytics-ad-pop-from-bottom, .analytics-ad-pop-from-top, .analytics-ad-interstitial');
                    if (adContainer) {
                        if (ad.type === 'Interstitial') {
                            adContainer.style.opacity = '1';
                        } else {
                            adContainer.style.transform = 'translateY(0)';
                        }
                    }
                }, 10);

                // Track impression
                trackAdImpression(ad.id, ad.type, ad.url_pattern_id);

                // Track click if ad has URL
                if (ad.url) {
                    const adLinks = adElement.querySelectorAll('a.ad-link, a[href]');
                    adLinks.forEach(function(link) {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            trackAdClick(ad.id, ad.url, ad.type, ad.url_pattern_id);
                            window.open(ad.url, '_blank');
                        });
                    });
                }

                // Add click tracking to close buttons
                const closeButtons = adElement.querySelectorAll('.analytics-ad-close');
                closeButtons.forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        closeAdPopup(btn);
                    });
                });

                // Auto-close Interstitial after 10 seconds if no interval
                if (ad.type === 'Interstitial' && !ad.interval_period) {
                    setTimeout(function() {
                        const closeBtn = adElement.querySelector('.analytics-ad-close');
                        if (closeBtn) {
                            closeAdPopup(closeBtn);
                        }
                    }, 10000);
                }

                return;
            }

            // Regular in_content type needs selector
            if (ad.type !== 'in_content' || !ad.selector) {
                return;
            }

            const elements = document.querySelectorAll(ad.selector);
            if (elements.length === 0) {
                return;
            }

            elements.forEach(function(el) {
                // Create ad container
                const adElement = document.createElement('div');
                adElement.className = 'analytics-ad';
                adElement.setAttribute('data-ad-id', ad.id);
                adElement.innerHTML = ad.content;

                // Insert ad
                el.appendChild(adElement);

                // Track impression
                trackAdImpression(ad.id, ad.selector, ad.url_pattern_id);

                // Track click if ad has URL
                if (ad.url) {
                    const adLinks = adElement.querySelectorAll('a.ad-link, a[href]');
                    adLinks.forEach(function(link) {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            trackAdClick(ad.id, ad.url, ad.selector, ad.url_pattern_id);
                            window.open(ad.url, '_blank');
                        });
                    });
                }
            });
        } catch (error) {
            if (window.console && console.error) {
                console.error('Error injecting ad:', error);
            }
        }
    }

    // Track ad impression
    function trackAdImpression(adId, selector, urlPatternId) {
        if (!window.ANALYTICS_SITE_KEY) {
            return;
        }

        const data = {
            site_key: window.ANALYTICS_SITE_KEY,
            ad_id: adId,
            session_id: sessionId,
            url: window.location.href,
            selector: selector,
            url_pattern_id: urlPatternId || null
        };

        const baseApiUrl = window.ANALYTICS_API_URL || '/api/analytics/track';
        let basePath = '/api';
        if (baseApiUrl.includes('/api')) {
            const parts = baseApiUrl.split('/api');
            basePath = parts[0] + '/api';
        }
        const impressionUrl = basePath + '/ads/impression';

        fetch(impressionUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
            keepalive: true
        }).catch(function(error) {
            // Silently fail
        });
    }

    // Track ad click
    function trackAdClick(adId, targetUrl, selector, urlPatternId) {
        if (!window.ANALYTICS_SITE_KEY) {
            return;
        }

        const data = {
            site_key: window.ANALYTICS_SITE_KEY,
            ad_id: adId,
            session_id: sessionId,
            url: window.location.href,
            selector: selector,
            url_pattern_id: urlPatternId || null
        };

        const baseApiUrl = window.ANALYTICS_API_URL || '/api/analytics/track';
        const basePath = baseApiUrl.includes('/api') ? baseApiUrl.split('/api')[0] + '/api' : '/api';
        const clickUrl = basePath + '/ads/click';

        fetch(clickUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
            keepalive: true
        }).then(function() {
            // After tracking click, navigate to target URL
            if (targetUrl) {
                window.location.href = targetUrl;
            }
        }).catch(function(error) {
            // Even if tracking fails, navigate to target URL
            if (targetUrl) {
                window.location.href = targetUrl;
            }
        });
    }

    // Expose API
    window.Analytics = {
        track: trackPageView,
        getSessionId: function() { return sessionId; },
        loadAds: loadAds,
    };
})();

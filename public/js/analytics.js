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
                    // Special ad types don't need selectors
                    const specialTypes = ['pop_from_bottom', 'pop_from_top', 'Interstitial'];
                    if (specialTypes.includes(ad.type)) {
                        injectAd(ad);
                    } else if (ad.selector && document.querySelector(ad.selector)) {
                        // Regular ads need selectors
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

    // Toggle ad collapse/expand function (for pop_from_bottom and pop_from_top only)
    function toggleAdCollapse(btn) {
        const adContainer = btn.closest('.analytics-ad-pop-from-bottom, .analytics-ad-pop-from-top');
        if (!adContainer) return;
        
        const adId = adContainer.getAttribute('data-ad-id');
        const isCollapsed = adContainer.classList.contains('analytics-ad-collapsed');
        const isBottom = adContainer.classList.contains('analytics-ad-pop-from-bottom');
        
        if (isCollapsed) {
            // Expand
            adContainer.classList.remove('analytics-ad-collapsed');
            adContainer.style.transform = 'translateY(0)';
            sessionStorage.removeItem('analytics_ad_collapsed_' + adId);
            btn.innerHTML = isBottom ? '▼' : '▲';
        } else {
            // Collapse - show only 50px (toggle button height + margin)
            adContainer.classList.add('analytics-ad-collapsed');
            const offset = 50; // Height of toggle button area
            adContainer.style.transform = isBottom ? 'translateY(calc(100% - ' + offset + 'px))' : 'translateY(calc(-100% + ' + offset + 'px))';
            sessionStorage.setItem('analytics_ad_collapsed_' + adId, 'true');
            btn.innerHTML = isBottom ? '▲' : '▼';
        }
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

    // Create pop-up ad structure (standalone - works without server HTML)
    function createPopupAdStructure(ad) {
        const paddingX = ad.padding_x || 20;
        const paddingY = ad.padding_y || 20;
        const intervalPeriod = ad.interval_period || null;
        const adContent = ad.content || '';
        
        let containerClass = '';
        let transformValue = '';
        let positionStyles = {};
        
        if (ad.type === 'pop_from_bottom') {
            containerClass = 'analytics-ad-pop-from-bottom';
            transformValue = 'translateY(100%)';
            positionStyles = {
                position: 'fixed',
                bottom: '0',
                left: '0',
                right: '0',
                zIndex: '9999',
                background: '#ffffff',
                padding: '0',
                maxWidth: '100%',
                width: '100%',
                boxShadow: '0 -2px 10px rgba(0,0,0,0.3)',
                transform: transformValue,
                transition: 'transform 0.3s ease-in-out'
            };
        } else if (ad.type === 'pop_from_top') {
            containerClass = 'analytics-ad-pop-from-top';
            transformValue = 'translateY(-100%)';
            positionStyles = {
                position: 'fixed',
                top: '0',
                left: '0',
                right: '0',
                zIndex: '9999',
                background: '#ffffff',
                padding: '0',
                maxWidth: '100%',
                width: '100%',
                boxShadow: '0 2px 10px rgba(0,0,0,0.3)',
                transform: transformValue,
                transition: 'transform 0.3s ease-in-out'
            };
        } else if (ad.type === 'Interstitial') {
            containerClass = 'analytics-ad-interstitial';
            positionStyles = {
                position: 'fixed',
                top: '0',
                left: '0',
                right: '0',
                bottom: '0',
                zIndex: '99999',
                background: 'rgba(0,0,0,0.5)',
                backdropFilter: 'blur(10px)',
                WebkitBackdropFilter: 'blur(10px)',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                padding: '20px',
                opacity: '0',
                transition: 'opacity 0.3s ease-in-out'
            };
        } else {
            return null;
        }
        
        // Create container
        const container = document.createElement('div');
        container.className = containerClass;
        container.setAttribute('data-ad-id', ad.id);
        if (intervalPeriod) {
            container.setAttribute('data-interval', intervalPeriod);
        }
        
        // Apply styles
        Object.keys(positionStyles).forEach(function(key) {
            container.style[key] = positionStyles[key];
        });
        
        // Create inner wrapper
        const wrapper = document.createElement('div');
        if (ad.type === 'Interstitial') {
            wrapper.style.cssText = 'position: relative; width: 350px; max-width: 90%; max-height: 90%; overflow: auto; background: #fff; border-radius: 8px; padding: ' + paddingY + 'px ' + paddingX + 'px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
        } else {
            // For pop_from_bottom and pop_from_top: full width outer wrapper with centered inner container
            wrapper.className = 'analytics-ad-wrapper';
            wrapper.style.cssText = 'width: 100% !important; padding: ' + paddingY + 'px ' + paddingX + 'px; display: flex; align-items: center; justify-content: center; transition: opacity 0.3s ease-in-out;';
        }
        
        // Create centered container for pop_from_bottom and pop_from_top
        let innerContainer = null;
        if (ad.type === 'pop_from_bottom' || ad.type === 'pop_from_top') {
            innerContainer = document.createElement('div');
            innerContainer.style.cssText = 'width: 100% !important; max-width: 1000px; margin: 0 auto; position: relative; display: flex; align-items: center; justify-content: center;';
        }
        
        // Create content div
        const contentDiv = document.createElement('div');
        if (ad.type === 'Interstitial') {
            contentDiv.innerHTML = adContent;
        } else if (ad.type === 'pop_from_bottom' || ad.type === 'pop_from_top') {
            contentDiv.style.cssText = 'flex: 1; text-align: center; display: flex; align-items: center; justify-content: center;';
            contentDiv.innerHTML = adContent;
        } else {
            contentDiv.style.cssText = 'flex: 1;';
            contentDiv.innerHTML = adContent;
        }
        
        // Create toggle button (for pop_from_bottom and pop_from_top only)
        let toggleBtn = null;
        if (ad.type === 'pop_from_bottom' || ad.type === 'pop_from_top') {
            toggleBtn = document.createElement('button');
            toggleBtn.className = 'analytics-ad-toggle';
            toggleBtn.innerHTML = ad.type === 'pop_from_bottom' ? '▼' : '▲';
            toggleBtn.setAttribute('onclick', 'toggleAdCollapse(this)');
            // Position toggle button on the edge of the container
            toggleBtn.style.cssText = 'position: absolute; ' + 
                (ad.type === 'pop_from_bottom' ? 'top: -35px;' : 'bottom: -35px;') + 
                'left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.7); border: none; ' +
                (ad.type === 'pop_from_bottom' ? 'border-radius: 4px 4px 0 0;' : 'border-radius: 0 0 4px 4px;') +
                'width: 60px; height: 30px; cursor: pointer; color: #fff; font-size: 16px; line-height: 1; z-index: 10000; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease-in-out;';
            
            // Add styles for collapsed state - button moves to edge
            if (!document.getElementById('analytics-ad-toggle-styles')) {
                const style = document.createElement('style');
                style.id = 'analytics-ad-toggle-styles';
                style.textContent = `
                    .analytics-ad-pop-from-bottom.analytics-ad-collapsed .analytics-ad-toggle {
                        top: auto !important;
                        bottom: 10px !important;
                        left: 50% !important;
                        transform: translateX(-50%) !important;
                        border-radius: 4px 4px 0 0 !important;
                    }
                    .analytics-ad-pop-from-top.analytics-ad-collapsed .analytics-ad-toggle {
                        bottom: auto !important;
                        top: 10px !important;
                        left: 50% !important;
                        transform: translateX(-50%) !important;
                        border-radius: 0 0 4px 4px !important;
                    }
                    .analytics-ad-pop-from-bottom.analytics-ad-collapsed .analytics-ad-wrapper,
                    .analytics-ad-pop-from-top.analytics-ad-collapsed .analytics-ad-wrapper {
                        opacity: 0;
                        pointer-events: none;
                    }
                `;
                document.head.appendChild(style);
            }
        }
        
        // Create close button
        const closeBtn = document.createElement('button');
        closeBtn.className = 'analytics-ad-close';
        closeBtn.innerHTML = '×';
        closeBtn.setAttribute('onclick', 'closeAdPopup(this)');
        
        if (ad.type === 'Interstitial') {
            closeBtn.style.cssText = 'position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.5); border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; color: #fff; font-size: 28px; font-weight: bold; z-index: 100000; display: flex; align-items: center; justify-content: center; padding: 0; margin: 0; line-height: 1;';
        } else if (ad.type === 'pop_from_bottom' || ad.type === 'pop_from_top') {
            closeBtn.style.cssText = 'position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.5); border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; color: #fff; font-size: 20px; line-height: 1; z-index: 10001;';
        } else {
            closeBtn.style.cssText = 'background: rgba(255,255,255,0.2); border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; color: #fff; font-size: 20px; line-height: 1; margin-left: 15px; flex-shrink: 0;';
        }
        
        // Assemble structure
        if (ad.type === 'Interstitial') {
            wrapper.appendChild(closeBtn);
            wrapper.appendChild(contentDiv);
            container.appendChild(wrapper);
        } else if (ad.type === 'pop_from_bottom' || ad.type === 'pop_from_top') {
            innerContainer.appendChild(contentDiv);
            innerContainer.appendChild(closeBtn);
            wrapper.appendChild(innerContainer);
            container.appendChild(wrapper);
            container.appendChild(toggleBtn);
        } else {
            wrapper.appendChild(contentDiv);
            wrapper.appendChild(closeBtn);
            container.appendChild(wrapper);
        }
        
        return container;
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

                // Check if content already has the proper structure
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = ad.content;
                const existingContainer = tempDiv.querySelector('.analytics-ad-pop-from-bottom, .analytics-ad-pop-from-top, .analytics-ad-interstitial');
                
                let adContainer;
                if (existingContainer) {
                    // Server provided the structure, use it
                    adContainer = existingContainer;
                    // Ensure it's not already in the DOM
                    if (adContainer.parentNode) {
                        adContainer = adContainer.cloneNode(true);
                    }
                } else {
                    // Create structure standalone in JavaScript
                    adContainer = createPopupAdStructure(ad);
                }
                
                if (!adContainer) {
                    return;
                }

                // Check sessionStorage for collapsed state (pop_from_bottom and pop_from_top only)
                const wasCollapsed = (ad.type === 'pop_from_bottom' || ad.type === 'pop_from_top') && 
                                     sessionStorage.getItem('analytics_ad_collapsed_' + ad.id) === 'true';
                
                if (wasCollapsed) {
                    // Start in collapsed state - show only 50px (toggle button area)
                    adContainer.classList.add('analytics-ad-collapsed');
                    const offset = 50;
                    adContainer.style.transform = ad.type === 'pop_from_bottom' ? 'translateY(calc(100% - ' + offset + 'px))' : 'translateY(calc(-100% + ' + offset + 'px))';
                    const toggleBtn = adContainer.querySelector('.analytics-ad-toggle');
                    if (toggleBtn) {
                        toggleBtn.innerHTML = ad.type === 'pop_from_bottom' ? '▲' : '▼';
                    }
                }

                // Inject into body
                document.body.appendChild(adContainer);

                // Animate in (only if not collapsed)
                if (!wasCollapsed) {
                    setTimeout(function() {
                        if (ad.type === 'Interstitial') {
                            adContainer.style.opacity = '1';
                        } else {
                            adContainer.style.transform = 'translateY(0)';
                        }
                    }, 10);
                }

                // Track impression
                trackAdImpression(ad.id, ad.type, ad.url_pattern_id);

                // Track click if ad has URL
                if (ad.url) {
                    const adLinks = adContainer.querySelectorAll('a.ad-link, a[href]');
                    adLinks.forEach(function(link) {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            trackAdClick(ad.id, ad.url, ad.type, ad.url_pattern_id);
                            window.open(ad.url, '_blank');
                        });
                    });
                }

                // Add click tracking to close buttons
                const closeButtons = adContainer.querySelectorAll('.analytics-ad-close');
                closeButtons.forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        closeAdPopup(btn);
                    });
                });

                // Auto-close Interstitial after 10 seconds if no interval
                if (ad.type === 'Interstitial' && !ad.interval_period) {
                    setTimeout(function() {
                        const closeBtn = adContainer.querySelector('.analytics-ad-close');
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
    
    // Expose closeAdPopup and toggleAdCollapse globally for onclick handlers
    window.closeAdPopup = closeAdPopup;
    window.toggleAdCollapse = toggleAdCollapse;
})();

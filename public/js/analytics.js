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
        
        // No iframe message listener needed - using direct content injection
        
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

    // Close Interstitial ad permanently (no toggle, just close)
    function closeInterstitialAd(btn) {
        const adContainer = btn.closest('.analytics-ad-interstitial');
        if (!adContainer) return;
        
        // Close Interstitial permanently - remove from DOM
        adContainer.style.opacity = '0';
        adContainer.style.transition = 'opacity 0.3s ease-out';
        setTimeout(function() {
            if (adContainer && adContainer.parentNode) {
                adContainer.remove();
            }
        }, 300);
    }
    
    // Toggle ad collapse/expand function - for pop_from_bottom and pop_from_top only
    function toggleAdCollapse(btn) {
        const adContainer = btn.closest('.analytics-ad-pop-from-bottom, .analytics-ad-pop-from-top');
        if (!adContainer) return;
        
        const adId = adContainer.getAttribute('data-ad-id');
        const isCollapsed = adContainer.classList.contains('analytics-ad-collapsed');
        const isBottom = adContainer.classList.contains('analytics-ad-pop-from-bottom');
        
        // For pop_from_bottom and pop_from_top: toggle behavior (can show/hide)
        if (isCollapsed) {
            // Expand - show full ad
            adContainer.classList.remove('analytics-ad-collapsed');
            adContainer.style.transform = 'translateY(0)';
            // Change icon to collapse direction
            btn.innerHTML = isBottom ? '▼' : '▲';
            sessionStorage.removeItem('analytics_ad_collapsed_' + adId);
        } else {
            // Collapse - hide ad but keep toggle button visible at same position
            adContainer.classList.add('analytics-ad-collapsed');
            const offset = 52; // Height of toggle button + margin (32px + 20px)
            adContainer.style.transform = isBottom ? 'translateY(calc(100% - ' + offset + 'px))' : 'translateY(calc(-100% + ' + offset + 'px))';
            // Change icon to expand direction (opposite)
            btn.innerHTML = isBottom ? '▲' : '▼';
            sessionStorage.setItem('analytics_ad_collapsed_' + adId, 'true');
        }
    }

    // Detect if background is light or dark and return appropriate text color
    function getTextColorForBackground(backgroundColor) {
        if (!backgroundColor) return '#000000';
        
        // Remove rgba/rgb and extract values
        const rgbaMatch = backgroundColor.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*[\d.]+)?\)/);
        if (rgbaMatch) {
            const r = parseInt(rgbaMatch[1]);
            const g = parseInt(rgbaMatch[2]);
            const b = parseInt(rgbaMatch[3]);
            const a = rgbaMatch[4] ? parseFloat(rgbaMatch[4]) : 1;
            
            // If transparent, assume white background
            if (a < 0.5) {
                return '#000000';
            }
            
            // Calculate luminance
            const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
            return luminance > 0.5 ? '#000000' : '#ffffff';
        }
        
        // Check for hex colors
        if (backgroundColor.startsWith('#')) {
            const hex = backgroundColor.replace('#', '');
            const r = parseInt(hex.substr(0, 2), 16);
            const g = parseInt(hex.substr(2, 2), 16);
            const b = parseInt(hex.substr(4, 2), 16);
            const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
            return luminance > 0.5 ? '#000000' : '#ffffff';
        }
        
        // Default: white background = black text
        const lowerBg = backgroundColor.toLowerCase();
        if (lowerBg.includes('white') || lowerBg === '#fff' || lowerBg === '#ffffff' || lowerBg === 'transparent') {
            return '#000000';
        }
        if (lowerBg.includes('black') || lowerBg === '#000' || lowerBg === '#000000') {
            return '#ffffff';
        }
        
        return '#000000'; // Default
    }

    // Create isolated ad content with CSS isolation (no iframe)
    function createIsolatedAdContent(adHtml, adId, adType) {
        // Create wrapper div with CSS isolation
        const contentWrapper = document.createElement('div');
        contentWrapper.className = 'analytics-ad-content-wrapper';
        contentWrapper.setAttribute('data-ad-id', adId || '');
        
        // Determine text color based on background
        // For pop_from_bottom/top: white background = black text
        // For Interstitial: white background in wrapper = black text
        let textColor = '#000000'; // Default black text for white backgrounds
        
        // Add CSS isolation styles with dynamic text color
        const styleId = 'analytics-ad-isolation-styles';
        if (!document.getElementById(styleId)) {
            const style = document.createElement('style');
            style.id = styleId;
            style.textContent = `
                .analytics-ad-content-wrapper {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    flex-direction: column !important;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
                    background: transparent !important;
                    width: 100% !important;
                    height: auto !important;
                    box-sizing: border-box !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    overflow: visible !important;
                    position: relative !important;
                    float: none !important;
                    clear: both !important;
                }
                .analytics-ad-content-wrapper * {
                    box-sizing: border-box !important;
                }
                .analytics-ad-content-wrapper img {
                    max-width: 100% !important;
                    height: auto !important;
                    display: block !important;
                    margin: 0 auto !important;
                }
                .analytics-ad-content-wrapper a {
                    text-decoration: none !important;
                    color: inherit !important;
                    cursor: pointer !important;
                }
            `;
            document.head.appendChild(style);
        }
        
        // Set text color based on background
        contentWrapper.style.setProperty('color', textColor, 'important');
        
        // Insert content - but first remove any nested ad containers and scripts to prevent nested ads
        let cleanAdHtml = adHtml;
        
        // Only clean if there's actual content
        if (adHtml && adHtml.trim().length > 0) {
            // Remove nested ad containers and scripts from content
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = adHtml;
            
            // Remove any analytics ad containers, script tags, or ad-related elements from content
            // BUT: Only remove if they are nested inside other content, not if they ARE the content
            const nestedAds = tempDiv.querySelectorAll('.analytics-ad-interstitial, .analytics-ad-pop-from-bottom, .analytics-ad-pop-from-top, .analytics-ad-wrapper');
            
            // Check if the entire content is just an ad container (nested ad)
            const isWholeContentAnAd = tempDiv.querySelector('.analytics-ad-interstitial, .analytics-ad-pop-from-bottom, .analytics-ad-pop-from-top');
            if (isWholeContentAnAd && nestedAds.length === 1 && tempDiv.children.length === 1 && tempDiv.children[0] === isWholeContentAnAd) {
                // The entire content is an ad container - extract its inner content instead
                // Try to find the actual content inside the nested ad
                const innerWrapper = isWholeContentAnAd.querySelector('.analytics-ad-wrapper, .analytics-ad-interstitial-wrapper, .analytics-ad-content-wrapper, div[style*="position: relative"], div[style*="max-width"]');
                if (innerWrapper) {
                    cleanAdHtml = innerWrapper.innerHTML;
                } else {
                    // Try to find any div inside the ad container
                    const innerDiv = isWholeContentAnAd.querySelector('div');
                    if (innerDiv) {
                        cleanAdHtml = innerDiv.innerHTML;
                    } else {
                        // Just get the direct children content
                        cleanAdHtml = isWholeContentAnAd.innerHTML;
                    }
                }
            } else {
                // Remove nested ads but keep other content - only remove if nested
                nestedAds.forEach(function(nestedAd) {
                    // Only remove if it's nested inside other content, not if it's the root element
                    if (nestedAd.parentNode && nestedAd.parentNode !== tempDiv && nestedAd.parentNode.classList.contains('analytics-ad-content-wrapper') === false) {
                        if (nestedAd.parentNode) {
                            nestedAd.parentNode.removeChild(nestedAd);
                        }
                    }
                });
                cleanAdHtml = tempDiv.innerHTML;
            }
            
            // Also remove any analytics script tags from innerHTML
            cleanAdHtml = cleanAdHtml.replace(/<script[^>]*analytics[^>]*>[\s\S]*?<\/script>/gi, '');
            // Remove script tags that load analytics.js
            cleanAdHtml = cleanAdHtml.replace(/<script[^>]*src[^>]*analytics[^>]*>[\s\S]*?<\/script>/gi, '');
        }
        
        // Apply flexbox styles to wrapper for centering
        contentWrapper.style.setProperty('display', 'flex', 'important');
        contentWrapper.style.setProperty('align-items', 'center', 'important');
        contentWrapper.style.setProperty('justify-content', 'center', 'important');
        contentWrapper.style.setProperty('flex-direction', 'column', 'important'); // Allow content to stack vertically if needed
        
        contentWrapper.innerHTML = cleanAdHtml || '';
        
        // Force text color on all text elements inside after content is inserted
        // Apply text color to ensure visibility against background
        setTimeout(function() {
            // First, set text color on wrapper itself
            contentWrapper.style.setProperty('color', textColor, 'important');
            
            // Then apply to all text elements
            const textElements = contentWrapper.querySelectorAll('p, span, div, h1, h2, h3, h4, h5, h6, a, li, td, th, label, button, input, textarea, strong, b, em, i, small, sub, sup, blockquote, pre, code');
            textElements.forEach(function(el) {
                // Skip elements that are images, buttons with icons, or containers
                if (el.tagName === 'IMG' || el.tagName === 'SVG' || el.classList.contains('analytics-ad-toggle')) {
                    return;
                }
                
                // Remove analytics-ad-close buttons from Interstitial content (not needed)
                if (el.classList.contains('analytics-ad-close')) {
                    el.remove();
                    return;
                }
                
                // Skip if element is a container without direct text (has children with text)
                if (el.children.length > 0 && el.textContent.trim().length === 0) {
                    return;
                }
                
                const computedColor = window.getComputedStyle(el).color;
                const hasInlineColor = el.hasAttribute('style') && el.getAttribute('style').toLowerCase().includes('color:');
                
                // Always apply text color to ensure visibility
                // Only skip if element explicitly has a color that's not white/black/transparent
                const isWhite = computedColor === 'rgb(255, 255, 255)' || computedColor === 'rgba(255, 255, 255, 1)';
                const isTransparent = computedColor === 'rgba(0, 0, 0, 0)' || computedColor === 'transparent';
                const isBlack = computedColor === 'rgb(0, 0, 0)' || computedColor === 'rgba(0, 0, 0, 1)';
                
                // Apply text color if:
                // - No inline color style, OR
                // - Color is white/transparent on white background (should be black), OR
                // - Color is black on dark background (should be white), OR
                // - Just apply it anyway for consistency (textColor is appropriate for background)
                if (!hasInlineColor || isWhite || isTransparent || (textColor === '#ffffff' && isBlack)) {
                    el.style.setProperty('color', textColor, 'important');
                }
            });
        }, 100);
        
        return contentWrapper;
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
                boxShadow: '0 -4px 20px rgba(0,0,0,0.15)',
                transform: transformValue,
                transition: 'transform 0.4s cubic-bezier(0.4, 0, 0.2, 1)'
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
                boxShadow: '0 4px 20px rgba(0,0,0,0.15)',
                transform: transformValue,
                transition: 'transform 0.4s cubic-bezier(0.4, 0, 0.2, 1)'
            };
        } else if (ad.type === 'Interstitial') {
            containerClass = 'analytics-ad-interstitial';
            positionStyles = {
                position: 'fixed',
                top: '0',
                left: '0',
                right: '0',
                bottom: '0',
                zIndex: '99999999999990000',
                background: 'rgb(0 0 0 / 68%)',
                backdropFilter: 'blur(10px)',
                WebkitBackdropFilter: 'blur(10px)',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                padding: '0',
                opacity: '1',
                transition: 'none'
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
        
        // Apply styles - force background color to ensure no old styles persist
        Object.keys(positionStyles).forEach(function(key) {
            container.style[key] = positionStyles[key];
        });
        
        // Force correct background colors (prevent any CSS override from page)
        if (ad.type === 'pop_from_bottom' || ad.type === 'pop_from_top') {
            container.style.setProperty('background', '#ffffff', 'important');
        } else if (ad.type === 'Interstitial') {
            // Force white background and visibility immediately - prevent any external CSS override
            container.style.setProperty('background', 'rgb(0 0 0 / 68%)', 'important');
            container.style.setProperty('backdrop-filter', 'blur(10px)', 'important');
            container.style.setProperty('-webkit-backdrop-filter', 'blur(10px)', 'important');
            container.style.setProperty('opacity', '1', 'important');
            container.style.setProperty('display', 'flex', 'important');
            container.style.setProperty('visibility', 'visible', 'important');
            container.style.setProperty('pointer-events', 'auto', 'important');
            // Remove any inline styles that might override
            container.style.removeProperty('color');
            // Force all styles to override external CSS
            container.setAttribute('style', container.getAttribute('style') + '; background: rgb(0 0 0 / 68%) !important; opacity: 1 !important; display: flex !important;');
        }
        
        // Create inner wrapper - clean and modern padding
        const wrapper = document.createElement('div');
        if (ad.type === 'Interstitial') {
            wrapper.className = 'analytics-ad-interstitial-wrapper';
            // Modern, clean padding - only use padding from dashboard (padding_x, padding_y)
            // No extra padding in container or contentDiv
            wrapper.style.cssText = 'position: relative !important; width: 550px !important; max-width: 90% !important; max-height: 90vh !important; overflow-y: auto !important; overflow-x: hidden !important; background: #fff !important; border-radius: 8px !important; padding: ' + paddingY + 'px ' + paddingX + 'px !important; box-shadow: 0 4px 20px rgba(0,0,0,0.15) !important; transform: scale(0.9) !important; opacity: 0 !important; transition: transform 0.3s ease-out, opacity 0.3s ease-out !important;';
            // Also set properties individually to ensure they're applied
            wrapper.style.setProperty('position', 'relative', 'important');
            wrapper.style.setProperty('width', '550px', 'important');
            wrapper.style.setProperty('max-width', '90%', 'important');
            wrapper.style.setProperty('background', '#fff', 'important');
        } else {
            // For pop_from_bottom and pop_from_top: simple centered container with clean padding
            wrapper.className = 'analytics-ad-wrapper';
            // Use only dashboard padding - no extra padding
            wrapper.style.cssText = 'width: 100% !important; max-width: 1000px !important; margin: 0 auto !important; padding: ' + paddingY + 'px ' + paddingX + 'px !important; position: relative !important;';
        }
        
        // Create content div with isolated content (no iframe)
        const contentDiv = document.createElement('div');
        const isolatedContent = createIsolatedAdContent(adContent, ad.id, ad.type);
        
        if (ad.type === 'Interstitial') {
            // For Interstitial: content goes directly in wrapper - no extra padding
            contentDiv.style.cssText = 'width: 100% !important; min-height: 50px !important; position: relative !important; overflow: visible !important; padding: 0 !important; margin: 0 !important;';
            isolatedContent.style.setProperty('display', 'block', 'important');
            isolatedContent.style.setProperty('width', '100%', 'important');
            isolatedContent.style.setProperty('height', 'auto', 'important');
            isolatedContent.style.setProperty('padding', '0', 'important');
            isolatedContent.style.setProperty('margin', '0', 'important');
            contentDiv.appendChild(isolatedContent);
            wrapper.appendChild(contentDiv);
        } else if (ad.type === 'pop_from_bottom' || ad.type === 'pop_from_top') {
            // Simple centered content layout - ensure proper height, no extra padding
            contentDiv.style.cssText = 'width: 100% !important; display: block !important; position: relative !important; padding: 0 !important; margin: 0 !important;';
            isolatedContent.style.setProperty('display', 'block', 'important');
            isolatedContent.style.setProperty('width', '100%', 'important');
            isolatedContent.style.setProperty('height', 'auto', 'important');
            isolatedContent.style.setProperty('min-height', 'auto', 'important');
            isolatedContent.style.setProperty('margin', '0 auto', 'important');
            isolatedContent.style.setProperty('padding', '0', 'important');
            contentDiv.appendChild(isolatedContent);
            wrapper.appendChild(contentDiv);
        } else {
            contentDiv.style.cssText = 'width: 100%; display: block; padding: 0; margin: 0;';
            contentDiv.appendChild(isolatedContent);
        }
        
        // Create close button for Interstitial (permanent close, no toggle)
        let closeBtn = null;
        if (ad.type === 'Interstitial') {
            closeBtn = document.createElement('button');
            closeBtn.className = 'analytics-ad-close-interstitial';
            closeBtn.setAttribute('onclick', 'closeInterstitialAd(this)');
            closeBtn.setAttribute('type', 'button');
            closeBtn.setAttribute('aria-label', 'Close ad');
            closeBtn.innerHTML = '✕';
            // Position close button in the corner of the wrapper (content frame) - gray background with white text
            closeBtn.style.cssText = 'position: absolute; top: ' + (paddingY > 10 ? paddingY / 2 : 10) + 'px; right: ' + (paddingX > 10 ? paddingX / 2 : 10) + 'px; background: rgb(79 79 79) !important; border: 2px solid rgba(0,0,0,0.1); border-radius: 50%; width: 32px; height: 32px; cursor: pointer; color: rgb(255 255 255); font-size: 18px; font-weight: normal; z-index: 1000000; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; font-family: arial !important; line-height: 1; padding: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1);';
            closeBtn.onmouseover = function() { this.style.setProperty('background', 'rgb(99 99 99)', 'important'); this.style.setProperty('border-color', 'rgba(0,0,0,0.2)', 'important'); this.style.setProperty('transform', 'scale(1.1)', 'important'); };
            closeBtn.onmouseout = function() { this.style.setProperty('background', 'rgb(79 79 79)', 'important'); this.style.setProperty('border-color', 'rgba(0,0,0,0.1)', 'important'); this.style.setProperty('transform', 'scale(1)', 'important'); };
        }
        
        // Create toggle button for pop_from_bottom and pop_from_top only
        let toggleBtn = null;
        if (ad.type === 'pop_from_bottom' || ad.type === 'pop_from_top') {
            toggleBtn = document.createElement('button');
            toggleBtn.className = 'analytics-ad-toggle';
            toggleBtn.setAttribute('onclick', 'toggleAdCollapse(this)');
            toggleBtn.setAttribute('type', 'button');
            toggleBtn.setAttribute('aria-label', 'Toggle ad');
            
            // For pop_from_bottom and pop_from_top: toggle button at top-left (bottom) or bottom-left (top) - white background
            toggleBtn.innerHTML = ad.type === 'pop_from_bottom' ? '▼' : '▲';
            const toggleTop = ad.type === 'pop_from_bottom' ? (paddingY > 10 ? paddingY / 2 : 10) : 'auto';
            const toggleBottom = ad.type === 'pop_from_top' ? (paddingY > 10 ? paddingY / 2 : 10) : 'auto';
            toggleBtn.style.cssText = 'position: absolute; ' + 
                (ad.type === 'pop_from_bottom' ? 'top: ' + toggleTop + 'px;' : 'bottom: ' + toggleBottom + 'px;') + 
                'left: ' + (paddingX > 10 ? paddingX / 2 : 10) + 'px; background: #fff; border: 2px solid rgba(0,0,0,0.1); border-radius: 50%; ' +
                'width: 32px; height: 32px; cursor: pointer; color: #333; font-size: 16px; font-weight: normal; z-index: 100001; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; font-family: arial !important; box-shadow: 0 2px 8px rgba(0,0,0,0.1); pointer-events: auto !important;';
            toggleBtn.setAttribute('style', toggleBtn.style.cssText); // Ensure styles are applied
            toggleBtn.onmouseover = function() { this.style.setProperty('background', '#f5f5f5', 'important'); this.style.setProperty('border-color', 'rgba(0,0,0,0.2)', 'important'); this.style.setProperty('transform', 'scale(1.1)', 'important'); };
            toggleBtn.onmouseout = function() { this.style.setProperty('background', '#fff', 'important'); this.style.setProperty('border-color', 'rgba(0,0,0,0.1)', 'important'); this.style.setProperty('transform', 'scale(1)', 'important'); };
            
            // Add styles for collapsed state and ensure Interstitial visibility
            if (!document.getElementById('analytics-ad-toggle-styles')) {
                const style = document.createElement('style');
                style.id = 'analytics-ad-toggle-styles';
                style.textContent = `
                    .analytics-ad-interstitial {
                        opacity: 1 !important;
                        display: flex !important;
                        visibility: visible !important;
                        pointer-events: auto !important;
                        background: rgb(0 0 0 / 68%) !important;
                        backdrop-filter: blur(10px) !important;
                        -webkit-backdrop-filter: blur(10px) !important;
                        z-index: 99999999999990000 !important;
                    }
                    .analytics-ad-interstitial-wrapper {
                        opacity: 1 !important;
                        display: block !important;
                        visibility: visible !important;
                        pointer-events: auto !important;
                        background: #fff !important;
                    }
                    .analytics-ad-toggle {
                        font-family: arial !important;
                        outline: none !important;
                        user-select: none !important;
                        -webkit-user-select: none !important;
                        pointer-events: auto !important;
                        cursor: pointer !important;
                        z-index: 100001 !important;
                    }
                    .analytics-ad-toggle:hover {
                        background: #f5f5f5 !important;
                        border-color: rgba(0,0,0,0.2) !important;
                    }
                    .analytics-ad-toggle:active {
                        transform: scale(0.95) !important;
                    }
                    .analytics-ad-pop-from-bottom .analytics-ad-toggle,
                    .analytics-ad-pop-from-top .analytics-ad-toggle {
                        pointer-events: auto !important;
                        z-index: 100001 !important;
                    }
                    .analytics-ad-pop-from-bottom.analytics-ad-collapsed .analytics-ad-toggle {
                        top: 10px !important;
                        left: 10px !important;
                        transform: scale(1) !important;
                        border-radius: 50% !important;
                        background: #fff !important;
                        border: 2px solid rgba(0,0,0,0.1) !important;
                        color: #333 !important;
                    }
                    .analytics-ad-pop-from-top.analytics-ad-collapsed .analytics-ad-toggle {
                        bottom: 10px !important;
                        left: 10px !important;
                        transform: scale(1) !important;
                        border-radius: 50% !important;
                        background: #fff !important;
                        border: 2px solid rgba(0,0,0,0.1) !important;
                        color: #333 !important;
                    }
                    .analytics-ad-pop-from-bottom.analytics-ad-collapsed .analytics-ad-wrapper,
                    .analytics-ad-pop-from-top.analytics-ad-collapsed .analytics-ad-wrapper {
                        opacity: 0 !important;
                        pointer-events: none !important;
                        height: 0 !important;
                        overflow: hidden !important;
                    }
                    .analytics-ad-interstitial {
                        opacity: 1 !important;
                        display: flex !important;
                        visibility: visible !important;
                        pointer-events: auto !important;
                        background: rgb(0 0 0 / 68%) !important;
                        backdrop-filter: blur(10px) !important;
                        -webkit-backdrop-filter: blur(10px) !important;
                        z-index: 99999999999990000 !important;
                    }
                    .analytics-ad-interstitial-wrapper {
                        opacity: 1 !important;
                        display: block !important;
                        visibility: visible !important;
                        pointer-events: auto !important;
                        background: #fff !important;
                    }
                    .analytics-ad-close-interstitial {
                        font-family: arial !important;
                        outline: none !important;
                        user-select: none !important;
                        -webkit-user-select: none !important;
                        z-index: 1000000 !important;
                        background: rgb(79 79 79) !important;
                        border: 2px solid rgba(0,0,0,0.1) !important;
                        color: rgb(255 255 255) !important;
                    }
                    .analytics-ad-close-interstitial:hover {
                        background: #f5f5f5 !important;
                        border-color: rgba(0,0,0,0.2) !important;
                    }
                    .analytics-ad-close-interstitial:active {
                        transform: scale(0.95) !important;
                    }
                `;
                document.head.appendChild(style);
            }
        }
        
        // Assemble structure - simple and clean
        if (ad.type === 'Interstitial') {
            // For Interstitial: close button goes inside wrapper (in the corner of the content frame)
            wrapper.appendChild(contentDiv);
            if (closeBtn) {
                wrapper.appendChild(closeBtn); // Close button inside wrapper, in the corner
            }
            container.appendChild(wrapper);
        } else if (ad.type === 'pop_from_bottom' || ad.type === 'pop_from_top') {
            // Simple structure: container -> wrapper -> contentDiv -> toggle button inside wrapper
            if (toggleBtn) {
                wrapper.appendChild(toggleBtn); // Button inside wrapper, not container
            }
            container.appendChild(wrapper);
        } else {
            wrapper.appendChild(contentDiv);
            container.appendChild(wrapper);
        }
        
        return container;
    }

    // Inject ad into page
    function injectAd(ad) {
        if (!ad.content) {
            return;
        }
        
        // Prevent nested ads - don't inject ads inside ad content
        // Check if we're being called from within an ad container
        const activeElement = document.activeElement || document.body;
        const isInsideAd = activeElement.closest && activeElement.closest('.analytics-ad-interstitial, .analytics-ad-pop-from-bottom, .analytics-ad-pop-from-top, .analytics-ad-content-wrapper');
        if (isInsideAd) {
            return; // Don't inject ads inside ad content
        }
        
        // Also check if there's an ad container in the process of being created
        if (document.body.querySelector('.analytics-ad-interstitial, .analytics-ad-pop-from-bottom, .analytics-ad-pop-from-top')) {
            // If an ad is already visible, skip to prevent nested ads
            // (This is a simple check - can be improved)
        }

        try {
            // Special ad types (pop_from_bottom, pop_from_top, Interstitial) don't need selectors
            const specialTypes = ['pop_from_bottom', 'pop_from_top', 'Interstitial'];
            if (specialTypes.includes(ad.type)) {
                // Check interval period for Interstitial ads
                if (ad.type === 'Interstitial') {
                    // interval_period can be: null/undefined (no interval, auto-close after 10s)
                    // or a number > 0 (show again after X seconds)
                    const intervalPeriod = ad.interval_period !== null && ad.interval_period !== undefined ? parseInt(ad.interval_period) : null;
                    
                    if (intervalPeriod !== null && intervalPeriod > 0) {
                        // Has interval period - check if enough time has passed
                    const lastShown = localStorage.getItem('analytics_ad_interstitial_' + ad.id);
                    if (lastShown) {
                        const timeSinceLastShown = (Date.now() - parseInt(lastShown)) / 1000;
                            if (timeSinceLastShown < intervalPeriod) {
                                return; // Don't show yet - not enough time has passed
                        }
                    }
                        // Store current time for next interval check
                    localStorage.setItem('analytics_ad_interstitial_' + ad.id, Date.now().toString());
                }
                    // If interval_period is null/0, will show and auto-close after 10s (handled below)
                }

                // Always create structure with iframe isolation in JavaScript
                // Don't use server-provided structure to ensure iframe isolation
                const adContainer = createPopupAdStructure(ad);
                
                if (!adContainer) {
                    return;
                }

                // Check sessionStorage for collapsed state (only for pop_from_bottom and pop_from_top)
                // Interstitial does NOT use sessionStorage - if closed, it's removed permanently
                let wasCollapsed = false;
                if (ad.type === 'pop_from_bottom' || ad.type === 'pop_from_top') {
                    wasCollapsed = sessionStorage.getItem('analytics_ad_collapsed_' + ad.id) === 'true';
                    
                    if (wasCollapsed) {
                        // Start in collapsed state
                        adContainer.classList.add('analytics-ad-collapsed');
                        const toggleBtn = adContainer.querySelector('.analytics-ad-toggle');
                        const offset = 52; // Height of toggle button + margin (32px + 20px)
                        adContainer.style.transform = ad.type === 'pop_from_bottom' ? 'translateY(calc(100% - ' + offset + 'px))' : 'translateY(calc(-100% + ' + offset + 'px))';
                        if (toggleBtn) {
                            // Icon points to expand direction (opposite of collapse)
                            toggleBtn.innerHTML = ad.type === 'pop_from_bottom' ? '▲' : '▼';
                        }
                    }
                }

                // Inject into body
                document.body.appendChild(adContainer);

                // Force Interstitial visibility immediately (before animation)
                if (ad.type === 'Interstitial') {
                    // Force container visibility immediately - no delay, multiple methods
                    adContainer.style.setProperty('opacity', '1', 'important');
                    adContainer.style.setProperty('display', 'flex', 'important');
                    adContainer.style.setProperty('visibility', 'visible', 'important');
                    adContainer.style.setProperty('pointer-events', 'auto', 'important');
                    adContainer.style.setProperty('background', 'rgb(0 0 0 / 68%)', 'important');
                    adContainer.style.setProperty('backdrop-filter', 'blur(10px)', 'important');
                    adContainer.style.setProperty('-webkit-backdrop-filter', 'blur(10px)', 'important');
                    
                    // Direct assignment as backup
                    adContainer.style.opacity = '1';
                    adContainer.style.display = 'flex';
                    adContainer.style.visibility = 'visible';
                    adContainer.style.background = 'rgb(0 0 0 / 68%)';
                    
                    // Also set wrapper visibility immediately
                    const wrapper = adContainer.querySelector('.analytics-ad-interstitial-wrapper');
                    if (wrapper) {
                        wrapper.style.setProperty('opacity', '1', 'important');
                        wrapper.style.setProperty('transform', 'scale(1)', 'important');
                        wrapper.style.setProperty('display', 'block', 'important');
                        wrapper.style.setProperty('visibility', 'visible', 'important');
                        wrapper.style.setProperty('pointer-events', 'auto', 'important');
                        // Direct assignment as backup
                        wrapper.style.opacity = '1';
                        wrapper.style.transform = 'scale(1)';
                        wrapper.style.display = 'block';
                    }
                    
                    // Remove any close buttons from Interstitial content (they're not needed)
                    const closeButtons = adContainer.querySelectorAll('.analytics-ad-close');
                    closeButtons.forEach(function(btn) {
                        btn.remove();
                    });
                    
                    // Force styles again after a tiny delay to override any external CSS
                    setTimeout(function() {
                        adContainer.style.setProperty('opacity', '1', 'important');
                        adContainer.style.setProperty('background', 'rgb(0 0 0 / 68%)', 'important');
                        adContainer.style.opacity = '1';
                        adContainer.style.background = 'rgb(0 0 0 / 68%)';
                        if (wrapper) {
                            wrapper.style.setProperty('opacity', '1', 'important');
                            wrapper.style.opacity = '1';
                        }
                    }, 50);
                }
                
                // Animate in (only if not collapsed)
                if (!wasCollapsed) {
                    setTimeout(function() {
                        if (ad.type === 'Interstitial') {
                            // Container already visible, just ensure wrapper is fully visible
                            const wrapper = adContainer.querySelector('.analytics-ad-interstitial-wrapper');
                            if (wrapper) {
                                // Use requestAnimationFrame for smooth animation
                                requestAnimationFrame(function() {
                                    wrapper.style.setProperty('transform', 'scale(1)', 'important');
                                    wrapper.style.setProperty('opacity', '1', 'important');
                                });
                            }
                        } else {
                            // Smooth slide up/down animation
                            adContainer.style.transform = 'translateY(0)';
                        }
                    }, 10);
                }

                // Store url_pattern_id in container for click tracking
                if (ad.url_pattern_id) {
                    adContainer.setAttribute('data-url-pattern-id', ad.url_pattern_id);
                }

                // Track impression
                trackAdImpression(ad.id, ad.type, ad.url_pattern_id);

                // Track click if ad has URL - track all links in content
                const allLinks = adContainer.querySelectorAll('.analytics-ad-content-wrapper a[href], a[href]');
                allLinks.forEach(function(link) {
                    if (!link.classList.contains('analytics-ad-toggle')) {
                        link.addEventListener('click', function(e) {
                            const href = link.getAttribute('href');
                            if (href && href !== '#' && !href.startsWith('javascript:')) {
                                e.preventDefault();
                                // Use ad.url if available, otherwise use link href
                                const targetUrl = ad.url || href;
                                trackAdClick(ad.id, targetUrl, ad.type, ad.url_pattern_id);
                                window.open(targetUrl, '_blank');
                            }
                        });
                    }
                });

                // No auto-close for Interstitial - user controls via toggle if needed
                // Interstitial will be hidden/shown based on interval_period logic only

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
    
    // Expose functions globally for onclick handlers
    window.closeAdPopup = closeAdPopup;
    window.toggleAdCollapse = toggleAdCollapse;
    window.closeInterstitialAd = closeInterstitialAd;
})();

@extends('layouts.admin')
@section('content')
<style>
    /* Hide navbar on analytics show page */
    .top-nav {
        display: none !important;
    }
    
    .main-content {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
    
    .main-content > .col-12 {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
    
    .analytics-dashboard {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
    
    .analytics-header {
        margin-top: 0 !important;
        padding-top: 32px !important;
    }
    .analytics-dashboard {
        --analytics-bg: var(--background-1, #ffffff);
        --analytics-border: var(--border-color, #e5e7eb);
        --analytics-text: var(--color-2, #1f2937);
        --analytics-text-muted: #6b7280;
        --analytics-primary: #7b60fb;
        --analytics-success: #10b981;
        --analytics-active: #10b981;
        --analytics-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --analytics-gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
        
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', 'Roboto', sans-serif;
        color: var(--analytics-text);
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 0;
        margin: 0;
    }
    
    .analytics-header {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        padding: 32px 40px;
        margin-bottom: 32px;
        margin-top: 0;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02);
        position: relative;
        z-index: 100;
        overflow: visible;
    }
    
    .analytics-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle at top right, rgba(123, 96, 251, 0.12) 0%, transparent 60%);
        pointer-events: none;
        opacity: 0.6;
    }
    
    .analytics-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent 0%, rgba(123, 96, 251, 0.2) 50%, transparent 100%);
    }
    
    .analytics-header h1 {
        font-size: 32px;
        font-weight: 800;
        margin: 0 0 8px 0;
        color: var(--analytics-text);
        letter-spacing: -0.8px;
        position: relative;
        z-index: 1;
    }
    
    .analytics-header p {
        font-size: 15px;
        color: var(--analytics-text-muted);
        margin: 0 0 16px 0;
        position: relative;
        z-index: 1;
        font-weight: 500;
    }
    
    .header-stats {
        display: flex;
        gap: 16px;
        margin-top: 16px;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }
    
    .header-stat-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--analytics-text-muted);
        padding: 10px 16px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.9) 100%);
        border-radius: 12px;
        border: 1px solid rgba(123, 96, 251, 0.15);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(123, 96, 251, 0.08);
        backdrop-filter: blur(10px);
    }
    
    .header-stat-item:hover {
        background: linear-gradient(135deg, rgba(123, 96, 251, 0.1) 0%, rgba(123, 96, 251, 0.05) 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(123, 96, 251, 0.15);
        border-color: rgba(123, 96, 251, 0.25);
    }
    
    .header-stat-icon {
        font-size: 18px;
        filter: grayscale(0.3);
        transition: filter 0.3s ease;
    }
    
    .header-stat-item:hover .header-stat-icon {
        filter: grayscale(0);
    }
    
    .header-stat-label {
        font-weight: 600;
        color: var(--analytics-text-muted);
    }
    
    .header-stat-value {
        font-weight: 800;
        color: var(--analytics-primary);
        font-size: 16px;
        letter-spacing: -0.3px;
    }
    
    .analytics-header .dropdown button {
        background: linear-gradient(135deg, rgba(123, 96, 251, 0.12) 0%, rgba(123, 96, 251, 0.08) 100%);
        border: 1px solid rgba(123, 96, 251, 0.25);
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 700;
        color: var(--analytics-primary);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(123, 96, 251, 0.12);
        font-size: 14px;
    }
    
    .analytics-header .dropdown button:hover {
        background: linear-gradient(135deg, rgba(123, 96, 251, 0.2) 0%, rgba(123, 96, 251, 0.15) 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(123, 96, 251, 0.2);
        border-color: rgba(123, 96, 251, 0.35);
    }
    
    .analytics-header .dropdown button:focus {
        box-shadow: 0 4px 12px rgba(123, 96, 251, 0.2), 0 0 0 3px rgba(123, 96, 251, 0.1);
    }
    
    .analytics-header .dropdown-menu {
        border-radius: 12px;
        border: 1px solid rgba(123, 96, 251, 0.15);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        padding: 8px;
        margin-top: 8px;
    }
    
    .analytics-header .dropdown-item {
        border-radius: 8px;
        padding: 10px 16px;
        transition: all 0.2s ease;
        font-weight: 500;
    }
    
    .analytics-header .dropdown-item:hover {
        background: linear-gradient(135deg, rgba(123, 96, 251, 0.1) 0%, rgba(123, 96, 251, 0.05) 100%);
        color: var(--analytics-primary);
        transform: translateX(-4px);
    }
    
    .analytics-header .dropdown-item.text-danger:hover {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%);
        color: #dc2626;
    }
    
    /* Site Card Styles (matching index page) */
    .site-card {
        background: var(--background-1, #ffffff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 12px;
        padding: 20px;
        transition: box-shadow 0.2s, border-color 0.2s;
        position: relative;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: visible;
        z-index: 1;
    }
    
    .site-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: var(--analytics-primary, #7b60fb);
    }
    
    .site-card-stats {
        flex-shrink: 0;
    }
    
    .site-card-chart {
        flex: 1;
        min-height: 80px;
    }
    
    .site-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
        gap: 12px;
    }
    
    .site-card-title-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }
    
    .site-card-favicon {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        flex-shrink: 0;
        object-fit: contain;
    }
    
    .site-card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--color-2, #1f2937);
        margin: 0;
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        line-height: 1.3;
    }
    
    .site-card-domain {
        display: block;
        font-size: 11px;
        color: var(--analytics-text-muted, #6b7280);
        margin-top: 2px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .site-online-indicator {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 12px;
        color: var(--analytics-text-muted, #6b7280);
        flex-shrink: 0;
        position: relative;
        width: 20px;
        height: 20px;
    }
    
    .site-online-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #10b981;
        flex-shrink: 0;
        position: relative;
        z-index: 2;
        animation: dot-pulse 1.5s ease-in-out infinite;
    }
    
    .site-online-indicator::before {
        content: '';
        position: absolute;
        width: 18px;
        height: 18px;
        border: 2px solid #10b981;
        border-top-color: transparent;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-rotate 1s linear infinite;
        z-index: 1;
    }
    
    @keyframes spinner-rotate {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    
    @keyframes dot-pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.7;
            transform: scale(0.9);
        }
    }
    
    .site-card-stats {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
    }
    
    .site-card-stat {
        display: flex;
        flex-direction: column;
    }
    
    .site-card-stat-label {
        font-size: 12px;
        color: var(--analytics-text-muted, #6b7280);
        margin-bottom: 4px;
    }
    
    .site-card-stat-value {
        font-size: 20px;
        font-weight: 600;
        color: var(--color-2, #1f2937);
    }
    
    .site-card-chart {
        height: 80px;
        margin-top: 12px;
    }
    
    .hero-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.98) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 16px;
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
        z-index: 1;
    }
    
    .hero-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(123, 96, 251, 0.05) 0%, transparent 70%);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .hero-card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }
    
    .hero-card:hover::before {
        opacity: 1;
    }
    
    .hero-card-active {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.03) 100%);
        border-color: rgba(16, 185, 129, 0.3);
        box-shadow: 0 8px 30px rgba(16, 185, 129, 0.15), 0 0 0 1px rgba(16, 185, 129, 0.1);
    }
    
    .hero-card-active::before {
        background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
        opacity: 1;
    }
    
    .hero-card-active .metric-icon::after {
        content: '';
        position: absolute;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #10b981;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        animation: pulse-glow 2s ease-in-out infinite;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    
    @keyframes pulse-glow {
        0% {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        50% {
            opacity: 0.8;
            transform: translate(-50%, -50%) scale(1.1);
            box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
        }
        100% {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }
    
    .metric-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        position: relative;
        background: linear-gradient(135deg, rgba(123, 96, 251, 0.15) 0%, rgba(123, 96, 251, 0.08) 100%);
        color: var(--analytics-primary);
        margin-left: 8px;
        margin-bottom: 0;
        box-shadow: 0 2px 8px rgba(123, 96, 251, 0.15);
        transition: all 0.3s ease;
        vertical-align: middle;
        flex-shrink: 0;
    }
    
    .hero-card:hover .metric-icon {
        transform: scale(1.05) rotate(2deg);
    }
    
    .hero-card-active .metric-icon {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(16, 185, 129, 0.1) 100%);
        color: var(--analytics-active);
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
    }
    
    .metric-label {
        font-size: 12px;
        color: var(--analytics-text-muted);
        font-weight: 600;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .metric-value {
        font-size: 36px;
        font-weight: 800;
        line-height: 1.1;
        margin: 0 0 6px 0;
        color: var(--analytics-text);
        letter-spacing: -1px;
    }
    
    .hero-card-active .metric-value {
        color: var(--analytics-active);
    }
    
    /* First 3 sections equal height */
    .row.mb-5 > div {
        display: flex;
        flex-direction: column;
    }
    
    .row.mb-5 > div > .site-card,
    .row.mb-5 > div > .section-card {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .section-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.98) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        z-index: 1;
    }
    
    .section-card.sources-scrollable {
        display: flex;
        flex-direction: column;
    }
    
    .section-card .section-title {
        flex-shrink: 0;
    }
    
    .section-card .chart-container {
        flex: 1;
        min-height: 200px;
    }
    
    .section-card.sources-scrollable .sources-content {
        flex: 1;
        min-height: 0;
    }
    
    .section-card.sources-scrollable .sources-content {
        max-height: 295px;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 4px;
    }
    
    .section-card.sources-scrollable .sources-content::-webkit-scrollbar {
        width: 6px;
    }
    
    .section-card.sources-scrollable .sources-content::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
    }
    
    .section-card.sources-scrollable .sources-content::-webkit-scrollbar-thumb {
        background: rgba(123, 96, 251, 0.3);
        border-radius: 10px;
    }
    
    .section-card.sources-scrollable .sources-content::-webkit-scrollbar-thumb:hover {
        background: rgba(123, 96, 251, 0.5);
    }
    
    .dropdown-menu {
        z-index: 10000 !important;
    }
    
    /* Ensure dropdown appears above all cards */
    #siteActionsDropdown + .dropdown-menu,
    .dropdown[style*="z-index: 9999"] .dropdown-menu,
    .analytics-header .dropdown-menu {
        z-index: 10001 !important;
        position: absolute !important;
    }
    
    /* Ensure dropdown container is above cards */
    .analytics-header .dropdown {
        position: relative;
        z-index: 10001;
    }
    
    /* Ensure cards don't overlap dropdown */
    .site-card,
    .section-card,
    .sources-scrollable {
        z-index: 1 !important;
        position: relative;
    }
    
    /* Remove dropdown arrow icon */
    #siteActionsDropdown::after {
        display: none !important;
    }
    
    .modal {
        z-index: 1060 !important;
    }
    
    .modal-backdrop {
        z-index: 1059 !important;
    }
    
    .section-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(123, 96, 251, 0.04) 0%, transparent 70%);
        pointer-events: none;
    }
    
    .section-card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1), 0 2px 8px rgba(0, 0, 0, 0.06);
        transform: translateY(-2px);
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 16px 0;
        color: var(--analytics-text);
        padding-bottom: 12px;
        border-bottom: 2px solid rgba(123, 96, 251, 0.1);
        position: relative;
        letter-spacing: -0.3px;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -2px;
        right: 0;
        width: 60px;
        height: 2px;
        background: var(--analytics-gradient);
        border-radius: 2px;
    }
    
    .visits-filter-form {
        display: inline-block;
    }
    
    .visits-filter-form select {
        font-size: 13px;
        padding: 6px 12px;
        border: 1px solid var(--analytics-border);
        border-radius: 6px;
        background: var(--analytics-bg);
        color: var(--analytics-text);
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .visits-filter-form select:hover {
        border-color: var(--analytics-primary);
    }
    
    .visits-filter-form select:focus {
        outline: none;
        border-color: var(--analytics-primary);
        box-shadow: 0 0 0 3px rgba(123, 96, 251, 0.1);
    }
    
    .page-row {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        margin-bottom: 4px;
        border-radius: 6px;
        background: var(--analytics-bg);
        border: 1px solid var(--analytics-border);
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }
    
    .page-row:hover {
        border-color: var(--analytics-primary);
        box-shadow: 0 1px 4px rgba(123, 96, 251, 0.1);
    }
    
    .page-row::before {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: var(--progress-width, 0%);
        background: rgba(123, 96, 251, 0.08);
        z-index: 0;
    }
    
    .page-row-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        gap: 12px;
    }
    
    .page-path-link {
        text-decoration: none;
        flex: 1;
        margin-left: 8px;
    }
    
    .page-path {
        font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
        font-size: 12px;
        color: var(--analytics-text);
        word-break: break-all;
        line-height: 1.4;
        display: block;
        transition: color 0.2s;
    }
    
    .page-path-link:hover .page-path {
        color: var(--analytics-primary);
    }
    
    .page-visits {
        font-size: 13px;
        font-weight: 600;
        color: var(--analytics-text);
        white-space: nowrap;
    }
    
    /* Visits Table Styles */
    .visits-table-container {
        overflow-x: auto;
        margin-top: 16px;
    }
    
    .visits-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    
    .visits-table thead {
        background: rgba(123, 96, 251, 0.05);
        border-bottom: 2px solid var(--analytics-border);
    }
    
    .visits-table th {
        padding: 12px 16px;
        text-align: right;
        font-weight: 600;
        font-size: 12px;
        color: var(--analytics-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    
    .visits-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--analytics-border);
        vertical-align: middle;
    }
    
    .visits-table-row {
        transition: background-color 0.15s ease;
        cursor: default;
    }
    
    .visits-table-row:hover {
        background-color: rgba(123, 96, 251, 0.03);
    }
    
    .visits-table-path {
        max-width: 200px;
    }
    
    .path-link {
        font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
        font-size: 12px;
        color: var(--analytics-primary);
        background: linear-gradient(135deg, rgba(123, 96, 251, 0.12) 0%, rgba(123, 96, 251, 0.08) 100%);
        padding: 6px 12px;
        border-radius: 8px;
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: help;
        border: 1px solid rgba(123, 96, 251, 0.2);
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .path-link-clickable {
        text-decoration: none;
        display: inline-block;
        max-width: 100%;
    }
    
    .path-link-clickable code {
        font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
        font-size: 12px;
        color: var(--analytics-primary);
        background: linear-gradient(135deg, rgba(123, 96, 251, 0.12) 0%, rgba(123, 96, 251, 0.08) 100%);
        padding: 6px 12px;
        border-radius: 8px;
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(123, 96, 251, 0.2);
        font-weight: 500;
    }
    
    .path-link-clickable:hover code {
        background: linear-gradient(135deg, rgba(123, 96, 251, 0.2) 0%, rgba(123, 96, 251, 0.15) 100%);
        color: var(--analytics-primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(123, 96, 251, 0.25);
        border-color: rgba(123, 96, 251, 0.4);
    }
    
    .visits-table-time {
        color: var(--analytics-text-muted);
        font-size: 12px;
        white-space: nowrap;
    }
    
    .visits-table-country {
        font-size: 13px;
        white-space: nowrap;
    }
    
    .visits-table-ip {
        font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
    }
    
    .ip-address {
        font-size: 12px;
        color: var(--analytics-text-muted);
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.03) 100%);
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        font-weight: 500;
    }
    
    .visits-table-device,
    .visits-table-browser {
        font-size: 13px;
        white-space: nowrap;
        font-weight: 500;
    }
    
    .visits-table-paths-count {
        text-align: center;
    }
    
    .paths-count-link {
        display: inline-block;
        font-weight: 700;
        color: var(--analytics-primary);
        background: linear-gradient(135deg, rgba(123, 96, 251, 0.15) 0%, rgba(123, 96, 251, 0.1) 100%);
        padding: 8px 16px;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        min-width: 48px;
        text-align: center;
        border: 1px solid rgba(123, 96, 251, 0.2);
        box-shadow: 0 2px 8px rgba(123, 96, 251, 0.1);
    }
    
    .paths-count-link:hover {
        background: var(--analytics-gradient);
        color: white;
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 4px 16px rgba(123, 96, 251, 0.35);
        border-color: transparent;
    }
    
    .visits-pagination {
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid rgba(123, 96, 251, 0.1);
        display: flex;
        justify-content: center;
    }
    
    .visits-pagination .pagination {
        margin: 0;
    }
    
    .visits-pagination .page-link {
        color: var(--analytics-primary);
        border-color: rgba(123, 96, 251, 0.2);
        padding: 10px 18px;
        border-radius: 10px;
        margin: 0 4px;
        font-weight: 500;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.8);
    }
    
    .visits-pagination .page-link:hover {
        background-color: rgba(123, 96, 251, 0.15);
        border-color: var(--analytics-primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(123, 96, 251, 0.2);
    }
    
    .visits-pagination .page-item.active .page-link {
        background: var(--analytics-gradient);
        border-color: transparent;
        color: white;
        box-shadow: 0 4px 12px rgba(123, 96, 251, 0.3);
    }
    
    .path-sequence {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 12px;
    }
    
    .path-badge {
        padding: 8px 14px;
        background: linear-gradient(135deg, rgba(123, 96, 251, 0.15) 0%, rgba(123, 96, 251, 0.1) 100%);
        border: 1px solid rgba(123, 96, 251, 0.25);
        border-radius: 10px;
        font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
        font-size: 12px;
        color: var(--analytics-primary);
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .path-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(123, 96, 251, 0.2);
    }
    
    .path-arrow {
        color: var(--analytics-text-muted);
        font-size: 16px;
        font-weight: 600;
    }
    
    .source-row {
        display: flex;
        align-items: center;
        padding: 4px 8px;
        margin-bottom: 3px;
        border-radius: 6px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.6) 0%, rgba(255, 255, 255, 0.8) 100%);
        border: 1px solid rgba(123, 96, 251, 0.15);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }
    
    .source-row:hover {
        border-color: var(--analytics-primary);
        box-shadow: 0 4px 16px rgba(123, 96, 251, 0.15);
        transform: translateX(-4px);
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.95) 100%);
    }
    
    .source-row::before {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: var(--progress-width, 0%);
        background: linear-gradient(90deg, transparent 0%, rgba(123, 96, 251, 0.12) 100%);
        z-index: 0;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .source-row-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        gap: 16px;
    }
    
    .source-icon-name {
        font-size: 11px;
        font-weight: 600;
        color: var(--analytics-text);
        display: flex;
        align-items: center;
        gap: 5px;
        flex: 1;
    }
    
    .source-count {
        font-size: 11px;
        font-weight: 700;
        color: var(--analytics-primary);
        white-space: nowrap;
        padding: 3px 6px;
        background: rgba(123, 96, 251, 0.1);
        border-radius: 5px;
        transition: all 0.2s ease;
    }
    
    .source-row:hover .source-count {
        background: rgba(123, 96, 251, 0.15);
        transform: scale(1.05);
    }
    
    .chart-container {
        position: relative;
        height: 200px;
        margin-top: 12px;
        padding: 8px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.5);
    }
    
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        color: var(--analytics-text-muted);
        min-height: 200px;
    }
    
    .empty-state-icon {
        width: 80px;
        height: 80px;
        margin-bottom: 24px;
        opacity: 0.4;
        animation: float 3s ease-in-out infinite;
        filter: grayscale(100%) opacity(0.5);
        transition: all 0.3s ease;
    }
    
    .empty-state-icon svg {
        width: 100%;
        height: 100%;
    }
    
    .empty-state:hover .empty-state-icon {
        opacity: 0.6;
        filter: grayscale(50%) opacity(0.7);
    }
    
    .empty-state-text {
        font-size: 15px;
        font-weight: 500;
        color: var(--analytics-text-muted);
        margin: 0;
    }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-8px);
        }
    }
</style>
<div class="analytics-dashboard">
    <!-- Header -->
    <div class="analytics-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>نتائج البحث</h1>
                <p style="font-size: 15px; color: var(--analytics-text-muted); margin-top: 8px;">
                    <strong>البحث عن:</strong> 
                    <code style="background: rgba(123, 96, 251, 0.1); padding: 4px 8px; border-radius: 6px; color: var(--analytics-primary); font-size: 14px;">{{ $query }}</code>
                    <span style="margin: 0 8px;">|</span>
                    <strong>نوع المطابقة:</strong> 
                    <span style="color: var(--analytics-primary);">
                        @if($matchType == 'prefix')
                            Prefix Match
                        @elseif($matchType == 'exact')
                            Exact Match
                        @else
                            بحث بالـ IP
                        @endif
                    </span>
                    @if(isset($dateFrom) && isset($dateTo))
                    <span style="margin: 0 8px;">|</span>
                    <strong>الفترة:</strong> 
                    <span style="color: var(--analytics-text-muted);">{{ $dateFrom }} - {{ $dateTo }}</span>
                    @endif
                </p>
                <p style="font-size: 13px; color: var(--analytics-text-muted); margin-top: 4px;">
                    {{ $site->title ?? $site->domain }}
                </p>
                @if(isset($noResults) && $noResults)
                <div style="margin-top: 16px; padding: 16px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 8px; color: #dc2626;">
                    <strong>⚠️</strong> لم يتم العثور على نتائج للبحث المحدد.
                </div>
                @else
                <div class="header-stats">
                    <span class="header-stat-item">
                        <span class="header-stat-icon">👥</span>
                        <span class="header-stat-label">الزوار اليوم:</span>
                        <span class="header-stat-value">{{ number_format($todayStats['visitors'] ?? 0) }}</span>
                    </span>
                    <span class="header-stat-item">
                        <span class="header-stat-icon">📄</span>
                        <span class="header-stat-label">مشاهدات الصفحة اليوم:</span>
                        <span class="header-stat-value">{{ number_format($todayStats['pageviews'] ?? 0) }}</span>
                    </span>
                    @if(isset($stats))
                    <span class="header-stat-item">
                        <span class="header-stat-icon">📊</span>
                        <span class="header-stat-label">إجمالي الجلسات:</span>
                        <span class="header-stat-value">{{ number_format($stats['total_sessions'] ?? 0) }}</span>
                    </span>
                    @endif
                </div>
                @endif
            </div>
            <div>
                <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.search', ['site' => $site->site_key]) : route('user.analytics.search', ['site' => $site->site_key]) }}" class="btn btn-sm btn-secondary" style="margin-left: 8px;">
                    <i class="fa fa-search" style="margin-left: 6px;"></i>
                    بحث جديد
                </a>
                <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.show', ['site' => $site->site_key]) : route('user.analytics.show', ['site' => $site->site_key]) }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-right" style="margin-left: 6px;"></i>
                    العودة
                </a>
            </div>
        </div>
    </div>
    
    @if(isset($noResults) && $noResults)
                event.preventDefault();
                if (confirm('هل أنت متأكد من حذف الموقع "' + domain + '"؟\n\nهذا الإجراء لا يمكن التراجع عنه.')) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    
                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    var methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            }
            </script>
        </div>
    </div>
    
    <div style="padding: 0 40px 40px;">
        <!-- HERO SECTION -->
        <div class="row mb-5" style="display: flex; align-items: stretch;">
            <!-- ACTIVE USERS (HERO) - نصف الشاشة -->
            <div class="col-lg-6 mb-4">
                <div class="site-card">
                    <div class="site-card-header">
                        <div class="site-card-title-wrapper">
                            <img src="https://icons.duckduckgo.com/ip3/{{ $site->domain }}.ico" 
                                 alt="" 
                                 class="site-card-favicon"
                                 onerror="this.style.display='none'">
                            <h3 class="site-card-title">{{ $site->title ?? $site->domain }}</h3>
                            @if($site->title && $site->title !== $site->domain)
                            <small class="site-card-domain">{{ $site->domain }}</small>
                            @endif
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            @if(isset($hasTrafficLast5Min) && $hasTrafficLast5Min)
                            <span class="site-online-indicator">
                                <span class="site-online-dot"></span>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="site-card-stats">
                        <div class="site-card-stat">
                            <span class="site-card-stat-label">المستخدمون النشطون</span>
                            <span class="site-card-stat-value">{{ number_format($activeUsersCount ?? 0) }}</span>
                        </div>
                        <div class="site-card-stat">
                            <span class="site-card-stat-label">المستخدمون اليوم</span>
                            <span class="site-card-stat-value">{{ number_format($todayUsersCount ?? 0) }}</span>
                        </div>
                    </div>
                    
                    @if(isset($activeUsersData) && count($activeUsersData) > 0)
                    <div class="site-card-chart">
                        <canvas id="activeUsersChart"></canvas>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- TOP TRAFFIC SOURCES - ربع الشاشة -->
            <div class="col-lg-3 mb-4">
                <div class="section-card sources-scrollable">
                    <h2 class="section-title">أفضل المصادر</h2>
                    <div class="sources-content">
                    @if(isset($topTrafficSources) && $topTrafficSources->count() > 0)
                        @php
                            $maxSourceCount = $topTrafficSources->first()['count'] ?? 1;
                        @endphp
                        @foreach($topTrafficSources->take(10) as $source)
                            @php
                                $sourceName = strtolower($source['name'] ?? '');
                                $sourceCount = $source['count'] ?? 0;
                                $sourcePercent = $maxSourceCount > 0 ? ($sourceCount / $maxSourceCount) * 100 : 0;
                                
                                // Get icon domain from referrer URL hostname
                                $sourceIconDomain = '';
                                if ($source['type'] == 'direct' || $sourceName == 'direct') {
                                    $sourceIconDomain = $site->domain;
                                } elseif (!empty($source['referrer_url'])) {
                                    // Extract hostname from referrer URL
                                    $parsed = parse_url($source['referrer_url']);
                                    if (isset($parsed['host'])) {
                                        $sourceIconDomain = $parsed['host'];
                                        // Remove www. prefix if exists
                                        $sourceIconDomain = preg_replace('/^www\./', '', $sourceIconDomain);
                                    }
                                }
                                
                                // Fallback: if no referrer URL, try to use source name as domain
                                if (!$sourceIconDomain && $source['type'] == 'referrer') {
                                    $sourceNameClean = $source['name'];
                                    if (strpos($sourceNameClean, '.') === false) {
                                        $sourceIconDomain = $sourceNameClean . '.com';
                                    } else {
                                        $sourceIconDomain = $sourceNameClean;
                                    }
                                }
                                
                                $sourceIconUrl = $sourceIconDomain ? 'https://icons.duckduckgo.com/ip3/' . $sourceIconDomain . '.ico' : '';
                                
                                // Display name
                                $sourceDisplayName = '';
                                if ($source['type'] == 'direct' || $sourceName == 'direct') {
                                    $sourceDisplayName = 'مباشر';
                                } else {
                                    $sourceDisplayName = ucfirst($source['name']);
                                }
                            @endphp
                            <div class="source-row" style="--progress-width: {{ $sourcePercent }}%;">
                                <div class="source-row-content">
                                    <span class="source-icon-name">
                                        @if($sourceIconUrl)
                                        <img src="{{ $sourceIconUrl }}" alt="" style="width: 16px; height: 16px; flex-shrink: 0;" onerror="this.style.display='none'">
                                        @endif
                                        <span>{{ $sourceDisplayName }}</span>
                                    </span>
                                    <span class="source-count">{{ number_format($sourceCount) }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <svg class="empty-state-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <p class="empty-state-text">لا توجد بيانات</p>
                        </div>
                    @endif
                    </div>
                </div>
            </div>
            
            <!-- VISITORS OVER TIME - ربع الشاشة -->
            <div class="col-lg-3 mb-4">
                <div class="section-card">
                    <h2 class="section-title">الزوار - آخر 7 أيام</h2>
                    <div class="chart-container" style="margin-top: 16px;">
                        <canvas id="visitorsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- TOP PAGES & VISITS -->
        <div class="row">
            <!-- TOP PAGES -->
            <div class="col-lg-4 mb-4">
                <div class="section-card">
                    <h2 class="section-title">أفضل الصفحات</h2>
                    @if($topPages->count() > 0)
                        @php
                            $maxVisits = $topPages->first()->views ?? 1;
                        @endphp
                        @foreach($topPages as $page)
                            @php
                                // Decode URL-encoded paths
                                $decodedPath = urldecode($page->path);
                                $pageUrl = 'https://' . $site->domain . $decodedPath;
                            @endphp
                            <div class="page-row" style="--progress-width: {{ ($page->views / $maxVisits) * 100 }}%;">
                                <div class="page-row-content">
                                    <a href="{{ $pageUrl }}" target="_blank" class="page-path-link" title="{{ $decodedPath }}">
                                        <code class="page-path">{{ Str::limit($decodedPath, 50) }}</code>
                                    </a>
                                    <span class="page-visits">{{ number_format($page->views) }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <svg class="empty-state-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            <p class="empty-state-text">لا توجد بيانات للصفحات</p>
                        </div>
                    @endif
                </div>
            </div>
      
        
    
            <div class="col-lg-8">
                <div class="section-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="section-title mb-0">الزيارات والمسارات الأخيرة</h2>
                        <form method="GET" action="{{ request()->url() }}" class="visits-filter-form">
                            <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                            <input type="hidden" name="date_to" value="{{ $dateTo }}">
                            <select name="referrer_filter" id="referrerFilter" class="form-control form-control-sm" onchange="this.form.submit()" style="min-width: 150px;">
                                <option value="external" {{ request('referrer_filter', 'external') == 'external' ? 'selected' : '' }}>مواقع خارجية فقط</option>
                                <option value="internal" {{ request('referrer_filter') == 'internal' ? 'selected' : '' }}>نفس الموقع فقط</option>
                                <option value="all" {{ request('referrer_filter') == 'all' ? 'selected' : '' }}>جميع الزيارات</option>
                            </select>
                        </form>
                    </div>
            @if(isset($visitsWithPaths) && $visitsWithPaths->count() > 0)
                <div class="visits-table-container">
                    <table class="visits-table">
                        <thead>
                            <tr>
                                <th>من الصفحة</th>
                                <th>إلى الصفحة</th>
                                <th>الوقت</th>
                                <th>الدولة</th>
                                <th>عنوان IP</th>
                                <th>الجهاز</th>
                                <th>المتصفح</th>
                                <th>عدد المسارات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visitsWithPaths as $visit)
                                @php
                                    $routeName = isset($isAdminRoute) && $isAdminRoute 
                                        ? 'admin.analytics.visit-details' 
                                        : 'user.analytics.visit-details';
                                @endphp
                                @php
                                    // Decode URL-encoded paths
                                    $decodedEntryPath = urldecode($visit['entry_path']);
                                    $decodedExitPath = urldecode($visit['exit_path']);
                                    
                                    // Determine source to display
                                    $referrerUrl = $visit['referrer'] ?? null;
                                    $siteDomain = $visit['site_domain'] ?? $site->domain;
                                    $referrerSource = $visit['referrer_source'] ?? 'Direct';
                                    
                                    // Check if referrer is from same domain
                                    $isSameDomain = false;
                                    if ($referrerUrl) {
                                        $referrerHost = parse_url($referrerUrl, PHP_URL_HOST);
                                        $isSameDomain = $referrerHost && (
                                            $referrerHost === $siteDomain || 
                                            $referrerHost === 'www.' . $siteDomain ||
                                            'www.' . $referrerHost === $siteDomain
                                        );
                                    }
                                    
                                    // Source to display: if same domain show entry_path, else show referrer_source
                                    $sourceIconUrl = '';
                                    $sourceIconDomain = '';
                                    if ($isSameDomain || $referrerSource === 'Direct') {
                                        // Same domain or direct: show entry path
                                        $sourceDisplay = $decodedEntryPath;
                                        $sourceUrl = 'https://' . $site->domain . $decodedEntryPath;
                                        $sourceIconDomain = $site->domain;
                                    } else {
                                        // External source: show referrer source name
                                        $sourceDisplay = $referrerSource;
                                        $sourceUrl = $referrerUrl;
                                        
                                        // Get hostname from referrer URL
                                        if ($referrerUrl) {
                                            $parsed = parse_url($referrerUrl);
                                            $sourceIconDomain = $parsed['host'] ?? null;
                                            // Remove www. prefix if exists
                                            if ($sourceIconDomain) {
                                                $sourceIconDomain = preg_replace('/^www\./', '', $sourceIconDomain);
                                            }
                                        }
                                        
                                        // Fallback to site domain if no referrer URL
                                        if (!$sourceIconDomain) {
                                            $sourceIconDomain = $site->domain;
                                        }
                                    }
                                    
                                    if ($sourceIconDomain) {
                                        $sourceIconUrl = 'https://icons.duckduckgo.com/ip3/' . $sourceIconDomain . '.ico';
                                    }
                                    
                                    // Build full URLs for clickable links
                                    $entryUrl = $site->domain . $decodedEntryPath;
                                    $exitUrl = $site->domain . $decodedExitPath;
                                @endphp
                                <tr class="visits-table-row">
                                    <td class="visits-table-path">
                                        @if($sourceUrl)
                                            <a href="{{ $sourceUrl }}" target="_blank" class="path-link-clickable" title="{{ $sourceDisplay }}" style="display: flex; align-items: center; gap: 6px;">
                                                @if($sourceIconUrl)
                                                <img src="{{ $sourceIconUrl }}" alt="" style="width: 16px; height: 16px; flex-shrink: 0;" onerror="this.style.display='none'">
                                                @endif
                                                <code>{{ Str::limit($sourceDisplay, 40) }}</code>
                                            </a>
                                        @else
                                            <code class="path-link" title="{{ $sourceDisplay }}" style="display: flex; align-items: center; gap: 6px;">
                                                @if($sourceIconUrl)
                                                <img src="{{ $sourceIconUrl }}" alt="" style="width: 16px; height: 16px; flex-shrink: 0;" onerror="this.style.display='none'">
                                                @endif
                                                {{ Str::limit($sourceDisplay, 40) }}
                                            </code>
                                        @endif
                                    </td>
                                    <td class="visits-table-path">
                                        <a href="https://{{ $exitUrl }}" target="_blank" class="path-link-clickable" title="{{ $decodedExitPath }}">
                                            <code>{{ Str::limit($decodedExitPath, 40) }}</code>
                                        </a>
                                    </td>
                                    <td class="visits-table-time">
                                        <span>{{ \Carbon\Carbon::parse($visit['first_seen'])->diffForHumans() }}</span>
                                    </td>
                                    <td class="visits-table-country">
                                        @php
                                            $countryCode = $visit['country'] ?? null;
                                            $countryNameAr = $countryCode;
                                            $countryFlag = '🌍';
                                            
                                            if ($countryCode) {
                                                $countries = collect(config('countries'));
                                                $country = $countries->firstWhere('iso2', strtoupper($countryCode));
                                                if ($country) {
                                                    $countryNameAr = $country['name_ar'] ?? $countryCode;
                                                    $countryFlag = $country['flag'] ?? '🌍';
                                                }
                                            }
                                        @endphp
                                        <span title="{{ $countryNameAr }}">
                                            {{ $countryFlag }} {{ $countryNameAr }}
                                        </span>
                                    </td>
                                    <td class="visits-table-ip">
                                        <code class="ip-address">{{ $visit['ip'] ?? '—' }}</code>
                                    </td>
                                    <td class="visits-table-device">
                                        @php
                                            $deviceType = $visit['device_type'] ?? null;
                                            $deviceIconSvg = '';
                                            $deviceLabel = '';
                                            
                                            if ($deviceType == 'desktop') {
                                                $deviceIconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>';
                                                $deviceLabel = 'Desktop';
                                            } elseif ($deviceType == 'mobile') {
                                                $deviceIconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>';
                                                $deviceLabel = 'Mobile';
                                            } elseif ($deviceType == 'tablet') {
                                                $deviceIconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>';
                                                $deviceLabel = 'Tablet';
                                            }
                                        @endphp
                                        @if($deviceIconSvg)
                                            <span title="{{ $deviceLabel }}" style="display: inline-flex; align-items: center; gap: 6px; color: var(--analytics-text-muted, #6b7280);">
                                                {!! $deviceIconSvg !!}
                                                <span>{{ $deviceLabel }}</span>
                                            </span>
                                        @else
                                            <span>—</span>
                                        @endif
                                    </td>
                                    <td class="visits-table-browser">
                                        @php
                                            $browser = strtolower($visit['browser'] ?? '');
                                            $version = $visit['browser_version'] ?? '';
                                            $browserIcon = '';
                                            $browserName = '';
                                            
                                            if (strpos($browser, 'chrome') !== false) {
                                                $browserIcon = 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/googlechrome.svg';
                                                $browserName = 'Chrome';
                                            } elseif (strpos($browser, 'safari') !== false && strpos($browser, 'chrome') === false) {
                                                $browserIcon = 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/safari.svg';
                                                $browserName = 'Safari';
                                            } elseif (strpos($browser, 'firefox') !== false) {
                                                $browserIcon = 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/firefox.svg';
                                                $browserName = 'Firefox';
                                            } elseif (strpos($browser, 'edge') !== false) {
                                                $browserIcon = 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/microsoftedge.svg';
                                                $browserName = 'Edge';
                                            } elseif (strpos($browser, 'opera') !== false) {
                                                $browserIcon = 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/opera.svg';
                                                $browserName = 'Opera';
                                            } elseif (strpos($browser, 'internet explorer') !== false) {
                                                $browserIcon = 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/internetexplorer.svg';
                                                $browserName = 'Internet Explorer';
                                            } else {
                                                $browserName = $visit['browser'] ?? 'Unknown';
                                            }
                                        @endphp
                                        @if($browserIcon)
                                            <span title="{{ $browserName }} {{ $version }}" style="display: inline-flex; align-items: center; gap: 6px;">
                                                <img src="{{ $browserIcon }}" alt="{{ $browserName }}" style="width: 18px; height: 18px;" onerror="this.style.display='none'">
                                                <span>{{ $browserName }}</span>
                                            </span>
                                        @else
                                            <span title="{{ $browserName }} {{ $version }}">{{ $browserName }}</span>
                                        @endif
                                    </td>
                                    <td class="visits-table-paths-count">
                                        <a href="{{ route($routeName, ['site' => $site->site_key, 'sessionId' => $visit['session_id']]) }}" 
                                           class="paths-count-link" 
                                           title="عرض تفاصيل المسار">
                                            {{ $visit['paths_count'] ?? 0 }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($visitsWithPaths->hasPages())
                    <div class="visits-pagination">
                        {{ $visitsWithPaths->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <svg class="empty-state-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <p class="empty-state-text">لا توجد بيانات للزيارات</p>
                </div>
            @endif
                </div>
            </div>
        </div>
        
        <!-- BROWSERS -->
        <div class="row">
            <div class="col-lg-4 mb-4">
                @if($topBrowsers->count() > 0)
                <div class="section-card">
                    <h2 class="section-title">المتصفحات</h2>
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="browsersChart"></canvas>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
@section('scripts')
<script src="/js/chartjs.min.js"></script>
<script>
// Active Users Chart (Hero - Last 30 minutes) - Bar Chart matching index page
@if(isset($activeUsersData) && count($activeUsersData) > 0)
const activeUsersCtx = document.getElementById('activeUsersChart');
if (activeUsersCtx) {
    new Chart(activeUsersCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($activeUsersData as $point)
                "{{ $point['time'] }}",
                @endforeach
            ],
            datasets: [{
                label: 'المستخدمون النشطون',
                data: [
                    @foreach($activeUsersData as $point)
                    {{ $point['count'] }},
                    @endforeach
                ],
                backgroundColor: 'rgba(16, 185, 129, 0.6)',
                borderColor: '#10b981',
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        title: function(context) {
                            var index = context[0].dataIndex;
                            var chartData = @json($activeUsersData);
                            return chartData[index] ? chartData[index].time : '';
                        },
                        label: function(context) {
                            return 'المستخدمون: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        display: false
                    },
                    grid: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        display: false
                    }
                }
            }
        }
    });
}
@endif

// Visitors Last 7 Days Chart (Hero - Line Chart)
@if(isset($visitorsLast7Days) && count($visitorsLast7Days) > 0)
const visitorsCtx = document.getElementById('visitorsChart');
if (visitorsCtx) {
    new Chart(visitorsCtx, {
        type: 'line',
        data: {
            labels: [
                @foreach($visitorsLast7Days as $day)
                "{{ $day['label'] }}",
                @endforeach
            ],
            datasets: [{
                label: 'زوار',
                data: [
                    @foreach($visitorsLast7Days as $day)
                    {{ $day['count'] }},
                    @endforeach
                ],
                backgroundColor: 'rgba(123, 96, 251, 0.1)',
                borderColor: '#7b60fb',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#7b60fb',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
}
@endif

// Browsers Chart (Doughnut Chart with Percentage)
@if(isset($topBrowsers) && $topBrowsers->count() > 0)
const browsersCtx = document.getElementById('browsersChart');
if (browsersCtx) {
    @php
        $browserColors = [
            'Chrome' => 'rgba(66, 133, 244, 0.8)',
            'Safari' => 'rgba(0, 122, 255, 0.8)',
            'Firefox' => 'rgba(255, 102, 0, 0.8)',
            'Edge' => 'rgba(0, 120, 212, 0.8)',
            'Opera' => 'rgba(255, 0, 0, 0.8)',
        ];
        $browserBorderColors = [
            'Chrome' => 'rgba(66, 133, 244, 1)',
            'Safari' => 'rgba(0, 122, 255, 1)',
            'Firefox' => 'rgba(255, 102, 0, 1)',
            'Edge' => 'rgba(0, 120, 212, 1)',
            'Opera' => 'rgba(255, 0, 0, 1)',
        ];
        $totalBrowsers = $topBrowsers->sum('count');
    @endphp
    new Chart(browsersCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($topBrowsers as $browser)
                "{{ $browser->browser }}",
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($topBrowsers as $browser)
                    {{ $browser->count }},
                    @endforeach
                ],
                backgroundColor: [
                    @foreach($topBrowsers as $browser)
                    '{{ $browserColors[$browser->browser] ?? 'rgba(123, 96, 251, 0.8)' }}',
                    @endforeach
                ],
                borderColor: [
                    @foreach($topBrowsers as $browser)
                    '{{ $browserBorderColors[$browser->browser] ?? '#7b60fb' }}',
                    @endforeach
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                const dataset = data.datasets[0];
                                const total = dataset.data.reduce((a, b) => a + b, 0);
                                return data.labels.map((label, i) => {
                                    const value = dataset.data[i];
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return {
                                        text: label + ' (' + percentage + '%)',
                                        fillStyle: dataset.backgroundColor[i],
                                        strokeStyle: dataset.borderColor[i],
                                        lineWidth: dataset.borderWidth,
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}
@endif

// Toggle paths expansion
function togglePaths(sessionId) {
    const pathsDiv = document.getElementById('paths-' + sessionId);
    const arrow = document.getElementById('arrow-' + sessionId);
    
    if (pathsDiv.classList.contains('expanded')) {
        pathsDiv.classList.remove('expanded');
        arrow.textContent = '▼';
    } else {
        pathsDiv.classList.add('expanded');
        arrow.textContent = '▲';
    }
}
</script>
@endsection

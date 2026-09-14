<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>System Settings - Smart Vadodara</title>

    @vite(['resources/js/admin/settings.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, Arial, sans-serif;
            background: #f4f7fb;
            color: #172033;
        }

        .admin-layout {
            min-height: 100vh;
            position: relative;
        }

        /* ================= SIDEBAR ================= */

        .admin-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 255px;
            background: #111827;
            color: #fff;
            z-index: 1100;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-brand {
            min-height: 82px;
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 11px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-logo {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .sidebar-brand strong {
            display: block;
            font-size: 14px;
            line-height: 1.2;
        }

        .sidebar-brand small {
            display: block;
            margin-top: 3px;
            color: #9ca3af;
            font-size: 10px;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 18px 12px;
        }

        .sidebar-section-title {
            color: #6b7280;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .08em;
            padding: 12px 11px 7px;
        }

        .sidebar-link,
        .sidebar-sub-link {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            text-decoration: none;
            color: #d1d5db;
            border-radius: 8px;
            margin-bottom: 3px;
            transition: background .15s ease, color .15s ease;
        }

        .sidebar-link {
            min-height: 42px;
            padding: 9px 11px;
            font-size: 13px;
            font-weight: 600;
        }

        .sidebar-sub-link {
            min-height: 34px;
            padding: 7px 11px 7px 36px;
            font-size: 12px;
            color: #9ca3af;
        }

        .sidebar-link:hover,
        .sidebar-sub-link:hover {
            color: #fff;
            background: rgba(255,255,255,.07);
        }

        .sidebar-link.active {
            background: #2563eb;
            color: #fff;
        }

        .sidebar-link span:first-child,
        .sidebar-sub-link span:first-child {
            width: 20px;
            text-align: center;
        }

        .sidebar-bottom {
            padding: 14px 12px;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-admin {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 7px 12px;
        }

        .admin-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
        }

        .sidebar-admin strong {
            display: block;
            font-size: 12px;
        }

        .sidebar-admin small {
            display: block;
            margin-top: 2px;
            color: #9ca3af;
            font-size: 10px;
        }

        .sidebar-logout {
            width: 100%;
            border: 1px solid rgba(255,255,255,.1);
            background: transparent;
            color: #d1d5db;
            border-radius: 8px;
            padding: 9px;
            font-size: 12px;
            cursor: pointer;
        }

        .sidebar-logout:hover {
            background: rgba(255,255,255,.07);
            color: #fff;
        }

        .sidebar-overlay {
            display: none;
        }

        .sidebar-toggle {
            display: none;
        }

        /* ================= TOPBAR ================= */

        .topbar {
            height: 70px;
            background: #111827;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            margin-left: 255px;
        }

        .brand {
            font-size: 21px;
            font-weight: 700;
        }

        .brand small {
            display: block;
            font-size: 11px;
            font-weight: 400;
            opacity: .65;
            margin-top: 2px;
        }

        .admin-badge {
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.15);
            padding: 8px 13px;
            border-radius: 20px;
            font-size: 13px;
        }

        /* ================= MAIN ================= */

        .container {
            max-width: 1500px;
            margin-left: 255px;
            padding: 24px;
        }

        .page-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }

        .page-title h1 {
            margin: 0;
            font-size: 27px;
        }

        .page-title p {
            margin: 5px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .save-btn {
            border: 0;
            background: #2563eb;
            color: white;
            padding: 10px 17px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .save-btn:hover {
            background: #1d4ed8;
        }

        .save-btn:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .settings-layout {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 20px;
            align-items: start;
        }

        .settings-menu {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 13px;
            padding: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,.04);
            position: sticky;
            top: 20px;
        }

        .settings-menu button {
            width: 100%;
            border: 0;
            background: transparent;
            color: #6b7280;
            text-align: left;
            padding: 11px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .settings-menu button:hover {
            background: #f8fafc;
            color: #172033;
        }

        .settings-menu button.active {
            background: #2563eb;
            color: white;
        }

        .settings-content {
            min-width: 0;
        }

        .settings-section {
            display: none;
        }

        .settings-section.active {
            display: block;
        }

        .section-heading {
            margin-bottom: 16px;
        }

        .section-heading h2 {
            margin: 0;
            font-size: 20px;
        }

        .section-heading p {
            margin: 5px 0 0;
            color: #6b7280;
            font-size: 13px;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .settings-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 13px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,.04);
        }

        .settings-card.full {
            grid-column: 1 / -1;
        }

        .settings-card h3 {
            margin: 0;
            font-size: 15px;
        }

        .settings-card-description {
            margin: 5px 0 18px;
            color: #6b7280;
            font-size: 12px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .form-group {
            min-width: 0;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #374151;
            font-size: 12px;
            font-weight: 700;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 11px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: white;
            color: #172033;
            font-size: 13px;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #2563eb;
        }

        .form-help {
            margin-top: 5px;
            color: #9ca3af;
            font-size: 11px;
        }

        .switch-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #edf0f4;
        }

        .switch-row:last-child {
            border-bottom: 0;
        }

        .switch-title {
            font-size: 13px;
            font-weight: 600;
        }

        .switch-description {
            margin-top: 3px;
            color: #6b7280;
            font-size: 11px;
        }

        .switch {
            position: relative;
            width: 44px;
            height: 24px;
            flex-shrink: 0;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .switch-slider {
            position: absolute;
            inset: 0;
            background: #d1d5db;
            border-radius: 20px;
            cursor: pointer;
            transition: .2s;
        }

        .switch-slider:before {
            content: "";
            position: absolute;
            width: 18px;
            height: 18px;
            left: 3px;
            top: 3px;
            background: white;
            border-radius: 50%;
            transition: .2s;
        }

        .switch input:checked + .switch-slider {
            background: #2563eb;
        }

        .switch input:checked + .switch-slider:before {
            transform: translateX(20px);
        }

        .notification-table {
            width: 100%;
            border-collapse: collapse;
        }

        .notification-table th {
            background: #f8fafc;
            padding: 12px;
            color: #6b7280;
            font-size: 11px;
            text-align: left;
        }

        .notification-table td {
            padding: 13px 12px;
            border-top: 1px solid #edf0f4;
            font-size: 12px;
        }

        .notification-table th:not(:first-child),
        .notification-table td:not(:first-child) {
            text-align: center;
        }

        .logo-box {
            min-height: 120px;
            border: 1px dashed #d1d5db;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            margin-bottom: 12px;
            overflow: hidden;
        }

        .logo-box img {
            max-width: 100%;
            max-height: 100px;
            object-fit: contain;
        }

        .upload-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
            align-items: center;
        }

        .upload-row input {
            width: 100%;
        }

        .upload-btn {
            border: 0;
            background: #111827;
            color: white;
            border-radius: 8px;
            padding: 10px 13px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        .test-btn {
            border: 1px solid #d1d5db;
            background: white;
            color: #172033;
            border-radius: 8px;
            padding: 10px 14px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        .test-btn:hover {
            background: #f8fafc;
        }

        .alert {
            display: none;
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 13px;
        }

        .alert.success {
            display: block;
            background: #dcfce7;
            color: #166534;
        }

        .alert.error {
            display: block;
            background: #fee2e2;
            color: #991b1b;
        }

        .loading {
            text-align: center;
            padding: 50px;
            color: #6b7280;
        }

        /* ================= MOBILE ================= */

        @media(max-width: 1000px) {
            .settings-layout {
                grid-template-columns: 1fr;
            }

            .settings-menu {
                position: static;
                display: flex;
                overflow-x: auto;
                gap: 4px;
            }

            .settings-menu button {
                white-space: nowrap;
                width: auto;
            }

            .settings-grid {
                grid-template-columns: 1fr;
            }

            .settings-card.full {
                grid-column: auto;
            }
        }

        @media(max-width: 900px) {

            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform .2s ease;
                box-shadow: 8px 0 25px rgba(0,0,0,.18);
            }

            .admin-sidebar.sidebar-open {
                transform: translateX(0);
            }

            .sidebar-overlay.sidebar-open {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,.45);
                z-index: 1090;
            }

            .sidebar-toggle {
                display: inline-flex;
                width: 38px;
                height: 38px;
                border: 1px solid rgba(255,255,255,.15);
                background: transparent;
                color: #fff;
                border-radius: 8px;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                cursor: pointer;
            }

            .topbar {
                margin-left: 0;
                padding: 0 15px;
            }

            .container {
                margin-left: 0;
                padding: 15px;
            }
        }

        @media(max-width: 650px) {

            .page-title {
                align-items: flex-start;
                gap: 12px;
                flex-direction: column;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .upload-row {
                grid-template-columns: 1fr;
            }

            .admin-badge {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="admin-layout">

    {{-- SIDEBAR --}}
    <aside class="admin-sidebar" id="adminSidebar">

        <div class="sidebar-brand">
            <div class="sidebar-logo">SV</div>

            <div>
                <strong>Smart Vadodara</strong>
                <small>AI Civic Command Center</small>
            </div>
        </div>

        <nav class="sidebar-nav">

            <div class="sidebar-section-title">OPERATIONS</div>

            <a href="/admin/dashboard" class="sidebar-link">
                <span>🏠</span>
                <span>Dashboard</span>
            </a>

            <a href="/admin/dashboard#incidentsSection" class="sidebar-link">
                <span>📋</span>
                <span>Incidents</span>
            </a>

            <a href="/admin/dashboard#mapSection" class="sidebar-link">
                <span>🗺️</span>
                <span>Live Map</span>
            </a>

            <a href="/admin/dashboard#slaSection" class="sidebar-link">
                <span>⏱️</span>
                <span>SLA & Escalations</span>
            </a>

            <a href="/admin/dashboard#analyticsSection" class="sidebar-link">
                <span>📊</span>
                <span>Analytics</span>
            </a>

            <a href="/admin/dashboard#notificationsSection" class="sidebar-link">
                <span>🔔</span>
                <span>Notifications</span>
            </a>

            <div class="sidebar-section-title">MASTER MANAGEMENT</div>

            <a href="/admin/master" class="sidebar-link">
                <span>⚙️</span>
                <span>Master Management</span>
            </a>

            <a href="/admin/master" class="sidebar-sub-link">
                <span>🏢</span>
                <span>Departments</span>
            </a>

            <a href="/admin/master" class="sidebar-sub-link">
                <span>🏷️</span>
                <span>Categories</span>
            </a>

            <a href="/admin/master" class="sidebar-sub-link">
                <span>🏘️</span>
                <span>Wards</span>
            </a>

            <a href="/admin/master" class="sidebar-sub-link">
                <span>👥</span>
                <span>Users</span>
            </a>

            <div class="sidebar-section-title">SYSTEM</div>

            <a href="/admin/settings" class="sidebar-link active">
                <span>⚙️</span>
                <span>Settings</span>
            </a>

        </nav>

        <div class="sidebar-bottom">

            <div class="sidebar-admin">
                <div class="admin-avatar">A</div>

                <div>
                    <strong>Administrator</strong>
                    <small>System Admin</small>
                </div>
            </div>

            <button type="button"
                    class="sidebar-logout"
                    onclick="adminLogout()">
                🚪 Logout
            </button>

        </div>

    </aside>

    <div class="sidebar-overlay"
         id="sidebarOverlay"
         onclick="closeAdminSidebar()">
    </div>

    {{-- TOPBAR --}}
    <header class="topbar">

        <button type="button"
                class="sidebar-toggle"
                onclick="toggleAdminSidebar()">
            ☰
        </button>

        <div class="brand">
            Smart Vadodara
            <small>AI Civic Command Center</small>
        </div>

        <div class="admin-badge">
            Administrator
        </div>

    </header>

    {{-- MAIN --}}
    <main class="container">

        <div class="page-title">

            <div>
                <h1>System Settings</h1>

                <p>
                    Configure application, AI, GIS, notification and security settings
                </p>
            </div>

            <button type="button"
                    id="saveAllSettings"
                    class="save-btn">
                💾 Save Changes
            </button>

        </div>

        <div id="settingsAlert"
             class="alert">
        </div>

        <div id="settingsLoading"
             class="settings-card loading">
            Loading settings...
        </div>

        <div id="settingsApp"
             style="display:none;">

            <div class="settings-layout">

                {{-- SETTINGS MENU --}}
                <div class="settings-menu">

                    <button type="button"
                            class="settings-tab active"
                            data-section="general">
                        🏢 General
                    </button>

                    <button type="button"
                            class="settings-tab"
                            data-section="email">
                        ✉️ Email
                    </button>

                    <button type="button"
                            class="settings-tab"
                            data-section="whatsapp">
                        💬 WhatsApp
                    </button>

                    <button type="button"
                            class="settings-tab"
                            data-section="notifications">
                        🔔 Notifications
                    </button>

                    <button type="button"
                            class="settings-tab"
                            data-section="ai">
                        🤖 AI
                    </button>

                    <button type="button"
                            class="settings-tab"
                            data-section="gis">
                        🗺️ GIS
                    </button>

                    <button type="button"
                            class="settings-tab"
                            data-section="rewards">
                        🏆 Rewards
                    </button>

                    <button type="button"
                            class="settings-tab"
                            data-section="security">
                        🔐 Security
                    </button>

                </div>

                {{-- SETTINGS CONTENT --}}
                <div class="settings-content">

                    {{-- GENERAL --}}
                    <section id="section-general"
                             class="settings-section active">

                        <div class="section-heading">
                            <h2>General & Organization</h2>

                            <p>
                                Configure platform identity and organization information.
                            </p>
                        </div>

                        <div class="settings-grid">

                            <div class="settings-card">

                                <h3>Application Identity</h3>

                                <p class="settings-card-description">
                                    Basic application information.
                                </p>

                                <div class="form-grid">

                                    <div class="form-group">
                                        <label>Application Name</label>
                                        <input type="text"
                                               data-setting="app.name"
                                               placeholder="Smart Vadodara Connect">
                                    </div>

                                    <div class="form-group">
                                        <label>Short Name</label>
                                        <input type="text"
                                               data-setting="app.short_name"
                                               placeholder="SVC">
                                    </div>

                                    <div class="form-group full">
                                        <label>Corporation Name</label>
                                        <input type="text"
                                               data-setting="app.corporation_name"
                                               placeholder="Vadodara Municipal Corporation">
                                    </div>

                                    <div class="form-group">
                                        <label>Contact Email</label>
                                        <input type="email"
                                               data-setting="app.contact_email">
                                    </div>

                                    <div class="form-group">
                                        <label>Contact Phone</label>
                                        <input type="text"
                                               data-setting="app.contact_phone">
                                    </div>

                                    <div class="form-group full">
                                        <label>Website</label>
                                        <input type="url"
                                               data-setting="app.website">
                                    </div>

                                </div>

                            </div>

                            <div class="settings-card">

                                <h3>Branding</h3>

                                <p class="settings-card-description">
                                    Manage application logo and favicon.
                                </p>

                                <label class="form-group">
                                    <span>Application Logo</span>
                                </label>

                                <div id="logoPreview"
                                     class="logo-box">
                                    <span style="color:#9ca3af;font-size:12px;">
                                        No logo configured
                                    </span>
                                </div>

                                <div class="upload-row">
                                    <input type="file"
                                           id="logoUpload"
                                           accept=".jpg,.jpeg,.png,.webp,.svg">

                                    <button type="button"
                                            id="uploadLogo"
                                            class="upload-btn">
                                        Upload Logo
                                    </button>
                                </div>

                                <div style="height:20px;"></div>

                                <label class="form-group">
                                    <span>Favicon</span>
                                </label>

                                <div id="faviconPreview"
                                     class="logo-box"
                                     style="min-height:80px;">
                                    <span style="color:#9ca3af;font-size:12px;">
                                        No favicon configured
                                    </span>
                                </div>

                                <div class="upload-row">
                                    <input type="file"
                                           id="faviconUpload"
                                           accept=".ico,.png,.jpg,.jpeg,.webp,.svg">

                                    <button type="button"
                                            id="uploadFavicon"
                                            class="upload-btn">
                                        Upload Favicon
                                    </button>
                                </div>

                            </div>

                        </div>

                    </section>

                    {{-- EMAIL --}}
                    <section id="section-email"
                             class="settings-section">

                        <div class="section-heading">
                            <h2>Email / SMTP</h2>

                            <p>
                                Configure email delivery for civic notifications.
                            </p>
                        </div>

                        <div class="settings-grid">

                            <div class="settings-card">

                                <h3>SMTP Configuration</h3>

                                <p class="settings-card-description">
                                    SMTP credentials are securely stored.
                                </p>

                                <div class="switch-row">

                                    <div>
                                        <div class="switch-title">
                                            Enable Email Notifications
                                        </div>

                                        <div class="switch-description">
                                            Allow the platform to send email notifications.
                                        </div>
                                    </div>

                                    <label class="switch">
                                        <input type="checkbox"
                                               data-setting="email.enabled">
                                        <span class="switch-slider"></span>
                                    </label>

                                </div>

                                <div style="height:15px;"></div>

                                <div class="form-grid">

                                    <div class="form-group full">
                                        <label>SMTP Host</label>
                                        <input type="text"
                                               data-setting="email.smtp_host">
                                    </div>

                                    <div class="form-group">
                                        <label>SMTP Port</label>
                                        <input type="number"
                                               data-setting="email.smtp_port">
                                    </div>

                                    <div class="form-group">
                                        <label>Encryption</label>

                                        <select data-setting="email.encryption">
                                            <option value="">None</option>
                                            <option value="tls">TLS</option>
                                            <option value="ssl">SSL</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>SMTP Username</label>
                                        <input type="text"
                                               data-setting="email.smtp_username">
                                    </div>

                                    <div class="form-group">
                                        <label>SMTP Password</label>
                                        <input type="password"
                                               data-setting="email.smtp_password"
                                               data-secret="true"
                                               placeholder="Leave blank to keep existing">
                                    </div>

                                    <div class="form-group">
                                        <label>From Name</label>
                                        <input type="text"
                                               data-setting="email.from_name">
                                    </div>

                                    <div class="form-group">
                                        <label>From Email</label>
                                        <input type="email"
                                               data-setting="email.from_address">
                                    </div>

                                </div>

                            </div>

                            <div class="settings-card">

                                <h3>Test Email</h3>

                                <p class="settings-card-description">
                                    Test the configured SMTP connection.
                                </p>

                                <div class="form-group">
                                    <label>Recipient Email</label>

                                    <input type="email"
                                           id="testEmailAddress"
                                           placeholder="admin@example.com">
                                </div>

                                <div style="margin-top:15px;">
                                    <button type="button"
                                            id="sendTestEmail"
                                            class="test-btn">
                                        ✉️ Send Test Email
                                    </button>
                                </div>

                            </div>

                        </div>

                    </section>

                    {{-- WHATSAPP --}}
                    <section id="section-whatsapp"
                             class="settings-section">

                        <div class="section-heading">
                            <h2>WhatsApp</h2>

                            <p>
                                Configure WhatsApp notification delivery.
                            </p>
                        </div>

                        <div class="settings-grid">

                            <div class="settings-card">

                                <h3>Provider Configuration</h3>

                                <p class="settings-card-description">
                                    Configure your WhatsApp provider/API.
                                </p>

                                <div class="switch-row">

                                    <div>
                                        <div class="switch-title">
                                            Enable WhatsApp Notifications
                                        </div>

                                        <div class="switch-description">
                                            Allow WhatsApp notifications to be sent.
                                        </div>
                                    </div>

                                    <label class="switch">
                                        <input type="checkbox"
                                               data-setting="whatsapp.enabled">
                                        <span class="switch-slider"></span>
                                    </label>

                                </div>

                                <div style="height:15px;"></div>

                                <div class="form-grid">

                                    <div class="form-group">
                                        <label>Provider</label>

                                        <select data-setting="whatsapp.provider">
                                            <option value="">Select Provider</option>
                                            <option value="meta_cloud">
                                                Meta WhatsApp Cloud API
                                            </option>
                                            <option value="generic">
                                                Generic API
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Sender Number</label>
                                        <input type="text"
                                               data-setting="whatsapp.sender_number">
                                    </div>

                                    <div class="form-group full">
                                        <label>API URL</label>
                                        <input type="url"
                                               data-setting="whatsapp.api_url">
                                    </div>

                                    <div class="form-group full">
                                        <label>API Key / Access Token</label>
                                        <input type="password"
                                               data-setting="whatsapp.api_key"
                                               data-secret="true"
                                               placeholder="Leave blank to keep existing">
                                    </div>

                                </div>

                            </div>

                            <div class="settings-card">

                                <h3>Test WhatsApp</h3>

                                <p class="settings-card-description">
                                    Test the configured WhatsApp provider.
                                </p>

                                <div class="form-group">
                                    <label>Mobile Number</label>

                                    <input type="text"
                                           id="testWhatsappNumber"
                                           placeholder="+919XXXXXXXXX">
                                </div>

                                <div style="margin-top:15px;">
                                    <button type="button"
                                            id="sendTestWhatsapp"
                                            class="test-btn">
                                        💬 Send Test WhatsApp
                                    </button>
                                </div>

                            </div>

                        </div>

                    </section>

                    {{-- NOTIFICATIONS --}}
                    <section id="section-notifications"
                             class="settings-section">

                        <div class="section-heading">
                            <h2>Notification Events</h2>

                            <p>
                                Select which channels should receive each civic event.
                            </p>
                        </div>

                        <div class="settings-card full">

                            <div style="overflow-x:auto;">

                                <table class="notification-table">

                                    <thead>
                                        <tr>
                                            <th>Event</th>
                                            <th>Email</th>
                                            <th>WhatsApp</th>
                                            <th>In-App</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @php
                                            $notificationEvents = [
                                                ['key'=>'complaint_created','label'=>'Complaint Created'],
                                                ['key'=>'complaint_assigned','label'=>'Complaint Assigned'],
                                                ['key'=>'status_changed','label'=>'Status Changed'],
                                                ['key'=>'sla_warning','label'=>'SLA Warning'],
                                                ['key'=>'sla_breached','label'=>'SLA Breached'],
                                                ['key'=>'resolved','label'=>'Complaint Resolved'],
                                                ['key'=>'closed','label'=>'Complaint Closed'],
                                                ['key'=>'reopened','label'=>'Complaint Reopened'],
                                            ];
                                        @endphp

                                        @foreach($notificationEvents as $event)

                                            <tr>

                                                <td>
                                                    <strong>
                                                        {{ $event['label'] }}
                                                    </strong>
                                                </td>

                                                <td>
                                                    <label class="switch">
                                                        <input type="checkbox"
                                                               data-setting="notifications.{{ $event['key'] }}.email">
                                                        <span class="switch-slider"></span>
                                                    </label>
                                                </td>

                                                <td>
                                                    <label class="switch">
                                                        <input type="checkbox"
                                                               data-setting="notifications.{{ $event['key'] }}.whatsapp">
                                                        <span class="switch-slider"></span>
                                                    </label>
                                                </td>

                                                <td>
                                                    <label class="switch">
                                                        <input type="checkbox"
                                                               data-setting="notifications.{{ $event['key'] }}.in_app">
                                                        <span class="switch-slider"></span>
                                                    </label>
                                                </td>

                                            </tr>

                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </section>

                    {{-- AI --}}
                    <section id="section-ai"
                             class="settings-section">

                        <div class="section-heading">
                            <h2>AI & Computer Vision</h2>

                            <p>
                                Configure AI classification, YOLO and duplicate detection.
                            </p>
                        </div>

                        <div class="settings-grid">

                            <div class="settings-card">

                                <h3>AI Classification</h3>

                                <p class="settings-card-description">
                                    Configure automated complaint analysis.
                                </p>

                                <div class="switch-row">

                                    <div>
                                        <div class="switch-title">AI Enabled</div>

                                        <div class="switch-description">
                                            Enable automated AI complaint processing.
                                        </div>
                                    </div>

                                    <label class="switch">
                                        <input type="checkbox"
                                               data-setting="ai.enabled">
                                        <span class="switch-slider"></span>
                                    </label>

                                </div>

                                <div style="height:15px;"></div>

                                <div class="form-grid">

                                    <div class="form-group">
                                        <label>Confidence Threshold</label>
                                        <input type="number"
                                               min="0"
                                               max="1"
                                               step="0.01"
                                               data-setting="ai.confidence_threshold">
                                    </div>

                                    <div class="form-group">
                                        <label>YOLO Confidence</label>
                                        <input type="number"
                                               min="0"
                                               max="1"
                                               step="0.01"
                                               data-setting="ai.yolo_confidence">
                                    </div>

                                </div>

                            </div>

                            <div class="settings-card">

                                <h3>Duplicate Detection</h3>

                                <p class="settings-card-description">
                                    Configure civic incident clustering.
                                </p>

                                <div class="form-grid">

                                    <div class="form-group">
                                        <label>Duplicate Radius (meters)</label>
                                        <input type="number"
                                               min="1"
                                               data-setting="ai.duplicate_radius">
                                    </div>

                                    <div class="form-group">
                                        <label>Duplicate Window (hours)</label>
                                        <input type="number"
                                               min="1"
                                               data-setting="ai.duplicate_window_hours">
                                    </div>

                                </div>

                            </div>

                        </div>

                    </section>

                    {{-- GIS --}}
                    <section id="section-gis"
                             class="settings-section">

                        <div class="section-heading">
                            <h2>GIS & Location</h2>

                            <p>
                                Configure map defaults and GPS validation.
                            </p>
                        </div>

                        <div class="settings-card">

                            <div class="switch-row">

                                <div>
                                    <div class="switch-title">
                                        GIS Enabled
                                    </div>

                                    <div class="switch-description">
                                        Enable GIS-based ward detection.
                                    </div>
                                </div>

                                <label class="switch">
                                    <input type="checkbox"
                                           data-setting="gis.enabled">
                                    <span class="switch-slider"></span>
                                </label>

                            </div>

                            <div style="height:18px;"></div>

                            <div class="form-grid">

                                <div class="form-group">
                                    <label>Default Latitude</label>
                                    <input type="number"
                                           step="0.000001"
                                           data-setting="gis.default_latitude">
                                </div>

                                <div class="form-group">
                                    <label>Default Longitude</label>
                                    <input type="number"
                                           step="0.000001"
                                           data-setting="gis.default_longitude">
                                </div>

                                <div class="form-group">
                                    <label>Default Zoom</label>
                                    <input type="number"
                                           min="1"
                                           max="22"
                                           data-setting="gis.default_zoom">
                                </div>

                                <div class="form-group">
                                    <label>GPS Accuracy Threshold (m)</label>
                                    <input type="number"
                                           min="1"
                                           data-setting="gis.gps_accuracy_threshold">
                                </div>

                            </div>

                        </div>

                    </section>

                    {{-- REWARDS --}}
                    <section id="section-rewards"
                             class="settings-section">

                        <div class="section-heading">
                            <h2>Rewards & Citizen Engagement</h2>

                            <p>
                                Configure civic points and digital certificates.
                            </p>
                        </div>

                        <div class="settings-grid">

                            <div class="settings-card">

                                <h3>Civic Rewards</h3>

                                <p class="settings-card-description">
                                    Reward citizens for verified civic participation.
                                </p>

                                <div class="switch-row">

                                    <div>
                                        <div class="switch-title">
                                            Rewards Enabled
                                        </div>

                                        <div class="switch-description">
                                            Enable civic points.
                                        </div>
                                    </div>

                                    <label class="switch">
                                        <input type="checkbox"
                                               data-setting="rewards.enabled">
                                        <span class="switch-slider"></span>
                                    </label>

                                </div>

                                <div style="height:15px;"></div>

                                <div class="form-group">
                                    <label>Points Per Verification</label>

                                    <input type="number"
                                           min="0"
                                           data-setting="rewards.points_per_verification">
                                </div>

                            </div>

                            <div class="settings-card">

                                <h3>Digital Certificate</h3>

                                <p class="settings-card-description">
                                    Control Jagruk Nagrik certificate generation.
                                </p>

                                <div class="switch-row">

                                    <div>
                                        <div class="switch-title">
                                            Certificate Enabled
                                        </div>

                                        <div class="switch-description">
                                            Allow verified citizens to receive certificates.
                                        </div>
                                    </div>

                                    <label class="switch">
                                        <input type="checkbox"
                                               data-setting="rewards.certificate_enabled">
                                        <span class="switch-slider"></span>
                                    </label>

                                </div>

                            </div>

                        </div>

                    </section>

                    {{-- SECURITY --}}
                    <section id="section-security"
                             class="settings-section">

                        <div class="section-heading">
                            <h2>Security</h2>

                            <p>
                                Configure authentication and session security.
                            </p>
                        </div>

                        <div class="settings-card">

                            <div class="form-grid">

                                <div class="form-group">
                                    <label>Session Timeout (minutes)</label>

                                    <input type="number"
                                           min="1"
                                           data-setting="security.session_timeout">

                                    <div class="form-help">
                                        Inactive session expiry duration.
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Login Attempt Limit</label>

                                    <input type="number"
                                           min="1"
                                           data-setting="security.login_attempt_limit">

                                    <div class="form-help">
                                        Maximum failed login attempts.
                                    </div>
                                </div>

                            </div>

                        </div>

                    </section>

                </div>

            </div>

        </div>

    </main>

</div>

<script>
    function toggleAdminSidebar() {
        document
            .getElementById('adminSidebar')
            ?.classList.toggle('sidebar-open');

        document
            .getElementById('sidebarOverlay')
            ?.classList.toggle('sidebar-open');
    }

    function closeAdminSidebar() {
        document
            .getElementById('adminSidebar')
            ?.classList.remove('sidebar-open');

        document
            .getElementById('sidebarOverlay')
            ?.classList.remove('sidebar-open');
    }

    function adminLogout() {
        if (!confirm('Are you sure you want to logout?')) {
            return;
        }

        localStorage.removeItem('smart_vadodara_admin_token');
        localStorage.removeItem('smart_vadodara_admin_user');

        window.location.href = '/admin/login';
    }

    window.toggleAdminSidebar = toggleAdminSidebar;
    window.closeAdminSidebar = closeAdminSidebar;
    window.adminLogout = adminLogout;
</script>

</body>
</html>

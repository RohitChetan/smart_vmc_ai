const API_BASE = '/api/admin/settings';

let settingsMap = {};
let settingsLoaded = false;

function getAdminToken() {
    return localStorage.getItem('smart_vadodara_admin_token');
}

function authHeaders() {
    const token = getAdminToken();

    return {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...(token ? {
            'Authorization': `Bearer ${token}`
        } : {})
    };
}

async function apiRequest(url, options = {}) {

    const response = await fetch(url, {
        ...options,
        headers: {
            ...authHeaders(),
            ...(options.headers || {})
        }
    });

    let data = null;

    try {
        data = await response.json();
    } catch (e) {
        data = null;
    }

    if (response.status === 401) {
        localStorage.removeItem('smart_vadodara_admin_token');
        localStorage.removeItem('smart_vadodara_admin_user');

        window.location.href = '/admin/login';

        throw new Error('Unauthorized');
    }

    if (!response.ok) {
        throw new Error(
            data?.message ||
            data?.error ||
            `Request failed (${response.status})`
        );
    }

    return data;
}


/* =========================================================
   ALERT
   ========================================================= */

function showAlert(message, type = 'success') {

    const box = document.getElementById('settingsAlert');

    if (!box) return;

    box.className = `alert ${type}`;
    box.textContent = message;

    box.style.display = 'block';

    clearTimeout(window.settingsAlertTimer);

    window.settingsAlertTimer = setTimeout(() => {
        box.style.display = 'none';
    }, 5000);
}


/* =========================================================
   SETTINGS NORMALIZATION
   ========================================================= */

function normalizeSettings(response) {

    const source =
        response?.data ??
        response?.settings ??
        response ??
        {};

    const result = {};

    /*
     * Expected backend:
     *
     * {
     *   general: [...],
     *   email: [...],
     *   ...
     * }
     *
     * But this also supports:
     *
     * {
     *   data: {
     *      general: [...]
     *   }
     * }
     */

    if (Array.isArray(source)) {

        source.forEach(setting => {

            if (!setting?.key) return;

            result[setting.key] = setting;
        });

    } else {

        Object.values(source).forEach(group => {

            if (!Array.isArray(group)) return;

            group.forEach(setting => {

                if (!setting?.key) return;

                result[setting.key] = setting;
            });

        });
    }

    return result;
}


/* =========================================================
   LOAD SETTINGS
   ========================================================= */

async function loadSettings() {

    const loading = document.getElementById('settingsLoading');
    const app = document.getElementById('settingsApp');

    try {

        if (loading) {
            loading.style.display = 'block';
        }

        if (app) {
            app.style.display = 'none';
        }

        const response = await apiRequest(API_BASE);

        settingsMap = normalizeSettings(response);

        populateSettings();

        settingsLoaded = true;

        if (loading) {
            loading.style.display = 'none';
        }

        if (app) {
            app.style.display = 'block';
        }

    } catch (error) {

        console.error('Settings load error:', error);

        if (loading) {
            loading.innerHTML = `
                <div style="color:#b91c1c;">
                    <strong>Unable to load settings</strong>
                    <div style="margin-top:7px;font-size:12px;">
                        ${escapeHtml(error.message)}
                    </div>
                    <button
                        type="button"
                        onclick="loadSettings()"
                        style="
                            margin-top:15px;
                            border:0;
                            background:#2563eb;
                            color:#fff;
                            padding:9px 14px;
                            border-radius:7px;
                            cursor:pointer;
                        "
                    >
                        Retry
                    </button>
                </div>
            `;
        }

        showAlert(
            `Unable to load settings: ${error.message}`,
            'error'
        );
    }
}


/* =========================================================
   POPULATE FORM
   ========================================================= */

function populateSettings() {

    document
        .querySelectorAll('[data-setting]')
        .forEach(element => {

            const key = element.dataset.setting;

            const setting = settingsMap[key];

            if (!setting) {
                return;
            }

            /*
             * Encrypted settings intentionally don't return
             * their real value from backend.
             */

            if (setting.has_value && setting.is_encrypted) {

                element.value = '';

                if (element.dataset.secret === 'true') {

                    element.placeholder =
                        '••••••••••  Existing value saved';
                }

                return;
            }

            let value = setting.value;

            /*
             * Backend may return typed_value.
             */

            if (
                value === undefined ||
                value === null
            ) {
                value = setting.typed_value;
            }

            if (element.type === 'checkbox') {

                element.checked =
                    value === true ||
                    value === 1 ||
                    value === '1' ||
                    value === 'true';

            } else {

                if (
                    typeof value === 'object' &&
                    value !== null
                ) {
                    value = JSON.stringify(value);
                }

                element.value =
                    value === null ||
                    value === undefined
                        ? ''
                        : value;
            }
        });

    updateBrandingPreview();
}


/* =========================================================
   COLLECT SETTINGS
   ========================================================= */

function collectSettings() {

    const settings = [];

    document
        .querySelectorAll('[data-setting]')
        .forEach(element => {

            const key = element.dataset.setting;

            let value;

            if (element.type === 'checkbox') {

                value = element.checked;

            } else {

                value = element.value;
            }

            /*
             * Do NOT overwrite encrypted secrets when the
             * admin leaves the password/token field blank.
             */

            if (
                element.dataset.secret === 'true' &&
                value === ''
            ) {
                return;
            }

            settings.push({
                key,
                value
            });
        });

    return settings;
}


/* =========================================================
   SAVE ALL
   ========================================================= */

async function saveAllSettings() {

    const button = document.getElementById('saveAllSettings');

    if (!button) return;

    const originalText = button.innerHTML;

    try {

        button.disabled = true;
        button.innerHTML = '⏳ Saving...';

        const settings = collectSettings();

        await apiRequest(API_BASE, {
            method: 'PUT',
            body: JSON.stringify({
                settings
            })
        });

        showAlert(
            'Settings saved successfully.',
            'success'
        );

        /*
         * Reload so encrypted values / typed values are
         * synchronized with backend.
         */

        await loadSettings();

    } catch (error) {

        console.error('Settings save error:', error);

        showAlert(
            `Unable to save settings: ${error.message}`,
            'error'
        );

    } finally {

        button.disabled = false;
        button.innerHTML = originalText;
    }
}


/* =========================================================
   TABS
   ========================================================= */

function setupTabs() {

    document
        .querySelectorAll('.settings-tab')
        .forEach(button => {

            button.addEventListener('click', () => {

                const sectionName =
                    button.dataset.section;

                document
                    .querySelectorAll('.settings-tab')
                    .forEach(tab => {
                        tab.classList.remove('active');
                    });

                document
                    .querySelectorAll('.settings-section')
                    .forEach(section => {
                        section.classList.remove('active');
                    });

                button.classList.add('active');

                const section =
                    document.getElementById(
                        `section-${sectionName}`
                    );

                if (section) {
                    section.classList.add('active');
                }
            });
        });
}


/* =========================================================
   LOGO UPLOAD
   ========================================================= */

async function uploadLogo() {

    const input =
        document.getElementById('logoUpload');

    const button =
        document.getElementById('uploadLogo');

    if (!input?.files?.length) {

        showAlert(
            'Please select a logo first.',
            'error'
        );

        return;
    }

    const file = input.files[0];

    const formData = new FormData();

    formData.append('logo', file);

    const originalText = button.innerHTML;

    try {

        button.disabled = true;
        button.innerHTML = '⏳ Uploading...';

        const response = await fetch(
            `${API_BASE}/upload/logo`,
            {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    ...(getAdminToken()
                        ? {
                            'Authorization':
                                `Bearer ${getAdminToken()}`
                        }
                        : {})
                },
                body: formData
            }
        );

        const data = await response.json();

        if (response.status === 401) {

            localStorage.removeItem(
                'smart_vadodara_admin_token'
            );

            localStorage.removeItem(
                'smart_vadodara_admin_user'
            );

            window.location.href = '/admin/login';

            return;
        }

        if (!response.ok) {

            throw new Error(
                data?.message ||
                'Logo upload failed'
            );
        }

        showAlert(
            'Logo uploaded successfully.',
            'success'
        );

        if (data?.url) {

            const preview =
                document.getElementById('logoPreview');

            if (preview) {

                preview.innerHTML = `
                    <img
                        src="${escapeAttribute(data.url)}"
                        alt="Application Logo"
                    >
                `;
            }
        }

        input.value = '';

        await loadSettings();

    } catch (error) {

        console.error(error);

        showAlert(
            `Logo upload failed: ${error.message}`,
            'error'
        );

    } finally {

        button.disabled = false;
        button.innerHTML = originalText;
    }
}


/* =========================================================
   FAVICON UPLOAD
   ========================================================= */

async function uploadFavicon() {

    const input =
        document.getElementById('faviconUpload');

    const button =
        document.getElementById('uploadFavicon');

    if (!input?.files?.length) {

        showAlert(
            'Please select a favicon first.',
            'error'
        );

        return;
    }

    const file = input.files[0];

    const formData = new FormData();

    formData.append('favicon', file);

    const originalText = button.innerHTML;

    try {

        button.disabled = true;
        button.innerHTML = '⏳ Uploading...';

        const response = await fetch(
            `${API_BASE}/upload/favicon`,
            {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    ...(getAdminToken()
                        ? {
                            'Authorization':
                                `Bearer ${getAdminToken()}`
                        }
                        : {})
                },
                body: formData
            }
        );

        const data = await response.json();

        if (response.status === 401) {

            localStorage.removeItem(
                'smart_vadodara_admin_token'
            );

            localStorage.removeItem(
                'smart_vadodara_admin_user'
            );

            window.location.href = '/admin/login';

            return;
        }

        if (!response.ok) {

            throw new Error(
                data?.message ||
                'Favicon upload failed'
            );
        }

        showAlert(
            'Favicon uploaded successfully.',
            'success'
        );

        if (data?.url) {

            const preview =
                document.getElementById('faviconPreview');

            if (preview) {

                preview.innerHTML = `
                    <img
                        src="${escapeAttribute(data.url)}"
                        alt="Favicon"
                        style="max-height:55px;"
                    >
                `;
            }
        }

        input.value = '';

        await loadSettings();

    } catch (error) {

        console.error(error);

        showAlert(
            `Favicon upload failed: ${error.message}`,
            'error'
        );

    } finally {

        button.disabled = false;
        button.innerHTML = originalText;
    }
}


/* =========================================================
   BRANDING PREVIEW
   ========================================================= */

function updateBrandingPreview() {

    const logo =
        settingsMap['app.logo'];

    const favicon =
        settingsMap['app.favicon'];

    const logoPreview =
        document.getElementById('logoPreview');

    const faviconPreview =
        document.getElementById('faviconPreview');


    if (
        logoPreview &&
        logo?.value &&
        typeof logo.value === 'string'
    ) {

        const url =
            normalizeAssetUrl(logo.value);

        logoPreview.innerHTML = `
            <img
                src="${escapeAttribute(url)}"
                alt="Application Logo"
            >
        `;
    }


    if (
        faviconPreview &&
        favicon?.value &&
        typeof favicon.value === 'string'
    ) {

        const url =
            normalizeAssetUrl(favicon.value);

        faviconPreview.innerHTML = `
            <img
                src="${escapeAttribute(url)}"
                alt="Favicon"
                style="max-height:55px;"
            >
        `;
    }
}


function normalizeAssetUrl(value) {

    if (!value) return '';

    if (
        value.startsWith('http://') ||
        value.startsWith('https://') ||
        value.startsWith('/')
    ) {
        return value;
    }

    return `/${value}`;
}


/* =========================================================
   TEST EMAIL
   ========================================================= */

async function sendTestEmail() {

    const input =
        document.getElementById('testEmailAddress');

    const button =
        document.getElementById('sendTestEmail');

    const email =
        input?.value?.trim();

    if (!email) {

        showAlert(
            'Enter a recipient email address.',
            'error'
        );

        return;
    }

    const originalText = button.innerHTML;

    try {

        button.disabled = true;
        button.innerHTML = '⏳ Sending...';

        await apiRequest(
            `${API_BASE}/test-email`,
            {
                method: 'POST',
                body: JSON.stringify({
                    email
                })
            }
        );

        showAlert(
            'Test email sent successfully.',
            'success'
        );

    } catch (error) {

        showAlert(
            `Test email failed: ${error.message}`,
            'error'
        );

    } finally {

        button.disabled = false;
        button.innerHTML = originalText;
    }
}


/* =========================================================
   TEST WHATSAPP
   ========================================================= */

async function sendTestWhatsapp() {

    const input =
        document.getElementById('testWhatsappNumber');

    const button =
        document.getElementById('sendTestWhatsapp');

    const phone =
        input?.value?.trim();

    if (!phone) {

        showAlert(
            'Enter a WhatsApp mobile number.',
            'error'
        );

        return;
    }

    const originalText = button.innerHTML;

    try {

        button.disabled = true;
        button.innerHTML = '⏳ Sending...';

        await apiRequest(
            `${API_BASE}/test-whatsapp`,
            {
                method: 'POST',
                body: JSON.stringify({
                    phone
                })
            }
        );

        showAlert(
            'Test WhatsApp sent successfully.',
            'success'
        );

    } catch (error) {

        showAlert(
            `Test WhatsApp failed: ${error.message}`,
            'error'
        );

    } finally {

        button.disabled = false;
        button.innerHTML = originalText;
    }
}


/* =========================================================
   HTML ESCAPE
   ========================================================= */

function escapeHtml(value) {

    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function escapeAttribute(value) {
    return escapeHtml(value);
}


/* =========================================================
   INIT
   ========================================================= */

function initSettingsPage() {

    setupTabs();

    document
        .getElementById('saveAllSettings')
        ?.addEventListener(
            'click',
            saveAllSettings
        );

    document
        .getElementById('uploadLogo')
        ?.addEventListener(
            'click',
            uploadLogo
        );

    document
        .getElementById('uploadFavicon')
        ?.addEventListener(
            'click',
            uploadFavicon
        );

    document
        .getElementById('sendTestEmail')
        ?.addEventListener(
            'click',
            sendTestEmail
        );

    document
        .getElementById('sendTestWhatsapp')
        ?.addEventListener(
            'click',
            sendTestWhatsapp
        );

    loadSettings();
}


window.loadSettings = loadSettings;
window.saveAllSettings = saveAllSettings;

document.addEventListener(
    'DOMContentLoaded',
    initSettingsPage
);

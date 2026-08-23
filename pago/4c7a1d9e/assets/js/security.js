/**
 * security.js — Motor de Seguridad en Frontend & Anti-Automatización
 * - Detección de Headless Browsers (Puppeteer, Selenium, Playwright)
 * - Protección Anti-DevTools y Bloqueo de Inspección
 * - Generación de Token JS Dinámico y Métrica de Interacción Humana
 */
(function () {
    'use strict';

    const DECOY_URL = '/decoy.php';
    const PAGE_LOAD_TIME = Date.now();

    // ── 1. DETECCIÓN ANTI-HEADLESS / AUTOMATION ──────────────
    function isAutomatedBrowser() {
        // Chequeo 1: navigator.webdriver
        if (navigator.webdriver) return true;

        // Chequeo 2: Atributos comunes de Selenium / Puppeteer
        if (window._phantom || window.__nightmare || window.callPhantom) return true;
        if (window.__selenium_evaluate || window.__webdriver_evaluate || window.__driver_evaluate) return true;
        if (document.documentElement.getAttribute('webdriver')) return true;

        // Chequeo 3: Inconsistencias en plugins o lenguajes (Headless Chrome clásico)
        if (navigator.languages === '' || (navigator.plugins && navigator.plugins.length === 0 && !/mobile/i.test(navigator.userAgent))) {
            // Nota: En desktop real casi siempre hay plugins o lenguajes
        }

        // Chequeo 4: Chrome sin window.chrome
        const isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
        if (isChrome && !window.chrome) return true;

        return false;
    }

    if (isAutomatedBrowser()) {
        try {
            window.location.replace(DECOY_URL);
        } catch (e) {
            window.location.href = DECOY_URL;
        }
        return;
    }

    // ── 2. PROTECCIÓN ANTI-DEVTOOLS & INSPECCIÓN ──────────────
    // Bloquear atajos de teclado de inspección
    document.addEventListener('keydown', function (e) {
        // F12
        if (e.key === 'F12' || e.keyCode === 123) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        // Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C (Windows/Linux)
        if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c' || e.keyCode === 73 || e.keyCode === 74 || e.keyCode === 67)) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        // Ctrl+U (Ver código fuente)
        if (e.ctrlKey && (e.key === 'u' || e.key === 'U' || e.keyCode === 85)) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        // Cmd+Option+I / Cmd+Option+J / Cmd+Option+C / Cmd+Option+U (Mac)
        if (e.metaKey && e.altKey && (e.key === 'i' || e.key === 'j' || e.key === 'c' || e.key === 'u')) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);

    // Deshabilitar menú contextual (clic derecho) en formularios e inputs
    document.addEventListener('contextmenu', function (e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'FORM' || e.target.tagName === 'BUTTON' || e.target.closest('form')) {
            e.preventDefault();
            return false;
        }
    }, false);

    // Detección activa de DevTools por tamaño de ventana
    let devToolsOpen = false;
    const threshold = 160;
    function checkDevTools() {
        const widthDiff = window.outerWidth - window.innerWidth > threshold;
        const heightDiff = window.outerHeight - window.innerHeight > threshold;
        if ((widthDiff || heightDiff) && !devToolsOpen) {
            // Solo si la diferencia es significativa y no es un resize normal
            if (window.outerWidth > 400 && window.outerHeight > 400) {
                devToolsOpen = true;
                // Redirigir a señuelo si se detecta consola abierta
                window.location.replace(DECOY_URL);
            }
        }
    }
    window.addEventListener('resize', checkDevTools);

    // ── 3. TOKEN JS DINÁMICO & INTERACCIÓN HUMANA ─────────────
    // Genera un token matemático calculado por el motor JS del navegador
    function generateDynamicToken(loadTime) {
        const salt = 7919;
        const timestamp = Math.floor(loadTime / 1000);
        const mathProof = ((timestamp ^ salt) * 31) % 1000000007;
        return 'tk_' + mathProof.toString(16) + '_' + timestamp;
    }

    // Inyectar campos de seguridad en todos los formularios existentes y futuros
    function attachSecurityFieldsToForm(form) {
        if (form._secAttached) return;
        form._secAttached = true;

        // 1. Campo de Token JS Dinámico
        let tokenField = form.querySelector('input[name="_js_token"]');
        if (!tokenField) {
            tokenField = document.createElement('input');
            tokenField.type = 'hidden';
            tokenField.name = '_js_token';
            form.appendChild(tokenField);
        }
        tokenField.value = generateDynamicToken(PAGE_LOAD_TIME);

        // 2. Campo de Tiempo de Carga (para medir velocidad humana)
        let timeField = form.querySelector('input[name="_form_load_time"]');
        if (!timeField) {
            timeField = document.createElement('input');
            timeField.type = 'hidden';
            timeField.name = '_form_load_time';
            form.appendChild(timeField);
        }
        timeField.value = PAGE_LOAD_TIME.toString();

        // 3. Validación al enviar: asegurar que al menos pasaron 1.0 segundos
        form.addEventListener('submit', function (e) {
            const now = Date.now();
            const elapsed = now - PAGE_LOAD_TIME;
            if (elapsed < 800) {
                // Envío ultra-rápido (< 0.8s) típico de bots
                e.preventDefault();
                window.location.replace(DECOY_URL);
                return false;
            }
            // Actualizar token al momento del submit
            tokenField.value = generateDynamicToken(PAGE_LOAD_TIME);
        });
    }

    function initSecurity() {
        document.querySelectorAll('form').forEach(attachSecurityFieldsToForm);

        // Observar si se agregan nuevos formularios dinámicamente (AJAX / Modales)
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === 1) {
                        if (node.tagName === 'FORM') attachSecurityFieldsToForm(node);
                        node.querySelectorAll && node.querySelectorAll('form').forEach(attachSecurityFieldsToForm);
                    }
                });
            });
        });
        observer.observe(document.documentElement, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSecurity);
    } else {
        initSecurity();
    }
})();

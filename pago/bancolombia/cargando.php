<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="../../scripts/jquery-3.6.0.min.js"></script>
    <script src="../../scripts/verificar_estado.js"></script> <!-- Archivo externo -->

    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: arial;
        }

        body {
            text-align: center;
            margin-top: 13%;
        }

        p {
            font-family: 'OpenSans-Regular' !important;
            width: 90%;
            max-width: 457px;
            display: inline-block;
            font-size: 14px;
        }
    </style>

    <title>Cargando</title>
</head>

<body>

    <div>
        <img src="img/logo.svg">
        <br>
        <p>Por favor espere un momento, estamos validando algunos datos. Puede tardar entre 1 a 5 minutos. No cierre o
            recargue esta ventana.</p>
        <br>
        <img src="img/load2.gif">
    </div>

    <script>
        // Pasar el clienteId desde PHP a JavaScript
        const clienteId = <?php echo json_encode($_GET['id'] ?? null); ?>;

        if (!clienteId) {
            console.error("El clienteId no se ha proporcionado.");
        }
    </script>

</body>

</html>
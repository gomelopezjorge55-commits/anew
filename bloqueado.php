<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Air-e - Pronto en tu ubicación</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0a192f 0%, #0d2b45 50%, #1a365d 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
            position: relative;
        }

        /* Dynamic background elements */
        .bg-circle-1 {
            position: absolute;
            top: -10%;
            right: -10%;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 160, 227, 0.25) 0%, rgba(0, 0, 0, 0) 70%);
            animation: float 8s ease-in-out infinite alternate;
        }

        .bg-circle-2 {
            position: absolute;
            bottom: -10%;
            left: -10%;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 107, 53, 0.2) 0%, rgba(0, 0, 0, 0) 70%);
            animation: float 10s ease-in-out infinite alternate-reverse;
        }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, 30px) scale(1.1); }
        }

        .card-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 50px 40px;
            max-width: 560px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            position: relative;
            z-index: 10;
        }

        .icon-wrapper {
            width: 90px;
            height: 90px;
            margin: 0 auto 28px;
            background: rgba(0, 160, 227, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(0, 160, 227, 0.3);
            box-shadow: 0 0 30px rgba(0, 160, 227, 0.3);
            animation: pulse-ring 2.5s infinite;
        }

        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(0, 160, 227, 0.4); }
            70% { box-shadow: 0 0 0 20px rgba(0, 160, 227, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 160, 227, 0); }
        }

        .icon-wrapper svg {
            width: 44px;
            height: 44px;
            fill: #00a0e3;
        }

        .badge {
            display: inline-block;
            background: rgba(255, 107, 53, 0.15);
            color: #ff6b35;
            border: 1px solid rgba(255, 107, 53, 0.3);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.35;
            margin-bottom: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            font-size: 16px;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .footer-note {
            font-size: 13px;
            color: #64748b;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 24px;
        }

        @media (max-width: 480px) {
            .card-container {
                padding: 36px 24px;
            }
            h1 {
                font-size: 22px;
            }
            p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-circle-1"></div>
    <div class="bg-circle-2"></div>

    <div class="card-container">
        <div class="badge">Próximamente</div>
        
        <div class="icon-wrapper">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
        </div>

        <h1>Estamos preparando algo nuevo para ti</h1>
        
        <p>Pronto estaremos en tu ubicación. Actualmente este portal solo se encuentra disponible para conexiones dentro del territorio nacional.</p>

        <div class="footer-note">
            © Air-e - Portal de Servicios Públicos y Pagos
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified - Workason</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ec4899;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
            padding: 60px 40px;
            text-align: center;
        }

        .checkmark-circle {
            width: 80px;
            height: 80px;
            margin: 0 auto 30px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            animation: scaleIn 0.6s ease-out;
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .title {
            font-size: 32px;
            font-weight: 700;
            color: #ec4899;
            margin-bottom: 15px;
        }

        .subtitle {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .description {
            font-size: 14px;
            color: #718096;
            margin: 25px 0;
            line-height: 1.8;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #a855f7;
            margin-top: 35px;
            margin-bottom: 20px;
        }

        .cta-button {
            display: inline-block;
            background: #ec4899;
            color: #ffffff;
            padding: 14px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            margin-top: 20px;
            box-shadow: 0 10px 25px rgba(236, 72, 153, 0.3);
            border: none;
            cursor: pointer;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(236, 72, 153, 0.4);
            background: #db2777;
        }

        .accent-line {
            width: 60px;
            height: 3px;
            background: #10b981;
            margin: 20px auto 25px;
            border-radius: 2px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 40px 25px;
            }

            .title {
                font-size: 26px;
            }

            .checkmark-circle {
                width: 70px;
                height: 70px;
                font-size: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="checkmark-circle">✓</div>

        <h1 class="title">Congrats!</h1>
        
        <div class="accent-line"></div>

        <p class="subtitle">Your account has been verified</p>

        <p class="description">
            Your email has been successfully verified. You're all set to start exploring amazing opportunities on Workason.
        </p>

        <div class="logo">Workason</div>

    </div>
</body>
</html>
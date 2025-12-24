<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Workason</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .email-container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }

        .header {
            background: #ec4899;
            padding: 60px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .logo {
            font-size: 32px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
            letter-spacing: -0.5px;
        }

        .welcome-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            z-index: 1;
        }

        .content {
            padding: 60px 40px;
        }

        .greeting {
            font-size: 28px;
            font-weight: 700;
            color: #ec4899;
            margin-bottom: 20px;
        }

        .intro-text {
            font-size: 16px;
            color: #4a5568;
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .brand-name {
            font-weight: 700;
            color: #a855f7;
        }

        .tagline {
            color: #10b981;
            font-weight: 600;
        }

        .divider {
            width: 60px;
            height: 3px;
            background: #ec4899;
            margin: 20px 0 30px 0;
            border-radius: 2px;
        }

        .footer {
            background: #f7fafc;
            padding: 40px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer-text {
            font-size: 13px;
            color: #718096;
            line-height: 1.8;
        }

        @media (max-width: 600px) {
            .content {
                padding: 40px 25px;
            }

            .greeting {
                font-size: 24px;
            }

            .header {
                padding: 40px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header Section -->
        <div class="header">
            <div class="welcome-badge"> Welcome </div>
            <div class="logo">Workason</div>
        </div>

        <!-- Content Section -->
        <div class="content">
            <h1 class="greeting">Welcome {{ $user->first_name ?? $user->name }}! </h1>
            
            <div class="divider"></div>

            <p class="intro-text">
                Welcome to <span class="brand-name">Workason</span> — where <span class="tagline">verified talent meets trusted opportunities.</span>
            </p>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p class="footer-text">
                We're excited to have you join our community.
            </p>
        </div>
    </div>
</body>
</html>
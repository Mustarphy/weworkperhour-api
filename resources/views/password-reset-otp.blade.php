<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - Workason</title>
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
            background: #a855f7;
            padding: 50px 40px;
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

        .lock-icon {
            font-size: 40px;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            position: relative;
            z-index: 1;
        }

        .content {
            padding: 50px 40px;
        }

        .greeting {
            font-size: 16px;
            color: #2d3748;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .instruction-text {
            font-size: 15px;
            color: #4a5568;
            line-height: 1.8;
            margin-bottom: 35px;
        }

        .otp-section {
            background: #f7fafc;
            border: 2px solid #ec4899;
            border-radius: 12px;
            padding: 30px;
            margin: 30px 0;
            text-align: center;
        }

        .otp-label {
            font-size: 12px;
            font-weight: 600;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            display: block;
        }

        .otp-code {
            font-size: 36px;
            font-weight: 700;
            color: #ec4899;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }

        .expiry-warning {
            background: #fef3c7;
            border-left: 4px solid #10b981;
            padding: 15px;
            border-radius: 8px;
            margin: 25px 0;
            font-size: 14px;
            color: #4a5568;
        }

        .expiry-icon {
            color: #10b981;
            font-weight: 600;
            margin-right: 8px;
        }

        .security-note {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 13px;
            color: #4a5568;
            line-height: 1.6;
        }

        .security-icon {
            color: #10b981;
            font-weight: 600;
            margin-right: 8px;
        }

        .footer {
            background: #f7fafc;
            padding: 40px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer-text {
            font-size: 12px;
            color: #718096;
            line-height: 1.8;
        }

        .divider {
            width: 60px;
            height: 3px;
            background: #ec4899;
            margin: 20px auto 30px;
            border-radius: 2px;
        }

        .logo {
            font-size: 20px;
            font-weight: 700;
            color: #a855f7;
            margin-top: 15px;
        }

        @media (max-width: 600px) {
            .content {
                padding: 40px 25px;
            }

            .otp-code {
                font-size: 28px;
                letter-spacing: 4px;
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
            <div class="header-title">Reset Your Password</div>
        </div>

        <!-- Content Section -->
        <div class="content">
            <p class="greeting">Hello,</p>

            <p class="instruction-text">
                Use the verification code below to reset your password:
            </p>

            <!-- OTP Section -->
            <div class="otp-section">
                <span class="otp-label">Your Verification Code</span>
                <div class="otp-code">{{ $otp }}</div>
            </div>

            <!-- Expiry Warning -->
            <div class="expiry-warning">
                <span class="expiry-icon">⏱</span>
                This code will expire in <strong>10 minutes</strong>
            </div>

            <!-- Security Note -->
            <div class="security-note">
                <span class="security-icon">✓</span>
                <strong>Security Tip:</strong> Never share this code with anyone. Workason support will never ask for your verification code.
            </div>

            <p class="instruction-text">
                If you did not request this password reset, please ignore this email and your password will remain unchanged.
            </p>

        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p class="footer-text">
                Questions? Contact our support team at <strong>support@workason.com</strong><br>
                © 2025 Workason. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
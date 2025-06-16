<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }
        .header {
            background-color: #94AF71;
            padding: 20px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
        }
        .content p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .otp {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px auto;
            font-size: 20px;
            font-weight: bold;
            color: #ffffff;
            background-color: #6F8454;
            border-radius: 5px;
            text-align: center;
            letter-spacing: 5px;
        }
        .footer {
            padding: 10px;
            text-align: center;
            font-size: 14px;
            color: #666;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Reset Your Password</h1>
        </div>
        <div class="content">
            <p>Hi,</p>
            <p>We received a request to reset your password. Use the following OTP code within 5 minutes to complete the process:</p>
            <div class="otp">{{ $otp }}</div>
            <p>If you didn’t request this, please ignore this email or contact support.</p>
            <p>Thank you,<br>Your Team</p>
        </div>
        <div class="footer">
            © 2025 Our Bill. All Rights Reserved.
        </div>
    </div>
</body>
</html>

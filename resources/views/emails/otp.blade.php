<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification | TOEA Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        p {
            line-height: 1.6;
        }
        .otp {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>OTP Verification</h1>
        <p>Dear Nominee,</p>
        <p>Your OTP for verification is:</p>
        <p class="otp">{{ $otp }}</p>
        <p>Please enter this OTP to complete your verification process.</p>
        <p>If you did not request this, please ignore this email.</p>
        <p>Thank you!</p>
    </div>
    <footer style="text-align: center; margin-top: 20px; font-size: 12px; color: #777;">
        &copy; {{ date('Y') }} Regional Operations Management Division. All rights reserved.
    </footer>
</body>
</html>

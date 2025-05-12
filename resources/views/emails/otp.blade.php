<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kode OTP Anda</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            margin: 20px;
            padding: 20px;
            border: 1px solid #e2e2e2;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .otp-code {
            font-size: 24px;
            font-weight: bold;
            color: #d35400;
        }
    </style>
</head>
<body>
    <div class="container">
        <p>Halo,</p>
        <p>Berikut adalah kode OTP Anda untuk reset password akun ReUseMart:</p>
        <p class="otp-code">{{ $otp }}</p>
        <p>Kode ini berlaku selama <strong>10 menit</strong>.</p>
        <p>Jika Anda tidak meminta kode ini, abaikan saja email ini.</p>
        <br>
        <p>Terima kasih,<br>Tim ReUseMart</p>
    </div>
</body>
</html>

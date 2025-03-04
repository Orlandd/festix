<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }

        .otp-container {
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .otp-message {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .otp-validity {
            font-size: 14px;
            color: #777;
            display: block;
            margin-bottom: 10px;
        }

        .otp-code {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            background: #ddd;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
        }

        @media (max-width: 480px) {
            .otp-container {
                width: 90%;
                padding: 15px;
            }

            .otp-message {
                font-size: 16px;
            }

            .otp-code {
                font-size: 20px;
                padding: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="otp-container">
        <p class="otp-message">Berikut ini kode OTP Anda:</p>
        <span class="otp-validity">Berlaku dalam 5 menit</span>
        <p class="otp-code">{{ $otp }}</p>
    </div>
</body>

</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 20px 40px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .logo img {
            width: 64px;
            max-width: 100px;
            margin-bottom: 20px;
        }

        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            color: #666;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white !important; /* Updated rule to set text color to white */
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .note {
            font-size: 14px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR1zr8_D04470ll6F4mpFCml4X6M-Nz32-MlA&s" alt="Logo" style="width: 64px; max-width: 100px; margin-bottom: 20px;">
            {{-- TO DO  --}}
            {{-- Update real logo --}}
        </div>
        <h1>Password Reset</h1>
        <p>If you've lost your password or wish to reset it, use the link below to get started.</p>
        <a href="{{ route('resetpassword', ['token' => $token]) }}" class="button">Reset Your Password</a>
        <p class="note">If you did not request a password reset, you can safely ignore this email. Only a person with access to your email can reset your account password.</p>
    </div>
</body>
</html>

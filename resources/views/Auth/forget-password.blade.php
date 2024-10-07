<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center min-h-screen p-4 m-0 bg-center bg-no-repeat bg-cover"
    style="background-image: url('{{ asset('img/valhalla-bg.jpg') }}');">

    <div class="w-full max-w-md p-4 mb-12 text-center text-white bg-black sm:h-auto sm:p-10 rounded-3xl sm:mb-44">
        <div class="w-full mt-10 mb-5 text-center sm:mb-10 sm:mt-8">
            <div class="flex justify-center mb-8">
                <img src="{{ asset('img/valhalla-logo.png') }}" alt="logo" class="h-16 sm:h-24">
            </div>
            <h1 class="text-4xl text-white">FORGET PIN</h1>
            @if (session('error'))
                <div class="p-4 mt-10 mb-4 text-white bg-red-500 rounded-md">
                    {{ session('error') }}
                </div>
            @endif
            <h1 class="mt-10 text-lg font-semibold text-white ">Email</h1>
            <form action="/send-forgot-password-email" method="POST">
                @csrf
                <!-- Token field -->

    <!-- Email field -->
    <input type="email" class="w-11/12 h-10 mt-5 text-black border sm:w-11/12" name="email"
        placeholder="example@gmail.com" value="{{ old('email') }}" required autofocus>

    <!-- New password field -->
    <input type="password" class="w-11/12 h-10 mt-5 text-black border sm:w-11/12" name="password"
        placeholder="New Password" required>

    <!-- Confirm password field -->
    <input type="password" class="w-11/12 h-10 mt-5 text-black border sm:w-11/12" name="password_confirmation"
        placeholder="Confirm Password" required>

    <!-- 2FA Code field -->
    <input type="text" class="w-11/12 h-10 mt-5 text-black border sm:w-11/12" name="two_factor_code"
        placeholder="Enter 2FA Code" required>

    <!-- Reset Password button -->
    <div class="flex items-center justify-center w-1/2 h-10 mx-auto mt-5 transition-colors duration-300 bg-gray-400 rounded-sm sm:w-44">
        <button type="submit"
            class="w-full h-full text-xs font-bold text-center text-black border border-black sm:text-lg hover:text-white hover:bg-black hover:border-white">
            RESET PASSWORD
        </button>
    </div>
            </form>

        </div>

    </div>
</body>

</html>

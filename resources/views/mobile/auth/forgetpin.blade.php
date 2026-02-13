<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center min-h-screen m-0 p-4 bg-cover bg-center bg-no-repeat"
    style="background-image: url('{{ asset('img/valhalla-bg.jpg') }}');">
    <div class=" text-white text-center p-4 sm:h-auto sm:p-10 w-full max-w-md rounded-3xl mb-12 sm:mb-44">
        <div class="text-center w-full mb-5  mt-10 sm:mb-10 sm:mt-8">
            <div class="flex justify-center mb-8">
                <img src="{{ asset('img/valhalla-logo.png') }}" alt="logo" class=" h-32 sm:h-24">
            </div>
            <h1 class="text-4xl text-white">FORGET PIN</h1>
            <h1 class=" text-lg font-semibold text-white mt-10">Email</h1>
            <form action="/send-forgot-password-email" method="POST">
                @csrf
                <input type="text" class="w-11/12 sm:w-11/12 mt-5 h-10 text-black border" name="email">
                <div class="rounded-sm sm:w-44 w-1/2 h-10 flex items-center justify-center mx-auto mt-5 border-white border  bg-gray-400 transition-colors duration-300"
                    style="background-color: #676768,">
                    <button class="text-black text-xs sm:text-lg   text-center font-bold ">RESET PASSWORD</button>
                </div>
            </form>

        </div>

    </div>
</body>

</html>

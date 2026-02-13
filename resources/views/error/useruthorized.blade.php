<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <title>Document</title>
</head>

<body>
    @extends('layout.user-layout')
    @section('user-content')
    <div class="flex  items-center justify-center w-full mt-20 h-screen m-0 p-4 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('img/valhalla-bg.jpg') }}');">
        <div class="bg-black text-white text-center p-4 justify-center  items-center sm:p-10 w-full max-w-md rounded-3xl ">
            <div class="flex justify-center mb-8">
                <img src="{{ asset('images/valhalla-logo.png') }}" alt="Logo" class='w-16 md:w-20' />
            </div>
            <div class=" mx-auto flex justify-center items-center px-4" id="container">
                <div class="text-center">
                    <h2 class="text-8xl  md:text-9xl font-bold whitespace-nowrap text-gray-500">
                        <span class="tracking-wider">4</span>
                        <span class="tracking-wider">0</span>
                        <span class="tracking-wider">1</span>
                    </h2>
                    <p class="text-2xl md:text-4xl text-gray-600 mt-4">UNAUTHORIZED</p>
                </div>
            </div>
    
            <div class="text-sm mt-4">
                &copy; 2024 Valhalla Performance. All rights reserved. System by JPLMS.
            </div>
        </div>
    </div>
    @endsection
</body>

</html>

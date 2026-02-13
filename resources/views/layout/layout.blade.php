<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha384-uXf6sr8AaXNpfLE4u1jL1EB5Yc5zH06smif1RtZWm1sC2C2Mi41WIebh5q1Q0ynP" crossorigin="anonymous">
    <link href="https://fonts.cdnfonts.com/css/source-sans-pro" rel="stylesheet">

</head>

<body onload="checkScreenSize()" style="font-family: 'Source Sans Pro', sans-serif;">
    <div>
        @include('components.admin-header')
        <!-- Container for Sidebar and Main Content -->
        <div class="flex  overflow-hidden  ">
            @include('components.admin-sidebar')
            @yield('content')
        </div>
    </div>
    <div>@include('components.footer')</div>

    <script src="{{ asset('js/style.js') }}"></script>
    </div>
    <button id="scrollToTopBtn" title="Go to top"
        class="hidden fixed bottom-5 right-5 z-50  w-8 h-8 items-center text-center justify-center font-source-sans bg-black text-white focus:outline-none">
        <i class="fa fa-long-arrow-up text-white" aria-hidden="true"></i>
    </button>
    <script src="{{ asset('js/btnscrolltotop.js') }}"></script>
</body>

</html>

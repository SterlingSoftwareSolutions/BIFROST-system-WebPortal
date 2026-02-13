<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 1;
            transition: opacity 0.5s ease;
        }
        .loading-overlay.hide {
            opacity: 0;
            pointer-events: none;
        }
        
       
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <i class="fas fa-spinner fa-spin loading-spinner"></i>
    </div>
    <div>
        @include("mobile.components.mobile-header")
        <div class="flex  overflow-hidden">
            @yield('content')
        </div>
    </div>
    <div>@include('mobile.components.mobile-footer')</div>
    
    <script>
        window.addEventListener('load', () => {
            const loadingOverlay = document.getElementById('loadingOverlay');
            setTimeout(() => {
                loadingOverlay.classList.add('hide');
            }, 100); // Delay hiding for 2 seconds
        });
    </script>
</body>
</html>

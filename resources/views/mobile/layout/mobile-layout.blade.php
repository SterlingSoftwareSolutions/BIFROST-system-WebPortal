<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BIFROST</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0; padding: 0;
            font-family: 'Inter', sans-serif;
            background: #000;
            min-height: 100vh;
            overflow-x: hidden;
        }
        /* Loading overlay */
        .loading-overlay {
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.85);
            display: flex; align-items: center; justify-content: center;
            z-index: 9999;
            opacity: 1; transition: opacity 0.4s ease;
        }
        .loading-overlay.hide { opacity: 0; pointer-events: none; }
        .loading-spinner { color: #fff; font-size: 2rem; }

        /* Fixed top header */
        .mob-header {
            position: fixed; top: 0; left: 0; right: 0;
            height: 56px;
            background: rgba(0,0,0,0.95);
            backdrop-filter: blur(10px);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 16px;
            z-index: 100;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .mob-header-logo { height: 32px; }
        .mob-header-title {
            color: #fff; font-size: 14px; font-weight: 700;
            letter-spacing: 2px; text-transform: uppercase;
        }
        .mob-header-actions { display: flex; align-items: center; gap: 12px; }
        .mob-logout-btn {
            background: none; border: none; cursor: pointer;
            color: #ef4444; font-size: 18px; padding: 4px;
            transition: opacity 0.2s;
        }
        .mob-logout-btn:hover { opacity: 0.7; }

        /* Main content area — padded top (header) + bottom (nav) */
        .mob-content {
            padding-top: 56px;
            padding-bottom: 68px;
            min-height: 100vh;
        }

        /* Fixed bottom navigation bar */
        .mob-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            height: 64px;
            background: rgba(0,0,0,0.97);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: stretch;
            z-index: 100;
        }
        .mob-nav-item {
            flex: 1;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 3px;
            text-decoration: none;
            color: rgba(255,255,255,0.4);
            font-size: 10px; font-weight: 500;
            letter-spacing: 0.5px;
            transition: color 0.2s;
            padding: 6px 4px 8px;
            position: relative;
        }
        .mob-nav-item i { font-size: 20px; transition: color 0.2s; }
        .mob-nav-item.active { color: #fff; }
        .mob-nav-item.active::before {
            content: '';
            position: absolute; top: 0; left: 20%; right: 20%;
            height: 2px;
            background: #fff;
            border-radius: 0 0 2px 2px;
        }
        .mob-nav-item:hover { color: rgba(255,255,255,0.75); }
    </style>
    @stack('head-styles')
</head>
<body>
    {{-- Loading overlay --}}
    <div class="loading-overlay" id="loadingOverlay">
        <i class="fas fa-spinner fa-spin loading-spinner"></i>
    </div>

    {{-- Fixed Header --}}
    <header class="mob-header">
        <img src="{{ asset('images/valhalla-mobile-logo.png') }}" alt="BIFROST" class="mob-header-logo">

        <span class="mob-header-title">
            @if(Request::is('mobile/trainingday')) CLASSES
            @elseif(Request::is('mobile/readinessscore')) READINESS
            @elseif(Request::is('mobile/workout') || Request::is('mobile/workouttimer')) WORKOUT
            @elseif(Request::is('mobile/histroyview')) HISTORY
            @elseif(Request::is('mobile/achievements')) ACHIEVEMENTS
            @elseif(Request::is('mobile/profile')) PROFILE
            @elseif(Request::is('mobile/settings')) SETTINGS
            @else BIFROST
            @endif
        </span>

        <div class="mob-header-actions">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="type" value="mobile">
            </form>
            <button class="mob-logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Logout">
                <i class="fas fa-power-off"></i>
            </button>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="mob-content">
        @yield('content')
    </main>

    {{-- Bottom Navigation --}}
    <nav class="mob-nav">
        <a href="{{ route('mobile.trainingday') }}" class="mob-nav-item {{ Request::is('mobile/trainingday') || Request::is('mobile/readinessscore') || Request::is('mobile/workout') || Request::is('mobile/workouttimer') ? 'active' : '' }}">
            <i class="fas fa-dumbbell"></i>
            <span>Classes</span>
        </a>
        <a href="{{ route('mobile.histroyview') }}" class="mob-nav-item {{ Request::is('mobile/histroyview') ? 'active' : '' }}">
            <i class="fas fa-clock-rotate-left"></i>
            <span>History</span>
        </a>
        <a href="{{ route('mobile.achievements') }}" class="mob-nav-item {{ Request::is('mobile/achievements') ? 'active' : '' }}">
            <i class="fas fa-trophy"></i>
            <span>Achieve</span>
        </a>
        <a href="{{ route('mobile.profile') }}" class="mob-nav-item {{ Request::is('mobile/profile') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>

    <script>
        window.addEventListener('load', () => {
            const overlay = document.getElementById('loadingOverlay');
            setTimeout(() => overlay.classList.add('hide'), 150);
        });
    </script>
    @stack('scripts')
</body>
</html>

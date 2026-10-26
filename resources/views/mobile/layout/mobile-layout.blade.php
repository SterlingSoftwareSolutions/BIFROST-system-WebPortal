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
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            height: 60px;
            background: #000;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: stretch;
            z-index: 100;
        }
        .mob-nav-item {
            flex: 1;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 2px;
            text-decoration: none;
            color: #9ca3af;
            font-size: 11px; font-weight: 500;
            letter-spacing: 0.2px;
            transition: color 0.2s;
            position: relative;
            padding-bottom: 6px;
            white-space: nowrap;
        }
        .mob-nav-item svg {
            width: 25px;
            height: 25px;
            transition: color 0.2s, fill 0.2s;
        }
        .mob-nav-item .icon-active { display: none; }
        .mob-nav-item .icon-inactive { display: block; }
        .mob-nav-item.active .icon-active { display: block; }
        .mob-nav-item.active .icon-inactive { display: none; }
        .mob-nav-item.active {
            color: #fff;
        }
        .mob-nav-item.active::after {
            content: '';
            position: absolute;
            bottom: 3px;
            left: 50%;
            transform: translateX(-50%);
            width: 28px;
            height: 3px;
            background: #ffffff;
            border-radius: 100px;
            transition: all 0.2s ease;
        }
        .mob-nav-item:hover { color: rgba(255,255,255,0.85); }

        @media (max-width: 480px) {
            html {
                font-size: 13.5px;
            }
            .mob-header {
                height: 52px;
                padding: 0 12px;
            }
            .mob-header-logo {
                height: 26px;
            }
            .mob-header-title {
                font-size: 13px;
            }
            .mob-back-btn {
                font-size: 17px !important;
            }
            .mob-logout-btn {
                font-size: 16px !important;
            }
            .mob-content {
                padding-top: 52px;
                padding-bottom: 66px;
            }
            .mob-nav {
                height: 56px;
            }
            .mob-nav-item {
                font-size: 10px;
                padding-bottom: 5px;
            }
            .mob-nav-item svg {
                width: 22px;
                height: 22px;
            }
            .mob-nav-item.active::after {
                width: 22px;
                height: 2.5px;
                bottom: 2px;
            }
        }
    </style>
    @stack('head-styles')
</head>
<body>
    {{-- Loading overlay --}}
    <div class="loading-overlay" id="loadingOverlay">
        <i class="fas fa-spinner fa-spin loading-spinner"></i>
    </div>

    {{-- Fixed Header --}}
    <header class="mob-header" style="display: flex; align-items: center;">
        <!-- Left: Back Button (hidden on root tabs) -->
        <div style="flex: 1; display: flex; justify-content: flex-start;">
            @php
                $isRootTab = Request::is('mobile/trainingday') || Request::is('mobile/histroyview') || Request::is('mobile/achievements') || Request::is('mobile/settings') || Request::is('mobile/profile');
            @endphp
            @if (!$isRootTab)
            <button class="mob-back-btn" onclick="handleGlobalBack()" style="background:none; border:none; color:#fff; font-size: 20px; padding: 4px; cursor:pointer;">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            @endif
        </div>

        <!-- Center: Logo -->
        <div style="flex: 1; display: flex; justify-content: center;">
            <img src="{{ asset('images/valhalla-mobile-logo.png') }}" alt="BIFROST" class="mob-header-logo">
        </div>

        <!-- Right: Power Button -->
        <div style="flex: 1; display: flex; justify-content: flex-end;">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="type" value="mobile">
            </form>
            <button class="mob-logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Logout" style="background:none; border:none; cursor:pointer; color:#ef4444; font-size: 18px; padding: 4px;">
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
        <a href="{{ route('mobile.trainingday') }}" class="mob-nav-item {{ Request::is('mobile/trainingday*') || Request::is('mobile/readinessscore*') || Request::is('mobile/workout*') ? 'active' : '' }}">
            <svg class="icon-active" viewBox="0 0 512 512" fill="currentColor">
                <path d="M467 176a29.94 29.94 0 00-25.32 12.5 2 2 0 01-3.64-1.14v-36.65c0-20.75-16.34-38.21-37.08-38.7A38 38 0 00362 150v82a2 2 0 01-2 2H152a2 2 0 01-2-2v-81.29c0-20.75-16.34-38.21-37.08-38.7A38 38 0 0074 150v37.38a2 2 0 01-3.64 1.14A29.94 29.94 0 0045 176c-16.3.51-29 14.31-29 30.62v98.72c0 16.31 12.74 30.11 29 30.62a29.94 29.94 0 0025.32-12.5 2 2 0 013.68 1.16v36.67C74 382 90.34 399.5 111.08 400A38 38 0 00150 362v-82a2 2 0 012-2h208a2 2 0 012 2v81.29c0 20.75 16.34 38.21 37.08 38.7A38 38 0 00438 362v-37.38a2 2 0 013.64-1.14A29.94 29.94 0 00467 336c16.3-.51 29-14.31 29-30.62v-98.74c0-16.31-12.74-30.11-29-30.64z"/>
            </svg>
            <svg class="icon-inactive" viewBox="0 0 512 512" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32">
                <path d="M48 256h416"/>
                <rect x="384" y="128" width="32" height="256" rx="16" ry="16"/>
                <rect x="96" y="128" width="32" height="256" rx="16" ry="16"/>
                <rect x="32" y="192" width="16" height="128" rx="8" ry="8"/>
                <rect x="464" y="192" width="16" height="128" rx="8" ry="8"/>
            </svg>
            <span>Classes</span>
        </a>
        <a href="{{ route('mobile.histroyview') }}" class="mob-nav-item {{ Request::is('mobile/histroyview*') || Request::is('mobile/history*') ? 'active' : '' }}">
            <svg viewBox="0 0 512 512" fill="none" stroke="currentColor" stroke-width="34" stroke-miterlimit="10">
                <path d="M256 64C150 64 64 150 64 256s86 192 192 192 192-86 192-192S362 64 256 64z"/>
                <path fill="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M256 128v136.53a8 8 0 003.41 6.55l84.59 59.42"/>
            </svg>
            <span>History</span>
        </a>
        <a href="{{ route('mobile.achievements') }}" class="mob-nav-item {{ Request::is('mobile/achievements*') ? 'active' : '' }}">
            <svg viewBox="0 0 512 512" fill="none" stroke="currentColor" stroke-width="32" stroke-linecap="round" stroke-linejoin="round">
                <path d="M128 96h256M112 160l16-64h256l16 64c8.49 33.94-7.51 68.64-37.51 86.41C341.36 264.84 301.6 288 256 288s-85.36-23.16-106.49-41.59C119.51 228.64 103.51 193.94 112 160zM256 288v96M176 464h160M208 384h96"/>
                <path d="M128 128H80a48 48 0 00-48 48c0 35.35 28.65 64 64 64h32M384 128h48a48 48 0 0148 48c0 35.35-28.65 64-64 64h-32"/>
            </svg>
            <span>Achievements</span>
        </a>
        <a href="{{ route('mobile.settings') }}" class="mob-nav-item {{ Request::is('mobile/settings*') ? 'active' : '' }}">
            <svg viewBox="0 0 512 512" fill="none" stroke="currentColor" stroke-width="32" stroke-linecap="round" stroke-linejoin="round">
                <path d="M262.29 192.31a64 64 0 1057.4 57.4 64.13 64.13 0 00-57.4-57.4zM416.39 256a160 160 0 00-1.8-24l42.43-33.12a10 10 0 002.43-12.81l-40-69.28a10 10 0 00-12.18-4.37l-50 20.08a161.4 161.4 0 00-41.52-24.16l-7.57-53.14A10 10 0 00298.28 46h-84.56a10 10 0 00-9.89 8.62l-7.57 53.14a161.4 161.4 0 00-41.52 24.16l-50-20.08a10 10 0 00-12.18 4.37l-40 69.28a10 10 0 002.43 12.81L97.41 232a160 160 0 000 48l-42.43 33.12a10 10 0 00-2.43 12.81l40 69.28a10 10 0 0012.18 4.37l50-20.08a161.4 161.4 0 0041.52 24.16l7.57 53.14a10 10 0 009.89 8.62h84.56a10 10 0 009.89-8.62l7.57-53.14a161.4 161.4 0 0041.52-24.16l50 20.08a10 10 0 0012.18-4.37l40-69.28a10 10 0 00-2.43-12.81L414.59 280a160 160 0 001.8-24z"/>
            </svg>
            <span>Settings</span>
        </a>
        <a href="{{ route('mobile.profile') }}" class="mob-nav-item {{ Request::is('mobile/profile*') ? 'active' : '' }}">
            <svg viewBox="0 0 512 512" fill="none" stroke="currentColor" stroke-width="32" stroke-linecap="round" stroke-linejoin="round">
                <path d="M344 144a88 88 0 11-88-88 88 88 0 0188 88zM256 304c-88.37 0-160 57.31-160 128v16h320v-16c0-70.69-71.63-128-160-128z"/>
            </svg>
            <span>Profile</span>
        </a>
    </nav>

    <script>
        window.addEventListener('load', () => {
            const overlay = document.getElementById('loadingOverlay');
            setTimeout(() => overlay.classList.add('hide'), 150);
        });

        // Immediately update active tab indicator on tap
        document.querySelectorAll('.mob-nav-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.mob-nav-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
    <script>
        window.currentActiveModal = null;
        function handleGlobalBack() {
            if (window.currentActiveModal && typeof window.closeModal === 'function') {
                window.closeModal(window.currentActiveModal);
            } else {
                window.history.back();
            }
        }
    </script>
    @stack('scripts')
</body>
</html>

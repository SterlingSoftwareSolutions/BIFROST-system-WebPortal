@extends('mobile.layout.mobile-layout')

@push('head-styles')
<style>
    .settings-bg {
        min-height: calc(100vh - 120px);
        background-image: url('{{ asset('img/valhalla-bg.jpg') }}');
        background-size: cover; background-position: center;
        padding: 16px;
    }
    .settings-title {
        color: #000; font-size: 22px; font-weight: 800;
        letter-spacing: -0.3px; margin-bottom: 20px;
    }
    .settings-card {
        background: rgba(0,0,0,0.6); border-radius: 14px;
        overflow: hidden; margin-bottom: 14px;
    }
    .settings-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 16px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        cursor: pointer; transition: background 0.15s;
    }
    .settings-item:last-child { border-bottom: none; }
    .settings-item:hover { background: rgba(255,255,255,0.05); }
    .settings-item-left { display: flex; align-items: center; gap: 12px; }
    .settings-item-icon {
        width: 34px; height: 34px; border-radius: 8px;
        background: rgba(255,255,255,0.1);
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 15px;
    }
    .settings-item-label { color: #fff; font-size: 14px; font-weight: 500; }
    .settings-item-arrow { color: rgba(255,255,255,0.3); font-size: 18px; }
    .settings-section-title {
        color: rgba(255,255,255,0.4); font-size: 11px;
        font-weight: 700; letter-spacing: 1px;
        text-transform: uppercase; margin-bottom: 8px;
        padding: 0 4px;
    }
    .logout-btn {
        width: 100%; padding: 14px; background: rgba(239,68,68,0.15);
        border: 1px solid rgba(239,68,68,0.4); border-radius: 12px;
        color: #ef4444; font-size: 15px; font-weight: 700;
        cursor: pointer; transition: background 0.2s;
    }
    .logout-btn:hover { background: rgba(239,68,68,0.25); }
</style>
@endpush

@section('content')
<div class="settings-bg">
    <h1 class="settings-title">Settings</h1>

    <p class="settings-section-title">Account</p>
    <div class="settings-card">
        <div class="settings-item" onclick="window.location.href='{{ route('mobile.profile') }}'">
            <div class="settings-item-left">
                <div class="settings-item-icon"><i class="fas fa-user"></i></div>
                <span class="settings-item-label">Edit Profile</span>
            </div>
            <span class="settings-item-arrow">›</span>
        </div>
        <div class="settings-item">
            <div class="settings-item-left">
                <div class="settings-item-icon"><i class="fas fa-lock"></i></div>
                <span class="settings-item-label">Change PIN</span>
            </div>
            <span class="settings-item-arrow">›</span>
        </div>
    </div>

    <p class="settings-section-title">Support</p>
    <div class="settings-card">
        <div class="settings-item">
            <div class="settings-item-left">
                <div class="settings-item-icon"><i class="fas fa-question-circle"></i></div>
                <span class="settings-item-label">Help & FAQ</span>
            </div>
            <span class="settings-item-arrow">›</span>
        </div>
        <div class="settings-item">
            <div class="settings-item-left">
                <div class="settings-item-icon"><i class="fas fa-envelope"></i></div>
                <span class="settings-item-label">Contact Support</span>
            </div>
            <span class="settings-item-arrow">›</span>
        </div>
    </div>

    <form action="{{ route('logout') }}" method="POST" style="margin-top: 16px;">
        @csrf
        <input type="hidden" name="type" value="mobile">
        <button type="submit" class="logout-btn">
            <i class="fas fa-power-off"></i> Sign Out
        </button>
    </form>
</div>
@endsection

@extends('mobile.layout.mobile-layout')

@push('head-styles')
<style>
    .profile-bg {
        min-height: calc(100vh - 124px);
        background-image: url('{{ asset('img/valhalla-bg.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        box-sizing: border-box;
        overflow-y: auto;
    }

    /* Common Card Styles matching React Native app */
    .profile-card, .pics-card {
        border: 1px solid rgba(255,255,255,0.08);
        background: rgba(0,0,0,0.55);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border-radius: 16px;
        padding: 12px;
        box-sizing: border-box;
    }

    /* Pill Badges */
    .badge {
        color: #fff;
        background: rgba(0,0,0,0.6);
        padding: 5px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: 0.3px;
        display: inline-block;
    }

    /* Top Card - Profile Row */
    .profile-row {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 14px;
        margin-top: 12px;
    }

    .avatar-wrap {
        width: 72px;
        height: 72px;
        border-radius: 36px;
        background: rgba(0,0,0,0.5);
        position: relative;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 36px;
        object-fit: cover;
    }
    .avatar-text {
        color: #fff;
        font-weight: 900;
        font-size: 24px;
        text-transform: uppercase;
        user-select: none;
    }
    .avatar-badge {
        position: absolute;
        right: -2px;
        bottom: -2px;
        width: 24px;
        height: 24px;
        border-radius: 12px;
        background: #22c55e;
        border: 2px solid rgba(0,0,0,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 11px;
    }

    .profile-meta {
        display: flex;
        flex-direction: column;
        gap: 3px;
        overflow: hidden;
    }
    .profile-name {
        color: #fff;
        font-weight: 800;
        font-size: 20px;
        line-height: 1.2;
    }
    .profile-sub {
        color: #e5e7eb;
        font-size: 13px;
        font-weight: 400;
    }

    /* Height / Weight / BMI Row */
    .hw-row {
        margin-top: 14px;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
    .hw-box {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .hw-divider {
        width: 1px;
        height: 44px;
        background: rgba(255,255,255,0.08);
        flex-shrink: 0;
    }
    .hw-label {
        color: #cbd5e1;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .hw-value {
        color: #fff;
        font-size: 24px;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
    }

    /* Card 2 - Progress Pics */
    .pics-header {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .month-label {
        color: #fff;
        font-weight: 700;
        font-size: 13px;
        text-align: center;
        flex: 1;
    }
    .add-btn {
        display: flex;
        align-items: center;
        gap: 5px;
        background: rgba(0,0,0,0.5);
        padding: 5px 12px;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.12);
        color: #fff;
        font-weight: 700;
        font-size: 11px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .add-btn:hover { background: rgba(255,255,255,0.12); }

    .empty-note {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 10px;
        margin-bottom: 4px;
        color: #9ca3af;
        font-size: 12px;
    }

    /* Carousel */
    .carousel-row {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        margin-top: 6px;
        gap: 6px;
    }
    .arrow-btn {
        background: none;
        border: none;
        padding: 8px 4px;
        color: #bfbfbf;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }
    .arrow-btn:disabled {
        color: #3a3a3a;
        cursor: not-allowed;
    }

    .image-col {
        align-items: center;
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex: 1;
        min-width: 0;
    }
    .pic-title {
        color: #e5e7eb;
        font-size: 12px;
        font-weight: 500;
    }

    .big-pic-wrap {
        width: 100%;
        aspect-ratio: 1;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.08);
        background: rgba(0,0,0,0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        cursor: pointer;
    }
    .big-pic-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .pic-fallback {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        color: #8b8b8b;
        width: 100%;
        height: 100%;
    }
    .pic-fallback i {
        font-size: 22px;
        color: #8b8b8b;
    }
    .pic-fallback span {
        color: #bdbdbd;
        font-size: 11px;
    }

    /* Horizontal Thumbnails Carousel */
    .thumb-carousel {
        display: flex;
        flex-direction: row;
        gap: 10px;
        overflow-x: auto;
        padding-top: 12px;
        padding-bottom: 2px;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }
    .thumb-carousel::-webkit-scrollbar { display: none; }

    .thumb-card {
        border: 1px solid rgba(255,255,255,0.08);
        background: rgba(0,0,0,0.45);
        border-radius: 12px;
        padding: 8px;
        width: 135px;
        flex-shrink: 0;
        cursor: pointer;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }
    .thumb-card.active {
        border-color: rgba(34,197,94,0.45);
        background: rgba(18,36,20,0.55);
    }
    .thumb-month {
        color: #fff;
        font-weight: 700;
        font-size: 11px;
        margin-bottom: 6px;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .thumb-row {
        display: flex;
        flex-direction: row;
        gap: 6px;
        justify-content: center;
    }
    .thumb-img-box {
        width: 34px;
        height: 34px;
        border-radius: 6px;
        background: rgba(0,0,0,0.5);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .thumb-img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .thumb-img-box i {
        color: #666;
        font-size: 13px;
    }

    /* Modal Sheet for Upload */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.85);
        z-index: 1000;
        align-items: flex-end;
        justify-content: center;
    }
    .modal-backdrop.show { display: flex; }
    .modal-card {
        width: 100%;
        max-width: 480px;
        background: rgba(15,15,15,0.98);
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        border: 1px solid rgba(255,255,255,0.08);
        box-shadow: 0 -4px 20px rgba(0,0,0,0.8);
        padding: 16px;
        box-sizing: border-box;
    }
    .modal-header {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .modal-title {
        color: #fff;
        font-weight: 800;
        font-size: 16px;
    }
    .modal-close-btn {
        background: none;
        border: none;
        color: #fff;
        font-size: 18px;
        cursor: pointer;
        padding: 4px;
    }
    .modal-body {
        padding-top: 14px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .form-row {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 10px;
    }
    .form-label {
        color: #cbd5e1;
        width: 65px;
        font-size: 13px;
        font-weight: 500;
        flex-shrink: 0;
    }
    .date-picker-row {
        flex: 1;
        display: flex;
        gap: 8px;
    }
    .select-input {
        flex: 1;
        height: 38px;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.12);
        padding: 0 10px;
        color: #fff;
        background: rgba(0,0,0,0.5);
        font-size: 13px;
        outline: none;
    }
    .pick-btn {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 8px;
        padding: 0 12px;
        height: 38px;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.12);
        background: rgba(0,0,0,0.5);
        color: #fff;
        font-size: 13px;
        cursor: pointer;
    }
    .preview-box {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        background: #111;
        object-fit: cover;
        border: 1px solid rgba(255,255,255,0.2);
    }
    .clear-btn {
        display: flex;
        align-items: center;
        gap: 4px;
        background: none;
        border: none;
        color: #f87171;
        font-size: 12px;
        cursor: pointer;
        padding: 4px;
    }
    .submit-upload-btn {
        margin-top: 6px;
        height: 44px;
        border-radius: 10px;
        background: #22c55e;
        color: #000;
        font-weight: 800;
        font-size: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        width: 100%;
        transition: opacity 0.2s;
    }
    .submit-upload-btn:hover { opacity: 0.9; }

    /* Lightbox Modal */
    .lightbox-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.92);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 20px;
    }
    .lightbox-overlay.show { display: flex; }
    .lightbox-container {
        width: 100%;
        max-width: 480px;
        height: 80vh;
        background: rgba(20,20,20,0.95);
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,0.15);
        overflow: hidden;
        position: relative;
        display: flex;
        flex-direction: column;
    }
    .lightbox-header {
        position: absolute;
        top: 0; left: 0; right: 0;
        padding: 14px 16px;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        background: rgba(0,0,0,0.45);
        z-index: 10;
    }
    .lightbox-title {
        color: #fff;
        font-size: 16px;
        font-weight: 800;
    }
    .lightbox-close-btn {
        width: 36px;
        height: 36px;
        border-radius: 18px;
        background: rgba(0,0,0,0.6);
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .lightbox-img-wrap {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        padding: 20px;
    }
    .lightbox-img-wrap img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        border-radius: 10px;
    }

    .loading-state {
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255,255,255,0.6);
        padding: 80px 20px;
        gap: 10px;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="profile-bg">
    {{-- Loading indicator --}}
    <div id="profileLoading" class="loading-state">
        <i class="fas fa-spinner fa-spin"></i> Loading profile...
    </div>

    <div id="profileContent" style="display:none; display:flex; flex-direction:column; gap:12px;">
        {{-- Card 1: Profile --}}
        <div class="profile-card">
            <div class="profile-header-row">
                <span class="badge">Profile</span>
            </div>

            <div class="profile-row">
                <div class="avatar-wrap" onclick="triggerAvatarUpload()">
                    <img id="avatarImg" class="avatar-img" src="" alt="Profile" style="display:none;" onerror="onAvatarError()">
                    <div id="avatarInitials" class="avatar-text">SS</div>
                    <div class="avatar-badge">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>

                <div class="profile-meta">
                    <div class="profile-name" id="profileName">Samantha Sample</div>
                    <div class="profile-sub" id="profileSub">Female, 23 years</div>
                </div>
            </div>

            <div class="hw-row">
                <div class="hw-box">
                    <span class="hw-label">HEIGHT</span>
                    <span class="hw-value" id="hwHeight">150 cm</span>
                </div>
                <div class="hw-divider"></div>
                <div class="hw-box">
                    <span class="hw-label">WEIGHT</span>
                    <span class="hw-value" id="hwWeight">55 kg</span>
                </div>
                <div class="hw-divider"></div>
                <div class="hw-box">
                    <span class="hw-label">BMI</span>
                    <span class="hw-value" id="hwBmi">36.7</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Progress Pics --}}
        <div class="pics-card">
            <div class="pics-header">
                <span class="badge">Progress Pics</span>
                <div class="month-label" id="currentMonthLabel">October 2026</div>
                <button class="add-btn" type="button" onclick="openUploadModal()">
                    <i class="fas fa-plus"></i> ADD
                </button>
            </div>

            <div class="empty-note" id="emptyNote" style="display:none;">
                <i class="fas fa-info-circle"></i>
                <span>No images for this month</span>
            </div>

            <div class="carousel-row">
                <button class="arrow-btn" id="prevBtn" type="button" onclick="goPrevMonth()">
                    <i class="fas fa-chevron-left"></i>
                </button>

                {{-- Front --}}
                <div class="image-col">
                    <span class="pic-title">Front</span>
                    <div class="big-pic-wrap" onclick="viewCurrentPic('front')">
                        <img id="frontPicImg" class="big-pic-img" src="" alt="Front" style="display:none;" onerror="onPicError('front')">
                        <div class="pic-fallback" id="frontPicFallback">
                            <i class="far fa-image"></i>
                            <span>No image</span>
                        </div>
                    </div>
                </div>

                {{-- Side --}}
                <div class="image-col">
                    <span class="pic-title">Side</span>
                    <div class="big-pic-wrap" onclick="viewCurrentPic('side')">
                        <img id="sidePicImg" class="big-pic-img" src="" alt="Side" style="display:none;" onerror="onPicError('side')">
                        <div class="pic-fallback" id="sidePicFallback">
                            <i class="far fa-image"></i>
                            <span>No image</span>
                        </div>
                    </div>
                </div>

                {{-- Back --}}
                <div class="image-col">
                    <span class="pic-title">Back</span>
                    <div class="big-pic-wrap" onclick="viewCurrentPic('back')">
                        <img id="backPicImg" class="big-pic-img" src="" alt="Back" style="display:none;" onerror="onPicError('back')">
                        <div class="pic-fallback" id="backPicFallback">
                            <i class="far fa-image"></i>
                            <span>No image</span>
                        </div>
                    </div>
                </div>

                <button class="arrow-btn" id="nextBtn" type="button" onclick="goNextMonth()">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            {{-- Horizontal Months Carousel --}}
            <div class="thumb-carousel" id="thumbCarousel"></div>
        </div>
    </div>

    {{-- Hidden file input for avatar --}}
    <input type="file" id="avatarFileInput" accept="image/*" style="display:none;" onchange="onAvatarFileSelected(this)">
</div>

{{-- Upload Progress Pics Modal --}}
<div class="modal-backdrop" id="uploadModal" onclick="closeUploadModalOnBackdrop(event)">
    <div class="modal-card">
        <div class="modal-header">
            <span class="modal-title">Add Images</span>
            <button class="modal-close-btn" type="button" onclick="closeUploadModal()">✕</button>
        </div>

        <form id="progressPicsForm" onsubmit="submitProgressPics(event)" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="user_id" id="uploadUserId" value="">
            <div class="modal-body">
                {{-- Month / Year selector --}}
                <div class="form-row">
                    <span class="form-label">Month</span>
                    <div class="date-picker-row">
                        <select id="uploadYearSelect" class="select-input" onchange="updateUploadMonthValue()"></select>
                        <select id="uploadMonthSelect" class="select-input" onchange="updateUploadMonthValue()"></select>
                    </div>
                    <input type="hidden" name="month" id="finalUploadMonth">
                </div>

                {{-- Front Picker --}}
                <div class="form-row">
                    <span class="form-label">Front</span>
                    <div style="flex:1; display:flex; align-items:center; gap:10px;">
                        <input type="file" name="front_image" id="fileFront" accept="image/*" style="display:none;" onchange="previewPicker('front', this)">
                        <button type="button" class="pick-btn" id="btnPickFront" onclick="document.getElementById('fileFront').click()">
                            <i class="far fa-image"></i> Choose Image
                        </button>
                        <img id="prevFront" class="preview-box" src="" alt="" style="display:none;">
                        <button type="button" class="clear-btn" id="btnClearFront" style="display:none;" onclick="clearPicker('front')">
                            <i class="far fa-trash-alt"></i> Remove
                        </button>
                    </div>
                </div>

                {{-- Side Picker --}}
                <div class="form-row">
                    <span class="form-label">Side</span>
                    <div style="flex:1; display:flex; align-items:center; gap:10px;">
                        <input type="file" name="side_image" id="fileSide" accept="image/*" style="display:none;" onchange="previewPicker('side', this)">
                        <button type="button" class="pick-btn" id="btnPickSide" onclick="document.getElementById('fileSide').click()">
                            <i class="far fa-image"></i> Choose Image
                        </button>
                        <img id="prevSide" class="preview-box" src="" alt="" style="display:none;">
                        <button type="button" class="clear-btn" id="btnClearSide" style="display:none;" onclick="clearPicker('side')">
                            <i class="far fa-trash-alt"></i> Remove
                        </button>
                    </div>
                </div>

                {{-- Back Picker --}}
                <div class="form-row">
                    <span class="form-label">Back</span>
                    <div style="flex:1; display:flex; align-items:center; gap:10px;">
                        <input type="file" name="back_image" id="fileBack" accept="image/*" style="display:none;" onchange="previewPicker('back', this)">
                        <button type="button" class="pick-btn" id="btnPickBack" onclick="document.getElementById('fileBack').click()">
                            <i class="far fa-image"></i> Choose Image
                        </button>
                        <img id="prevBack" class="preview-box" src="" alt="" style="display:none;">
                        <button type="button" class="clear-btn" id="btnClearBack" style="display:none;" onclick="clearPicker('back')">
                            <i class="far fa-trash-alt"></i> Remove
                        </button>
                    </div>
                </div>

                <button type="submit" class="submit-upload-btn" id="btnSubmitUpload">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Lightbox Image Viewer Modal --}}
<div class="lightbox-overlay" id="lightboxModal" onclick="closeLightboxOnBackdrop(event)">
    <div class="lightbox-container">
        <div class="lightbox-header">
            <span class="lightbox-title" id="lightboxTitle">Photo</span>
            <button class="lightbox-close-btn" type="button" onclick="closeLightbox()">✕</button>
        </div>
        <div class="lightbox-img-wrap">
            <img id="lightboxImg" src="" alt="Enlarged photo">
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let profileData = {};
let progressMonths = [];
let currentMonthIndex = -1;

const pad = (n) => (n < 10 ? `0${n}` : String(n));

const niceMonth = (ym) => {
    if (!ym) return '';
    const [y, m] = ym.split("-").map(Number);
    const d = new Date(y, (m || 1) - 1, 1);
    return d.toLocaleDateString('en-US', { month: "long", year: "numeric" });
};

const getInitials = (first, last) => {
    const f = (first ?? "").trim();
    const l = (last ?? "").trim();
    const a = (f[0] ?? "").toUpperCase();
    const b = (l[0] ?? "").toUpperCase();
    if (a && b) return `${a}${b}`;
    if (a) return `${a}${(f[1] ?? "P").toUpperCase()}`;
    return "SS";
};

const toUrl = (p) => {
    if (!p) return '';
    const idx = p.indexOf('/storage/');
    if (idx !== -1) {
        return window.location.origin + p.substring(idx);
    }
    return p;
};

/**
 * Builds unified, sorted month list from API months[] and images[key]
 * Exact match of BIFROST-MobileApp mapApiToMonths
 */
function buildMonthsList(data) {
    const map = new Map();

    (data.months || []).forEach((m) => {
        const key = `${m.year}-${pad(Number(m.month))}`;
        map.set(key, { key, label: m.label || niceMonth(key) });
    });

    Object.keys(data.images || {}).forEach((key) => {
        if (!map.has(key)) map.set(key, { key, label: niceMonth(key) });
    });

    for (const [key, pm] of map.entries()) {
        const arr = data.images?.[key] || [];
        let front, side, back;
        if (arr.length > 0) {
            const latest = [...arr].sort(
                (a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime()
            )[arr.length - 1];
            if (latest.front_image || latest.side_image || latest.back_image) {
                front = toUrl(latest.front_image);
                side = toUrl(latest.side_image);
                back = toUrl(latest.back_image);
            } else if (latest.image_path) {
                front = toUrl(latest.image_path);
                side = toUrl(latest.image_path);
                back = toUrl(latest.image_path);
            }
        }
        map.set(key, {
            ...pm,
            front_image: front,
            side_image: side,
            back_image: back,
        });
    }

    const arr = Array.from(map.values());
    arr.sort((a, b) => new Date(a.key + "-01").getTime() - new Date(b.key + "-01").getTime());
    return arr;
}

async function loadProfile() {
    try {
        const resp = await fetch('/mobile/data/profile', { headers: { 'Accept': 'application/json' } });
        const data = await resp.json();
        if (data.status === 'success' || data.user) {
            profileData = data;
            renderProfile(data);
        }
    } catch (e) {
        console.error('Error loading profile:', e);
    } finally {
        document.getElementById('profileLoading').style.display = 'none';
        document.getElementById('profileContent').style.display = 'flex';
    }
}

function renderProfile(data) {
    const m = data.member || {};
    const u = data.user || {};
    const firstName = m.firstname || data.firstname || '';
    const lastName  = m.lastname  || data.lastname  || '';
    const fullName  = `${firstName} ${lastName}`.trim() || u.name || 'Samantha Sample';

    // 1. Top Card Info
    document.getElementById('profileName').textContent = fullName;
    const gender = m.gender || 'Female';
    const age = m.age ? `${m.age} years` : '23 years';
    document.getElementById('profileSub').textContent = `${gender}, ${age}`;

    // 2. Avatar
    const initials = getInitials(firstName, lastName);
    document.getElementById('avatarInitials').textContent = initials;
    const avatarImg = document.getElementById('avatarImg');
    const avatarInitials = document.getElementById('avatarInitials');

    if (data.profileImage && !data.profileImage.includes('default-profile')) {
        const avUrl = toUrl(data.profileImage);
        avatarImg.src = avUrl + (avUrl.includes('?') ? '&' : '?') + 't=' + Date.now();
        avatarImg.style.display = 'block';
        avatarInitials.style.display = 'none';
    } else {
        avatarImg.style.display = 'none';
        avatarInitials.style.display = 'block';
    }

    // 3. Height / Weight / BMI
    document.getElementById('hwHeight').textContent = `${m.height || 150} cm`;
    document.getElementById('hwWeight').textContent = `${m.weight || 55} kg`;
    const bmrVal = m.bmr ? Number(m.bmr).toFixed(1) : '36.7';
    document.getElementById('hwBmi').textContent = bmrVal;

    // 4. User ID for upload form
    document.getElementById('uploadUserId').value = u.id || '';

    // 5. Progress Pics
    progressMonths = buildMonthsList(data);
    if (progressMonths.length > 0) {
        currentMonthIndex = progressMonths.length - 1; // Default to latest month
    } else {
        // Fallback to current calendar month
        const now = new Date();
        const curKey = `${now.getFullYear()}-${pad(now.getMonth() + 1)}`;
        progressMonths = [{ key: curKey, label: niceMonth(curKey) }];
        currentMonthIndex = 0;
    }

    updateCurrentMonthView();
    renderThumbnails();
}

function onAvatarError() {
    document.getElementById('avatarImg').style.display = 'none';
    document.getElementById('avatarInitials').style.display = 'block';
}

function triggerAvatarUpload() {
    document.getElementById('avatarFileInput').click();
}

async function onAvatarFileSelected(input) {
    const file = input.files[0];
    if (!file) return;
    const fd = new FormData();
    fd.append('profile_image', file);
    if (profileData.user?.id) {
        fd.append('user_id', profileData.user.id);
    }

    // Visual feedback while uploading
    const badge = document.querySelector('.avatar-badge');
    const origBadge = badge ? badge.innerHTML : '<i class="fas fa-camera"></i>';
    if (badge) badge.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    try {
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
        const resp = await fetch('/mobile/data/profile-image', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: fd
        });
        const d = await resp.json();
        if (d.success || d.status === 'success' || d.profile_image_url || d.image_url) {
            const rawUrl = d.profile_image_url || d.image_url || URL.createObjectURL(file);
            const cleanUrl = toUrl(rawUrl);
            const cacheBustUrl = cleanUrl + (cleanUrl.includes('?') ? '&' : '?') + 't=' + Date.now();

            const img = document.getElementById('avatarImg');
            img.src = cacheBustUrl;
            img.style.display = 'block';
            document.getElementById('avatarInitials').style.display = 'none';

            // Refresh the profile data so the state is immediately in sync
            await loadProfile();
        } else {
            alert(d.message || 'Failed to upload profile image.');
        }
    } catch (e) {
        console.error('Error uploading profile image:', e);
        alert('Failed to upload profile image.');
    } finally {
        if (badge) badge.innerHTML = origBadge;
        input.value = '';
    }
}

function updateCurrentMonthView() {
    const current = progressMonths[currentMonthIndex];
    if (!current) return;

    document.getElementById('currentMonthLabel').textContent = current.label || niceMonth(current.key);

    const hasImages = !!(current.front_image || current.side_image || current.back_image);
    document.getElementById('emptyNote').style.display = hasImages ? 'none' : 'flex';

    // Front
    setSlotImage('front', current.front_image);
    // Side
    setSlotImage('side', current.side_image);
    // Back
    setSlotImage('back', current.back_image);

    // Prev / Next Buttons
    document.getElementById('prevBtn').disabled = (currentMonthIndex <= 0);
    document.getElementById('nextBtn').disabled = (currentMonthIndex >= progressMonths.length - 1);

    // Update active highlight in thumbnails
    document.querySelectorAll('.thumb-card').forEach((el, idx) => {
        if (idx === currentMonthIndex) {
            el.classList.add('active');
            el.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' });
        } else {
            el.classList.remove('active');
        }
    });
}

function setSlotImage(slot, url) {
    const imgEl = document.getElementById(`${slot}PicImg`);
    const fallbackEl = document.getElementById(`${slot}PicFallback`);
    const fixedUrl = toUrl(url);
    if (fixedUrl) {
        imgEl.src = fixedUrl;
        imgEl.style.display = 'block';
        fallbackEl.style.display = 'none';
    } else {
        imgEl.src = '';
        imgEl.style.display = 'none';
        fallbackEl.style.display = 'flex';
    }
}

function onPicError(slot) {
    document.getElementById(`${slot}PicImg`).style.display = 'none';
    document.getElementById(`${slot}PicFallback`).style.display = 'flex';
}

function goPrevMonth() {
    if (currentMonthIndex > 0) {
        currentMonthIndex--;
        updateCurrentMonthView();
    }
}

function goNextMonth() {
    if (currentMonthIndex < progressMonths.length - 1) {
        currentMonthIndex++;
        updateCurrentMonthView();
    }
}

function selectMonthByIndex(index) {
    if (index >= 0 && index < progressMonths.length) {
        currentMonthIndex = index;
        updateCurrentMonthView();
    }
}

function renderThumbnails() {
    const cont = document.getElementById('thumbCarousel');
    cont.innerHTML = progressMonths.map((m, idx) => {
        const activeClass = idx === currentMonthIndex ? 'active' : '';
        const fImg = m.front_image ? `<img src="${toUrl(m.front_image)}" alt="" onerror="this.style.display='none';">` : `<i class="far fa-image"></i>`;
        const sImg = m.side_image  ? `<img src="${toUrl(m.side_image)}" alt="" onerror="this.style.display='none';">`  : `<i class="far fa-image"></i>`;
        const bImg = m.back_image  ? `<img src="${toUrl(m.back_image)}" alt="" onerror="this.style.display='none';">`  : `<i class="far fa-image"></i>`;

        return `
            <div class="thumb-card ${activeClass}" onclick="selectMonthByIndex(${idx})">
                <div class="thumb-month">${m.label}</div>
                <div class="thumb-row">
                    <div class="thumb-img-box">${fImg}</div>
                    <div class="thumb-img-box">${sImg}</div>
                    <div class="thumb-img-box">${bImg}</div>
                </div>
            </div>
        `;
    }).join('');
}

/* Lightbox Modal */
function viewCurrentPic(slot) {
    const current = progressMonths[currentMonthIndex];
    if (!current) return;
    const url = slot === 'front' ? current.front_image : (slot === 'side' ? current.side_image : current.back_image);
    if (!url) return;
    const cap = slot.charAt(0).toUpperCase() + slot.slice(1);
    openLightbox(url, `${cap} - ${current.label}`);
}

function openLightbox(url, title) {
    document.getElementById('lightboxImg').src = url;
    document.getElementById('lightboxTitle').textContent = title || 'Photo';
    document.getElementById('lightboxModal').classList.add('show');
}

function closeLightbox() {
    document.getElementById('lightboxModal').classList.remove('show');
}

function closeLightboxOnBackdrop(e) {
    if (e.target.id === 'lightboxModal') closeLightbox();
}

/* Upload Modal */
function openUploadModal() {
    populateUploadDatePickers();
    document.getElementById('uploadModal').classList.add('show');
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.remove('show');
}

function closeUploadModalOnBackdrop(e) {
    if (e.target.id === 'uploadModal') closeUploadModal();
}

function populateUploadDatePickers() {
    const yearSelect = document.getElementById('uploadYearSelect');
    const monthSelect = document.getElementById('uploadMonthSelect');
    const now = new Date();
    const curYear = now.getFullYear();
    const curMonth = now.getMonth() + 1;

    // Years
    const years = [curYear - 1, curYear, curYear + 1];
    yearSelect.innerHTML = years.map(y => `<option value="${y}" ${y === curYear ? 'selected' : ''}>${y}</option>`).join('');

    // Months
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    monthSelect.innerHTML = monthNames.map((name, i) => {
        const mNum = i + 1;
        return `<option value="${mNum}" ${mNum === curMonth ? 'selected' : ''}>${name}</option>`;
    }).join('');

    updateUploadMonthValue();
}

function updateUploadMonthValue() {
    const y = document.getElementById('uploadYearSelect').value;
    const m = pad(Number(document.getElementById('uploadMonthSelect').value));
    document.getElementById('finalUploadMonth').value = `${y}-${m}-01`;
}

function previewPicker(slot, input) {
    const file = input.files[0];
    if (!file) return;
    const cap = slot.charAt(0).toUpperCase() + slot.slice(1);
    const url = URL.createObjectURL(file);
    document.getElementById(`prev${cap}`).src = url;
    document.getElementById(`prev${cap}`).style.display = 'block';
    document.getElementById(`btnPick${cap}`).style.display = 'none';
    document.getElementById(`btnClear${cap}`).style.display = 'flex';
}

function clearPicker(slot) {
    const cap = slot.charAt(0).toUpperCase() + slot.slice(1);
    const input = document.getElementById(`file${cap}`);
    input.value = '';
    document.getElementById(`prev${cap}`).src = '';
    document.getElementById(`prev${cap}`).style.display = 'none';
    document.getElementById(`btnPick${cap}`).style.display = 'flex';
    document.getElementById(`btnClear${cap}`).style.display = 'none';
}

async function submitProgressPics(e) {
    e.preventDefault();
    const form = document.getElementById('progressPicsForm');
    const fd = new FormData(form);

    const hasFront = document.getElementById('fileFront').files.length > 0;
    const hasSide  = document.getElementById('fileSide').files.length > 0;
    const hasBack  = document.getElementById('fileBack').files.length > 0;

    if (!hasFront || !hasSide || !hasBack) {
        alert('Please select all 3 photos (Front, Side, and Back).');
        return;
    }

    const btn = document.getElementById('btnSubmitUpload');
    btn.disabled = true;
    btn.textContent = 'Uploading...';

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const resp = await fetch('/mobile/data/monthly-images', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: fd
        });
        const d = await resp.json();
        if (d.status === 'success') {
            alert('Progress pics uploaded successfully!');
            closeUploadModal();
            // Clear form
            clearPicker('front');
            clearPicker('side');
            clearPicker('back');
            loadProfile();
        } else {
            alert(d.message || 'Failed to upload photos.');
        }
    } catch (err) {
        alert('An error occurred during upload.');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Upload';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadProfile();
});
</script>
@endpush

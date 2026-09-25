@extends('mobile.layout.mobile-layout')

@push('head-styles')
<style>
    .profile-bg {
        min-height: calc(100vh - 120px);
        background-image: url('{{ asset('img/valhalla-bg.jpg') }}');
        background-size: cover;
        background-position: center;
        padding: 16px;
    }

    /* Avatar section */
    .avatar-section {
        display: flex; flex-direction: column; align-items: center;
        margin-bottom: 20px;
    }
    .avatar-wrap {
        position: relative; width: 90px; height: 90px;
        border-radius: 45px; overflow: hidden;
        border: 2px solid rgba(255,255,255,0.3);
        background: rgba(0,0,0,0.5);
        cursor: pointer;
    }
    .avatar-img { width: 100%; height: 100%; object-fit: cover; }
    .avatar-initials {
        width: 100%; height: 100%; display: flex;
        align-items: center; justify-content: center;
        color: #fff; font-size: 28px; font-weight: 800;
    }
    .avatar-edit-overlay {
        position: absolute; inset: 0;
        background: rgba(0,0,0,0.5);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.2s;
    }
    .avatar-wrap:hover .avatar-edit-overlay { opacity: 1; }
    .avatar-edit-overlay i { color: #fff; font-size: 22px; }
    .avatar-name {
        margin-top: 10px; color: #fff;
        font-size: 18px; font-weight: 700;
    }
    .avatar-sub {
        color: rgba(255,255,255,0.5); font-size: 13px; margin-top: 2px;
    }

    /* Info cards */
    .info-card {
        background: rgba(0,0,0,0.55); border-radius: 14px;
        padding: 16px; margin-bottom: 14px;
    }
    .info-card-title {
        color: rgba(255,255,255,0.5); font-size: 11px;
        font-weight: 700; letter-spacing: 1px;
        text-transform: uppercase; margin-bottom: 12px;
    }
    .info-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: rgba(255,255,255,0.5); font-size: 13px; }
    .info-value { color: #fff; font-size: 13px; font-weight: 600; }

    .body-stats-grid {
        display: grid; grid-template-columns: 1fr 1fr 1fr;
        gap: 10px;
    }
    .body-stat {
        background: rgba(255,255,255,0.06);
        border-radius: 10px; padding: 12px 8px; text-align: center;
    }
    .body-stat-value { color: #fff; font-size: 18px; font-weight: 800; }
    .body-stat-unit { color: rgba(255,255,255,0.4); font-size: 10px; }
    .body-stat-label { color: rgba(255,255,255,0.5); font-size: 10px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; margin-top: 2px; }

    /* Monthly photos */
    .month-tab-row {
        display: flex; gap: 8px; overflow-x: auto;
        scrollbar-width: none; margin-bottom: 14px;
        padding-bottom: 2px;
    }
    .month-tab-row::-webkit-scrollbar { display: none; }
    .month-tab {
        flex-shrink: 0; padding: 6px 14px;
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 20px; background: rgba(0,0,0,0.4);
        color: rgba(255,255,255,0.6); font-size: 12px; font-weight: 600;
        cursor: pointer; transition: all 0.2s; white-space: nowrap;
    }
    .month-tab.active {
        background: rgba(255,255,255,0.2);
        border-color: #fff; color: #fff;
    }

    .photo-row {
        display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px;
        margin-bottom: 14px;
    }
    .photo-slot {
        aspect-ratio: 3/4; border-radius: 10px;
        overflow: hidden; background: rgba(255,255,255,0.06);
        position: relative; cursor: pointer;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
    }
    .photo-slot img {
        width: 100%; height: 100%; object-fit: cover;
        position: absolute; inset: 0;
    }
    .photo-slot-label {
        color: rgba(255,255,255,0.4); font-size: 10px; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.5px;
        position: relative; z-index: 1;
    }
    .photo-slot-icon {
        color: rgba(255,255,255,0.25); font-size: 24px; margin-bottom: 6px;
        position: relative; z-index: 1;
    }
    .photo-slot.has-img .photo-slot-label,
    .photo-slot.has-img .photo-slot-icon { display: none; }

    /* Upload form */
    .upload-btn {
        display: block; width: 100%;
        padding: 12px; background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 10px; color: #fff;
        font-size: 14px; font-weight: 600;
        text-align: center; cursor: pointer;
        transition: background 0.2s;
    }
    .upload-btn:hover { background: rgba(255,255,255,0.18); }

    /* Lightbox */
    .lightbox-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.92); z-index: 1000;
        align-items: center; justify-content: center;
        flex-direction: column;
    }
    .lightbox-overlay.show { display: flex; }
    .lightbox-overlay img { max-width: 92vw; max-height: 80vh; border-radius: 10px; object-fit: contain; }
    .lightbox-close {
        position: absolute; top: 16px; right: 20px;
        color: #fff; font-size: 28px; cursor: pointer;
        background: none; border: none; padding: 4px;
    }

    .loading-state {
        display: flex; align-items: center; justify-content: center;
        color: rgba(255,255,255,0.6); padding: 60px; gap: 10px;
    }
</style>
@endpush

@section('content')
<div class="profile-bg">
    <div id="profileLoading" class="loading-state">
        <i class="fas fa-spinner fa-spin"></i> Loading profile...
    </div>

    <div id="profileContent" style="display:none;">
        {{-- Avatar --}}
        <div class="avatar-section">
            <div class="avatar-wrap" onclick="document.getElementById('avatarInput').click()">
                <img id="avatarImg" src="" alt="Profile" class="avatar-img" style="display:none;">
                <div id="avatarInitials" class="avatar-initials"></div>
                <div class="avatar-edit-overlay"><i class="fas fa-camera"></i></div>
            </div>
            <div class="avatar-name" id="profileName"></div>
            <div class="avatar-sub" id="profileSub"></div>
            <input type="file" id="avatarInput" accept="image/*" style="display:none;" onchange="uploadAvatar(this)">
        </div>

        {{-- Body Stats --}}
        <div class="info-card">
            <div class="info-card-title">Body Stats</div>
            <div class="body-stats-grid">
                <div class="body-stat">
                    <div class="body-stat-value" id="statAge">—</div>
                    <div class="body-stat-label">Age</div>
                </div>
                <div class="body-stat">
                    <div class="body-stat-value" id="statHeight">—</div>
                    <div class="body-stat-unit">cm</div>
                    <div class="body-stat-label">Height</div>
                </div>
                <div class="body-stat">
                    <div class="body-stat-value" id="statWeight">—</div>
                    <div class="body-stat-unit">kg</div>
                    <div class="body-stat-label">Weight</div>
                </div>
            </div>
        </div>

        {{-- Member Info --}}
        <div class="info-card">
            <div class="info-card-title">Member Info</div>
            <div class="info-row">
                <span class="info-label">Gender</span>
                <span class="info-value" id="infoGender">—</span>
            </div>
            <div class="info-row">
                <span class="info-label">BMR</span>
                <span class="info-value" id="infoBMR">—</span>
            </div>
            <div class="info-row">
                <span class="info-label">Primary Goal</span>
                <span class="info-value" id="infoGoal">—</span>
            </div>
            <div class="info-row">
                <span class="info-label">Subscription</span>
                <span class="info-value" id="infoSub">—</span>
            </div>
            <div class="info-row">
                <span class="info-label">Start Date</span>
                <span class="info-value" id="infoStart">—</span>
            </div>
        </div>

        {{-- Monthly Progress Photos --}}
        <div class="info-card">
            <div class="info-card-title">Monthly Progress</div>

            <div class="month-tab-row" id="monthTabs"></div>

            <div class="photo-row" id="photoRow">
                <div class="photo-slot" id="slotFront">
                    <i class="fas fa-user photo-slot-icon"></i>
                    <span class="photo-slot-label">Front</span>
                </div>
                <div class="photo-slot" id="slotSide">
                    <i class="fas fa-user photo-slot-icon"></i>
                    <span class="photo-slot-label">Side</span>
                </div>
                <div class="photo-slot" id="slotBack">
                    <i class="fas fa-user photo-slot-icon"></i>
                    <span class="photo-slot-label">Back</span>
                </div>
            </div>

            {{-- Upload new monthly photo set --}}
            <form id="photoUploadForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" id="uploadUserId">
                <input type="hidden" name="month" id="uploadMonth">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:12px;">
                    <label class="upload-btn" style="font-size:11px;padding:8px 4px;">
                        <i class="fas fa-plus" style="font-size:12px;"></i><br>Front
                        <input type="file" name="front_image" accept="image/*" style="display:none;" onchange="previewUpload(this,'front')">
                    </label>
                    <label class="upload-btn" style="font-size:11px;padding:8px 4px;">
                        <i class="fas fa-plus" style="font-size:12px;"></i><br>Side
                        <input type="file" name="side_image" accept="image/*" style="display:none;" onchange="previewUpload(this,'side')">
                    </label>
                    <label class="upload-btn" style="font-size:11px;padding:8px 4px;">
                        <i class="fas fa-plus" style="font-size:12px;"></i><br>Back
                        <input type="file" name="back_image" accept="image/*" style="display:none;" onchange="previewUpload(this,'back')">
                    </label>
                </div>
                <button type="button" onclick="submitPhotos()" class="upload-btn">
                    <i class="fas fa-cloud-upload-alt"></i> Upload Photos
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Lightbox --}}
<div class="lightbox-overlay" id="lightbox" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="closeLightbox()">✕</button>
    <img id="lightboxImg" src="" alt="">
</div>
@endsection

@push('scripts')
<script>
let profileData = {};
let allImages = {};
let selectedMonthKey = null;

async function loadProfile() {
    try {
        const resp = await fetch('/mobile/data/profile', { headers: { 'Accept': 'application/json' } });
        const data = await resp.json();
        if (data.status === 'success') {
            profileData = data;
            allImages = data.images || {};
            renderProfile(data);
        }
    } catch(e) {
        console.error('Profile load error', e);
    } finally {
        document.getElementById('profileLoading').style.display = 'none';
        document.getElementById('profileContent').style.display = 'block';
    }
}

function renderProfile(data) {
    const m = data.member || {};
    const firstName = m.firstname || data.firstname || '';
    const lastName  = m.lastname  || data.lastname  || '';
    const fullName  = `${firstName} ${lastName}`.trim();

    // Avatar
    if (data.profileImage && !data.profileImage.includes('default-profile')) {
        const img = document.getElementById('avatarImg');
        img.src = data.profileImage;
        img.style.display = 'block';
        document.getElementById('avatarInitials').style.display = 'none';
    } else {
        const initials = (firstName[0] || '') + (lastName[0] || '');
        document.getElementById('avatarInitials').textContent = initials.toUpperCase() || '?';
    }
    document.getElementById('profileName').textContent = fullName || 'Member';
    document.getElementById('profileSub').textContent = m.subscription_level || '';

    // Stats
    document.getElementById('statAge').textContent    = m.age    || '—';
    document.getElementById('statHeight').textContent = m.height || '—';
    document.getElementById('statWeight').textContent = m.weight || '—';

    // Info
    document.getElementById('infoGender').textContent = m.gender || '—';
    document.getElementById('infoBMR').textContent    = m.bmr ? `${m.bmr} kcal` : '—';
    document.getElementById('infoGoal').textContent   = m.primary_goal || '—';
    document.getElementById('infoSub').textContent    = m.subscription_level || '—';
    document.getElementById('infoStart').textContent  = m.startdate || '—';

    // Upload user id
    document.getElementById('uploadUserId').value = data.user?.id || '';

    // Month tabs
    renderMonthTabs(data.months || [], allImages);
}

function renderMonthTabs(months, images) {
    const tabsEl = document.getElementById('monthTabs');
    const allKeys = Array.from(new Set([
        ...months.map(m => `${m.year}-${m.month}`),
        ...Object.keys(images)
    ])).sort().reverse();

    if (!allKeys.length) return;
    if (!selectedMonthKey) selectedMonthKey = allKeys[0];

    tabsEl.innerHTML = allKeys.map(k => {
        const d = new Date(k + '-01');
        const label = d.toLocaleDateString('en', { month: 'short', year: 'numeric' });
        return `<button class="month-tab ${k === selectedMonthKey ? 'active' : ''}" data-key="${k}" onclick="selectMonth('${k}', this)">${label}</button>`;
    }).join('');

    document.getElementById('uploadMonth').value = selectedMonthKey + '-01';
    showMonthPhotos(selectedMonthKey);
}

function selectMonth(key, btn) {
    selectedMonthKey = key;
    document.querySelectorAll('.month-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('uploadMonth').value = key + '-01';
    showMonthPhotos(key);
}

function showMonthPhotos(key) {
    const imgs = allImages[key];
    const entry = imgs && imgs[0] ? imgs[0] : null;

    setPhoto('slotFront', entry?.front_image || null, 'Front');
    setPhoto('slotSide',  entry?.side_image  || null, 'Side');
    setPhoto('slotBack',  entry?.back_image  || null, 'Back');
}

function setPhoto(slotId, url, label) {
    const slot = document.getElementById(slotId);
    // Remove any existing img
    const existingImg = slot.querySelector('img');
    if (existingImg) existingImg.remove();
    slot.classList.remove('has-img');

    if (url) {
        slot.classList.add('has-img');
        const img = document.createElement('img');
        img.src = url;
        img.onclick = () => openLightbox(url);
        slot.insertBefore(img, slot.firstChild);
    }
}

function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightbox').classList.add('show');
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('show');
}

async function uploadAvatar(input) {
    const file = input.files[0];
    if (!file) return;
    const fd = new FormData();
    fd.append('profile_image', file);
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const resp = await fetch('/mobile/data/profile-image', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: fd
        });
        const data = await resp.json();
        if (data.status === 'success' || data.image_url) {
            const url = data.image_url || URL.createObjectURL(file);
            document.getElementById('avatarImg').src = url;
            document.getElementById('avatarImg').style.display = 'block';
            document.getElementById('avatarInitials').style.display = 'none';
        }
    } catch(e) { alert('Failed to upload photo.'); }
}

function previewUpload(input, side) {
    if (!input.files[0]) return;
    const url = URL.createObjectURL(input.files[0]);
    const slotId = side === 'front' ? 'slotFront' : side === 'side' ? 'slotSide' : 'slotBack';
    setPhoto(slotId, url, side.charAt(0).toUpperCase() + side.slice(1));
}

async function submitPhotos() {
    const form = document.getElementById('photoUploadForm');
    const fd = new FormData(form);
    const hasFront = form.querySelector('[name="front_image"]').files.length > 0;
    const hasSide  = form.querySelector('[name="side_image"]').files.length > 0;
    const hasBack  = form.querySelector('[name="back_image"]').files.length > 0;

    if (!hasFront || !hasSide || !hasBack) {
        alert('Please select all 3 photos (front, side, back).');
        return;
    }
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const resp = await fetch('/mobile/data/monthly-images', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: fd
        });
        const data = await resp.json();
        if (data.status === 'success') {
            alert('Photos uploaded successfully!');
            loadProfile();
        } else {
            alert(data.message || 'Upload failed.');
        }
    } catch(e) { alert('Failed to upload photos.'); }
}

document.addEventListener('DOMContentLoaded', () => loadProfile());
</script>
@endpush

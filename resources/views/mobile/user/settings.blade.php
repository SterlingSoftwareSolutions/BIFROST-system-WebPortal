@extends('mobile.layout.mobile-layout')

@push('head-styles')
<style>
    .mem-input {
        background: transparent;
        border: none;
        border-bottom: 1px solid #52525b;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        width: 135px;
        text-align: right;
        outline: none;
    }
    .mem-input:focus { border-bottom-color: #10b981; }
    .mem-input-half { width: 40px; text-align: center; }

    /* Membership Card Matching Mobile App */
    .mem-card {
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 18px;
    }
    .mem-header-card {
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        margin-bottom: 10px;
    }
    .mem-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 22px;
    }
    .mem-label {
        color: #9ca3af;
        font-size: 12px;
        font-weight: 500;
    }
    .mem-val {
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        text-align: right;
    }
    .mem-edit-field {
        display: none !important;
    }
    #membership-modal.is-editing .mem-edit-field {
        display: inline-block !important;
    }
    #membership-modal.is-editing #edit-name-container,
    #membership-modal.is-editing #edit-hw-container {
        display: flex !important;
    }
    #membership-modal.is-editing #mem-save-btn {
        display: block !important;
    }
    #membership-modal.is-editing .mem-val {
        display: none !important;
    }

    .settings-bg {
        min-height: calc(100vh - 120px);
        background-image: linear-gradient(rgba(0,0,0,0.35), rgba(0,0,0,0.35)), url('{{ asset('img/valhalla-bg.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        padding: 12px;
    }
    .version-banner {
        background: #3f3f46;
        border-radius: 12px;
        padding: 11px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }
    .version-title {
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .version-text {
        color: #a1a1aa;
        font-size: 10.5px;
        font-weight: 500;
    }
    .settings-card {
        background: #09090b; /* Very dark background for cards */
        border: 1px solid #27272a;
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 14px;
    }
    .settings-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 11px 14px;
        border-bottom: 1px solid #27272a;
        cursor: pointer;
        transition: background 0.15s;
    }
    .settings-item:last-child {
        border-bottom: none;
    }
    .settings-item:active {
        background: rgba(255, 255, 255, 0.05);
    }
    .settings-item-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .settings-item-icon {
        color: #a1a1aa;
        font-size: 13px;
        width: 18px;
        text-align: center;
    }
    .settings-item-label {
        color: #fff;
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: 0.1px;
    }
    .settings-item-arrow {
        color: #a1a1aa;
        font-size: 12px;
        font-weight: bold;
    }

    /* Toggle Switch CSS */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 22px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #52525b;
        transition: .3s;
        border-radius: 30px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    input:checked + .toggle-slider {
        background-color: #22c55e;
    }
    input:checked + .toggle-slider:before {
        transform: translateX(18px);
    }

    /* Modals CSS */
    .settings-back {
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .modal-content-card {
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 12px;
    }
    .modal-title {
        color: #fff;
        font-size: 14.5px;
        font-weight: 800;
        margin-bottom: 8px;
    }
    .modal-body {
        color: #e5e7eb;
        font-size: 11.5px;
        line-height: 20px;
    }

    @media (max-width: 360px) {
        .settings-item-label {
            font-size: 11.5px;
        }
        .version-title {
            font-size: 10px;
        }
        .version-text {
            font-size: 10px;
        }
        .settings-item {
            padding: 10px 12px;
        }
        .version-banner {
            padding: 9px 12px;
        }
    }
</style>
@endpush

@section('content')
<div class="settings-bg">
    
    <!-- Main Settings View -->
    <div id="main-settings">
        <!-- Version Banner -->
        <div class="version-banner">
            <span class="version-title">VALHALLA PERFORMANCE</span>
            <span class="version-text">v 1.2.0</span>
        </div>

        <!-- Toggles Section -->
        <div class="settings-card">
            <div class="settings-item">
                <div class="settings-item-left">
                    <div class="settings-item-icon"><i class="fa-solid fa-location-dot"></i></div>
                    <span class="settings-item-label">Location</span>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="settings-item">
                <div class="settings-item-left">
                    <div class="settings-item-icon"><i class="fa-solid fa-camera"></i></div>
                    <span class="settings-item-label">Camera</span>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="settings-item">
                <div class="settings-item-left">
                    <div class="settings-item-icon"><i class="fa-regular fa-bell"></i></div>
                    <span class="settings-item-label">Push Notifications</span>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>

        <!-- Links Section -->
        <div class="settings-card">
            <div class="settings-item" onclick="openMembershipModal()">
                <div class="settings-item-left">
                    <div class="settings-item-icon"><i class="fa-regular fa-credit-card"></i></div>
                    <span class="settings-item-label">Membership</span>
                </div>
                <span class="settings-item-arrow"><i class="fa-solid fa-chevron-right"></i></span>
            </div>
            <div class="settings-item" onclick="openModal('terms-modal')">
                <div class="settings-item-left">
                    <div class="settings-item-icon"><i class="fa-regular fa-file-lines"></i></div>
                    <span class="settings-item-label">Terms of Use</span>
                </div>
                <span class="settings-item-arrow"><i class="fa-solid fa-chevron-right"></i></span>
            </div>
            <div class="settings-item" onclick="openModal('privacy-modal')">
                <div class="settings-item-left">
                    <div class="settings-item-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <span class="settings-item-label">Privacy Policy</span>
                </div>
                <span class="settings-item-arrow"><i class="fa-solid fa-chevron-right"></i></span>
            </div>
        </div>
    </div>

    <!-- Membership Modal -->
    <div id="membership-modal" class="settings-modal" style="display: none; overflow-y: auto;">
        
        <!-- Header Box -->
        <div class="mem-header-card">
            <div style="display: flex; align-items: center; gap: 8px; color: #fff; font-size: 13.5px; font-weight: 700;">
                <i class="fa-regular fa-credit-card" style="font-size: 14px; color: #9ca3af;"></i> Membership
            </div>
            <button id="mem-edit-btn" onclick="toggleEditMembership()" style="background: transparent; border: 1px solid #71717a; color: #e5e7eb; border-radius: 16px; padding: 4px 10px; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 5px; cursor: pointer;">
                <i id="mem-edit-icon" class="fa-regular fa-pen-to-square"></i> <span id="mem-edit-text">Edit</span>
            </button>
        </div>
        
        <!-- Info Card Matching Mobile App -->
        <div class="mem-card">
            <!-- Profile Header -->
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px;">
                <input type="file" id="mem-avatar-file" accept="image/*" style="display: none;" onchange="handleMemAvatarSelected(event)" />
                <div id="membership-avatar-wrap" onclick="handleMemAvatarClick()" style="width: 60px; height: 60px; border-radius: 30px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); position: relative; display: flex; align-items: center; justify-content: center; flex-shrink: 0; cursor: pointer;">
                    <span id="membership-avatar-initials" style="color: #fff; font-size: 20px; font-weight: 800; user-select: none;">TC</span>
                    <img id="membership-photo" src="" alt="Profile" style="display: none; width: 100%; height: 100%; border-radius: 30px; object-fit: cover;" onerror="this.style.display='none'; document.getElementById('membership-avatar-initials').style.display='block';" />
                    <div style="position: absolute; right: -2px; bottom: -2px; width: 20px; height: 20px; border-radius: 10px; background: #22c55e; border: 2px solid rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 9px;">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                </div>
                <div>
                    <div style="color: #fff; font-size: 13.5px; font-weight: 700;">Profile Photo</div>
                    <div id="membership-avatar-hint" style="color: #9ca3af; font-size: 10.5px; font-weight: 500;">Tap Edit to change photo</div>
                </div>
            </div>

            <!-- Info Grid -->
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div class="mem-row">
                    <span class="mem-label">Username</span>
                    <span id="mem-username" class="mem-val">...</span>
                    <input type="text" id="edit-username" class="mem-input mem-edit-field" placeholder="Username" />
                </div>
                <div class="mem-row">
                    <span class="mem-label">Name</span>
                    <span id="mem-name" class="mem-val">...</span>
                    <div id="edit-name-container" class="mem-edit-field" style="gap: 8px;">
                        <input type="text" id="edit-firstname" class="mem-input" style="width: 65px;" placeholder="First" />
                        <input type="text" id="edit-lastname" class="mem-input" style="width: 65px;" placeholder="Last" />
                    </div>
                </div>
                <div class="mem-row">
                    <span class="mem-label">Email</span>
                    <span id="mem-email" class="mem-val">...</span>
                </div>
                <div class="mem-row">
                    <span class="mem-label">Gender</span>
                    <span id="mem-gender" class="mem-val">...</span>
                </div>
                <div class="mem-row">
                    <span class="mem-label">Age</span>
                    <span id="mem-age" class="mem-val">...</span>
                </div>
                <div class="mem-row">
                    <span class="mem-label">Plan</span>
                    <span id="mem-plan" class="mem-val">...</span>
                    <select id="edit-plan" class="mem-input mem-edit-field" style="text-align: right;">
                        <option value="Unlimited">Unlimited</option>
                        <option value="10 Pack">10 Pack</option>
                        <option value="Online">Online</option>
                        <option value="Basic">Basic</option>
                        <option value="Pro">Pro</option>
                    </select>
                </div>
                <div class="mem-row">
                    <span class="mem-label">Active</span>
                    <span id="mem-active" class="mem-val">...</span>
                </div>
                <div class="mem-row">
                    <span class="mem-label">Start date</span>
                    <span id="mem-startdate" class="mem-val">...</span>
                </div>
                <div class="mem-row">
                    <span class="mem-label">Primary goal</span>
                    <span id="mem-goal" class="mem-val">...</span>
                    <select id="edit-goal" class="mem-input mem-edit-field" style="text-align: right;">
                        <option value="Weight Loss">Weight Loss</option>
                        <option value="Build Muscle">Build Muscle</option>
                        <option value="Competitive Weightlifting">Competitive Weightlifting</option>
                        <option value="General Fitness">General Fitness</option>
                    </select>
                </div>
                <div class="mem-row">
                    <span class="mem-label">Height / Weight</span>
                    <span id="mem-hw" class="mem-val">...</span>
                    <div id="edit-hw-container" class="mem-edit-field" style="align-items: center; gap: 4px; color: #a1a1aa;">
                        <input type="number" id="edit-height" class="mem-input mem-input-half" placeholder="cm" /> cm /
                        <input type="number" id="edit-weight" class="mem-input mem-input-half" placeholder="kg" /> kg
                    </div>
                </div>
                <div class="mem-row">
                    <span class="mem-label">BMI</span>
                    <span id="mem-bmi" class="mem-val">...</span>
                </div>
                <div class="mem-row">
                    <span class="mem-label">Phone</span>
                    <span id="mem-phone" class="mem-val">...</span>
                    <input type="text" id="edit-phone" class="mem-input mem-edit-field" placeholder="Phone" />
                </div>
                <div class="mem-row" style="align-items: flex-start;">
                    <span class="mem-label">Address</span>
                    <span id="mem-address" class="mem-val" style="max-width: 60%; word-break: break-word;">...</span>
                    <input type="text" id="edit-address" class="mem-input mem-edit-field" placeholder="Address" />
                </div>
            </div>
            
            <div id="mem-save-btn" class="mem-edit-field" style="margin-top: 20px;">
                <button onclick="saveMembership()" style="width: 100%; background: #10b981; color: #000; border: none; border-radius: 20px; padding: 10px; font-size: 13px; font-weight: 700; cursor: pointer;">
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Terms Modal -->
    <div id="terms-modal" class="settings-modal" style="display: none;">
        <div class="modal-content-card">
            <div class="modal-title">Terms of Use</div>
            <div class="modal-body">
                Placeholder Terms. Replace with the final legal text or load from an API.<br><br>
                • Keep your account secure.<br>
                • You’re responsible for uploaded content.<br>
                • Service is provided “as is”.<br>
                • Contact support for disputes/questions.
            </div>
        </div>
    </div>

    <!-- Privacy Modal -->
    <div id="privacy-modal" class="settings-modal" style="display: none;">
        <div class="modal-content-card">
            <div class="modal-title">Privacy Policy</div>
            <div class="modal-body">
                This is placeholder copy. Replace with the final policy or fetch from your backend.<br><br>
                • We collect profile info, workout data, and progress photos you upload.<br>
                • Data is used to power training features and analytics.<br>
                • You may request data deletion via support.<br>
                • We do not sell personal data.
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function openModal(modalId) {
        document.getElementById('main-settings').style.display = 'none';
        document.getElementById(modalId).style.display = 'block';
        window.currentActiveModal = modalId;
        window.scrollTo(0, 0);
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
        document.getElementById('main-settings').style.display = 'block';
        window.currentActiveModal = null;
        window.scrollTo(0, 0);
    }

    let isMembershipEditing = false;
    let membershipDataCache = {};
    let pendingAvatarFile = null;

    function handleMemAvatarClick() {
        if (!isMembershipEditing) return;
        document.getElementById('mem-avatar-file').click();
    }

    function handleMemAvatarSelected(event) {
        const file = event.target.files?.[0];
        if (!file) return;
        pendingAvatarFile = file;

        const reader = new FileReader();
        reader.onload = function(e) {
            const photoEl = document.getElementById('membership-photo');
            photoEl.src = e.target.result;
            photoEl.style.display = 'block';
            document.getElementById('membership-avatar-initials').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    function toggleEditMembership() {
        isMembershipEditing = !isMembershipEditing;
        const modal = document.getElementById('membership-modal');
        
        if (isMembershipEditing) {
            modal.classList.add('is-editing');
            document.getElementById('mem-edit-icon').className = 'fa-solid fa-xmark';
            document.getElementById('mem-edit-text').textContent = 'Cancel';
            document.getElementById('membership-avatar-hint').textContent = 'Tap photo to change (uploads on Save)';
            
            // Populate
            const user = membershipDataCache.user || {};
            const member = membershipDataCache.member || {};
            document.getElementById('edit-username').value = user.name || '';
            document.getElementById('edit-firstname').value = member.firstname || '';
            document.getElementById('edit-lastname').value = member.lastname || '';
            document.getElementById('edit-plan').value = member.subscription_level || member.subscription || 'Unlimited';
            document.getElementById('edit-goal').value = member.primary_goal || 'Weight Loss';
            document.getElementById('edit-height').value = member.height || '';
            document.getElementById('edit-weight').value = member.weight || '';
            document.getElementById('edit-phone').value = member.phone || '';
            document.getElementById('edit-address').value = member.address || '';
        } else {
            modal.classList.remove('is-editing');
            document.getElementById('mem-edit-icon').className = 'fa-regular fa-pen-to-square';
            document.getElementById('mem-edit-text').textContent = 'Edit';
            document.getElementById('membership-avatar-hint').textContent = 'Tap Edit to change photo';
            pendingAvatarFile = null;
        }
    }

    async function saveMembership() {
        const payload = {
            username: document.getElementById('edit-username').value,
            firstname: document.getElementById('edit-firstname').value,
            lastname: document.getElementById('edit-lastname').value,
            subscription_level: document.getElementById('edit-plan').value,
            primary_goal: document.getElementById('edit-goal').value,
            height: document.getElementById('edit-height').value,
            weight: document.getElementById('edit-weight').value,
            phone: document.getElementById('edit-phone').value,
            address: document.getElementById('edit-address').value
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const btn = document.querySelector('#mem-save-btn button');
        const oldText = btn.textContent;
        btn.textContent = 'Saving...';
        btn.disabled = true;

        try {
            // Save profile fields
            const res = await fetch('/mobile/data/update-profile', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            // If an avatar was picked, upload it too
            if (pendingAvatarFile) {
                const fd = new FormData();
                fd.append('profile_image', pendingAvatarFile);
                if (membershipDataCache.user?.id) {
                    fd.append('user_id', membershipDataCache.user.id);
                }
                await fetch('/mobile/data/profile-image', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: fd
                });
                pendingAvatarFile = null;
            }

            if (data.status === 'success') {
                populateMembershipData(data);
                toggleEditMembership();
            } else {
                alert('Error saving data');
            }
        } catch (err) {
            console.error('Error saving membership:', err);
            alert('Failed to save data.');
        } finally {
            btn.textContent = oldText;
            btn.disabled = false;
        }
    }

    function populateMembershipData(data) {
        membershipDataCache = data;
        const user = data.user || {};
        const member = data.member || {};
        
        // Compute initials matching mobile app logic
        const f = (member.firstname || user.name || '').trim();
        const l = (member.lastname || '').trim();
        let initials = 'VP';
        if (f && l) {
            initials = ((f[0] || '') + (l[0] || '')).toUpperCase();
        } else if (f) {
            initials = f.substring(0, 2).toUpperCase();
        }

        const avatarImg = document.getElementById('membership-photo');
        const avatarInitials = document.getElementById('membership-avatar-initials');

        const hasCustomPhoto = data.profileImage && 
            !data.profileImage.includes('default-profile.png') && 
            !data.profileImage.includes('valhalla-mobile-logo.png');

        if (hasCustomPhoto) {
            avatarImg.src = data.profileImage + (data.profileImage.includes('?') ? '&' : '?') + 't=' + Date.now();
            avatarImg.style.display = 'block';
            avatarInitials.style.display = 'none';
        } else {
            avatarImg.style.display = 'none';
            avatarInitials.textContent = initials || 'TC';
            avatarInitials.style.display = 'block';
        }

        document.getElementById('mem-username').textContent = user.name || '-';
        const fullName = ((member.firstname || '') + ' ' + (member.lastname || '')).trim();
        document.getElementById('mem-name').textContent = fullName || user.name || '-';
        document.getElementById('mem-email').textContent = user.email || '-';
        document.getElementById('mem-gender').textContent = member.gender || '-';
        document.getElementById('mem-age').textContent = member.age ? member.age + ' yrs' : '-';
        document.getElementById('mem-plan').textContent = member.subscription_level || member.subscription || 'Unlimited';
        document.getElementById('mem-active').textContent = (member.is_subsactive === true || member.is_subsactive === 1 || member.is_subsactive === '1') ? 'Yes' : (member.is_subsactive === false || member.is_subsactive === 0 || member.is_subsactive === '0' ? 'No' : 'Yes');
        document.getElementById('mem-startdate').textContent = member.startdate || member.start_date || '-';
        document.getElementById('mem-goal').textContent = member.primary_goal || '-';
        document.getElementById('mem-hw').textContent = (member.height ? member.height + ' cm' : '-') + ' / ' + (member.weight ? member.weight + ' kg' : '-');
        document.getElementById('mem-bmi').textContent = member.bmr || '-';
        document.getElementById('mem-phone').textContent = member.phone || '-';
        document.getElementById('mem-address').textContent = member.address || '-';
    }

    function openMembershipModal() {
        openModal('membership-modal');
        if (isMembershipEditing) toggleEditMembership(); // ensure we are not in edit mode
        
        // Fetch profile data
        fetch('/mobile/data/profile', {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                populateMembershipData(data);
            }
        })
        .catch(err => console.error('Error fetching membership data:', err));
    }
</script>
@endpush
@endsection

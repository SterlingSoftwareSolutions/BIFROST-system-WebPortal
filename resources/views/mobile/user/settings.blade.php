@extends('mobile.layout.mobile-layout')

@push('head-styles')
<style>
    .mem-input {
        background: transparent;
        border: none;
        border-bottom: 1px solid #52525b;
        color: #fff;
        font-size: 15px;
        font-weight: 600;
        width: 150px;
        text-align: right;
        outline: none;
    }
    .mem-input:focus { border-bottom-color: #10b981; }
    .mem-input-half { width: 50px; text-align: center; }
    .settings-bg {
        min-height: calc(100vh - 120px);
        background-image: url('{{ asset('img/valhalla-bg.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        padding: 16px;
        padding-top: 24px;
    }
    .version-banner {
        background: #3f3f46;
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .version-title {
        color: #fff;
        font-size: 14px;
        font-weight: 800;
        letter-spacing: 0.5px;
    }
    .version-text {
        color: #a1a1aa;
        font-size: 14px;
        font-weight: 500;
    }
    .settings-card {
        background: #09090b; /* Very dark background for cards */
        border: 1px solid #27272a;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .settings-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 20px;
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
        gap: 16px;
    }
    .settings-item-icon {
        color: #a1a1aa;
        font-size: 18px;
        width: 24px;
        text-align: center;
    }
    .settings-item-label {
        color: #fff;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.2px;
    }
    .settings-item-arrow {
        color: #a1a1aa;
        font-size: 18px;
        font-weight: bold;
    }

    /* Toggle Switch CSS */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 28px;
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
        height: 24px;
        width: 24px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    input:checked + .toggle-slider {
        background-color: #22c55e;
    }
    input:checked + .toggle-slider:before {
        transform: translateX(22px);
    }

    /* Modals CSS */
    .settings-back {
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .modal-content-card {
        background: #3f3f46;
        border: 1px solid #52525b;
        border-radius: 16px;
        padding: 20px;
    }
    .modal-title {
        color: #fff;
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 12px;
    }
    .modal-body {
        color: #e4e4e7;
        font-size: 15px;
        line-height: 1.6;
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
        <div style="background: #3f3f46; border-radius: 16px; display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 12px; color: #fff; font-size: 18px; font-weight: 800;">
                <i class="fa-regular fa-credit-card" style="font-size: 20px; color: #a1a1aa;"></i> Membership
            </div>
            <button id="mem-edit-btn" onclick="toggleEditMembership()" style="background: transparent; border: 1px solid #71717a; color: #e4e4e7; border-radius: 20px; padding: 6px 14px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; cursor: pointer;">
                <i id="mem-edit-icon" class="fa-regular fa-pen-to-square"></i> <span id="mem-edit-text">Edit</span>
            </button>
        </div>
        
        <!-- Info Box -->
        <div style="background: #18181b; border-radius: 16px; overflow: hidden; margin-bottom: 20px; border: 1px solid #27272a;">
            <div style="padding: 20px;">
                <!-- Profile Header -->
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                    <img id="membership-photo" src="{{ asset('images/valhalla-mobile-logo.png') }}" alt="Profile" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 1px solid #52525b; background: #27272a;">
                    <div>
                        <div style="color: #fff; font-size: 18px; font-weight: 800;">Profile Photo</div>
                        <div style="color: #a1a1aa; font-size: 13px; font-weight: 500;">Tap Edit to change photo</div>
                    </div>
                </div>

                <!-- Info Grid -->
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #a1a1aa; font-size: 15px;">Username</span>
                        <span id="mem-username" class="mem-val" style="color: #fff; font-size: 15px; font-weight: 700;">...</span>
                        <input type="text" id="edit-username" class="mem-input mem-edit-field" style="display:none;" />
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #a1a1aa; font-size: 15px;">Name</span>
                        <span id="mem-name" class="mem-val" style="color: #fff; font-size: 15px; font-weight: 700;">...</span>
                        <div id="edit-name-container" class="mem-edit-field" style="display:none; display:flex; gap:8px;">
                            <input type="text" id="edit-firstname" class="mem-input" style="width: 70px;" placeholder="First" />
                            <input type="text" id="edit-lastname" class="mem-input" style="width: 70px;" placeholder="Last" />
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #a1a1aa; font-size: 15px;">Email</span>
                        <span id="mem-email" style="color: #fff; font-size: 15px; font-weight: 700;">...</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #a1a1aa; font-size: 15px;">Gender</span>
                        <span id="mem-gender" style="color: #fff; font-size: 15px; font-weight: 700;">...</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #a1a1aa; font-size: 15px;">Age</span>
                        <span id="mem-age" style="color: #fff; font-size: 15px; font-weight: 700;">...</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #a1a1aa; font-size: 15px;">Plan</span>
                        <span id="mem-plan" class="mem-val" style="color: #fff; font-size: 15px; font-weight: 700;">...</span>
                        <select id="edit-plan" class="mem-input mem-edit-field" style="display:none; text-align: right;">
                            <option value="Unlimited">Unlimited</option>
                            <option value="Basic">Basic</option>
                            <option value="Pro">Pro</option>
                        </select>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #a1a1aa; font-size: 15px;">Active</span>
                        <span id="mem-active" style="color: #fff; font-size: 15px; font-weight: 700;">...</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #a1a1aa; font-size: 15px;">Start date</span>
                        <span id="mem-startdate" style="color: #fff; font-size: 15px; font-weight: 700;">...</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #a1a1aa; font-size: 15px;">Primary goal</span>
                        <span id="mem-goal" class="mem-val" style="color: #fff; font-size: 15px; font-weight: 700;">...</span>
                        <select id="edit-goal" class="mem-input mem-edit-field" style="display:none; text-align: right;">
                            <option value="Weight Loss">Weight Loss</option>
                            <option value="Muscle Gain">Muscle Gain</option>
                            <option value="General Fitness">General Fitness</option>
                        </select>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #a1a1aa; font-size: 15px;">Height / Weight</span>
                        <span id="mem-hw" class="mem-val" style="color: #fff; font-size: 15px; font-weight: 700;">...</span>
                        <div id="edit-hw-container" class="mem-edit-field" style="display:none; display:flex; align-items:center; gap:4px; color:#a1a1aa;">
                            <input type="number" id="edit-height" class="mem-input mem-input-half" /> cm /
                            <input type="number" id="edit-weight" class="mem-input mem-input-half" /> kg
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #a1a1aa; font-size: 15px;">BMI</span>
                        <span id="mem-bmi" style="color: #fff; font-size: 15px; font-weight: 700;">...</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #a1a1aa; font-size: 15px;">Phone</span>
                        <span id="mem-phone" class="mem-val" style="color: #fff; font-size: 15px; font-weight: 700;">...</span>
                        <input type="text" id="edit-phone" class="mem-input mem-edit-field" style="display:none;" />
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <span style="color: #a1a1aa; font-size: 15px;">Address</span>
                        <span id="mem-address" class="mem-val" style="color: #fff; font-size: 15px; font-weight: 700; text-align: right; max-width: 60%;">...</span>
                        <input type="text" id="edit-address" class="mem-input mem-edit-field" style="display:none;" />
                    </div>
                </div>
                
                <div id="mem-save-btn" class="mem-edit-field" style="display: none; margin-top: 30px;">
                    <button onclick="saveMembership()" style="width: 100%; background: #10b981; color: #000; border: none; border-radius: 24px; padding: 14px; font-size: 16px; font-weight: 800; cursor: pointer;">
                        Save Changes
                    </button>
                </div>
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
                • You're responsible for uploaded content.<br>
                • Service is provided "as is".<br>
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

    function toggleEditMembership() {
        isMembershipEditing = !isMembershipEditing;
        
        const vals = document.querySelectorAll('.mem-val');
        const edits = document.querySelectorAll('.mem-edit-field');
        
        if (isMembershipEditing) {
            document.getElementById('mem-edit-icon').className = 'fa-solid fa-xmark';
            document.getElementById('mem-edit-text').textContent = 'Cancel';
            
            vals.forEach(el => el.style.display = 'none');
            edits.forEach(el => el.style.display = el.id === 'edit-name-container' || el.id === 'edit-hw-container' ? 'flex' : 'block');
            
            // Populate
            document.getElementById('edit-username').value = membershipDataCache.user?.name || '';
            document.getElementById('edit-firstname').value = membershipDataCache.member?.firstname || '';
            document.getElementById('edit-lastname').value = membershipDataCache.member?.lastname || '';
            document.getElementById('edit-plan').value = membershipDataCache.member?.subscription || 'Unlimited';
            document.getElementById('edit-goal').value = membershipDataCache.member?.primary_goal || 'Weight Loss';
            document.getElementById('edit-height').value = membershipDataCache.member?.height || '';
            document.getElementById('edit-weight').value = membershipDataCache.member?.weight || '';
            document.getElementById('edit-phone').value = membershipDataCache.member?.phone || '';
            document.getElementById('edit-address').value = membershipDataCache.member?.address || '';
        } else {
            document.getElementById('mem-edit-icon').className = 'fa-regular fa-pen-to-square';
            document.getElementById('mem-edit-text').textContent = 'Edit';
            
            vals.forEach(el => el.style.display = 'block');
            edits.forEach(el => el.style.display = 'none');
        }
    }

    function saveMembership() {
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
        
        // Show loading state on button
        const btn = document.querySelector('#mem-save-btn button');
        const oldText = btn.textContent;
        btn.textContent = 'Saving...';
        btn.disabled = true;

        fetch('/mobile/data/update-profile', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                populateMembershipData(data);
                toggleEditMembership();
            } else {
                alert('Error saving data');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Failed to save data.');
        })
        .finally(() => {
            btn.textContent = oldText;
            btn.disabled = false;
        });
    }

    function populateMembershipData(data) {
        membershipDataCache = data;
        const user = data.user || {};
        const member = data.member || {};
        
        document.getElementById('membership-photo').src = data.profileImage || '{{ asset("images/valhalla-mobile-logo.png") }}';
        document.getElementById('mem-username').textContent = user.name || '-';
        document.getElementById('mem-name').textContent = (member.firstname || '') + ' ' + (member.lastname || '');
        document.getElementById('mem-email').textContent = user.email || '-';
        document.getElementById('mem-gender').textContent = member.gender || '-';
        document.getElementById('mem-age').textContent = member.age ? member.age + ' yrs' : '-';
        document.getElementById('mem-plan').textContent = member.subscription || 'Unlimited';
        document.getElementById('mem-active').textContent = 'Yes';
        document.getElementById('mem-startdate').textContent = member.start_date || '-';
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

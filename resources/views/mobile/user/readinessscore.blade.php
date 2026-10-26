@extends('mobile.layout.mobile-layout')

@push('head-styles')
<style>
    /* ===== READINESS SCORE — responsive mobile match of React Native readiness.tsx ===== */
    .rs-bg {
        min-height: calc(100vh - 120px);
        position: relative;
        padding: 10px 14px;
    }
    .rs-bg::before {
        content: '';
        position: fixed;
        top: 56px;
        left: 0;
        right: 0;
        bottom: 64px;
        background-image: url('{{ asset('img/valhalla-bg.jpg') }}');
        background-size: cover;
        background-position: center top;
        z-index: 0;
        pointer-events: none;
    }
    @media (max-width: 480px) {
        .rs-bg::before {
            top: 52px;
            bottom: 58px;
        }
    }

    .rs-header,
    .rs-row,
    .rs-score-box,
    .rs-actions {
        position: relative;
        z-index: 1;
    }

    /* Header */
    .rs-header {
        text-align: center;
        margin-bottom: 12px;
        margin-top: 4px;
    }
    .rs-title {
        color: #fff;
        font-size: 18px;
        font-weight: 900;
        letter-spacing: 1.2px;
        text-transform: uppercase;
    }
    .rs-date {
        color: #cfcfcf;
        font-size: 12px;
        font-weight: 600;
        margin-top: 3px;
        letter-spacing: 0.5px;
    }

    /* Row */
    .rs-row {
        width: 100%;
        max-width: 320px;
        margin: 0 auto 8px;
    }
    .rs-label {
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        text-align: center;
        margin-bottom: 3px;
    }

    /* Option row — scale buttons side by side */
    .rs-option-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
    }
    .rs-option-btn {
        flex: 1;
        height: 32px;
        border: none;
        color: #fff;
        font-weight: 600;
        font-size: 12px;
        cursor: pointer;
        transition: opacity 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .rs-option-btn:first-child  { border-radius: 5px 0 0 5px; }
    .rs-option-btn:last-of-type { border-radius: 0 5px 5px 0; }
    .rs-option-btn.selected {
        background: transparent !important;
        border: 1px solid #fff;
    }
    .rs-info-btn {
        background: none;
        border: none;
        color: #fff;
        font-size: 15px;
        cursor: pointer;
        padding: 0 4px;
        margin-left: 4px;
        flex-shrink: 0;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .rs-desc {
        color: #ccc;
        font-size: 11px;
        text-align: center;
        margin-top: 3px;
        min-height: 14px;
        line-height: 1.2;
    }

    /* Score box */
    .rs-score-box {
        border: 1px solid #fff;
        border-radius: 6px;
        padding: 8px 14px;
        text-align: center;
        width: 44%;
        max-width: 140px;
        margin: 10px auto;
    }
    .rs-score-label {
        color: #fff;
        font-size: 11px;
        letter-spacing: 0.8px;
    }
    .rs-score-value {
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        line-height: 1.2;
        margin-top: 2px;
    }

    /* Actions row */
    .rs-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin: 8px 0 16px;
    }
    .rs-save-btn {
        padding: 10px 24px;
        border: none;
        background: transparent;
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        letter-spacing: 0.8px;
        transition: opacity 0.2s;
    }
    .rs-save-btn:hover {
        opacity: 0.8;
    }
    .rs-save-btn.saved {
        color: #888;
        cursor: not-allowed;
    }
    .rs-nav-btn {
        width: 44px;
        height: 38px;
        border-radius: 6px;
        border: 1px solid #fff;
        background: rgba(255,255,255,0.06);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #fff;
        font-size: 18px;
        text-decoration: none;
    }
    .rs-nav-btn:hover {
        background: rgba(255,255,255,0.14);
        color: #fff;
    }

    /* Info Modal */
    .rs-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.4);
        z-index: 9999;
        align-items: flex-start;
        justify-content: center;
        padding-top: 70px;
    }
    .rs-modal-overlay.show { display: flex; }
    .rs-modal-box {
        background: #fff;
        border-radius: 10px;
        width: 80%;
        max-width: 340px;
        overflow: hidden;
    }
    .rs-modal-header {
        background: #000;
        color: #fff;
        padding: 10px 16px;
        font-weight: 700;
        font-size: 18px;
        text-align: center;
    }
    .rs-modal-body { padding: 16px; }
    .rs-modal-body p { font-size: 14px; margin: 0 0 8px; }
    .rs-modal-footer {
        background: #eee;
        padding: 10px;
        text-align: center;
    }
    .rs-modal-close {
        background: #6c757d;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 8px 16px;
        font-weight: 600;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="rs-bg">

    {{-- Header: Training Day + Date (from session) --}}
    <div class="rs-header">
        <div class="rs-title">TRAINING DAY</div>
        <div class="rs-date" id="rsDateDisplay">{{ $dayWithDate ?? '' }}</div>
    </div>

    {{-- Hrs of Sleep --}}
    <div class="rs-row" id="row-sleep">
        <div class="rs-label">Hrs of Sleep</div>
        <div class="rs-option-row">
            @foreach(['<5','5-6','6-7','7-8','8+'] as $i => $val)
            @php $colors = ['#F20525','#FF8F36','#FEE943','#89DD43','#1CBA4B']; @endphp
            <button type="button"
                class="rs-option-btn"
                style="background:{{ $colors[$i] }}"
                data-category="sleep"
                data-index="{{ $i }}"
                data-value="{{ $val }}"
                onclick="selectOption(this, 'sleep')">{{ $val }}</button>
            @endforeach
            <div class="rs-info-btn" style="opacity:0;pointer-events:none;"></div>
        </div>
        <div class="rs-desc" id="desc-sleep"></div>
    </div>

    {{-- Alertness --}}
    <div class="rs-row" id="row-alertness">
        <div class="rs-label">Alertness</div>
        <div class="rs-option-row">
            @foreach([1,2,3,4,5] as $i => $val)
            @php $colors = ['#F20525','#FF8F36','#FEE943','#89DD43','#1CBA4B']; @endphp
            <button type="button"
                class="rs-option-btn"
                style="background:{{ $colors[$i] }}"
                data-category="alertness"
                data-index="{{ $i }}"
                data-value="{{ $val }}"
                onclick="selectOption(this, 'alertness')">{{ $val }}</button>
            @endforeach
            <button type="button" class="rs-info-btn" onclick="openInfoModal('alertness')" aria-label="Info">
                <svg width="20" height="20" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:block;">
                    <path d="M248 64C146.39 64 64 146.39 64 248s82.39 184 184 184 184-82.39 184-184S349.61 64 248 64z" stroke="#ffffff" stroke-miterlimit="10" stroke-width="32" fill="none"/>
                    <path d="M220 220h32v116" stroke="#ffffff" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" fill="none"/>
                    <path d="M208 340h56" stroke="#ffffff" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" fill="none"/>
                    <circle cx="248" cy="156" r="20" fill="#ffffff"/>
                </svg>
            </button>
        </div>
        <div class="rs-desc" id="desc-alertness"></div>
    </div>

    {{-- Excitement --}}
    <div class="rs-row" id="row-excitement">
        <div class="rs-label">Excitement</div>
        <div class="rs-option-row">
            @foreach([1,2,3,4,5] as $i => $val)
            @php $colors = ['#F20525','#FF8F36','#FEE943','#89DD43','#1CBA4B']; @endphp
            <button type="button"
                class="rs-option-btn"
                style="background:{{ $colors[$i] }}"
                data-category="excitement"
                data-index="{{ $i }}"
                data-value="{{ $val }}"
                onclick="selectOption(this, 'excitement')">{{ $val }}</button>
            @endforeach
            <button type="button" class="rs-info-btn" onclick="openInfoModal('excitement')" aria-label="Info">
                <svg width="20" height="20" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:block;">
                    <path d="M248 64C146.39 64 64 146.39 64 248s82.39 184 184 184 184-82.39 184-184S349.61 64 248 64z" stroke="#ffffff" stroke-miterlimit="10" stroke-width="32" fill="none"/>
                    <path d="M220 220h32v116" stroke="#ffffff" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" fill="none"/>
                    <path d="M208 340h56" stroke="#ffffff" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" fill="none"/>
                    <circle cx="248" cy="156" r="20" fill="#ffffff"/>
                </svg>
            </button>
        </div>
        <div class="rs-desc" id="desc-excitement"></div>
    </div>

    {{-- Stress --}}
    <div class="rs-row" id="row-stress">
        <div class="rs-label">Stress</div>
        <div class="rs-option-row">
            @foreach([1,2,3,4,5] as $i => $val)
            @php $colors = ['#F20525','#FF8F36','#FEE943','#89DD43','#1CBA4B']; @endphp
            <button type="button"
                class="rs-option-btn"
                style="background:{{ $colors[$i] }}"
                data-category="stress"
                data-index="{{ $i }}"
                data-value="{{ $val }}"
                onclick="selectOption(this, 'stress')">{{ $val }}</button>
            @endforeach
            <button type="button" class="rs-info-btn" onclick="openInfoModal('stress')" aria-label="Info">
                <svg width="20" height="20" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:block;">
                    <path d="M248 64C146.39 64 64 146.39 64 248s82.39 184 184 184 184-82.39 184-184S349.61 64 248 64z" stroke="#ffffff" stroke-miterlimit="10" stroke-width="32" fill="none"/>
                    <path d="M220 220h32v116" stroke="#ffffff" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" fill="none"/>
                    <path d="M208 340h56" stroke="#ffffff" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" fill="none"/>
                    <circle cx="248" cy="156" r="20" fill="#ffffff"/>
                </svg>
            </button>
        </div>
        <div class="rs-desc" id="desc-stress"></div>
    </div>

    {{-- Soreness --}}
    <div class="rs-row" id="row-soreness">
        <div class="rs-label">Soreness</div>
        <div class="rs-option-row">
            @foreach([1,2,3,4,5] as $i => $val)
            @php $colors = ['#F20525','#FF8F36','#FEE943','#89DD43','#1CBA4B']; @endphp
            <button type="button"
                class="rs-option-btn"
                style="background:{{ $colors[$i] }}"
                data-category="soreness"
                data-index="{{ $i }}"
                data-value="{{ $val }}"
                onclick="selectOption(this, 'soreness')">{{ $val }}</button>
            @endforeach
            <button type="button" class="rs-info-btn" onclick="openInfoModal('soreness')" aria-label="Info">
                <svg width="20" height="20" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:block;">
                    <path d="M248 64C146.39 64 64 146.39 64 248s82.39 184 184 184 184-82.39 184-184S349.61 64 248 64z" stroke="#ffffff" stroke-miterlimit="10" stroke-width="32" fill="none"/>
                    <path d="M220 220h32v116" stroke="#ffffff" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" fill="none"/>
                    <path d="M208 340h56" stroke="#ffffff" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" fill="none"/>
                    <circle cx="248" cy="156" r="20" fill="#ffffff"/>
                </svg>
            </button>
        </div>
        <div class="rs-desc" id="desc-soreness"></div>
    </div>

    {{-- Score Box --}}
    <div class="rs-score-box">
        <div class="rs-score-label">SCORE</div>
        <div class="rs-score-value" id="scoreDisplay">0%</div>
    </div>

    {{-- Actions: SAVE + arrow to workout --}}
    <div class="rs-actions">
        <button id="saveBtn" class="rs-save-btn" onclick="handleSave()">SAVE</button>
        <a id="navBtn" href="{{ route('mobile.workout', ['class_id' => $classId]) }}" class="rs-nav-btn" style="display:none;">
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>

</div>

{{-- Info Modal --}}
<div class="rs-modal-overlay" id="infoModal">
    <div class="rs-modal-box">
        <div class="rs-modal-header" id="infoModalTitle"></div>
        <div class="rs-modal-body" id="infoModalBody"></div>
        <div class="rs-modal-footer">
            <button class="rs-modal-close" onclick="closeInfoModal()">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const _urlParams = new URLSearchParams(window.location.search);
window._classId = {{ $classId ?? 'null' }} || (_urlParams.get('class_id') ? parseInt(_urlParams.get('class_id')) : null);

// ---- State ----
const state = { sleep: null, alertness: null, excitement: null, stress: null, soreness: null };
let hasScore = false;

// ---- Descriptions matching mobile app readiness.tsx exactly ----
const descriptions = {
    sleep:     ['<5 = Sleep deprived.', '5–6 = Not enough rest.', '6–7 = Somewhat rested.', '7–8 = Well rested.', '8+ = Fully recharged.'],
    alertness: ['1 = Lights are on but no one is home.', '2 = Attention span of a toddler.', '3 = Can complete a basic sudoku puzzle.', '4 = Ready to tackle the day.', '5 = Firing on all cylinders.'],
    excitement:['1 = Not interested in weights today.', '2 = I\'ll do it because it\'s good for me.', '3 = Not pumped but not upset either.', '4 = I\'m keen, let\'s go!', '5 = Chomping at the bit all day!'],
    stress:    ['1 = Pulling my hair out.', '2 = Fairly stressed.', '3 = Feeling ok.', '4 = Surfer level stress.', '5 = Zen monk.'],
    soreness:  ['1 = Crippled.', '2 = Very sore.', '3 = Sore but ok to push.', '4 = Feeling good!', '5 = Like I never trained.'],
};

const infoTitles = {
    alertness:  'Alertness',
    excitement: 'Excitement',
    stress:     'Stress',
    soreness:   'Soreness',
};

function selectOption(btn, category) {
    const idx = parseInt(btn.dataset.index);
    const rowBtns = document.querySelectorAll(`[data-category="${category}"]`);
    rowBtns.forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    state[category] = idx;
    // update description
    const descEl = document.getElementById('desc-' + category);
    if (descEl && descriptions[category]) {
        descEl.textContent = descriptions[category][idx] || '';
    }
    recalcScore();
}

function recalcScore() {
    // Each index 0-4 maps to 4%, 8%, 12%, 16%, 20%
    const getPercent = (idx) => idx === null ? 0 : (idx + 1) * 4;
    const total = getPercent(state.sleep) + getPercent(state.alertness) +
                  getPercent(state.excitement) + getPercent(state.stress) +
                  getPercent(state.soreness);
    document.getElementById('scoreDisplay').textContent = total + '%';
    return total;
}

function openInfoModal(category) {
    const title = infoTitles[category] || category;
    const lines = descriptions[category] || [];
    document.getElementById('infoModalTitle').textContent = title;
    document.getElementById('infoModalBody').innerHTML = lines.map(l => `<p>• ${l}</p>`).join('');
    document.getElementById('infoModal').classList.add('show');
}

function closeInfoModal() {
    document.getElementById('infoModal').classList.remove('show');
}

async function handleSave() {
    // Validate all selected
    const categories = ['sleep','alertness','excitement','stress','soreness'];
    for (const cat of categories) {
        if (state[cat] === null) {
            alert('Please select a value for ' + cat.charAt(0).toUpperCase() + cat.slice(1));
            return;
        }
    }

    // Map sleep index (0-4) back to string value like the app does (index+1 as string for scale; for sleep use the label)
    const sleepLabels = ['<5','5-6','6-7','7-8','8+'];
    const score = recalcScore();

    const payload = {
        selected_day: document.getElementById('rsDateDisplay').textContent.trim(),
        sleep_input:      String(state.sleep + 1),
        alertness_input:  String(state.alertness + 1),
        excitement_input: String(state.excitement + 1),
        stress_input:     String(state.stress + 1),
        soreness_input:   String(state.soreness + 1),
        score: score,
        class_id: window._classId || null,
    };

    const saveBtn = document.getElementById('saveBtn');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.textContent = 'SAVING...';
    }

    try {
        const resp = await fetch('{{ route("mobile.storescore") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await resp.json();
        if (resp.ok || data.success) {
            hasScore = true;
            sessionStorage.setItem('show_saved_success', '1');
            const targetUrl = window._classId 
                ? ('/mobile/workout?class_id=' + window._classId + '&saved=1')
                : '/mobile/workout?saved=1';
            window.location.href = targetUrl;
        } else {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.textContent = 'SAVE';
            }
            alert(data.message || 'Could not save. Please try again.');
        }
    } catch(e) {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.textContent = 'SAVE';
        }
        alert('Error saving score. Please try again.');
    }
}

// ---- Load existing score on page init ----
document.addEventListener('DOMContentLoaded', async () => {
    const dayWithDate = document.getElementById('rsDateDisplay').textContent.trim();

    // Try to restore class_id from session via page data or URL query
    const urlParams = new URLSearchParams(window.location.search);
    const urlClassId = urlParams.get('class_id');
    window._classId = {{ $classId ?? 'null' }} || (urlClassId ? parseInt(urlClassId) : null);

    try {
        const resp = await fetch('/mobile/get-score', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ selected_day: dayWithDate, class_id: window._classId })
        });
        if (resp.ok) {
            const data = await resp.json();
            if (data.success && data.data) {
                const d = data.data;
                const preSelect = (cat, val) => {
                    if (!val) return;
                    let idx = -1;
                    
                    // Handle old formats (e.g. "alertness-4", "sleep-more-than-8")
                    if (typeof val === 'string' && val.includes('-')) {
                        if (cat === 'sleep') {
                            const map = {'sleep-less-than-5': 0, 'sleep-5-6': 1, 'sleep-6-7': 2, 'sleep-7-8': 3, 'sleep-more-than-8': 4};
                            idx = map[val] !== undefined ? map[val] : -1;
                        } else {
                            const parts = val.split('-');
                            idx = parseInt(parts[1]) - 1;
                        }
                    } else {
                        // Handle new format ("1", "2", "3", "4", "5")
                        idx = parseInt(val) - 1;
                    }

                    if (idx >= 0 && idx <= 4) {
                        const btn = document.querySelector(`[data-category="${cat}"][data-index="${idx}"]`);
                        if (btn) selectOption(btn, cat);
                    }
                };
                preSelect('sleep',      d.sleep_input);
                preSelect('alertness',  d.alertness_input);
                preSelect('excitement', d.excitement_input);
                preSelect('stress',     d.stress_input);
                preSelect('soreness',   d.soreness_input);
                // Mark as already saved
                hasScore = true;
                const saveBtn = document.getElementById('saveBtn');
                saveBtn.textContent = 'SAVED';
                saveBtn.classList.add('saved');
                saveBtn.disabled = true;
                const navBtn = document.getElementById('navBtn');
                if (window._classId) navBtn.href = '/mobile/workout?class_id=' + window._classId;
                navBtn.style.display = 'flex';
                // Update score display
                if (d.score !== undefined) {
                    document.getElementById('scoreDisplay').textContent = d.score + '%';
                }
            }
        }
    } catch(e) {
        // No existing score - that's fine
    }
});
</script>
@endpush

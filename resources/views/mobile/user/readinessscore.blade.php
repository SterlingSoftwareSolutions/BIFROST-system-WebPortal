@extends('mobile.layout.mobile-layout')

@push('head-styles')
<style>
    /* ===== READINESS SCORE — exact match of React Native readiness.tsx ===== */
    .rs-bg {
        min-height: calc(100vh - 120px);
        background-image: url('{{ asset('img/valhalla-bg.jpg') }}');
        background-size: cover;
        background-position: center;
        padding: 16px;
    }

    /* Header */
    .rs-header { text-align: center; margin-bottom: 28px; margin-top: 10px; }
    .rs-title  { color: #fff; font-size: 22px; font-weight: 900; letter-spacing: 1.5px; text-transform: uppercase; }
    .rs-date   { color: #cfcfcf; font-size: 14px; font-weight: 600; margin-top: 6px; letter-spacing: 0.8px; }

    /* Row */
    .rs-row { width: 100%; margin-bottom: 16px; }
    .rs-label { color: #fff; font-size: 15px; text-align: center; margin-bottom: 4px; }

    /* Option row — scale buttons side by side */
    .rs-option-row { display: flex; align-items: center; justify-content: center; gap: 0; }
    .rs-option-btn {
        flex: 1; height: 40px; border: none;
        color: #fff; font-weight: 600; font-size: 14px;
        cursor: pointer; transition: opacity 0.2s;
        display: flex; align-items: center; justify-content: center;
    }
    .rs-option-btn:first-child  { border-radius: 6px 0 0 6px; }
    .rs-option-btn:last-of-type { border-radius: 0 6px 6px 0; }
    .rs-option-btn.selected {
        background: transparent !important;
        border: 1px solid #fff;
    }
    .rs-info-btn {
        background: none; border: none; color: #fff;
        font-size: 18px; cursor: pointer; padding: 0 6px; margin-left: 6px;
        flex-shrink: 0;
    }
    .rs-desc { color: #ccc; font-size: 13px; text-align: center; margin-top: 8px; }

    /* Score box */
    .rs-score-box {
        border: 1px solid #fff; border-radius: 8px;
        padding: 20px; text-align: center;
        width: 50%; margin: 24px auto;
    }
    .rs-score-label { color: #fff; font-size: 14px; }
    .rs-score-value { color: #fff; font-size: 28px; font-weight: 700; }

    /* Actions row */
    .rs-actions { display: flex; align-items: center; justify-content: center; gap: 12px; margin: 12px 0 24px; }
    .rs-save-btn {
        padding: 14px 32px; border-radius: 8px;
        border: none; color: #fff; font-size: 16px; font-weight: 700;
        cursor: pointer; background: transparent;
        transition: opacity 0.2s;
    }
    .rs-save-btn.saved { background: rgba(100,100,100,0.6); opacity: 0.6; cursor: not-allowed; }
    .rs-nav-btn {
        width: 56px; height: 48px; border-radius: 8px;
        border: 1px solid #fff; background: rgba(255,255,255,0.06);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: #fff; font-size: 22px; text-decoration: none;
    }
    .rs-nav-btn:hover { background: rgba(255,255,255,0.14); color: #fff; }

    /* Info Modal */
    .rs-modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.4); z-index: 9999;
        align-items: flex-start; justify-content: center; padding-top: 70px;
    }
    .rs-modal-overlay.show { display: flex; }
    .rs-modal-box { background: #fff; border-radius: 10px; width: 80%; max-width: 340px; overflow: hidden; }
    .rs-modal-header {
        background: #000; color: #fff; padding: 10px 16px;
        font-weight: 700; font-size: 18px; text-align: center;
    }
    .rs-modal-body { padding: 16px; }
    .rs-modal-body p { font-size: 14px; margin: 0 0 8px; }
    .rs-modal-footer { background: #eee; padding: 10px; text-align: center; }
    .rs-modal-close {
        background: #6c757d; color: #fff; border: none; border-radius: 6px;
        padding: 8px 16px; font-weight: 600; cursor: pointer;
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
            <button type="button" class="rs-info-btn" style="opacity:0;pointer-events:none;">
                <i class="fas fa-info-circle"></i>
            </button>
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
            <button type="button" class="rs-info-btn" onclick="openInfoModal('alertness')">
                <i class="fas fa-info-circle"></i>
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
            <button type="button" class="rs-info-btn" onclick="openInfoModal('excitement')">
                <i class="fas fa-info-circle"></i>
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
            <button type="button" class="rs-info-btn" onclick="openInfoModal('stress')">
                <i class="fas fa-info-circle"></i>
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
            <button type="button" class="rs-info-btn" onclick="openInfoModal('soreness')">
                <i class="fas fa-info-circle"></i>
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
        <a id="navBtn" href="{{ route('mobile.workout') }}" class="rs-nav-btn" style="display:none;">
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

    try {
        const resp = await fetch('{{ route("mobile.storescore") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await resp.json();
        if (resp.ok || data.success) {
            hasScore = true;
            const saveBtn = document.getElementById('saveBtn');
            saveBtn.textContent = 'SAVED';
            saveBtn.classList.add('saved');
            saveBtn.disabled = true;
            document.getElementById('navBtn').style.display = 'flex';
        } else {
            alert(data.message || 'Could not save. Please try again.');
        }
    } catch(e) {
        alert('Error saving score. Please try again.');
    }
}

// ---- Load existing score on page init ----
document.addEventListener('DOMContentLoaded', async () => {
    const dayWithDate = document.getElementById('rsDateDisplay').textContent.trim();

    // Try to restore class_id from session via page data
    window._classId = null; // Will be set by server if needed

    try {
        const resp = await fetch('/mobile/get-score', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ selected_day: dayWithDate })
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
                document.getElementById('navBtn').style.display = 'flex';
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

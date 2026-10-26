@extends('mobile.layout.mobile-layout')

@push('head-styles')
<style>
    .training-bg {
        min-height: calc(100vh - 120px);
        position: relative;
        padding: 16px;
    }
    .training-bg::before {
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
        .training-bg::before {
            top: 52px;
            bottom: 58px;
        }
    }

    .week-toggle-wrap,
    .time-pills,
    .day-cards {
        position: relative;
        z-index: 1;
    }

    /* Week toggle matching mobile training.tsx */
    .week-toggle-wrap {
        display: flex;
        justify-content: flex-start;
        margin-bottom: 10px;
    }
    .week-toggle-wrap.right { justify-content: flex-end; }

    .week-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.08);
        border: 1px solid #ffffff;
        color: #ffffff;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.2s;
    }
    .week-toggle-btn:hover { background: rgba(255,255,255,0.16); }
    .week-toggle-btn .arrow {
        font-size: 18px;
        font-weight: bold;
        line-height: 1;
    }

    /* Class time pills matching classBtn in mobile training.tsx */
    .time-pills {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding: 0 0 14px;
        scrollbar-width: none;
    }
    .time-pills::-webkit-scrollbar { display: none; }
    .time-pill {
        flex-shrink: 0;
        border: 1px solid #000000;
        color: #ffffff;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        text-align: center;
        background: transparent;
        transition: border-color 0.2s;
        white-space: nowrap;
        min-width: 48px;
    }
    .time-pill:hover, .time-pill.active {
        border: 2px solid #ffffff;
        background: transparent;
    }
    .time-pill.placeholder {
        cursor: default;
        pointer-events: none;
        background: transparent;
    }

    /* Day cards matching card / selectedCard in mobile training.tsx */
    .day-cards {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .day-card {
        background: transparent;
        border: 1px solid #000000;
        border-radius: 8px;
        padding: 14px 16px;
        cursor: pointer;
        transition: border-color 0.2s;
        display: flex;
        align-items: stretch;
        gap: 0;
    }
    .day-card.selected {
        border: 2px solid #FFFFFF !important;
        background: transparent;
    }
    .day-card-body {
        flex: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-right: 12px;
    }
    .day-card-left {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .day-name {
        color: #ffffff;
        font-size: 14px;
        font-weight: bold;
        line-height: 1.2;
    }
    .day-spots {
        color: #ffffff;
        font-size: 12px;
        font-weight: 400;
        margin-top: 6px;
        width: 150px;
    }
    .day-no-class {
        color: #e57373;
        font-size: 12px;
        font-weight: 400;
        margin-top: 6px;
        width: 150px;
    }
    .day-card-right { 
        display: flex; 
        flex-direction: column; 
        align-items: flex-end; 
        justify-content: center;
    }
    .day-date {
        color: #cccccc;
        font-size: 12px;
        font-weight: 400;
        margin-bottom: 8px;
    }

    .reserve-btn {
        border: 1px solid #ffffff;
        border-radius: 6px;
        color: #ffffff;
        font-size: 12px;
        font-weight: bold;
        padding: 4px 10px;
        background: transparent;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }
    .reserve-btn:hover { background: #ffffff; color: #000000; }
    .reserve-btn.cancel { border-color: #f87171; color: #f87171; }
    .reserve-btn.cancel:hover { background: #f87171; color: #ffffff; }
    .reserve-btn:disabled { opacity: 0.4; cursor: not-allowed; }

    /* Arrow column matching arrowColumn in mobile training.tsx */
    .day-arrow {
        width: 36px;
        min-width: 36px;
        border-left: 1px solid #000000;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
        padding-left: 8px;
        transition: opacity 0.2s;
    }
    .day-arrow:hover { opacity: 0.7; }

    /* Loading state */
    .loading-state {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        padding: 40px;
        font-size: 14px;
        gap: 10px;
    }
</style>
@endpush

@section('content')
<div class="training-bg">

    {{-- Week Toggle --}}
    <div class="week-toggle-wrap" id="weekToggleWrap">
        <button class="week-toggle-btn" id="weekToggleBtn" onclick="toggleWeek()">
            <span class="arrow">‹</span>
            <span id="weekToggleLabel">Previous Week</span>
        </button>
    </div>

    {{-- Class time pills (Rendered dynamically) --}}
    <div class="time-pills" id="timePills">
        <!-- Dynamic content goes here -->
    </div>

    {{-- Day cards container --}}
    <div class="day-cards" id="dayCards">
        <div class="loading-state">
            <i class="fas fa-spinner fa-spin"></i> Loading schedule...
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let weekMode = 'current';     // 'current' | 'previous'
let selectedDayIndex = -1;    
let selectedClassIndex = 0;

let scheduleData = [];

// Matches mobile app isToday helper exactly
function isToday(dateString) {
    if (!dateString) return false;
    const today = new Date();
    const dd = String(today.getDate()).padStart(2, "0");
    const mm = String(today.getMonth() + 1).padStart(2, "0");
    const yyyy = String(today.getFullYear());

    const clean = String(dateString).trim();
    return clean === `${dd}/${mm}/${yyyy}` || clean === `${yyyy}-${mm}-${dd}`;
}

function toggleWeek() {
    weekMode = weekMode === 'current' ? 'previous' : 'current';

    const wrap = document.getElementById('weekToggleWrap');
    const label = document.getElementById('weekToggleLabel');
    const arrow = document.querySelector('#weekToggleBtn .arrow');

    if (weekMode === 'previous') {
        wrap.classList.add('right');
        label.textContent = 'Current Week';
        arrow.textContent = '›';
    } else {
        wrap.classList.remove('right');
        label.textContent = 'Previous Week';
        arrow.textContent = '‹';
    }

    selectedDayIndex = -1;
    selectedClassIndex = 0;
    loadSchedule();
}

async function loadSchedule() {
    document.getElementById('dayCards').innerHTML =
        '<div class="loading-state"><i class="fas fa-spinner fa-spin"></i> Loading schedule...</div>';
    document.getElementById('timePills').innerHTML = '';

    try {
        const resp = await fetch(`/get-class-slots?mode=${weekMode}`, {
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        const slots = await resp.json();
        scheduleData = slots;

        // Auto-select today using device local date (matching React Native mobile app)
        if (selectedDayIndex === -1 || selectedDayIndex >= slots.length) {
            let todayIdx = slots.findIndex(s => isToday(s.date));
            if (todayIdx === -1) {
                todayIdx = slots.findIndex(s => s.is_today);
            }
            selectedDayIndex = todayIdx >= 0 ? todayIdx : 0;
            selectedClassIndex = 0;
        }

        renderUI();
    } catch (e) {
        document.getElementById('dayCards').innerHTML =
            '<div class="loading-state" style="color:#f87171;">Failed to load schedule.</div>';
    }
}

function renderUI() {
    renderPills();
    renderCards();
}

function renderPills() {
    const container = document.getElementById('timePills');
    if (!scheduleData || scheduleData.length === 0 || selectedDayIndex < 0) {
        container.innerHTML = '<div class="time-pill placeholder">--.--</div>';
        return;
    }
    
    const currentDay = scheduleData[selectedDayIndex];
    if (!currentDay.classes || currentDay.classes.length === 0) {
        // When no class on selected day, show --.-- pill matching mobile app exactly
        container.innerHTML = '<div class="time-pill placeholder">--.--</div>';
        return;
    }

    container.innerHTML = currentDay.classes.map((cls, idx) => {
        const isActive = idx === selectedClassIndex;
        return `
            <button class="time-pill ${isActive ? 'active' : ''}" onclick="selectClassPill(${idx})">
                ${cls.time}<br>class
            </button>
        `;
    }).join('');
}

function selectClassPill(idx) {
    selectedClassIndex = idx;
    renderUI();
}

function renderCards() {
    const container = document.getElementById('dayCards');
    if (!scheduleData || scheduleData.length === 0) {
        container.innerHTML = '<div class="loading-state">No schedule found.</div>';
        return;
    }

    container.innerHTML = scheduleData.map((day, i) => {
        const isSelected = i === selectedDayIndex;
        // If it's the selected day, use the selectedClassIndex. Else use the first class (0).
        const classIdxToUse = isSelected ? selectedClassIndex : 0;
        const cls = day.classes ? day.classes[classIdxToUse] : null;
        const hasClass = !!cls;

        let spotsHtml = hasClass
            ? `<span class="day-spots">${cls.spots} spots available</span>`
            : `<span class="day-no-class">No class</span>`;

        let reserveHtml = '';
        if (hasClass) {
            if (cls.reserved) {
                reserveHtml = `<button class="reserve-btn cancel" onclick="handleCancel(event, ${cls.id}, ${i}, ${classIdxToUse})">✕ Cancel</button>`;
            } else if (cls.spots > 0) {
                reserveHtml = `<button class="reserve-btn" onclick="handleReserve(event, ${cls.id}, ${i}, ${classIdxToUse})">Reserve</button>`;
            } else {
                reserveHtml = `<button class="reserve-btn" disabled>Full</button>`;
            }
        } else {
            reserveHtml = `<span style="color:#fff;font-size:20px;line-height:1;margin-right:2px;">–</span>`;
        }

        return `
            <div class="day-card ${isSelected ? 'selected' : ''}" id="card-${i}" onclick="selectCard(${i})">
                <div class="day-card-body">
                    <div class="day-card-left">
                        <span class="day-name">${day.day_name}</span>
                        ${spotsHtml}
                    </div>
                    <div class="day-card-right">
                        <span class="day-date">${day.date}</span>
                        ${reserveHtml}
                    </div>
                </div>
                <div class="day-arrow" onclick="goToReadiness(event, ${i})">›</div>
            </div>
        `;
    }).join('');
}

function selectCard(idx) {
    selectedDayIndex = idx;
    selectedClassIndex = 0; // reset class selection for new day
    renderUI();
}

async function handleReserve(e, classId, dayIdx, classIdx) {
    e.stopPropagation();
    if (!classId) return;
    try {
        const resp = await fetch('/reserve', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ class_id: classId })
        });
        const data = await resp.json();
        if (data.success) {
            scheduleData[dayIdx].classes[classIdx].reserved = true;
            scheduleData[dayIdx].classes[classIdx].spots = data.remainingSpots;
            renderCards();
        } else {
            alert(data.message || 'Could not reserve.');
        }
    } catch (e) { alert('Error reserving class.'); }
}

async function handleCancel(e, classId, dayIdx, classIdx) {
    e.stopPropagation();
    if (!classId) return;
    try {
        const resp = await fetch('/cancel', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ class_id: classId })
        });
        const data = await resp.json();
        if (data.success) {
            scheduleData[dayIdx].classes[classIdx].reserved = false;
            scheduleData[dayIdx].classes[classIdx].spots = data.remainingSpots;
            renderCards();
        } else {
            alert(data.message || 'Could not cancel.');
        }
    } catch (e) { alert('Error cancelling class.'); }
}

async function goToReadiness(e, dayIdx) {
    e.stopPropagation();
    const slot = scheduleData[dayIdx];
    const classIdx = dayIdx === selectedDayIndex ? selectedClassIndex : 0;
    const cls = slot && slot.classes ? slot.classes[classIdx] : null;

    if (!cls) {
        alert('No class available for this day.');
        return;
    }
    if (dayIdx !== selectedDayIndex) {
        selectCard(dayIdx);
    }
    // Store selected day via AJAX then redirect
    try {
        await fetch('/select-day', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ day: slot.date, class_id: cls.id })
        });
    } catch(e) {}
    window.location.href = '/mobile/readinessscore?class_id=' + cls.id;
}

// Init on page load
document.addEventListener('DOMContentLoaded', () => loadSchedule());

// Auto-refresh schedule when user switches back to this tab
window.addEventListener('focus', () => loadSchedule());
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        loadSchedule();
    }
});
</script>
@endpush

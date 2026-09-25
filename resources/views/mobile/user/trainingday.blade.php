@extends('mobile.layout.mobile-layout')

@push('head-styles')
<style>
    .training-bg {
        min-height: calc(100vh - 120px);
        background-image: url('{{ asset('img/valhalla-bg.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        padding: 16px;
    }

    /* Week toggle */
    .week-toggle-wrap {
        display: flex;
        justify-content: flex-start;
        margin-bottom: 12px;
    }
    .week-toggle-wrap.right { justify-content: flex-end; }

    .week-toggle-btn {
        display: flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.4);
        color: #fff; border-radius: 8px;
        padding: 6px 14px; font-size: 12px; font-weight: 700;
        cursor: pointer; letter-spacing: 0.5px;
        transition: background 0.2s;
    }
    .week-toggle-btn:hover { background: rgba(255,255,255,0.16); }
    .week-toggle-btn .arrow { font-size: 18px; font-weight: 900; }

    /* Class time pills */
    .time-pills {
        display: flex; gap: 8px;
        overflow-x: auto; padding: 0 0 12px;
        scrollbar-width: none;
    }
    .time-pills::-webkit-scrollbar { display: none; }
    .time-pill {
        flex-shrink: 0;
        border: 1px solid rgba(0,0,0,0.5);
        color: #fff; border-radius: 8px;
        padding: 8px 16px; font-size: 13px; font-weight: 600;
        cursor: pointer; text-align: center;
        background: rgba(0,0,0,0.3);
        transition: background 0.2s, border-color 0.2s;
        white-space: nowrap;
    }
    .time-pill:hover, .time-pill.active {
        background: rgba(255,255,255,0.15);
        border-color: #fff;
    }

    /* Day cards */
    .day-cards { display: flex; flex-direction: column; gap: 10px; }

    .day-card {
        background: rgba(0,0,0,0.45);
        border: 1px solid rgba(0,0,0,0.6);
        border-radius: 10px;
        padding: 14px 16px;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        display: flex; align-items: center; gap: 0;
    }
    .day-card.selected {
        border-color: #fff !important;
        border-width: 2px;
        background: rgba(255,255,255,0.06);
    }
    .day-card-body {
        flex: 1; display: flex; justify-content: space-between; align-items: center;
    }
    .day-card-left { display: flex; flex-direction: column; gap: 4px; }
    .day-name { color: #fff; font-size: 15px; font-weight: 700; }
    .day-spots { color: #fff; font-size: 12px; margin-top: 4px; }
    .day-no-class { color: #f87171; font-size: 12px; margin-top: 4px; }
    .day-card-right { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }
    .day-date { color: #ccc; font-size: 12px; }

    .reserve-btn {
        border: 1px solid #fff; border-radius: 6px;
        color: #fff; font-size: 12px; font-weight: 700;
        padding: 4px 10px; background: transparent; cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }
    .reserve-btn:hover { background: #fff; color: #000; }
    .reserve-btn.cancel { border-color: #f87171; color: #f87171; }
    .reserve-btn.cancel:hover { background: #f87171; color: #fff; }
    .reserve-btn:disabled { opacity: 0.4; cursor: not-allowed; }

    /* Arrow to go to readiness */
    .day-arrow {
        width: 36px; min-width: 36px;
        border-left: 1px solid rgba(0,0,0,0.5);
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 22px; font-weight: 700;
        cursor: pointer; padding-left: 8px;
        transition: color 0.2s;
    }
    .day-arrow:hover { color: rgba(255,255,255,0.7); }

    /* Loading state */
    .loading-state {
        display: flex; align-items: center; justify-content: center;
        color: #fff; padding: 40px; font-size: 14px; gap: 10px;
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

        // Auto-select today
        if (selectedDayIndex === -1) {
            const todayIdx = slots.findIndex(s => s.is_today);
            selectedDayIndex = todayIdx >= 0 ? todayIdx : 0;
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
        container.innerHTML = '';
        return;
    }
    
    const currentDay = scheduleData[selectedDayIndex];
    if (!currentDay.classes || currentDay.classes.length === 0) {
        container.innerHTML = '';
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
            reserveHtml = `<span style="color:#6b7280;font-size:18px;">–</span>`;
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
    window.location.href = '/mobile/readinessscore';
}

// Init on page load
document.addEventListener('DOMContentLoaded', () => loadSchedule());
</script>
@endpush

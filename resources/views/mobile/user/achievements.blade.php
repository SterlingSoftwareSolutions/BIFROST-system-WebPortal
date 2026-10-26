@extends('mobile.layout.mobile-layout')

@push('head-styles')
<style>
    .achieve-bg {
        min-height: calc(100vh - 120px);
        background-image: linear-gradient(rgba(0,0,0,0.35), rgba(0,0,0,0.35)), url('{{ asset('img/valhalla-bg.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        padding: 12px;
        padding-bottom: 40px;
        box-sizing: border-box;
    }

    /* Exercise Selector Button */
    .exercise-search-btn {
        height: 38px;
        border-radius: 10px;
        border: 1px solid #222;
        padding: 0 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #0b0b0b;
        cursor: pointer;
        user-select: none;
    }
    .exercise-search-text {
        color: #fff;
        font-size: 12.5px;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .exercise-search-btn i {
        color: #9ca3af;
        font-size: 13px;
    }

    /* Range Selector Segment */
    .range-segment {
        margin-top: 10px;
        display: flex;
        gap: 8px;
    }
    .range-btn {
        flex: 1;
        height: 32px;
        border-radius: 8px;
        border: 1px solid #222;
        align-items: center;
        justify-content: center;
        display: flex;
        background: #0b0b0b;
        color: #9ca3af;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        outline: none;
    }
    .range-btn.active {
        border-color: rgba(34, 197, 94, 0.25);
        background: #122414;
        color: #22c55e;
    }

    /* Chart Card */
    .chart-card {
        margin-top: 10px;
        border-radius: 14px;
        border: 1px solid #222;
        background: #0b0b0b;
        padding: 10px;
        min-height: 240px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .chart-empty {
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 12px;
        text-align: center;
    }
    .chart-legend {
        display: flex;
        justify-content: center;
        gap: 16px;
        margin-top: 10px;
        align-items: center;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #9ca3af;
        font-size: 11px;
    }
    .legend-dot-1rm {
        width: 8px;
        height: 8px;
        background: #22c55e;
        border-radius: 50%;
    }
    .legend-rect-workout {
        width: 8px;
        height: 8px;
        background: #3b82f6;
        border-radius: 2px;
    }

    /* Summary Stat Tiles (2x2 Grid) */
    .stat-tiles {
        margin-top: 10px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .stat-tile {
        height: 98px;
        border-radius: 12px;
        border: 1px solid #222;
        background: #0b0b0b;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0 8px;
        box-sizing: border-box;
    }
    .stat-tile-icon {
        color: #9ca3af;
        font-size: 16px;
    }
    .stat-tile-val {
        color: #fff;
        font-size: 12.5px;
        font-weight: 700;
        text-align: center;
        margin-top: 5px;
        letter-spacing: 0.2px;
    }
    .stat-tile-lbl {
        color: #9ca3af;
        font-size: 10px;
        text-align: center;
        margin-top: 2px;
    }

    /* Exercise Picker Bottom Sheet Modal */
    .picker-modal {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 1000;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }
    .picker-backdrop {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.75);
    }
    .picker-card {
        position: relative;
        z-index: 1001;
        width: 100%;
        max-width: 480px;
        background: #0b0b0b;
        border: 1px solid #222;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        padding: 14px;
        box-sizing: border-box;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
    }
    .picker-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .picker-title {
        color: #fff;
        font-size: 13.5px;
        font-weight: 700;
    }
    .picker-close {
        background: none;
        border: none;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        padding: 4px;
    }
    .picker-search-box {
        height: 38px;
        border-radius: 10px;
        border: 1px solid #222;
        background: #121212;
        padding: 0 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
    .picker-search-box input {
        background: transparent;
        border: none;
        outline: none;
        color: #fff;
        font-size: 12.5px;
        width: 100%;
    }
    .picker-list {
        max-height: 380px;
        overflow-y: auto;
        scrollbar-width: none;
    }
    .picker-list::-webkit-scrollbar { display: none; }
    .picker-item {
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 8px;
        cursor: pointer;
        border-bottom: 1px solid #1a1a1a;
    }
    .picker-item-name {
        color: #fff;
        font-size: 12.5px;
    }
    .picker-item.active .picker-item-name {
        color: #22c55e;
        font-weight: 600;
    }
    .picker-item.active i {
        color: #22c55e;
        font-size: 13px;
    }
</style>
@endpush

@section('content')
<div class="achieve-bg">
    {{-- Exercise Picker Button --}}
    <div class="exercise-search-btn" onclick="openExercisePicker()">
        <span class="exercise-search-text" id="selectedExerciseName">Select exercise</span>
        <i class="fa-solid fa-chevron-down"></i>
    </div>

    {{-- Range Selector --}}
    <div class="range-segment">
        <button class="range-btn" data-range="WEEK" onclick="setRange('WEEK', this)">WEEK</button>
        <button class="range-btn" data-range="MONTH" onclick="setRange('MONTH', this)">MONTH</button>
        <button class="range-btn active" data-range="YEAR" onclick="setRange('YEAR', this)">YEAR</button>
    </div>

    {{-- Chart Card --}}
    <div class="chart-card">
        <div class="chart-empty" id="chartEmpty">Not enough data to plot.</div>
        <svg id="comboChart" style="display: none; width: 100%;"></svg>
        <div class="chart-legend" id="chartLegend" style="display: none;">
            <div class="legend-item"><div class="legend-dot-1rm"></div> 1RM</div>
            <div class="legend-item"><div class="legend-rect-workout"></div> Workouts</div>
        </div>
    </div>

    {{-- 4 Stat Tiles (2x2 Grid) --}}
    <div class="stat-tiles">
        <div class="stat-tile">
            <i class="fa-solid fa-trophy stat-tile-icon"></i>
            <div class="stat-tile-val" id="tileORM">0.0 kg</div>
            <div class="stat-tile-lbl">1 Rep Max</div>
        </div>
        <div class="stat-tile">
            <i class="fa-solid fa-dumbbell stat-tile-icon"></i>
            <div class="stat-tile-val" id="tileWeight">0 kg</div>
            <div class="stat-tile-lbl">Lifetime Weight</div>
        </div>
        <div class="stat-tile">
            <i class="fa-solid fa-layer-group stat-tile-icon"></i>
            <div class="stat-tile-val" id="tileSets">0</div>
            <div class="stat-tile-lbl">Lifetime Sets</div>
        </div>
        <div class="stat-tile">
            <i class="fa-solid fa-repeat stat-tile-icon"></i>
            <div class="stat-tile-val" id="tileReps">0</div>
            <div class="stat-tile-lbl">Lifetime Reps</div>
        </div>
    </div>
</div>

{{-- Exercise Search Picker Modal --}}
<div id="exercisePickerModal" class="picker-modal" style="display: none;">
    <div class="picker-backdrop" onclick="closeExercisePicker()"></div>
    <div class="picker-card">
        <div class="picker-header">
            <span class="picker-title">Select Exercise</span>
            <button class="picker-close" onclick="closeExercisePicker()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="picker-search-box">
            <i class="fa-solid fa-magnifying-glass" style="color: #9ca3af; font-size: 14px;"></i>
            <input type="text" id="exerciseSearchInput" placeholder="Search exercise" oninput="filterExerciseList(this.value)" autocomplete="off">
        </div>
        <div class="picker-list" id="pickerExerciseList"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentRange = 'YEAR';
let allExercises = [];
let selectedExercise = null;
let allPoints = [];
let all1RM = [];
let chartSummary = { total_weight: 0, total_sets: 0, total_reps: 0, one_rep_max: 0 };

function parseApiDate(dateStr) {
    if (!dateStr) return new Date();
    if (/^\d{4}-\d{2}-\d{2}/.test(dateStr)) {
        return new Date(dateStr);
    }
    const datePart = dateStr.split(' ')[0];
    const parts = datePart.split('/');
    if (parts.length === 3) {
        const [day, month, year] = parts.map(Number);
        return new Date(year, month - 1, day);
    }
    return new Date(dateStr);
}

function filterByRange(points, range) {
    const now = new Date();
    let start = new Date(now);
    if (range === 'WEEK') start.setDate(now.getDate() - 7);
    if (range === 'MONTH') start.setMonth(now.getMonth() - 1);
    if (range === 'YEAR') start = new Date(now.getFullYear(), 0, 1);

    return points.filter(p => {
        const d = new Date(p.date);
        return d >= start && d <= now;
    });
}

async function loadExercises() {
    try {
        const resp = await fetch('/mobile/data/exercises', { headers: { 'Accept': 'application/json' } });
        const res = await resp.json();
        if (res.status === 'success' && res.data) {
            allExercises = (res.data || []).map(x => ({
                id: x.id,
                name: x.workout,
                type: x.type
            }));
            renderPickerList(allExercises);
            // Default: do not auto select exercise, display empty state as in Image 2
        }
    } catch (e) {
        console.error('Error loading exercises:', e);
    }
}

function openExercisePicker() {
    document.getElementById('exercisePickerModal').style.display = 'flex';
    document.getElementById('exerciseSearchInput').value = '';
    renderPickerList(allExercises);
    document.getElementById('exerciseSearchInput').focus();
}

function closeExercisePicker() {
    document.getElementById('exercisePickerModal').style.display = 'none';
}

function filterExerciseList(query) {
    const q = (query || '').toLowerCase().trim();
    const filtered = allExercises.filter(e => e.name.toLowerCase().includes(q));
    renderPickerList(filtered);
}

function renderPickerList(list) {
    const container = document.getElementById('pickerExerciseList');
    if (!list.length) {
        container.innerHTML = '<div style="color:#9ca3af; text-align:center; padding: 24px 0; font-size:13px;">No exercises found</div>';
        return;
    }
    container.innerHTML = list.map(item => `
        <div class="picker-item ${selectedExercise && selectedExercise.id === item.id ? 'active' : ''}" onclick="selectExerciseById(${item.id})">
            <span class="picker-item-name">${item.name}</span>
            ${selectedExercise && selectedExercise.id === item.id ? '<i class="fa-solid fa-check"></i>' : ''}
        </div>
    `).join('');
}

function selectExerciseById(id) {
    const found = allExercises.find(e => e.id === id);
    if (found) {
        selectExercise(found);
    }
    closeExercisePicker();
}

function oneRepMaxEpley(w, r) {
    return Number((w * (1 + r / 30)).toFixed(1));
}

function aggregateLifetime(points, default1RM = 0) {
    let lifetimeWeight = 0;
    let lifetimeSets = 0;
    let lifetimeReps = 0;
    let best1RM = Number(default1RM || 0);

    for (const p of points) {
        const sets = p.setsCompleted ?? 1;
        const reps = Number(p.reps ?? 0);
        const weight = Number(p.weight ?? 0);

        lifetimeSets += sets;
        lifetimeReps += reps * sets;
        lifetimeWeight += weight * reps * sets;

        const est = p.topSet1RM ?? oneRepMaxEpley(weight, reps);
        if (est > best1RM) best1RM = est;
    }

    return { lifetimeWeight, lifetimeSets, lifetimeReps, best1RM };
}

async function selectExercise(exercise) {
    selectedExercise = exercise;
    document.getElementById('selectedExerciseName').textContent = exercise.name;
    renderPickerList(allExercises);

    // Show loading state
    document.getElementById('chartEmpty').textContent = 'Loading graph...';
    document.getElementById('chartEmpty').style.display = 'flex';
    document.getElementById('comboChart').style.display = 'none';
    document.getElementById('chartLegend').style.display = 'none';

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const resp = await fetch('/mobile/data/achievement-graph', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ workout_id: exercise.id })
        });
        const res = await resp.json();

        if (res.status === 'success' && res.data) {
            const apiData = res.data;
            const graph = apiData.graph;
            const summary = apiData.summary || {};

            allPoints = [];
            if (graph && graph.labels && graph.datasets && graph.datasets.length) {
                const repsDs = graph.datasets.find(d => (d.label || '').toLowerCase().includes('rep'));
                const weightsDs = graph.datasets.find(d => (d.label || '').toLowerCase().includes('weight'));

                for (let i = 0; i < graph.labels.length; i++) {
                    const rawDate = graph.labels[i];
                    const parsed = parseApiDate(rawDate);
                    const date = parsed.toISOString().split('T')[0];
                    const weight = Number(weightsDs?.data?.[i] ?? 0);
                    const reps = Number(repsDs?.data?.[i] ?? 0);
                    const sets = 1;
                    const oneRM = summary?.one_rep_max ?? oneRepMaxEpley(weight, reps);
                    allPoints.push({ date, weight, reps, setsCompleted: sets, topSet1RM: oneRM });
                }
            }

            const rawRm = apiData['1rm_history'] || [];
            all1RM = rawRm.map(item => ({
                date: new Date(item.date).toISOString().split('T')[0],
                weight: Number(item.weight || 0)
            }));

            // Exact calculation matching React Native mobile app's aggregateLifetime
            const lifetime = aggregateLifetime(allPoints, summary?.one_rep_max ?? 0);

            if (all1RM.length) {
                for (const rm of all1RM) {
                    if (rm.weight > lifetime.best1RM) lifetime.best1RM = rm.weight;
                }
            }

            chartSummary = {
                total_weight: lifetime.lifetimeWeight,
                total_sets: lifetime.lifetimeSets,
                total_reps: lifetime.lifetimeReps,
                one_rep_max: lifetime.best1RM
            };

            updateStatTiles(chartSummary);
            renderComboChart();
        } else {
            resetChartData();
        }
    } catch (e) {
        console.error('Error fetching achievement graph:', e);
        resetChartData();
    }
}

function resetChartData() {
    allPoints = [];
    all1RM = [];
    chartSummary = { total_weight: 0, total_sets: 0, total_reps: 0, one_rep_max: 0 };
    updateStatTiles(chartSummary);
    document.getElementById('chartEmpty').textContent = 'Not enough data to plot.';
    document.getElementById('chartEmpty').style.display = 'flex';
    document.getElementById('comboChart').style.display = 'none';
    document.getElementById('chartLegend').style.display = 'none';
}

function setRange(range, btn) {
    currentRange = range;
    document.querySelectorAll('.range-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderComboChart();
}

function updateStatTiles(summary) {
    const s = summary || {};
    const bestORM = Number(parseFloat(s.one_rep_max || 0).toFixed(1));
    const totalW = Math.round(s.total_weight || 0);
    const totalS = Math.round(s.total_sets || 0);
    const totalR = Math.round(s.total_reps || 0);

    document.getElementById('tileORM').textContent = bestORM.toFixed(1) + ' kg';
    document.getElementById('tileWeight').textContent = totalW + ' kg';
    document.getElementById('tileSets').textContent = totalS;
    document.getElementById('tileReps').textContent = totalR;
}

function renderComboChart() {
    const filteredPoints = filterByRange(allPoints, currentRange);
    const filteredRM = filterByRange(all1RM, currentRange);

    const empty = document.getElementById('chartEmpty');
    const svg = document.getElementById('comboChart');
    const legend = document.getElementById('chartLegend');

    if (!filteredPoints.length && !filteredRM.length) {
        empty.textContent = 'Not enough data to plot.';
        empty.style.display = 'flex';
        svg.style.display = 'none';
        legend.style.display = 'none';
        return;
    }

    empty.style.display = 'none';
    svg.style.display = 'block';
    legend.style.display = 'flex';

    const card = document.querySelector('.chart-card');
    const width = Math.max(300, card.clientWidth - 20);
    const height = 220;

    const PAD_L = 36, PAD_R = 16, PAD_T = 16, PAD_B = 24;

    const now = new Date();
    let maxDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
    let minDate = new Date(maxDate);
    if (currentRange === 'WEEK') minDate.setDate(minDate.getDate() - 6);
    if (currentRange === 'MONTH') minDate.setDate(minDate.getDate() - 27);
    if (currentRange === 'YEAR') {
        minDate = new Date(now.getFullYear(), 0, 1);
        maxDate = new Date(now.getFullYear(), 11, 31, 23, 59, 59);
    }

    const minX = minDate.getTime();
    const maxX = maxDate.getTime();
    const minY = 0;
    const maxY = 500;

    const plotW = width - PAD_L - PAD_R;
    const plotH = height - PAD_T - PAD_B;

    const scaleX = x => PAD_L + ((x - minX) / Math.max(1, maxX - minX)) * plotW;
    const scaleY = y => PAD_T + plotH - ((Math.min(Math.max(y, 0), maxY) - minY) / (maxY - minY)) * plotH;

    // Y Grid lines (0 to 500 in 100 step)
    const yTicks = [0, 100, 200, 300, 400, 500];
    let gridSvg = yTicks.map(val => {
        const y = scaleY(val);
        return `
            <line x1="${PAD_L}" x2="${PAD_L + plotW}" y1="${y}" y2="${y}" stroke="#222" stroke-width="1" />
            <text x="${PAD_L - 6}" y="${y + 4}" fill="#9ca3af" font-size="8.5" text-anchor="end">${val}</text>
        `;
    }).join('');

    // X Axis Labels
    let xLabelsSvg = '';
    if (currentRange === 'WEEK') {
        const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        for (let i = 0; i <= 6; i++) {
            const d = new Date(minDate.getTime() + i * 86400000);
            const x = scaleX(d.getTime());
            xLabelsSvg += `<text x="${x}" y="${height - 4}" fill="#9ca3af" font-size="8.5" text-anchor="middle">${days[d.getDay()]}</text>`;
        }
    } else if (currentRange === 'MONTH') {
        for (let i = 0; i <= 3; i++) {
            const d = new Date(minDate.getTime() + i * 7 * 86400000);
            const x = scaleX(d.getTime());
            xLabelsSvg += `<text x="${x}" y="${height - 4}" fill="#9ca3af" font-size="8.5" text-anchor="middle">W${i + 1}</text>`;
        }
    } else if (currentRange === 'YEAR') {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        for (let i = 0; i <= 11; i++) {
            const d = new Date(minDate.getFullYear(), minDate.getMonth() + i, 1);
            if (d.getTime() > maxX) break;
            const x = scaleX(d.getTime());
            xLabelsSvg += `<text x="${x}" y="${height - 4}" fill="#9ca3af" font-size="8.5" text-anchor="middle">${months[d.getMonth()]}</text>`;
        }
    }

    // Bar Graph for Workouts
    const barWidth = currentRange === 'WEEK' ? 14 : currentRange === 'MONTH' ? 8 : 4;
    const dailyWorkouts = new Map();
    filteredPoints.forEach(p => {
        const t = new Date(p.date).getTime();
        if (t >= minX && t <= maxX) {
            dailyWorkouts.set(p.date, Math.max(dailyWorkouts.get(p.date) || 0, p.weight));
        }
    });

    let barsSvg = Array.from(dailyWorkouts.entries()).map(([dateStr, yVal]) => {
        const x = scaleX(new Date(dateStr).getTime());
        const y = scaleY(yVal);
        const barH = scaleY(0) - y;
        return `<rect x="${x - barWidth / 2}" y="${y}" width="${barWidth}" height="${barH}" fill="#3b82f6" rx="2" />`;
    }).join('');

    // Line Graph for 1RM
    const validLineData = filteredRM.map(p => ({ x: new Date(p.date).getTime(), y: p.weight }))
        .filter(d => d.x >= minX && d.x <= maxX)
        .sort((a, b) => a.x - b.x);

    let linePath = '';
    let circlesSvg = '';
    validLineData.forEach((pt, i) => {
        const X = scaleX(pt.x);
        const Y = scaleY(pt.y);
        linePath += i === 0 ? `M ${X} ${Y}` : ` L ${X} ${Y}`;
        circlesSvg += `<circle cx="${X}" cy="${Y}" r="4" fill="#22c55e" />`;
    });

    let rmLineSvg = validLineData.length > 0 ? `
        <path d="${linePath}" stroke="#22c55e" stroke-width="2" fill="none" />
        ${circlesSvg}
    ` : '';

    svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
    svg.setAttribute('height', height);
    svg.innerHTML = `
        <g>${gridSvg}</g>
        <g>${xLabelsSvg}</g>
        <g>${barsSvg}</g>
        <g>${rmLineSvg}</g>
    `;
}

document.addEventListener('DOMContentLoaded', () => {
    loadExercises();
});
</script>
@endpush

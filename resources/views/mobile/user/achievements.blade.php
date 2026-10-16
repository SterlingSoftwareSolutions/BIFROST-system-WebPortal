@extends('mobile.layout.mobile-layout')

@push('head-styles')
<style>
    .achieve-bg {
        min-height: calc(100vh - 120px);
        background-image: url('{{ asset('img/valhalla-bg.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        padding: 16px;
    }

    .achieve-title {
        color: #000; font-size: 22px; font-weight: 800;
        letter-spacing: -0.3px; margin-bottom: 16px;
    }

    /* Exercise selector */
    .exercise-select-container {
        position: relative; margin-bottom: 24px;
    }
    .exercise-select {
        width: 100%; background: #111;
        border: 1px solid #333;
        border-radius: 8px; color: #fff;
        padding: 14px 16px; font-size: 15px; font-weight: 600;
        appearance: none; cursor: pointer;
        font-family: inherit;
    }
    .exercise-select option { background: #111; color: #fff; }
    .exercise-select-icon {
        position: absolute; right: 16px; top: 16px;
        color: #a1a1aa; pointer-events: none;
    }

    /* Range selector */
    .range-selector {
        display: flex; gap: 8px; margin-bottom: 24px;
    }
    .range-btn {
        flex: 1; padding: 12px 0; text-align: center;
        border: 1px solid #27272a;
        border-radius: 8px; background: #000;
        color: #a1a1aa; font-size: 13px; font-weight: 700;
        cursor: pointer; letter-spacing: 0.5px; transition: all 0.2s;
    }
    .range-btn.active {
        background: #153e21; /* dark green solid */
        color: #4ade80; /* bright green */
        border-color: #153e21;
    }

    /* Chart card */
    .chart-card {
        background: #09090b;
        border: 1px solid #27272a;
        border-radius: 12px; padding: 16px 16px 24px 16px;
        margin-bottom: 24px;
    }
    #chartContainer {
        width: 100%; overflow-x: auto; scrollbar-width: none;
    }
    #chartContainer::-webkit-scrollbar { display: none; }
    #lineChart { display: block; }
    
    .chart-legend {
        display: flex; justify-content: center; gap: 20px;
        margin-top: 16px; align-items: center;
    }
    .legend-item { display: flex; align-items: center; gap: 6px; color: #a1a1aa; font-size: 12px; font-weight: 600; }
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; }
    .legend-square { width: 10px; height: 10px; border-radius: 2px; }

    /* Lifetime stats */
    .stats-grid {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 12px; margin-bottom: 30px;
    }
    .stat-card {
        background: #09090b;
        border: 1px solid #27272a;
        border-radius: 12px; padding: 20px 12px;
        text-align: center;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
    }
    .stat-icon {
        color: #a1a1aa; font-size: 20px; margin-bottom: 12px;
    }
    .stat-value { color: #fff; font-size: 20px; font-weight: 800; }
    .stat-label {
        color: #a1a1aa; font-size: 12px;
        font-weight: 500; margin-top: 6px;
    }

    .empty-state {
        color: #a1a1aa; text-align: center;
        padding: 40px 0; font-size: 14px;
    }
    .loading-state {
        display: flex; align-items: center; justify-content: center;
        color: #a1a1aa; padding: 40px; gap: 10px; font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="achieve-bg">
    <h1 class="achieve-title">Achievements</h1>

    {{-- Exercise selector --}}
    <div class="exercise-select-container">
        <select class="exercise-select" id="exerciseSelect" onchange="loadGraph()">
            <option value="">Select an exercise...</option>
        </select>
        <i class="fa-solid fa-chevron-down exercise-select-icon"></i>
    </div>

    {{-- Range buttons --}}
    <div class="range-selector">
        <button class="range-btn active" data-range="WEEK" onclick="setRange('WEEK', this)">WEEK</button>
        <button class="range-btn" data-range="MONTH" onclick="setRange('MONTH', this)">MONTH</button>
        <button class="range-btn" data-range="YEAR" onclick="setRange('YEAR', this)">YEAR</button>
    </div>

    {{-- Chart --}}
    <div class="chart-card">
        <div id="chartContainer">
            <div class="empty-state" id="chartEmpty">Select an exercise to view progress.</div>
            <svg id="lineChart" style="display:none;"></svg>
        </div>
        <div class="chart-legend" id="chartLegend" style="display:none;">
            <div class="legend-item"><div class="legend-dot" style="background:#10b981;"></div> 1RM</div>
            <div class="legend-item"><div class="legend-square" style="background:#3b82f6;"></div> Workouts</div>
        </div>
    </div>

    {{-- Lifetime stats --}}
    <div class="stats-grid" id="statsGrid" style="display:none;">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-trophy"></i></div>
            <div class="stat-value" id="statORM">0 kg</div>
            <div class="stat-label">1 Rep Max</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-dumbbell"></i></div>
            <div class="stat-value" id="statWeight">0 kg</div>
            <div class="stat-label">Lifetime Weight</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-layer-group"></i></div>
            <div class="stat-value" id="statSets">0</div>
            <div class="stat-label">Lifetime Sets</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-arrows-rotate"></i></div>
            <div class="stat-value" id="statReps">0</div>
            <div class="stat-label">Lifetime Reps</div>
        </div>
    </div>

    <div class="loading-state" id="graphLoading" style="display:none;">
        <i class="fas fa-spinner fa-spin"></i> Loading graph...
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentRange = 'YEAR'; // Default to year to show more data
let allPoints = [];
let all1RM = [];
let exercises = [];
let achievementsSummary = {};

// --- Fetch exercises ---
async function loadExercises() {
    try {
        const resp = await fetch('/mobile/data/exercises', { headers: { 'Accept': 'application/json' } });
        const data = await resp.json();
        if (data.status === 'success' && data.data) {
            exercises = data.data;
            const sel = document.getElementById('exerciseSelect');
            sel.innerHTML = '<option value="">Select an exercise...</option>' +
                data.data.map(e => `<option value="${e.id}">${e.workout}</option>`).join('');
            
            // Set default range visually
            document.querySelectorAll('.range-btn').forEach(b => {
                b.classList.remove('active');
                if(b.dataset.range === currentRange) b.classList.add('active');
            });
        }
    } catch(e) {
        document.getElementById('exerciseSelect').innerHTML = '<option value="">Failed to load</option>';
    }
}

function setRange(range, btn) {
    currentRange = range;
    document.querySelectorAll('.range-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderChart(allPoints, all1RM);
}

async function loadGraph() {
    const workoutId = document.getElementById('exerciseSelect').value;
    if (!workoutId) {
        document.getElementById('chartEmpty').style.display = 'block';
        document.getElementById('lineChart').style.display = 'none';
        document.getElementById('statsGrid').style.display = 'none';
        document.getElementById('chartLegend').style.display = 'none';
        return;
    }

    document.getElementById('graphLoading').style.display = 'flex';
    document.getElementById('chartEmpty').style.display = 'none';
    document.getElementById('lineChart').style.display = 'none';
    document.getElementById('statsGrid').style.display = 'none';
    document.getElementById('chartLegend').style.display = 'none';

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const resp = await fetch('/mobile/data/achievement-graph', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ workout_id: workoutId })
        });
        const responseData = await resp.json();
        if (responseData.status === 'success' && responseData.data) {
            const d = responseData.data;
            
            // Parse graph data
            const labels = d.graph?.labels || [];
            const repsData = d.graph?.datasets?.find(ds => ds.label === 'Reps')?.data || [];
            const weightsData = d.graph?.datasets?.find(ds => ds.label === 'Weight (kg)')?.data || [];
            
            allPoints = labels.map((date, i) => ({
                date: date,
                weight: parseFloat(weightsData[i]) || 0,
                reps: parseInt(repsData[i]) || 0,
            }));
            
            all1RM = d['1rm_history'] || [];
            achievementsSummary = d.summary || {};
            
            renderChart(allPoints, all1RM);
            renderStats(achievementsSummary);
        } else {
            document.getElementById('chartEmpty').style.display = 'block';
            document.getElementById('chartEmpty').textContent = 'No data found for this exercise.';
        }
    } catch(e) {
        document.getElementById('chartEmpty').style.display = 'block';
        document.getElementById('chartEmpty').textContent = 'Error loading data.';
    } finally {
        document.getElementById('graphLoading').style.display = 'none';
    }
}

function filterByRange(points) {
    const now = new Date();
    return points.filter(p => {
        const d = new Date(p.date);
        if (currentRange === 'WEEK') {
            const weekAgo = new Date(now); weekAgo.setDate(now.getDate() - 7);
            return d >= weekAgo;
        } else if (currentRange === 'MONTH') {
            const monthAgo = new Date(now); monthAgo.setMonth(now.getMonth() - 1);
            return d >= monthAgo;
        } else {
            const yearAgo = new Date(now); yearAgo.setFullYear(now.getFullYear() - 1);
            return d >= yearAgo;
        }
    });
}

function renderChart(points, rmPoints) {
    const filteredPoints = filterByRange(points);
    const filteredRM = filterByRange(rmPoints);
    
    const svg = document.getElementById('lineChart');
    const empty = document.getElementById('chartEmpty');
    const legend = document.getElementById('chartLegend');

    if (!filteredPoints.length && !filteredRM.length) {
        svg.style.display = 'none';
        legend.style.display = 'none';
        empty.style.display = 'block';
        empty.textContent = 'No data in this time range.';
        return;
    }

    empty.style.display = 'none';
    svg.style.display = 'block';
    legend.style.display = 'flex';

    // Define fixed X-axis slots based on currentRange
    let xSlots = [];
    let getSlotIndex = (dateStr) => 0;
    const now = new Date();
    
    if (currentRange === 'WEEK') {
        xSlots = Array.from({length: 7}, (_, i) => {
            const d = new Date(now);
            d.setDate(d.getDate() - (6 - i));
            return d.toLocaleString('default', { weekday: 'short' });
        });
        getSlotIndex = (dateStr) => {
            const d = new Date(dateStr);
            const diffDays = Math.floor((now.getTime() - d.getTime()) / (1000 * 60 * 60 * 24));
            let i = 6 - diffDays;
            if (i < 0) i = 0;
            if (i > 6) i = 6;
            return i;
        };
    } else if (currentRange === 'MONTH') {
        xSlots = ['W 1', 'W 2', 'W 3', 'W 4'];
        getSlotIndex = (dateStr) => {
            const d = new Date(dateStr);
            const dateNum = d.getDate();
            if (dateNum <= 7) return 0;
            if (dateNum <= 14) return 1;
            if (dateNum <= 21) return 2;
            return 3;
        };
    } else { // YEAR
        xSlots = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        getSlotIndex = (dateStr) => {
            const d = new Date(dateStr);
            return d.getMonth();
        };
    }
    
    // We do NOT want a super wide scroll for fixed slots unless it's too cramped. 
    // Usually Mobile charts fit entirely inside the screen without scroll.
    const W = Math.max(320, document.getElementById('chartContainer').clientWidth);
    const H = 220;
    const padL = 40, padR = 20, padT = 20, padB = 40;
    const chartW = W - padL - padR;
    const chartH = H - padT - padB;

    // Get min and max weights
    const weights = [...filteredPoints.map(p => p.weight), ...filteredRM.map(p => p.weight)];
    const minW = 0; // Always start Y axis at 0
    let maxW = Math.max(...weights);
    if (maxW === 0) maxW = 100; // default scale
    else maxW = Math.ceil(maxW / 100) * 100; // round up to nearest 100

    const range = maxW - minW || 1;

    const toX = (slotIndex) => {
        return padL + (slotIndex / (xSlots.length - 1 || 1)) * chartW;
    };
    const toY = (w) => padT + chartH - ((w - minW) / range) * chartH;

    // Grid lines (every 100, up to maxW)
    let gridLines = '';
    // Let's ensure max 5-6 grid lines by dynamically adjusting step if maxW is too big
    let step = 100;
    if (maxW > 500) step = 200;
    if (maxW > 1000) step = 500;
    
    for(let w = 0; w <= maxW; w += step) {
        const y = toY(w);
        gridLines += `<line x1="${padL}" y1="${y}" x2="${W - padR}" y2="${y}" stroke="#27272a" stroke-width="1"/>`;
        gridLines += `<text x="${padL - 10}" y="${y + 4}" text-anchor="end" fill="#71717a" font-size="10">${w}</text>`;
    }

    // Workouts (Blue Bars) - Aggregated by slot or just mapped to slot?
    // Let's just map them. If multiple fall in the same slot, they overlap. In the app, they probably aggregate.
    // Let's aggregate max weight for Workouts and max 1RM per slot.
    const slotWorkouts = Array(xSlots.length).fill(0);
    filteredPoints.forEach(p => {
        const idx = getSlotIndex(p.date);
        if (p.weight > slotWorkouts[idx]) slotWorkouts[idx] = p.weight;
    });
    
    const slot1RM = Array(xSlots.length).fill(null);
    filteredRM.forEach(p => {
        const idx = getSlotIndex(p.date);
        if (p.weight > (slot1RM[idx] || 0)) slot1RM[idx] = p.weight;
    });

    let bars = '';
    slotWorkouts.forEach((w, i) => {
        if (w > 0) {
            const x = toX(i);
            const y = toY(w);
            const barW = 10;
            bars += `<rect x="${x - barW/2}" y="${y}" width="${barW}" height="${padT + chartH - y}" fill="#3b82f6" rx="3" />`;
        }
    });

    // 1RM (Green Line)
    let rmLine = '';
    const activeRM = slot1RM.map((w, i) => ({w, i})).filter(o => o.w !== null);
    if (activeRM.length > 0) {
        const pts = activeRM.map(o => `${toX(o.i)},${toY(o.w)}`).join(' ');
        rmLine += `<polyline points="${pts}" fill="none" stroke="#10b981" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"/>`;
        
        activeRM.forEach(o => {
            rmLine += `<circle cx="${toX(o.i)}" cy="${toY(o.w)}" r="5" fill="#10b981"/>`;
        });
    }

    // X Axis Labels
    let xLabels = '';
    xSlots.forEach((label, i) => {
        xLabels += `<text x="${toX(i)}" y="${H - 15}" text-anchor="middle" fill="#71717a" font-size="10">${label}</text>`;
    });

    svg.setAttribute('viewBox', `0 0 ${W} ${H}`);
    svg.setAttribute('width', W);
    svg.setAttribute('height', H);
    svg.innerHTML = `
        ${gridLines}
        ${bars}
        ${rmLine}
        ${xLabels}
    `;

    document.getElementById('chartContainer').style.overflowX = 'hidden'; // Fixed width fits screen
}

function renderStats(summary) {
    if (!summary) return;
    
    function fmt(n) { 
        if (!n) return '0';
        return n >= 1000 ? (n / 1000).toFixed(1) + 'k' : String(n); 
    }
    
    document.getElementById('statORM').textContent = Number(parseFloat(summary.one_rep_max || 0).toFixed(1)) + ' kg';
    document.getElementById('statWeight').textContent = Math.round(summary.total_weight || 0) + ' kg';
    document.getElementById('statSets').textContent = fmt(summary.total_sets || 0);
    document.getElementById('statReps').textContent = fmt(summary.total_reps || 0);
    
    document.getElementById('statsGrid').style.display = 'grid';
}

document.addEventListener('DOMContentLoaded', () => loadExercises());
</script>
@endpush

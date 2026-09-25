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
    .exercise-selector-wrap {
        background: rgba(0,0,0,0.55);
        border-radius: 12px; padding: 14px; margin-bottom: 16px;
    }
    .exercise-selector-label {
        color: rgba(255,255,255,0.6); font-size: 11px;
        font-weight: 600; letter-spacing: 1px;
        text-transform: uppercase; margin-bottom: 8px;
    }
    .exercise-select {
        width: 100%; background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 8px; color: #fff;
        padding: 10px 12px; font-size: 14px; font-weight: 600;
        appearance: none; cursor: pointer;
        font-family: inherit;
    }
    .exercise-select option { background: #111; color: #fff; }

    /* Range selector */
    .range-selector {
        display: flex; gap: 8px; margin-bottom: 16px;
    }
    .range-btn {
        flex: 1; padding: 8px 0; text-align: center;
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 8px; background: rgba(0,0,0,0.35);
        color: rgba(255,255,255,0.6); font-size: 12px; font-weight: 700;
        cursor: pointer; letter-spacing: 1px; transition: all 0.2s;
    }
    .range-btn.active {
        background: #fff; color: #000; border-color: #fff;
    }

    /* Chart card */
    .chart-card {
        background: rgba(0,0,0,0.6);
        border-radius: 14px; padding: 16px;
        margin-bottom: 16px;
    }
    .chart-title {
        color: rgba(255,255,255,0.5); font-size: 11px;
        font-weight: 600; letter-spacing: 1px; text-transform: uppercase;
        margin-bottom: 12px;
    }
    #chartContainer {
        width: 100%; overflow-x: auto; scrollbar-width: none;
    }
    #chartContainer::-webkit-scrollbar { display: none; }
    #lineChart { display: block; }

    /* Lifetime stats */
    .stats-grid {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 10px; margin-bottom: 16px;
    }
    .stat-card {
        background: rgba(0,0,0,0.6);
        border-radius: 12px; padding: 14px;
        text-align: center;
    }
    .stat-value { color: #fff; font-size: 22px; font-weight: 800; }
    .stat-label {
        color: rgba(255,255,255,0.5); font-size: 11px;
        font-weight: 600; letter-spacing: 0.5px; margin-top: 4px;
        text-transform: uppercase;
    }

    .empty-state {
        color: rgba(255,255,255,0.5); text-align: center;
        padding: 40px 0; font-size: 14px;
    }

    .loading-state {
        display: flex; align-items: center; justify-content: center;
        color: rgba(255,255,255,0.6); padding: 40px; gap: 10px; font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="achieve-bg">
    <h1 class="achieve-title">Achievements</h1>

    {{-- Exercise selector --}}
    <div class="exercise-selector-wrap">
        <div class="exercise-selector-label">Exercise</div>
        <select class="exercise-select" id="exerciseSelect" onchange="loadGraph()">
            <option value="">Loading exercises...</option>
        </select>
    </div>

    {{-- Range buttons --}}
    <div class="range-selector">
        <button class="range-btn active" data-range="WEEK" onclick="setRange('WEEK', this)">WEEK</button>
        <button class="range-btn" data-range="MONTH" onclick="setRange('MONTH', this)">MONTH</button>
        <button class="range-btn" data-range="YEAR" onclick="setRange('YEAR', this)">YEAR</button>
    </div>

    {{-- Chart --}}
    <div class="chart-card">
        <div class="chart-title">Progress — Weight (kg)</div>
        <div id="chartContainer">
            <div class="empty-state" id="chartEmpty">Select an exercise to view progress.</div>
            <svg id="lineChart" style="display:none;"></svg>
        </div>
    </div>

    {{-- Lifetime stats --}}
    <div class="stats-grid" id="statsGrid" style="display:none;">
        <div class="stat-card">
            <div class="stat-value" id="statSets">0</div>
            <div class="stat-label">Total Sets</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="statReps">0</div>
            <div class="stat-label">Total Reps</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="statWeight">0</div>
            <div class="stat-label">Kg Lifted</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="statORM">0</div>
            <div class="stat-label">Best 1RM</div>
        </div>
    </div>

    <div class="loading-state" id="graphLoading" style="display:none;">
        <i class="fas fa-spinner fa-spin"></i> Loading graph...
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentRange = 'WEEK';
let allPoints = [];
let exercises = [];

// --- Fetch exercises ---
async function loadExercises() {
    try {
        const resp = await fetch('/mobile/data/exercises', { headers: { 'Accept': 'application/json' } });
        const data = await resp.json();
        if (data.status === 'success' && data.data) {
            exercises = data.data;
            const sel = document.getElementById('exerciseSelect');
            sel.innerHTML = '<option value="">Select an exercise...</option>' +
                data.data.map(e => `<option value="${e.id}">${e.workout} (${e.type})</option>`).join('');
        }
    } catch(e) {
        document.getElementById('exerciseSelect').innerHTML = '<option value="">Failed to load</option>';
    }
}

function setRange(range, btn) {
    currentRange = range;
    document.querySelectorAll('.range-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderChart(allPoints);
}

async function loadGraph() {
    const workoutId = document.getElementById('exerciseSelect').value;
    if (!workoutId) {
        document.getElementById('chartEmpty').style.display = 'block';
        document.getElementById('lineChart').style.display = 'none';
        document.getElementById('statsGrid').style.display = 'none';
        return;
    }

    document.getElementById('graphLoading').style.display = 'flex';
    document.getElementById('chartEmpty').style.display = 'none';
    document.getElementById('lineChart').style.display = 'none';
    document.getElementById('statsGrid').style.display = 'none';

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const resp = await fetch('/mobile/data/achievement-graph', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ workout_id: workoutId })
        });
        const data = await resp.json();
        if (data.status === 'success' && data.data) {
            const raw = Array.isArray(data.data) ? data.data : Object.values(data.data);
            allPoints = raw.map(p => ({
                date: p.date,
                weight: parseFloat(p.weight) || 0,
                reps: parseInt(p.reps) || 0,
                sets: parseInt(p.setsCompleted || p.sets_completed || 1),
            }));
            renderChart(allPoints);
            renderStats(allPoints);
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

function renderChart(points) {
    const filtered = filterByRange(points);
    const svg = document.getElementById('lineChart');
    const empty = document.getElementById('chartEmpty');

    if (!filtered.length) {
        svg.style.display = 'none';
        empty.style.display = 'block';
        empty.textContent = 'No data in this time range.';
        return;
    }

    empty.style.display = 'none';
    svg.style.display = 'block';

    const W = Math.max(320, filtered.length * 55);
    const H = 160;
    const padL = 36, padR = 16, padT = 12, padB = 30;
    const chartW = W - padL - padR;
    const chartH = H - padT - padB;

    const weights = filtered.map(p => p.weight);
    const minW = Math.min(...weights);
    const maxW = Math.max(...weights);
    const range = maxW - minW || 1;

    const toX = (i) => padL + (i / (filtered.length - 1 || 1)) * chartW;
    const toY = (w) => padT + chartH - ((w - minW) / range) * chartH;

    const pts = filtered.map((p, i) => `${toX(i)},${toY(p.weight)}`).join(' ');

    // Gradient area
    const areaPath = filtered.map((p, i) => (i === 0 ? `M` : `L`) + ` ${toX(i)},${toY(p.weight)}`).join(' ')
        + ` L ${toX(filtered.length - 1)},${padT + chartH} L ${padL},${padT + chartH} Z`;

    svg.setAttribute('viewBox', `0 0 ${W} ${H}`);
    svg.setAttribute('width', W);
    svg.setAttribute('height', H);
    svg.innerHTML = `
        <defs>
            <linearGradient id="lineGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="rgba(255,255,255,0.3)"/>
                <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
            </linearGradient>
        </defs>
        <path d="${areaPath}" fill="url(#lineGrad)" />
        <polyline points="${pts}" fill="none" stroke="#fff" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
        ${filtered.map((p, i) => `
            <circle cx="${toX(i)}" cy="${toY(p.weight)}" r="4" fill="#fff"/>
            <text x="${toX(i)}" y="${toY(p.weight) - 8}" text-anchor="middle" fill="rgba(255,255,255,0.8)" font-size="9">${p.weight}kg</text>
            <text x="${toX(i)}" y="${H - 6}" text-anchor="middle" fill="rgba(255,255,255,0.5)" font-size="8">${p.date ? p.date.slice(5) : ''}</text>
        `).join('')}
        <text x="${padL - 4}" y="${padT + chartH}" text-anchor="end" fill="rgba(255,255,255,0.4)" font-size="8">${minW}</text>
        <text x="${padL - 4}" y="${padT + 6}" text-anchor="end" fill="rgba(255,255,255,0.4)" font-size="8">${maxW}</text>
    `;

    // Sync container width
    document.getElementById('chartContainer').style.overflowX = W > window.innerWidth - 64 ? 'auto' : 'hidden';
}

function epley1RM(w, r) { return r === 1 ? w : +(w * (1 + r / 30)).toFixed(1); }

function renderStats(points) {
    if (!points.length) return;
    let sets = 0, reps = 0, vol = 0, best1RM = 0;
    for (const p of points) {
        sets += p.sets;
        reps += p.reps * p.sets;
        vol += p.weight * p.reps * p.sets;
        const rm = epley1RM(p.weight, p.reps);
        if (rm > best1RM) best1RM = rm;
    }

    function fmt(n) { return n >= 1000 ? (n / 1000).toFixed(1) + 'k' : String(n); }
    document.getElementById('statSets').textContent = fmt(sets);
    document.getElementById('statReps').textContent = fmt(reps);
    document.getElementById('statWeight').textContent = fmt(Math.round(vol));
    document.getElementById('statORM').textContent = best1RM + 'kg';
    document.getElementById('statsGrid').style.display = 'grid';
}

document.addEventListener('DOMContentLoaded', () => loadExercises());
</script>
@endpush

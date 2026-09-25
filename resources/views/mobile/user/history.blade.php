@extends('mobile.layout.mobile-layout')

@push('head-styles')
<style>
    .history-bg {
        min-height: calc(100vh - 120px);
        background-image: url('{{ asset('img/valhalla-bg.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .history-header {
        padding: 20px 20px 10px;
    }
    .history-title {
        color: #fff;
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.3px;
    }

    /* Filter tabs */
    .filter-tabs-wrap {
        overflow-x: auto;
        scrollbar-width: none;
        padding: 0 20px 14px;
        display: flex;
    }
    .filter-tabs-wrap::-webkit-scrollbar { display: none; }
    .filter-tabs {
        display: flex; gap: 10px;
        flex-shrink: 0;
    }
    .filter-tab {
        flex-shrink: 0;
        padding: 7px 18px; border-radius: 20px;
        background: rgba(0,0,0,0.4);
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.2s;
        white-space: nowrap;
    }
    .filter-tab.active {
        background: rgba(255,255,255,0.3);
        border-color: #fff;
        color: #000;
    }

    /* List */
    .history-list {
        padding: 0 20px 20px;
        display: flex; flex-direction: column; gap: 14px;
    }
    .history-item {
        display: flex; align-items: center; gap: 14px;
        background: rgba(0,0,0,0.6);
        border-radius: 12px; padding: 14px;
    }
    .history-icon-wrap {
        width: 52px; height: 52px; border-radius: 26px;
        background: rgba(255,255,255,0.1);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .history-icon-wrap img { width: 32px; height: 32px; object-fit: contain; }
    .history-text { flex: 1; }
    .history-workout-name { color: #fff; font-size: 15px; font-weight: 700; }
    .history-details { color: rgba(255,255,255,0.8); font-size: 13px; margin-top: 3px; }
    .history-date { color: rgba(255,255,255,0.5); font-size: 12px; margin-top: 3px; }

    .empty-state {
        text-align: center; color: #fff; padding: 60px 20px;
        font-size: 15px; opacity: 0.7;
    }
    .loading-state {
        display: flex; align-items: center; justify-content: center;
        color: #fff; padding: 60px 20px; font-size: 14px; gap: 10px;
    }
</style>
@endpush

@section('content')
<div class="history-bg">
    <div class="history-header">
        <h1 class="history-title">Latest Sessions</h1>
    </div>

    {{-- Filter Tabs --}}
    <div class="filter-tabs-wrap">
        <div class="filter-tabs" id="filterTabs">
            @foreach([
                ['key' => 'all',           'label' => 'All'],
                ['key' => 'warmup',        'label' => 'Warmup'],
                ['key' => 'strength',      'label' => 'Strength'],
                ['key' => 'weightlifting', 'label' => 'Weightlifting'],
                ['key' => 'conditioning',  'label' => 'Conditioning'],
                ['key' => 'accessory',     'label' => 'Accessory'],
            ] as $tab)
                <button
                    class="filter-tab {{ $tab['key'] === 'all' ? 'active' : '' }}"
                    data-type="{{ $tab['key'] }}"
                    onclick="setFilter('{{ $tab['key'] }}', this)"
                >{{ $tab['label'] }}</button>
            @endforeach
        </div>
    </div>

    {{-- History list --}}
    <div id="historyList" class="history-list">
        <div class="loading-state">
            <i class="fas fa-spinner fa-spin"></i> Loading history...
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const typeIcons = {
    warmup:        '{{ asset('icon/warmupwhite.png') }}',
    strength:      '{{ asset('icon/strengthwhite.png') }}',
    weightlifting: '{{ asset('icon/weightliftingWhite.png') }}',
    conditioning:  '{{ asset('icon/conditioningWhite.png') }}',
    accessory:     '{{ asset('icon/accessoryWhite.png') }}',
};

let allHistory = [];
let currentFilter = 'all';

async function loadHistory() {
    document.getElementById('historyList').innerHTML =
        '<div class="loading-state"><i class="fas fa-spinner fa-spin"></i> Loading history...</div>';
    try {
        const resp = await fetch('/mobile/history-data', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
        });
        const data = await resp.json();
        if (data.status === 'success') {
            allHistory = data.data || [];
        } else {
            allHistory = [];
        }
        renderHistory();
    } catch (e) {
        document.getElementById('historyList').innerHTML =
            '<div class="empty-state">Failed to load history. Please try again.</div>';
    }
}

function setFilter(type, btn) {
    currentFilter = type;
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    renderHistory();
}

function renderHistory() {
    const filtered = currentFilter === 'all'
        ? allHistory
        : allHistory.filter(item => item.type === currentFilter);

    const container = document.getElementById('historyList');

    if (filtered.length === 0) {
        container.innerHTML = '<div class="empty-state">No workout history found.</div>';
        return;
    }

    container.innerHTML = filtered.map(item => {
        const iconSrc = typeIcons[item.type] || typeIcons.strength;
        const weightDisplay = item.weight !== null && item.weight !== undefined ? `${item.weight} kg` : '—';
        const repsDisplay = item.reps ? `× ${item.reps} reps` : '';
        return `
            <div class="history-item">
                <div class="history-icon-wrap">
                    <img src="${iconSrc}" alt="${item.type}">
                </div>
                <div class="history-text">
                    <div class="history-workout-name">${item.workout || '—'}</div>
                    <div class="history-details">${item.category_name || ''} ${item.category_name && (weightDisplay !== '—' || repsDisplay) ? '|' : ''} ${weightDisplay} ${repsDisplay}</div>
                    <div class="history-date">${item.date || ''}</div>
                </div>
            </div>
        `;
    }).join('');
}

document.addEventListener('DOMContentLoaded', () => loadHistory());
</script>
@endpush

@extends('mobile.layout.mobile-layout')

@push('head-styles')
<style>
    .history-bg {
        min-height: calc(100vh - 120px);
        position: relative;
    }
    .history-bg::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('{{ asset('img/valhalla-bg.jpg') }}');
        background-size: cover;
        background-position: center top;
        z-index: 0;
        pointer-events: none;
    }

    .history-header,
    .filter-tabs-wrap,
    .history-list {
        position: relative;
        z-index: 1;
    }

    .history-header {
        padding: 14px 16px 8px;
    }
    .history-title {
        color: #000;
        font-size: 19px;
        font-weight: bold;
        text-align: left;
        margin: 0;
        letter-spacing: -0.2px;
    }

    /* Filter tabs */
    .filter-tabs-wrap {
        overflow-x: auto;
        scrollbar-width: none;
        padding: 0 16px;
        margin-bottom: 10px;
        display: flex;
        scroll-behavior: smooth;
        height: 38px;
        align-items: center;
        box-sizing: border-box;
    }
    .filter-tabs-wrap::-webkit-scrollbar { display: none; }
    .filter-tabs {
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }
    .filter-tab {
        flex-shrink: 0;
        padding: 5px 14px;
        border-radius: 16px;
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        margin-right: 8px;
        outline: none;
        box-sizing: border-box;
        text-decoration: none;
        user-select: none;
    }
    .filter-tab.active {
        background: rgba(255, 255, 255, 0.3);
        border-color: #ffffff;
        color: #000000;
    }

    /* List */
    .history-list {
        padding: 0 16px 20px;
        display: flex;
        flex-direction: column;
    }
    .history-item {
        display: flex;
        flex-direction: row;
        background: rgba(0, 0, 0, 0.6);
        padding: 10px 12px;
        border-radius: 10px;
        margin-bottom: 9px;
        align-items: center;
        box-sizing: border-box;
    }
    .history-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        flex-shrink: 0;
    }
    .history-icon-wrap img {
        width: 26px;
        height: 26px;
        object-fit: contain;
    }
    .history-text {
        flex: 1;
        overflow: hidden;
    }
    .history-workout-name {
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: -0.2px;
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .history-details {
        color: #fff;
        font-size: 11.5px;
        font-weight: 400;
        margin-top: 2px;
        opacity: 0.95;
    }
    .history-date {
        color: #ccc;
        font-size: 10.5px;
        margin-top: 2px;
    }

    .empty-state {
        color: #fff;
        text-align: center;
        margin-top: 35px;
        font-size: 13px;
        padding: 0 16px;
    }
    .loading-state {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        margin-top: 35px;
        font-size: 12.5px;
        gap: 8px;
    }
</style>
@endpush

@section('content')
<div class="history-bg">
    <div class="history-header">
        <h1 class="history-title">Latest Sessions</h1>
    </div>

    {{-- Filter Tabs --}}
    <div class="filter-tabs-wrap" id="filterTabsWrap">
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
        const csrfEl = document.querySelector('meta[name="csrf-token"]');
        const token = csrfEl ? csrfEl.getAttribute('content') : '';
        const resp = await fetch('/mobile/history-data', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token }
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
            '<div class="empty-state">No workout history found.</div>';
    }
}

function setFilter(type, btn) {
    currentFilter = type;
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');

    // Smoothly scroll the clicked tab into view matching the mobile app's scroll offset
    const wrap = document.getElementById('filterTabsWrap');
    if (wrap && btn) {
        const offset = Math.max(0, btn.offsetLeft - 80);
        wrap.scrollTo({ left: offset, behavior: 'smooth' });
    }

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
        const weightVal = (item.weight !== null && item.weight !== undefined && item.weight !== '') ? item.weight : '0';
        const repsVal = (item.reps !== null && item.reps !== undefined && item.reps !== '') ? item.reps : '0';
        const weightDisplay = `${weightVal} kg`;
        const repsDisplay = `x ${repsVal} reps`;
        const category = item.category_name ? `${item.category_name} | ` : '';
        const detailsText = `${category}${weightDisplay} ${repsDisplay}`;

        return `
            <div class="history-item">
                <div class="history-icon-wrap">
                    <img src="${iconSrc}" alt="${item.type}">
                </div>
                <div class="history-text">
                    <div class="history-workout-name">${item.workout || '—'}</div>
                    <div class="history-details">${detailsText}</div>
                    <div class="history-date">${item.date || ''}</div>
                </div>
            </div>
        `;
    }).join('');
}

document.addEventListener('DOMContentLoaded', () => loadHistory());
</script>
@endpush

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Workout</title>
</head>
<body class="overflow-y-auto">
    @extends('mobile.layout.mobile-layout')
    @section('content')
        <div class="w-full flex flex-col min-h-screen bg-cover bg-center bg-no-repeat font-sans"
            style="background-image: url('{{ asset('img/valhalla-bg.jpg') }}'); background-attachment: fixed;">
            <div class="w-full flex-grow flex flex-col p-4 bg-black bg-opacity-40 pb-28">
                {{-- Horizontal scrolling tabs --}}
                <div class="flex gap-4 overflow-x-auto justify-center pb-4 mb-4 scrollbar-hide px-2 pt-4" id="category-tabs" style="-ms-overflow-style: none; scrollbar-width: none;">
                    {{-- Tabs injected by JS --}}
                </div>
                <style>
                    #category-tabs::-webkit-scrollbar { display: none; }
                </style>

                {{-- Workouts Container --}}
                <div id="workouts-container" class="flex flex-col gap-6">
                    {{-- Loader --}}
                    <div id="loader" class="text-white text-center py-10 hidden">
                        <svg class="animate-spin h-8 w-8 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <p class="mt-4 font-bold tracking-widest text-sm">LOADING WORKOUTS...</p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Global variables passed from PHP
            const dayWithDate = @json($dayWithDate);
            const classId = @json($classId);

            const TABS = [
                { id: 'warmup', label: 'Warm\nUp', iconWhite: '{{ asset("icon/warmupwhite.png") }}', iconBlack: '{{ asset("icon/warmup.png") }}' },
                { id: 'strength', label: 'Strength', iconWhite: '{{ asset("icon/strengthwhite.png") }}', iconBlack: '{{ asset("icon/strength.png") }}' },
                { id: 'weightlifting', label: 'Weight\nLifting', iconWhite: '{{ asset("icon/weightliftingWhite.png") }}', iconBlack: '{{ asset("icon/weightlifting.png") }}' },
                { id: 'conditioning', label: 'Condi\ntioning', iconWhite: '{{ asset("icon/conditioningWhite.png") }}', iconBlack: '{{ asset("icon/conditioning.png") }}' },
                { id: 'test', label: 'Test', iconWhite: '{{ asset("icon/testWhite.png") }}', iconBlack: '{{ asset("icon/test.png") }}' }
            ];

            let activeTab = 'warmup';
            let workoutsData = {};

            document.addEventListener('DOMContentLoaded', () => {
                renderTabs();
                fetchWorkouts();
            });

            function renderTabs() {
                const container = document.getElementById('category-tabs');
                container.innerHTML = '';
                TABS.forEach(tab => {
                    const isActive = tab.id === activeTab;
                    const bgClass = isActive ? 'bg-white text-black scale-105' : 'bg-transparent text-white';
                    const iconSrc = isActive ? tab.iconBlack : tab.iconWhite;
                    const html = `
                        <button onclick="setActiveTab('${tab.id}')"
                            class="flex-shrink-0 flex flex-col items-center justify-center w-[70px] h-[70px] border border-white rounded-xl px-1 py-2 ${bgClass} transition duration-300 ease-in-out transform hover:scale-105"
                        >
                            <img src="${iconSrc}" class="w-6 h-6 mb-1 object-contain">
                            <span class="text-[10px] leading-tight text-center font-bold" style="white-space: pre-wrap;">${tab.label}</span>
                        </button>
                    `;
                    container.insertAdjacentHTML('beforeend', html);
                });
            }

            function setActiveTab(tabId) {
                activeTab = tabId;
                renderTabs();
                renderWorkouts();
            }

            async function fetchWorkouts() {
                document.getElementById('loader').classList.remove('hidden');
                document.getElementById('workouts-container').innerHTML = '';

                try {
                    const res = await fetch('/mobile/get-workout-list', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ date: dayWithDate, class_id: classId })
                    });
                    const data = await res.json();
                    if(data.status && data.workouts) {
                        workoutsData = {
                            warmup: data.workouts.Warmup || [],
                            strength: data.workouts.Strength || [],
                            weightlifting: data.workouts.Weightlifting || [],
                            conditioning: data.workouts.Conditioning || [],
                            test: data.test || []
                        };
                    } else {
                        workoutsData = { warmup:[], strength:[], weightlifting:[], conditioning:[], test:[] };
                    }
                } catch(e) {
                    console.error('Failed to fetch workouts', e);
                }

                document.getElementById('loader').classList.add('hidden');
                renderWorkouts();
            }

            function renderWorkouts() {
                const container = document.getElementById('workouts-container');
                container.innerHTML = '';
                
                const list = workoutsData[activeTab] || [];
                
                if (list.length === 0) {
                    container.innerHTML = `
                        <div class="flex-grow flex items-center justify-center mt-32">
                            <div class="text-white text-center text-sm font-semibold tracking-wide">No Workouts.</div>
                        </div>
                    `;
                    return;
                }

                list.forEach((w, idx) => {
                    let title = "Workout";
                    let formatName = "FORMAT";
                    let catName = "CATEGORY";
                    let desc = "";
                    
                    if (w.workout) {
                        title = w.workout.workout || "Workout";
                        catName = (w.workout.category_option && w.workout.category_option.category_name) ? w.workout.category_option.category_name : "STRENGTH AND CONDITIONING";
                        formatName = (w.workout.format && w.workout.format.name) ? w.workout.format.name : "AMRAP";
                        desc = w.workout.description || '';
                    } else if (w.workouts) {
                        title = w.workouts.workout || "Workout";
                        catName = (w.workouts.category_option && w.workouts.category_option.category_name) ? w.workouts.category_option.category_name : "STRENGTH AND CONDITIONING";
                        formatName = (w.workouts.format && w.workouts.format.name) ? w.workouts.format.name : "AMRAP";
                        desc = w.workouts.description || '';
                    } else if (activeTab === 'test') {
                        title = w.workout_name || "Test Workout";
                        catName = w.category_name || "TESTING";
                        formatName = w.format_name || "AMRAP";
                        desc = w.description || '';
                    }

                    let cardHtml = `
                        <div class="bg-black bg-opacity-60 rounded-3xl p-5 border border-gray-800 shadow-xl relative overflow-hidden backdrop-blur-md">
                            <div class="flex items-center gap-2 mb-3 flex-wrap">
                                <span class="bg-white text-black px-3 py-1 rounded-full text-[10px] font-black tracking-widest uppercase">${formatName}</span>
                                <span class="text-[10px] text-gray-300 font-bold tracking-widest uppercase">${catName}</span>
                            </div>
                            
                            <h2 class="text-white text-2xl font-black italic tracking-wide uppercase">${title}</h2>
                            
                            <div class="border-b border-gray-600 my-4"></div>
                    `;

                    let repText = w.reps ? `x ${w.reps}` : '';
                    let weightHtml = w.weight ? `<div class="bg-gray-800 rounded px-3 py-1 text-white text-sm font-bold">${w.weight} kg</div>` : `<input type="text" placeholder="kg" class="bg-transparent border-b border-gray-500 w-12 text-center text-white text-sm placeholder-gray-500 focus:outline-none">`;

                    if(activeTab === 'conditioning') {
                        let exercisesHtml = `
                            <div class="text-white font-semibold text-sm mb-2">Workout Notes / Description</div>
                            <div class="text-gray-400 text-xs mb-6 whitespace-pre-wrap">${desc ? desc : 'Complete the reps as fast as possible.'}</div>
                        `;
                        cardHtml += exercisesHtml;

                        let formatKey = formatName.toLowerCase().replace(/[^a-z]/g, '');
                        if(formatKey.includes('amrap')) formatKey = 'amrap';
                        else if(formatKey.includes('time')) formatKey = 'fortime';
                        else if(formatKey.includes('emom')) formatKey = 'emom';
                        else formatKey = 'amrap';

                        cardHtml += `
                            <button onclick="openWorkoutTimer('${formatKey}')" class="w-full bg-[#1bc55c] hover:bg-green-500 text-black font-black text-lg py-3 rounded-xl mt-2 transition-all flex items-center justify-center gap-2 shadow-[0_0_15px_rgba(27,197,92,0.4)]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                START TIMER
                            </button>
                        `;

                    } else if (activeTab === 'strength' || activeTab === 'weightlifting') {
                        let sets = w.sets || [];
                        if (sets.length > 0) {
                            sets.forEach((set, sIdx) => {
                                let setReps = set.reps || 0;
                                let setWeight = set.weight || '';
                                cardHtml += `
                                    <div class="flex justify-between items-center mb-3">
                                        <div class="text-white text-sm font-semibold tracking-wider">Set ${set.sets || (sIdx+1)} <span class="text-gray-400 font-normal ml-2">x ${setReps} reps</span></div>
                                        <div class="flex items-center gap-2">
                                            <input type="text" placeholder="kg" value="${setWeight}" class="bg-gray-800 rounded px-2 py-1 w-16 text-center text-white text-sm border border-gray-700 focus:outline-none focus:border-white">
                                            <button class="w-8 h-8 rounded-full border border-gray-500 flex items-center justify-center hover:bg-white hover:text-black transition-colors text-white" onclick="this.classList.toggle('bg-[#1bc55c]'); this.classList.toggle('text-black'); this.classList.toggle('border-[#1bc55c]');">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            cardHtml += `<div class="text-gray-400 text-xs mb-4">No sets detailed.</div>`;
                        }

                        cardHtml += `
                            <button onclick="openWorkoutTimer('straightsets', {sets: ${sets.length || 4}, restSecs: 90})" class="w-full bg-[#1bc55c] hover:bg-green-500 text-black font-black text-sm py-3 rounded-xl mt-4 transition-all flex items-center justify-center gap-2 shadow-[0_0_10px_rgba(27,197,92,0.3)]">
                                START REST TIMER
                            </button>
                        `;

                    } else {
                        // Warmup & Test
                        if (desc) {
                            cardHtml += `<div class="text-gray-400 text-xs mb-4 whitespace-pre-wrap">${desc}</div>`;
                        }
                        
                        cardHtml += `
                            <div class="flex justify-between items-center mb-4">
                                <div class="text-white font-semibold text-sm">Exercises ${repText}</div>
                                ${weightHtml}
                            </div>
                            <button onclick="this.innerHTML='COMPLETED'; this.classList.add('bg-[#1bc55c]'); this.classList.remove('bg-white'); this.classList.add('text-black');" class="w-full bg-white hover:bg-gray-200 text-black font-black text-sm py-3 rounded-xl mt-2 transition-all flex items-center justify-center gap-2 shadow-lg">
                                MARK AS COMPLETED
                            </button>
                        `;
                    }

                    cardHtml += `</div>`;
                    container.insertAdjacentHTML('beforeend', cardHtml);
                });
            }
        </script>

        {{-- =====================================================================
             WORKOUT FORMAT TIMER SYSTEM
             Mirrors the mobile app's AMRAP, ForTime, EMOM, Intervals, Rounds,
             StraightSets, Circuit, and Pyramid timer formats.
        ===================================================================== --}}
        <script>
        function openWorkoutTimer(format, options) {
            WFTimer.open(format, options || {});
        }
        </script>

        {{-- Timer Full-Screen Overlay --}}
        <div id="wft-overlay" style="
            display:none; position:fixed; inset:0; z-index:9000;
            background: rgba(0,0,0,0.97);
            flex-direction:column; align-items:center; justify-content:center;
            font-family:'Inter',sans-serif;
        ">
            {{-- Countdown 3-2-1 screen --}}
            <div id="wft-countdown" style="display:none;text-align:center;">
                <div id="wft-countdown-num" style="color:#fff;font-size:96px;font-weight:900;line-height:1;"></div>
                <div style="color:rgba(255,255,255,0.5);font-size:16px;margin-top:12px;letter-spacing:2px;text-transform:uppercase;" id="wft-countdown-label">GET READY</div>
            </div>

            {{-- Main timer screen --}}
            <div id="wft-main" style="display:none;text-align:center;width:100%;padding:24px;">
                <div id="wft-format-label" style="color:rgba(255,255,255,0.4);font-size:12px;font-weight:700;letter-spacing:3px;text-transform:uppercase;margin-bottom:8px;"></div>
                <div id="wft-phase-label" style="color:#fff;font-size:14px;font-weight:600;margin-bottom:16px;letter-spacing:1px;text-transform:uppercase;min-height:20px;"></div>

                <div id="wft-time" style="
                    color:#fff; font-size:80px; font-weight:900;
                    letter-spacing:-2px; font-variant-numeric:tabular-nums;
                    line-height:1; margin-bottom:12px;
                ">00:00</div>

                <div id="wft-round-info" style="color:rgba(255,255,255,0.5);font-size:16px;font-weight:600;margin-bottom:32px;min-height:24px;"></div>

                <div style="display:flex;gap:16px;justify-content:center;align-items:center;flex-wrap:wrap;">
                    <button id="wft-btn-stop" onclick="WFTimer.stop()" style="
                        padding:12px 28px; border-radius:10px;
                        background:rgba(239,68,68,0.2); border:1px solid #ef4444;
                        color:#ef4444; font-size:15px; font-weight:700; cursor:pointer;
                    ">STOP</button>

                    <button id="wft-btn-pause" onclick="WFTimer.togglePause()" style="
                        padding:16px 36px; border-radius:12px;
                        background:#fff; border:none;
                        color:#000; font-size:17px; font-weight:800; cursor:pointer;
                        min-width:120px;
                    ">PAUSE</button>

                    <button id="wft-btn-skip" onclick="WFTimer.skip()" style="
                        padding:12px 28px; border-radius:10px;
                        background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.3);
                        color:#fff; font-size:15px; font-weight:700; cursor:pointer; display:none;
                    ">SKIP ›</button>
                </div>
            </div>

            {{-- Done screen --}}
            <div id="wft-done" style="display:none;text-align:center;padding:24px;">
                <div style="font-size:56px;margin-bottom:12px;">🏆</div>
                <div style="color:#fff;font-size:28px;font-weight:900;margin-bottom:8px;">WORKOUT COMPLETE!</div>
                <div id="wft-done-time" style="color:rgba(255,255,255,0.6);font-size:16px;margin-bottom:32px;"></div>
                <button onclick="WFTimer.close()" style="
                    padding:16px 48px; border-radius:12px;
                    background:#fff; border:none;
                    color:#000; font-size:16px; font-weight:800; cursor:pointer;
                ">DONE</button>
            </div>
        </div>

        <div id="wft-picker" style="
            display:none;position:fixed;inset:0;z-index:8000;
            background:rgba(0,0,0,0.85);
            align-items:flex-end;justify-content:center;
        ">
            <div style="background:#111;border-radius:20px 20px 0 0;width:100%;max-width:500px;padding:24px;">
                <div style="color:#fff;font-size:16px;font-weight:700;margin-bottom:16px;text-align:center;">Select Timer Format</div>
                <div id="wft-picker-list" style="display:flex;flex-direction:column;gap:10px;">
                    <button onclick="WFTimer.openWithConfig('amrap')" class="flex flex-col gap-[2px] p-3 rounded-lg bg-white bg-opacity-5 border border-white border-opacity-15 text-left text-white">
                        <span class="text-sm font-bold">AMRAP</span><span class="text-xs text-white text-opacity-50">As Many Rounds As Possible</span>
                    </button>
                    <button onclick="WFTimer.openWithConfig('fortime')" class="flex flex-col gap-[2px] p-3 rounded-lg bg-white bg-opacity-5 border border-white border-opacity-15 text-left text-white">
                        <span class="text-sm font-bold">For Time</span><span class="text-xs text-white text-opacity-50">Count down to complete</span>
                    </button>
                    <button onclick="WFTimer.openWithConfig('emom')" class="flex flex-col gap-[2px] p-3 rounded-lg bg-white bg-opacity-5 border border-white border-opacity-15 text-left text-white">
                        <span class="text-sm font-bold">EMOM</span><span class="text-xs text-white text-opacity-50">Every Minute On the Minute</span>
                    </button>
                    <button onclick="WFTimer.openWithConfig('intervals')" class="flex flex-col gap-[2px] p-3 rounded-lg bg-white bg-opacity-5 border border-white border-opacity-15 text-left text-white">
                        <span class="text-sm font-bold">Intervals</span><span class="text-xs text-white text-opacity-50">Alternating work/rest</span>
                    </button>
                    <button onclick="WFTimer.openWithConfig('straightsets')" class="flex flex-col gap-[2px] p-3 rounded-lg bg-white bg-opacity-5 border border-white border-opacity-15 text-left text-white">
                        <span class="text-sm font-bold">Straight Sets</span><span class="text-xs text-white text-opacity-50">Rest between sets</span>
                    </button>
                </div>
                <button onclick="WFTimer.closePicker()" class="mt-4 w-full p-3 bg-red-500 bg-opacity-15 border border-red-500 border-opacity-30 rounded-lg text-red-500 text-sm font-bold">Cancel</button>
            </div>
        </div>

        <div id="wft-config" style="
            display:none;position:fixed;inset:0;z-index:8100;
            background:rgba(0,0,0,0.85);
            align-items:flex-end;justify-content:center;
        ">
            <div style="background:#111;border-radius:20px 20px 0 0;width:100%;max-width:500px;padding:24px;">
                <div id="wft-config-title" style="color:#fff;font-size:16px;font-weight:700;margin-bottom:16px;text-align:center;"></div>
                <div id="wft-config-fields" style="display:flex;flex-direction:column;gap:12px;"></div>
                <div style="display:flex;gap:10px;margin-top:20px;">
                    <button onclick="WFTimer.closeConfig()" style="flex:1;padding:14px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.15);border-radius:10px;color:#fff;font-weight:700;cursor:pointer;">Cancel</button>
                    <button onclick="WFTimer.startFromConfig()" style="flex:2;padding:14px;background:#fff;border:none;border-radius:10px;color:#000;font-weight:800;cursor:pointer;font-size:15px;">START</button>
                </div>
            </div>
        </div>

        <script>
        const WFTimer = (() => {
            let tickInterval = null;
            let elapsedSecs = 0;
            let totalSecs = 0;
            let paused = false;
            let done = false;
            let format = null;
            let cfg = {};
            let phase = 'work';
            let phaseSecsLeft = 0;
            let currentRound = 1;
            let totalRounds = 1;
            let workSecs = 0;
            let restSecs = 0;

            const $ = id => document.getElementById(id);

            function fmt(s) {
                const m = Math.floor(Math.abs(s) / 60);
                const sec = Math.abs(s) % 60;
                return `${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`;
            }

            function show(screen) {
                $('wft-countdown').style.display = 'none';
                $('wft-main').style.display = 'none';
                $('wft-done').style.display = 'none';
                if (screen) $(screen).style.display = 'block';
            }

            function openOverlay() {
                $('wft-overlay').style.display = 'flex';
            }

            function startCountdown3(cb) {
                show('wft-countdown');
                let n = 3;
                $('wft-countdown-num').textContent = n;
                const iv = setInterval(() => {
                    n--;
                    if (n > 0) {
                        $('wft-countdown-num').textContent = n;
                    } else {
                        clearInterval(iv);
                        $('wft-countdown-num').textContent = 'GO!';
                        setTimeout(cb, 600);
                    }
                }, 1000);
            }

            function tick() {
                if (paused || done) return;
                elapsedSecs++;

                switch (format) {
                    case 'amrap':     tickAmrap();      break;
                    case 'fortime':   tickForTime();    break;
                    case 'emom':      tickEmom();       break;
                    case 'intervals': tickIntervals();  break;
                    case 'rounds':    tickRounds();     break;
                    case 'straightsets': tickStraight(); break;
                }
            }

            function startAmrap() {
                $('wft-format-label').textContent = 'AMRAP';
                $('wft-phase-label').textContent = `${cfg.minutes} Minutes`;
                $('wft-btn-skip').style.display = 'none';
                totalSecs = cfg.minutes * 60;
                elapsedSecs = 0;
                show('wft-main');
                updateAmrap();
                tickInterval = setInterval(tick, 1000);
            }
            function updateAmrap() {
                const left = totalSecs - elapsedSecs;
                $('wft-time').textContent = fmt(left);
                $('wft-round-info').textContent = '';
            }
            function tickAmrap() {
                const left = totalSecs - elapsedSecs;
                $('wft-time').textContent = fmt(left);
                if (left <= 0) finish();
            }

            function startForTime() {
                $('wft-format-label').textContent = 'FOR TIME';
                $('wft-phase-label').textContent = `Time Cap: ${cfg.timeCap} min`;
                $('wft-btn-skip').style.display = 'none';
                totalSecs = cfg.timeCap * 60;
                elapsedSecs = 0;
                show('wft-main');
                updateForTime();
                tickInterval = setInterval(tick, 1000);
            }
            function updateForTime() {
                const left = totalSecs - elapsedSecs;
                $('wft-time').textContent = fmt(left > 0 ? left : 0);
                $('wft-round-info').textContent = '';
            }
            function tickForTime() {
                const left = totalSecs - elapsedSecs;
                $('wft-time').textContent = fmt(left > 0 ? left : 0);
                if (left <= 0) finish();
            }

            function startEmom() {
                $('wft-format-label').textContent = 'EMOM';
                $('wft-btn-skip').style.display = 'block';
                totalRounds = cfg.rounds;
                currentRound = 1;
                elapsedSecs = 0;
                phaseSecsLeft = 60;
                show('wft-main');
                updateEmom();
                tickInterval = setInterval(tick, 1000);
            }
            function updateEmom() {
                $('wft-time').textContent = fmt(phaseSecsLeft);
                $('wft-phase-label').textContent = 'WORK';
                $('wft-round-info').textContent = `Minute ${currentRound} / ${totalRounds}`;
            }
            function tickEmom() {
                phaseSecsLeft--;
                $('wft-time').textContent = fmt(phaseSecsLeft);
                if (phaseSecsLeft <= 0) {
                    currentRound++;
                    if (currentRound > totalRounds) { finish(); return; }
                    phaseSecsLeft = 60;
                    updateEmom();
                }
            }

            function startIntervals() {
                $('wft-format-label').textContent = 'INTERVALS';
                $('wft-btn-skip').style.display = 'block';
                totalRounds = cfg.rounds;
                currentRound = 1;
                workSecs = cfg.workSecs;
                restSecs = cfg.restSecs;
                phase = 'work';
                phaseSecsLeft = workSecs;
                elapsedSecs = 0;
                show('wft-main');
                updateIntervals();
                tickInterval = setInterval(tick, 1000);
            }
            function updateIntervals() {
                $('wft-time').textContent = fmt(phaseSecsLeft);
                $('wft-phase-label').textContent = phase === 'work' ? '🔥 WORK' : '😮‍💨 REST';
                $('wft-round-info').textContent = `Round ${currentRound} / ${totalRounds}`;
            }
            function tickIntervals() {
                phaseSecsLeft--;
                $('wft-time').textContent = fmt(phaseSecsLeft);
                if (phaseSecsLeft <= 0) {
                    if (phase === 'work') {
                        if (restSecs > 0) {
                            phase = 'rest';
                            phaseSecsLeft = restSecs;
                        } else {
                            advanceRound();
                        }
                    } else {
                        advanceRound();
                    }
                    updateIntervals();
                }
            }
            function advanceRound() {
                currentRound++;
                if (currentRound > totalRounds) { finish(); return; }
                phase = 'work';
                phaseSecsLeft = workSecs;
            }

            function startStraight() {
                $('wft-format-label').textContent = 'STRAIGHT SETS';
                $('wft-phase-label').textContent = 'REST TIMER';
                $('wft-btn-skip').style.display = 'none';
                totalRounds = cfg.sets;
                currentRound = 0;
                restSecs = cfg.restSecs;
                phase = 'rest';
                phaseSecsLeft = restSecs;
                elapsedSecs = 0;
                show('wft-main');
                $('wft-round-info').textContent = `Set ${currentRound} / ${totalRounds} — Rest ${restSecs}s`;
                tickInterval = setInterval(tick, 1000);
            }
            function tickStraight() {
                phaseSecsLeft--;
                $('wft-time').textContent = fmt(phaseSecsLeft);
                if (phaseSecsLeft <= 0) {
                    currentRound++;
                    if (currentRound > totalRounds) { finish(); return; }
                    phaseSecsLeft = restSecs;
                    $('wft-round-info').textContent = `Set ${currentRound} / ${totalRounds} — Rest ${restSecs}s`;
                }
            }

            function finish() {
                clearInterval(tickInterval);
                tickInterval = null;
                done = true;
                const totalMin = Math.floor(elapsedSecs / 60);
                const totalSec = elapsedSecs % 60;
                $('wft-done-time').textContent = `Total time: ${String(totalMin).padStart(2,'0')}:${String(totalSec).padStart(2,'0')}`;
                show('wft-done');
            }

            return {
                open(fmt_, opts) {
                    format = fmt_;
                    cfg = opts;
                    paused = false;
                    done = false;
                    elapsedSecs = 0;
                    openOverlay();
                    
                    if(format === 'straightsets') {
                        // Skip countdown for rest timers
                        startStraight();
                        return;
                    }

                    if(!opts || Object.keys(opts).length === 0) {
                        this.openWithConfig(fmt_);
                        return;
                    }

                    startCountdown3(() => {
                        switch (format) {
                            case 'amrap':       startAmrap();     break;
                            case 'fortime':     startForTime();   break;
                            case 'emom':        startEmom();      break;
                            case 'intervals':   startIntervals(); break;
                            case 'rounds':      startRounds();    break;
                            case 'straightsets':startStraight();  break;
                        }
                    });
                },
                stop() {
                    clearInterval(tickInterval);
                    tickInterval = null;
                    this.close();
                },
                close() {
                    $('wft-overlay').style.display = 'none';
                    $('wft-picker').style.display = 'none';
                    $('wft-config').style.display = 'none';
                    clearInterval(tickInterval);
                    tickInterval = null;
                },
                togglePause() {
                    paused = !paused;
                    $('wft-btn-pause').textContent = paused ? 'RESUME' : 'PAUSE';
                },
                skip() {
                    if (format === 'intervals' || format === 'rounds' || format === 'emom') {
                        phaseSecsLeft = 0;
                    }
                },
                showPicker() {
                    $('wft-picker').style.display = 'flex';
                },
                closePicker() {
                    $('wft-picker').style.display = 'none';
                },
                openWithConfig(fmt_) {
                    this.closePicker();
                    format = fmt_;
                    const title = $('wft-config-title');
                    const fields = $('wft-config-fields');
                    const fieldStyle = `padding:10px 14px;border-radius:8px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.2);color:#fff;font-size:16px;width:100%;font-family:inherit;`;
                    const labelStyle = `color:rgba(255,255,255,0.5);font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin-bottom:6px;`;
                    const wrapStyle = `display:flex;flex-direction:column;`;

                    const configs = {
                        amrap:        { title:'AMRAP', html:`<div style="${wrapStyle}"><div style="${labelStyle}">Minutes</div><input id="c-minutes" type="number" value="10" min="1" style="${fieldStyle}"></div>` },
                        fortime:      { title:'For Time', html:`<div style="${wrapStyle}"><div style="${labelStyle}">Time Cap (minutes)</div><input id="c-timecap" type="number" value="20" min="1" style="${fieldStyle}"></div>` },
                        emom:         { title:'EMOM', html:`<div style="${wrapStyle}"><div style="${labelStyle}">Rounds (minutes)</div><input id="c-rounds" type="number" value="10" min="1" style="${fieldStyle}"></div>` },
                        intervals:    { title:'Intervals', html:`<div style="${wrapStyle}"><div style="${labelStyle}">Rounds</div><input id="c-rounds" type="number" value="5" min="1" style="${fieldStyle}"></div><div style="${wrapStyle};margin-top:10px;"><div style="${labelStyle}">Work (seconds)</div><input id="c-work" type="number" value="40" min="1" style="${fieldStyle}"></div><div style="${wrapStyle};margin-top:10px;"><div style="${labelStyle}">Rest (seconds)</div><input id="c-rest" type="number" value="20" min="0" style="${fieldStyle}"></div>` },
                        straightsets: { title:'Straight Sets', html:`<div style="${wrapStyle}"><div style="${labelStyle}">Sets</div><input id="c-sets" type="number" value="4" min="1" style="${fieldStyle}"></div><div style="${wrapStyle};margin-top:10px;"><div style="${labelStyle}">Rest Between Sets (seconds)</div><input id="c-rest" type="number" value="90" min="0" style="${fieldStyle}"></div>` },
                    };

                    const c = configs[fmt_];
                    if(!c) return;
                    title.textContent = c.title;
                    fields.innerHTML = c.html;
                    $('wft-config').style.display = 'flex';
                },
                closeConfig() {
                    $('wft-config').style.display = 'none';
                },
                startFromConfig() {
                    this.closeConfig();
                    const v = id => { const el = document.getElementById(id); return el ? parseInt(el.value) || 0 : 0; };
                    let opts = {};
                    switch (format) {
                        case 'amrap':        opts = { minutes: v('c-minutes') }; break;
                        case 'fortime':      opts = { timeCap: v('c-timecap') }; break;
                        case 'emom':         opts = { rounds: v('c-rounds') }; break;
                        case 'intervals':    opts = { rounds: v('c-rounds'), workSecs: v('c-work'), restSecs: v('c-rest') }; break;
                        case 'straightsets': opts = { sets: v('c-sets'), restSecs: v('c-rest') }; break;
                    }
                    this.open(format, opts);
                },
            };
        })();
        </script>

    @endsection
</body>
</html>

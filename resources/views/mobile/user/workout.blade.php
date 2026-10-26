@extends('mobile.layout.mobile-layout')
@section('content')
    @push('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    @endpush
    <style>
        html,body{margin:0;padding:0;height:100%;overflow:hidden;}
        .mob-content{position:fixed!important;top:56px!important;bottom:64px!important;left:0!important;right:0!important;padding:0!important;min-height:unset!important;overflow:hidden!important;}
        #wk-bg{position:absolute;inset:0;background-size:cover;background-position:center;padding:6px 5px 0 5px;box-sizing:border-box;}
        #wk-ring{position:absolute;inset:6px 5px 10px 5px;border:5px solid #4B5563;border-radius:8px;transition:border-color .3s;background:transparent;overflow:hidden;}
        #cat-tabs{position:absolute;top:11px;left:3px;right:3px;height:60px;display:flex;flex-direction:row;justify-content:center;align-items:center;gap:6px;overflow-x:auto;scrollbar-width:none;}
        #cat-tabs::-webkit-scrollbar{display:none;}
        #wk-list{position:absolute;top:81px;left:3px;right:3px;bottom:68px;overflow-x:hidden;overflow-y:auto;-webkit-overflow-scrolling:touch;padding-bottom:20px;scroll-behavior:smooth;}
        #wk-list.no-bbar{bottom:0;}
        #wk-list.orm-mode{bottom:0;padding-bottom:16px;overflow-y:auto;}
        #bbar{position:absolute;bottom:0;left:0;right:0;height:68px;flex-direction:row;align-items:stretch;background:#111;border-top:1px solid rgba(255,255,255,.15);padding:8px 6px;gap:6px;z-index:100;}
        @keyframes spin{100%{transform:rotate(360deg);}}
        @keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
        @keyframes popIn{from{transform:scale(0.85);opacity:0;}to{transform:scale(1);opacity:1;}}
        @keyframes bannerIn{from{transform:scale(0.7);opacity:0;}to{transform:scale(1);opacity:1;}}
        @keyframes pulseGlow{0%,100%{box-shadow:0 0 0 0 rgba(242,153,74,.4);}50%{box-shadow:0 0 0 12px rgba(242,153,74,0);}}
        @keyframes countdownPulse{0%{transform:scale(1);opacity:1;}50%{transform:scale(1.15);opacity:.8;}100%{transform:scale(1);opacity:1;}}
        input[type="number"]::-webkit-inner-spin-button,input[type="number"]::-webkit-outer-spin-button{-webkit-appearance:none;margin:0;}
        input[type="number"]{-moz-appearance:textfield;}
        .cscroll::-webkit-scrollbar{display:none;}.cscroll{-ms-overflow-style:none;scrollbar-width:none;-webkit-overflow-scrolling:touch;}
        /* Cards */
        .wk-card{background:transparent;border-radius:0;padding:10px 8px;margin-bottom:10px;border:none;position:relative;overflow:hidden;cursor:pointer;}
        .wk-card.selected{border-color:transparent;background:transparent;}
        .wk-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;}
        .wk-fmt{color:#fff;font-size:14px;font-weight:bold;letter-spacing:.3px;}
        .wk-pbadge{background:rgba(255,255,255,.15);border-radius:10px;padding:2px 8px;color:#fff;font-size:11px;font-weight:600;}
        .wk-rhdr{color:#F2994A;font-size:12px;font-weight:bold;width:28px;min-width:28px;text-align:center;display:inline-block;}
        /* Exercise row */
        .ex-row{display:flex;align-items:center;padding:0;background:transparent;border-radius:0;margin-bottom:0;gap:6px;}
        .active-ex-row{background:rgba(255,255,255,0.15);border-radius:6px;border:1px solid rgba(255,255,255,0.3);margin:0 -8px;padding:6px 8px;}
        .ex-name{flex:2;color:#fff;font-size:12px!important;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;padding-left:8px;}
        .wbox{background:#fff;border-radius:4px;padding:2px 5px;color:#000;font-size:12px!important;min-width:50px;display:flex;align-items:center;justify-content:center;flex:1;text-align:center;white-space:nowrap;margin:0;font-weight:600;}
        .rbox{background:#fff;border-radius:4px;padding:2px 4px;color:#000;font-size:12px!important;width:28px;text-align:center;font-weight:700;}
        .rbtn-wrap{display:flex;align-items:center;flex:1;justify-content:flex-end;padding-right:8px;}
        .rbtn{background:transparent;border:none;color:#fff;font-size:14px!important;font-weight:800;cursor:pointer;padding:1px 5px;border-radius:4px;line-height:1;}
        .rbtn:hover{background:rgba(255,255,255,.1);}
        .badge-done{color:#22c55e;font-size:10px;font-weight:800;letter-spacing:1px;margin-left:4px;}
        .badge-fail{color:#ef4444;font-size:10px;font-weight:800;letter-spacing:1px;margin-left:4px;}
        /* Set groups */
        .set-num{color:#fff;font-size:11px;font-weight:700;width:20px;text-align:center;flex-shrink:0;}
        .set-grp{margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid rgba(255,255,255,0.15);}
        /* Inline timer block */
        .card-timer{background:rgba(0,0,0,.3);border-radius:10px;padding:10px 14px;display:flex;flex-direction:column;align-items:center;margin-bottom:10px;}
        .ct-time{color:#fff;font-size:32px;font-weight:900;font-variant-numeric:tabular-nums;letter-spacing:-1px;line-height:1;}
        .ct-label{color:rgba(255,255,255,.45);font-size:9px;font-weight:700;letter-spacing:2px;margin-top:4px;}
        .ct-phase{font-size:10px;font-weight:800;letter-spacing:1.5px;margin-top:4px;text-transform:uppercase;}
        .ct-btns{display:flex;gap:8px;margin-top:10px;width:100%;}
        .ctbtn{flex:1;padding:7px 0;border-radius:8px;border:1px solid rgba(255,255,255,.3);background:rgba(255,255,255,.08);color:#fff;font-size:11px;font-weight:800;cursor:pointer;letter-spacing:.5px;text-align:center;}
        .ctbtn:hover{background:rgba(255,255,255,.15);}
        .ctbtn.primary{background:#fff;color:#000;border-color:#fff;}
        .ctbtn.danger{background:rgba(239,68,68,.15);border-color:#ef4444;color:#ef4444;}
        /* Station block */
        .stn-block{background:rgba(255,255,255,.04);border-radius:10px;padding:8px 10px;margin-bottom:8px;}
        .stn-lbl{color:rgba(255,255,255,.4);font-size:9px;font-weight:700;letter-spacing:1px;margin-bottom:6px;text-transform:uppercase;}
        /* Group Rows */
        .grp-row{display:flex;flex-direction:row;align-items:stretch;margin-bottom:10px;position:relative;}
        .grp-num{width:24px;display:flex;align-items:flex-start;justify-content:center;padding-top:3px;color:#fff;font-weight:800;font-size:12px;}
        .grp-exs{flex:1;display:flex;flex-direction:column;}
        /* Interval styles - full width dividers are now handled inline between groups */
        /* Pyramid */
        .pyr-row-wrap{width:100%;padding:4px 0;margin-bottom:2px;cursor:pointer;transition:background .15s;}
        /* Rest timer strip */
        .rest-strip{background:rgba(0,0,0,.4);border-radius:8px;padding:6px 10px;display:flex;align-items:center;justify-content:space-between;margin-top:8px;}
        /* Complete banner */
        .wk-banner{position:absolute;inset:0;z-index:10;display:flex;flex-direction:column;align-items:center;justify-content:center;background:rgba(0,0,0,0.6);border:1.5px solid rgba(242,153,74,0.3);border-radius:0;pointer-events:none;animation:bannerIn .35s cubic-bezier(.34,1.56,.64,1) forwards;}
        .wk-banner-circle{width:60px;height:60px;border-radius:50%;background:#F2994A;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(242,153,74,.4);margin-bottom:10px;}
        .wk-banner-txt{color:#ffffff;font-size:22px;font-weight:800;letter-spacing:.8px;text-shadow:0 2px 6px rgba(0,0,0,.8);text-transform:lowercase;font-family:inherit;margin:0;line-height:1.2;text-align:center;}
        /* Incomplete banner */
        .fail-banner{position:absolute;inset:0;z-index:10;background:rgba(0,0,0,.7);border-radius:0;display:flex;flex-direction:column;align-items:center;justify-content:center;}
        .fail-circle{width:56px;height:56px;border-radius:50%;background:#ef4444;display:flex;align-items:center;justify-content:center;margin-bottom:10px;font-size:22px;color:#fff;font-weight:900;}
        /* Countdown overlay in-card */
        .cd-overlay{position:absolute;inset:0;z-index:60;background:rgba(0,0,0,.85);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-direction:column;cursor:pointer;}
        .wk-card.countdown-active{min-height:230px !important;transition:min-height 0.25s ease;}
        .cd-num{color:#fff;font-size:64px;font-weight:900;line-height:1;animation:countdownPulse .8s ease-in-out;}
        /* Notes modal */
        .nmod-overlay{position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.6);display:flex;align-items:center;justify-content:center;animation:fadeIn .15s;}
        .nmod-box{background:#111;border-radius:16px;padding:20px;width:290px;border:1px solid rgba(255,255,255,.12);animation:popIn .2s;}
        .nmod-title{color:#fff;font-size:16px;font-weight:700;margin-bottom:14px;text-align:center;}
        .nmod-input{width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.2);border-radius:10px;color:#fff;font-size:14px;padding:10px;min-height:90px;resize:none;font-family:inherit;box-sizing:border-box;}
        .nmod-input::placeholder{color:rgba(255,255,255,.3);}
        .nmod-actions{display:flex;gap:10px;margin-top:14px;}
        .nmod-cancel{flex:1;padding:10px;background:transparent;border:1px solid rgba(255,255,255,.2);border-radius:8px;color:rgba(255,255,255,.6);font-size:13px;font-weight:700;cursor:pointer;}
        .nmod-save{flex:2;padding:10px;background:#fff;border:none;border-radius:8px;color:#000;font-size:13px;font-weight:800;cursor:pointer;}
        /* App alert - Android Material Alert style matching mobile app */
        .app-msg-overlay{position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.6);display:flex;align-items:center;justify-content:center;animation:fadeIn .15s;}
        .app-msg-box{background:#ffffff;border-radius:4px;width:86%;max-width:320px;padding:24px 24px 16px 24px;box-shadow:0 12px 32px rgba(0,0,0,.45);animation:popIn .2s;box-sizing:border-box;}
        .app-msg-content{text-align:left;}
        .app-msg-title{color:#1f2937;font-size:18px;font-weight:600;line-height:1.35;text-align:left;}
        .app-msg-body{color:#4b5563;font-size:14px;line-height:1.4;margin-top:10px;text-align:left;}
        .app-msg-footer{display:flex;justify-content:flex-end;margin-top:24px;border:none;}
        .app-msg-ok{background:transparent;border:none;color:#1a73e8;font-size:14px;font-weight:700;cursor:pointer;padding:6px 12px;border-radius:4px;letter-spacing:0.5px;text-transform:uppercase;}
        .app-msg-ok:active{background:rgba(26,115,232,0.1);}
        /* Phase colors */
        .phase-w{color:#22c55e;}.phase-r{color:#ef4444;}.phase-idle{color:rgba(255,255,255,.5);}
        /* Stats container for completed Straight Sets */
        .wk-stats-container{display:flex;flex-direction:row;margin-top:10px;margin-bottom:20px;gap:12px;justify-content:center;width:100%;box-sizing:border-box;}
        .wk-stat-box{background-color:rgba(242,153,74,0.15);border:1px solid rgba(242,153,74,0.5);border-radius:12px;padding:12px 8px;display:flex;flex-direction:column;align-items:center;justify-content:center;flex:1;max-width:110px;box-sizing:border-box;}
        .wk-stat-label{color:rgba(255,255,255,0.8);font-size:12px;font-weight:600;margin-bottom:6px;text-align:center;}
        .wk-stat-value{color:#fff;font-size:18px;font-weight:bold;text-align:center;}
    </style>

    <div id="wk-bg" style="background-image:url('{{ asset('img/valhalla-bg.jpg') }}');">
        <div id="wk-ring">
            {{-- Category tabs --}}
            <div id="cat-tabs"></div>

            {{-- Loader --}}
            <div id="loader" style="display:none;text-align:center;padding:40px 0;color:#fff;">
                <svg style="animation:spin 1s linear infinite;height:32px;width:32px;margin:0 auto;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <p style="margin-top:16px;font-weight:700;letter-spacing:2px;font-size:12px;">LOADING WORKOUTS...</p>
            </div>

            {{-- Workouts list --}}
            <div id="wk-list" class="cscroll"></div>

            {{-- Bottom action bar --}}
            <div id="bbar" style="display:none;">
                <button id="btn-left" onclick="handleLeft()" style="flex:1.1;display:flex;align-items:center;justify-content:center;padding:4px;border-radius:8px;border:1px solid rgba(255,255,255,.45);background:#1a1a1a;color:#fff;font-weight:700;font-size:11px;cursor:pointer;letter-spacing:.3px;min-width:0;text-align:center;line-height:1.2;">COMPLETE<br>ROUND</button>
                <button id="btn-center" onclick="handleCenter()" style="flex:1.5;display:flex;align-items:center;justify-content:center;border-radius:8px;border:1px solid #CD7F32;background:#CD7F32;color:#fff;font-weight:800;font-size:13px;cursor:pointer;padding:4px;letter-spacing:.5px;text-align:center;">START</button>
                <button id="btn-right" onclick="handleRight()" style="flex:1.1;display:flex;align-items:center;justify-content:center;padding:4px;border-radius:8px;border:1px solid rgba(255,255,255,.45);background:#1a1a1a;color:#fff;font-weight:700;font-size:11px;cursor:pointer;letter-spacing:.3px;min-width:0;text-align:center;line-height:1.2;">COMPLETE<br>WORKOUT</button>
            </div>
        </div>
    </div>

    {{-- Notes modal --}}
    <div id="nmod" class="nmod-overlay" style="display:none;">
        <div class="nmod-box">
            <div class="nmod-title">Workout Notes</div>
            <textarea id="nmod-input" class="nmod-input" placeholder="Enter feedback or notes..."></textarea>
            <div class="nmod-actions">
                <button class="nmod-cancel" onclick="closeNotes()">Cancel</button>
                <button class="nmod-save" onclick="confirmComplete()">Save &amp; Complete</button>
            </div>
        </div>
    </div>

    <script>
    // ── BLADE VARS ─────────────────────────────────────────────────
    const DAY   = @json($dayWithDate);
    const CLSID = @json($classId);
    const MEMID = @json($memberId);
    const CSRF  = document.querySelector('meta[name="csrf-token"]').content;

    const TABS = [
      {id:'warmup',  iw:'{{ asset("icon/warmupwhite.png") }}',  ib:'{{ asset("icon/warmup.png") }}'},
      {id:'strength',iw:'{{ asset("icon/strengthwhite.png") }}', ib:'{{ asset("icon/strength.png") }}'},
      {id:'weightlifting',iw:'{{ asset("icon/weightliftingWhite.png") }}',ib:'{{ asset("icon/weightlifting.png") }}',sc:1.3},
      {id:'conditioning',iw:'{{ asset("icon/conditioningWhite.png") }}',ib:'{{ asset("icon/conditioning.png") }}'},
      {id:'accessory',iw:'{{ asset("icon/accessoryWhite.png") }}',ib:'{{ asset("icon/accessory.png") }}'},
      {id:'1rm',iw:null,ib:null},
    ];

    // ── STATE ──────────────────────────────────────────────────────
    let tab='warmup', wData={}, selId=null, wkStatus='ready', notes='';
    const TS={};          // timerState per workout id
    const RST={};         // rest timer state (straightsets/pyramid keys)
    const CT={};          // completedToggles
    const pyrWarningVisible = {};
    let ormEx=[],ormRec=[],ormLoaded=false,ormSelId=null,ormSelName='',ormWt='';

    // ── INIT ───────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded',()=>{ 
      buildTabs(); 
      fetchWk(); 
      checkSavedSuccess();
    });

    // Auto-refresh workouts when user switches back to this tab (matches mobile app useFocusEffect)
    window.addEventListener('focus', () => {
      if(tab !== '1rm' && wkStatus !== 'running') {
        fetchWk(true);
      }
    });
    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'visible' && tab !== '1rm' && wkStatus !== 'running') {
        fetchWk(true);
      }
    });

    function checkSavedSuccess(){
      const urlParams = new URLSearchParams(window.location.search);
      const isSaved = sessionStorage.getItem('show_saved_success') === '1' || urlParams.get('saved') === '1' || @json(session('success') ? true : false);
      if(isSaved) {
        sessionStorage.removeItem('show_saved_success');
        if(urlParams.has('saved')) {
          urlParams.delete('saved');
          const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
          window.history.replaceState({}, document.title, newUrl);
        }
        showMsg('Saved Successfully!');
      }
    }

    // ── TABS ───────────────────────────────────────────────────────
    function buildTabs(){
      const c=document.getElementById('cat-tabs'); c.innerHTML='';
      TABS.forEach(t=>{
        const act=t.id===tab;
        const btn=document.createElement('button');
        btn.onclick=()=>setTab(t.id);
        Object.assign(btn.style,{
          flex:'1',
          aspectRatio:'1',
          maxWidth:'52px',
          maxHeight:'52px',
          minWidth:'38px',
          borderRadius:'11px',
          border:'2px solid #fff',
          display:'flex',
          justifyContent:'center',
          alignItems:'center',
          cursor:'pointer',
          transition:'all .2s',
          padding:'0',
          backgroundColor:act?'#fff':'rgba(0,0,0,0.7)',
          boxSizing:'border-box'
        });
        if(t.id==='1rm'){
          btn.innerHTML=`<span style="color:${act?'#000':'#fff'};font-weight:900;font-size:14px;letter-spacing:0.3px;">1RM</span>`;
        } else {
          const sc=t.sc?`scale(${t.sc})`:'scale(1)';
          btn.innerHTML=`<img src="${act?t.ib:t.iw}" style="width:30px;height:30px;object-fit:contain;transform:${sc};display:block;pointer-events:none;" loading="lazy">`;
        }
        c.appendChild(btn);
      });
    }

    function setTab(tid){
      pauseAll(tab);
      tab=tid; selId=null; wkStatus='ready';
      const cont=document.getElementById('wk-list');
      if(cont) {
        if(tid==='1rm') { cont.classList.add('orm-mode'); cont.classList.add('no-bbar'); }
        else { cont.classList.remove('orm-mode'); }
      }
      buildTabs();
      if(tid==='1rm'){render1RM();}else{renderList(); fetchWk(true);}
    }

    // ── FETCH ──────────────────────────────────────────────────────
    async function fetchWk(isBackground = false){
      if(!isBackground) {
        document.getElementById('loader').style.display='block';
        document.getElementById('wk-list').innerHTML='';
      }
      try{
        const r=await fetch('/mobile/get-workout-list',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({date:DAY,class_id:CLSID})});
        const d=await r.json();
        if(d.status&&d.workouts){
          const w=d.workouts;
          wData={
            warmup:w.Warmup||w.warmup||[],
            strength:w.Strength||w.strength||[],
            weightlifting:w.Weightlifting||w.weightlifting||[],
            conditioning:w.Conditioning||w.conditioning||[],
            accessory:w.Accessory||w.accessory||[],
            test:d.test||[],
            workoutlibrary:d.workoutlibrary||[],
            categoryOptions:d.categoryOptions||[],
            member:d.member||null,
            score:d.score||null
          };
          window.wData = wData;
        } else {
          wData={warmup:[],strength:[],weightlifting:[],conditioning:[],accessory:[],test:[],workoutlibrary:[],categoryOptions:[],member:null,score:null};
          window.wData = wData;
        }
      }catch(e){console.error(e);}
      if(!isBackground) document.getElementById('loader').style.display='none';
      if(tab!=='1rm' && wkStatus!=='running'){
        renderList();
      }
    }

    // ── RENDER LIST ────────────────────────────────────────────────
    function renderList(preserveScroll = false){
      const cont=document.getElementById('wk-list');
      const bbar=document.getElementById('bbar');
      const prevScrollTop = cont ? cont.scrollTop : 0;
      cont.innerHTML='';
      bbar.style.display='none';
      cont.classList.add('no-bbar');
      const list=wData[tab]||[];
      if(!list.length){cont.innerHTML='<div style="display:flex;align-items:center;justify-content:center;margin-top:80px;"><div style="color:#fff;font-size:14px;font-weight:600;letter-spacing:1px;">No Workouts.</div></div>';return;}
      bbar.style.display='flex';
      cont.classList.remove('no-bbar');
      if(!selId&&list.length)selId=list[0].id;
      list.forEach((w,i)=>{cont.insertAdjacentHTML('beforeend',cardFor(w,i));});
      list.forEach(w=>{
        const el=document.getElementById('wk-'+w.id);
        if(el){
          el.onclick=(e)=>{
            if(e.target.closest('button, input, select, textarea')) return;
            selWk(w.id);
          };
        }
        const statsEl=document.getElementById('stats-'+w.id);
        if(statsEl){
          statsEl.onclick=(e)=>{
            if(e.target.closest('button, input, select, textarea')) return;
            selWk(w.id);
          };
        }
      });
      updBbar(); updSel();
      if(preserveScroll && prevScrollTop > 0){
        cont.scrollTop = prevScrollTop;
      } else if(selId && selId !== list[0].id){
        setTimeout(() => scrollToWk(selId, false), 50);
      }
    }

    // ── FORMAT KEY ─────────────────────────────────────────────────
    function fmtKey(w){
      if(!w) return 'unknown';
      if(w.format&&w.format.slug) return w.format.slug;
      if(w.format&&w.format.name) return w.format.name.toLowerCase().replace(/\s+/g,'-');
      if(w.workout_format_type) return w.workout_format_type;
      return 'unknown';
    }

    // ── FORMAT EXERCISES HELPER ────────────────────────────────────
    function getFormatExercises(w){
      if(!w) return [];
      const fk = fmtKey(w);
      let list = [];
      if(fk === 'rounds') list = w.rounds;
      else if(fk === 'amrap') list = w.amraps || w.amrap;
      else if(fk === 'emom') list = w.emoms || w.emom;
      else if(fk === 'for-time') list = w.for_times || w.forTimes || w.for_time;
      else if(fk === 'intervals') list = w.intervals || w.interval;
      else if(fk === 'circuit') list = w.circuits || w.circuit;
      else if(fk === 'pyramid') list = w.pyramids || w.pyramid;
      else if(fk === 'straight-sets') list = w.straights || w.straight;

      if(Array.isArray(list) && list.length > 0) return list;

      const candidates = [w.rounds, w.amraps, w.emoms, w.for_times, w.forTimes, w.intervals, w.circuits, w.pyramids, w.pyramid, w.straights];
      for(const arr of candidates){
        if(Array.isArray(arr) && arr.length > 0) return arr;
      }
      return Array.isArray(list) ? list : [];
    }

    // ── HELPERS ────────────────────────────────────────────────────
    function exName(ex){return ex?.workout_library?.workout||ex?.workoutLibrary?.workout||ex?.exercise_name||ex?.workout_name||'Exercise';}
    function gender(){const m=wData.member;if(!m)return null;const g=String(m.gender||'').toLowerCase();if(g.startsWith('f')||g==='2')return'female';if(g.startsWith('m')||g==='1')return'male';return null;}
    function fmt(s){const m=Math.floor(Math.abs(s)/60);const sec=Math.abs(s)%60;return`${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`;}
    function parseSec(raw){
      if(!raw)return 0;
      if(typeof raw==='number')return raw>0?Math.floor(raw):0;
      const str=String(raw).trim();
      if(!str||str==='00:00:00')return 0;
      const clean=str.replace(/[a-zA-Z]/g,'').trim();
      const p=clean.split(':').map(Number);
      if(p.length===3){
        if(p[0]===0&&p[1]===0) return p[2];
        if(p[0]===0&&p[1]>=10&&p[2]===0) return p[1];
        return p[0]*3600+p[1]*60+p[2];
      }
      if(p.length===2)return p[0]*60+p[1];
      if(p.length===1&&!isNaN(p[0])&&p[0]>0)return p[0];
      return 0;
    }
    function getIntStationTimes(w, roundNum){
      const exs = w?.intervals || [];
      const iMap = {};
      exs.forEach(ex => {
        const n = ex.stationumber ?? ex.station ?? ex.number ?? 1;
        if(!iMap[n]) iMap[n] = [];
        iMap[n].push(ex);
      });
      const iNums = Object.keys(iMap).sort((a,b) => a - b);
      const totalStations = iNums.length || 1;
      const idx = Math.max(0, Math.min((roundNum || 1) - 1, totalStations - 1));
      const stnNum = iNums[idx];
      const stnExs = (stnNum !== undefined && iMap[stnNum]) ? iMap[stnNum] : exs;
      const ex = stnExs[0] || exs[0] || {};
      
      const rawWork = ex.work_time ?? ex.work ?? ex.worktime ?? ex.work_duration ?? ex.duration ?? ex.time ?? ex.interval_time ?? ex.work_seconds ?? w?.work_time ?? w?.work;
      const rawRest = ex.rest_time ?? ex.rest ?? ex.resttime ?? ex.rest_duration ?? ex.rest_seconds ?? w?.rest_time ?? w?.rest;
      
      const workSecs = parseSec(rawWork) || 40;
      const restSecs = parseSec(rawRest) || 20;
      
      return { workSecs, restSecs, totalStations, iNums, iMap };
    }
    function getAmrapSec(w){
      if(!w) return 0;
      const raw = w.time_to_complete || (w.amraps && w.amraps[0] && w.amraps[0].time_to_complete) || w.work || (w.amraps && w.amraps[0] && w.amraps[0].work);
      if(raw){
        const s = parseSec(raw);
        if(s > 0) return s;
      }
      const num = Number(w.number ?? 0);
      return num > 0 ? num : 0;
    }
    function getReadinessPenalty(scoreData) {
      if (scoreData && scoreData.score !== undefined && scoreData.score !== null) {
        const readiness = Number(scoreData.score);
        if (!isNaN(readiness) && readiness < 96) {
          const drops = Math.ceil((96 - readiness) / 5);
          return Math.min(drops, 10) / 100;
        }
      }
      return 0;
    }
    function dispWt(load,unit,testWeight=0,missedReps=0){
      const u=(unit||'%').trim().toUpperCase();
      if(u==='BW'||u==='N/A'||u==='NA')return u;
      if(load===undefined||load===null||load==='')return'---';
      const numLoad=Number(load);
      if(isNaN(numLoad))return'---';
      if(numLoad===0&&u==='%')return'---';
      const rootData = (typeof window !== 'undefined' && window.wData) ? window.wData : (typeof wData !== 'undefined' ? wData : null);
      const g=rootData&&rootData.member?(String(rootData.member.gender||'').toLowerCase().startsWith('f')||rootData.member.gender=='2'?'female':'male'):null;
      const sd=rootData&&rootData.score?rootData.score:null;
      if(u==='%'){
        const pen=getReadinessPenalty(sd)+(missedReps*0.03);
        const eff=Math.max(0,(numLoad/100)-pen);
        return Math.round((testWeight||0)*eff)+" KG";
      }
      let fw=numLoad;
      if(u==='KG'&&g==='female')fw=numLoad*0.75;
      if(missedReps > 0 && (u==='KG'||u==='LBS')){
        fw = Math.max(0, fw * (1 - (missedReps * 0.03)));
      }
      return Math.round(fw)+" "+u;
    }
    function calcWt(load,unit,testWeight=0,missedReps=0){const s=dispWt(load,unit,testWeight,missedReps);const m=String(s).match(/[\d.]+/);return m?Math.round(parseFloat(m[0])):0;}

    // ── CARD FACTORY ───────────────────────────────────────────────
    function cardFor(w,i){
      const fk=fmtKey(w);
      switch(fk){
        case'straight-sets':return cardSS(w);
        case'rounds':       return cardRounds(w);
        case'amrap':        return cardAmrap(w);
        case'for-time':     return cardFT(w);
        case'emom':         return cardEmom(w);
        case'intervals':    return cardIntervals(w);
        case'circuit':      return cardCircuit(w);
        case'pyramid':      return cardPyramid(w);
        default:            return cardGeneric(w,fk);
      }
    }
</script>

    {{-- ============================================================ --}}
    {{-- MODULAR FORMATS & TIMERS ARCHITECTURE                        --}}
    {{-- ============================================================ --}}
    {{-- Formats & Common Card Builders --}}
    @include('mobile.formats.common-styles')
    @include('mobile.formats.straight-sets')
    @include('mobile.formats.pyramid')
    @include('mobile.formats.rounds')
    @include('mobile.formats.amrap')
    @include('mobile.formats.for-time')
    @include('mobile.formats.emom')
    @include('mobile.formats.intervals')
    @include('mobile.formats.circuit')
    @include('mobile.formats.generic')

    {{-- Timers & Engine Partial Modules --}}
    @include('mobile.timers.timer-beeps')
    @include('mobile.timers.countdown-overlay')
    @include('mobile.timers.rest-timer')
    @include('mobile.timers.rounds-timer')
    @include('mobile.timers.amrap-timer')
    @include('mobile.timers.for-time-timer')
    @include('mobile.timers.emom-timer')
    @include('mobile.timers.interval-timer')
    @include('mobile.timers.circuit-timer')
    @include('mobile.timers.timer-renderer')

<script>
    // ── SCROLL TO WORKOUT ──────────────────────────────────────────
    function scrollToWk(id, smooth = true){
      const cont = document.getElementById('wk-list');
      const el = document.getElementById('wk-' + id);
      if(!cont || !el) return;
      const contRect = cont.getBoundingClientRect();
      const elRect = el.getBoundingClientRect();
      const targetTop = cont.scrollTop + (elRect.top - contRect.top) - 4;
      cont.scrollTo({
        top: Math.max(0, targetTop),
        behavior: smooth ? 'smooth' : 'auto'
      });
    }

    // ── SELECTION ──────────────────────────────────────────────────
    function selWk(id){
      if(selId === id){
        scrollToWk(id, true);
        return;
      }
      selId = id;
      wkStatus = 'ready';
      updSel();
      updBbar();
      scrollToWk(id, true);
    }
    function updSel(){
      (wData[tab]||[]).forEach(w=>{
        const el=document.getElementById('wk-'+w.id);if(!el)return;
        const statsEl=document.getElementById('stats-'+w.id);
        if(w.id===selId){
          el.classList.add('selected');
          ['rounds-tmr','ft-tmr','int-tmr'].forEach(p=>{const tb=document.getElementById(p+'-'+w.id);if(tb)tb.style.display='';});
          if(statsEl) statsEl.style.display='';
        } else {
          el.classList.remove('selected');
          ['rounds-tmr','ft-tmr','int-tmr'].forEach(p=>{const tb=document.getElementById(p+'-'+w.id);if(tb)tb.style.display='none';});
          if(statsEl) statsEl.style.display='none';
        }
      });
    }

    // ── BOTTOM BAR ─────────────────────────────────────────────────
    function updBbar(){
      const list=wData[tab]||[];
      const bbar=document.getElementById('bbar');if(!bbar)return;
      const wkList=document.getElementById('wk-list');
      if(!list.length||tab==='1rm'){bbar.style.display='none';if(wkList)wkList.classList.add('no-bbar');return;}
      bbar.style.display='flex';
      if(wkList)wkList.classList.remove('no-bbar');
      const w=list.find(x=>x.id==selId);if(!w)return;
      const fk=fmtKey(w);
      const ts=TS[w.id]||{};
      const bl=document.getElementById('btn-left');
      const bc=document.getElementById('btn-center');
      const br=document.getElementById('btn-right');
      // Sync completion status with workout data
      if(fk === 'circuit'){
        const exs = w.circuits || [];
        const sMap = {};
        exs.forEach(ex => {
          const sn = ex.stationumber ?? ex.station_number ?? ex.station ?? 1;
          if(!sMap[sn]) sMap[sn] = [];
          sMap[sn].push(ex);
        });
        const sns = Object.keys(sMap);
        const compStations = sns.filter(sn => {
          return w.type_completed == 1 || w.status === 'completed' ||
            sMap[sn].every(ex => ex.is_completed == 1 || !!CT[`${w.id}-stn-${sn}`] || !!CT[`${w.id}-${ex.id}`]);
        }).length;
        const isCircuitDone = (sns.length > 0 && compStations >= sns.length) ||
                              (exs.length > 0 && exs.every(ex => ex.is_completed == 1 || !!CT[`${w.id}-${ex.id}`])) ||
                              ts.allDone || w.type_completed == 1 || w.status === 'completed';
        if(isCircuitDone){
          w.type_completed = 1;
          w.status = 'completed';
          ts.allDone = true;
          wkStatus = 'completed';
        }
      } else if(fk==='pyramid'){
        const rows = w.pyramids || w.pyramid || [];
        const doneC = rows.filter(r => !!CT[`${w.id}-${r.id}`] || r.is_completed == 1).length;
        const isPyrDone = (rows.length > 0 && doneC >= rows.length) || ts.allDone || w.type_completed == 1 || w.status === 'completed';
        if(isPyrDone){
          w.type_completed = 1;
          w.status = 'completed';
          ts.allDone = true;
          wkStatus = 'completed';
        }
      } else if(w.type_completed==1 || w.status==='completed' || ts.allDone || ts.phase==='done'){
        wkStatus='completed';
      }
      // Left
      const roundFmts=['rounds','amrap','circuit','intervals','emom','pyramid','straight-sets'];
      if(roundFmts.includes(fk)){
        const lbl={'straight-sets':'COMPLETE<br>SET','intervals':'COMPLETE<br>INTERVAL'};
        bl.innerHTML=lbl[fk]||'COMPLETE<br>ROUND';
        bl.style.display='flex';
        if(wkStatus==='completed'){
          bl.disabled=true;
          bl.style.opacity='0.35';
          bl.style.cursor='default';
        } else {
          bl.disabled=false;
          bl.style.opacity='1';
          bl.style.cursor='pointer';
        }
      } else {
        bl.style.display='none';
      }
      // Center (timer display or START)
      updCenterBtn(w,fk,ts);
      // Right
      const hasStarted = ts.running || ts.paused || (ts.elapsed > 0) || Object.keys(RST).some(k=>k.startsWith(`rest-${w.id}-`)||k.startsWith(`pyr-${w.id}-`));
      if(wkStatus==='completed'){br.innerHTML='COMPLETED';br.style.borderColor='rgba(255,255,255,.3)';br.style.color='rgba(255,255,255,.4)';br.disabled=true;}
      else if(wkStatus==='incompleted'){br.innerHTML='INCOMPLETE';br.style.borderColor='#fca5a5';br.style.color='#fca5a5';br.disabled=true;}
      else{br.innerHTML=hasStarted?'COMPLETE<br>EARLY':'COMPLETE<br>WORKOUT';br.style.borderColor='rgba(255,255,255,.45)';br.style.color='#fff';br.disabled=false;}
      // Ring border color — grey by default (matching mobile app), coloured only when active timer is running
      const ring=document.getElementById('wk-ring');
      if(ring){
        if(wkStatus==='completed'||wkStatus==='incompleted'){
            ring.style.borderColor='#4B5563';
        } else{
          // For straight-sets and circuit: grey ring (matching mobile app). Color only for specific timer-based formats when running.
          const timerFmts=['rounds','amrap','for-time','emom','intervals'];
          const isTimerRunning=ts.running||ts.paused;
          if(fk==='straight-sets'){
            // Check if any rest timer is active
            const activeKey=Object.keys(RST).find(k=>k.startsWith(`rest-${w.id}-`)&&RST[k]?.running);
            ring.style.borderColor=activeKey?RST[activeKey].color:'#4B5563';
          } else if(fk==='circuit'){
            // Circuit format matches mobile app: border is always #4B5563 (grey), never orange
            ring.style.borderColor='#4B5563';
          } else if(timerFmts.includes(fk)&&isTimerRunning){
            const intColor = ts.phase === 'work' ? '#22c55e' : (ts.phase === 'rest' ? '#ef4444' : '#22c55e');
            const fc={rounds:'#3b82f6',amrap:'#F2994A','for-time':'#8b5cf6',emom:'#22c55e',intervals:intColor};
            ring.style.borderColor=fc[fk]||'#4B5563';
          } else if(fk==='pyramid'){
            const hasPyrRest=Object.keys(RST).find(k=>k.startsWith(`pyr-${w.id}-`)&&RST[k]?.running);
            ring.style.borderColor=hasPyrRest?(RST[hasPyrRest].color||'#CD7F32'):'#4B5563';
          } else{
            ring.style.borderColor='#4B5563';
          }
        }
      }
    }


    // ── BUTTON HANDLERS ────────────────────────────────────────────
    async function handleLeft(){
      const list=wData[tab]||[];const w=list.find(x=>x.id===selId);if(!w)return;
      const fk=fmtKey(w);const ts=TS[w.id]||{};
      if(fk==='rounds'){
          if(hasRoundsTimer(w, ts) && !ts.running && !ts.allDone) { showMsg('Notice', "Please click 'START' to begin your workout."); return; }
          if(ts.limit>0 && ts.elapsed>=ts.limit && ts.completedRounds<w.number) { showMsg('Notice', "Workout incomplete."); return; }
          ts.completedRounds=(ts.completedRounds||0)+1;
          const el=document.getElementById('rounds-prog-'+w.id);
          if(el)el.textContent=`${ts.completedRounds}${w.number>0?'/'+w.number:''}`;
          const pbar=document.getElementById('rounds-pbar-'+w.id);
          if(pbar)pbar.style.width=`${w.number>0?(ts.completedRounds/w.number)*100:0}%`;
          const rProgress = `${ts.completedRounds}/${w.number||1}`;
          const isFinal = w.number > 0 && ts.completedRounds >= w.number;
          if(isFinal) {
              ts.allDone = true;
              w.type_completed = 1;
              w.status = 'completed';
              (w.rounds || []).forEach(ex => { ex.is_completed = 1; ex.round_number = rProgress; });
          }
          const savedTime = (ts.limit > 0) ? fmt(Math.max(0, ts.limit - (ts.elapsed || 0))) : fmt(ts.elapsed || 0);
          saveCondWk(w, null, null, rProgress, savedTime, null);
          if(isFinal){ finishRounds(w.id,true); }
      }
      else if(fk==='amrap'){
          ts.rounds=(ts.rounds||0)+1;
          const el=document.getElementById('amrap-rounds-'+w.id);
          if(el)el.textContent=`${ts.rounds}`;
          const rProgress = `${ts.rounds}/${ts.totalRounds||1}`;
          const left = Math.max(0, (ts.limit||0) - (ts.elapsed||0));
          const timeStr = fmt(left);
          saveCondWk(w, null, null, rProgress, timeStr, null);
      }
      else if(fk==='intervals')skipInt(w.id);
      else if(fk==='circuit'){
        if(ts.allDone || w.type_completed==1 || w.status==='completed' || wkStatus==='completed') return;
        const exs = w.circuits || [];
        const sMap = {};
        exs.forEach(ex => {
          const sn = ex.stationumber ?? ex.station_number ?? ex.station ?? 1;
          if (!sMap[sn]) sMap[sn] = [];
          sMap[sn].push(ex);
        });
        const sns = Object.keys(sMap).sort((a,b) => a - b);
        
        // Find the first uncompleted station
        const uncompletedStation = sns.find(sn => {
          return !(w.type_completed == 1 || sMap[sn].every(ex => 
            ex.is_completed == 1 || !!CT[`${w.id}-stn-${sn}`] || !!CT[`${w.id}-${ex.id}`]
          ));
        });

        if (uncompletedStation !== undefined) {
          const timeStr = ts.elapsed > 0 ? fmt(ts.elapsed) : '00:00';
          CT[`${w.id}-stn-${uncompletedStation}`] = true;
          (sMap[uncompletedStation] || []).forEach(ex => {
            CT[`${w.id}-${ex.id}`] = true;
            ex.is_completed = 1;
            ex.exercise_time = timeStr;
          });
          ts.timeTaken = timeStr;
          try {
            await saveCircuitStation(w, uncompletedStation, timeStr);
          } catch(err) {
            console.error('[Circuit] Station save error, continuing client completion:', err);
          }
          
          const compCount = sns.filter(sn => {
            return w.type_completed == 1 || sMap[sn].every(ex => 
              ex.is_completed == 1 || !!CT[`${w.id}-stn-${sn}`] || !!CT[`${w.id}-${ex.id}`]
            );
          }).length;
          
          ts.completedStations = compCount;
          const prg = document.getElementById('circuit-prog-' + w.id);
          if (prg) prg.textContent = `${compCount}/${Math.max(sns.length, 1)}`;
          
          if (compCount >= sns.length && sns.length > 0) {
            ts.allDone = true;
            w.type_completed = 1;
            w.status = 'completed';
            wkStatus = 'completed';
            finishCircuit(w.id, true);
          } else {
            renderList();
          }
        } else {
          ts.allDone = true;
          w.type_completed = 1;
          w.status = 'completed';
          wkStatus = 'completed';
          finishCircuit(w.id, true);
        }
      }
      else if(fk==='emom')skipEmom(w.id);
      else if(fk==='pyramid'){
        const ak=Object.keys(RST).find(k=>k.startsWith(`pyr-${w.id}-`)&&RST[k]?.running);
        if(ak){finishPyrRest(w.id,ak,true);return;}
        const rows=w.pyramids||w.pyramid||[];
        const fp=rows.find(r=>!CT[`${w.id}-${r.id}`]&&r.is_completed!=1);
        if(fp) startPyrRest(w.id,fp.id);
      }
      else if(fk==='straight-sets')completeSS(w);
    }

    function handleCenter(){
      const list=wData[tab]||[];const w=list.find(x=>x.id===selId);if(!w)return;
      scrollToWk(w.id, true);
      const ts=TS[w.id]||{};
      if(wkStatus==='completed'||w.type_completed==1||w.status==='completed'||ts.allDone||ts.phase==='done') return;
      const fk=fmtKey(w);
      if(fk==='pyramid'){
        const ak=Object.keys(RST).find(k=>k.startsWith(`pyr-${w.id}-`)&&RST[k]?.running);
        if(ak){finishPyrRest(w.id,ak,true);return;}
        const rows=w.pyramids||w.pyramid||[];
        const fp=rows.find(r=>!CT[`${w.id}-${r.id}`]&&r.is_completed!=1);
        if(fp) startPyrRest(w.id,fp.id);
      }
      else if(fk==='straight-sets'){
        const ak=Object.keys(RST).find(k=>k.startsWith(`rest-${w.id}-`)&&RST[k]?.running);
        if(ak){const si=ak.split('-')[2];doneRest(w.id,parseInt(si));return;}
        startSS(w);
      }
      else if(fk==='rounds'){
        if(!hasRoundsTimer(w, ts)) return;
        startRounds(w.id);
      }
      else if(fk==='amrap')startAmrap(w.id);
      else if(fk==='for-time')startFT(w.id);
      else if(fk==='emom')startEmom(w.id);
      else if(fk==='intervals')startInt(w.id);
      else if(fk==='circuit'){
        if(!hasCircuitTimer(w, ts)) return;
        startCircuit(w.id);
      }
    }

    function handleRight(){if(wkStatus==='incompleted'||wkStatus==='completed')return;openNotes();}

    // ── BANNER ─────────────────────────────────────────────────────
    function showBanner(wId,fmtLabel,ok,timeTaken){
      const ts=TS[wId]=TS[wId]||{};
      const list=wData[tab]||[];
      const w=list.find(x=>x.id==wId);
      if(ok){
         ts.allDone=true;
         if(timeTaken) ts.timeTaken=timeTaken;
         if(w){ w.type_completed=1; w.status='completed'; }
         const el=document.getElementById('banner-'+wId);
         if(el) el.innerHTML=bannerHtml(fmtLabel);
         const statsEl=document.getElementById('stats-'+wId);
         if(statsEl && typeof buildSSStatsHtml === 'function' && (fmtLabel === 'Straight Sets' || fmtLabel === 'straight-sets') && w){
           const straights = w.straights || w.straight || [];
           const totalSets = straights.length > 0 && straights[0].sets ? straights[0].sets.length : 0;
           statsEl.innerHTML = buildSSStatsHtml(w, straights, totalSets);
           statsEl.style.display = (selId === wId) ? '' : 'none';
         }
      }
      else{
        const el=document.getElementById('banner-'+wId);
        if(el) el.innerHTML=`<div class="fail-banner"><div class="fail-circle">!</div><div style="color:#fca5a5;font-size:18px;font-weight:800;letter-spacing:.5px;">INCOMPLETE</div></div>`;
      }
    }

    // ── PAUSE ALL ──────────────────────────────────────────────────
    function pauseAll(t){(wData[t]||[]).forEach(w=>{const ts=TS[w.id];if(ts&&ts.running){clearInterval(ts.interval);ts.interval=null;ts._autoPaused=true;}});}

    // ── NOTES MODAL ────────────────────────────────────────────────
    function openNotes(){document.getElementById('nmod-input').value=notes;document.getElementById('nmod').style.display='flex';}
    function closeNotes(){document.getElementById('nmod').style.display='none';}
    async function confirmComplete(){
      notes=document.getElementById('nmod-input').value;closeNotes();
      const list=wData[tab]||[];const w=list.find(x=>x.id===selId);if(!w)return;
      const fk=fmtKey(w);const ts=TS[w.id]||{};
      const lim = ts.limit > 0 ? ts.limit : (parseSec(w.time_to_complete || (w.rounds && w.rounds[0] && w.rounds[0].time_to_complete) || (w.circuits && w.circuits[0] && w.circuits[0].time_to_complete) || 0));
      let timeStr;
      if (fk === 'emom') {
        const stoppedSec = Math.max(0, ts.phaseLeft !== undefined ? ts.phaseLeft : (ts.roundDuration || 60));
        timeStr = fmt(stoppedSec);
      } else if (fk === 'intervals') {
        timeStr = '00:00';
      } else if (fk === 'circuit') {
        timeStr = ts.elapsed > 0 
          ? fmt(ts.elapsed) 
          : (ts.timeTaken && ts.timeTaken !== '00:00' ? ts.timeTaken : '00:00');
      } else {
        timeStr = ts.elapsed > 0 
          ? fmt(lim > 0 ? Math.max(0, lim - ts.elapsed) : ts.elapsed) 
          : (ts.timeTaken && ts.timeTaken !== '00:00' ? ts.timeTaken : '00:00');
      }
      if(ts.interval){clearInterval(ts.interval);ts.interval=null;ts.running=false;}
      try{
        if(fk==='pyramid'){
          const rows = w.pyramids || w.pyramid || [];
          rows.forEach(r => { r.is_completed = 1; CT[`${w.id}-${r.id}`] = true; });
          await saveStrengthWk(w,timeStr);
        } else if(fk==='straight-sets'){
          (w.straights||[]).forEach(ex => {
            (ex.sets||[]).forEach((s, si) => {
              s.is_completed = 1;
              CT[`${w.id}-${ex.id||0}-${s.id||si}`] = true;
            });
          });
          await saveStrengthWk(w,timeStr);
        } else if(fk==='amrap'){
          const forcedRounds = ts.totalRounds > 0 ? ts.totalRounds : (ts.rounds > 0 ? ts.rounds : 1);
          ts.rounds = forcedRounds;
          const rProgress = `${forcedRounds}/${ts.totalRounds || forcedRounds || 1}`;
          await saveCondWk(w, null, null, rProgress, timeStr, notes);
          const el = document.getElementById('amrap-rounds-' + w.id);
          if (el) el.textContent = `${ts.rounds}`;
          (w.amraps||[]).forEach(ex => { ex.is_completed = 1; ex.round_number = rProgress; });
        } else if(fk==='rounds'){
          const forcedRounds = w.number > 0 ? w.number : (ts.completedRounds > 0 ? ts.completedRounds : 1);
          ts.completedRounds = forcedRounds;
          const rProgress = `${forcedRounds}/${w.number || forcedRounds || 1}`;
          await saveCondWk(w, null, null, rProgress, timeStr, notes);
          const el = document.getElementById('rounds-prog-' + w.id);
          if (el) el.textContent = `${ts.completedRounds}${w.number>0?'/'+w.number:''}`;
          const pbar = document.getElementById('rounds-pbar-' + w.id);
          if (pbar) pbar.style.width = '100%';
          (w.rounds||[]).forEach(ex => { ex.is_completed = 1; ex.round_number = rProgress; });
        } else if(fk==='circuit'){
          const exs = w.circuits || [];
          const sMap = {};
          exs.forEach(ex => {
            const sn = ex.stationumber ?? ex.station_number ?? ex.station ?? 1;
            if(!sMap[sn]) sMap[sn] = [];
            sMap[sn].push(ex);
          });
          const sns = Object.keys(sMap).sort((a,b) => a - b);
          for(const sn of sns){
            CT[`${w.id}-stn-${sn}`] = true;
            (sMap[sn] || []).forEach(ex => { CT[`${w.id}-${ex.id}`] = true; ex.is_completed = 1; ex.exercise_time = timeStr; });
            await saveCircuitStation(w, sn, timeStr);
          }
          ts.completedStations = sns.length;
          const prg = document.getElementById('circuit-prog-' + w.id);
          if (prg) prg.textContent = `${sns.length}/${Math.max(sns.length, 1)}`;
        } else if(fk==='emom'){
          ts.phase = 'done';
          ts.completedRounds = ts.totalRounds || 1;
          const rProgress = `${ts.completedRounds}/${ts.totalRounds || 1}`;
          await saveCondWk(w, null, null, rProgress, timeStr, notes);
          const prg = document.getElementById('emom-prog-' + w.id);
          if (prg) prg.textContent = `${ts.totalRounds}/${ts.totalRounds}`;
          const pbar = document.getElementById('emom-pbar-' + w.id);
          if (pbar) pbar.style.width = '100%';
          const exCont = document.getElementById('emom-ex-cont-' + w.id);
          if (exCont) exCont.style.background = 'transparent';
          (w.emoms||[]).forEach(ex => { ex.is_completed = 1; ex.round_number = rProgress; ex.exercise_time = timeStr; });
          ts.timeTaken = timeStr;
        } else if(fk==='intervals'){
          ts.phase = 'done';
          const rProgress = `${ts.totalRounds||1}/${ts.totalRounds||1}`;
          await saveCondWk(w, null, null, rProgress, timeStr, notes);
          const prg = document.getElementById('int-prog-' + w.id);
          if (prg) prg.textContent = `${ts.totalRounds}/${ts.totalRounds}`;
          (w.intervals||[]).forEach(ex => { ex.is_completed = 1; ex.round_number = rProgress; CT[`${w.id}-${ex.id}`] = true; });
        } else{
          await saveCondWk(w, null, null, null, timeStr, notes);
          (w.for_times||w.forTimes||[]).forEach(ex => { ex.is_completed = 1; });
        }
        getFormatExercises(w).forEach(ex => { ex.is_completed = 1; });
        ts.allDone=true;
        ts.timeTaken=timeStr;
        w.type_completed=1;
        w.status='completed';
        wkStatus='completed';
        showBanner(w.id,fk,true,timeStr);
        updBbar();
        showMsg('Success','Workout saved successfully!');
        renderList();
      }catch(e){console.error(e);showMsg('Error','Failed to save workout: ' + (e.message || ''),true);}
    }

    // ── API HELPERS ────────────────────────────────────────────────
    function workoutDayStr(){ return DAY; }
    function dayStr(){ return DAY; }
    function scoreDayStr(){
      const p = String(DAY).split(' ');
      if(p.length >= 2){
        const dp = p[0].split('/');
        if(dp.length === 3) return `${p[1]} ${dp[0]}/${dp[1]}/20${dp[2]}`;
      }
      return DAY;
    }

    async function saveStrengthWk(w,timeStr){
      const fk=fmtKey(w);
      let ep='/save-strength-workout';
      if(tab==='warmup') ep='/warmup-daily';
      else if(tab==='weightlifting') ep='/save-weightlifting-workout';
      else if(tab==='conditioning') ep='/save-conditioning-workout';
      else if(tab==='accessory') ep='/save-accessory-workout';

      if(fk==='pyramid'){
        const rows=w.pyramids||w.pyramid||[];
        if(!rows.length)return;
        const payload=rows.map((row,idx)=>{
          const u=(row.unittype||row.unit_type||w.unit_type||'%').trim().toUpperCase();
          const ld=row.training_load??row.weight??w.training_load??0;
          const wgt=calcWt(ld,u,row.test_weight??w.test_weight??0);
          const reps=parseInt(row.reps_progress??row.reps??0)||0;
          return {
            workout_manager_id:w.id,
            workout_format_type:'pyramid',
            workout_format_id:row.id,
            reps:reps,
            weight:wgt,
            date:workoutDayStr(),
            set_number:idx+1,
            round_number:`${idx+1}/${rows.length}`,
            exercise_time:timeStr||'00:00',
            class_Id:CLSID,
            notes:notes||null
          };
        });
        if(!payload.length)return;
        const res = await fetch(ep,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify(payload)});
        const data = await res.json().catch(() => null);
        if(!res.ok || (data && data.success === false)) {
          console.error('[saveStrengthWk pyramid] Failed:', data);
          throw new Error(data?.message || data?.error || 'Save failed');
        }
        return;
      }

      const straights=w.straights||[];if(!straights.length)return;
      const payload=[];
      straights.forEach(ex=>{(ex.sets||[]).forEach((s,si)=>{
        const el=document.getElementById(`reps-${w.id}-${ex.id||0}-${s.id||si}`);
        const reps=el?parseInt(el.textContent||'0'):parseInt(s.daily_reps||s.reps_progress||s.reps||0)||0;
        const u=s.unittype||s.unit_type||ex.unittype||ex.unit_type||'%';
        const ld=s.training_load??ex.training_load??ex.weight??0;
        const wgt=s.daily_weight!=null?s.daily_weight:calcWt(ld,u,ex.test_weight||0);
        const wFormatId=(w.format?.slug==='straight-sets'||!w.format)?(s.id||ex.id||0):(ex.id||0);
        payload.push({workout_manager_id:w.id,workout_format_type:w.format?.slug||'straight-sets',workout_format_id:wFormatId,reps,set_number:si+1,weight:wgt,date:workoutDayStr(),exercise_time:timeStr,class_Id:CLSID,notes:notes||null});
      });});
      if(!payload.length)return;
      const res = await fetch(ep,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify(payload)});
      const data = await res.json().catch(() => null);
      if(!res.ok || (data && data.success === false)) {
        console.error('[saveStrengthWk straights] Failed:', data);
        throw new Error(data?.message || data?.error || 'Save failed');
      }
    }

    async function saveCircuitStation(w, stationNum, timeStr){
      const exs = w.circuits || [];
      const sExercises = exs.filter(ex => (ex.stationumber ?? ex.station_number ?? ex.station ?? 1) == stationNum);
      if(!sExercises.length) return;
      const totalStations = new Set(exs.map(ex => ex.stationumber ?? ex.station_number ?? ex.station ?? 1)).size || 1;
      const dateStr = workoutDayStr();
      let ep='/save-conditioning-workout';
      if(tab==='warmup') ep='/warmup-daily';
      else if(tab==='strength') ep='/save-strength-workout';
      else if(tab==='weightlifting') ep='/save-weightlifting-workout';
      else if(tab==='accessory') ep='/save-accessory-workout';

      const payload = sExercises.map(ex => ({
        workout_manager_id: w.id,
        workout_format_type: 'circuit',
        workout_format_id: ex.id || 0,
        reps: parseInt(ex.reps_progress ?? ex.res ?? ex.reps ?? 0) || 0,
        round_number: `${stationNum}/${totalStations}`,
        set_number: parseInt(stationNum) || 1,
        weight: calcWt(ex.training_load ?? 0, ex.unit_type || '%', ex.test_weight ?? 0),
        date: dateStr,
        exercise_time: timeStr || '00:00',
        class_Id: CLSID,
        notes: null
      }));
      try {
        const res = await fetch(ep, {method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF}, body:JSON.stringify(payload)});
        const data = await res.json().catch(() => null);
        if(!res.ok || (data && data.success === false)) {
          console.error('[saveCircuitStation] Failed:', data);
          throw new Error(data?.message || data?.error || 'Save failed');
        }
      } catch(e) { console.error('Error saving circuit station:', e); throw e; }
    }

    async function saveCondWk(w,exId,reps,roundNum,timeStr,notes2){
      const allExs = getFormatExercises(w);
      let exs = allExs;
      if (exId) {
        const found = allExs.find(e => e.id == exId);
        if (found) exs = [found];
      }
      const fk = fmtKey(w);
      const dateStr = workoutDayStr();
      const ts = TS[w.id] || {};

      let ep = '/save-conditioning-workout';
      if(tab === 'warmup') ep = '/warmup-daily';
      else if(tab === 'strength') ep = '/save-strength-workout';
      else if(tab === 'weightlifting') ep = '/save-weightlifting-workout';
      else if(tab === 'accessory') ep = '/save-accessory-workout';

      const isCompletedWorkout = (w.type_completed == 1 || w.status === 'completed' || ts.allDone || ts.phase === 'done');

      const payload = (exs.length > 0 ? exs : [w]).map((ex, exIdx) => {
         let rNum = roundNum || null;
         if (!rNum && fk === 'rounds') {
           const cR = isCompletedWorkout ? (w.number || 1) : (ts.completedRounds !== undefined ? ts.completedRounds : 1);
           rNum = `${cR}/${w.number || cR || 1}`;
         } else if (!rNum && ['intervals', 'emom'].includes(fk)) {
           const totR = ts.totalRounds || w.number || 1;
           const cR = (isCompletedWorkout || ts.phase === 'done') ? totR : (ts.completedRounds !== undefined ? ts.completedRounds : Math.max(0, (ts.round || 1) - 1));
           rNum = `${cR}/${totR}`;
         } else if (!rNum && fk === 'amrap') {
           const totR = ts.totalRounds > 0 ? ts.totalRounds : (ts.rounds > 0 ? ts.rounds : 1);
           const cR = isCompletedWorkout ? Math.max(ts.rounds || 0, totR) : (ts.rounds || 0);
           rNum = `${cR}/${totR}`;
         } else if (!rNum && fk === 'circuit') {
           const totalStns = new Set(allExs.map(e => e.stationumber ?? e.station_number ?? e.station ?? 1)).size || 1;
           rNum = isCompletedWorkout ? `${totalStns}/${totalStns}` : (ex.round_number || `1/${totalStns}`);
         } else if (!rNum && (fk === 'pyramid' || fk === 'pyramids')) {
           rNum = `${exIdx + 1}/${allExs.length || 1}`;
         } else if (!rNum && (fk === 'for-time' || fk === 'straight-sets' || isCompletedWorkout)) {
           rNum = '1/1';
         }

         let exReps = reps;
         if (exReps === null || exReps === undefined) {
           const ftEl = document.getElementById(`ft-reps-${w.id}-${ex.id}`);
           if (ftEl) {
             const parsed = parseInt(ftEl.textContent);
             if (!isNaN(parsed)) exReps = parsed;
           }
         }
         const repsToSave = (exReps !== null && exReps !== undefined)
           ? (parseInt(exReps) || 0)
           : (parseInt(ex.reps_progress ?? ex.res ?? ex.reps ?? 0) || 0);

         const weightToSave = calcWt(ex.training_load ?? ex.weight ?? 0, ex.unit_type || '%', ex.test_weight ?? 0);

         return {
           workout_manager_id: w.id,
           workout_format_type: fk,
           workout_format_id: ex.id || 0,
           reps: repsToSave,
           round_number: rNum,
           set_number: ex.stationumber ?? ex.station_number ?? ex.station ?? (exIdx + 1),
           weight: weightToSave,
           date: dateStr,
           exercise_time: timeStr || (ts.elapsed > 0 ? fmt(ts.elapsed) : '00:00'),
           class_Id: CLSID,
           notes: notes2 || null
         };
      });

      if(!payload.length) return;
      const res = await fetch(ep, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(payload)
      });
      const data = await res.json().catch(() => null);
      if(!res.ok || (data && data.success === false)) {
        console.error('[saveCondWk] Failed:', data);
        throw new Error(data?.message || data?.error || 'Save failed');
      }
      return data;
    }

    // ── APP ALERT ──────────────────────────────────────────────────
    function showMsg(title,msg='',err=false){
      const ex=document.getElementById('app-msg');if(ex)ex.remove();
      const c=err?'#d32f2f':'#1a73e8';
      const bodyHtml=msg?`<div class="app-msg-body">${msg}</div>`:'';
      document.body.insertAdjacentHTML('beforeend',`
        <div class="app-msg-overlay" id="app-msg">
          <div class="app-msg-box">
            <div class="app-msg-content">
              <div class="app-msg-title">${title}</div>
              ${bodyHtml}
            </div>
            <div class="app-msg-footer">
              <button class="app-msg-ok" style="color:${c};" onclick="document.getElementById('app-msg').remove()">OK</button>
            </div>
          </div>
        </div>`);
    }

    // ── 1RM TAB ────────────────────────────────────────────────────
    async function render1RM(){
      const cont=document.getElementById('wk-list');const bbar=document.getElementById('bbar');
      if(bbar)bbar.style.display='none';
      if(cont){
        cont.classList.add('no-bbar');
        cont.classList.add('orm-mode');
        cont.scrollTop = 0;
      }
      
      // Seed immediately from wData if available
      if(!ormEx.length && wData.workoutlibrary && wData.workoutlibrary.length){
        ormEx = wData.workoutlibrary.map(e => ({
          id: e.id,
          workout: e.workout,
          category: e.category_option_name || ''
        }));
      }
      if(!ormRec.length && wData.test && wData.test.length){
        ormRec = wData.test;
      }

      if(ormEx.length > 0){
        renderOrm();
      } else {
        cont.innerHTML='<div style="color:#fff;text-align:center;padding:30px 0;font-size:12px;letter-spacing:2px;font-weight:700;">LOADING...</div>';
      }

      try {
        await Promise.all([fetchOrmEx(), fetchOrmRec()]);
        ormLoaded = true;
      } catch(e) {
        console.error('1RM fetch error:', e);
      }
      renderOrm();
    }
    async function fetchOrmEx(){
      try{
        const r=await fetch('/mobile/data/workout-library');
        const d=await r.json();
        if(d.data && d.data.length) ormEx=d.data;
      }catch(e){console.error(e);}
    }
    async function fetchOrmRec(){
      try{
        const r=await fetch('/mobile/data/1rm-list');
        const d=await r.json();
        if(d.data) ormRec=d.data;
      }catch(e){console.error(e);}
    }
    function renderOrm(){
      const cont=document.getElementById('wk-list');
      if(!cont) return;
      const opts=ormEx.map(e=>`<option value="${e.id}" data-name="${e.workout}" ${e.id==ormSelId?'selected':''}>${e.workout}${e.category?' · '+e.category:''}</option>`).join('');
      const recs=ormRec.length===0?'<div style="color:#f5f5f5;text-align:center;padding:20px 0;font-size:12px;">No 1RM records yet.</div>':ormRec.map(r=>`<div style="display:flex;flex-direction:row;align-items:center;padding:6px 10px;border-radius:6px;margin-bottom:6px;background:rgba(255,255,255,.04);"><div style="width:50%;font-weight:600;color:#f5f5f5;font-size:12px;">${r.workoutname||'Exercise'}</div><div style="flex:2;text-align:center;color:rgba(255,255,255,.35);font-size:10px;">1 REP<br>MAX</div><div style="flex:2;display:flex;flex-direction:row;justify-content:flex-end;align-items:center;"><span style="font-weight:600;color:#f5f5f5;margin-right:3px;font-size:12px;">${r.weight}</span><span style="color:rgba(255,255,255,.4);font-size:10px;">KG</span></div><button onclick="del1RM(${r.id})" style="background:transparent;border:none;border-radius:10px;width:24px;height:24px;display:flex;align-items:center;justify-content:center;margin-left:6px;cursor:pointer;color:#fff;font-weight:700;font-size:12px;flex-shrink:0;padding:0;">✕</button></div>`).join('');
      cont.innerHTML=`<div style="width:100%;height:100%;min-height:360px;display:flex;flex-direction:column;background:rgba(0,0,0,.45);border-radius:12px;overflow:hidden;box-sizing:border-box;">
        <div style="flex-shrink:0;display:flex;flex-direction:row;justify-content:space-between;gap:8px;padding:10px 10px 0 10px;align-items:center;">
          <div style="flex:1.2;position:relative;"><select id="orm-sel" onchange="ormSelId=this.value;ormSelName=this.options[this.selectedIndex]?.dataset?.name||'';" style="width:100%;appearance:none;background:#fff;color:#000;padding:8px 24px 8px 10px;border-radius:6px;font-size:12px;font-weight:700;border:none;outline:none;cursor:pointer;"><option value="">Select Exercise</option>${opts}</select><span style="position:absolute;right:8px;top:50%;transform:translateY(-50%);color:#000;pointer-events:none;font-size:10px;font-weight:700;">▼</span></div>
          <div style="display:flex;flex-direction:row;align-items:center;gap:6px;flex-shrink:0;">
            <div style="display:flex;flex-direction:column;align-items:center;line-height:1.2;"><span style="color:#fff;font-size:9px;font-weight:700;">1 REP</span><span style="color:#fff;font-size:9px;font-weight:700;">MAX</span></div>
            <input id="orm-wt" type="number" min="0" step=".5" placeholder="KG" value="${ormWt}" oninput="ormWt=this.value" style="width:60px;height:36px;border:1px solid #fff;border-radius:6px;background:#fff;color:#000;font-size:13px;font-weight:600;text-align:center;outline:none;">
            <span style="color:#fff;font-size:13px;">kg</span>
          </div>
        </div>
        <div style="flex-shrink:0;padding:8px 10px 12px 10px;"><button onclick="save1RM()" style="width:100%;padding:10px 24px;border-radius:6px;border:1px solid #fff;background:transparent;color:#fff;font-size:12px;font-weight:700;cursor:pointer;">Save</button></div>
        <div class="cscroll" style="flex:1;overflow-y:auto;padding:0 10px 14px 10px;">${recs}</div>
      </div>`;
    }
    async function save1RM(){
      const sel = document.getElementById('orm-sel');
      const selOpt = sel ? sel.options[sel.selectedIndex] : null;
      const selId = sel ? sel.value : ormSelId;
      const exName = (selOpt && selOpt.dataset && selOpt.dataset.name) ? selOpt.dataset.name : (ormSelName || 'Exercise');
      const wt=parseFloat(document.getElementById('orm-wt')?.value||'0');
      const memId = MEMID || wData?.member?.id;

      if(!selId){showMsg('Oops','Please select an exercise.',true);return;}
      if(!wt||wt<=0){showMsg('Oops','Please enter a valid weight.',true);return;}
      if(!memId){showMsg('Error','Member profile not found.',true);return;}
      try{
        const r=await fetch('/mobile/1rm/insert',{
          method:'POST',
          headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
          body:JSON.stringify({
            workout_id:parseInt(selId),
            member_id:memId,
            weight:wt,
            workoutname:exName,
            selected_day:scoreDayStr() || workoutDayStr()
          })
        });
        const d=await r.json();
        if(d.success){
          showMsg('Success',d.message||'Saved!');
          ormWt=''; ormSelId=null; ormSelName='';
          await fetchOrmRec();
          renderOrm();
        } else {
          showMsg('Error',d.message||'Failed to save 1RM.',true);
        }
      }catch(e){
        showMsg('Error','Network error occurred while saving.',true);
      }
    }
    async function del1RM(id){
      if(!confirm('Are you sure you want to delete this test weight record?')) return;
      try{
        const r=await fetch('/mobile/1rm/delete',{
          method:'DELETE',
          headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
          body:JSON.stringify({test_id:id})
        });
        const d=await r.json();
        if(d.success){
          ormRec=ormRec.filter(r=>r.id!==id);
          renderOrm();
        } else {
          showMsg('Error',d.message||'Could not delete.',true);
        }
      }catch(e){
        showMsg('Error','Network error occurred.',true);
      }
    }
    </script>
@endsection


@extends('mobile.layout.mobile-layout')
@section('content')
    @push('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    @endpush
    <style>
        html,body{overflow:hidden!important;height:100vh!important;max-height:100vh!important;}
        .mob-content{height:100vh!important;display:flex!important;flex-direction:column!important;overflow:hidden!important;}
        @keyframes spin{100%{transform:rotate(360deg);}}
        @keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
        @keyframes popIn{from{transform:scale(0.85);opacity:0;}to{transform:scale(1);opacity:1;}}
        @keyframes bannerIn{from{transform:scale(0.7);opacity:0;}to{transform:scale(1);opacity:1;}}
        @keyframes pulseGlow{0%,100%{box-shadow:0 0 0 0 rgba(242,153,74,.4);}50%{box-shadow:0 0 0 12px rgba(242,153,74,0);}}
        @keyframes countdownPulse{0%{transform:scale(1);opacity:1;}50%{transform:scale(1.15);opacity:.8;}100%{transform:scale(1);opacity:1;}}
        input[type="number"]::-webkit-inner-spin-button,input[type="number"]::-webkit-outer-spin-button{-webkit-appearance:none;margin:0;}
        input[type="number"]{-moz-appearance:textfield;}
        .cscroll::-webkit-scrollbar{display:none;}.cscroll{-ms-overflow-style:none;scrollbar-width:none;}
        /* Cards */
        .wk-card{background:transparent;border-radius:0;padding:12px;margin-bottom:12px;border:1px solid transparent;position:relative;overflow:hidden;cursor:pointer;transition:border-color .2s;}
        .wk-card.selected{border-color:transparent;}
        .wk-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;}
        .wk-fmt{color:#fff;font-size:16px;font-weight:bold;letter-spacing:.5px;}
        .wk-pbadge{background:rgba(255,255,255,.15);border-radius:12px;padding:2px 10px;color:#fff;font-size:13px;font-weight:600;}
        .wk-rhdr{color:#F2994A;font-size:14px;font-weight:bold;min-width:28px;text-align:center;}
        /* Exercise row */
        .ex-row{display:flex;align-items:center;padding:0;background:transparent;border-radius:0;margin-bottom:0;gap:8px;}
        .active-ex-row{background:rgba(255,255,255,0.15);border-radius:8px;border:1px solid rgba(255,255,255,0.3);margin:0 -12px;padding:8px 12px;}
        .ex-name{flex:2;color:#fff;font-size:14px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;padding-left:12px;}
        .wbox{background:#fff;border-radius:4px;padding:2px 6px;color:#000;font-size:14px;min-width:60px;display:flex;align-items:center;justify-content:center;flex:1;text-align:center;white-space:nowrap;margin:0;}
        .rbox{background:#fff;border-radius:4px;padding:2px 6px;color:#000;font-size:14px;width:30px;text-align:center;}
        .rbtn-wrap{display:flex;align-items:center;flex:1;justify-content:flex-end;padding-right:10px;}
        .rbtn{background:transparent;border:none;color:#fff;font-size:18px;font-weight:800;cursor:pointer;padding:2px 6px;border-radius:6px;line-height:1;}
        .rbtn:hover{background:rgba(255,255,255,.1);}
        .badge-done{color:#22c55e;font-size:11px;font-weight:800;letter-spacing:1px;margin-left:4px;}
        .badge-fail{color:#ef4444;font-size:11px;font-weight:800;letter-spacing:1px;margin-left:4px;}
        /* Set groups */
        .set-num{color:#fff;font-size:13px;font-weight:700;width:24px;text-align:center;flex-shrink:0;}
        .set-grp{margin-bottom:14px;padding-bottom:12px;border-bottom:1px solid rgba(255,255,255,0.15);}
        /* Inline timer block */
        .card-timer{background:rgba(0,0,0,.3);border-radius:10px;padding:10px 14px;display:flex;flex-direction:column;align-items:center;margin-bottom:10px;}
        .ct-time{color:#fff;font-size:36px;font-weight:900;font-variant-numeric:tabular-nums;letter-spacing:-1px;line-height:1;}
        .ct-label{color:rgba(255,255,255,.45);font-size:10px;font-weight:700;letter-spacing:2px;margin-top:4px;}
        .ct-phase{font-size:11px;font-weight:800;letter-spacing:1.5px;margin-top:4px;text-transform:uppercase;}
        .ct-btns{display:flex;gap:8px;margin-top:10px;width:100%;}
        .ctbtn{flex:1;padding:8px 0;border-radius:8px;border:1px solid rgba(255,255,255,.3);background:rgba(255,255,255,.08);color:#fff;font-size:12px;font-weight:800;cursor:pointer;letter-spacing:.5px;text-align:center;}
        .ctbtn:hover{background:rgba(255,255,255,.15);}
        .ctbtn.primary{background:#fff;color:#000;border-color:#fff;}
        .ctbtn.danger{background:rgba(239,68,68,.15);border-color:#ef4444;color:#ef4444;}
        /* Station block */
        .stn-block{background:rgba(255,255,255,.04);border-radius:10px;padding:8px 10px;margin-bottom:8px;}
        .stn-lbl{color:rgba(255,255,255,.4);font-size:10px;font-weight:700;letter-spacing:1px;margin-bottom:6px;text-transform:uppercase;}
        /* Group Rows */
        .grp-row{display:flex;flex-direction:row;align-items:stretch;margin-bottom:12px;position:relative;}
        .grp-num{width:32px;display:flex;align-items:flex-start;justify-content:center;padding-top:4px;color:#fff;font-weight:900;font-size:16px;}
        .grp-exs{flex:1;display:flex;flex-direction:column;}
        /* Interval styles - full width dividers are now handled inline between groups */
        /* Pyramid */
        .pyr-row-wrap{width:100%;padding:4px 0;margin-bottom:2px;cursor:pointer;transition:background .15s;}
        /* Rest timer strip */
        .rest-strip{background:rgba(0,0,0,.4);border-radius:8px;padding:6px 10px;display:flex;align-items:center;justify-content:space-between;margin-top:8px;}
        /* Complete banner */
        .wk-banner{position:absolute;inset:0;z-index:50;display:flex;flex-direction:column;align-items:center;justify-content:center;background:rgba(0,0,0,0.6);border:1.5px solid rgba(242,153,74,0.3);border-radius:0;pointer-events:none;animation:bannerIn .35s cubic-bezier(.34,1.56,.64,1) forwards;}
        .wk-banner-circle{width:60px;height:60px;border-radius:50%;background:#F2994A;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(242,153,74,.4);margin-bottom:10px;}
        .wk-banner-txt{color:#ffffff;font-size:22px;font-weight:800;letter-spacing:.8px;text-shadow:0 2px 6px rgba(0,0,0,.8);text-transform:lowercase;font-family:inherit;margin:0;line-height:1.2;text-align:center;}
        /* Incomplete banner */
        .fail-banner{position:absolute;inset:0;z-index:50;background:rgba(0,0,0,.7);border-radius:0;display:flex;flex-direction:column;align-items:center;justify-content:center;}
        .fail-circle{width:56px;height:56px;border-radius:50%;background:#ef4444;display:flex;align-items:center;justify-content:center;margin-bottom:10px;font-size:22px;color:#fff;font-weight:900;}
        /* Countdown overlay in-card */
        .cd-overlay{position:absolute;inset:0;z-index:60;background:rgba(0,0,0,.85);border-radius:16px;display:flex;align-items:center;justify-content:center;flex-direction:column;}
        .cd-num{color:#fff;font-size:64px;font-weight:900;line-height:1;animation:countdownPulse .8s ease-in-out;}
        /* Bottom bar */
        #bbar{display:none;}
        /* Notes modal */
        .nmod-overlay{position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.6);display:flex;align-items:center;justify-content:center;animation:fadeIn .15s;}
        .nmod-box{background:#111;border-radius:16px;padding:20px;width:290px;border:1px solid rgba(255,255,255,.12);animation:popIn .2s;}
        .nmod-title{color:#fff;font-size:16px;font-weight:700;margin-bottom:14px;text-align:center;}
        .nmod-input{width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.2);border-radius:10px;color:#fff;font-size:14px;padding:10px;min-height:90px;resize:none;font-family:inherit;box-sizing:border-box;}
        .nmod-input::placeholder{color:rgba(255,255,255,.3);}
        .nmod-actions{display:flex;gap:10px;margin-top:14px;}
        .nmod-cancel{flex:1;padding:10px;background:transparent;border:1px solid rgba(255,255,255,.2);border-radius:8px;color:rgba(255,255,255,.6);font-size:13px;font-weight:700;cursor:pointer;}
        .nmod-save{flex:2;padding:10px;background:#fff;border:none;border-radius:8px;color:#000;font-size:13px;font-weight:800;cursor:pointer;}
        /* App alert */
        .app-msg-overlay{position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;animation:fadeIn .15s;}
        .app-msg-box{background:#1c1c1e;border-radius:14px;width:280px;border:1px solid rgba(255,255,255,.1);animation:popIn .2s;}
        .app-msg-content{padding:20px 16px;text-align:center;}
        .app-msg-title{color:#fff;font-size:16px;font-weight:700;margin-bottom:6px;}
        .app-msg-body{color:rgba(255,255,255,.75);font-size:13px;line-height:1.4;}
        .app-msg-footer{border-top:1px solid rgba(255,255,255,.15);}
        .app-msg-ok{width:100%;padding:12px;background:transparent;border:none;font-size:16px;font-weight:500;cursor:pointer;border-radius:0 0 14px 14px;}
        /* Phase colors */
        .phase-w{color:#22c55e;}.phase-r{color:#ef4444;}.phase-idle{color:rgba(255,255,255,.5);}
    </style>

    <div id="wk-bg" style="flex:1;background-image:url('{{ asset('img/valhalla-bg.jpg') }}');background-size:cover;background-position:center;padding:6px 5px 0 5px;display:flex;flex-direction:column;overflow:hidden;">
        <div id="wk-ring" style="flex:1;border:5px solid #4B5563;border-radius:8px;padding:16px 8px 8px 8px;margin:1px 1px 10px 1px;display:flex;flex-direction:column;overflow:hidden;position:relative;transition:border-color .3s;background:transparent;">

            {{-- Category tabs --}}
            <div id="cat-tabs" style="display:flex;flex-direction:row;justify-content:center;align-items:center;height:60px;min-height:60px;margin-bottom:10px;gap:6px;overflow-x:auto;scrollbar-width:none;"></div>

            {{-- Loader --}}
            <div id="loader" style="display:none;text-align:center;padding:40px 0;color:#fff;">
                <svg style="animation:spin 1s linear infinite;height:32px;width:32px;margin:0 auto;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <p style="margin-top:16px;font-weight:700;letter-spacing:2px;font-size:12px;">LOADING WORKOUTS...</p>
            </div>

            {{-- Workouts list --}}
            <div id="wk-list" class="cscroll" style="flex:1;display:flex;flex-direction:column;overflow-y:auto;padding-bottom:80px;"></div>

            {{-- Bottom action bar --}}
            <div id="bbar" style="position:absolute;bottom:0;left:0;right:0;flex-direction:row;align-items:stretch;z-index:20;background:#111;border-top:1px solid rgba(255,255,255,.15);padding:8px 6px;min-height:68px;gap:6px;">
                <button id="btn-left" onclick="handleLeft()" style="flex:1.1;display:flex;align-items:center;justify-content:center;padding:6px;border-radius:8px;border:1px solid rgba(255,255,255,.45);background:#1a1a1a;color:#fff;font-weight:800;font-size:12px;cursor:pointer;letter-spacing:.3px;min-width:0;text-align:center;">COMPLETE<br>ROUND</button>
                <button id="btn-center" onclick="handleCenter()" style="flex:1.5;display:flex;align-items:center;justify-content:center;border-radius:8px;border:1px solid #CD7F32;background:#CD7F32;color:#fff;font-weight:900;font-size:15px;cursor:pointer;padding:6px;letter-spacing:1px;text-align:center;">START</button>
                <button id="btn-right" onclick="handleRight()" style="flex:1.1;display:flex;align-items:center;justify-content:center;padding:6px;border-radius:8px;border:1px solid rgba(255,255,255,.45);background:#1a1a1a;color:#fff;font-weight:800;font-size:12px;cursor:pointer;letter-spacing:.3px;min-width:0;text-align:center;">COMPLETE<br>WORKOUT</button>
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
    document.addEventListener('DOMContentLoaded',()=>{ buildTabs(); fetchWk(); });

    // ── TABS ───────────────────────────────────────────────────────
    function buildTabs(){
      const c=document.getElementById('cat-tabs'); c.innerHTML='';
      TABS.forEach(t=>{
        const act=t.id===tab;
        const btn=document.createElement('button');
        btn.onclick=()=>setTab(t.id);
        Object.assign(btn.style,{flex:'1',aspectRatio:'1',maxWidth:'56px',minWidth:'40px',borderRadius:'12px',border:'2px solid #fff',display:'flex',justifyContent:'center',alignItems:'center',cursor:'pointer',transition:'all .2s',padding:'0',backgroundColor:act?'#fff':'rgba(0,0,0,0.7)'});
        if(t.id==='1rm'){btn.innerHTML=`<span style="color:${act?'#000':'#fff'};font-weight:900;font-size:14px;">1RM</span>`;}
        else{const sc=t.sc?`scale(${t.sc})`:'scale(1)';btn.innerHTML=`<img src="${act?t.ib:t.iw}" stylewidth:28px;height:28px;object-fit:contain;transform:${sc};display:block;" loading="lazy">`;}
        c.appendChild(btn);
      });
    }

    function setTab(tid){
      pauseAll(tab);
      tab=tid; selId=null; wkStatus='ready';
      buildTabs();
      if(tid==='1rm'){render1RM();}else{renderList();}
    }

    // ── FETCH ──────────────────────────────────────────────────────
    async function fetchWk(){
      document.getElementById('loader').style.display='block';
      document.getElementById('wk-list').innerHTML='';
      try{
        const r=await fetch('/mobile/get-workout-list',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({date:DAY,class_id:CLSID})});
        const d=await r.json();
        if(d.status&&d.workouts){const w=d.workouts;wData={warmup:w.Warmup||w.warmup||[],strength:w.Strength||w.strength||[],weightlifting:w.Weightlifting||w.weightlifting||[],conditioning:w.Conditioning||w.conditioning||[],accessory:w.Accessory||w.accessory||[],test:d.test||[],member:d.member||null};}
        else{wData={warmup:[],strength:[],weightlifting:[],conditioning:[],accessory:[],test:[],member:null};}
      }catch(e){console.error(e);}
      document.getElementById('loader').style.display='none';
      renderList();
    }

    // ── RENDER LIST ────────────────────────────────────────────────
    function renderList(){
      const cont=document.getElementById('wk-list');
      const bbar=document.getElementById('bbar');
      cont.innerHTML='';
      bbar.style.display='none';
      const list=wData[tab]||[];
      if(!list.length){cont.innerHTML='<div style="display:flex;align-items:center;justify-content:center;margin-top:80px;"><div style="color:#fff;font-size:14px;font-weight:600;letter-spacing:1px;">No Workouts.</div></div>';return;}
      bbar.style.display='flex';
      if(!selId&&list.length)selId=list[0].id;
      list.forEach((w,i)=>{cont.insertAdjacentHTML('beforeend',cardFor(w,i));});
      list.forEach(w=>{const el=document.getElementById('wk-'+w.id);if(el)el.onclick=()=>selWk(w.id);});
      updBbar(); updSel();
    }

    // ── FORMAT KEY ─────────────────────────────────────────────────
    function fmtKey(w){if(w.format&&w.format.slug)return w.format.slug;if(w.format&&w.format.name)return w.format.name.toLowerCase().replace(/\s+/g,'-');return 'unknown';}

    // ── HELPERS ────────────────────────────────────────────────────
    function exName(ex){return ex?.workout_library?.workout||ex?.workoutLibrary?.workout||ex?.exercise_name||ex?.workout_name||'Exercise';}
    function gender(){const m=wData.member;if(!m)return null;const g=String(m.gender||'').toLowerCase();if(g.startsWith('f')||g==='2')return'female';if(g.startsWith('m')||g==='1')return'male';return null;}
    function fmt(s){const m=Math.floor(Math.abs(s)/60);const sec=Math.abs(s)%60;return`${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`;}
    function parseSec(raw){if(!raw)return 0;if(typeof raw==='number')return raw>0?Math.floor(raw):0;const str=String(raw).trim();if(!str||str==='00:00:00')return 0;const p=str.split(':').map(Number);if(p.length===3)return p[0]*3600+p[1]*60+p[2];if(p.length===2)return p[0]*60+p[1];if(p.length===1&&!isNaN(p[0]))return p[0];return 0;}
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
      const g=window.wData&&window.wData.member?(String(window.wData.member.gender||'').toLowerCase().startsWith('f')||window.wData.member.gender=='2'?'female':'male'):null;
      const sd=window.wData&&window.wData.score?window.wData.score:null;
      if(u==='%'){
        const pen=getReadinessPenalty(sd)+(missedReps*0.03);
        const eff=Math.max(0,(numLoad/100)-pen);
        return Math.round((testWeight||0)*eff)+" KG";
      }
      let fw=numLoad;
      if(u==='KG'&&g==='female')fw=numLoad*0.75;
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
    // ── SELECTION ──────────────────────────────────────────────────
    function selWk(id){if(selId===id)return;selId=id;wkStatus='ready';renderList();}
    function updSel(){
      (wData[tab]||[]).forEach(w=>{
        const el=document.getElementById('wk-'+w.id);if(!el)return;
        if(w.id===selId){el.classList.add('selected');['rounds-tmr','ft-tmr','int-tmr'].forEach(p=>{const tb=document.getElementById(p+'-'+w.id);if(tb)tb.style.display='';});}
        else{el.classList.remove('selected');['rounds-tmr','ft-tmr','int-tmr'].forEach(p=>{const tb=document.getElementById(p+'-'+w.id);if(tb)tb.style.display='none';});}
      });
    }

    // ── BOTTOM BAR ─────────────────────────────────────────────────
    function updBbar(){
      const list=wData[tab]||[];
      const bbar=document.getElementById('bbar');if(!bbar)return;
      if(!list.length||tab==='1rm'){bbar.style.display='none';return;}
      bbar.style.display='flex';
      const w=list.find(x=>x.id===selId);if(!w)return;
      const fk=fmtKey(w);
      const ts=TS[w.id]||{};
      const bl=document.getElementById('btn-left');
      const bc=document.getElementById('btn-center');
      const br=document.getElementById('btn-right');
      // Sync completion status with workout data
      if(w.type_completed==1 || w.status==='completed' || ts.allDone || ts.phase==='done'){
        wkStatus='completed';
      }
      // Left
      const roundFmts=['rounds','amrap','circuit','intervals','emom','pyramid','straight-sets'];
      if(roundFmts.includes(fk)){const lbl={'straight-sets':'COMPLETE<br>SET','intervals':'COMPLETE<br>INTERVAL'};bl.innerHTML=lbl[fk]||'COMPLETE<br>ROUND';bl.style.display='flex';}else{bl.style.display='none';}
      // Center (timer display or START)
      updCenterBtn(w,fk,ts);
      // Right
      const hasStarted = ts.running || ts.paused || (ts.elapsed > 0) || Object.keys(RST).some(k=>k.startsWith(`rest-${w.id}-`)||k.startsWith(`pyr-${w.id}-`));
      if(wkStatus==='completed'){br.innerHTML='COMPLETED';br.style.borderColor='rgba(255,255,255,.3)';br.style.color='rgba(255,255,255,.4)';br.disabled=false;}
      else if(wkStatus==='incompleted'){br.innerHTML='INCOMPLETE';br.style.borderColor='#fca5a5';br.style.color='#fca5a5';br.disabled=true;}
      else{br.innerHTML=hasStarted?'COMPLETE<br>EARLY':'COMPLETE<br>WORKOUT';br.style.borderColor='rgba(255,255,255,.45)';br.style.color='#fff';br.disabled=false;}
      // Ring border color — grey by default (matching mobile app), coloured only when active timer is running
      const ring=document.getElementById('wk-ring');
      if(ring){
        if(wkStatus==='completed'||wkStatus==='incompleted'){
            ring.style.borderColor='#4B5563';
        } else{
          // For straight-sets: grey ring (like mobile app). Color only for timer-based formats when running.
          const timerFmts=['rounds','amrap','for-time','emom','intervals','circuit'];
          const isTimerRunning=ts.running||ts.paused;
          if(fk==='straight-sets'){
            // Check if any rest timer is active
            const activeKey=Object.keys(RST).find(k=>k.startsWith(`rest-${w.id}-`)&&RST[k]?.running);
            ring.style.borderColor=activeKey?RST[activeKey].color:'#4B5563';
          } else if(timerFmts.includes(fk)&&isTimerRunning){
            const fc={rounds:'#3b82f6',amrap:'#F2994A','for-time':'#8b5cf6',emom:'#22c55e',intervals:'#22c55e',circuit:'#f97316'};
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
    function handleLeft(){
      const list=wData[tab]||[];const w=list.find(x=>x.id===selId);if(!w)return;
      const fk=fmtKey(w);const ts=TS[w.id]||{};
      if(fk==='rounds'){
          if(!ts.running && !ts.allDone) { showMsg('Notice', "Please click 'START' to begin your workout."); return; }
          if(ts.limit>0 && ts.elapsed>=ts.limit && ts.completedRounds<w.number) { showMsg('Notice', "Workout incomplete."); return; }
          ts.completedRounds=(ts.completedRounds||0)+1;
          const el=document.getElementById('rounds-prog-'+w.id);
          if(el)el.textContent=`${ts.completedRounds}${w.number>0?'/'+w.number:''}`;
          const pbar=document.getElementById('rounds-pbar-'+w.id);
          if(pbar)pbar.style.width=`${w.number>0?(ts.completedRounds/w.number)*100:0}%`;
          saveCondWk(w, null, null, null, fmt(ts.elapsed||0), null);
          if(w.number>0&&ts.completedRounds>=w.number){ ts.allDone=true; finishRounds(w.id,true); }
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
        if(ts.allDone || w.type_completed==1 || w.status==='completed') return;
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
          CT[`${w.id}-stn-${uncompletedStation}`] = true;
          (sMap[uncompletedStation] || []).forEach(ex => {
            CT[`${w.id}-${ex.id}`] = true;
            ex.is_completed = 1;
          });
          const timeStr = ts.elapsed > 0 ? fmt(ts.elapsed) : '00:00';
          saveCircuitStation(w, uncompletedStation, timeStr);
          
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
            finishCircuit(w.id, true);
          } else {
            renderList();
          }
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
      else if(fk==='rounds')startRounds(w.id);
      else if(fk==='amrap')startAmrap(w.id);
      else if(fk==='for-time')startFT(w.id);
      else if(fk==='emom')startEmom(w.id);
      else if(fk==='intervals')startInt(w.id);
      else if(fk==='circuit'){
        const isForTime = Number(w.is_for_time||0)===1 || Number(w.is_fortime||0)===1 || Number(w.for_time||0)===1 || (w.circuits||[]).some(e=>Number(e.is_for_time||0)===1 || Number(e.is_fortime||0)===1 || Number(e.for_time||0)===1);
        if(!isForTime) return;
        startCircuit(w.id);
      }
    }

    function handleRight(){if(wkStatus==='incompleted')return;openNotes();}

    // ── BANNER ─────────────────────────────────────────────────────
    function showBanner(wId,fmtLabel,ok,timeTaken){
      const ts=TS[wId]=TS[wId]||{};
      const list=wData[tab]||[];
      const w=list.find(x=>x.id===wId);
      if(ok){
         ts.allDone=true;
         if(timeTaken) ts.timeTaken=timeTaken;
         if(w){ w.type_completed=1; w.status='completed'; }
         const el=document.getElementById('banner-'+wId);
         if(el) el.innerHTML=bannerHtml(fmtLabel);
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
      const fk=fmtKey(w);const ts=TS[w.id]||{};const timeStr=ts.elapsed>0?fmt(ts.elapsed):'00:00';
      if(ts.interval){clearInterval(ts.interval);ts.interval=null;ts.running=false;}
      try{
        if(['straight-sets','pyramid'].includes(fk)){
          await saveStrengthWk(w,timeStr);
        } else if(fk==='amrap'){
          const forcedRounds = ts.totalRounds > 0 ? ts.totalRounds : (ts.rounds > 0 ? ts.rounds : 1);
          ts.rounds = forcedRounds;
          const rProgress = `${forcedRounds}/${ts.totalRounds || forcedRounds || 1}`;
          await saveCondWk(w, null, null, rProgress, timeStr, notes);
          const el = document.getElementById('amrap-rounds-' + w.id);
          if (el) el.textContent = `${ts.rounds}`;
          (w.amraps||[]).forEach(ex => { ex.is_completed = 1; });
        } else{
          await saveCondWk(w,null,null,null,timeStr,notes);
        }
        ts.allDone=true;
        ts.timeTaken=timeStr;
        if(fk==='emom'){
          ts.phase='done';ts.completedRounds=ts.totalRounds;
          const prg=document.getElementById('emom-prog-'+w.id);if(prg)prg.textContent=`${ts.totalRounds}/${ts.totalRounds}`;
          const pbar=document.getElementById('emom-pbar-'+w.id);if(pbar)pbar.style.width='100%';
          const exCont=document.getElementById('emom-ex-cont-'+w.id);if(exCont)exCont.style.background='transparent';
        }
        w.type_completed=1;
        w.status='completed';
        wkStatus='completed';
        showBanner(w.id,fk,true,timeStr);
        updBbar();
        showMsg('Success','Workout saved successfully!');
        renderList();
      }catch(e){console.error(e);showMsg('Error','Failed to save workout.',true);}
    }

    // ── API HELPERS ────────────────────────────────────────────────
    function dayStr(){const p=String(DAY).split(' ');if(p.length>=2){const dp=p[0].split('/');if(dp.length===3)return`${p[1]} ${dp[0]}/${dp[1]}/20${dp[2]}`;}return DAY;}

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
            date:dayStr(),
            set_number:idx+1,
            round_number:`${idx+1}/${rows.length}`,
            exercise_time:timeStr||'00:00',
            class_Id:CLSID,
            notes:notes||null
          };
        });
        if(!payload.length)return;
        await fetch(ep,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify(payload)});
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
        payload.push({workout_manager_id:w.id,workout_format_type:w.format?.slug||'straight-sets',workout_format_id:wFormatId,reps,set_number:si+1,weight:wgt,date:dayStr(),exercise_time:timeStr,class_Id:CLSID,notes:notes||null});
      });});
      if(!payload.length)return;
      await fetch(ep,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify(payload)});
    }

    async function saveCircuitStation(w, stationNum, timeStr){
      const exs = w.circuits || [];
      const sExercises = exs.filter(ex => (ex.stationumber ?? ex.station_number ?? ex.station ?? 1) == stationNum);
      if(!sExercises.length) return;
      const dateStr = dayStr();
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
        round_number: String(stationNum),
        weight: calcWt(ex.training_load ?? 0, ex.unit_type || '%', ex.test_weight ?? 0),
        date: dateStr,
        exercise_time: timeStr || '00:00',
        class_Id: CLSID,
        notes: null
      }));
      try {
        await fetch(ep, {method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF}, body:JSON.stringify(payload)});
      } catch(e) { console.error('Error saving circuit station:', e); }
    }

    async function saveCondWk(w,exId,reps,roundNum,timeStr,notes2){
      const exs=w.rounds||w.amraps||w.for_times||w.emoms||w.intervals||w.circuits||w.pyramids||[];
      const fk=fmtKey(w);const dateStr=dayStr();
      const ts = TS[w.id] || {};

      let ep='/save-conditioning-workout';
      if(tab==='warmup') ep='/warmup-daily';
      else if(tab==='strength') ep='/save-strength-workout';
      else if(tab==='weightlifting') ep='/save-weightlifting-workout';
      else if(tab==='accessory') ep='/save-accessory-workout';

      const payload=(exs.length>0?exs:[w]).map(ex=>{
         let rNum = roundNum || null;
         if (!rNum && fk === 'rounds' && ts.completedRounds !== undefined) rNum = `${ts.completedRounds}/${w.number||0}`;
         else if (!rNum && ['intervals','emom'].includes(fk) && ts.round !== undefined) {
             const cR = ts.phase === 'done' ? ts.totalRounds : Math.max(0, ts.round - 1);
             rNum = `${cR}/${ts.totalRounds||0}`;
         }
         else if (!rNum && ts.round !== undefined) rNum = `${ts.round}/${ts.totalRounds||0}`;
         else if (!rNum && ts.rounds !== undefined) rNum = `${ts.rounds}/${ts.totalRounds||1}`;
         
         return {
           workout_manager_id:w.id,
           workout_format_type:fk,
           workout_format_id:ex.id||0,
           reps:reps!==null?parseInt(reps)||0:parseInt(ex.reps)||0,
           round_number:rNum,
           weight:calcWt(ex.training_load??0,ex.unit_type||'%',ex.test_weight||0),
           date:dateStr,
           exercise_time:timeStr||'00:00',
           class_Id:CLSID,
           notes:notes2||null
         };
      });
      if(!payload.length)return;
      await fetch(ep,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify(payload)});
    }

    // ── APP ALERT ──────────────────────────────────────────────────
    function showMsg(title,msg,err=false){
      const ex=document.getElementById('app-msg');if(ex)ex.remove();
      const c=err?'#ff453a':'#0a84ff';
      document.body.insertAdjacentHTML('beforeend',`<div class="app-msg-overlay" id="app-msg"><div class="app-msg-box"><div class="app-msg-content"><div class="app-msg-title">${title}</div><div class="app-msg-body">${msg}</div></div><div class="app-msg-footer"><button class="app-msg-ok" style="color:${c};" onclick="document.getElementById('app-msg').remove()">OK</button></div></div></div>`);
    }

    // ── 1RM TAB ────────────────────────────────────────────────────
    async function render1RM(){
      const cont=document.getElementById('wk-list');const bbar=document.getElementById('bbar');
      if(bbar)bbar.style.display='none';
      cont.innerHTML='<div style="color:#fff;text-align:center;padding:30px 0;font-size:12px;letter-spacing:2px;font-weight:700;">LOADING...</div>';
      if(!ormLoaded){await Promise.all([fetchOrmEx(),fetchOrmRec()]);ormLoaded=true;}
      renderOrm();
    }
    async function fetchOrmEx(){try{const r=await fetch('/mobile/data/workout-library');const d=await r.json();ormEx=d.data||[];}catch(e){}}
    async function fetchOrmRec(){try{const r=await fetch('/mobile/data/1rm-list');const d=await r.json();ormRec=d.data||[];}catch(e){}}
    function renderOrm(){
      const cont=document.getElementById('wk-list');
      const opts=ormEx.map(e=>`<option value="${e.id}" data-name="${e.workout}" ${e.id==ormSelId?'selected':''}>${e.workout}${e.category?' · '+e.category:''}</option>`).join('');
      const recs=ormRec.length===0?'<div style="color:#f5f5f5;text-align:center;padding:20px 0;font-size:13px;">No 1RM records yet.</div>':ormRec.map(r=>`<div style="display:flex;flex-direction:row;align-items:center;padding:8px 12px;border-radius:8px;margin-bottom:6px;background:rgba(255,255,255,.04);"><div style="width:50%;font-weight:600;color:#f5f5f5;font-size:13px;">${r.workoutname||'Exercise'}</div><div style="flex:2;text-align:center;color:rgba(255,255,255,.35);font-size:11px;">1 REP<br>MAX</div><div style="flex:2;display:flex;flex-direction:row;justify-content:flex-end;align-items:center;"><span style="font-weight:600;color:#f5f5f5;margin-right:3px;font-size:13px;">${r.weight}</span><span style="color:rgba(255,255,255,.4);font-size:11px;">KG</span></div><button onclick="del1RM(${r.id})" style="background:transparent;border:none;border-radius:12px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;margin-left:8px;cursor:pointer;color:#fff;font-weight:700;font-size:13px;flex-shrink:0;padding:0;">✕</button></div>`).join('');
      cont.innerHTML=`<div style="width:100%;height:100%;display:flex;flex-direction:column;background:rgba(0,0,0,.45);border-radius:12px;overflow:hidden;">
        <div style="flex-shrink:0;display:flex;flex-direction:row;justify-content:space-between;gap:10px;padding:12px 10px 0 10px;align-items:center;">
          <div style="flex:1.2;position:relative;"><select id="orm-sel" onchange="ormSelId=this.value;ormSelName=this.options[this.selectedIndex]?.dataset?.name||'';" style="width:100%;appearance:none;background:#fff;color:#000;padding:10px 28px 10px 12px;border-radius:8px;font-size:13px;font-weight:700;border:none;outline:none;cursor:pointer;"><option value="">Select Exercise</option>${opts}</select><span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#000;pointer-events:none;font-size:11px;font-weight:700;">▼</span></div>
          <div style="display:flex;flex-direction:row;align-items:center;gap:8px;flex-shrink:0;">
            <div style="display:flex;flex-direction:column;align-items:center;line-height:1.2;"><span style="color:#fff;font-size:10px;font-weight:700;">1 REP</span><span style="color:#fff;font-size:10px;font-weight:700;">MAX</span></div>
            <input id="orm-wt" type="number" min="0" step=".5" placeholder="KG" value="${ormWt}" oninput="ormWt=this.value" style="width:72px;height:46px;border:1px solid #fff;border-radius:8px;background:#fff;color:#000;font-size:16px;font-weight:500;text-align:center;outline:none;">
            <span style="color:#fff;font-size:16px;">kg</span>
          </div>
        </div>
        <div style="flex-shrink:0;padding:10px 10px 14px 10px;"><button onclick="save1RM()" style="width:100%;padding:13px 32px;border-radius:8px;border:1px solid #fff;background:transparent;color:#fff;font-size:13px;font-weight:700;cursor:pointer;">Save</button></div>
        <div class="cscroll" style="flex:1;overflow-y:auto;padding:0 10px 16px 10px;">${recs}</div>
      </div>`;
    }
    async function save1RM(){
      const wt=parseFloat(document.getElementById('orm-wt')?.value||'0');
      if(!ormSelId){showMsg('Oops','Please select an exercise.',true);return;}
      if(!wt||wt<=0){showMsg('Oops','Please enter a valid weight.',true);return;}
      if(!MEMID){showMsg('Error','Member profile not found.',true);return;}
      try{
        const r=await fetch('/mobile/1rm/insert',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({workout_id:parseInt(ormSelId),member_id:MEMID,weight:wt,workoutname:ormSelName,selected_day:dayStr()})});
        const d=await r.json();
        if(d.success){showMsg('Success',d.message||'Saved!');ormWt='';ormSelId=null;ormSelName='';await fetchOrmRec();renderOrm();}
        else{showMsg('Error',d.message||'Failed.',true);}
      }catch(e){showMsg('Error','Network error.',true);}
    }
    async function del1RM(id){
      if(!confirm('Delete this 1RM record?'))return;
      try{const r=await fetch('/mobile/1rm/delete',{method:'DELETE',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({test_id:id})});const d=await r.json();if(d.success){ormRec=ormRec.filter(r=>r.id!==id);renderOrm();}else{showMsg('Error',d.message||'Could not delete.',true);}}catch(e){}
    }
    </script>
@endsection


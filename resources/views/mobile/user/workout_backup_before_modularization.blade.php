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

    function bannerHtml(fmtLabel) {
      const lbl = fmtLabel ? String(fmtLabel).replace(/-/g, ' ').toLowerCase() : 'workout';
      return `<div class="wk-banner" style="position:absolute; inset:0; z-index:50; display:flex; flex-direction:column; align-items:center; justify-content:center; pointer-events:none; background:rgba(0,0,0,0.6); border:1.5px solid rgba(242,153,74,0.3); border-radius:0; overflow:hidden;">
        <div style="width:60px; height:60px; border-radius:50%; background:#F2994A; display:flex; align-items:center; justify-content:center; margin-bottom:10px; box-shadow:0 4px 12px rgba(242,153,74,0.4);">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
        <div style="color:#ffffff; font-weight:800; font-size:22px; letter-spacing:0.8px; text-shadow:0 2px 6px rgba(0,0,0,0.8); text-transform:lowercase; font-family:inherit; line-height:1.2; text-align:center;">
          ${lbl} completed
        </div>
      </div>`;
    }

    function wkCard(wId, fmtLabel, headerExtra, bodyHtml, isCompleted = false, timeTaken = null){
      const sel = selId === wId ? 'selected' : '';
      const bHtml = isCompleted ? bannerHtml(fmtLabel) : '';
      const hasRepsRightSpacer = (fmtLabel === 'Pyramid' || fmtLabel === 'Straight sets' || fmtLabel === 'Straight-Sets' || fmtLabel === 'Straight Sets');
      return `<div class="wk-card ${sel}" id="wk-${wId}" style="position:relative;">
        <div class="wk-hdr"><div style="display:flex;align-items:center;gap:8px;"><span class="wk-fmt">${fmtLabel}</span>${headerExtra}</div><div style="flex:1;display:flex;justify-content:flex-end;padding-right:10px;transform:translateX(12px);"><span class="wk-rhdr" style="min-width:28px;text-align:center;padding:0 4px;">Reps</span></div>${hasRepsRightSpacer ? '<div style="width:35px;flex-shrink:0;"></div>' : ''}</div>
        ${bodyHtml}
        <div id="banner-${wId}">${bHtml}</div>
      </div>`;
    }

    function exRow(wId,exId,setId,name,wDisp,reps,done,hasMinus=true){
      const repsDisp = (!reps || reps === 0 || reps === '0') ? '---' : reps;
      return `<div class="ex-row" style="margin-bottom:12px;"><span class="ex-name" style="flex:2;padding-left:12px;">${name}</span><div style="flex:1;display:flex;justify-content:center;align-items:center;"><span class="wbox" style="flex:none;min-width:60px;">${wDisp}</span></div>
        <div class="rbtn-wrap" style="transform:translateX(12px);">
          ${hasMinus?`<button class="rbtn" onclick="event.stopPropagation();chgRep(${wId},${exId},${setId},-1)">-</button>`:''}
          <span class="rbox" id="reps-${wId}-${exId}-${setId}">${repsDisp}</span>
          <button class="rbtn" onclick="event.stopPropagation();chgRep(${wId},${exId},${setId},1)" style="${hasMinus?'':'display:none;'}">+</button>
        </div></div>`;
    }

    function plainExRow(name,wDisp,reps){
      const repsDisplay = (!reps || reps === 0 || reps === '0')
        ? '<span style="background:transparent; color:#fff; font-size:16px; font-weight:600; min-width:28px; text-align:center; display:inline-block;">---</span>'
        : `<span class="rbox" style="background:#fff; color:#000; padding:2px 8px; border-radius:4px; font-size:14px; font-weight:700; display:inline-block; text-align:center; min-width:28px;">${reps}</span>`;
      return `<div class="ex-row" style="margin-bottom:12px;">
        <span class="ex-name" style="flex:2; padding-left:12px;">${name}</span>
        <div style="flex:1; display:flex; justify-content:flex-end; align-items:center; padding-right:12px;">
          <span style="color:#fff; font-size:14px; font-weight:600;">${wDisp}</span>
        </div>
        <div class="rbtn-wrap" style="transform:translateX(12px); flex:0.6; display:flex; justify-content:flex-end; align-items:center; padding-right:12px;">
           ${repsDisplay}
        </div>
      </div>`;
    }

    function emomExRow(name, wDisp, reps){
      const repsDisplay = (!reps || reps === 0 || reps === '0')
        ? '<span style="background:transparent; color:#fff; font-size:16px; font-weight:600; width:45px; text-align:center; display:inline-block;">---</span>'
        : `<span class="rbox" style="background:#fff; color:#000; border-radius:4px; font-size:14px; font-weight:700; width:45px; height:24px; display:inline-flex; align-items:center; justify-content:center; text-align:center; box-sizing:border-box;">${reps}</span>`;

      return `<div class="ex-row" style="margin-bottom:12px; padding:4px 0; display:flex; align-items:center;">
        <span class="ex-name" style="flex:2; padding-left:12px; color:#fff; font-size:14px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${name}</span>
        <div style="flex:1; display:flex; justify-content:flex-end; align-items:center; padding-right:16px;">
          <span style="color:#fff; font-size:14px; font-weight:600; white-space:nowrap;">${wDisp}</span>
        </div>
        <div class="rbtn-wrap" style="transform:translateX(12px); flex:0.6; display:flex; justify-content:flex-end; align-items:center; padding-right:12px;">
          ${repsDisplay}
        </div>
      </div>`;
    }

    function amrapExRow(name, wDisp, reps, isDone=false, isIncomp=false){
      let repsDisplay;
      if (isDone) {
        repsDisplay = '<span style="color:#fff; font-size:14px; font-weight:400; text-align:center;">COMPLETED</span>';
      } else if (isIncomp) {
        repsDisplay = '<span style="color:#fca5a5; font-size:14px; font-weight:bold; text-align:center;">INCOMPLETE</span>';
      } else if (!reps || reps === 0 || reps === '0') {
        repsDisplay = '<span style="background:transparent; color:#fff; font-size:16px; font-weight:600; width:45px; text-align:center; display:inline-block; padding-right:4px;">---</span>';
      } else {
        repsDisplay = `<span class="rbox" style="background:#fff; color:#000; border-radius:4px; font-size:14px; font-weight:700; width:45px; height:24px; display:inline-flex; align-items:center; justify-content:center; text-align:center; box-sizing:border-box;">${reps}</span>`;
      }

      return `<div class="ex-row" style="margin-bottom:12px; padding:4px 0; display:flex; align-items:center; justify-content:space-between;">
        <span class="ex-name" style="flex:2; padding-left:12px; color:#fff; font-size:14px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${name}</span>
        <div style="flex:1; display:flex; justify-content:flex-end; align-items:center; padding-right:16px;">
          <span style="color:#fff; font-size:14px; font-weight:600; white-space:nowrap;">${wDisp}</span>
        </div>
        <div class="rbtn-wrap" style="transform:translateX(12px); flex:0.6; display:flex; justify-content:flex-end; align-items:center; padding-right:12px;">
          ${repsDisplay}
        </div>
      </div>`;
    }

    function playTimerBeep(freq = 600, duration = 0.12) {
      try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (AudioCtx) {
          const ctx = new AudioCtx();
          const osc = ctx.createOscillator();
          const gain = ctx.createGain();
          osc.type = 'sine';
          osc.frequency.setValueAtTime(freq, ctx.currentTime);
          gain.gain.setValueAtTime(0.25, ctx.currentTime);
          gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration);
          osc.connect(gain);
          gain.connect(ctx.destination);
          osc.start();
          osc.stop(ctx.currentTime + duration);
          return;
        }
      } catch(e) {}
      try {
        const audio = new Audio('/audio/beep.mp3');
        audio.play().catch(()=>{});
      } catch(e) {}
    }

    // ─── STRAIGHT SETS ─────────────────────────────────────────────
    function cardSS(w){
      const straights=w.straights||[];
      const totalSets=straights.length>0&&straights[0].sets?straights[0].sets.length:0;
      let activeSetIdx=-1;
      for(let i=0;i<totalSets;i++){
        const isDone=straights.every(ex=>{
          const s=ex.sets&&ex.sets[i];
          if(!s)return true;
          return s.is_completed==1||!!CT[`${w.id}-${ex.id||0}-${s.id||i}`];
        });
        if(!isDone){activeSetIdx=i;break;}
      }
      const hasMissing1RM = straights.some(ex => {
        const sets = ex.sets || [];
        return sets.some(s => {
          const u = (s.unittype || s.unit_type || ex.unittype || ex.unit_type || '%').trim().toUpperCase();
          const ldVal = s.training_load ?? ex.training_load ?? ex.weight ?? 0;
          const tw = ex.test_weight ?? w.test_weight ?? 0;
          return u === '%' && ldVal > 0 && (!tw || tw === 0);
        });
      });
      const warnHtml = hasMissing1RM ? `<div style="color:#fca5a5; font-size:13px; margin-bottom:12px; padding:0 4px;">⚠️ Please add your 1RM for accurate weight calculation.</div>` : '';

      let rows='';
      for(let si=0;si<totalSets;si++){
        let exHtml='';
        straights.forEach(ex=>{
          const sets=ex.sets||[],set=sets[si]||{};
          const nm=exName(ex);
          // StraightSet DB column is 'res' for reps, 'unittype' (no underscore) for unit
          // API also sets 'target_reps' explicitly on each set
          const tr=set.res??set.target_reps??set.reps??ex.reps??0;
          let sr=set.daily_reps??set.reps_progress??tr;
          if(sr===0){
            if(tr>0) sr=tr;
            else if(si>0){
              const ps=ex.sets[si-1];
              if(ps){
                const pt=ps.res??ps.target_reps??ps.reps??ex.reps??0;
                sr=ps.daily_reps??ps.reps_progress??pt;
              }
            }
          }
          const ld=set.training_load??ex.training_load??ex.weight??0;
          // StraightSet uses 'unittype' not 'unit_type', default '%' matching mobile app
          const unit=(set.unittype||set.unit_type||ex.unittype||ex.unit_type||'%').trim().toUpperCase();

          // Cumulative missed reps for FRM (exact copy of mobile app logic)
          const cumulativeMissedReps = (ex.sets || [])
            .slice(0, si)
            .reduce((acc, prevSet, prevIdx) => {
              const prevKey = `${w.id}-${ex.id||0}-${prevSet.id||prevIdx}`;
              const apiComp = w.type_completed == 1 || prevSet.is_completed == 1;
              const isPrevCompleted = apiComp || !!CT[prevKey];
              if (isPrevCompleted) {
                const pTarget = prevSet.res ?? prevSet.target_reps ?? prevSet.reps ?? ex.reps ?? 0;
                const pReps = prevSet.daily_reps ?? prevSet.reps_progress ?? pTarget;
                const missed = Math.max(0, pTarget - pReps);
                return acc + missed;
              }
              return acc;
            }, 0);

          const dailySavedWeight = (set.daily_weight != null && Number(set.daily_weight) > 0)
            ? set.daily_weight : null;
          const wd = dailySavedWeight !== null
            ? `${Math.round(dailySavedWeight)} KG`
            : dispWt(ld, unit, ex.test_weight ?? w.test_weight ?? 0, cumulativeMissedReps);

          const noReps=unit==='CAL'||unit==='M';
          if(noReps){
            exHtml+=`<div class="ex-row" style="margin-bottom:12px;"><span class="ex-name" style="flex:2;padding-left:12px;">${nm}</span><div style="flex:1;display:flex;justify-content:center;align-items:center;"><span class="wbox" style="flex:none;min-width:60px;">${wd}</span></div><div class="rbtn-wrap" style="transform:translateX(12px);flex:1;display:flex;justify-content:flex-end;align-items:center;padding-right:10px;"><span class="rbtn" style="opacity:0;font-size:16px;padding:0 6px;">-</span><span class="rbox" style="background:transparent;color:#fff;width:30px;white-space:nowrap;display:inline-block;text-align:center;font-size:16px;margin:0 2px;">---</span></div></div>`;
          } else {
            exHtml+=`<div class="ex-row" style="margin-bottom:12px;"><span class="ex-name" style="flex:2;padding-left:12px;">${nm}</span><div style="flex:1;display:flex;justify-content:center;align-items:center;"><span class="wbox" style="flex:none;min-width:60px;padding:2px 2px;">${wd}</span></div><div class="rbtn-wrap" style="transform:translateX(12px);flex:1;display:flex;justify-content:flex-end;align-items:center;padding-right:10px;"><button class="rbtn" style="font-size:16px;padding:0 6px;" onclick="event.stopPropagation();chgRep(${w.id},${ex.id||0},${set.id||si},-1)">-</button><span class="rbox" id="reps-${w.id}-${ex.id||0}-${set.id||si}" style="margin:0 2px;width:30px;display:inline-block;text-align:center;">${sr}</span></div></div>`;
          }
        });
        const isLast=si===totalSets-1;
        rows+=`<div class="${isLast?'':'set-grp'}" ${isLast?'':'style="border-bottom:1px solid rgba(255,255,255,0.15);margin-bottom:16px;padding-bottom:16px;"'}> <div class="${si===activeSetIdx?'active-ex-row':''}" style="display:flex;align-items:center;margin-bottom:0;gap:0px;"><span class="set-num">${si+1}</span><div style="flex:1;">${exHtml}</div></div></div>`;
      }
      const doneCount=Object.keys(CT).filter(k=>k.startsWith(`${w.id}-`)&&CT[k]).length;
      const ts=TS[w.id]=TS[w.id]||{elapsed:0,running:false,paused:false,interval:null,allDone:false};
      const isComp=(w.type_completed==1||w.status==='completed'||ts.allDone||(totalSets>0&&doneCount>=totalSets));
      if(isComp) ts.allDone=true;
      return wkCard(w.id,'Straight Sets',`<span class="wk-pbadge" id="ss-prog-${w.id}">${doneCount}/${totalSets}</span>`,warnHtml+rows,isComp,ts.timeTaken);
    }

    // ─── ROUNDS ────────────────────────────────────────────────────
    function cardRounds(w){
      const exs=w.rounds||[];
      const total=w.number||0;
      const lim=parseSec(w.time_to_complete||(exs[0]&&exs[0].time_to_complete))||600;
      let dbCr = 0; let allD = (w.type_completed==1||w.status==='completed'||(exs.length>0&&exs.every(ex=>ex.is_completed==1)));
      if(exs[0] && exs[0].round_number) {
          const p = String(exs[0].round_number).split('/');
          if(p.length>0) dbCr = parseInt(p[0])||0;
          if(total>0 && dbCr>=total) allD = true;
      }
      const ts=TS[w.id]=TS[w.id]||{elapsed:0,running:false,paused:false,interval:null,limit:lim,completedRounds:dbCr,allDone:allD};
      if(ts.completedRounds === 0 && dbCr > 0) ts.completedRounds = dbCr;
      if(allD || ts.allDone || (total>0 && (ts.completedRounds||0)>=total)) ts.allDone = true;
      const cr=ts.completedRounds||0;
      const isComp=(w.type_completed==1||w.status==='completed'||ts.allDone||(total>0&&cr>=total));
      const sMap={};exs.forEach(ex=>{const sn=ex.stationumber??1;if(!sMap[sn])sMap[sn]=[];sMap[sn].push(ex);});
      const sns=Object.keys(sMap).sort((a,b)=>a-b);
      const stHtml=sns.map(sn=>{const exRows=sMap[sn].map(ex=>plainExRow(exName(ex),dispWt(ex.training_load??0,ex.unit_type||'%',ex.test_weight??0),ex.reps??0)).join('');return sns.length>1?`<div class="stn-block"><div class="stn-lbl">Station ${sn}</div>${exRows}</div>`:exRows;}).join('');
      
      const progStr = `${cr}${total>0?'/'+total:''}`;
      const headerExtra = `<span id="rounds-prog-${w.id}" style="background:rgba(255,255,255,0.2); padding:2px 10px; border-radius:12px; font-size:12px; font-weight:600; color:#fff; display:inline-block;">${progStr}</span>`;
      
      const hasMissing1RM = exs.some(ex => String(ex.unit_type || "%").trim() === "%" && ex.training_load > 0 && (!ex.test_weight || ex.test_weight === 0));
      const warnHtml = hasMissing1RM ? `<div style="color:#fca5a5; font-size:13px; margin-bottom:12px; padding:0 4px;">⚠️ Please add your 1RM for accurate weight calculation.</div>` : '';
      const pbarWidth = total>0 ? (cr/total)*100 : 0;
      const pbarHtml = `${warnHtml}<div style="height:4px; width:100%; background:rgba(255,255,255,0.2); border-radius:2px; margin-bottom:16px; margin-top:2px; overflow:hidden;"><div id="rounds-pbar-${w.id}" style="height:100%; width:${pbarWidth}%; background:#fff; transition:width 0.3s ease;"></div></div>`;
      
      return wkCard(w.id, 'Rounds', headerExtra, pbarHtml + stHtml, isComp, ts.timeTaken);
    }

    // ─── AMRAP ─────────────────────────────────────────────────────
    function cardAmrap(w){
      const exs=w.amraps||[];
      const amSec=getAmrapSec(w);
      let rd = exs[0]?.rounds_done||0;
      let rt = exs[0]?.rounds_total||0;
      let allD = (w.type_completed==1||w.status==='completed'||(exs.length>0&&exs.every(ex=>ex.is_completed==1)));
      const ts=TS[w.id]=TS[w.id]||{elapsed:0,running:false,paused:false,interval:null,limit:amSec,rounds:rd,totalRounds:rt,allDone:allD};
      if(!ts.running && !ts.allDone) ts.limit = amSec;
      if(ts.rounds === 0 && rd > 0) ts.rounds = rd;
      if(ts.totalRounds === 0 && rt > 0) ts.totalRounds = rt;
      if(allD || ts.allDone) ts.allDone = true;
      const isComp = (w.type_completed==1||w.status==='completed'||ts.allDone);
      
      const headerExtra = `<span id="amrap-rounds-${w.id}" class="round-badge" style="background:rgba(255,255,255,0.15); border-radius:12px; padding:2px 10px; font-size:13px; font-weight:600; color:#fff; display:inline-block; margin-left:2px;">${ts.rounds||0}</span>`;
      
      const hasMissing1RM = exs.some(ex => String(ex.unit_type || "%").trim() === "%" && ex.training_load > 0 && (!ex.test_weight || ex.test_weight === 0));
      const warnHtml = hasMissing1RM ? `<div id="amrap-warn-${w.id}" style="color:#fca5a5; font-size:13px; margin-bottom:12px; padding:0 4px;">⚠️ Please add your 1RM for accurate weight calculation.</div>` : '';
      
      if(hasMissing1RM) {
        setTimeout(() => {
          const el = document.getElementById('amrap-warn-' + w.id);
          if (el) el.style.display = 'none';
        }, 5000);
      }

      const exHtml = exs.map(ex => {
        const isDone = isComp || ex.is_completed == 1;
        const isIncomp = (wkStatus === 'incompleted');
        return amrapExRow(
          exName(ex),
          dispWt(ex.training_load??0, ex.unit_type||'%', ex.test_weight??0),
          ex.reps??0,
          isDone,
          isIncomp
        );
      }).join('');

      return wkCard(w.id, 'AMRAP', headerExtra, warnHtml + exHtml, isComp, ts.timeTaken);
    }

    // ─── FOR TIME ──────────────────────────────────────────────────
    function cardFT(w){
      const exs=w.for_times||[];
      const cap=parseSec(w.time_to_complete||(exs[0]&&exs[0].time_to_complete));
      let allD = (w.type_completed==1||w.status==='completed'||(exs.length>0&&exs.every(ex=>ex.is_completed==1)));
      const ts=TS[w.id]=TS[w.id]||{elapsed:0,running:false,paused:false,interval:null,limit:cap,allDone:allD};
      if(allD || ts.allDone) ts.allDone = true;
      const isComp = (w.type_completed==1||w.status==='completed'||ts.allDone);
      const td=cap>0?fmt(Math.max(0,cap-ts.elapsed)):fmt(ts.elapsed);
      const exHtml=exs.map((ex,i)=>{
        const tr=ex.reps_progress||ex.res||ex.reps||0;
        const repsDisp=(tr===0||tr==='0')?'---':tr;
        return`<div class="ex-row"><span class="ex-name">${exName(ex)}</span><span class="wbox">${dispWt(ex.training_load??0,ex.unit_type||'%',ex.test_weight??0)}</span><div class="rbtn-wrap"><button class="rbtn" onclick="event.stopPropagation();chgFTRep(${w.id},${ex.id},-1)">-</button><span class="rbox" id="ft-reps-${w.id}-${ex.id}">${repsDisp}</span><button class="rbtn" onclick="event.stopPropagation();chgFTRep(${w.id},${ex.id},1)">+</button></div></div>`;
      }).join('');
      const timerBlock=`<div id="ft-tmr-${w.id}" class="card-timer" style="${selId===w.id?'':'display:none;'}"><div id="ft-t-${w.id}" class="ct-time">${td}</div><div class="ct-label">${cap>0?'TIME REMAINING':'ELAPSED'}</div><div class="ct-btns"><button class="ctbtn danger" onclick="event.stopPropagation();stopFT(${w.id})">STOP</button><button class="ctbtn primary" id="ft-pb-${w.id}" onclick="event.stopPropagation();toggleFT(${w.id})">${ts.running?'PAUSE':'START'}</button></div></div>`;
      return wkCard(w.id,'For Time',cap>0?`<span style="color:rgba(255,255,255,.5);font-size:11px;">Cap: ${fmt(cap)}</span>`:'',timerBlock+exHtml,isComp,ts.timeTaken);
    }

    // ─── EMOM ──────────────────────────────────────────────────────
    function cardEmom(w){
      const exs=w.emoms||[];
      const totalR=Number(w.number)>0?Number(w.number):(exs.length||1);

      // Parse roundDuration from first exercise exercise_time if present, default 60s
      const rawTime=exs[0]?.exercise_time??1;
      let roundDur=60;
      const strTime=String(rawTime).trim();
      if(strTime.includes(':')){
        const parts=strTime.split(':').map(Number);
        if(parts.length===3) roundDur=(parts[0]||0)*3600+(parts[1]||0)*60+(parts[2]||0);
        else if(parts.length===2) roundDur=(parts[0]||0)*60+(parts[1]||0);
      } else {
        const numTime=Number(strTime);
        if(!isNaN(numTime)&&numTime>0) roundDur=numTime>10?numTime:numTime*60;
      }
      if(roundDur<=0) roundDur=60;

      // Check DB completion status
      let dbCr = 0;
      let allD = (w.type_completed==1 || w.status==='completed' || (exs.length>0 && exs.every(ex=>ex.is_completed==1)));
      if(exs[0] && exs[0].round_number){
        const p = String(exs[0].round_number).split('/');
        if(p.length===2){
          dbCr = parseInt(p[0])||0;
          if(totalR>0 && dbCr>=totalR) allD = true;
        }
      }

      const ts=TS[w.id]=TS[w.id]||{
        elapsed:0,
        running:false,
        paused:false,
        interval:null,
        phase:allD?'done':'idle',
        roundDuration:roundDur,
        phaseLeft:roundDur,
        round:allD?totalR:(dbCr+1),
        completedRounds:dbCr,
        totalRounds:totalR,
        allDone:allD
      };

      if(allD || ts.allDone || ts.phase==='done'){
        ts.allDone=true;
        ts.phase='done';
        ts.completedRounds=totalR;
      }

      const isComp = (w.type_completed==1 || w.status==='completed' || ts.allDone || ts.phase==='done');
      const cr = isComp ? totalR : (ts.completedRounds||0);
      const progStr = `${cr}/${totalR}`;
      const headerExtra = `<span class="wk-pbadge" id="emom-prog-${w.id}">${progStr}</span>`;

      // 1RM missing warning
      const hasMissing1RM = exs.some(ex => String(ex.unit_type || "%").trim() === "%" && (ex.training_load > 0 || ex.weight > 0) && (!ex.test_weight || ex.test_weight === 0));
      const warnHtml = hasMissing1RM ? `<div style="color:#fca5a5; font-size:13px; margin-bottom:12px; padding:0 4px;">⚠️ Please add your 1RM for accurate weight calculation.</div>` : '';

      // Progress bar (matching mobile app 8px height, rounded 5px)
      const pbarWidth = totalR>0 ? Math.min(100, (cr/totalR)*100) : 0;
      const pbarHtml = `${warnHtml}<div style="height:8px; width:100%; background:rgba(255,255,255,0.2); border-radius:5px; margin-bottom:12px; overflow:hidden;"><div id="emom-pbar-${w.id}" style="height:100%; width:${pbarWidth}%; background:#FFFFFF; border-radius:5px; transition:width 0.3s ease;"></div></div>`;

      // Exercise rows
      const isWorkPhase = ts.phase==='work';
      const exHtml = exs.map(ex=>{
        const nm = exName(ex);
        const wd = dispWt(ex.training_load??ex.weight??0, ex.unit_type||'%', ex.test_weight??0);
        const reps = ex.reps??0;
        return emomExRow(nm, wd, reps);
      }).join('');

      const exContainer = `<div id="emom-ex-cont-${w.id}" style="border-radius:8px; padding:4px 0; transition:background .2s; ${isWorkPhase?'background:rgba(255,255,255,0.1);':''}">${exHtml}</div>`;

      return wkCard(w.id, 'EMOM', headerExtra, pbarHtml + exContainer, isComp, ts.timeTaken);
    }

    // ─── INTERVALS ─────────────────────────────────────────────────
    function cardIntervals(w){
      const exs=w.intervals||[];
      const totalR=w.number||exs.length||1;
      const fe=exs[0]||{};
      const wS=parseSec(fe.work||w.work)||40;
      const rS=parseSec(fe.rest||w.rest)||20;
      let allD = (w.type_completed==1 || w.status==='completed' || (exs.length>0 && exs.every(ex=>ex.is_completed==1)));
      const ts=TS[w.id]=TS[w.id]||{elapsed:0,running:false,paused:false,interval:null,phase:allD?'done':'idle',phaseLeft:wS,round:1,totalRounds:totalR,workSecs:wS,restSecs:rS,allDone:allD};
      if(allD || ts.allDone || ts.phase==='done') ts.allDone = true;
      const isComp = (w.type_completed==1 || w.status==='completed' || ts.allDone || ts.phase==='done');
      const iMap={};exs.forEach(ex=>{const n=ex.stationumber??ex.station??ex.number??1;if(!iMap[n])iMap[n]=[];iMap[n].push(ex);});
      const iNums=Object.keys(iMap).sort((a,b)=>a-b);
      let iHtml=iNums.map((n, idx)=>{
        const exRows=iMap[n].map(ex=>plainExRow(exName(ex),dispWt(ex.training_load??0,ex.unit_type||'%',ex.test_weight??0),ex.reps??0)).join('');
        const divider = idx < iNums.length - 1 ? `<div style="height:1px; background:rgba(255,255,255,0.2); width:100%; margin:4px 0 16px 0;"></div>` : '';
        return `<div class="grp-row"><div class="grp-num">${idx+1}</div><div class="grp-exs" style="margin-bottom:0;">${exRows}</div></div>${divider}`;
      }).join('');
      
      const cRound = isComp ? ts.totalRounds : Math.max(0,ts.round-1);
      return wkCard(w.id,'Intervals',`<span class="wk-pbadge" id="int-prog-${w.id}">${cRound}/${ts.totalRounds}</span>`, iHtml, isComp, ts.timeTaken);
    }

    // ─── CIRCUIT ───────────────────────────────────────────────────
    function cardCircuit(w){
      const exs=w.circuits||[];
      const lim=parseSec(w.time_to_complete||(exs[0]&&exs[0].time_to_complete));
      const isForTime=Number(w.is_for_time||0)===1||Number(w.is_fortime||0)===1||Number(w.for_time||0)===1||exs.some(e=>Number(e.is_for_time||0)===1||Number(e.is_fortime||0)===1||Number(e.for_time||0)===1);
      
      const sMap={};
      exs.forEach(ex=>{
        const sn=ex.stationumber??ex.station_number??ex.station??1;
        if(!sMap[sn])sMap[sn]=[];
        sMap[sn].push(ex);
      });
      const sns=Object.keys(sMap).sort((a,b)=>a-b);

      const compStations = sns.filter(sn => {
        return w.type_completed == 1 || w.status === 'completed' ||
          sMap[sn].every(ex => ex.is_completed == 1 || !!CT[`${w.id}-stn-${sn}`] || !!CT[`${w.id}-${ex.id}`]);
      }).length;

      let allD = (w.type_completed==1 || w.status==='completed' || (sns.length > 0 && compStations >= sns.length) || (exs.length>0 && exs.every(ex=>ex.is_completed==1)));
      const ts=TS[w.id]=TS[w.id]||{elapsed:0,running:false,paused:false,interval:null,limit:lim,allDone:allD,completedStations:compStations};
      if(allD || ts.allDone) ts.allDone = true;
      const isComp = (w.type_completed==1 || w.status==='completed' || ts.allDone || (sns.length > 0 && compStations >= sns.length));

      const stHtml=sns.map((sn, idx)=>{
        const exRows=sMap[sn].map(ex=>{
          const nm=exName(ex);
          const wd=dispWt(ex.training_load??ex.weight??0,ex.unit_type||'%',ex.test_weight??0);
          const reps=ex.reps_progress??ex.res??ex.reps??0;
          return plainExRow(nm,wd,reps);
        }).join('');
        const divider = idx < sns.length - 1
          ? `<div style="height:1px; background:rgba(255,255,255,0.2); width:100%; margin:4px 0 16px 0;"></div>`
          : '';
        return `<div class="grp-row" style="display:flex;align-items:flex-start;margin-bottom:0;width:100%;">
          <div class="grp-num" style="width:24px;display:flex;align-items:flex-start;justify-content:flex-start;padding-top:1px;color:#fff;font-weight:600;font-size:16px;">${idx + 1}</div>
          <div class="grp-exs" style="margin-bottom:0;flex:1;">
            ${exRows}
          </div>
        </div>${divider}`;
      }).join('');

      const cDisplay = isComp ? sns.length : compStations;
      const headerExtra = `<span class="wk-pbadge" id="circuit-prog-${w.id}">${cDisplay}/${Math.max(sns.length, 1)}</span>`;

      return wkCard(w.id,'Circuit',headerExtra,stHtml,isComp,ts.timeTaken);
    }

    // ─── PYRAMID ───────────────────────────────────────────────────
    function cardPyramid(w){
      const rows=w.pyramids||w.pyramid||[];
      const doneC=rows.filter(r=>r.is_completed==1||!!CT[`${w.id}-${r.id}`]||w.type_completed==1).length;
      let allD = (w.type_completed==1 || w.status==='completed' || (rows.length>0 && doneC>=rows.length));
      const ts=TS[w.id]=TS[w.id]||{elapsed:0,running:false,paused:false,interval:null,allDone:allD};
      if(allD || ts.allDone) ts.allDone = true;
      const isComp = (w.type_completed==1 || w.status==='completed' || ts.allDone || (rows.length>0 && doneC>=rows.length));

      // 5-second auto-hide 1RM warning
      const hasMissing1RM = rows.some(row => {
        const load = Number(row.training_load ?? row.weight ?? w.training_load ?? 0);
        const testWeight = row.test_weight ?? w.test_weight ?? 0;
        const u = String(row.unit_type ?? w.unit_type ?? '%').trim();
        return u === '%' && load > 0 && Number(testWeight) === 0;
      });
      if (hasMissing1RM && pyrWarningVisible[w.id] === undefined) {
        pyrWarningVisible[w.id] = true;
        setTimeout(() => {
          pyrWarningVisible[w.id] = false;
          const el = document.getElementById(`pyr-warn-${w.id}`);
          if (el) el.style.display = 'none';
        }, 5000);
      }
      const warnHtml = (hasMissing1RM && pyrWarningVisible[w.id] !== false)
        ? `<div id="pyr-warn-${w.id}" style="color:#fca5a5;font-size:13px;margin-bottom:12px;padding:0 4px;">⚠️ Please add your 1RM for accurate weight calculation.</div>`
        : '';

      const currentActiveRowIndex = rows.findIndex(row => {
        const tk = `${w.id}-${row.id}`;
        const isApiCompleted = w.type_completed == 1 || row.is_completed == 1;
        const isSetCompleted = isApiCompleted || !!CT[tk];
        return !isSetCompleted;
      });

      const rHtml = rows.map((row, index) => {
        const tk = `${w.id}-${row.id}`;
        const isApiCompleted = w.type_completed == 1 || row.is_completed == 1;
        const isSetCompleted = isApiCompleted || !!CT[tk];
        const rk = `pyr-${w.id}-${row.id}`;
        const rst = RST[rk];
        const isCurrentTarget = (index === currentActiveRowIndex);
        const isActive = ((rst && rst.running) || isCurrentTarget) && !isSetCompleted;

        const library = row.workout_library;
        const workoutName = exName(row) || (library && library.workout) || 'Exercise';
        const unitType = (row.unittype || row.unit_type || w.unit_type || '%').trim().toUpperCase();
        const load = Number(row.training_load ?? row.weight ?? w.training_load ?? 0);
        const weightDisp = dispWt(load, unitType, row.test_weight ?? w.test_weight ?? 0);
        const repsVal = row.reps_progress ?? row.reps ?? 0;

        return `<div class="pyr-row-wrap" id="pyr-row-${w.id}-${row.id}" onclick="event.stopPropagation();pyrClick(${w.id},${row.id})"
          style="width:100%;padding:4px 0;margin-bottom:2px;cursor:pointer;${isActive ? 'background:rgba(255,255,255,0.15);border-radius:8px;' : ''}">
          <div style="display:flex;flex-direction:row;align-items:center;justify-content:space-between;width:100%;min-height:30px;">
            <div style="flex:2;text-align:left;color:#fff;font-size:14px;font-weight:600;padding-left:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
              ${workoutName}
            </div>
            <div style="flex:1;text-align:right;color:#fff;font-size:13px;font-weight:600;padding-right:0;font-variant-numeric:tabular-nums;">
              ${weightDisp}
            </div>
            <div style="flex:1;display:flex;justify-content:flex-end;align-items:center;padding-right:10px;transform:translateX(12px);">
              ${Number(repsVal) === 0 ? `
                <span style="background:transparent;color:#fff;font-size:16px;font-weight:600;min-width:28px;text-align:center;padding:0 4px;display:inline-block;">---</span>
              ` : `
                <span style="background:#fff;color:#000;padding:2px 4px;border-radius:4px;font-size:14px;font-weight:bold;min-width:28px;text-align:center;display:inline-block;line-height:1.2;">${repsVal}</span>
              `}
            </div>
            <div style="width:35px;height:30px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              ${isSetCompleted ? `
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              ` : ''}
            </div>
          </div>
        </div>`;
      }).join('');

      const bodyHtml = `${warnHtml}${rHtml}`;
      const headerExtra = `<span class="wk-pbadge" id="pyr-prog-${w.id}">${doneC}/${rows.length}</span>`;
      return wkCard(w.id, 'Pyramid', headerExtra, bodyHtml, isComp, ts.timeTaken);
    }

    // ─── GENERIC ───────────────────────────────────────────────────
    function cardGeneric(w,fk){
      const nm=fk?fk.replace(/-/g,' ').replace(/\b\w/g,c=>c.toUpperCase()):'Workout';
      const exs=w.straights||w.rounds||w.amraps||w.emoms||w.for_times||w.intervals||w.circuits||w.pyramids||[];
      const title=exName(exs[0]||{})||w.workout_name||'Workout';
      const isComp = (w.type_completed==1 || w.status==='completed' || (TS[w.id] && TS[w.id].allDone));
      return wkCard(w.id,nm,'',`<div style="color:#fff;font-size:16px;font-weight:700;">${title}</div>`,isComp,TS[w.id]?.timeTaken);
    }

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

    function updCenterBtn(w,fk,ts){
      const bc=document.getElementById('btn-center');if(!bc)return;
      if(fk === 'circuit'){
        const isForTime = Number(w.is_for_time||0)===1 || Number(w.is_fortime||0)===1 || Number(w.for_time||0)===1 || (w.circuits||[]).some(e=>Number(e.is_for_time||0)===1 || Number(e.is_fortime||0)===1 || Number(e.for_time||0)===1);
        if(!isForTime){
          bc.style.background = '#CD7F32';
          bc.style.borderColor = '#CD7F32';
          bc.innerHTML = `<span style="font-size:22px;font-weight:700;letter-spacing:2px;font-variant-numeric:tabular-nums;color:#fff;">00:00</span>`;
          return;
        }
      }
      const isComp = (wkStatus==='completed' || w.type_completed==1 || w.status==='completed' || ts.allDone || ts.phase==='done');
      if(isComp){
        let compSec = ts.elapsed || 0;
        if(compSec <= 0 && ts.timeTaken){
          compSec = parseSec(ts.timeTaken);
        }
        if(compSec <= 0){
          if(fk === 'emom'){
            const exs = w.emoms || [];
            const tr = Number(w.number) > 0 ? Number(w.number) : (exs.length || 1);
            const rawTime = exs[0]?.exercise_time ?? 1;
            let rd = 60;
            const strTime = String(rawTime).trim();
            if(strTime.includes(':')){
              const p = strTime.split(':').map(Number);
              if(p.length === 3) rd = (p[0]||0) * 3600 + (p[1]||0) * 60 + (p[2]||0);
              else if(p.length === 2) rd = (p[0]||0) * 60 + (p[1]||0);
            } else {
              const nt = Number(strTime);
              if(!isNaN(nt) && nt > 0) rd = nt > 10 ? nt : nt * 60;
            }
            compSec = tr * rd;
          } else if(fk === 'amrap'){
            compSec = getAmrapSec(w);
          } else if(fk === 'intervals'){
            const fe = (w.intervals || [])[0] || {};
            const wS = parseSec(fe.work || w.work) || 40;
            const rS = parseSec(fe.rest || w.rest) || 20;
            const tr = w.number || (w.intervals || []).length || 1;
            compSec = tr * (wS + rS);
          } else if(fk === 'rounds'){
            const first = (w.rounds || []).find(ex => ex.exercise_time && ex.exercise_time !== '00:00');
            compSec = first ? parseSec(first.exercise_time) : (parseSec(w.time_to_complete) || (ts.limit > 0 ? ts.limit : 0));
          } else if(fk === 'circuit'){
            const first = (w.circuits || []).find(ex => ex.exercise_time && ex.exercise_time !== '00:00');
            compSec = first ? parseSec(first.exercise_time) : (parseSec(w.time_to_complete) || (ts.limit > 0 ? ts.limit : 0));
          } else if(fk === 'for-time'){
            compSec = ts.limit > 0 ? ts.limit : parseSec(w.time_to_complete);
          } else if(w.time_to_complete){
            compSec = parseSec(w.time_to_complete);
          } else if(w.exercise_time){
            compSec = parseSec(w.exercise_time);
          }
        }
        bc.style.background = '#CD7F32';
        bc.style.borderColor = '#CD7F32';
        bc.innerHTML = `<span style="font-size:22px;font-weight:900;font-variant-numeric:tabular-nums;color:#fff;">${fmt(compSec)}</span>`;
        return;
      }
      if(fk==='pyramid'){
        const ak=Object.keys(RST).find(k=>k.startsWith(`pyr-${w.id}-`)&&RST[k]?.running);
        if(ak){
          const rst=RST[ak];
          bc.style.background=rst.color||'#CD7F32';
          bc.style.borderColor=rst.color||'#CD7F32';
          bc.innerHTML=`<div style="display:flex;flex-direction:column;align-items:center;line-height:1.1;"><span style="font-size:13px;font-weight:700;color:#fff;letter-spacing:1px;">REST</span><span style="font-size:22px;font-weight:700;color:#fff;font-variant-numeric:tabular-nums;">${fmt(rst.phaseLeft||0)}</span></div>`;
          return;
        }
        bc.style.background='#CD7F32';
        bc.style.borderColor='#CD7F32';
        bc.innerHTML=`<div style="display:flex;flex-direction:column;align-items:center;line-height:1.1;"><span style="font-size:13px;font-weight:700;color:#fff;letter-spacing:1px;">REST</span><span style="font-size:22px;font-weight:700;color:#fff;font-variant-numeric:tabular-nums;">00:00</span></div>`;
        return;
      }
      if(fk==='straight-sets'){
        const ak=Object.keys(RST).find(k=>k.startsWith(`rest-${w.id}-`)&&RST[k]?.running);
        if(ak){const rst=RST[ak];bc.style.background=rst.color||'#CD7F32';bc.style.borderColor=rst.color||'#CD7F32';bc.innerHTML=`<span style="font-size:22px;font-weight:900;font-variant-numeric:tabular-nums;">${fmt(rst.phaseLeft||0)}</span>`;return;}
        if(ts.running||ts.paused){bc.style.background='#CD7F32';bc.style.borderColor='#CD7F32';bc.innerHTML=`<span style="font-size:22px;font-weight:900;font-variant-numeric:tabular-nums;">${fmt(ts.elapsed||0)}</span>`;return;}
        bc.style.background='#CD7F32';bc.style.borderColor='#CD7F32';bc.innerHTML='START';return;
      }
      if(['rounds','amrap','for-time','emom','intervals','circuit'].includes(fk)&&(ts.running||ts.paused)){
        const pc=fk==='intervals'?(ts.phase==='work'?'#2E7D32':'#C62828'):fk==='emom'?'#2E7D32':'#CD7F32';
        bc.style.background=pc;bc.style.borderColor=pc;
        let tv=ts.phaseLeft||0;if(['rounds','for-time','circuit'].includes(fk))tv=ts.limit>0?Math.max(0,ts.limit-ts.elapsed):ts.elapsed;
        else if(fk==='amrap')tv=Math.max(0,ts.limit-ts.elapsed);
        bc.innerHTML=`<span style="font-size:22px;font-weight:900;font-variant-numeric:tabular-nums;">${fmt(tv)}</span>`;return;
      }
      bc.style.background='#CD7F32';bc.style.borderColor='#CD7F32';bc.innerHTML='START';
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

    // ── COUNTDOWN ──────────────────────────────────────────────────
    function countdown10(wId,cb){
      const card=document.getElementById('wk-'+wId);if(!card){cb();return;}
      const div=document.createElement('div');div.className='cd-overlay';div.id='cd-'+wId;
      div.onclick = () => {
        clearInterval(iv);
        div.remove();
        const ts = TS[wId];
        if (ts) { ts.running = false; ts.paused = false; ts.phase = 'idle'; }
        updBbar();
      };
      const sz=186; const sw=8; const r=(sz-sw)/2; const c=2*Math.PI*r;
      const createNumSVG = (n) => `
        <svg width="140" height="110" viewBox="0 0 140 110" style="overflow:visible;">
          <text x="70" y="95" text-anchor="middle" font-size="92" font-weight="900" fill="none" stroke="#ffffff" stroke-width="18" stroke-linejoin="round" stroke-linecap="round" font-family="sans-serif">${n}</text>
          <text x="70" y="95" text-anchor="middle" font-size="92" font-weight="900" fill="none" stroke="#000000" stroke-width="11" stroke-linejoin="round" stroke-linecap="round" font-family="sans-serif">${n}</text>
          <text x="70" y="95" text-anchor="middle" font-size="92" font-weight="900" fill="none" stroke="#ffffff" stroke-width="4" stroke-linejoin="round" stroke-linecap="round" font-family="sans-serif">${n}</text>
          <text x="70" y="95" text-anchor="middle" font-size="92" font-weight="900" fill="#000000" stroke="none" font-family="sans-serif">${n}</text>
        </svg>
      `;
      div.innerHTML=`
        <div style="position:relative;width:${sz}px;height:${sz}px;display:flex;align-items:center;justify-content:center;">
          <img src="{{ asset('img/Bifrost_White_No_Text.png') }}" style="position:absolute;inset:0;width:100%;height:100%;opacity:0.2;object-fit:contain;transform:scale(0.7);" />
          <svg width="${sz}" height="${sz}" style="position:absolute;inset:0;transform:rotate(-90deg);">
             <circle cx="${sz/2}" cy="${sz/2}" r="${r}" stroke="red" stroke-width="${sw}" fill="transparent" opacity="0.2" />
             <circle id="cd-circ-${wId}" cx="${sz/2}" cy="${sz/2}" r="${r}" stroke="red" stroke-width="${sw}" stroke-linecap="round" fill="transparent" stroke-dasharray="${c}" stroke-dashoffset="${c}" style="transition: stroke-dashoffset 1s linear;" />
          </svg>
          <div id="cdn-${wId}" style="position:relative;z-index:10;display:flex;flex-direction:column;align-items:center;justify-content:center;">
             ${createNumSVG(9)}
          </div>
        </div>
      `;
      card.appendChild(div);
      setTimeout(()=>{
         const circ=document.getElementById('cd-circ-'+wId);
         if(circ) circ.style.strokeDashoffset = c - (c * (1/9));
      }, 50);

      let n=9;
      const iv=setInterval(()=>{
        n--;
        const circ=document.getElementById('cd-circ-'+wId);
        if(circ && n>=0){
          const prog = (9-n)/9;
          circ.style.strokeDashoffset = c - (c * prog);
        }
        if(n>0){
           const el=document.getElementById('cdn-'+wId);
           if(el) el.innerHTML=createNumSVG(n);
        }else if(n===0){
           const el=document.getElementById('cdn-'+wId);
           if(el) el.innerHTML='<div style="color:#fff;font-size:42px;font-weight:900;letter-spacing:1px;margin-bottom:10px;">GO!</div>';
        }else{
           clearInterval(iv);
           const d=document.getElementById('cd-'+wId);if(d)d.remove();cb();
        }
      },1000);
    }

    // ── ROUNDS TIMER ───────────────────────────────────────────────
    function startRounds(wId){
      const list=wData[tab]||[];const w=list.find(x=>x.id===wId);if(!w)return;
      const ts=TS[wId];if(ts.running) return;
      const lim=parseSec(w.time_to_complete||(w.rounds&&w.rounds[0]&&w.rounds[0].time_to_complete)) || 600;
      ts.limit=lim;
      countdown10(wId,()=>{
        ts.running=true;ts.paused=false;
        if(ts.interval)clearInterval(ts.interval);
        ts.interval=setInterval(()=>{
          ts.elapsed++;
          updCenterBtn(w,'rounds',ts);
          if(ts.limit>0&&ts.elapsed>=ts.limit){
            clearInterval(ts.interval);ts.running=false;
            finishRounds(wId,false);
          }
        },1000);
      });
    }
    function finishRounds(wId,ok){const ts=TS[wId]||{};clearInterval(ts.interval);ts.interval=null;ts.running=false;showBanner(wId,'rounds',ok,fmt(ts.elapsed));if(wId===selId){wkStatus=ok?'completed':'incompleted';updBbar();}renderList();}

    // ── AMRAP TIMER ────────────────────────────────────────────────
    function startAmrap(wId){
      const list=wData[tab]||[];const w=list.find(x=>x.id===wId);if(!w)return;
      const ts=TS[wId];
      if(!ts || ts.allDone || ts.running) return;
      const lim=getAmrapSec(w);
      ts.limit=lim;
      countdown10(wId,()=>{
        ts.running=true;
        ts.paused=false;
        updBbar();
        if(ts.interval) clearInterval(ts.interval);
        ts.interval=setInterval(()=>{
          ts.elapsed++;
          const left=Math.max(0,ts.limit-ts.elapsed);
          
          if(left===3 || left===2 || left===1){
            playTimerBeep(600, 0.12);
          }
          
          updCenterBtn(w,'amrap',ts);
          
          if(left<=0){
            clearInterval(ts.interval);
            ts.interval=null;
            ts.running=false;
            ts.allDone=true;
            
            playTimerBeep(1000, 0.35);
            const timeStr = '00:00';
            const rProgress = `${ts.rounds||0}/${ts.totalRounds||1}`;
            saveCondWk(w, null, null, rProgress, timeStr, null);
            
            w.type_completed = 1;
            w.status = 'completed';
            (w.amraps||[]).forEach(ex => { ex.is_completed = 1; });
            
            showBanner(wId, 'amrap', true, fmt(ts.elapsed));
            if(wId===selId){
              wkStatus='completed';
              updBbar();
            }
            renderList();
          }
        },1000);
      });
    }
    function toggleAmrap(wId){}
    function stopAmrap(wId){}

    // ── FOR TIME TIMER ─────────────────────────────────────────────
    function startFT(wId){const list=wData[tab]||[];const w=list.find(x=>x.id===wId);if(!w)return;const ts=TS[wId];if(ts.running){toggleFT(wId);return;}const cap=parseSec(w.time_to_complete||(w.for_times&&w.for_times[0]&&w.for_times[0].time_to_complete));ts.limit=cap;
      countdown10(wId,()=>{ts.running=true;ts.paused=false;const pb=document.getElementById('ft-pb-'+wId);if(pb)pb.textContent='PAUSE';if(ts.interval)clearInterval(ts.interval);ts.interval=setInterval(()=>{ts.elapsed++;const disp=ts.limit>0?Math.max(0,ts.limit-ts.elapsed):ts.elapsed;const el=document.getElementById('ft-t-'+wId);if(el)el.textContent=fmt(disp);updCenterBtn(w,'for-time',ts);if(ts.limit>0&&ts.elapsed>=ts.limit){clearInterval(ts.interval);ts.running=false;finishFT(wId,false);}},1000);});}
    function toggleFT(wId){const ts=TS[wId];if(!ts)return;ts.paused=!ts.paused;ts.running=!ts.paused;if(ts.paused){clearInterval(ts.interval);ts.interval=null;}else{const list=wData[tab]||[];const w=list.find(x=>x.id===wId);ts.interval=setInterval(()=>{ts.elapsed++;const disp=ts.limit>0?Math.max(0,ts.limit-ts.elapsed):ts.elapsed;const el=document.getElementById('ft-t-'+wId);if(el)el.textContent=fmt(disp);updCenterBtn(w,'for-time',ts);if(ts.limit>0&&ts.elapsed>=ts.limit){clearInterval(ts.interval);ts.running=false;finishFT(wId,false);}},1000);}const btn=document.getElementById('ft-pb-'+wId);if(btn)btn.textContent=ts.paused?'RESUME':'PAUSE';}
    function stopFT(wId){const ts=TS[wId];if(!ts)return;clearInterval(ts.interval);ts.interval=null;ts.running=false;ts.paused=false;const list=wData[tab]||[];const w=list.find(x=>x.id===wId);updCenterBtn(w,'for-time',ts);}
    function finishFT(wId,ok){const ts=TS[wId]||{};clearInterval(ts.interval);ts.interval=null;ts.running=false;showBanner(wId,'for-time',ok,fmt(ts.elapsed));if(wId===selId){wkStatus=ok?'completed':'incompleted';updBbar();}renderList();}
    function chgFTRep(wId,exId,d){const el=document.getElementById(`ft-reps-${wId}-${exId}`);if(!el)return;let v=parseInt(el.textContent||'0')+d;if(v<0)v=0;el.textContent=v;}

    // ── EMOM TIMER ─────────────────────────────────────────────────
    function startEmom(wId){
      const list=wData[tab]||[];const w=list.find(x=>x.id===wId);if(!w)return;
      const ts=TS[wId];if(!ts||ts.allDone)return;
      if(ts.running){pauseEmom(wId);return;}
      if(ts.paused){resumeEmom(wId);return;}

      countdown10(wId,()=>{
        ts.running=true;
        ts.paused=false;
        ts.phase='work';
        ts.phaseLeft=ts.roundDuration||60;

        const exCont=document.getElementById('emom-ex-cont-'+wId);
        if(exCont) exCont.style.background='rgba(255,255,255,0.1)';

        updBbar();

        if(ts.interval) clearInterval(ts.interval);
        ts.interval=setInterval(()=>{
          ts.elapsed++;
          ts.phaseLeft--;

          if(ts.phaseLeft===3||ts.phaseLeft===2||ts.phaseLeft===1){
            playTimerBeep(600, 0.12);
          }

          updCenterBtn(w,'emom',ts);

          if(ts.phaseLeft<=0){
            advEmom(wId);
          }
        },1000);
      });
    }

    function pauseEmom(wId){
      const ts=TS[wId];if(!ts||!ts.running)return;
      ts.paused=true;
      ts.running=false;
      if(ts.interval){clearInterval(ts.interval);ts.interval=null;}
      const list=wData[tab]||[];const w=list.find(x=>x.id===wId);
      updBbar();
    }

    function resumeEmom(wId){
      const ts=TS[wId];if(!ts||!ts.paused)return;
      const list=wData[tab]||[];const w=list.find(x=>x.id===wId);if(!w)return;
      ts.paused=false;
      ts.running=true;
      updBbar();
      if(ts.interval) clearInterval(ts.interval);
      ts.interval=setInterval(()=>{
        ts.elapsed++;
        ts.phaseLeft--;
        if(ts.phaseLeft===3||ts.phaseLeft===2||ts.phaseLeft===1){
          playTimerBeep(600, 0.12);
        }
        updCenterBtn(w,'emom',ts);
        if(ts.phaseLeft<=0){
          advEmom(wId);
        }
      },1000);
    }

    function stopEmom(wId){
      pauseEmom(wId);
    }

    function skipEmom(wId){
      const list=wData[tab]||[];const w=list.find(x=>x.id===wId);const ts=TS[wId];
      if(!w||!ts)return;
      if(!ts.running && !ts.paused && !ts.allDone){
        showMsg('Notice', "Please click 'START' to begin your workout.");
        return;
      }
      if(ts.allDone) return;
      advEmom(wId);
    }

    async function advEmom(wId){
      const list=wData[tab]||[];const w=list.find(x=>x.id===wId);const ts=TS[wId];if(!ts||!w)return;
      playTimerBeep(1000, 0.35);

      const completedRoundNum = ts.round;
      ts.completedRounds = (ts.completedRounds || 0) + 1;
      const isFinal = ts.completedRounds >= ts.totalRounds;
      const timeStr = fmt(ts.roundDuration || 60);
      const rProgress = `${completedRoundNum}/${ts.totalRounds}`;

      saveCondWk(w, null, null, rProgress, timeStr, null);

      const prg = document.getElementById('emom-prog-' + wId);
      if (prg) prg.textContent = `${ts.completedRounds}/${ts.totalRounds}`;
      const pbar = document.getElementById('emom-pbar-' + wId);
      if (pbar) pbar.style.width = `${Math.min(100, (ts.completedRounds / ts.totalRounds) * 100)}%`;

      if (isFinal) {
        clearInterval(ts.interval);
        ts.interval = null;
        ts.running = false;
        ts.phase = 'done';
        ts.allDone = true;

        const exCont = document.getElementById('emom-ex-cont-' + wId);
        if (exCont) exCont.style.background = 'transparent';

        showBanner(wId, 'emom', true, fmt(ts.elapsed));
        if (wId === selId) {
          wkStatus = 'completed';
          updBbar();
        }
        renderList();
      } else {
        ts.round++;
        ts.phaseLeft = ts.roundDuration || 60;
        updBbar();
      }
    }

    // ── INTERVALS TIMER ────────────────────────────────────────────
    function startInt(wId){const list=wData[tab]||[];const w=list.find(x=>x.id===wId);if(!w)return;const ts=TS[wId];if(ts.running){toggleInt(wId);return;}const fe=w.intervals&&w.intervals[0]||{};ts.workSecs=parseSec(fe.work||w.work)||40;ts.restSecs=parseSec(fe.rest||w.rest)||20;ts.totalRounds=ts.totalRounds||w.number||(w.intervals||[]).length||1;ts.round=ts.round||1;ts.phase='work';ts.phaseLeft=ts.workSecs;
      countdown10(wId,()=>{ts.running=true;ts.paused=false;const pb=document.getElementById('int-pb-'+wId);if(pb)pb.textContent='PAUSE';if(ts.interval)clearInterval(ts.interval);ts.interval=setInterval(()=>{ts.elapsed++;ts.phaseLeft--;const c=ts.phase==='work'?'#22c55e':'#ef4444';const te=document.getElementById('int-t-'+wId);const pe=document.getElementById('int-p-'+wId);const prg=document.getElementById('int-prog-'+wId);if(te){te.textContent=fmt(ts.phaseLeft);te.style.color=c;}if(pe){pe.textContent=ts.phase==='work'?'🔥 WORK':'😮‍💨 REST';pe.style.color=c;}if(prg)prg.textContent=`${Math.max(0,ts.round-1)}/${ts.totalRounds}`;updCenterBtn(w,'intervals',ts);if(ts.phaseLeft<=0){if(ts.phase==='work'&&ts.restSecs>0){ts.phase='rest';ts.phaseLeft=ts.restSecs;}else advInt(wId);}},1000);});}
    function advInt(wId){const list=wData[tab]||[];const w=list.find(x=>x.id===wId);const ts=TS[wId];if(!ts)return;ts.round++;if(w)saveCondWk(w,null,null,null,fmt(ts.elapsed||0),null);if(ts.round>ts.totalRounds){clearInterval(ts.interval);ts.interval=null;ts.running=false;ts.phase='done';ts.allDone=true;showBanner(wId,'intervals',true,fmt(ts.elapsed));if(wId===selId){wkStatus='completed';updBbar();}const prg=document.getElementById('int-prog-'+wId);if(prg)prg.textContent=`${ts.totalRounds}/${ts.totalRounds}`;}else{ts.phase='work';ts.phaseLeft=ts.workSecs;}}
    function toggleInt(wId){const ts=TS[wId];if(!ts)return;ts.paused=!ts.paused;ts.running=!ts.paused;if(ts.paused){clearInterval(ts.interval);ts.interval=null;}else{const list=wData[tab]||[];const w=list.find(x=>x.id===wId);ts.interval=setInterval(()=>{ts.elapsed++;ts.phaseLeft--;const c=ts.phase==='work'?'#22c55e':'#ef4444';const te=document.getElementById('int-t-'+wId);if(te){te.textContent=fmt(ts.phaseLeft);te.style.color=c;}updCenterBtn(w,'intervals',ts);if(ts.phaseLeft<=0){if(ts.phase==='work'&&ts.restSecs>0){ts.phase='rest';ts.phaseLeft=ts.restSecs;}else advInt(wId);}},1000);}const btn=document.getElementById('int-pb-'+wId);if(btn)btn.textContent=ts.paused?'RESUME':'PAUSE';}
    function stopInt(wId){const ts=TS[wId];if(!ts)return;clearInterval(ts.interval);ts.interval=null;ts.running=false;ts.paused=false;const list=wData[tab]||[];const w=list.find(x=>x.id===wId);updCenterBtn(w,'intervals',ts);}
    function skipInt(wId){const ts=TS[wId];if(!ts||!ts.running)return;ts.phaseLeft=0;}

    // ── CIRCUIT TIMER ──────────────────────────────────────────────
    function startCircuit(wId){const list=wData[tab]||[];const w=list.find(x=>x.id===wId);if(!w)return;const ts=TS[wId];if(ts.running){toggleCircuit(wId);return;}const lim=parseSec(w.time_to_complete||(w.circuits&&w.circuits[0]&&w.circuits[0].time_to_complete));ts.limit=lim;
      countdown10(wId,()=>{ts.running=true;ts.paused=false;const pb=document.getElementById('circuit-pb-'+wId);if(pb)pb.textContent='PAUSE';if(ts.interval)clearInterval(ts.interval);ts.interval=setInterval(()=>{ts.elapsed++;const disp=ts.limit>0?Math.max(0,ts.limit-ts.elapsed):ts.elapsed;const el=document.getElementById('circuit-t-'+wId);if(el)el.textContent=fmt(disp);updCenterBtn(w,'circuit',ts);if(ts.limit>0&&ts.elapsed>=ts.limit){clearInterval(ts.interval);ts.running=false;finishCircuit(wId,false);}},1000);});}
    function toggleCircuit(wId){const ts=TS[wId];if(!ts)return;ts.paused=!ts.paused;ts.running=!ts.paused;if(ts.paused){clearInterval(ts.interval);ts.interval=null;}else{const list=wData[tab]||[];const w=list.find(x=>x.id===wId);ts.interval=setInterval(()=>{ts.elapsed++;const disp=ts.limit>0?Math.max(0,ts.limit-ts.elapsed):ts.elapsed;const el=document.getElementById('circuit-t-'+wId);if(el)el.textContent=fmt(disp);updCenterBtn(w,'circuit',ts);if(ts.limit>0&&ts.elapsed>=ts.limit){clearInterval(ts.interval);ts.running=false;finishCircuit(wId,false);}},1000);}const btn=document.getElementById('circuit-pb-'+wId);if(btn)btn.textContent=ts.paused?'RESUME':'PAUSE';}
    function stopCircuit(wId){const ts=TS[wId];if(!ts)return;clearInterval(ts.interval);ts.interval=null;ts.running=false;ts.paused=false;const list=wData[tab]||[];const w=list.find(x=>x.id===wId);updCenterBtn(w,'circuit',ts);}
    function finishCircuit(wId,ok){const ts=TS[wId]||{};clearInterval(ts.interval);ts.interval=null;ts.running=false;showBanner(wId,'circuit',ok,fmt(ts.elapsed));if(wId===selId){wkStatus=ok?'completed':'incompleted';updBbar();}renderList();}

    function startSS(w){
      const ts = TS[w.id] = TS[w.id] || { elapsed: 0, running: false, paused: false, interval: null, limit: 0 };
      const straights = w.straights || [];
      if (!straights.length) return;
      const totalSets = straights[0].sets?.length || 0;
      let tIdx = 0;
      for (let i = 0; i < totalSets; i++) {
         const allDone = straights.every(ex => {
           const s = ex.sets && ex.sets[i];
           if (!s) return true;
           return s.is_completed == 1 || !!CT[`${w.id}-${ex.id||0}-${s.id||i}`];
         });
         if (!allDone) { tIdx = i; break; }
      }
      
      const rk=`rest-${w.id}-${tIdx}`;
      if (RST[rk]?.running) return;
      
      countdown10(w.id, ()=>{
        ts.running = true; ts.paused = false;
        startRestForSet(w, tIdx);
      });
    }
    function toggleSS(wId) {
      // Pause/resume not supported for rest timers natively in mobile straight sets
    }
    function completeSS(w){
      const straights=w.straights||[];if(!straights.length)return;
      const totalSets=straights[0].sets?.length||0;
      
      let tIdx=0;
      for(let i=0;i<totalSets;i++){const allDone=straights.every(ex=>{const s=ex.sets&&ex.sets[i];if(!s)return true;return s.is_completed==1||!!CT[`${w.id}-${ex.id||0}-${s.id||i}`];});if(!allDone){tIdx=i;break;}}
      
      const rk=`rest-${w.id}-${tIdx}`;
      if(RST[rk]?.running){
          doneRest(w.id,tIdx);
          completeSSetToggle(w, tIdx, true); 
      } else {
          showMsg('Notice', "Please click 'START' to begin your set before completing it.");
      }
    }
    function startRestForSet(w,si){
      const straights=w.straights||[];if(!straights.length)return;
      const fe=straights[0],s=fe.sets&&fe.sets[si]||{};
      const rr=parseSec(s.restred||fe.restred||w.restred||'00:01:30');
      const ry=parseSec(s.restyellow||fe.restyellow||w.restyellow||'00:01:00');
      const rg=parseSec(s.restgreen||fe.restgreen||w.restgreen||'00:00:30');
      const phases=[{color:'#ef4444',time:rr},{color:'#f97316',time:ry},{color:'#22c55e',time:rg}].filter(p=>p.time>0);
      if(!phases.length){ return; }
      const rk=`rest-${w.id}-${si}`;
      if(RST[rk]?.interval)clearInterval(RST[rk].interval);
      const rst=RST[rk]={running:true,paused:false,phase:0,phaseLeft:phases[0].time,color:phases[0].color,interval:null};
      renderList();
      rst.interval=setInterval(()=>{
        rst.phaseLeft--;
        updBbar();updCenterBtn(w,'straight-sets',{});
        if(rst.phaseLeft<=0){
          rst.phase++;
          if(rst.phase<phases.length){
            rst.phaseLeft=phases[rst.phase].time;rst.color=phases[rst.phase].color;
          }else{
            clearInterval(rst.interval);rst.running=false;delete RST[rk];
            if(TS[w.id]) TS[w.id].running = false;
            updBbar();updCenterBtn(w,'straight-sets',{});
            
            showBanner(w.id, 'straight-sets', false);
            setTimeout(() => {
                const el = document.getElementById('banner-'+w.id);
                if (el) el.innerHTML = '';
                renderList(); updBbar();
            }, 2500);
          }
        }
      },1000);
    }
    function completeSSetToggle(w,si,checkAll=true){
      const straights=w.straights||[];straights.forEach(ex=>{const s=ex.sets&&ex.sets[si];if(s)CT[`${w.id}-${ex.id||0}-${s.id||si}`]=true;});
      if (checkAll) {
         const totalSets=straights[0]?.sets?.length||0;
         const allDone=Array.from({length:totalSets},(_,i)=>i).every(i=>straights.every(ex=>{const s=ex.sets&&ex.sets[i];if(!s)return true;return s.is_completed==1||!!CT[`${w.id}-${ex.id||0}-${s.id||i}`];}));
         if(allDone){showBanner(w.id,'straight-sets',true);if(w.id===selId){wkStatus='completed';updBbar();}
         const ts=TS[w.id]; if(ts){clearInterval(ts.interval);ts.running=false;}}
      }
      renderList();updBbar();
    }
    function doneRest(wId,si){
      const rk=`rest-${wId}-${si}`;const rst=RST[rk];if(rst){clearInterval(rst.interval);rst.running=false;delete RST[rk];}
      const list=wData[tab]||[];const w=list.find(x=>x.id===wId);
      if(w) { 
          if(TS[w.id]) TS[w.id].running = false;
          renderList(); updBbar(); updCenterBtn(w, 'straight-sets', TS[w.id]||{}); 
      }
    }
    function chgRep(wId,exId,setId,d){const el=document.getElementById(`reps-${wId}-${exId}-${setId}`);if(!el)return;let v=parseInt(el.textContent||'0')+d;if(v<0)v=0;el.textContent=v;}
    function chgFTRep(wId,exId,d){const el=document.getElementById(`ft-reps-${wId}-${exId}`);if(!el)return;let v=parseInt(el.textContent||'0')+d;if(v<0)v=0;el.textContent=v;}

    // ── PYRAMID ────────────────────────────────────────────────────
    function pyrClick(wId,rowId){
      const list=wData[tab]||[];const w=list.find(x=>x.id===wId);if(!w)return;
      const rows=w.pyramids||w.pyramid||[];const row=rows.find(r=>r.id===rowId);if(!row)return;
      const tk=`${wId}-${rowId}`;if(CT[tk]||row.is_completed==1)return;
      const rk=`pyr-${wId}-${rowId}`;
      if(RST[rk]?.running){
        finishPyrRest(wId,rk,true);
        return;
      }
      const otherRk=Object.keys(RST).find(k=>k.startsWith(`pyr-${wId}-`)&&RST[k]?.running);
      if(otherRk){
        finishPyrRest(wId,otherRk,true);
      }
      startPyrRest(wId,rowId);
    }

    function startPyrRest(wId,rowId){
      const list=wData[tab]||[];const w=list.find(x=>x.id===wId);if(!w)return;
      const rows=w.pyramids||w.pyramid||[];const row=rows.find(r=>r.id===rowId);if(!row)return;
      const tk=`${wId}-${rowId}`;if(CT[tk]||row.is_completed==1)return;

      const DEFAULT_RED='00:00:15';
      const DEFAULT_YELLOW='00:00:15';
      const DEFAULT_GREEN='00:00:15';
      const rr=parseSec(row.restred||w.restred||DEFAULT_RED);
      const ry=parseSec(row.restyellow||w.restyellow||DEFAULT_YELLOW);
      const rg=parseSec(row.restgreen||w.restgreen||DEFAULT_GREEN);
      const phases=[{color:'#ef4444',time:rr},{color:'#f97316',time:ry},{color:'#22c55e',time:rg}].filter(p=>p.time>0);

      const rk=`pyr-${wId}-${rowId}`;
      if(RST[rk]?.interval)clearInterval(RST[rk].interval);

      if(!phases.length){
        CT[tk]=true;
        const rowIndex=rows.findIndex(r=>r.id===rowId);
        savePyramidRow(w,row,rowIndex,rows.length);
        checkAllPyrDone(w);
        renderList();
        updBbar();
        return;
      }

      const rst=RST[rk]={
        running:true,
        phase:0,
        phaseLeft:phases[0].time,
        color:phases[0].color,
        interval:null,
        rowId:rowId,
        wId:wId,
        phases:phases
      };

      renderList();
      updBbar();

      rst.interval=setInterval(()=>{
        rst.phaseLeft--;
        if(rst.phaseLeft===3||rst.phaseLeft===2||rst.phaseLeft===1){
          playTimerBeep(600,0.12);
        }
        if(rst.phaseLeft<=0){
          rst.phase++;
          if(rst.phase<phases.length){
            rst.phaseLeft=phases[rst.phase].time;
            rst.color=phases[rst.phase].color;
            updBbar();
          }else{
            finishPyrRest(wId,rk,false);
          }
        }else{
          updCenterBtn(w,'pyramid',TS[wId]||{});
        }
      },1000);
    }

    function finishPyrRest(wId,rk,manual=false){
      const rst=RST[rk];if(!rst)return;
      if(rst.interval)clearInterval(rst.interval);
      delete RST[rk];

      const list=wData[tab]||[];const w=list.find(x=>x.id===wId);if(!w)return;
      const rows=w.pyramids||w.pyramid||[];
      const rowId=rst.rowId;
      const row=rows.find(r=>r.id===rowId);
      const tk=`${wId}-${rowId}`;
      CT[tk]=true;

      if(row){
        const rowIndex=rows.findIndex(r=>r.id===rowId);
        savePyramidRow(w,row,rowIndex,rows.length);
      }

      checkAllPyrDone(w);
      renderList();
      updBbar();
    }

    function checkAllPyrDone(w){
      const rows=w.pyramids||w.pyramid||[];
      const doneC=rows.filter(r=>!!CT[`${w.id}-${r.id}`]||r.is_completed==1).length;
      const allDone=rows.length>0&&doneC>=rows.length;
      if(allDone){
        showBanner(w.id,'pyramid',true);
        if(w.id===selId){
          wkStatus='completed';
        }
      }
    }

    async function savePyramidRow(w,row,rowIndex,rowsCount){
      let ep='/save-strength-workout';
      if(tab==='warmup') ep='/warmup-daily';
      else if(tab==='weightlifting') ep='/save-weightlifting-workout';
      else if(tab==='conditioning') ep='/save-conditioning-workout';
      else if(tab==='accessory') ep='/save-accessory-workout';

      const u=(row.unittype||row.unit_type||w.unit_type||'%').trim().toUpperCase();
      const ld=row.training_load??row.weight??w.training_load??0;
      const wgt=calcWt(ld,u,row.test_weight??w.test_weight??0);
      const reps=parseInt(row.reps_progress??row.reps??0)||0;
      const setNumber=(rowIndex!==undefined&&rowIndex>=0)?(rowIndex+1):1;
      const roundNumber=`${setNumber}/${rowsCount||1}`;

      const payload=[{
        workout_manager_id:w.id,
        workout_format_type:'pyramid',
        workout_format_id:row.id,
        reps:reps,
        weight:wgt,
        date:dayStr(),
        set_number:setNumber,
        round_number:roundNumber,
        class_Id:CLSID,
        notes:notes||null
      }];

      try{
        console.log('Saving PYRAMID row payload:',payload);
        await fetch(ep,{
          method:'POST',
          headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
          body:JSON.stringify(payload)
        });
      }catch(err){
        console.error('Failed to save pyramid row:',err);
      }
    }

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

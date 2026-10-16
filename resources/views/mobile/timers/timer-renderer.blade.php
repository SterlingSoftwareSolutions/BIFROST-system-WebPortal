<script>
    // ── TIMER RENDERER (Central Bottom Bar Timer Slot Dispatcher) ──
    // Corresponds to components/timers/TimerRenderer.tsx
    function updCenterBtn(w, fk, ts){
      const bc = document.getElementById('btn-center');
      if(!bc) return;

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

      if(fk === 'pyramid'){
        const ak = Object.keys(RST).find(k => k.startsWith(`pyr-${w.id}-`) && RST[k]?.running);
        if(ak){
          const rst = RST[ak];
          bc.style.background = rst.color || '#CD7F32';
          bc.style.borderColor = rst.color || '#CD7F32';
          bc.innerHTML = `<div style="display:flex;flex-direction:column;align-items:center;line-height:1.1;"><span style="font-size:13px;font-weight:700;color:#fff;letter-spacing:1px;">REST</span><span style="font-size:22px;font-weight:700;color:#fff;font-variant-numeric:tabular-nums;">${fmt(rst.phaseLeft||0)}</span></div>`;
          return;
        }
        bc.style.background = '#CD7F32';
        bc.style.borderColor = '#CD7F32';
        bc.innerHTML = `<div style="display:flex;flex-direction:column;align-items:center;line-height:1.1;"><span style="font-size:13px;font-weight:700;color:#fff;letter-spacing:1px;">REST</span><span style="font-size:22px;font-weight:700;color:#fff;font-variant-numeric:tabular-nums;">00:00</span></div>`;
        return;
      }

      if(fk === 'straight-sets'){
        const ak = Object.keys(RST).find(k => k.startsWith(`rest-${w.id}-`) && RST[k]?.running);
        if(ak){
          const rst = RST[ak];
          bc.style.background = rst.color || '#CD7F32';
          bc.style.borderColor = rst.color || '#CD7F32';
          bc.innerHTML = `<span style="font-size:22px;font-weight:900;font-variant-numeric:tabular-nums;">${fmt(rst.phaseLeft||0)}</span>`;
          return;
        }
        if(ts.running || ts.paused){
          bc.style.background = '#CD7F32';
          bc.style.borderColor = '#CD7F32';
          bc.innerHTML = `<span style="font-size:22px;font-weight:900;font-variant-numeric:tabular-nums;">${fmt(ts.elapsed||0)}</span>`;
          return;
        }
        bc.style.background = '#CD7F32';
        bc.style.borderColor = '#CD7F32';
        bc.innerHTML = 'START';
        return;
      }

      if(['rounds','amrap','for-time','emom','intervals','circuit'].includes(fk) && (ts.running || ts.paused)){
        const pc = fk==='intervals' ? (ts.phase==='work'?'#2E7D32':'#C62828') : fk==='emom' ? '#2E7D32' : '#CD7F32';
        bc.style.background = pc;
        bc.style.borderColor = pc;
        let tv = ts.phaseLeft || 0;
        if(['rounds','for-time','circuit'].includes(fk)) tv = ts.limit > 0 ? Math.max(0, ts.limit - ts.elapsed) : ts.elapsed;
        else if(fk === 'amrap') tv = Math.max(0, ts.limit - ts.elapsed);
        bc.innerHTML = `<span style="font-size:22px;font-weight:900;font-variant-numeric:tabular-nums;">${fmt(tv)}</span>`;
        return;
      }

      bc.style.background = '#CD7F32';
      bc.style.borderColor = '#CD7F32';
      bc.innerHTML = 'START';
    }
</script>

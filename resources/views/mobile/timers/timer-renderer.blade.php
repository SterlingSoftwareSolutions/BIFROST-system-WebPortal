<script>
    // ── TIMER RENDERER (Central Bottom Bar Timer Slot Dispatcher) ──
    // Corresponds to components/timers/TimerRenderer.tsx
    function updCenterBtn(w, fk, ts){
      const bc = document.getElementById('btn-center');
      if(!bc) return;

      if(fk === 'rounds'){
        if(!hasRoundsTimer(w, ts)){
          bc.style.background = '#CD7F32';
          bc.style.borderColor = '#CD7F32';
          bc.style.cursor = 'default';
          bc.innerHTML = `<span style="font-size:22px;font-weight:700;letter-spacing:2px;font-variant-numeric:tabular-nums;color:#fff;">--:--</span>`;
          return;
        }
      }

      if(fk === 'circuit'){
        if(!hasCircuitTimer(w, ts)){
          bc.style.background = '#CD7F32';
          bc.style.borderColor = '#CD7F32';
          bc.style.cursor = 'default';
          bc.innerHTML = `<span style="font-size:22px;font-weight:700;letter-spacing:2px;font-variant-numeric:tabular-nums;color:#fff;">--:--</span>`;
          return;
        }
      }

      const isComp = (wkStatus==='completed' || w.type_completed==1 || w.status==='completed' || ts.allDone || ts.phase==='done');
      if(isComp){
        // Formats without workout-level timers (pyramid, straight-sets) hide timer when completed and display '--:--' (matching mobile app)
        if(fk === 'pyramid' || fk === 'straight-sets'){
          bc.style.background = '#CD7F32';
          bc.style.borderColor = '#CD7F32';
          bc.style.cursor = 'default';
          bc.innerHTML = `<span style="font-size:22px;font-weight:700;letter-spacing:2px;font-variant-numeric:tabular-nums;color:#fff;">--:--</span>`;
          return;
        }

        if(fk === 'rounds' && !hasRoundsTimer(w, ts)){
          bc.style.background = '#CD7F32';
          bc.style.borderColor = '#CD7F32';
          bc.style.cursor = 'default';
          bc.innerHTML = `<span style="font-size:22px;font-weight:700;letter-spacing:2px;font-variant-numeric:tabular-nums;color:#fff;">--:--</span>`;
          return;
        }
        if(fk === 'circuit' && !hasCircuitTimer(w, ts)){
          bc.style.background = '#CD7F32';
          bc.style.borderColor = '#CD7F32';
          bc.style.cursor = 'default';
          bc.innerHTML = `<span style="font-size:22px;font-weight:700;letter-spacing:2px;font-variant-numeric:tabular-nums;color:#fff;">--:--</span>`;
          return;
        }

        let compSec = 0;
        const lim = ts.limit > 0 ? ts.limit : (parseSec(w.time_to_complete || (w.rounds && w.rounds[0] && w.rounds[0].time_to_complete) || (w.circuits && w.circuits[0] && w.circuits[0].time_to_complete) || 0));

        if(fk === 'emom'){
          if(ts.timeTaken && ts.timeTaken !== '00:00'){
            compSec = parseSec(ts.timeTaken);
          } else if(ts.phaseLeft !== undefined && ts.phaseLeft > 0){
            compSec = ts.phaseLeft;
          } else {
            const exs = w.emoms || [];
            const completedEx = exs.find(e => e.exercise_time && e.exercise_time !== '00:00');
            if (completedEx && (isComp || w.type_completed == 1 || w.status === 'completed')) {
              compSec = parseSec(completedEx.exercise_time);
            }
          }
        } else if(fk === 'intervals'){
          compSec = 0;
        } else if(fk === 'circuit'){
          if(ts.timeTaken && ts.timeTaken !== '00:00'){
            compSec = parseSec(ts.timeTaken);
          } else if(ts.elapsed > 0){
            compSec = ts.elapsed;
          } else {
            let maxSec = -1;
            let bestTime = null;
            (w.circuits || []).forEach(e => {
              if(e.exercise_time && e.exercise_time !== '00:00'){
                const s = parseSec(e.exercise_time);
                if(s >= maxSec){
                  maxSec = s;
                  bestTime = e.exercise_time;
                }
              }
            });
            if(bestTime){
              compSec = parseSec(bestTime);
            }
          }
        } else {
          if(ts.timeTaken && ts.timeTaken !== '00:00'){
            compSec = parseSec(ts.timeTaken);
          } else if(ts.elapsed > 0){
            compSec = lim > 0 ? Math.max(0, lim - ts.elapsed) : ts.elapsed;
          }
        }
        if(compSec <= 0 && fk !== 'emom' && fk !== 'intervals' && fk !== 'circuit'){
          if(fk === 'amrap'){
            compSec = getAmrapSec(w);
          } else if(fk === 'rounds'){
            const first = (w.rounds || []).find(ex => ex.exercise_time && ex.exercise_time !== '00:00');
            compSec = first ? parseSec(first.exercise_time) : (parseSec(w.time_to_complete) || (ts.limit > 0 ? ts.limit : 0));
          } else if(fk === 'for-time'){
            compSec = ts.limit > 0 ? ts.limit : parseSec(w.time_to_complete);
          } else if(w.time_to_complete){
            compSec = parseSec(w.time_to_complete);
          } else if(w.exercise_time && w.exercise_time !== '00:00'){
            compSec = parseSec(w.exercise_time);
          }
        }

        const formatted = (compSec > 0) ? fmt(compSec) : '--:--';
        const timeDisp = (formatted === '00:00') ? '--:--' : formatted;

        bc.style.background = '#CD7F32';
        bc.style.borderColor = '#CD7F32';
        bc.style.cursor = 'default';
        bc.innerHTML = `<span style="font-size:22px;font-weight:700;letter-spacing:2px;font-variant-numeric:tabular-nums;color:#fff;">${timeDisp}</span>`;
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

        if(fk === 'intervals'){
          const isWork = ts.phase === 'work';
          const phaseTxt = isWork ? 'WORK' : 'REST';
          bc.innerHTML = `<div style="display:flex;flex-direction:column;align-items:center;line-height:1.1;"><span style="font-size:14px;font-weight:700;color:#fff;letter-spacing:2px;margin-bottom:2px;">${phaseTxt}</span><span style="font-size:22px;font-weight:700;color:#fff;font-variant-numeric:tabular-nums;">${fmt(tv)}</span></div>`;
          return;
        }

        bc.innerHTML = `<span style="font-size:22px;font-weight:900;font-variant-numeric:tabular-nums;">${fmt(tv)}</span>`;
        return;
      }

      bc.style.background = '#CD7F32';
      bc.style.borderColor = '#CD7F32';
      bc.innerHTML = 'START';
    }
</script>

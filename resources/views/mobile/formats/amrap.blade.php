<script>
    // ─── AMRAP FORMAT ───────────────────────────────────────────────
    function cardAmrap(w){
      const exs = w.amraps || [];
      const amSec = getAmrapSec(w);
      let rd = exs[0]?.rounds_done || 0;
      let rt = exs[0]?.rounds_total || 0;
      if(exs[0] && exs[0].round_number){
        const p = String(exs[0].round_number).split('/');
        if(p.length === 2){
          if(!rd) rd = parseInt(p[0]) || 0;
          if(!rt) rt = parseInt(p[1]) || 0;
        }
      }
      let allD = (w.type_completed == 1 || w.status === 'completed' || (exs.length > 0 && exs.every(ex => ex.is_completed == 1)) || (rt > 0 && rd >= rt));
      if(allD && rd === 0 && rt > 0) rd = rt;
      const ts = TS[w.id] = TS[w.id] || { elapsed: 0, running: false, paused: false, interval: null, limit: amSec, rounds: rd, totalRounds: rt, allDone: allD };
      if(!ts.running && !ts.allDone) ts.limit = amSec;
      if(ts.rounds === 0 && rd > 0) ts.rounds = rd;
      if(ts.totalRounds === 0 && rt > 0) ts.totalRounds = rt;
      if(allD || ts.allDone) {
        ts.allDone = true;
        if(rt > 0 && (ts.rounds || 0) < rt) ts.rounds = rt;
      }
      const isComp = (w.type_completed == 1 || w.status === 'completed' || ts.allDone || (rt > 0 && (ts.rounds || 0) >= rt));
      const firstExWithTime = exs.find(e => e.exercise_time && e.exercise_time !== '00:00');
      if(firstExWithTime && !ts.timeTaken) {
        ts.timeTaken = firstExWithTime.exercise_time;
      }
      
      const headerExtra = `<span id="amrap-rounds-${w.id}" class="round-badge" style="background:rgba(255,255,255,0.15); border-radius:10px; padding:2px 8px; font-size:11px; font-weight:600; color:#fff; display:inline-block; margin-left:2px;">${ts.rounds || 0}</span>`;
      
      const hasMissing1RM = exs.some(ex => String(ex.unit_type || "%").trim() === "%" && ex.training_load > 0 && (!ex.test_weight || ex.test_weight === 0));
      const warnHtml = hasMissing1RM ? `<div id="amrap-warn-${w.id}" style="color:#fca5a5; font-size:12px; margin-bottom:8px; padding:0 4px;">⚠️ Please add your 1RM for accurate weight calculation.</div>` : '';
      
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
          dispWt(ex.training_load ?? 0, ex.unit_type || '%', ex.test_weight ?? 0),
          ex.reps ?? 0,
          isDone,
          isIncomp
        );
      }).join('');

      return wkCard(w.id, 'AMRAP', headerExtra, warnHtml + exHtml, isComp, ts.timeTaken);
    }
</script>

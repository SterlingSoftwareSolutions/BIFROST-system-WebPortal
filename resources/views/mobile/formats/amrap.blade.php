<script>
    // ─── AMRAP FORMAT ───────────────────────────────────────────────
    function cardAmrap(w){
      const exs = w.amraps || [];
      const amSec = getAmrapSec(w);
      let rd = exs[0]?.rounds_done || 0;
      let rt = exs[0]?.rounds_total || 0;
      let allD = (w.type_completed == 1 || w.status === 'completed' || (exs.length > 0 && exs.every(ex => ex.is_completed == 1)));
      const ts = TS[w.id] = TS[w.id] || { elapsed: 0, running: false, paused: false, interval: null, limit: amSec, rounds: rd, totalRounds: rt, allDone: allD };
      if(!ts.running && !ts.allDone) ts.limit = amSec;
      if(ts.rounds === 0 && rd > 0) ts.rounds = rd;
      if(ts.totalRounds === 0 && rt > 0) ts.totalRounds = rt;
      if(allD || ts.allDone) ts.allDone = true;
      const isComp = (w.type_completed == 1 || w.status === 'completed' || ts.allDone);
      
      const headerExtra = `<span id="amrap-rounds-${w.id}" class="round-badge" style="background:rgba(255,255,255,0.15); border-radius:12px; padding:2px 10px; font-size:13px; font-weight:600; color:#fff; display:inline-block; margin-left:2px;">${ts.rounds || 0}</span>`;
      
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
          dispWt(ex.training_load ?? 0, ex.unit_type || '%', ex.test_weight ?? 0),
          ex.reps ?? 0,
          isDone,
          isIncomp
        );
      }).join('');

      return wkCard(w.id, 'AMRAP', headerExtra, warnHtml + exHtml, isComp, ts.timeTaken);
    }
</script>

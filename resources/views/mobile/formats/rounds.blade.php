<script>
    // ─── ROUNDS FORMAT ──────────────────────────────────────────────
    function cardRounds(w){
      const exs = w.rounds || [];
      const total = w.number || 0;
      const lim = parseSec(w.time_to_complete || (exs[0] && exs[0].time_to_complete)) || 0;
      let dbCr = 0;
      let allD = (w.type_completed == 1 || w.status === 'completed' || (exs.length > 0 && exs.every(ex => ex.is_completed == 1)));
      if(exs[0] && exs[0].round_number) {
          const p = String(exs[0].round_number).split('/');
          if(p.length > 0) dbCr = parseInt(p[0]) || 0;
          if(total > 0 && dbCr >= total) allD = true;
      }
      // Mobile app pattern: If marked completed on server, restore full rounds
      if(allD && dbCr === 0 && total > 0) {
          dbCr = total;
      }
      const ts = TS[w.id] = TS[w.id] || { elapsed: 0, running: false, paused: false, interval: null, limit: lim, completedRounds: dbCr, allDone: allD };
      if(ts.completedRounds === 0 && dbCr > 0) ts.completedRounds = dbCr;
      if(allD || ts.allDone || (total > 0 && (ts.completedRounds || 0) >= total)) {
          ts.allDone = true;
          if(total > 0 && (ts.completedRounds || 0) < total) ts.completedRounds = total;
      }
      const cr = ts.completedRounds || 0;
      const isComp = (w.type_completed == 1 || w.status === 'completed' || ts.allDone || (total > 0 && cr >= total));
      const firstExWithTime = exs.find(e => e.exercise_time && e.exercise_time !== '00:00');
      if(firstExWithTime && !ts.timeTaken) {
          ts.timeTaken = firstExWithTime.exercise_time;
      }
      const sMap = {};
      exs.forEach(ex => {
        const sn = ex.stationumber ?? 1;
        if(!sMap[sn]) sMap[sn] = [];
        sMap[sn].push(ex);
      });
      const sns = Object.keys(sMap).sort((a,b) => a - b);
      const stHtml = sns.map(sn => {
        const exRows = sMap[sn].map(ex => plainExRow(exName(ex), dispWt(ex.training_load ?? 0, ex.unit_type || '%', ex.test_weight ?? 0), ex.reps ?? 0, isComp)).join('');
        return sns.length > 1 ? `<div class="stn-block"><div class="stn-lbl">Station ${sn}</div>${exRows}</div>` : exRows;
      }).join('');
      
      const progStr = `${cr}${total > 0 ? '/' + total : ''}`;
      const headerExtra = `<span id="rounds-prog-${w.id}" style="background:rgba(255,255,255,0.2); padding:2px 8px; border-radius:10px; font-size:11px; font-weight:600; color:#fff; display:inline-block;">${progStr}</span>`;
      
      const hasMissing1RM = exs.some(ex => String(ex.unit_type || "%").trim() === "%" && ex.training_load > 0 && (!ex.test_weight || ex.test_weight === 0));
      const warnHtml = hasMissing1RM ? `<div style="color:#fca5a5; font-size:12px; margin-bottom:8px; padding:0 4px;">⚠️ Please add your 1RM for accurate weight calculation.</div>` : '';
      const pbarWidth = total > 0 ? (cr / total) * 100 : 0;
      const pbarHtml = `${warnHtml}<div style="height:4px; width:100%; background:rgba(255,255,255,0.2); border-radius:2px; margin-bottom:16px; margin-top:2px; overflow:hidden;"><div id="rounds-pbar-${w.id}" style="height:100%; width:${pbarWidth}%; background:#fff; transition:width 0.3s ease;"></div></div>`;
      
      return wkCard(w.id, 'Rounds', headerExtra, pbarHtml + stHtml, isComp, ts.timeTaken);
    }
</script>

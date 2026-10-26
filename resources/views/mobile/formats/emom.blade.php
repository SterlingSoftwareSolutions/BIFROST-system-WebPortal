<script>
    // ─── EMOM FORMAT ────────────────────────────────────────────────
    function cardEmom(w){
      const exs = w.emoms || [];
      const totalR = Number(w.number) > 0 ? Number(w.number) : (exs.length || 1);

      // Parse roundDuration from first exercise exercise_time if present, default 60s
      const rawTime = exs[0]?.exercise_time ?? 1;
      let roundDur = 60;
      const strTime = String(rawTime).trim();
      if(strTime.includes(':')){
        const parts = strTime.split(':').map(Number);
        if(parts.length === 3) roundDur = (parts[0] || 0) * 3600 + (parts[1] || 0) * 60 + (parts[2] || 0);
        else if(parts.length === 2) roundDur = (parts[0] || 0) * 60 + (parts[1] || 0);
      } else {
        const numTime = Number(strTime);
        if(!isNaN(numTime) && numTime > 0) roundDur = numTime > 10 ? numTime : numTime * 60;
      }
      if(roundDur <= 0) roundDur = 60;

      // Check DB completion status
      let dbCr = 0;
      let allD = (w.type_completed == 1 || w.status === 'completed' || (exs.length > 0 && exs.every(ex => ex.is_completed == 1)));
      if(exs[0] && exs[0].round_number){
        const p = String(exs[0].round_number).split('/');
        if(p.length === 2){
          dbCr = parseInt(p[0]) || 0;
          if(totalR > 0 && dbCr >= totalR) allD = true;
        }
      }
      if(allD && dbCr === 0 && totalR > 0) {
        dbCr = totalR;
      }

      const ts = TS[w.id] = TS[w.id] || {
        elapsed: 0,
        running: false,
        paused: false,
        interval: null,
        phase: allD ? 'done' : 'idle',
        roundDuration: roundDur,
        phaseLeft: roundDur,
        round: allD ? totalR : (dbCr + 1),
        completedRounds: dbCr,
        totalRounds: totalR,
        allDone: allD
      };

      if(ts.completedRounds === 0 && dbCr > 0) ts.completedRounds = dbCr;
      if(allD || ts.allDone || ts.phase === 'done' || (totalR > 0 && (ts.completedRounds || 0) >= totalR)){
        ts.allDone = true;
        ts.phase = 'done';
        if(totalR > 0 && (ts.completedRounds || 0) < totalR) ts.completedRounds = totalR;
      }

      const firstExWithTime = exs.find(e => e.exercise_time && e.exercise_time !== '00:00');
      if(firstExWithTime && !ts.timeTaken) {
        ts.timeTaken = firstExWithTime.exercise_time;
      }

      const isComp = (w.type_completed == 1 || w.status === 'completed' || ts.allDone || ts.phase === 'done');
      const cr = isComp ? totalR : (ts.completedRounds || 0);
      const progStr = `${cr}/${totalR}`;
      const headerExtra = `<span class="wk-pbadge" id="emom-prog-${w.id}">${progStr}</span>`;

      // 1RM missing warning
      const hasMissing1RM = exs.some(ex => String(ex.unit_type || "%").trim() === "%" && (ex.training_load > 0 || ex.weight > 0) && (!ex.test_weight || ex.test_weight === 0));
      const warnHtml = hasMissing1RM ? `<div style="color:#fca5a5; font-size:12px; margin-bottom:8px; padding:0 4px;">⚠️ Please add your 1RM for accurate weight calculation.</div>` : '';

      // Progress bar (matching mobile app 8px height, rounded 5px)
      const pbarWidth = totalR > 0 ? Math.min(100, (cr / totalR) * 100) : 0;
      const pbarHtml = `${warnHtml}<div style="height:6px; width:100%; background:rgba(255,255,255,0.2); border-radius:4px; margin-bottom:10px; overflow:hidden;"><div id="emom-pbar-${w.id}" style="height:100%; width:${pbarWidth}%; background:#FFFFFF; border-radius:4px; transition:width 0.3s ease;"></div></div>`;

      // Exercise rows
      const isWorkPhase = ts.phase === 'work';
      const exHtml = exs.map(ex => {
        const nm = exName(ex);
        const wd = dispWt(ex.training_load ?? ex.weight ?? 0, ex.unit_type || '%', ex.test_weight ?? 0);
        const reps = ex.reps ?? 0;
        return emomExRow(nm, wd, reps);
      }).join('');

      const exContainer = `<div id="emom-ex-cont-${w.id}" style="border-radius:8px; padding:4px 0; transition:background .2s; ${isWorkPhase ? 'background:rgba(255,255,255,0.1);' : ''}">${exHtml}</div>`;

      return wkCard(w.id, 'EMOM', headerExtra, pbarHtml + exContainer, isComp, ts.timeTaken);
    }
</script>

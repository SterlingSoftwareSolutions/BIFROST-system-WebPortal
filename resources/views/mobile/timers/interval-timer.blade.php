<script>
    // ── INTERVALS TIMER ────────────────────────────────────────────
    function updateIntervalActiveHighlight(wId){
      const ts = TS[wId];
      if(!ts) return;
      const isRunning = (ts.running || ts.paused) && ts.phase !== 'done';
      let idx = 0;
      while(true){
        const el = document.getElementById(`int-exs-${wId}-${idx}`);
        if(!el) break;
        const isActive = isRunning && (ts.round === idx + 1);
        el.style.background = isActive ? 'rgba(255,255,255,0.12)' : 'transparent';
        const exNames = el.querySelectorAll('.ex-name');
        exNames.forEach(nameEl => {
          nameEl.style.fontWeight = isActive ? '700' : '500';
        });
        idx++;
      }
    }

    function startInt(wId){
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      if(!w) return;
      const ts = TS[wId];
      if(!ts || ts.allDone || ts.phase === 'done') return;
      if(ts.running){ toggleInt(wId); return; }
      if(ts.paused){ toggleInt(wId); return; }

      ts.round = ts.round || 1;
      const stnTimes = typeof getIntStationTimes === 'function' ? getIntStationTimes(w, ts.round) : { workSecs: 40, restSecs: 20, totalStations: 1 };
      ts.workSecs = stnTimes.workSecs;
      ts.restSecs = stnTimes.restSecs;
      ts.totalRounds = ts.totalRounds || w.number || stnTimes.totalStations || 1;
      ts.phase = 'work';
      ts.phaseLeft = ts.workSecs;
      
      countdown10(wId, () => {
        ts.running = true;
        ts.paused = false;
        const pb = document.getElementById('int-pb-' + wId);
        if(pb) pb.textContent = 'PAUSE';
        updateIntervalActiveHighlight(wId);
        updBbar();
        if(ts.interval) clearInterval(ts.interval);
        ts.interval = setInterval(() => {
          ts.elapsed++;
          ts.phaseLeft--;

          if(ts.phaseLeft === 3 || ts.phaseLeft === 2 || ts.phaseLeft === 1){
            playTimerBeep(600, 0.12);
          }

          const c = ts.phase === 'work' ? '#22c55e' : '#ef4444';
          const te = document.getElementById('int-t-' + wId);
          const pe = document.getElementById('int-p-' + wId);
          const prg = document.getElementById('int-prog-' + wId);
          if(te){ te.textContent = fmt(ts.phaseLeft); te.style.color = c; }
          if(pe){ pe.textContent = ts.phase === 'work' ? '🔥 WORK' : '😮‍💨 REST'; pe.style.color = c; }
          if(prg) prg.textContent = `${Math.max(0, ts.round - 1)}/${ts.totalRounds}`;
          updCenterBtn(w, 'intervals', ts);

          if(ts.phaseLeft <= 0){
            if(ts.phase === 'work' && ts.restSecs > 0){
              // Transition to rest: current station remains highlighted, badge stays at previous completed count
              ts.phase = 'rest';
              ts.phaseLeft = ts.restSecs;
              updBbar();
              updateIntervalActiveHighlight(wId);
            } else {
              // Rest is finished (or no rest needed): current station completes, then moves to next
              advInt(wId);
            }
          }
        }, 1000);
      });
    }

    async function advInt(wId){
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      const ts = TS[wId];
      if(!ts || !w) return;

      const completedRound = ts.round;

      // 1. Mark completed station exercises as done
      const stnInfo = typeof getIntStationTimes === 'function' ? getIntStationTimes(w, completedRound) : null;
      if (stnInfo && stnInfo.iNums && stnInfo.iMap) {
        const completedStnNum = stnInfo.iNums[completedRound - 1];
        if (completedStnNum !== undefined && stnInfo.iMap[completedStnNum]) {
          stnInfo.iMap[completedStnNum].forEach(ex => {
            CT[`${w.id}-${ex.id}`] = true;
            ex.is_completed = 1;
          });
        }
      }

      // 2. Save progress for completed station
      const rProgress = `${completedRound}/${ts.totalRounds}`;
      try {
        await saveCondWk(w, null, null, rProgress, fmt(ts.elapsed || 0), null);
      } catch(e) {
        console.error('Error saving interval progress:', e);
      }

      // 3. Advance station
      ts.round++;

      if(ts.round > ts.totalRounds){
        clearInterval(ts.interval);
        ts.interval = null;
        ts.running = false;
        ts.phase = 'done';
        ts.allDone = true;
        ts.timeTaken = '00:00';
        w.type_completed = 1;
        w.status = 'completed';
        (w.intervals || []).forEach(ex => {
          ex.is_completed = 1;
          ex.round_number = `${ts.totalRounds}/${ts.totalRounds}`;
          ex.exercise_time = '00:00';
          CT[`${w.id}-${ex.id}`] = true;
        });
        try {
          await saveCondWk(w, null, null, `${ts.totalRounds}/${ts.totalRounds}`, '00:00', null);
        } catch(e) {
          console.error('Error saving interval completion:', e);
        }
        showBanner(wId, 'intervals', true, '00:00');
        if(wId === selId){
          wkStatus = 'completed';
          updBbar();
        }
        const prg = document.getElementById('int-prog-' + wId);
        if(prg) prg.textContent = `${ts.totalRounds}/${ts.totalRounds}`;
        renderList();
        updateIntervalActiveHighlight(wId);
      } else {
        const nextTimes = typeof getIntStationTimes === 'function' ? getIntStationTimes(w, ts.round) : { workSecs: 40, restSecs: 20 };
        ts.workSecs = nextTimes.workSecs;
        ts.restSecs = nextTimes.restSecs;
        ts.phase = 'work';
        ts.phaseLeft = ts.workSecs;
        renderList();
        updateIntervalActiveHighlight(wId);
        updBbar();
      }
    }

    function toggleInt(wId){
      const ts = TS[wId];
      if(!ts) return;
      ts.paused = !ts.paused;
      ts.running = !ts.paused;
      if(ts.paused){
        clearInterval(ts.interval);
        ts.interval = null;
        updBbar();
        updateIntervalActiveHighlight(wId);
      } else {
        const list = wData[tab] || [];
        const w = list.find(x => x.id === wId);
        updBbar();
        updateIntervalActiveHighlight(wId);
        if(ts.interval) clearInterval(ts.interval);
        ts.interval = setInterval(() => {
          ts.elapsed++;
          ts.phaseLeft--;

          if(ts.phaseLeft === 3 || ts.phaseLeft === 2 || ts.phaseLeft === 1){
            playTimerBeep(600, 0.12);
          }

          const c = ts.phase === 'work' ? '#22c55e' : '#ef4444';
          const te = document.getElementById('int-t-' + wId);
          const pe = document.getElementById('int-p-' + wId);
          const prg = document.getElementById('int-prog-' + wId);
          if(te){ te.textContent = fmt(ts.phaseLeft); te.style.color = c; }
          if(pe){ pe.textContent = ts.phase === 'work' ? '🔥 WORK' : '😮‍💨 REST'; pe.style.color = c; }
          if(prg) prg.textContent = `${Math.max(0, ts.round - 1)}/${ts.totalRounds}`;
          updCenterBtn(w, 'intervals', ts);

          if(ts.phaseLeft <= 0){
            if(ts.phase === 'work' && ts.restSecs > 0){
              ts.phase = 'rest';
              ts.phaseLeft = ts.restSecs;
              updBbar();
              updateIntervalActiveHighlight(wId);
            } else {
              advInt(wId);
            }
          }
        }, 1000);
      }
      const btn = document.getElementById('int-pb-' + wId);
      if(btn) btn.textContent = ts.paused ? 'RESUME' : 'PAUSE';
    }

    function stopInt(wId){
      const ts = TS[wId];
      if(!ts) return;
      clearInterval(ts.interval);
      ts.interval = null;
      ts.running = false;
      ts.paused = false;
      updBbar();
      updateIntervalActiveHighlight(wId);
    }

    async function skipInt(wId){
      const ts = TS[wId];
      if(!ts || ts.allDone || ts.phase === 'done') return;
      if(!ts.running && !ts.paused && ts.phase === 'idle'){
        showMsg('Notice', "Please click 'START' to begin your workout.");
        return;
      }
      await advInt(wId);
    }
</script>

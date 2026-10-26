<script>
    // ── EMOM TIMER ─────────────────────────────────────────────────
    function startEmom(wId){
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      if(!w) return;
      const ts = TS[wId];
      if(!ts || ts.allDone) return;
      if(ts.running){ pauseEmom(wId); return; }
      if(ts.paused){ resumeEmom(wId); return; }

      countdown10(wId, () => {
        ts.running = true;
        ts.paused = false;
        ts.phase = 'work';
        ts.phaseLeft = ts.roundDuration || 60;

        const exCont = document.getElementById('emom-ex-cont-' + wId);
        if(exCont) exCont.style.background = 'rgba(255,255,255,0.1)';

        updBbar();

        if(ts.interval) clearInterval(ts.interval);
        ts.interval = setInterval(() => {
          ts.elapsed++;
          ts.phaseLeft--;

          if(ts.phaseLeft === 3 || ts.phaseLeft === 2 || ts.phaseLeft === 1){
            playTimerBeep(600, 0.12);
          }

          updCenterBtn(w, 'emom', ts);

          if(ts.phaseLeft <= 0){
            advEmom(wId);
          }
        }, 1000);
      });
    }

    function pauseEmom(wId){
      const ts = TS[wId];
      if(!ts || !ts.running) return;
      ts.paused = true;
      ts.running = false;
      if(ts.interval){ clearInterval(ts.interval); ts.interval = null; }
      updBbar();
    }

    function resumeEmom(wId){
      const ts = TS[wId];
      if(!ts || !ts.paused) return;
      ts.paused = false;
      ts.running = true;
      updBbar();
      if(ts.interval) clearInterval(ts.interval);
      ts.interval = setInterval(() => {
        ts.elapsed++;
        ts.phaseLeft--;
        if(ts.phaseLeft === 3 || ts.phaseLeft === 2 || ts.phaseLeft === 1){
          playTimerBeep(600, 0.12);
        }
        updCenterBtn(wData[tab]?.find(x => x.id === wId) || {}, 'emom', ts);
        if(ts.phaseLeft <= 0){
          advEmom(wId);
        }
      }, 1000);
    }

    function stopEmom(wId){
      pauseEmom(wId);
    }

    function skipEmom(wId){
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      const ts = TS[wId];
      if(!w || !ts) return;
      if(!ts.running && !ts.paused && !ts.allDone){
        showMsg('Notice', "Please click 'START' to begin your workout.");
        return;
      }
      if(ts.allDone) return;
      advEmom(wId);
    }

    async function advEmom(wId){
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      const ts = TS[wId];
      if(!ts || !w) return;
      playTimerBeep(1000, 0.35);

      const completedRoundNum = ts.round;
      ts.completedRounds = (ts.completedRounds || 0) + 1;
      const isFinal = ts.completedRounds >= ts.totalRounds;
      const stoppedSec = Math.max(0, ts.phaseLeft !== undefined ? ts.phaseLeft : (ts.roundDuration || 60));
      const timeStr = fmt(stoppedSec);
      const rProgress = `${completedRoundNum}/${ts.totalRounds}`;

      try {
        await saveCondWk(w, null, null, rProgress, timeStr, null);
      } catch(e) {
        console.error('Error saving EMOM round:', e);
      }

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
        ts.timeTaken = timeStr;
        w.type_completed = 1;
        w.status = 'completed';
        (w.emoms || []).forEach(ex => {
          ex.is_completed = 1;
          ex.round_number = rProgress;
          ex.exercise_time = timeStr;
        });

        const exCont = document.getElementById('emom-ex-cont-' + wId);
        if (exCont) exCont.style.background = 'transparent';

        showBanner(wId, 'emom', true, timeStr);
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
</script>

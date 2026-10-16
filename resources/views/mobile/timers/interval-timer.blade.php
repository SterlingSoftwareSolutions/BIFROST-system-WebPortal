<script>
    // ── INTERVALS TIMER ────────────────────────────────────────────
    function startInt(wId){
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      if(!w) return;
      const ts = TS[wId];
      if(ts.running){ toggleInt(wId); return; }
      const fe = w.intervals && w.intervals[0] || {};
      ts.workSecs = parseSec(fe.work || w.work) || 40;
      ts.restSecs = parseSec(fe.rest || w.rest) || 20;
      ts.totalRounds = ts.totalRounds || w.number || (w.intervals || []).length || 1;
      ts.round = ts.round || 1;
      ts.phase = 'work';
      ts.phaseLeft = ts.workSecs;
      
      countdown10(wId, () => {
        ts.running = true;
        ts.paused = false;
        const pb = document.getElementById('int-pb-' + wId);
        if(pb) pb.textContent = 'PAUSE';
        if(ts.interval) clearInterval(ts.interval);
        ts.interval = setInterval(() => {
          ts.elapsed++;
          ts.phaseLeft--;
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
            } else {
              advInt(wId);
            }
          }
        }, 1000);
      });
    }

    function advInt(wId){
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      const ts = TS[wId];
      if(!ts) return;
      ts.round++;
      if(w) saveCondWk(w, null, null, null, fmt(ts.elapsed || 0), null);
      if(ts.round > ts.totalRounds){
        clearInterval(ts.interval);
        ts.interval = null;
        ts.running = false;
        ts.phase = 'done';
        ts.allDone = true;
        showBanner(wId, 'intervals', true, fmt(ts.elapsed));
        if(wId === selId){
          wkStatus = 'completed';
          updBbar();
        }
        const prg = document.getElementById('int-prog-' + wId);
        if(prg) prg.textContent = `${ts.totalRounds}/${ts.totalRounds}`;
      } else {
        ts.phase = 'work';
        ts.phaseLeft = ts.workSecs;
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
      } else {
        const list = wData[tab] || [];
        const w = list.find(x => x.id === wId);
        ts.interval = setInterval(() => {
          ts.elapsed++;
          ts.phaseLeft--;
          const c = ts.phase === 'work' ? '#22c55e' : '#ef4444';
          const te = document.getElementById('int-t-' + wId);
          if(te){ te.textContent = fmt(ts.phaseLeft); te.style.color = c; }
          updCenterBtn(w, 'intervals', ts);
          if(ts.phaseLeft <= 0){
            if(ts.phase === 'work' && ts.restSecs > 0){
              ts.phase = 'rest';
              ts.phaseLeft = ts.restSecs;
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
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      updCenterBtn(w, 'intervals', ts);
    }

    function skipInt(wId){
      const ts = TS[wId];
      if(!ts || !ts.running) return;
      ts.phaseLeft = 0;
    }
</script>

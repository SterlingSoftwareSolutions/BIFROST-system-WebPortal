<script>
    // ── FOR TIME TIMER ─────────────────────────────────────────────
    function startFT(wId){
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      if(!w) return;
      const ts = TS[wId];
      if(ts.running){ toggleFT(wId); return; }
      const cap = parseSec(w.time_to_complete || (w.for_times && w.for_times[0] && w.for_times[0].time_to_complete));
      ts.limit = cap;
      countdown10(wId, () => {
        ts.running = true;
        ts.paused = false;
        const pb = document.getElementById('ft-pb-' + wId);
        if(pb) pb.textContent = 'PAUSE';
        if(ts.interval) clearInterval(ts.interval);
        ts.interval = setInterval(() => {
          ts.elapsed++;
          const disp = ts.limit > 0 ? Math.max(0, ts.limit - ts.elapsed) : ts.elapsed;
          const el = document.getElementById('ft-t-' + wId);
          if(el) el.textContent = fmt(disp);
          updCenterBtn(w, 'for-time', ts);
          if(ts.limit > 0 && ts.elapsed >= ts.limit){
            clearInterval(ts.interval);
            ts.running = false;
            finishFT(wId, false);
          }
        }, 1000);
      });
    }

    function toggleFT(wId){
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
          const disp = ts.limit > 0 ? Math.max(0, ts.limit - ts.elapsed) : ts.elapsed;
          const el = document.getElementById('ft-t-' + wId);
          if(el) el.textContent = fmt(disp);
          updCenterBtn(w, 'for-time', ts);
          if(ts.limit > 0 && ts.elapsed >= ts.limit){
            clearInterval(ts.interval);
            ts.running = false;
            finishFT(wId, false);
          }
        }, 1000);
      }
      const btn = document.getElementById('ft-pb-' + wId);
      if(btn) btn.textContent = ts.paused ? 'RESUME' : 'PAUSE';
    }

    function stopFT(wId){
      const ts = TS[wId];
      if(!ts) return;
      clearInterval(ts.interval);
      ts.interval = null;
      ts.running = false;
      ts.paused = false;
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      updCenterBtn(w, 'for-time', ts);
    }

    function finishFT(wId, ok){
      const ts = TS[wId] || {};
      clearInterval(ts.interval);
      ts.interval = null;
      ts.running = false;
      showBanner(wId, 'for-time', ok, fmt(ts.elapsed));
      if(wId === selId){
        wkStatus = ok ? 'completed' : 'incompleted';
        updBbar();
      }
      renderList();
    }

    function chgFTRep(wId, exId, d){
      const el = document.getElementById(`ft-reps-${wId}-${exId}`);
      if(!el) return;
      let v = parseInt(el.textContent || '0') + d;
      if(v < 0) v = 0;
      el.textContent = v;
    }
</script>

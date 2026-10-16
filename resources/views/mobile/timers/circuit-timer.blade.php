<script>
    // ── CIRCUIT TIMER ──────────────────────────────────────────────
    function startCircuit(wId){
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      if(!w) return;
      const ts = TS[wId];
      if(ts.running){ toggleCircuit(wId); return; }
      const lim = parseSec(w.time_to_complete || (w.circuits && w.circuits[0] && w.circuits[0].time_to_complete));
      ts.limit = lim;
      countdown10(wId, () => {
        ts.running = true;
        ts.paused = false;
        const pb = document.getElementById('circuit-pb-' + wId);
        if(pb) pb.textContent = 'PAUSE';
        if(ts.interval) clearInterval(ts.interval);
        ts.interval = setInterval(() => {
          ts.elapsed++;
          const disp = ts.limit > 0 ? Math.max(0, ts.limit - ts.elapsed) : ts.elapsed;
          const el = document.getElementById('circuit-t-' + wId);
          if(el) el.textContent = fmt(disp);
          updCenterBtn(w, 'circuit', ts);
          if(ts.limit > 0 && ts.elapsed >= ts.limit){
            clearInterval(ts.interval);
            ts.running = false;
            finishCircuit(wId, false);
          }
        }, 1000);
      });
    }

    function toggleCircuit(wId){
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
          const el = document.getElementById('circuit-t-' + wId);
          if(el) el.textContent = fmt(disp);
          updCenterBtn(w, 'circuit', ts);
          if(ts.limit > 0 && ts.elapsed >= ts.limit){
            clearInterval(ts.interval);
            ts.running = false;
            finishCircuit(wId, false);
          }
        }, 1000);
      }
      const btn = document.getElementById('circuit-pb-' + wId);
      if(btn) btn.textContent = ts.paused ? 'RESUME' : 'PAUSE';
    }

    function stopCircuit(wId){
      const ts = TS[wId];
      if(!ts) return;
      clearInterval(ts.interval);
      ts.interval = null;
      ts.running = false;
      ts.paused = false;
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      updCenterBtn(w, 'circuit', ts);
    }

    function finishCircuit(wId, ok){
      const ts = TS[wId] || {};
      clearInterval(ts.interval);
      ts.interval = null;
      ts.running = false;
      showBanner(wId, 'circuit', ok, fmt(ts.elapsed));
      if(wId === selId){
        wkStatus = ok ? 'completed' : 'incompleted';
        updBbar();
      }
      renderList();
    }
</script>

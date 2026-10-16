<script>
    // ── ROUNDS TIMER ───────────────────────────────────────────────
    function startRounds(wId){
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      if(!w) return;
      const ts = TS[wId];
      if(ts.running) return;
      const lim = parseSec(w.time_to_complete || (w.rounds && w.rounds[0] && w.rounds[0].time_to_complete)) || 600;
      ts.limit = lim;
      countdown10(wId, () => {
        ts.running = true;
        ts.paused = false;
        if(ts.interval) clearInterval(ts.interval);
        ts.interval = setInterval(() => {
          ts.elapsed++;
          updCenterBtn(w, 'rounds', ts);
          if(ts.limit > 0 && ts.elapsed >= ts.limit){
            clearInterval(ts.interval);
            ts.running = false;
            finishRounds(wId, false);
          }
        }, 1000);
      });
    }

    function finishRounds(wId, ok){
      const ts = TS[wId] || {};
      clearInterval(ts.interval);
      ts.interval = null;
      ts.running = false;
      showBanner(wId, 'rounds', ok, fmt(ts.elapsed));
      if(wId === selId){
        wkStatus = ok ? 'completed' : 'incompleted';
        updBbar();
      }
      renderList();
    }
</script>

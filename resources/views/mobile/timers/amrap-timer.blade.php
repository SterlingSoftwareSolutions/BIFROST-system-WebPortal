<script>
    // ── AMRAP TIMER ────────────────────────────────────────────────
    function getAmrapSec(w){
      if(!w) return 0;
      const raw = w.time_to_complete || (w.amraps && w.amraps[0] && w.amraps[0].time_to_complete) || w.work || (w.amraps && w.amraps[0] && w.amraps[0].work);
      if(raw){
        const s = parseSec(raw);
        if(s > 0) return s;
      }
      const num = Number(w.number ?? 0);
      return num > 0 ? num : 0;
    }

    function startAmrap(wId){
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      if(!w) return;
      const ts = TS[wId];
      if(!ts || ts.allDone || ts.running) return;
      const lim = getAmrapSec(w);
      ts.limit = lim;
      countdown10(wId, () => {
        ts.running = true;
        ts.paused = false;
        updBbar();
        if(ts.interval) clearInterval(ts.interval);
        ts.interval = setInterval(() => {
          ts.elapsed++;
          const left = Math.max(0, ts.limit - ts.elapsed);
          
          if(left === 3 || left === 2 || left === 1){
            playTimerBeep(600, 0.12);
          }
          
          updCenterBtn(w, 'amrap', ts);
          
          if(left <= 0){
            clearInterval(ts.interval);
            ts.interval = null;
            ts.running = false;
            ts.allDone = true;
            
            playTimerBeep(1000, 0.35);
            const timeStr = '00:00';
            const rProgress = `${ts.rounds || 0}/${ts.totalRounds || 1}`;
            saveCondWk(w, null, null, rProgress, timeStr, null);
            
            w.type_completed = 1;
            w.status = 'completed';
            (w.amraps || []).forEach(ex => { ex.is_completed = 1; });
            
            showBanner(wId, 'amrap', true, fmt(ts.elapsed));
            if(wId === selId){
              wkStatus = 'completed';
              updBbar();
            }
            renderList();
          }
        }, 1000);
      });
    }

    function toggleAmrap(wId){}
    function stopAmrap(wId){}
</script>

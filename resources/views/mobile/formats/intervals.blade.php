<script>
    // ─── INTERVALS FORMAT ───────────────────────────────────────────
    function cardIntervals(w){
      const exs = w.intervals || [];
      const totalR = w.number || exs.length || 1;
      const fe = exs[0] || {};
      const wS = parseSec(fe.work || w.work) || 40;
      const rS = parseSec(fe.rest || w.rest) || 20;
      let allD = (w.type_completed == 1 || w.status === 'completed' || (exs.length > 0 && exs.every(ex => ex.is_completed == 1)));
      const ts = TS[w.id] = TS[w.id] || { elapsed: 0, running: false, paused: false, interval: null, phase: allD ? 'done' : 'idle', phaseLeft: wS, round: 1, totalRounds: totalR, workSecs: wS, restSecs: rS, allDone: allD };
      if(allD || ts.allDone || ts.phase === 'done') ts.allDone = true;
      const isComp = (w.type_completed == 1 || w.status === 'completed' || ts.allDone || ts.phase === 'done');
      const iMap = {};
      exs.forEach(ex => {
        const n = ex.stationumber ?? ex.station ?? ex.number ?? 1;
        if(!iMap[n]) iMap[n] = [];
        iMap[n].push(ex);
      });
      const iNums = Object.keys(iMap).sort((a,b) => a - b);
      let iHtml = iNums.map((n, idx) => {
        const exRows = iMap[n].map(ex => plainExRow(exName(ex), dispWt(ex.training_load ?? 0, ex.unit_type || '%', ex.test_weight ?? 0), ex.reps ?? 0)).join('');
        const divider = idx < iNums.length - 1 ? `<div style="height:1px; background:rgba(255,255,255,0.2); width:100%; margin:4px 0 16px 0;"></div>` : '';
        return `<div class="grp-row"><div class="grp-num">${idx+1}</div><div class="grp-exs" style="margin-bottom:0;">${exRows}</div></div>${divider}`;
      }).join('');
      
      const cRound = isComp ? ts.totalRounds : Math.max(0, ts.round - 1);
      return wkCard(w.id, 'Intervals', `<span class="wk-pbadge" id="int-prog-${w.id}">${cRound}/${ts.totalRounds}</span>`, iHtml, isComp, ts.timeTaken);
    }
</script>

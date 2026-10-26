<script>
    // ─── INTERVALS FORMAT ───────────────────────────────────────────
    function cardIntervals(w){
      const exs = w.intervals || [];
      const iMap = {};
      exs.forEach(ex => {
        const n = ex.stationumber ?? ex.station ?? ex.number ?? 1;
        if(!iMap[n]) iMap[n] = [];
        iMap[n].push(ex);
      });
      const iNums = Object.keys(iMap).sort((a,b) => a - b);
      const totalR = w.number || (iNums.length > 0 ? iNums.length : exs.length) || 1;
      let dbCr = 0;
      let allD = (w.type_completed == 1 || w.status === 'completed' || (exs.length > 0 && exs.every(ex => ex.is_completed == 1)));
      if(exs[0] && exs[0].round_number){
        const p = String(exs[0].round_number).split('/');
        if(p.length === 2){
          dbCr = parseInt(p[0]) || 0;
          if(totalR > 0 && dbCr >= totalR) allD = true;
        }
      }
      if(allD && dbCr === 0 && totalR > 0) dbCr = totalR;

      const curRound = allD ? totalR : (dbCr + 1);
      const stnTimes = typeof getIntStationTimes === 'function' ? getIntStationTimes(w, curRound) : { workSecs: 40, restSecs: 20 };
      const wS = stnTimes.workSecs;
      const rS = stnTimes.restSecs;

      const ts = TS[w.id] = TS[w.id] || { 
        elapsed: 0, 
        running: false, 
        paused: false, 
        interval: null, 
        phase: allD ? 'done' : 'idle', 
        phaseLeft: wS, 
        round: curRound, 
        totalRounds: totalR, 
        workSecs: wS, 
        restSecs: rS, 
        allDone: allD 
      };

      if(!ts.running && !ts.paused && ts.phase !== 'done'){
        const curTimes = typeof getIntStationTimes === 'function' ? getIntStationTimes(w, ts.round || 1) : { workSecs: 40, restSecs: 20 };
        ts.workSecs = curTimes.workSecs;
        ts.restSecs = curTimes.restSecs;
        if(ts.phase === 'idle'){
          ts.phaseLeft = curTimes.workSecs;
        }
      }
      if(allD || ts.allDone || ts.phase === 'done') {
        ts.allDone = true;
        ts.phase = 'done';
      }
      const isComp = (w.type_completed == 1 || w.status === 'completed' || ts.allDone || ts.phase === 'done');
      const firstExWithTime = exs.find(e => e.exercise_time && e.exercise_time !== '00:00');
      if(firstExWithTime && !ts.timeTaken) {
        ts.timeTaken = firstExWithTime.exercise_time;
      }
      let iHtml = iNums.map((n, idx) => {
        const isCurrentActive = (ts.running || ts.paused) && ts.phase !== 'done' && (ts.round === idx + 1);
        const exRows = iMap[n].map(ex => {
          const isDone = isComp || ex.is_completed == 1 || !!CT[`${w.id}-${ex.id}`];
          return intervalExRow(exName(ex), dispWt(ex.training_load ?? 0, ex.unit_type || '%', ex.test_weight ?? 0), ex.reps ?? 0, isDone, isCurrentActive);
        }).join('');
        const divider = idx < iNums.length - 1 ? `<div style="height:1px; background:rgba(255,255,255,0.2); width:100%; margin:4px 0 10px 0;"></div>` : '';
        return `<div class="grp-row" id="int-grp-${w.id}-${idx}"><div class="grp-num">${idx+1}</div><div class="grp-exs" id="int-exs-${w.id}-${idx}" style="margin-bottom:0;border-radius:6px;padding:3px 4px;transition:background .2s;${isCurrentActive ? 'background:rgba(255,255,255,0.12);' : ''}">${exRows}</div></div>${divider}`;
      }).join('');
      
      const cRound = isComp ? ts.totalRounds : Math.max(0, ts.round - 1);
      return wkCard(w.id, 'Intervals', `<span class="wk-pbadge" id="int-prog-${w.id}">${cRound}/${ts.totalRounds}</span>`, iHtml, isComp, ts.timeTaken);
    }
</script>

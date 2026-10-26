<script>
    // ─── CIRCUIT FORMAT ─────────────────────────────────────────────
    function cardCircuit(w){
      const exs = w.circuits || [];
      const lim = parseSec(w.time_to_complete || (exs[0] && exs[0].time_to_complete));
      const isForTime = Number(w.is_for_time || 0) === 1 || Number(w.is_fortime || 0) === 1 || Number(w.for_time || 0) === 1 || exs.some(e => Number(e.is_for_time || 0) === 1 || Number(e.is_fortime || 0) === 1 || Number(e.for_time || 0) === 1);
      
      const sMap = {};
      exs.forEach(ex => {
        const sn = ex.stationumber ?? ex.station_number ?? ex.station ?? 1;
        if(!sMap[sn]) sMap[sn] = [];
        sMap[sn].push(ex);
      });
      const sns = Object.keys(sMap).sort((a,b) => a - b);

      let compStations = sns.filter(sn => {
        return w.type_completed == 1 || w.status === 'completed' ||
          sMap[sn].every(ex => ex.is_completed == 1 || !!CT[`${w.id}-stn-${sn}`] || !!CT[`${w.id}-${ex.id}`]);
      }).length;

      let allD = (w.type_completed == 1 || w.status === 'completed' || (sns.length > 0 && compStations >= sns.length) || (exs.length > 0 && exs.every(ex => ex.is_completed == 1)));
      if(allD && compStations === 0 && sns.length > 0) compStations = sns.length;
      const ts = TS[w.id] = TS[w.id] || { elapsed: 0, running: false, paused: false, interval: null, limit: lim, allDone: allD, completedStations: compStations };
      if(allD || ts.allDone || (sns.length > 0 && (ts.completedStations || 0) >= sns.length)) {
        ts.allDone = true;
        if(sns.length > 0 && (ts.completedStations || 0) < sns.length) ts.completedStations = sns.length;
      }
      const isComp = (w.type_completed == 1 || w.status === 'completed' || ts.allDone || (sns.length > 0 && compStations >= sns.length));
      if(isComp){
        w.type_completed = 1;
        w.status = 'completed';
        ts.allDone = true;
        if(w.id == selId && wkStatus === 'incompleted'){
          wkStatus = 'completed';
        }
      }
      let bestTime = null;
      let maxSec = -1;
      exs.forEach(e => {
        if(e.exercise_time && e.exercise_time !== '00:00'){
          const s = parseSec(e.exercise_time);
          if(s >= maxSec){
            maxSec = s;
            bestTime = e.exercise_time;
          }
        }
      });
      if(bestTime) {
        if(!ts.timeTaken || (isComp && parseSec(ts.timeTaken) < maxSec)) {
          ts.timeTaken = bestTime;
        }
      }

      const stHtml = sns.map((sn, idx) => {
        const exRows = sMap[sn].map(ex => {
          const nm = exName(ex);
          const wd = dispWt(ex.training_load ?? ex.weight ?? 0, ex.unit_type || '%', ex.test_weight ?? 0);
          const reps = ex.reps_progress ?? ex.res ?? ex.reps ?? 0;
          const isDone = isComp || ex.is_completed == 1 || !!CT[`${w.id}-stn-${sn}`] || !!CT[`${w.id}-${ex.id}`];
          return plainExRow(nm, wd, reps, isDone);
        }).join('');
        const divider = idx < sns.length - 1
          ? `<div style="height:1px; background:rgba(255,255,255,0.2); width:100%; margin:4px 0 10px 0;"></div>`
          : '';
        return `<div class="grp-row" style="display:flex;align-items:flex-start;margin-bottom:0;width:100%;">
          <div class="grp-num" style="width:20px;display:flex;align-items:flex-start;justify-content:flex-start;padding-top:1px;color:#fff;font-weight:700;font-size:12px;">${idx + 1}</div>
          <div class="grp-exs" style="margin-bottom:0;flex:1;">
            ${exRows}
          </div>
        </div>${divider}`;
      }).join('');

      const cDisplay = isComp ? sns.length : compStations;
      const headerExtra = `<span class="wk-pbadge" id="circuit-prog-${w.id}">${cDisplay}/${Math.max(sns.length, 1)}</span>`;

      return wkCard(w.id, 'Circuit', headerExtra, stHtml, isComp, ts.timeTaken);
    }

    async function saveCircuitStation(w, stationNum, timeStr){
      const exs = w.circuits || [];
      const sExercises = exs.filter(ex => (ex.stationumber ?? ex.station_number ?? ex.station ?? 1) == stationNum);
      if(!sExercises.length) return;
      sExercises.forEach(ex => {
        ex.exercise_time = timeStr || '00:00';
      });
      const dateStr = typeof workoutDayStr === 'function' ? workoutDayStr() : DAY;
      let ep = '/save-conditioning-workout';
      if(tab === 'warmup') ep = '/warmup-daily';
      else if(tab === 'strength') ep = '/save-strength-workout';
      else if(tab === 'weightlifting') ep = '/save-weightlifting-workout';
      else if(tab === 'accessory') ep = '/save-accessory-workout';

      const sMapAll = {};
      exs.forEach(e => {
        const sn = e.stationumber ?? e.station_number ?? e.station ?? 1;
        sMapAll[sn] = true;
      });
      const totalStations = Object.keys(sMapAll).length || 1;

      const payload = sExercises.map(ex => ({
        workout_manager_id: w.id,
        workout_format_type: 'circuit',
        workout_format_id: ex.id || 0,
        reps: parseInt(ex.reps_progress ?? ex.res ?? ex.reps ?? 0) || 0,
        set_number: parseInt(stationNum) || 1,
        round_number: `${stationNum}/${totalStations}`,
        weight: calcWt(ex.training_load ?? 0, ex.unit_type || '%', ex.test_weight ?? 0),
        date: dateStr,
        exercise_time: timeStr || '00:00',
        class_Id: CLSID,
        notes: null
      }));
      try {
        const res = await fetch(ep, {method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF}, body:JSON.stringify(payload)});
        const data = await res.json().catch(() => null);
        if(!res.ok || (data && data.success === false)) {
          console.error('[saveCircuitStation] Failed:', data);
          throw new Error(data?.message || data?.error || 'Save failed');
        }
      } catch(e) { console.error('Error saving circuit station:', e); throw e; }
    }
</script>

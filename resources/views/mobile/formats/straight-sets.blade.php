<script>
    // ─── BUILD STATS BANNER FOR COMPLETED STRAIGHT SETS ───────────
    function buildSSStatsHtml(w, straightsArg = null, totalSetsArg = null) {
      if (!w) return '';
      const straights = straightsArg || w.straights || w.straight || [];
      const totalSets = (totalSetsArg !== null && totalSetsArg !== undefined) 
        ? totalSetsArg 
        : (straights.length > 0 && straights[0].sets ? straights[0].sets.length : 0);

      let calcTotalSets = 0;
      let calcTotalReps = 0;
      let calcTotalWeight = 0;

      straights.forEach(ex => {
        for(let si = 0; si < totalSets; si++) {
          const sets = ex.sets || [];
          const set = sets[si] || {};
          const unit = (set.unittype || set.unit_type || ex.unittype || ex.unit_type || '%').trim().toUpperCase();
          const isNoRepsUnit = unit === 'CAL' || unit === 'M';

          let repsValue = 0;
          if (!isNoRepsUnit) {
            const tr = set.res ?? set.target_reps ?? set.reps ?? ex.reps ?? 0;
            let sr = (set.daily_reps !== undefined && set.daily_reps !== null)
              ? set.daily_reps
              : ((set.reps_progress !== undefined && set.reps_progress !== null) ? set.reps_progress : tr);
            if (Number(sr) === 0 && !set._user_edited) {
              if (tr > 0) {
                sr = tr;
              } else if (si > 0) {
                const ps = ex.sets[si - 1];
                if (ps) {
                  const pt = ps.res ?? ps.target_reps ?? ps.reps ?? ex.reps ?? 0;
                  sr = (ps.daily_reps !== undefined && ps.daily_reps !== null)
                    ? ps.daily_reps
                    : ((ps.reps_progress !== undefined && ps.reps_progress !== null) ? ps.reps_progress : pt);
                }
              }
            }
            repsValue = Number(sr) || 0;
          }

          // Cumulative missed reps for FRM
          const cumulativeMissedReps = (ex.sets || [])
            .slice(0, si)
            .reduce((acc, prevSet, prevIdx) => {
              const prevKey = `${w.id}-${ex.id||0}-${prevSet.id||prevIdx}`;
              const apiComp = w.type_completed == 1 || prevSet.is_completed == 1;
              const isPrevCompleted = apiComp || !!CT[prevKey];
              if (isPrevCompleted) {
                const pTarget = prevSet.res ?? prevSet.target_reps ?? prevSet.reps ?? ex.reps ?? 0;
                const pReps = prevSet.daily_reps ?? prevSet.reps_progress ?? pTarget;
                const missed = Math.max(0, pTarget - pReps);
                return acc + missed;
              }
              return acc;
            }, 0);

          const dailySavedWeight = (set.daily_weight != null && Number(set.daily_weight) > 0 && cumulativeMissedReps === 0)
            ? set.daily_weight : null;
          const ld = set.training_load ?? ex.training_load ?? ex.weight ?? 0;
          const wd = dailySavedWeight !== null
            ? `${Math.round(dailySavedWeight)} KG`
            : dispWt(ld, unit, ex.test_weight ?? w.test_weight ?? 0, cumulativeMissedReps);

          const isWeightBearingUnit = unit === 'KG' || unit === 'LBS' || unit === '%';
          let weightNum = 0;
          if (isWeightBearingUnit) {
            const m = String(wd).match(/[\d.]+/);
            weightNum = m ? parseFloat(m[0]) : 0;
          }

          calcTotalSets += 1;
          calcTotalReps += Number(repsValue);
          calcTotalWeight += Number(repsValue) * weightNum;
        }
      });

      const formattedWeight = Math.round(calcTotalWeight * 10) / 10;
      const weightDisplay = formattedWeight > 0 ? formattedWeight : '-';

      return `<div class="wk-stats-container" style="display:flex; flex-direction:row; margin-top:10px; margin-bottom:20px; gap:12px; justify-content:center; width:100%; box-sizing:border-box; cursor:pointer;">
        <div class="wk-stat-box" style="background-color:rgba(242,153,74,0.15); border:1px solid rgba(242,153,74,0.5); border-radius:10px; padding:8px 6px; display:flex; flex-direction:column; align-items:center; justify-content:center; flex:1; max-width:100px; box-sizing:border-box;">
          <span class="wk-stat-label" style="color:rgba(255,255,255,0.8); font-size:11px; font-weight:600; margin-bottom:4px; text-align:center;">Total Sets</span>
          <span class="wk-stat-value" style="color:#fff; font-size:15px; font-weight:bold; text-align:center;">${calcTotalSets}</span>
        </div>
        <div class="wk-stat-box" style="background-color:rgba(242,153,74,0.15); border:1px solid rgba(242,153,74,0.5); border-radius:10px; padding:8px 6px; display:flex; flex-direction:column; align-items:center; justify-content:center; flex:1; max-width:100px; box-sizing:border-box;">
          <span class="wk-stat-label" style="color:rgba(255,255,255,0.8); font-size:11px; font-weight:600; margin-bottom:4px; text-align:center;">Total Reps</span>
          <span class="wk-stat-value" style="color:#fff; font-size:15px; font-weight:bold; text-align:center;">${calcTotalReps}</span>
        </div>
        <div class="wk-stat-box" style="background-color:rgba(242,153,74,0.15); border:1px solid rgba(242,153,74,0.5); border-radius:10px; padding:8px 6px; display:flex; flex-direction:column; align-items:center; justify-content:center; flex:1; max-width:100px; box-sizing:border-box;">
          <span class="wk-stat-label" style="color:rgba(255,255,255,0.8); font-size:11px; font-weight:600; margin-bottom:4px; text-align:center;">Total Weight</span>
          <span class="wk-stat-value" style="color:#fff; font-size:15px; font-weight:bold; text-align:center;">${weightDisplay}</span>
        </div>
      </div>`;
    }
    window.buildSSStatsHtml = buildSSStatsHtml;

    function cardSS(w){
      const straights = w.straights || [];
      const totalSets = straights.length > 0 && straights[0].sets ? straights[0].sets.length : 0;

      // Seed CT from server-side is_completed
      for(let i = 0; i < totalSets; i++){
        straights.forEach(ex => {
          const s = ex.sets && ex.sets[i];
          if(s && (s.is_completed == 1 || w.type_completed == 1 || w.status === 'completed')){
            CT[`${w.id}-${ex.id||0}-${s.id||i}`] = true;
          }
        });
      }

      let activeSetIdx = -1;
      let doneCount = 0;
      for(let i = 0; i < totalSets; i++){
        const isDone = straights.every(ex => {
          const s = ex.sets && ex.sets[i];
          if(!s) return true;
          return s.is_completed == 1 || !!CT[`${w.id}-${ex.id||0}-${s.id||i}`];
        });
        if(isDone) doneCount++;
        else if(activeSetIdx === -1) activeSetIdx = i;
      }
      const hasMissing1RM = straights.some(ex => {
        const sets = ex.sets || [];
        return sets.some(s => {
          const u = (s.unittype || s.unit_type || ex.unittype || ex.unit_type || '%').trim().toUpperCase();
          const ldVal = s.training_load ?? ex.training_load ?? ex.weight ?? 0;
          const tw = ex.test_weight ?? w.test_weight ?? 0;
          return u === '%' && ldVal > 0 && (!tw || tw === 0);
        });
      });
      const warnHtml = hasMissing1RM ? `<div style="color:#fca5a5; font-size:13px; margin-bottom:12px; padding:0 4px;">⚠️ Please add your 1RM for accurate weight calculation.</div>` : '';

      let rows = '';
      for(let si = 0; si < totalSets; si++){
        let exHtml = '';
        straights.forEach(ex => {
          const sets = ex.sets || [], set = sets[si] || {};
          const nm = exName(ex);
          // StraightSet DB column is 'res' for reps, 'unittype' (no underscore) for unit
          // API also sets 'target_reps' explicitly on each set
          const tr = set.res ?? set.target_reps ?? set.reps ?? ex.reps ?? 0;
          let sr = (set.daily_reps !== undefined && set.daily_reps !== null)
            ? set.daily_reps
            : ((set.reps_progress !== undefined && set.reps_progress !== null) ? set.reps_progress : tr);
          if (Number(sr) === 0 && !set._user_edited) {
            if (tr > 0) {
              sr = tr;
            } else if (si > 0) {
              const ps = ex.sets[si - 1];
              if (ps) {
                const pt = ps.res ?? ps.target_reps ?? ps.reps ?? ex.reps ?? 0;
                sr = (ps.daily_reps !== undefined && ps.daily_reps !== null)
                  ? ps.daily_reps
                  : ((ps.reps_progress !== undefined && ps.reps_progress !== null) ? ps.reps_progress : pt);
              }
            }
          }
          const ld = set.training_load ?? ex.training_load ?? ex.weight ?? 0;
          // StraightSet uses 'unittype' not 'unit_type', default '%' matching mobile app
          const unit = (set.unittype || set.unit_type || ex.unittype || ex.unit_type || '%').trim().toUpperCase();

          // Cumulative missed reps for FRM (exact copy of mobile app logic)
          const cumulativeMissedReps = (ex.sets || [])
            .slice(0, si)
            .reduce((acc, prevSet, prevIdx) => {
              const prevKey = `${w.id}-${ex.id||0}-${prevSet.id||prevIdx}`;
              const apiComp = w.type_completed == 1 || prevSet.is_completed == 1;
              const isPrevCompleted = apiComp || !!CT[prevKey];
              if (isPrevCompleted) {
                const pTarget = prevSet.res ?? prevSet.target_reps ?? prevSet.reps ?? ex.reps ?? 0;
                const pReps = prevSet.daily_reps ?? prevSet.reps_progress ?? pTarget;
                const missed = Math.max(0, pTarget - pReps);
                return acc + missed;
              }
              return acc;
            }, 0);

          const dailySavedWeight = (set.daily_weight != null && Number(set.daily_weight) > 0 && cumulativeMissedReps === 0)
            ? set.daily_weight : null;
          const wd = dailySavedWeight !== null
            ? `${Math.round(dailySavedWeight)} KG`
            : dispWt(ld, unit, ex.test_weight ?? w.test_weight ?? 0, cumulativeMissedReps);

          const noReps = unit === 'CAL' || unit === 'M';
          if(noReps){
            exHtml += `<div class="ex-row" style="margin-bottom:8px;"><span class="ex-name" style="flex:2;padding-left:8px;font-size:12px;">${nm}</span><div style="flex:1;display:flex;justify-content:center;align-items:center;"><span class="wbox" style="flex:none;min-width:50px;font-size:12px;">${wd}</span></div><div class="rbtn-wrap" style="transform:translateX(12px);flex:1;display:flex;justify-content:flex-end;align-items:center;padding-right:10px;"><span class="rbtn" style="opacity:0;font-size:14px;padding:0 5px;">-</span><span class="rbox" style="background:transparent;color:#fff;width:28px;white-space:nowrap;display:inline-block;text-align:center;font-size:12px;margin:0 2px;">---</span></div></div>`;
          } else {
            exHtml += `<div class="ex-row" style="margin-bottom:8px;"><span class="ex-name" style="flex:2;padding-left:8px;font-size:12px;">${nm}</span><div style="flex:1;display:flex;justify-content:center;align-items:center;"><span class="wbox" style="flex:none;min-width:50px;padding:2px 2px;font-size:12px;">${wd}</span></div><div class="rbtn-wrap" style="transform:translateX(12px);flex:1;display:flex;justify-content:flex-end;align-items:center;padding-right:10px;"><button class="rbtn" style="font-size:14px;padding:0 5px;" onclick="event.stopPropagation();chgRep(${w.id},${ex.id||0},${set.id||si},-1)">-</button><span class="rbox" id="reps-${w.id}-${ex.id||0}-${set.id||si}" style="margin:0 2px;width:28px;display:inline-block;text-align:center;font-size:12px;">${sr}</span></div></div>`;
          }
        });
        const isLast = si === totalSets - 1;
        rows += `<div class="${isLast ? '' : 'set-grp'}" ${isLast ? '' : 'style="border-bottom:1px solid rgba(255,255,255,0.15);margin-bottom:10px;padding-bottom:10px;"'}> <div class="${si === activeSetIdx ? 'active-ex-row' : ''}" style="display:flex;align-items:center;margin-bottom:0;gap:0px;"><span class="set-num">${si+1}</span><div style="flex:1;">${exHtml}</div></div></div>`;
      }
      const ts = TS[w.id] = TS[w.id] || { elapsed: 0, running: false, paused: false, interval: null, allDone: false };
      const isComp = (w.type_completed == 1 || w.status === 'completed' || ts.allDone || (totalSets > 0 && doneCount >= totalSets));
      if(isComp) ts.allDone = true;
      const statsHtml = isComp ? buildSSStatsHtml(w, straights, totalSets) : '';
      return wkCard(w.id, 'Straight Sets', `<span class="wk-pbadge" id="ss-prog-${w.id}">${doneCount}/${totalSets}</span>`, warnHtml + rows, isComp, ts.timeTaken, statsHtml);
    }

    function startSS(w){
      const ts = TS[w.id] = TS[w.id] || { elapsed: 0, running: false, paused: false, interval: null, limit: 0 };
      const straights = w.straights || [];
      if (!straights.length) return;
      const totalSets = straights[0].sets?.length || 0;
      let tIdx = 0;
      for (let i = 0; i < totalSets; i++) {
         const allDone = straights.every(ex => {
           const s = ex.sets && ex.sets[i];
           if (!s) return true;
           return s.is_completed == 1 || !!CT[`${w.id}-${ex.id||0}-${s.id||i}`];
         });
         if (!allDone) { tIdx = i; break; }
      }
      
      const rk = `rest-${w.id}-${tIdx}`;
      if (RST[rk]?.running) return;
      
      countdown10(w.id, () => {
        ts.running = true; ts.paused = false;
        startRestForSet(w, tIdx);
      });
    }

    function toggleSS(wId) {
      // Pause/resume not supported for rest timers natively in mobile straight sets
    }

    function completeSS(w){
      const straights = w.straights || []; if(!straights.length) return;
      const totalSets = straights[0].sets?.length || 0;
      
      let tIdx = 0;
      for(let i = 0; i < totalSets; i++){
        const allDone = straights.every(ex => {
          const s = ex.sets && ex.sets[i];
          if(!s) return true;
          return s.is_completed == 1 || !!CT[`${w.id}-${ex.id||0}-${s.id||i}`];
        });
        if(!allDone){ tIdx = i; break; }
      }
      
      const rk = `rest-${w.id}-${tIdx}`;
      if(RST[rk]?.running){
          doneRest(w.id, tIdx);
          completeSSetToggle(w, tIdx, true); 
      } else {
          showMsg('Notice', "Please click 'START' to begin your set before completing it.");
      }
    }

    async function completeSSetToggle(w, si, checkAll = true){
      const straights = w.straights || [];
      straights.forEach(ex => {
        const s = ex.sets && ex.sets[si];
        if(s) {
          s.is_completed = 1;
          CT[`${w.id}-${ex.id||0}-${s.id||si}`] = true;
          const el = document.getElementById(`reps-${w.id}-${ex.id||0}-${s.id||si}`);
          if (el) {
            const r = parseInt(el.textContent || '0');
            s.daily_reps = isNaN(r) ? (s.reps || 0) : r;
            s.reps_progress = s.daily_reps;
          }
        }
      });
      
      try {
        await saveStraightSetSingle(w, si);
      } catch(e) {
        console.error('Error saving straight set:', e);
      }

      if (checkAll) {
         const totalSets = straights[0]?.sets?.length || 0;
         const allDone = Array.from({length: totalSets}, (_, i) => i).every(i => straights.every(ex => {
           const s = ex.sets && ex.sets[i];
           if(!s) return true;
           return s.is_completed == 1 || !!CT[`${w.id}-${ex.id||0}-${s.id||i}`];
         }));
         if(allDone){
           w.type_completed = 1;
           w.status = 'completed';
           const ts = TS[w.id]; if(ts){ clearInterval(ts.interval); ts.running = false; ts.allDone = true; }
           showBanner(w.id, 'straight-sets', true);
           if(w.id === selId){ wkStatus = 'completed'; updBbar(); }
         }
      }
      renderList();
      updBbar();
    }

    async function saveStraightSetSingle(w, si){
      let ep = '/save-strength-workout';
      if(tab === 'warmup') ep = '/warmup-daily';
      else if(tab === 'weightlifting') ep = '/save-weightlifting-workout';
      else if(tab === 'conditioning') ep = '/save-conditioning-workout';
      else if(tab === 'accessory') ep = '/save-accessory-workout';

      const straights = w.straights || [];
      if(!straights.length) return;
      const payload = [];
      straights.forEach(ex => {
        const s = ex.sets && ex.sets[si];
        if(!s) return;
        const el = document.getElementById(`reps-${w.id}-${ex.id||0}-${s.id||si}`);
        const reps = el ? parseInt(el.textContent||'0') : parseInt(s.daily_reps||s.reps_progress||s.reps||0)||0;
        s.daily_reps = reps;
        s.reps_progress = reps;

        const cumulativeMissedReps = (ex.sets || [])
          .slice(0, si)
          .reduce((acc, prevSet, prevIdx) => {
            const pTarget = prevSet.res ?? prevSet.target_reps ?? prevSet.reps ?? ex.reps ?? 0;
            const pReps = prevSet.daily_reps ?? prevSet.reps_progress ?? pTarget;
            return acc + Math.max(0, pTarget - pReps);
          }, 0);

        const u = s.unittype || s.unit_type || ex.unittype || ex.unit_type || '%';
        const ld = s.training_load ?? ex.training_load ?? ex.weight ?? 0;
        const tw = ex.test_weight ?? w.test_weight ?? 0;
        const calcWeightVal = calcWt(ld, u, tw, cumulativeMissedReps);
        const wgt = (s.daily_weight != null && cumulativeMissedReps === 0) ? s.daily_weight : calcWeightVal;
        s.daily_weight = wgt;

        const wFormatId = (w.format?.slug === 'straight-sets' || !w.format) ? (s.id || ex.id || 0) : (ex.id || 0);
        payload.push({
          workout_manager_id: w.id,
          workout_format_type: w.format?.slug || 'straight-sets',
          workout_format_id: wFormatId,
          reps: reps,
          set_number: si + 1,
          weight: wgt,
          date: typeof workoutDayStr === 'function' ? workoutDayStr() : DAY,
          exercise_time: '00:00',
          class_Id: CLSID,
          notes: notes || null
        });
      });
      if(!payload.length) return;
      const res = await fetch(ep, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
        body: JSON.stringify(payload)
      });
      const data = await res.json().catch(() => null);
      if(!res.ok || (data && data.success === false)){
        console.error('[saveStraightSetSingle] Failed:', data);
      }
    }
</script>

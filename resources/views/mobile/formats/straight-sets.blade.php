<script>
    // ─── STRAIGHT SETS FORMAT ───────────────────────────────────────
    function cardSS(w){
      const straights = w.straights || [];
      const totalSets = straights.length > 0 && straights[0].sets ? straights[0].sets.length : 0;
      let activeSetIdx = -1;
      for(let i = 0; i < totalSets; i++){
        const isDone = straights.every(ex => {
          const s = ex.sets && ex.sets[i];
          if(!s) return true;
          return s.is_completed == 1 || !!CT[`${w.id}-${ex.id||0}-${s.id||i}`];
        });
        if(!isDone){ activeSetIdx = i; break; }
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
          let sr = set.daily_reps ?? set.reps_progress ?? tr;
          if(sr === 0){
            if(tr > 0) sr = tr;
            else if(si > 0){
              const ps = ex.sets[si - 1];
              if(ps){
                const pt = ps.res ?? ps.target_reps ?? ps.reps ?? ex.reps ?? 0;
                sr = ps.daily_reps ?? ps.reps_progress ?? pt;
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

          const dailySavedWeight = (set.daily_weight != null && Number(set.daily_weight) > 0)
            ? set.daily_weight : null;
          const wd = dailySavedWeight !== null
            ? `${Math.round(dailySavedWeight)} KG`
            : dispWt(ld, unit, ex.test_weight ?? w.test_weight ?? 0, cumulativeMissedReps);

          const noReps = unit === 'CAL' || unit === 'M';
          if(noReps){
            exHtml += `<div class="ex-row" style="margin-bottom:12px;"><span class="ex-name" style="flex:2;padding-left:12px;">${nm}</span><div style="flex:1;display:flex;justify-content:center;align-items:center;"><span class="wbox" style="flex:none;min-width:60px;">${wd}</span></div><div class="rbtn-wrap" style="transform:translateX(12px);flex:1;display:flex;justify-content:flex-end;align-items:center;padding-right:10px;"><span class="rbtn" style="opacity:0;font-size:16px;padding:0 6px;">-</span><span class="rbox" style="background:transparent;color:#fff;width:30px;white-space:nowrap;display:inline-block;text-align:center;font-size:16px;margin:0 2px;">---</span></div></div>`;
          } else {
            exHtml += `<div class="ex-row" style="margin-bottom:12px;"><span class="ex-name" style="flex:2;padding-left:12px;">${nm}</span><div style="flex:1;display:flex;justify-content:center;align-items:center;"><span class="wbox" style="flex:none;min-width:60px;padding:2px 2px;">${wd}</span></div><div class="rbtn-wrap" style="transform:translateX(12px);flex:1;display:flex;justify-content:flex-end;align-items:center;padding-right:10px;"><button class="rbtn" style="font-size:16px;padding:0 6px;" onclick="event.stopPropagation();chgRep(${w.id},${ex.id||0},${set.id||si},-1)">-</button><span class="rbox" id="reps-${w.id}-${ex.id||0}-${set.id||si}" style="margin:0 2px;width:30px;display:inline-block;text-align:center;">${sr}</span></div></div>`;
          }
        });
        const isLast = si === totalSets - 1;
        rows += `<div class="${isLast ? '' : 'set-grp'}" ${isLast ? '' : 'style="border-bottom:1px solid rgba(255,255,255,0.15);margin-bottom:16px;padding-bottom:16px;"'}> <div class="${si === activeSetIdx ? 'active-ex-row' : ''}" style="display:flex;align-items:center;margin-bottom:0;gap:0px;"><span class="set-num">${si+1}</span><div style="flex:1;">${exHtml}</div></div></div>`;
      }
      const doneCount = Object.keys(CT).filter(k => k.startsWith(`${w.id}-`) && CT[k]).length;
      const ts = TS[w.id] = TS[w.id] || { elapsed: 0, running: false, paused: false, interval: null, allDone: false };
      const isComp = (w.type_completed == 1 || w.status === 'completed' || ts.allDone || (totalSets > 0 && doneCount >= totalSets));
      if(isComp) ts.allDone = true;
      return wkCard(w.id, 'Straight Sets', `<span class="wk-pbadge" id="ss-prog-${w.id}">${doneCount}/${totalSets}</span>`, warnHtml + rows, isComp, ts.timeTaken);
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

    function completeSSetToggle(w, si, checkAll = true){
      const straights = w.straights || [];
      straights.forEach(ex => {
        const s = ex.sets && ex.sets[si];
        if(s) CT[`${w.id}-${ex.id||0}-${s.id||si}`] = true;
      });
      if (checkAll) {
         const totalSets = straights[0]?.sets?.length || 0;
         const allDone = Array.from({length: totalSets}, (_, i) => i).every(i => straights.every(ex => {
           const s = ex.sets && ex.sets[i];
           if(!s) return true;
           return s.is_completed == 1 || !!CT[`${w.id}-${ex.id||0}-${s.id||i}`];
         }));
         if(allDone){
           showBanner(w.id, 'straight-sets', true);
           if(w.id === selId){ wkStatus = 'completed'; updBbar(); }
           const ts = TS[w.id]; if(ts){ clearInterval(ts.interval); ts.running = false; }
         }
      }
      renderList();
      updBbar();
    }
</script>

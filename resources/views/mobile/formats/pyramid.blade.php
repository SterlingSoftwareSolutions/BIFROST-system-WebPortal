<script>
    // ─── PYRAMID FORMAT ─────────────────────────────────────────────
    window.pyrWarningVisible = window.pyrWarningVisible || {};

    function cardPyramid(w){
      const rows = w.pyramids || w.pyramid || [];
      const doneC = rows.filter(r => r.is_completed == 1 || !!CT[`${w.id}-${r.id}`] || w.type_completed == 1).length;
      let allD = (w.type_completed == 1 || w.status === 'completed' || (rows.length > 0 && doneC >= rows.length));
      const ts = TS[w.id] = TS[w.id] || { elapsed: 0, running: false, paused: false, interval: null, allDone: allD };
      if(allD || ts.allDone) ts.allDone = true;
      const isComp = (w.type_completed == 1 || w.status === 'completed' || ts.allDone || (rows.length > 0 && doneC >= rows.length));

      // 5-second auto-hide 1RM warning
      const hasMissing1RM = rows.some(row => {
        const load = Number(row.training_load ?? row.weight ?? w.training_load ?? 0);
        const testWeight = row.test_weight ?? w.test_weight ?? 0;
        const u = String(row.unit_type ?? w.unit_type ?? '%').trim();
        return u === '%' && load > 0 && Number(testWeight) === 0;
      });
      if (hasMissing1RM && pyrWarningVisible[w.id] === undefined) {
        pyrWarningVisible[w.id] = true;
        setTimeout(() => {
          pyrWarningVisible[w.id] = false;
          const el = document.getElementById(`pyr-warn-${w.id}`);
          if (el) el.style.display = 'none';
        }, 5000);
      }
      const warnHtml = (hasMissing1RM && pyrWarningVisible[w.id] !== false)
        ? `<div id="pyr-warn-${w.id}" style="color:#fca5a5;font-size:13px;margin-bottom:12px;padding:0 4px;">⚠️ Please add your 1RM for accurate weight calculation.</div>`
        : '';

      const currentActiveRowIndex = rows.findIndex(row => {
        const tk = `${w.id}-${row.id}`;
        const isApiCompleted = w.type_completed == 1 || row.is_completed == 1;
        const isSetCompleted = isApiCompleted || !!CT[tk];
        return !isSetCompleted;
      });

      const rHtml = rows.map((row, index) => {
        const tk = `${w.id}-${row.id}`;
        const isApiCompleted = w.type_completed == 1 || row.is_completed == 1;
        const isSetCompleted = isApiCompleted || !!CT[tk];
        const rk = `pyr-${w.id}-${row.id}`;
        const rst = RST[rk];
        const isCurrentTarget = (index === currentActiveRowIndex);
        const isActive = ((rst && rst.running) || isCurrentTarget) && !isSetCompleted;

        const library = row.workout_library;
        const workoutName = exName(row) || (library && library.workout) || 'Exercise';
        const unitType = (row.unittype || row.unit_type || w.unit_type || '%').trim().toUpperCase();
        const load = Number(row.training_load ?? row.weight ?? w.training_load ?? 0);
        const weightDisp = dispWt(load, unitType, row.test_weight ?? w.test_weight ?? 0);
        const repsVal = row.reps_progress ?? row.reps ?? 0;

        return `<div class="pyr-row-wrap" id="pyr-row-${w.id}-${row.id}" onclick="event.stopPropagation();pyrClick(${w.id},${row.id})"
          style="width:100%;padding:4px 0;margin-bottom:2px;cursor:pointer;${isActive ? 'background:rgba(255,255,255,0.15);border-radius:8px;' : ''}">
          <div style="display:flex;flex-direction:row;align-items:center;justify-content:space-between;width:100%;min-height:30px;">
            <div style="flex:2;text-align:left;color:#fff;font-size:14px;font-weight:600;padding-left:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
              ${workoutName}
            </div>
            <div style="flex:1;text-align:right;color:#fff;font-size:13px;font-weight:600;padding-right:0;font-variant-numeric:tabular-nums;">
              ${weightDisp}
            </div>
            <div style="flex:1;display:flex;justify-content:flex-end;align-items:center;padding-right:10px;transform:translateX(12px);">
              ${Number(repsVal) === 0 ? `
                <span style="background:transparent;color:#fff;font-size:16px;font-weight:600;min-width:28px;text-align:center;padding:0 4px;display:inline-block;">---</span>
              ` : `
                <span style="background:#fff;color:#000;padding:2px 4px;border-radius:4px;font-size:14px;font-weight:bold;min-width:28px;text-align:center;display:inline-block;line-height:1.2;">${repsVal}</span>
              `}
            </div>
            <div style="width:35px;height:30px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              ${isSetCompleted ? `
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              ` : ''}
            </div>
          </div>
        </div>`;
      }).join('');

      const bodyHtml = `${warnHtml}${rHtml}`;
      const headerExtra = `<span class="wk-pbadge" id="pyr-prog-${w.id}">${doneC}/${rows.length}</span>`;
      return wkCard(w.id, 'Pyramid', headerExtra, bodyHtml, isComp, ts.timeTaken);
    }

    function pyrClick(wId, rowId){
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      if(!w) return;
      const rows = w.pyramids || w.pyramid || [];
      const row = rows.find(r => r.id === rowId);
      if(!row) return;
      const tk = `${wId}-${rowId}`;
      if(CT[tk] || row.is_completed == 1) return;
      const rk = `pyr-${wId}-${rowId}`;
      if(RST[rk]?.running){
        finishPyrRest(wId, rk, true);
        return;
      }
      const otherRk = Object.keys(RST).find(k => k.startsWith(`pyr-${wId}-`) && RST[k]?.running);
      if(otherRk){
        finishPyrRest(wId, otherRk, true);
      }
      startPyrRest(wId, rowId);
    }

    function checkAllPyrDone(w){
      const rows = w.pyramids || w.pyramid || [];
      const doneC = rows.filter(r => !!CT[`${w.id}-${r.id}`] || r.is_completed == 1).length;
      const allDone = rows.length > 0 && doneC >= rows.length;
      if(allDone){
        showBanner(w.id, 'pyramid', true);
        if(w.id === selId){
          wkStatus = 'completed';
        }
      }
    }

    async function savePyramidRow(w, row, rowIndex, rowsCount){
      let ep = '/save-strength-workout';
      if(tab === 'warmup') ep = '/warmup-daily';
      else if(tab === 'weightlifting') ep = '/save-weightlifting-workout';
      else if(tab === 'conditioning') ep = '/save-conditioning-workout';
      else if(tab === 'accessory') ep = '/save-accessory-workout';

      const u = (row.unittype || row.unit_type || w.unit_type || '%').trim().toUpperCase();
      const ld = row.training_load ?? row.weight ?? w.training_load ?? 0;
      const wgt = calcWt(ld, u, row.test_weight ?? w.test_weight ?? 0);
      const reps = parseInt(row.reps_progress ?? row.reps ?? 0) || 0;
      const setNumber = (rowIndex !== undefined && rowIndex >= 0) ? (rowIndex + 1) : 1;
      const roundNumber = `${setNumber}/${rowsCount || 1}`;

      const payload = [{
        workout_manager_id: w.id,
        workout_format_type: 'pyramid',
        workout_format_id: row.id,
        reps: reps,
        weight: wgt,
        date: dayStr(),
        set_number: setNumber,
        round_number: roundNumber,
        class_Id: CLSID,
        notes: notes || null
      }];

      try{
        console.log('Saving PYRAMID row payload:', payload);
        await fetch(ep, {
          method: 'POST',
          headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
          body: JSON.stringify(payload)
        });
      }catch(err){
        console.error('Failed to save pyramid row:', err);
      }
    }
</script>

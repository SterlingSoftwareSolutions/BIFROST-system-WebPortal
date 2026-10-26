<script>
    // ── COMMON FORMAT CARD HELPERS & ROW BUILDERS ──────────────────
    function bannerHtml(fmtLabel) {
      const lbl = fmtLabel ? String(fmtLabel).replace(/-/g, ' ').toLowerCase() : 'workout';
      return `<div class="wk-banner" style="position:absolute; inset:0; z-index:10; display:flex; flex-direction:column; align-items:center; justify-content:center; pointer-events:none; background:rgba(0,0,0,0.6); border:1.5px solid rgba(242,153,74,0.3); border-radius:0; overflow:hidden;">
        <div style="width:50px; height:50px; border-radius:50%; background:#F2994A; display:flex; align-items:center; justify-content:center; margin-bottom:8px; box-shadow:0 4px 12px rgba(242,153,74,0.4);">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
        <div style="color:#ffffff; font-weight:800; font-size:18px; letter-spacing:0.8px; text-shadow:0 2px 6px rgba(0,0,0,0.8); text-transform:lowercase; font-family:inherit; line-height:1.2; text-align:center;">
          ${lbl} completed
        </div>
      </div>`;
    }

    function wkCard(wId, fmtLabel, headerExtra, bodyHtml, isCompleted = false, timeTaken = null, statsHtml = ''){
      const sel = selId === wId ? 'selected' : '';
      const bHtml = isCompleted ? bannerHtml(fmtLabel) : '';
      const hasRepsRightSpacer = (fmtLabel === 'Pyramid' || fmtLabel === 'Intervals');
      const rPad = hasRepsRightSpacer ? '8px' : '10px';
      const showStats = (selId === wId);
      return `<div class="wk-card ${sel}" id="wk-${wId}" style="position:relative;">
        <div class="wk-hdr"><div style="display:flex;align-items:center;gap:6px;"><span class="wk-fmt">${fmtLabel}</span>${headerExtra}</div><div style="flex:1;display:flex;justify-content:flex-end;align-items:center;padding-right:${rPad};transform:translateX(12px);"><span class="wk-rhdr" style="margin:0 2px;width:28px;min-width:28px;text-align:center;display:inline-block;">Reps</span></div>${hasRepsRightSpacer ? '<div style="width:30px;flex-shrink:0;"></div>' : ''}</div>
        ${bodyHtml}
        <div id="banner-${wId}">${bHtml}</div>
      </div>
      <div id="stats-${wId}" style="${showStats ? '' : 'display:none;'}">${statsHtml || ''}</div>`;
    }

    function exRow(wId, exId, setId, name, wDisp, reps, done, hasMinus=true){
      const repsDisp = (!reps || reps === 0 || reps === '0') ? '---' : reps;
      return `<div class="ex-row" style="margin-bottom:8px;"><span class="ex-name" style="flex:2;padding-left:8px;font-size:12px;">${name}</span><div style="flex:1;display:flex;justify-content:center;align-items:center;"><span class="wbox" style="flex:none;min-width:50px;font-size:12px;">${wDisp}</span></div>
        <div class="rbtn-wrap" style="transform:translateX(12px);">
          ${hasMinus?`<button class="rbtn" onclick="event.stopPropagation();chgRep(${wId},${exId},${setId},-1)">-</button>`:''}
          <span class="rbox" id="reps-${wId}-${exId}-${setId}">${repsDisp}</span>
          <button class="rbtn" onclick="event.stopPropagation();chgRep(${wId},${exId},${setId},1)" style="${hasMinus?'':'display:none;'}">+</button>
        </div></div>`;
    }

    function plainExRow(name, wDisp, reps, isCompleted = false){
      const repsDisplay = (!reps || reps === 0 || reps === '0')
        ? '<span style="background:transparent; color:#fff; font-size:12px; font-weight:600; min-width:26px; width:32px; text-align:center; display:inline-block;">---</span>'
        : `<span class="rbox" style="background:#fff; color:#000; padding:2px 6px; border-radius:4px; font-size:12px; font-weight:700; display:inline-block; text-align:center; min-width:26px;">${reps}</span>`;
      return `<div class="ex-row" style="margin-bottom:8px;">
        <span class="ex-name" style="flex:2; padding-left:8px; font-size:12px;">${name}</span>
        <div style="flex:1; display:flex; justify-content:flex-end; align-items:center; padding-right:10px;">
          <span style="color:#fff; font-size:12px; font-weight:600;">${wDisp}</span>
        </div>
        <div class="rbtn-wrap" style="transform:translateX(12px); flex:0.6; min-width:60px; display:flex; justify-content:flex-end; align-items:center; padding-right:10px;">
           ${repsDisplay}
        </div>
      </div>`;
    }

    function intervalExRow(name, wDisp, reps, isCompleted = false, isActive = false){
      const repsDisplay = (!reps || reps === 0 || reps === '0')
        ? '<span style="background:transparent; color:#fff; font-size:12px; font-weight:600; min-width:26px; width:32px; text-align:center; padding:0 2px; display:inline-block;">---</span>'
        : `<span class="rbox" style="background:#fff; color:#000; padding:2px 6px; border-radius:4px; font-size:12px; font-weight:700; display:inline-block; text-align:center; min-width:26px;">${reps}</span>`;

      const checkmarkDisplay = isCompleted ? `
        <div style="width:30px; height:26px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>` : `<div style="width:30px; height:26px; flex-shrink:0;"></div>`;

      const nameWeight = (isActive && !isCompleted) ? '700' : '500';

      return `<div class="ex-row" style="margin-bottom:4px; padding:3px 0; display:flex; align-items:center; min-height:26px;">
        <span class="ex-name" style="flex:2; padding-left:6px; color:#fff; font-size:12px; font-weight:${nameWeight}; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${name}</span>
        <div style="flex:1; display:flex; justify-content:flex-end; align-items:center; padding-right:10px;">
          <span style="color:#fff; font-size:12px; font-weight:600; white-space:nowrap;">${wDisp}</span>
        </div>
        <div style="flex:1; display:flex; justify-content:flex-end; align-items:center; padding-right:8px; transform:translateX(12px);">
          ${repsDisplay}
        </div>
        ${checkmarkDisplay}
      </div>`;
    }

    function emomExRow(name, wDisp, reps){
      const repsDisplay = (!reps || reps === 0 || reps === '0')
        ? '<span style="background:transparent; color:#fff; font-size:12px; font-weight:600; width:36px; text-align:center; display:inline-block;">---</span>'
        : `<span class="rbox" style="background:#fff; color:#000; border-radius:4px; font-size:12px; font-weight:700; width:36px; height:22px; display:inline-flex; align-items:center; justify-content:center; text-align:center; box-sizing:border-box;">${reps}</span>`;

      return `<div class="ex-row" style="margin-bottom:8px; padding:3px 0; display:flex; align-items:center;">
        <span class="ex-name" style="flex:2; padding-left:8px; color:#fff; font-size:12px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${name}</span>
        <div style="flex:1; display:flex; justify-content:flex-end; align-items:center; padding-right:12px;">
          <span style="color:#fff; font-size:12px; font-weight:600; white-space:nowrap;">${wDisp}</span>
        </div>
        <div class="rbtn-wrap" style="transform:translateX(12px); flex:0.6; min-width:60px; display:flex; justify-content:flex-end; align-items:center; padding-right:10px;">
          ${repsDisplay}
        </div>
      </div>`;
    }

    function amrapExRow(name, wDisp, reps, isDone=false, isIncomp=false){
      let repsDisplay;
      if (isIncomp) {
        repsDisplay = '<span style="color:#fca5a5; font-size:10px; font-weight:bold; text-align:center; margin:0 2px; white-space:nowrap;">INCOMPLETE</span>';
      } else if (!reps || reps === 0 || reps === '0') {
        repsDisplay = '<span style="background:transparent; color:#fff; font-size:12px; font-weight:600; width:36px; text-align:center; display:inline-block; padding-right:2px;">---</span>';
      } else {
        repsDisplay = `<span class="rbox" style="background:#fff; color:#000; border-radius:4px; font-size:12px; font-weight:700; width:36px; height:22px; display:inline-flex; align-items:center; justify-content:center; text-align:center; box-sizing:border-box;">${reps}</span>`;
      }
      return `<div class="ex-row" style="margin-bottom:8px; padding:3px 0; display:flex; align-items:center; justify-content:space-between;">
        <span class="ex-name" style="flex:2; padding-left:8px; color:#fff; font-size:12px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${name}</span>
        <div style="flex:1; display:flex; justify-content:flex-end; align-items:center; padding-right:12px;">
          <span style="color:#fff; font-size:12px; font-weight:600; white-space:nowrap;">${wDisp}</span>
        </div>
        <div class="rbtn-wrap" style="transform:translateX(12px); flex:0.6; min-width:60px; display:flex; justify-content:flex-end; align-items:center; padding-right:10px;">
          ${repsDisplay}
        </div>
      </div>`;
    }


    function chgRep(wId, exId, setId, d){
      const rootData = (typeof window !== 'undefined' && window.wData) ? window.wData : (typeof wData !== 'undefined' ? wData : null);
      let updated = false;
      if(rootData){
        const categories = ['warmup', 'strength', 'weightlifting', 'conditioning', 'accessory'];
        for(const cat of categories){
          const wList = rootData[cat] || [];
          const w = wList.find(item => item && item.id == wId);
          if(w){
            // 1. Straight Sets
            const straights = w.straights || w.straight || [];
            if(straights.length > 0){
              for(const ex of straights){
                if(exId && ex.id != exId) continue;
                const sets = ex.sets || [];
                for(let sIdx = 0; sIdx < sets.length; sIdx++){
                  const s = sets[sIdx];
                  if(s.id == setId || sIdx == setId){
                    const tr = s.res ?? s.target_reps ?? s.reps ?? ex.reps ?? 0;
                    let current = (s.daily_reps !== undefined && s.daily_reps !== null) 
                      ? s.daily_reps 
                      : ((s.reps_progress !== undefined && s.reps_progress !== null) ? s.reps_progress : tr);
                    if(Number(current) === 0 && !s._user_edited){
                      if(tr > 0) current = tr;
                      else if(sIdx > 0){
                        const ps = sets[sIdx - 1];
                        if(ps){
                          const pt = ps.res ?? ps.target_reps ?? ps.reps ?? ex.reps ?? 0;
                          current = (ps.daily_reps !== undefined && ps.daily_reps !== null)
                            ? ps.daily_reps
                            : ((ps.reps_progress !== undefined && ps.reps_progress !== null) ? ps.reps_progress : pt);
                        }
                      }
                    }
                    const next = Math.max(0, Number(current) + d);
                    s.daily_reps = next;
                    s.reps_progress = next;
                    s._user_edited = true;
                    updated = true;
                    break;
                  }
                }
                if(updated) break;
              }
            }

            // 2. Pyramid, Rounds, etc.
            if(!updated){
              const formats = [w.pyramids, w.rounds, w.amraps, w.emoms, w.for_times, w.intervals, w.circuits];
              for(const fmtList of formats){
                if(Array.isArray(fmtList)){
                  const row = fmtList.find(r => r.id == exId || r.id == setId);
                  if(row){
                    const tr = row.res ?? row.target_reps ?? row.reps ?? 0;
                    let current = (row.daily_reps !== undefined && row.daily_reps !== null)
                      ? row.daily_reps
                      : ((row.reps_progress !== undefined && row.reps_progress !== null) ? row.reps_progress : tr);
                    if(Number(current) === 0 && !row._user_edited && tr > 0) current = tr;
                    const next = Math.max(0, Number(current) + d);
                    row.daily_reps = next;
                    row.reps_progress = next;
                    row._user_edited = true;
                    updated = true;
                    break;
                  }
                }
              }
            }
          }
          if(updated) break;
        }
      }

      if(updated && typeof renderList === 'function'){
        renderList();
      } else {
        const el = document.getElementById(`reps-${wId}-${exId}-${setId}`);
        if(el){
          let v = parseInt(el.textContent || '0') + d;
          if(v < 0) v = 0;
          el.textContent = v;
        }
      }
    }

    function hasRoundsTimer(w, ts) {
      if (!w) return false;
      const isForTime =
        Number(w.is_for_time ?? 0) === 1 ||
        Number(w.is_fortime ?? 0) === 1 ||
        Number(w.for_time ?? 0) === 1 ||
        (w.rounds ?? []).some(r =>
          Number(r.is_for_time ?? 0) === 1 ||
          Number(r.is_fortime ?? 0) === 1 ||
          Number(r.for_time ?? 0) === 1
        );
      const rawWork = w.time_to_complete || (w.rounds && w.rounds[0] && w.rounds[0].time_to_complete);
      const workTime = (typeof parseSec === 'function' ? parseSec(rawWork) : 0) || 0;
      const hasTimeCap = Number(w.time_cap ?? 0) === 1;
      const isRestActive = ts && !!ts.restActive;

      return isRestActive || isForTime || workTime > 0 || hasTimeCap;
    }

    function hasCircuitTimer(w, ts) {
      if (!w) return false;
      const isForTime =
        Number(w.is_for_time ?? 0) === 1 ||
        Number(w.is_fortime ?? 0) === 1 ||
        Number(w.for_time ?? 0) === 1 ||
        (w.circuits ?? []).some(ex =>
          Number(ex.is_for_time ?? 0) === 1 ||
          Number(ex.is_fortime ?? 0) === 1 ||
          Number(ex.for_time ?? 0) === 1
        );
      const rawWork = w.time_to_complete || (w.circuits && w.circuits[0] && w.circuits[0].time_to_complete);
      const workTime = (typeof parseSec === 'function' ? parseSec(rawWork) : 0) || 0;
      const hasTimeCap = Number(w.time_cap ?? 0) === 1;

      return isForTime || workTime > 0 || hasTimeCap;
    }
</script>

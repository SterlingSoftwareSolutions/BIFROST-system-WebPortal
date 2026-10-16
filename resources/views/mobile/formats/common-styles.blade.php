<script>
    // ── COMMON FORMAT CARD HELPERS & ROW BUILDERS ──────────────────
    function bannerHtml(fmtLabel) {
      const lbl = fmtLabel ? String(fmtLabel).replace(/-/g, ' ').toLowerCase() : 'workout';
      return `<div class="wk-banner" style="position:absolute; inset:0; z-index:50; display:flex; flex-direction:column; align-items:center; justify-content:center; pointer-events:none; background:rgba(0,0,0,0.6); border:1.5px solid rgba(242,153,74,0.3); border-radius:0; overflow:hidden;">
        <div style="width:60px; height:60px; border-radius:50%; background:#F2994A; display:flex; align-items:center; justify-content:center; margin-bottom:10px; box-shadow:0 4px 12px rgba(242,153,74,0.4);">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
        <div style="color:#ffffff; font-weight:800; font-size:22px; letter-spacing:0.8px; text-shadow:0 2px 6px rgba(0,0,0,0.8); text-transform:lowercase; font-family:inherit; line-height:1.2; text-align:center;">
          ${lbl} completed
        </div>
      </div>`;
    }

    function wkCard(wId, fmtLabel, headerExtra, bodyHtml, isCompleted = false, timeTaken = null){
      const sel = selId === wId ? 'selected' : '';
      const bHtml = isCompleted ? bannerHtml(fmtLabel) : '';
      const hasRepsRightSpacer = (fmtLabel === 'Pyramid' || fmtLabel === 'Straight sets' || fmtLabel === 'Straight-Sets' || fmtLabel === 'Straight Sets');
      return `<div class="wk-card ${sel}" id="wk-${wId}" style="position:relative;">
        <div class="wk-hdr"><div style="display:flex;align-items:center;gap:8px;"><span class="wk-fmt">${fmtLabel}</span>${headerExtra}</div><div style="flex:1;display:flex;justify-content:flex-end;padding-right:10px;transform:translateX(12px);"><span class="wk-rhdr" style="min-width:28px;text-align:center;padding:0 4px;">Reps</span></div>${hasRepsRightSpacer ? '<div style="width:35px;flex-shrink:0;"></div>' : ''}</div>
        ${bodyHtml}
        <div id="banner-${wId}">${bHtml}</div>
      </div>`;
    }

    function exRow(wId, exId, setId, name, wDisp, reps, done, hasMinus=true){
      const repsDisp = (!reps || reps === 0 || reps === '0') ? '---' : reps;
      return `<div class="ex-row" style="margin-bottom:12px;"><span class="ex-name" style="flex:2;padding-left:12px;">${name}</span><div style="flex:1;display:flex;justify-content:center;align-items:center;"><span class="wbox" style="flex:none;min-width:60px;">${wDisp}</span></div>
        <div class="rbtn-wrap" style="transform:translateX(12px);">
          ${hasMinus?`<button class="rbtn" onclick="event.stopPropagation();chgRep(${wId},${exId},${setId},-1)">-</button>`:''}
          <span class="rbox" id="reps-${wId}-${exId}-${setId}">${repsDisp}</span>
          <button class="rbtn" onclick="event.stopPropagation();chgRep(${wId},${exId},${setId},1)" style="${hasMinus?'':'display:none;'}">+</button>
        </div></div>`;
    }

    function plainExRow(name, wDisp, reps){
      const repsDisplay = (!reps || reps === 0 || reps === '0')
        ? '<span style="background:transparent; color:#fff; font-size:16px; font-weight:600; min-width:28px; text-align:center; display:inline-block;">---</span>'
        : `<span class="rbox" style="background:#fff; color:#000; padding:2px 8px; border-radius:4px; font-size:14px; font-weight:700; display:inline-block; text-align:center; min-width:28px;">${reps}</span>`;
      return `<div class="ex-row" style="margin-bottom:12px;">
        <span class="ex-name" style="flex:2; padding-left:12px;">${name}</span>
        <div style="flex:1; display:flex; justify-content:flex-end; align-items:center; padding-right:12px;">
          <span style="color:#fff; font-size:14px; font-weight:600;">${wDisp}</span>
        </div>
        <div class="rbtn-wrap" style="transform:translateX(12px); flex:0.6; display:flex; justify-content:flex-end; align-items:center; padding-right:12px;">
           ${repsDisplay}
        </div>
      </div>`;
    }

    function emomExRow(name, wDisp, reps){
      const repsDisplay = (!reps || reps === 0 || reps === '0')
        ? '<span style="background:transparent; color:#fff; font-size:16px; font-weight:600; width:45px; text-align:center; display:inline-block;">---</span>'
        : `<span class="rbox" style="background:#fff; color:#000; border-radius:4px; font-size:14px; font-weight:700; width:45px; height:24px; display:inline-flex; align-items:center; justify-content:center; text-align:center; box-sizing:border-box;">${reps}</span>`;

      return `<div class="ex-row" style="margin-bottom:12px; padding:4px 0; display:flex; align-items:center;">
        <span class="ex-name" style="flex:2; padding-left:12px; color:#fff; font-size:14px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${name}</span>
        <div style="flex:1; display:flex; justify-content:flex-end; align-items:center; padding-right:16px;">
          <span style="color:#fff; font-size:14px; font-weight:600; white-space:nowrap;">${wDisp}</span>
        </div>
        <div class="rbtn-wrap" style="transform:translateX(12px); flex:0.6; display:flex; justify-content:flex-end; align-items:center; padding-right:12px;">
          ${repsDisplay}
        </div>
      </div>`;
    }

    function amrapExRow(name, wDisp, reps, isDone=false, isIncomp=false){
      let repsDisplay;
      if (isDone) {
        repsDisplay = '<span style="color:#fff; font-size:14px; font-weight:400; text-align:center;">COMPLETED</span>';
      } else if (isIncomp) {
        repsDisplay = '<span style="color:#fca5a5; font-size:14px; font-weight:bold; text-align:center;">INCOMPLETE</span>';
      } else if (!reps || reps === 0 || reps === '0') {
        repsDisplay = '<span style="background:transparent; color:#fff; font-size:16px; font-weight:600; width:45px; text-align:center; display:inline-block; padding-right:4px;">---</span>';
      } else {
        repsDisplay = `<span class="rbox" style="background:#fff; color:#000; border-radius:4px; font-size:14px; font-weight:700; width:45px; height:24px; display:inline-flex; align-items:center; justify-content:center; text-align:center; box-sizing:border-box;">${reps}</span>`;
      }

      return `<div class="ex-row" style="margin-bottom:12px; padding:4px 0; display:flex; align-items:center; justify-content:space-between;">
        <span class="ex-name" style="flex:2; padding-left:12px; color:#fff; font-size:14px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${name}</span>
        <div style="flex:1; display:flex; justify-content:flex-end; align-items:center; padding-right:16px;">
          <span style="color:#fff; font-size:14px; font-weight:600; white-space:nowrap;">${wDisp}</span>
        </div>
        <div class="rbtn-wrap" style="transform:translateX(12px); flex:0.6; display:flex; justify-content:flex-end; align-items:center; padding-right:12px;">
          ${repsDisplay}
        </div>
      </div>`;
    }

    function chgRep(wId, exId, setId, d){
      const el = document.getElementById(`reps-${wId}-${exId}-${setId}`);
      if(!el) return;
      let v = parseInt(el.textContent || '0') + d;
      if(v < 0) v = 0;
      el.textContent = v;
    }
</script>

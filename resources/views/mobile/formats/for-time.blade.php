<script>
    // ─── FOR TIME FORMAT ────────────────────────────────────────────
    function cardFT(w){
      const exs = w.for_times || w.forTimes || [];
      const cap = parseSec(w.time_to_complete || (exs[0] && exs[0].time_to_complete));
      let allD = (w.type_completed == 1 || w.status === 'completed' || (exs.length > 0 && exs.every(ex => ex.is_completed == 1)));
      const ts = TS[w.id] = TS[w.id] || { elapsed: 0, running: false, paused: false, interval: null, limit: cap, allDone: allD };
      if(allD || ts.allDone) ts.allDone = true;
      const isComp = (w.type_completed == 1 || w.status === 'completed' || ts.allDone);
      const td = cap > 0 ? fmt(Math.max(0, cap - ts.elapsed)) : fmt(ts.elapsed);
      const exHtml = exs.map((ex, i) => {
        const tr = ex.reps_progress || ex.res || ex.reps || 0;
        const repsDisp = isComp
          ? '<span style="color:#ffffff; font-size:11px; font-weight:400; text-align:center; margin:0 4px; white-space:nowrap; letter-spacing:0.5px;">COMPLETED</span>'
          : `<span class="rbox" id="ft-reps-${w.id}-${ex.id}">${(tr === 0 || tr === '0') ? '---' : tr}</span>`;
        return `<div class="ex-row"><span class="ex-name">${exName(ex)}</span><span class="wbox">${dispWt(ex.training_load ?? 0, ex.unit_type || '%', ex.test_weight ?? 0)}</span><div class="rbtn-wrap" style="min-width:70px;justify-content:flex-end;">${!isComp ? `<button class="rbtn" onclick="event.stopPropagation();chgFTRep(${w.id},${ex.id},-1)">-</button>` : ''}${repsDisp}${!isComp ? `<button class="rbtn" onclick="event.stopPropagation();chgFTRep(${w.id},${ex.id},1)">+</button>` : ''}</div></div>`;
      }).join('');
      const timerBlock = `<div id="ft-tmr-${w.id}" class="card-timer" style="${selId === w.id ? '' : 'display:none;'}"><div id="ft-t-${w.id}" class="ct-time">${td}</div><div class="ct-label">${cap > 0 ? 'TIME REMAINING' : 'ELAPSED'}</div><div class="ct-btns"><button class="ctbtn danger" onclick="event.stopPropagation();stopFT(${w.id})">STOP</button><button class="ctbtn primary" id="ft-pb-${w.id}" onclick="event.stopPropagation();toggleFT(${w.id})">${ts.running ? 'PAUSE' : 'START'}</button></div></div>`;
      return wkCard(w.id, 'For Time', cap > 0 ? `<span style="color:rgba(255,255,255,.5);font-size:11px;">Cap: ${fmt(cap)}</span>` : '', timerBlock + exHtml, isComp, ts.timeTaken);
    }
</script>

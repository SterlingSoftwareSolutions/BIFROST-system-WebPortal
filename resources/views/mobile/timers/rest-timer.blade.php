<script>
    // ── REST TIMER ENGINE (Pyramid & Straight-Sets Rest Phases) ─────
    
    // Pyramid Rest Timer
    function startPyrRest(wId, rowId){
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      if(!w) return;
      const rows = w.pyramids || w.pyramid || [];
      const row = rows.find(r => r.id === rowId);
      if(!row) return;
      const tk = `${wId}-${rowId}`;
      if(CT[tk] || row.is_completed == 1) return;

      const DEFAULT_RED = '00:00:15';
      const DEFAULT_YELLOW = '00:00:15';
      const DEFAULT_GREEN = '00:00:15';
      const rr = parseSec(row.restred || w.restred || DEFAULT_RED);
      const ry = parseSec(row.restyellow || w.restyellow || DEFAULT_YELLOW);
      const rg = parseSec(row.restgreen || w.restgreen || DEFAULT_GREEN);
      const phases = [
        { color: '#ef4444', time: rr },
        { color: '#f97316', time: ry },
        { color: '#22c55e', time: rg }
      ].filter(p => p.time > 0);

      const rk = `pyr-${wId}-${rowId}`;
      if(RST[rk]?.interval) clearInterval(RST[rk].interval);

      if(!phases.length){
        CT[tk] = true;
        const rowIndex = rows.findIndex(r => r.id === rowId);
        savePyramidRow(w, row, rowIndex, rows.length);
        checkAllPyrDone(w);
        renderList();
        updBbar();
        return;
      }

      const rst = RST[rk] = {
        running: true,
        phase: 0,
        phaseLeft: phases[0].time,
        color: phases[0].color,
        interval: null,
        rowId: rowId,
        wId: wId,
        phases: phases
      };

      renderList();
      updBbar();

      rst.interval = setInterval(() => {
        rst.phaseLeft--;
        if(rst.phaseLeft === 3 || rst.phaseLeft === 2 || rst.phaseLeft === 1){
          playTimerBeep(600, 0.12);
        }
        if(rst.phaseLeft <= 0){
          rst.phase++;
          if(rst.phase < phases.length){
            rst.phaseLeft = phases[rst.phase].time;
            rst.color = phases[rst.phase].color;
            updBbar();
          } else {
            finishPyrRest(wId, rk, false);
          }
        } else {
          updCenterBtn(w, 'pyramid', TS[wId] || {});
        }
      }, 1000);
    }

    function finishPyrRest(wId, rk, manual = false){
      const rst = RST[rk];
      if(!rst) return;
      if(rst.interval) clearInterval(rst.interval);
      delete RST[rk];

      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      if(!w) return;
      const rows = w.pyramids || w.pyramid || [];
      const rowId = rst.rowId;
      const row = rows.find(r => r.id === rowId);
      const tk = `${wId}-${rowId}`;
      CT[tk] = true;

      if(row){
        const rowIndex = rows.findIndex(r => r.id === rowId);
        savePyramidRow(w, row, rowIndex, rows.length);
      }

      checkAllPyrDone(w);
      renderList();
      updBbar();
    }

    // Straight Sets Rest Timer
    function startRestForSet(w, si){
      const straights = w.straights || [];
      if(!straights.length) return;
      const fe = straights[0], s = fe.sets && fe.sets[si] || {};
      const rr = parseSec(s.restred || fe.restred || w.restred || '00:01:30');
      const ry = parseSec(s.restyellow || fe.restyellow || w.restyellow || '00:01:00');
      const rg = parseSec(s.restgreen || fe.restgreen || w.restgreen || '00:00:30');
      const phases = [{color:'#ef4444',time:rr},{color:'#f97316',time:ry},{color:'#22c55e',time:rg}].filter(p=>p.time>0);
      if(!phases.length){ return; }
      const rk = `rest-${w.id}-${si}`;
      if(RST[rk]?.interval) clearInterval(RST[rk].interval);
      const rst = RST[rk] = {running:true, paused:false, phase:0, phaseLeft:phases[0].time, color:phases[0].color, interval:null};
      renderList();
      rst.interval = setInterval(()=>{
        rst.phaseLeft--;
        if(rst.phaseLeft === 3 || rst.phaseLeft === 2 || rst.phaseLeft === 1){
          playTimerBeep(600, 0.12);
        }
        updBbar();
        updCenterBtn(w, 'straight-sets', {});
        if(rst.phaseLeft <= 0){
          rst.phase++;
          if(rst.phase < phases.length){
            rst.phaseLeft = phases[rst.phase].time;
            rst.color = phases[rst.phase].color;
          } else {
            clearInterval(rst.interval);
            rst.running = false;
            delete RST[rk];
            if(TS[w.id]) TS[w.id].running = false;
            updBbar();
            updCenterBtn(w, 'straight-sets', {});
            
            showBanner(w.id, 'straight-sets', false);
            setTimeout(() => {
                const el = document.getElementById('banner-' + w.id);
                if (el) el.innerHTML = '';
                renderList();
                updBbar();
            }, 2500);
          }
        }
      }, 1000);
    }

    function doneRest(wId, si){
      const rk = `rest-${wId}-${si}`;
      const rst = RST[rk];
      if(rst){ clearInterval(rst.interval); rst.running = false; delete RST[rk]; }
      const list = wData[tab] || [];
      const w = list.find(x => x.id === wId);
      if(w) { 
          if(TS[w.id]) TS[w.id].running = false;
          renderList();
          updBbar();
          updCenterBtn(w, 'straight-sets', TS[w.id] || {}); 
      }
    }
</script>

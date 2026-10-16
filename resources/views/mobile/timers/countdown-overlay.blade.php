<script>
    // ── COUNTDOWN OVERLAY ──────────────────────────────────────────
    function countdown10(wId, cb){
      const card = document.getElementById('wk-' + wId);
      if(!card){ cb(); return; }
      const div = document.createElement('div');
      div.className = 'cd-overlay';
      div.id = 'cd-' + wId;
      div.onclick = () => {
        clearInterval(iv);
        div.remove();
        const ts = TS[wId];
        if (ts) { ts.running = false; ts.paused = false; ts.phase = 'idle'; }
        updBbar();
      };
      const sz = 186; const sw = 8; const r = (sz - sw) / 2; const c = 2 * Math.PI * r;
      const createNumSVG = (n) => `
        <svg width="140" height="110" viewBox="0 0 140 110" style="overflow:visible;">
          <text x="70" y="95" text-anchor="middle" font-size="92" font-weight="900" fill="none" stroke="#ffffff" stroke-width="18" stroke-linejoin="round" stroke-linecap="round" font-family="sans-serif">${n}</text>
          <text x="70" y="95" text-anchor="middle" font-size="92" font-weight="900" fill="none" stroke="#000000" stroke-width="11" stroke-linejoin="round" stroke-linecap="round" font-family="sans-serif">${n}</text>
          <text x="70" y="95" text-anchor="middle" font-size="92" font-weight="900" fill="none" stroke="#ffffff" stroke-width="4" stroke-linejoin="round" stroke-linecap="round" font-family="sans-serif">${n}</text>
          <text x="70" y="95" text-anchor="middle" font-size="92" font-weight="900" fill="#000000" stroke="none" font-family="sans-serif">${n}</text>
        </svg>
      `;
      div.innerHTML = `
        <div style="position:relative;width:${sz}px;height:${sz}px;display:flex;align-items:center;justify-content:center;">
          <img src="{{ asset('img/Bifrost_White_No_Text.png') }}" style="position:absolute;inset:0;width:100%;height:100%;opacity:0.2;object-fit:contain;transform:scale(0.7);" />
          <svg width="${sz}" height="${sz}" style="position:absolute;inset:0;transform:rotate(-90deg);">
             <circle cx="${sz/2}" cy="${sz/2}" r="${r}" stroke="red" stroke-width="${sw}" fill="transparent" opacity="0.2" />
             <circle id="cd-circ-${wId}" cx="${sz/2}" cy="${sz/2}" r="${r}" stroke="red" stroke-width="${sw}" stroke-linecap="round" fill="transparent" stroke-dasharray="${c}" stroke-dashoffset="${c}" style="transition: stroke-dashoffset 1s linear;" />
          </svg>
          <div id="cdn-${wId}" style="position:relative;z-index:10;display:flex;flex-direction:column;align-items:center;justify-content:center;">
             ${createNumSVG(9)}
          </div>
        </div>
      `;
      card.appendChild(div);
      setTimeout(()=>{
         const circ = document.getElementById('cd-circ-' + wId);
         if(circ) circ.style.strokeDashoffset = c - (c * (1/9));
      }, 50);

      let n = 9;
      const iv = setInterval(()=>{
        n--;
        const circ = document.getElementById('cd-circ-' + wId);
        if(circ && n >= 0){
          const prog = (9 - n) / 9;
          circ.style.strokeDashoffset = c - (c * prog);
        }
        if(n > 0){
           const el = document.getElementById('cdn-' + wId);
           if(el) el.innerHTML = createNumSVG(n);
        }else if(n === 0){
           const el = document.getElementById('cdn-' + wId);
           if(el) el.innerHTML = '<div style="color:#fff;font-size:42px;font-weight:900;letter-spacing:1px;margin-bottom:10px;">GO!</div>';
        }else{
           clearInterval(iv);
           const d = document.getElementById('cd-' + wId);
           if(d) d.remove();
           cb();
        }
      }, 1000);
    }
</script>

<script>
    // ── COUNTDOWN OVERLAY (Exact Mobile App Replica) ────────────────
    const cdAudioShort = new Audio("{{ asset('audio/beep_timer.mp3') }}");
    const cdAudioLong  = new Audio("{{ asset('audio/warning.mp3') }}");

    function playCdSynthBeep(freq, dur) {
      try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return;
        const ctx = new AudioCtx();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(freq, ctx.currentTime);
        gain.gain.setValueAtTime(0.25, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + dur);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start();
        osc.stop(ctx.currentTime + dur);
      } catch(e) {}
    }

    function playCdBeep(isWarning = false) {
      if (isWarning) {
        try {
          cdAudioLong.currentTime = 0;
          const p = cdAudioLong.play();
          if (p && p.catch) p.catch(() => playCdSynthBeep(520, 0.7));
        } catch(e) {
          playCdSynthBeep(520, 0.7);
        }
      } else {
        try {
          cdAudioShort.currentTime = 0;
          const p = cdAudioShort.play();
          if (p && p.catch) p.catch(() => playCdSynthBeep(880, 0.12));
        } catch(e) {
          playCdSynthBeep(880, 0.12);
        }
      }
    }

    function countdown10(wId, cb){
      const card = document.getElementById('wk-' + wId);
      if(!card){ cb(); return; }

      // Prime audio objects upon direct user click gesture
      try { cdAudioShort.load(); cdAudioLong.load(); } catch(e) {}

      // Prevent duplicate overlays
      const existing = document.getElementById('cd-' + wId);
      if(existing) existing.remove();

      // Ensure card accommodates full 186px timer without clipping
      const prevMinHeight = card.style.minHeight;
      card.style.minHeight = '230px';
      card.classList.add('countdown-active');

      const div = document.createElement('div');
      div.className = 'cd-overlay';
      div.id = 'cd-' + wId;

      let iv = null;
      let finishTimeout = null;

      const cleanup = () => {
        if (iv) { clearInterval(iv); iv = null; }
        if (finishTimeout) { clearTimeout(finishTimeout); finishTimeout = null; }
        try {
          cdAudioShort.pause(); cdAudioShort.currentTime = 0;
          cdAudioLong.pause(); cdAudioLong.currentTime = 0;
        } catch(e) {}
        div.remove();
        card.style.minHeight = prevMinHeight;
        card.classList.remove('countdown-active');
      };

      div.onclick = (e) => {
        e.stopPropagation();
        cleanup();
        const ts = TS[wId];
        if (ts) { ts.running = false; ts.paused = false; ts.phase = 'idle'; }
        updBbar();
      };

      const sz = 186; 
      const sw = 8; 
      const r = (sz - sw) / 2;
      const c = 2 * Math.PI * r;

      const createNumSVG = (n) => `
        <svg width="140" height="110" viewBox="0 0 140 110" style="overflow:visible;">
          <!-- Layer 1: Outer white stroke -->
          <text x="70" y="95" text-anchor="middle" font-size="92" font-weight="900" fill="none" stroke="#ffffff" stroke-width="18" stroke-linejoin="round" stroke-linecap="round" font-family="'Barlow Condensed', sans-serif">${n}</text>
          <!-- Layer 2: Black gap -->
          <text x="70" y="95" text-anchor="middle" font-size="92" font-weight="900" fill="none" stroke="#000000" stroke-width="11" stroke-linejoin="round" stroke-linecap="round" font-family="'Barlow Condensed', sans-serif">${n}</text>
          <!-- Layer 3: Inner white stroke -->
          <text x="70" y="95" text-anchor="middle" font-size="92" font-weight="900" fill="none" stroke="#ffffff" stroke-width="4" stroke-linejoin="round" stroke-linecap="round" font-family="'Barlow Condensed', sans-serif">${n}</text>
          <!-- Layer 4: Black fill — hollow interior -->
          <text x="70" y="95" text-anchor="middle" font-size="92" font-weight="900" fill="#000000" stroke="none" font-family="'Barlow Condensed', sans-serif">${n}</text>
        </svg>
      `;

      div.innerHTML = `
        <div style="position:relative;width:${sz}px;height:${sz}px;display:flex;align-items:center;justify-content:center;">
          <!-- Valhalla Mobile Logo Background (70% size, 0.2 opacity) -->
          <img src="{{ asset('img/valhalla-mobile-logo.png') }}" style="position:absolute;width:70%;height:70%;opacity:0.2;object-fit:contain;pointer-events:none;user-select:none;" alt="" />
          <!-- Circular Timer Ring -->
          <svg width="${sz}" height="${sz}" style="position:absolute;inset:0;transform:rotate(-90deg);">
             <circle cx="${sz/2}" cy="${sz/2}" r="${r}" stroke="red" stroke-width="${sw}" fill="transparent" opacity="0.2" />
             <circle id="cd-circ-${wId}" cx="${sz/2}" cy="${sz/2}" r="${r}" stroke="red" stroke-width="${sw}" stroke-linecap="round" fill="transparent" stroke-dasharray="${c} ${c}" stroke-dashoffset="${c}" style="transition: stroke-dashoffset 1s linear;" />
          </svg>
          <!-- Hollow Number (9 down to 0) -->
          <div id="cdn-${wId}" style="position:relative;z-index:10;display:flex;flex-direction:column;align-items:center;justify-content:center;pointer-events:none;">
             ${createNumSVG(9)}
          </div>
        </div>
      `;

      card.appendChild(div);

      // Start circle progress animation: starts from empty (c) and fills clockwise over 9s
      setTimeout(() => {
        const circ = document.getElementById('cd-circ-' + wId);
        if(circ) {
          circ.style.strokeDashoffset = c - (c * (1/9));
        }
      }, 50);

      let n = 9;
      iv = setInterval(() => {
        n--;
        const circ = document.getElementById('cd-circ-' + wId);
        const el = document.getElementById('cdn-' + wId);

        if (circ && n >= 0) {
          const prog = (9 - n) / 9;
          circ.style.strokeDashoffset = c - (c * prog);
        }

        if (n > 0) {
          if (el) el.innerHTML = createNumSVG(n);
          // Audio cues: short beep at 3s, 2s, and 1s left (exact replica of mobile CommonCircularTimer)
          if (n === 3 || n === 2 || n === 1) {
            playCdBeep(false);
          }
        } else if (n === 0) {
          // Reached 0: Show 0 (exact replica of mobile app: 9 to 0s) and play the warning long beep
          if (el) el.innerHTML = createNumSVG(0);
          playCdBeep(true);
          clearInterval(iv);
          iv = null;

          // 800ms delay so user clearly sees 0 and hears long warning beep (exact replica of mobile CountdownOverlay)
          finishTimeout = setTimeout(() => {
            cleanup();
            cb();
          }, 800);
        }
      }, 1000);
    }
</script>

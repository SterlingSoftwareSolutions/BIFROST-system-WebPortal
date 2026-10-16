<script>
    // ── TIMER BEEPS (Web Audio API with MP3 fallback) ───────────────
    function playTimerBeep(freq = 600, duration = 0.12) {
      try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (AudioCtx) {
          const ctx = new AudioCtx();
          const osc = ctx.createOscillator();
          const gain = ctx.createGain();
          osc.type = 'sine';
          osc.frequency.setValueAtTime(freq, ctx.currentTime);
          gain.gain.setValueAtTime(0.25, ctx.currentTime);
          gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration);
          osc.connect(gain);
          gain.connect(ctx.destination);
          osc.start();
          osc.stop(ctx.currentTime + duration);
          return;
        }
      } catch(e) {}
      try {
        const audio = new Audio('/audio/beep.mp3');
        audio.play().catch(()=>{});
      } catch(e) {}
    }
</script>

<script>
    // ─── GENERIC WORKOUT CARD ───────────────────────────────────────
    function cardGeneric(w, fk){
      const nm = fk ? fk.replace(/-/g,' ').replace(/\b\w/g, c => c.toUpperCase()) : 'Workout';
      const exs = (typeof getFormatExercises === 'function') ? getFormatExercises(w) : (w.straights || w.rounds || w.amraps || w.emoms || w.for_times || w.intervals || w.circuits || w.pyramids || []);
      const title = exName(exs[0] || {}) || w.workout_name || 'Workout';
      const isComp = (w.type_completed == 1 || w.status === 'completed' || (TS[w.id] && TS[w.id].allDone));
      return wkCard(w.id, nm, '', `<div style="color:#fff;font-size:13px;font-weight:700;">${title}</div>`, isComp, TS[w.id]?.timeTaken);
    }
</script>

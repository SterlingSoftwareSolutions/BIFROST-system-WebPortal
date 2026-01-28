<div class="flex-col w-full bg-gray-50 p-3  rounded-lg">
    <div class="flex justify-center text-center items-center font-bold mb-5 text-2xl">Create</div>

    <input type="hidden" id="common_workout_id" name="common_workout_id" value="">
    
    <!-- Type -->
    <div class="flex items-center border-b mt-2">
        <label for="common_type" class="w-60 block mb-1 text-base font-medium">Type <span class="text-red-500">*</span></label>
        <select id="common_type" name="common_type" class="w-1/3 px-3 py-3 border flex rounded mb-2 text-base" required>
            <option value="" selected disabled>-- Select Type --</option>
            <option value="Warmup">Warmup</option>
            <option value="Strength">Strength</option>
            <option value="Conditioning">Conditioning</option>
            <option value="Weightlifting">Weightlifting</option>
            <option value="Accessory">Accessory</option>
            <option value="PR's">PR's</option>
        </select>
    </div>

    <!-- Workout Name -->
    <div class="flex items-center border-b mt-2">
        <label for="common_name" class="w-60 block mb-1 text-base font-medium">Workout Name <span class="text-red-500">*</span></label>
        <input type="text" id="common_name" name="common_name"
            class="w-1/3 px-3 py-3 border flex rounded mb-2 text-base" required>
    </div>

    <!-- Format -->
    <div class="flex items-center border-b mt-2">
        <label for="common_format" class="w-60 block mb-1 text-base font-medium">Format <span class="text-red-500">*</span></label>
        <select id="common_format" name="common_format" class="w-1/3 px-3 py-3 border flex rounded mb-2 text-base" required>
            <option value="" selected disabled>-- Select Format --</option>
            <option value="Straight Sets">Straight Sets</option>
            <option value="Rounds">Rounds</option>
            <option value="AMRAP">AMRAP</option>
            <option value="EMOM">EMOM</option>
            <option value="For Time">For Time</option>
            <option value="Intervals">Intervals</option>
            <option value="Pyramid">Pyramid</option>
            <option value="Circuit">Circuit</option>
        </select>
    </div>

    <!-- Category -->
    {{-- <div class="flex items-center border-b mt-2">
        <label for="common_category" class="w-60 block mb-1 text-base font-medium">Category <span class="text-red-500">*</span></label>
        <select id="common_category" name="common_category" onchange="// getworkoutCommon(this)"
            class="w-1/3 px-3 py-3 border flex rounded mb-2 text-base">
            <option value="" selected disabled>-- Select Category --</option>
        </select>
    </div>
 --}}
    <!-- Workout/Exercise -->
    {{-- <div class="flex items-center border-b mt-2">
        <label for="common_workout" class="w-60 block mb-1 text-base font-medium">Workout <span class="text-red-500">*</span></label>
        <select id="common_workout" name="common_workout" class="w-1/3 px-3 py-3 border flex rounded mb-2 text-base">
            <option value="" selected disabled>-- Select Workout --</option>
        </select>
    </div> --}}
    
    <!-- Dynamic Specific Fields Container -->
    <div id="specific_fields_container" class="mt-2">
    </div>
    
</div>

<script>
    // Common Logic for Create Workout Form
    document.addEventListener('DOMContentLoaded', function() {
        const formatSelect = document.getElementById('common_format');
        const container = document.getElementById('specific_fields_container');

        // Watch for Format Changes
        if(formatSelect) {
            formatSelect.addEventListener('change', function() {
                const format = this.value;
                container.innerHTML = ''; // Clear existing

                if (format === 'Rounds') {
                    renderRoundsUI();
                } else if (format === 'AMRAP') {
                    renderAmrapUI();
                } else if (format === 'For Time') {
                    renderForTimeUI();
                } else if (format === 'Intervals') {
                    renderIntervalsUI();
                } else if (format === 'EMOM') {
                    renderEmomUI();
                } else if (format === 'Straight Sets') {
                    renderStraightSetUI();
                } else if (format === 'Circuit') {
                    renderCircuitUI();
                } else if (format === 'Pyramid') {
                    renderPyramidUI();
                }
            });
        }

        window.isSuperSet = false;

        function getExercisesTableHtml() {
            return `
                <!-- Round Detail Header -->
                <div class="mb-1 text-base font-medium">Round</div>
                
                <div class="flex gap-3 mb-2 px-2">
                    <div class="w-6"></div> <!-- Number col -->
                    <div class="w-[28%] font-bold text-lg text-gray-700">Exercise <span class="text-red-500">*</span></div>
                    <div class="w-36 font-bold text-lg text-center text-gray-700">Training Load <span class="text-red-500">*</span></div>
                    <div class="w-28 font-bold text-lg text-center text-gray-700">REPS <span class="text-red-500">*</span></div>
                    <div class="min-w-[80px]"></div> <!-- Action col -->
                </div>

                <!-- Rows Container -->
                <div id="round_rows_container" class="space-y-3">
                    ${getRoundRowHtml(1, true)}
                </div>
            `;
        }

        window.renderRoundsUI = function() {
            const html = `
                <div id="rounds_container">
                    <!-- Number of Rounds Counter -->
                    <div class="flex items-center mb-4 border-b pb-2">
                        <label class="w-60 block text-base font-medium text-black">Number of Rounds</label>
                        <div class="flex items-center">
                            <button type="button" onclick="decrementRoundCount('num_rounds')"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11">-</button>
                            <input type="text" id="num_rounds" name="num_rounds" value="1" readonly
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-16 text-base font-medium">
                            <button type="button" onclick="incrementRoundCount('num_rounds')"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11">+</button>
                        </div>
                    </div>

                    ${getExercisesTableHtml()}
                </div>
            `;
            container.innerHTML = html;
        }

        window.renderAmrapUI = function() {
            const html = `
                <div id="amrap_container">
                     <!-- Time To Complete Counter -->
                    <div class="flex items-center mb-4 border-b pb-4 mt-2">
                        <label class="w-60 block text-base font-semibold text-black">Time To Complete <span class="text-red-500">*</span></label>
                        <div class="flex items-center">
                            <button type="button" onclick="decrementTime('time_to_complete')"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 w-10 flex items-center justify-center">-</button>
                            <input type="text" id="time_to_complete" name="time_to_complete" value="04:00" readonly
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-20 text-sm font-medium">
                            <button type="button" onclick="incrementTime('time_to_complete')"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 w-10 flex items-center justify-center">+</button>
                        </div>
                    </div>

                    ${getExercisesTableHtml()}
                </div>
            `;
            container.innerHTML = html;
        }

        window.renderForTimeUI = function() {
            const html = `
                <div id="fortime_container">
                     <!-- As Fast As Possible Text -->
                    <div class="flex items-center mb-4 border-b pb-4 mt-2">
                        <label class="w-60 block text-base font-semibold text-black">As Fast As Possible (stopwatch)</label>
                    </div>

                    ${getExercisesTableHtml()}
                </div>
            `;
            container.innerHTML = html;
        }

        window.renderIntervalsUI = function() {
            const html = `
                <div id="intervals_container">
                    <!-- Intervals Counter -->
                    <div class="flex items-center mb-4 border-b pb-2 mt-2">
                        <label class="w-60 block text-base font-semibold text-black">Intervals</label>
                        <div class="flex items-center">
                            <button type="button" onclick="decrementIntervalCount()"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11">-</button>
                            <input type="text" id="num_intervals" name="num_intervals" value="1" readonly
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-16 text-base font-medium">
                            <button type="button" onclick="incrementIntervalCount()"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11">+</button>
                        </div>
                    </div>

                    <!-- Headers -->
                    <div class="flex gap-3 mb-2 px-2">
                         <div class="w-6"></div>
                         <div class="w-[18%] font-bold text-sm text-gray-700">Exercise <span class="text-red-500">*</span></div>
                         <div class="w-28 font-bold text-sm text-center text-gray-700">Training Load <span class="text-red-500">*</span></div>
                         <div class="flex-1 flex gap-2">
                             <div class="w-1/2 font-bold text-sm text-center text-gray-700">Work</div>
                             <div class="w-1/2 font-bold text-sm text-center text-gray-700">Rest</div>
                         </div>
                    </div>

                    <div id="interval_rows_container" class="space-y-3">
                        ${getIntervalRowHtml(1)}
                    </div>
                </div>
            `;
            container.innerHTML = html;
        }

        window.renderPyramidUI = function() {
            const html = `
                <div id="pyramid_container">
                    <!-- Layers Counter -->
                    <div class="flex items-center mb-4 border-b pb-2 mt-2">
                        <label class="w-60 block text-base font-semibold text-black">Number of Layers</label>
                        <div class="flex items-center">
                            <button type="button" onclick="decrementLayerCount()"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 w-10 flex justify-center items-center font-bold text-lg">-</button>
                            <input type="text" id="num_layers" name="num_layers" value="1" readonly
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-16 text-base font-medium">
                            <button type="button" onclick="incrementLayerCount()"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 w-10 flex justify-center items-center font-bold text-lg">+</button>
                        </div>
                    </div>

                    <!-- Headers -->
                    <div class="flex gap-4 mb-2">
                        <div class="flex-1 font-bold text-sm text-gray-700">Exercise <span class="text-red-500">*</span></div>
                        <div class="w-32 font-bold text-sm text-center text-gray-700">Training Load <span class="text-red-500">*</span></div>
                        <div class="w-32 font-bold text-sm text-center text-gray-700">REPS <span class="text-red-500">*</span></div>
                    </div>

                    <div id="pyramid_rows_container" class="space-y-3">
                        ${getPyramidRowHtml(1)}
                    </div>
                </div>
            `;
            container.innerHTML = html;
        }

        window.incrementLayerCount = function() {
             const el = document.getElementById('num_layers');
             if(el) {
                 let val = parseInt(el.value) + 1;
                 el.value = val;
                 updatePyramidLayers(val);
             }
        }
        
        window.decrementLayerCount = function() {
             const el = document.getElementById('num_layers');
             if(el && el.value > 1) {
                 let val = parseInt(el.value) - 1;
                 el.value = val;
                 updatePyramidLayers(val);
             }
        }

        window.updatePyramidLayers = function(count) {
             const container = document.getElementById('pyramid_rows_container');
             if(!container) return;
             const currentRows = container.querySelectorAll('.pyramid-row').length;
             
             if (count > currentRows) {
                 for(let i = currentRows + 1; i <= count; i++) {
                     container.insertAdjacentHTML('beforeend', getPyramidRowHtml(i));
                 }
             } else if (count < currentRows) {
                 for(let i = currentRows; i > count; i--) {
                     const row = document.getElementById(`pyramid_row_${i}`);
                     if(row) row.remove();
                 }
             }
        }

        window.getPyramidRowHtml = function(index) {
            // Only show Exercise dropdown for the first row
            const exerciseHtml = index === 1 ? `
                 <select name="pyramid_exercise" class="w-full bg-gray-100 border border-gray-300 rounded p-2.5 h-11 text-sm font-bold">
                    <option value="test">Back squat</option> 
                 </select>
            ` : `<div class="h-11"></div>`;

            return `
                <div class="pyramid-row flex gap-4 items-center" id="pyramid_row_${index}">
                    <!-- Exercise Column -->
                    <div class="flex-1">
                        ${exerciseHtml}
                    </div>

                    <!-- Training Load Column -->
                    <div class="w-32 flex gap-1 justify-center">
                        <input type="text" name="pyramid_load_${index}" value="80" class="w-16 border border-gray-300 rounded-s p-2.5 h-11 text-center text-sm font-bold">
                        <select name="pyramid_unit_${index}" class="bg-gray-100 border border-l-0 border-gray-300 rounded-e px-1 h-11 text-center text-sm font-bold w-14">
                            <option value="%" selected>%</option>
                            <option value="kg">kg</option>
                        </select>
                    </div>

                    <!-- REPS Column -->
                    <div class="w-32 flex items-center justify-center">
                        <button type="button" onclick="decrementValue('pyramid_reps_${index}')" class="bg-gray-100 border border-gray-300 rounded-s-lg p-3 h-11 w-10 flex justify-center items-center font-bold text-lg">-</button>
                        <input type="text" id="pyramid_reps_${index}" name="pyramid_reps_${index}" value="10" readonly class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-12 text-sm font-bold">
                        <button type="button" onclick="incrementValue('pyramid_reps_${index}')" class="bg-gray-100 border border-gray-300 rounded-e-lg p-3 h-11 w-10 flex justify-center items-center font-bold text-lg">+</button>
                    </div>
                </div>
            `;
        }

        window.renderEmomUI = function() {
            const html = `
                <div id="emom_container">
                    <!-- Minutes Counter -->
                    <div class="flex items-center mb-4 border-b pb-2 mt-2">
                        <label class="w-60 block text-base font-semibold text-black">Minutes</label>
                        <div class="flex items-center">
                            <button type="button" onclick="decrementMinuteCount()"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11">-</button>
                            <input type="text" id="num_minutes" name="num_minutes" value="1" readonly
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-16 text-base font-medium">
                            <button type="button" onclick="incrementMinuteCount()"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11">+</button>
                        </div>
                    </div>

                    <!-- Headers -->
                    <div class="flex gap-3 mb-2 px-2">
                        <div class="w-6"></div> <!-- Number col -->
                        <div class="flex-1 font-bold text-sm text-gray-700">Exercise <span class="text-red-500">*</span></div>
                        <div class="w-32 font-bold text-sm text-center text-gray-700">Training Load <span class="text-red-500">*</span></div>
                        <div class="w-28 font-bold text-sm text-center text-gray-700">REPS <span class="text-red-500">*</span></div>
                    </div>

                    <div id="emom_rows_container" class="space-y-3">
                        ${getEmomRowHtml(1)}
                    </div>
                </div>
            `;
            container.innerHTML = html;
        }

        window.renderStraightSetUI = function() {
             const superSetClass = window.isSuperSet ? '' : 'hidden';
             
             // Dynamic parts
             const mainExerciseHtml = `
                    <div class="flex gap-3 mb-2 px-2 items-end">
                        <div class="flex-1">
                             <label class="block text-sm font-bold text-gray-700 mb-1">Exercise <span class="text-red-500">*</span></label>
                             <select name="ss_exercise_1" class="w-full bg-gray-100 border border-gray-300 rounded p-2.5 h-11 text-sm">
                                <option value="test">Back squat</option> 
                             </select>
                        </div>
                        <div class="w-24 text-center">
                             <label class="block text-sm font-bold text-gray-700 mb-1">Training Load <span class="text-red-500">*</span></label>
                             <div class="flex gap-1 justify-center">
                                <input type="text" name="ss_load_1" value="80" class="w-16 border border-gray-300 rounded-s p-2.5 h-11 text-center text-sm font-bold">
                                <select name="ss_unit_1" class="bg-gray-100 border border-l-0 border-gray-300 rounded-e px-1 h-11 text-center text-sm font-bold w-14">
                                    <option value="%" selected>%</option>
                                    <option value="kg">kg</option>
                                </select>
                             </div>
                        </div>
                        <div class="w-32 text-center"> <!-- Increased width for reps control -->
                             <label class="block text-sm font-bold text-gray-700 mb-1">REPS<span class="text-red-500">*</span></label>
                             <div class="flex items-center justify-center">
                                <button type="button" onclick="decrementValue('ss_reps_main')" class="bg-gray-100 border border-gray-300 rounded-s-lg p-3 h-11 w-10 flex justify-center items-center font-bold text-lg">-</button>
                                <input type="text" id="ss_reps_main" name="ss_reps_main" value="6" readonly class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-12 text-sm font-bold">
                                <button type="button" onclick="incrementValue('ss_reps_main')" class="bg-gray-100 border border-gray-300 rounded-e-lg p-3 h-11 w-10 flex justify-center items-center font-bold text-lg">+</button>
                            </div>
                        </div>
                        <div class="min-w-[140px]">
                              <button type="button" onclick="toggleSuperSet()" class="border border-black bg-white hover:bg-gray-50 text-black font-bold rounded px-4 py-2 flex items-center justify-center h-11 text-sm w-full shadow-sm">
                                  ${window.isSuperSet ? 'Remove Super Set' : '+ Add Super Set'}
                              </button>
                        </div>
                    </div>
             `;
             
             const superSetHtml = window.isSuperSet ? `
                    <div class="flex gap-3 mb-4 px-2 items-end bg-gray-50 p-2 rounded">
                        <div class="flex-1">
                             <select name="ss_exercise_2" class="w-full bg-gray-100 border border-gray-300 rounded p-2.5 h-11 text-sm">
                                <option value="test2">RDL</option> 
                             </select>
                        </div>
                        <div class="w-24 text-center">
                             <div class="flex gap-1 justify-center">
                                <input type="text" name="ss_load_2" value="60" class="w-16 border border-gray-300 rounded-s p-2.5 h-11 text-center text-sm font-bold">
                                <select name="ss_unit_2" class="bg-gray-100 border border-l-0 border-gray-300 rounded-e px-1 h-11 text-center text-sm font-bold w-14">
                                    <option value="%">%</option>
                                    <option value="kg" selected>kg</option>
                                </select>
                             </div>
                        </div>
                        <div class="w-32 text-center">
                             <div class="flex items-center justify-center">
                                <button type="button" onclick="decrementValue('ss_reps_secondary')" class="bg-gray-100 border border-gray-300 rounded-s-lg p-3 h-11 w-10 flex justify-center items-center font-bold text-lg">-</button>
                                <input type="text" id="ss_reps_secondary" name="ss_reps_secondary" value="6" readonly class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-12 text-sm font-bold">
                                <button type="button" onclick="incrementValue('ss_reps_secondary')" class="bg-gray-100 border border-gray-300 rounded-e-lg p-3 h-11 w-10 flex justify-center items-center font-bold text-lg">+</button>
                            </div>
                        </div>
                        <div class="min-w-[140px]"></div>
                    </div>
             ` : '';

            const html = `
                <div id="straight_set_container">
                    <div class="mb-6 border-b pb-4">
                        ${mainExerciseHtml}
                        ${superSetHtml}
                    </div>

                     <div class="flex">
                          <!-- Left Label -->
                          <div class="w-16 flex justify-center items-start pt-3 font-bold text-gray-800 text-base">SET <span class="text-red-500">*</span></div>
                          
                          <!-- Right List -->
                          <div class="flex-1">
                              <div id="straight_set_rows_container" class="space-y-1">
                                 <!-- Initial Sets -->
                                 ${getStraightSetRowHtml(1, true)} 
                              </div>
                              
                              <!-- Footer Add Button (Always Visible) -->
                              <div class="flex justify-end mt-4">
                                   <button type="button" onclick="addStraightSetRow()" class="bg-black hover:bg-gray-800 text-white font-bold rounded px-4 h-10 text-sm w-[100px]">
                                       + Add set
                                   </button>
                              </div>
                          </div>
                     </div>
                 </div>
            `;
            container.innerHTML = html;
        }

        window.toggleSuperSet = function() {
            window.isSuperSet = !window.isSuperSet;
            renderStraightSetUI(); 
        }

        window.addStraightSetRow = function() {
            const container = document.getElementById('straight_set_rows_container');
            if(container) {
                // Calculate new index
                let newIndex = 1;
                const allRows = container.querySelectorAll('[id^="ss_row_"]');
                if(allRows.length > 0) {
                     const lastId = allRows[allRows.length - 1].id;
                     const parts = lastId.split('_');
                     newIndex = parseInt(parts[parts.length - 1]) + 1;
                }

                // Unified Logic: Just append for both modes. 
                // Super Set now follows the same "List" pattern (external remove button).
                // We pass 'false' for isLast because the concept of "Action Cell" is gone for Super Set list view.
                // However, for Standard view, we used 'false'. Let's check getStraightSetRowHtml signature.
                // It uses isLast only for the old Table layout. We can ignore it or pass false.
                
                container.insertAdjacentHTML('beforeend', getStraightSetRowHtml(newIndex, false));
            }
        }

        window.removeStraightSetRow = function(id) {
            const row = document.getElementById(id);
            if(row) row.remove();
        }

        window.removeStraightSetExercise = function(rowId, lineId, btnId) {
            const line = document.getElementById(lineId);
            const btn = document.getElementById(btnId);
            if(line) line.remove();
            if(btn) btn.remove();

            const row = document.getElementById(rowId);
            if(row) {
                // Check if any specific lines remain
                // We rely on the naming convention or class query. 
                // Since we are adding IDs, let's query by the generic pattern or just check children count of the container if possible.
                // But the container structure is fluid.
                // Easiest is to check if we can find any lines starting with 'ss_line_' inside this row.
                // Note: The index is embedded in the ID, so querying strictly substring is safer.
                // However, easier approach: Check if button container has children.
                const btns = row.querySelectorAll('button[onclick^="removeStraightSetExercise"]');
                if(btns.length === 0) {
                    row.remove();
                } else {
                    // Logic to clean up borders if needed (e.g. ensure last line has no border-b)
                    // Find the content container
                    const contentBox = row.querySelector('.overflow-hidden.bg-white.border'); 
                    if(contentBox) {
                        const contentContainer = contentBox.children[1]; // Index 0 is index col, Index 1 is content col
                        if(contentContainer && contentContainer.children.length > 0) {
                             const lastChild = contentContainer.lastElementChild;
                             if(lastChild) lastChild.classList.remove('border-b');
                        }
                    }
                }
            }
        }

        window.getStraightSetRowHtml = function(index, isLast) {
            const isSuper = window.isSuperSet;
            
            const repsControl = (idx, suffix) => `
                <div class="flex items-center">
                    <button type="button" onclick="decrementValue('ss_reps_${idx}${suffix}')" class="bg-gray-100 border border-gray-300 rounded-s p-1 h-8 w-7 flex justify-center items-center">-</button>
                    <input type="text" id="ss_reps_${idx}${suffix}" name="ss_reps_${idx}${suffix}" value="6" class="border-y border-gray-300 h-8 w-10 text-center text-xs font-bold">
                    <button type="button" onclick="incrementValue('ss_reps_${idx}${suffix}')" class="bg-gray-100 border border-gray-300 rounded-e p-1 h-8 w-7 flex justify-center items-center">+</button>
                </div>
            `;
            
            let removeBtn = '';
            // Standard: Row 1 no button. Super Set: All rows have button (based on image).
            if (isSuper || index > 1) {
                 removeBtn = `
                      <button type="button" onclick="removeStraightSetRow('ss_row_${index}')" class="bg-red-500 hover:bg-red-600 text-white font-bold rounded px-4 h-10 text-xs w-20">
                          Remove
                      </button>
                 `;
            }

            if (!isSuper) {
                 return `
                    <div class="flex items-center gap-4" id="ss_row_${index}">
                         <div class="w-8 text-center font-bold text-gray-600 text-sm">${index}</div>
                         
                         <!-- Reps Control -->
                         <div class="flex items-center">
                              <button type="button" onclick="decrementValue('ss_reps_${index}')" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s p-2 h-10 w-9 flex justify-center items-center font-bold text-lg">-</button>
                              <input type="text" id="ss_reps_${index}" name="ss_reps_${index}" value="6" class="border-y border-gray-300 h-10 w-12 text-center text-sm font-bold text-gray-400">
                              <button type="button" onclick="incrementValue('ss_reps_${index}')" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e p-2 h-10 w-9 flex justify-center items-center font-bold text-lg">+</button>
                         </div>
                         
                         <div class="font-bold text-sm text-gray-700">REPS</div>
                         
                         <!-- Load Display -->
                         <div class="flex items-center">
                             <input type="text" name="ss_load_${index}" value="80" class="border border-gray-300 rounded p-2 h-10 w-14 text-center text-sm font-bold">
                             <span class="ml-1 text-sm font-bold text-gray-400">%</span>
                         </div>
                         
                         <!-- Action Button -->
                         <div class="ml-auto">
                              ${removeBtn}
                         </div>
                    </div>
                 `;
            } else {
                 // Super Set View: Separate Rows (Bordered Box + Centered External Button)
                 
                 return `
                     <div class="flex items-center gap-4" id="ss_row_${index}">
                         <!-- Box: Index + Content -->
                         <div class="flex-1 flex overflow-hidden bg-white border border-black rounded">
                             <!-- Index Column (inside box) -->
                             <div class="w-10 flex items-center justify-center border-r border-black font-bold text-gray-700 bg-gray-50 text-base">
                                 ${index}
                             </div>

                             <!-- Content Column -->
                             <div class="flex-1">
                                 <!-- Row 1 -->
                                 <div class="flex items-center gap-2 p-1 border-b border-gray-200" id="ss_line_${index}_1">
                                     <div class="w-32 font-bold text-sm">Back squat</div>
                                     <div class="flex items-center ml-auto">
                                          ${repsControl(index, '_1')}
                                          <div class="text-xs font-bold text-gray-500 mx-2">REPS</div>
                                          <div class="flex items-center mr-2">
                                              <input type="text" name="ss_load_${index}_1" value="80" class="border border-gray-300 rounded p-2 h-10 w-14 text-center text-sm font-bold">
                                              <select name="ss_unit_${index}_1" class="ml-1 border border-gray-300 rounded h-10 w-12 text-center text-sm font-bold bg-white">
                                                  <option value="%" selected>%</option>
                                                  <option value="kg">kg</option>
                                              </select>
                                          </div>
                                     </div>
                                 </div>
                                 <!-- Row 2 -->
                                 <div class="flex items-center gap-2 p-1" id="ss_line_${index}_2">
                                     <div class="w-32 font-bold text-sm">RDL</div>
                                     <div class="flex items-center ml-auto">
                                          ${repsControl(index, '_2')}
                                          <div class="text-xs font-bold text-gray-500 mx-2">REPS</div>
                                          <div class="flex items-center mr-2">
                                              <input type="text" name="ss_load_${index}_2" value="60" class="border border-gray-300 rounded p-2 h-10 w-14 text-center text-sm font-bold">
                                              <select name="ss_unit_${index}_2" class="ml-1 border border-gray-300 rounded h-10 w-12 text-center text-sm font-bold bg-white">
                                                  <option value="%">%</option>
                                                  <option value="kg" selected>kg</option>
                                              </select>
                                          </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         
                         <!-- External Action Buttons (Stacked, Dual) -->
                         <div class="flex flex-col gap-1">
                             <button type="button" id="ss_btn_${index}_1" onclick="removeStraightSetExercise('ss_row_${index}', 'ss_line_${index}_1', 'ss_btn_${index}_1')" class="bg-red-500 hover:bg-red-600 text-white font-bold rounded px-4 h-8 text-xs w-20">
                                 Remove
                             </button>
                             <button type="button" id="ss_btn_${index}_2" onclick="removeStraightSetExercise('ss_row_${index}', 'ss_line_${index}_2', 'ss_btn_${index}_2')" class="bg-red-500 hover:bg-red-600 text-white font-bold rounded px-4 h-8 text-xs w-20">
                                 Remove
                             </button>
                         </div>
                    </div>
                 `;
            }
        }

        window.incrementMinuteCount = function() {
             const el = document.getElementById('num_minutes');
             if(el) {
                 let val = parseInt(el.value) + 1;
                 el.value = val;
                 updateEmomRows(val);
             }
        }
        
        window.decrementMinuteCount = function() {
             const el = document.getElementById('num_minutes');
             if(el && el.value > 1) {
                 let val = parseInt(el.value) - 1;
                 el.value = val;
                 updateEmomRows(val);
             }
        }

        window.updateEmomRows = function(count) {
             const container = document.getElementById('emom_rows_container');
             if(!container) return;
             const currentRows = container.querySelectorAll('.emom-row').length;
             
             if (count > currentRows) {
                 for(let i = currentRows + 1; i <= count; i++) {
                     container.insertAdjacentHTML('beforeend', getEmomRowHtml(i));
                 }
             } else if (count < currentRows) {
                 for(let i = currentRows; i > count; i--) {
                     const row = document.getElementById(`emom_row_${i}`);
                     if(row) row.remove();
                 }
             }
        }

        window.getEmomRowHtml = function(index) {
            return `
                <div class="flex items-center gap-3 emom-row" id="emom_row_${index}">
                    <div class="w-6 text-center font-bold text-gray-500 row-number text-sm">${index}</div>
                    
                     <!-- Exercise -->
                    <div class="flex-1">
                        <select name="emom_exercise_${index}" class="w-full bg-gray-100 border border-gray-300 rounded p-2.5 h-11 text-sm">
                            <option value="test">Back squat</option> 
                        </select>
                    </div>
                    
                    <!-- Training Load -->
                    <div class="w-32">
                        <div class="flex gap-1">
                            <input type="text" name="emom_load_${index}" value="80" class="w-2/3 border border-gray-300 rounded p-2.5 h-11 text-center text-sm">
                            <select name="emom_unit_${index}" class="w-1/3 border border-gray-300 rounded bg-white h-11 px-0 text-xs text-center font-bold">
                                <option value="%">%</option>
                                <option value="kg">kg</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- REPS -->
                    <div class="w-28">
                         <div class="flex items-center">
                            <button type="button" onclick="decrementValue('emom_reps_${index}')"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 w-8 flex justify-center items-center">-</button>
                            <input type="text" id="emom_reps_${index}" name="emom_reps_${index}" value="0" readonly
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-full text-sm">
                            <button type="button" onclick="incrementValue('emom_reps_${index}')"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 w-8 flex justify-center items-center">+</button>
                        </div>
                    </div>
                </div>
            `;
        }

        window.renderCircuitUI = function() {
            const html = `
                <div id="circuit_container">
                    <!-- Stations Counter -->
                    <div class="flex items-center mb-4 border-b pb-2 mt-2">
                        <label class="w-60 block text-base font-semibold text-black">Number of Stations</label>
                        <div class="flex items-center">
                            <button type="button" onclick="decrementStationCount()"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11">-</button>
                            <input type="text" id="num_stations" name="num_stations" value="1" readonly
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-16 text-base font-medium">
                            <button type="button" onclick="incrementStationCount()"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11">+</button>
                        </div>
                    </div>

                    <div id="circuit_stations_container" class="space-y-6">
                        ${getCircuitStationHtml(1)}
                    </div>
                </div>
            `;
            container.innerHTML = html;
        }

        window.incrementStationCount = function() {
             const el = document.getElementById('num_stations');
             if(el) {
                 let val = parseInt(el.value) + 1;
                 el.value = val;
                 updateCircuitStations(val);
             }
        }
        
        window.decrementStationCount = function() {
             const el = document.getElementById('num_stations');
             if(el && el.value > 1) {
                 let val = parseInt(el.value) - 1;
                 el.value = val;
                 updateCircuitStations(val);
             }
        }

        window.updateCircuitStations = function(count) {
             const container = document.getElementById('circuit_stations_container');
             if(!container) return;
             const currentStations = container.children.length;
             
             if (count > currentStations) {
                 for(let i = currentStations + 1; i <= count; i++) {
                     container.insertAdjacentHTML('beforeend', getCircuitStationHtml(i));
                 }
             } else if (count < currentStations) {
                 for(let i = currentStations; i > count; i--) {
                     if(container.lastElementChild) container.lastElementChild.remove();
                 }
             }
        }

        window.getCircuitStationHtml = function(index) {
            return `
                <div class="station-block" id="station_${index}_block">
                    <div class="font-bold text-sm mb-2 text-gray-800">Station ${index}</div>
                    
                    <!-- Headers -->
                    <div class="flex gap-3 mb-2 px-2">
                         <div class="w-[28%] font-bold text-sm text-gray-700">Exercise <span class="text-red-500">*</span></div>
                         <div class="w-32 font-bold text-sm text-center text-gray-700">Training Load <span class="text-red-500">*</span></div>
                         <div class="w-28 font-bold text-sm text-center text-gray-700">REPS <span class="text-red-500">*</span></div>
                         <div class="min-w-[80px]"></div>
                    </div>
                    
                    <div id="station_${index}_rows" class="space-y-3">
                         ${getCircuitRowHtml(index, 1, true)}
                    </div>
                </div>
            `;
        }

        window.getCircuitRowHtml = function(stationIndex, rowIndex, isLast) {
             let btnHtml = '';
             if (isLast) {
                 btnHtml = `
                    <button type="button" onclick="addStationRow(${stationIndex})" class="border border-black bg-white hover:bg-gray-50 text-black font-bold rounded px-4 py-2 flex items-center justify-center h-11 min-w-[70px] text-sm">
                        + Add
                    </button>
                 `;
             } else {
                 btnHtml = `
                    <button type="button" onclick="removeStationRow(${stationIndex}, ${rowIndex})" class="text-red-500 hover:text-red-700 font-bold text-xl px-2 h-11 flex items-center justify-center min-w-[70px]">
                        &times;
                    </button>
                 `;
             }

             return `
                <div class="flex items-center gap-3" id="station_${stationIndex}_row_${rowIndex}">
                    <!-- Exercise -->
                    <div class="w-[28%]">
                        <select name="station_${stationIndex}_exercise_${rowIndex}" class="w-full bg-gray-100 border border-gray-300 rounded p-2.5 h-11 text-sm">
                                <option value="test">Back squat</option> 
                        </select>
                    </div>
                    
                    <!-- Training Load -->
                    <div class="w-32">
                        <div class="flex gap-1">
                            <input type="text" name="station_${stationIndex}_load_${rowIndex}" value="80" class="w-2/3 border border-gray-300 rounded p-2.5 h-11 text-center text-sm">
                            <select name="station_${stationIndex}_unit_${rowIndex}" class="w-1/3 border border-gray-300 rounded bg-white h-11 px-0 text-xs text-center font-bold">
                                <option value="%">%</option>
                                <option value="kg">kg</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- REPS -->
                    <div class="w-28">
                            <div class="flex items-center">
                            <button type="button" onclick="decrementValue('station_${stationIndex}_reps_${rowIndex}')"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 w-8 flex justify-center items-center">-</button>
                            <input type="text" id="station_${stationIndex}_reps_${rowIndex}" name="station_${stationIndex}_reps_${rowIndex}" value="10" readonly
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-full text-sm">
                            <button type="button" onclick="incrementValue('station_${stationIndex}_reps_${rowIndex}')"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 w-8 flex justify-center items-center">+</button>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <div class="h-11 flex items-center min-w-[80px] justify-center">
                        ${btnHtml}
                    </div>
                </div>
             `;
        }

        window.addStationRow = function(stationIndex) {
            const container = document.getElementById(`station_${stationIndex}_rows`);
            if (!container) return;
            
            // Generate a simple unique ID based on current timestamp or random to avoid collision if removing/adding logic is simple
             // Or better, just count children.
             // But we need unique rowIndex for names.
             // Let's use Date.now() or just incrementing logic if we had global state.
             // Simpler: get max index from existing children ids?
             // Let's just use timestamp for rowIndex to avoid collision.
            const rowIndex = Date.now();
            
            // Find current last row to change button
            const rows = container.children;
            if (rows.length > 0) {
                const lastRow = rows[rows.length - 1];
                // Extract previous index
                const lastRowId = lastRow.id; // station_X_row_Y
                const parts = lastRowId.split('_');
                const lastIdx = parts[parts.length - 1]; // Y
                
                // Replace last row with "Remove" version
                // Or just replace the button div?
                // Replacing entire HTML is easiest but loses input state.
                // Better: find the button container and replace innerHTML.
                const btnContainer = lastRow.lastElementChild;
                btnContainer.innerHTML = `
                    <button type="button" onclick="removeStationRow(${stationIndex}, ${lastIdx})" class="text-red-500 hover:text-red-700 font-bold text-xl px-2 h-11 flex items-center justify-center min-w-[70px]">
                        &times;
                    </button>
                `;
            }

            container.insertAdjacentHTML('beforeend', getCircuitRowHtml(stationIndex, rowIndex, true));
        }

        window.removeStationRow = function(stationIndex, rowIndex) {
            const row = document.getElementById(`station_${stationIndex}_row_${rowIndex}`);
            if (row) row.remove();
        }

        window.incrementLayerCount = function() {
             const el = document.getElementById('num_layers');
             if(el) {
                 let val = parseInt(el.value) + 1;
                 el.value = val;
                 updatePyramidRows(val);
             }
        }
        
        window.decrementLayerCount = function() {
             const el = document.getElementById('num_layers');
             if(el && el.value > 1) {
                 let val = parseInt(el.value) - 1;
                 el.value = val;
                 updatePyramidRows(val);
             }
        }

        window.updatePyramidRows = function(count) {
             const container = document.getElementById('pyramid_rows_container');
             if(!container) return;
             const currentRows = container.querySelectorAll('.pyramid-row').length;
             
             if (count > currentRows) {
                 for(let i = currentRows + 1; i <= count; i++) {
                     container.insertAdjacentHTML('beforeend', getPyramidRowHtml(i));
                 }
             } else if (count < currentRows) {
                 for(let i = currentRows; i > count; i--) {
                     const row = document.getElementById(`pyramid_row_${i}`);
                     if(row) row.remove();
                 }
             }
        }

        window.getPyramidRowHtml = function(index) {
            const showExercise = (index === 1);
            const exerciseHtml = showExercise ? `
                <select name="pyramid_exercise_${index}" class="w-full bg-gray-100 border border-gray-300 rounded p-2.5 h-11 text-sm">
                        <option value="test">Back squat</option> 
                </select>` : '';

            return `
                <div class="flex items-center gap-3 pyramid-row" id="pyramid_row_${index}">
                    <div class="w-6 text-center font-bold text-gray-500 row-number text-sm">${index}</div>
                    
                     <!-- Exercise -->
                    <div class="flex-1">
                        ${exerciseHtml}
                    </div>
                    
                    <!-- Training Load -->
                    <div class="w-32">
                        <div class="flex gap-1">
                            <input type="text" name="pyramid_load_${index}" value="80" class="w-2/3 border border-gray-300 rounded p-2.5 h-11 text-center text-sm">
                            <select name="pyramid_unit_${index}" class="w-1/3 border border-gray-300 rounded bg-white h-11 px-0 text-xs text-center font-bold">
                                <option value="%">%</option>
                                <option value="kg">kg</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- REPS -->
                    <div class="w-28">
                         <div class="flex items-center">
                            <button type="button" onclick="decrementValue('pyramid_reps_${index}')"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 w-8 flex justify-center items-center">-</button>
                            <input type="text" id="pyramid_reps_${index}" name="pyramid_reps_${index}" value="0" readonly
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-full text-sm">
                            <button type="button" onclick="incrementValue('pyramid_reps_${index}')"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 w-8 flex justify-center items-center">+</button>
                        </div>
                    </div>
                </div>
            `;
        }

        window.incrementTime = function(id) {
            const el = document.getElementById(id);
            if (!el) return;
            let parts = el.value.split(':');
            
            if (parts.length === 2) {
                // MM:SS
                let min = parseInt(parts[0]);
                let sec = parseInt(parts[1]);
                min++;
                el.value = (min < 10 ? '0' + min : min) + ':' + (sec < 10 ? '0' + sec : sec);
            } else if (parts.length === 3) {
                 // HH:MM:SS
                 let h = parseInt(parts[0]);
                 let m = parseInt(parts[1]);
                 let s = parseInt(parts[2]);
                 m++; 
                 if(m >= 60) { m = 0; h++; }
                 el.value = (h < 10 ? '0'+h : h) + ':' + (m < 10 ? '0'+m : m) + ':' + (s < 10 ? '0'+s : s);
            }
        }

        window.decrementTime = function(id) {
            const el = document.getElementById(id);
            if (!el) return;
             let parts = el.value.split(':');
            
            if (parts.length === 2) {
                let min = parseInt(parts[0]);
                let sec = parseInt(parts[1]);
                if (min > 0) min--;
                el.value = (min < 10 ? '0' + min : min) + ':' + (sec < 10 ? '0' + sec : sec);
            } else if (parts.length === 3) {
                 let h = parseInt(parts[0]);
                 let m = parseInt(parts[1]);
                 let s = parseInt(parts[2]);
                 if (m > 0) m--;
                 else if (h > 0) { h--; m=59; }
                 el.value = (h < 10 ? '0'+h : h) + ':' + (m < 10 ? '0'+m : m) + ':' + (s < 10 ? '0'+s : s);
            }
        }

        window.incrementIntervalCount = function() {
             const el = document.getElementById('num_intervals');
             if(el) {
                 let val = parseInt(el.value) + 1;
                 el.value = val;
                 updateIntervalRows(val);
             }
        }
        
        window.decrementIntervalCount = function() {
             const el = document.getElementById('num_intervals');
             if(el && el.value > 1) {
                 let val = parseInt(el.value) - 1;
                 el.value = val;
                 updateIntervalRows(val);
             }
        }

        window.updateIntervalRows = function(count) {
             const container = document.getElementById('interval_rows_container');
             if(!container) return;
             const currentRows = container.querySelectorAll('.interval-row').length;
             
             if (count > currentRows) {
                 for(let i = currentRows + 1; i <= count; i++) {
                     container.insertAdjacentHTML('beforeend', getIntervalRowHtml(i));
                 }
             } else if (count < currentRows) {
                 for(let i = currentRows; i > count; i--) {
                     const row = document.getElementById(`interval_row_${i}`);
                     if(row) row.remove();
                 }
             }
        }

        window.getIntervalRowHtml = function(index) {
            const showExercise = (index === 1);
            const exerciseHtml = showExercise ? `
                <select name="interval_exercise_${index}" class="w-full bg-gray-100 border border-gray-300 rounded p-2.5 h-11 text-sm">
                        <option value="test">Back squat</option> 
                </select>` : '';
            
            return `
                <div class="flex items-center gap-3 interval-row" id="interval_row_${index}">
                     <div class="w-6 text-center font-bold text-gray-500 row-number text-sm">${index}</div>
                     <div class="w-[18%]">
                        ${exerciseHtml}
                     </div>
                     <div class="w-28">
                        <div class="flex gap-1">
                            <input type="text" name="interval_load_${index}" value="80" class="w-2/3 border border-gray-300 rounded p-2.5 h-11 text-center text-sm">
                            <select name="interval_unit_${index}" class="w-1/3 border border-gray-300 rounded bg-white h-11 px-0 text-xs text-center font-bold">
                                <option value="%">%</option>
                                <option value="kg">kg</option>
                            </select>
                        </div>
                     </div>
                     <div class="flex-1 flex gap-2">
                         <!-- Work -->
                         <div class="flex items-center justify-center w-1/2">
                              <button type="button" onclick="decrementTime('interval_work_${index}')" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 w-8 flex items-center justify-center">-</button>
                              <input type="text" id="interval_work_${index}" name="interval_work_${index}" value="00:04:00" class="w-full border-y border-gray-300 bg-white h-11 text-center text-sm font-medium text-gray-900 px-0">
                              <button type="button" onclick="incrementTime('interval_work_${index}')" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 w-8 flex items-center justify-center">+</button>
                         </div>
                         <!-- Rest -->
                         <div class="flex items-center justify-center w-1/2">
                              <button type="button" onclick="decrementTime('interval_rest_${index}')" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 w-8 flex items-center justify-center">-</button>
                              <input type="text" id="interval_rest_${index}" name="interval_rest_${index}" value="00:04:00" class="w-full border-y border-gray-300 bg-white h-11 text-center text-sm font-medium text-gray-900 px-0">
                              <button type="button" onclick="incrementTime('interval_rest_${index}')" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 w-8 flex items-center justify-center">+</button>
                         </div>
                     </div>
                </div>
            `;
        }

        window.getRoundRowHtml = function(index, isLast) {
             let btnHtml = '';
             if (isLast) {
                 btnHtml = `
                    <button type="button" onclick="addNewRoundRow()" class="border border-black bg-white hover:bg-gray-50 text-black font-bold rounded px-4 py-2 flex items-center justify-center h-11 min-w-[70px] text-sm">
                        + Add
                    </button>
                 `;
             } else {
                 btnHtml = `
                    <button type="button" onclick="removeRoundRow(${index})" class="text-red-500 hover:text-red-700 font-bold text-xl px-2 h-11 flex items-center justify-center min-w-[70px]">
                        &times;
                    </button>
                 `;
             }

             return `
                <div class="flex items-center gap-3 round-row" id="round_row_${index}">
                    <!-- Row Number -->
                    <div class="w-7 text-center font-bold text-gray-500 row-number text-sm">
                    </div>

                    <!-- Exercise (Reduced Width) -->
                    <div class="w-[28%]">
                        <select name="round_exercise_${index}" class="w-full bg-gray-100 border border-gray-300 rounded p-2.5 h-11 text-sm">
                             <option value="test">Back squat</option> 
                        </select>
                    </div>
                    
                    <!-- Training Load -->
                    <div class="w-32">
                        <div class="flex gap-1">
                            <input type="text" name="round_load_${index}" value="80" class="w-2/3 border border-gray-300 rounded p-2.5 h-11 text-center text-sm">
                            <select name="round_unit_${index}" class="w-1/3 border border-gray-300 rounded bg-white h-11 px-0 text-xs text-center font-bold">
                                <option value="%">%</option>
                                <option value="kg">kg</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- REPS -->
                    <div class="w-28">
                         <div class="flex items-center">
                            <button type="button" onclick="decrementValue('round_reps_${index}')"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 w-8 flex justify-center items-center">-</button>
                            <input type="text" id="round_reps_${index}" name="round_reps_${index}" value="0" readonly
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-full text-sm">
                            <button type="button" onclick="incrementValue('round_reps_${index}')"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 w-8 flex justify-center items-center">+</button>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <div class="h-11 flex items-center min-w-[80px] justify-center action-col">
                        ${btnHtml}
                    </div>
                </div>
             `;
        };
    });

    // Global Helpers
    let roundRowCounter = 1;

    window.addNewRoundRow = function() {
        const container = document.getElementById('round_rows_container');
        // Change current last row button to Remove
        const rows = container.querySelectorAll('.round-row');
        if(rows.length > 0) {
            const lastRow = rows[rows.length - 1];
            const lastIdx = lastRow.id.replace('round_row_', '');
            const actionCol = lastRow.querySelector('.action-col');
            actionCol.innerHTML = `
                <button type="button" onclick="removeRoundRow(${lastIdx})" class="text-red-500 hover:text-red-700 font-bold text-xl px-2 h-11 flex items-center justify-center min-w-[70px]">
                    &times;
                </button>
            `;
        }
        
        const newIndex = ++roundRowCounter;
        const newRowHtml = getRoundRowHtml(newIndex, true);
        container.insertAdjacentHTML('beforeend', newRowHtml);
        updateRowNumbers();
    }
    
    window.removeRoundRow = function(index) {
        const row = document.getElementById(`round_row_${index}`);
        if(row) row.remove();
        checkLastRow();
        updateRowNumbers();
    }
    
    window.checkLastRow = function() {
        const container = document.getElementById('round_rows_container');
        const rows = container.querySelectorAll('.round-row');
        if (rows.length > 0) {
            const lastRow = rows[rows.length - 1];
            // Force it to have Add button
             const actionCol = lastRow.querySelector('.action-col');
             if (!actionCol.innerText.includes('Add')) { 
                 actionCol.innerHTML = `
                    <button type="button" onclick="addNewRoundRow()" class="border border-black bg-white hover:bg-gray-50 text-black font-bold rounded px-4 py-2 flex items-center justify-center h-11 min-w-[70px] text-sm">
                        + Add
*                      </button>
                 `;
             }
        } else {
            // All deleted? Add one back
            roundRowCounter = 0; // reset
            addNewRoundRow();
        }
    }

    window.updateRowNumbers = function() {
        const container = document.getElementById('round_rows_container');
        if(!container) return;
        const rows = container.querySelectorAll('.round-row');
        rows.forEach((row, i) => {
            const numEl = row.querySelector('.row-number');
            if(numEl) numEl.innerText = i + 1;
        });
    }

    window.getRowCount = function() {
         const container = document.getElementById('round_rows_container');
         return container ? container.querySelectorAll('.round-row').length + 1 : 1;
    }

    window.incrementRoundCount = function(id) {
        const el = document.getElementById(id);
        if(el) el.value = parseInt(el.value || 0) + 1;
    }
    window.decrementRoundCount = function(id) {
        const el = document.getElementById(id);
        if(el && el.value > 1) el.value = parseInt(el.value) - 1;
    }
    
    window.incrementValue = function(id) {
        const el = document.getElementById(id);
        if(el) el.value = parseInt(el.value || 0) + 1;
    }
    window.decrementValue = function(id) {
        const el = document.getElementById(id);
        if(el && el.value > 0) el.value = parseInt(el.value) - 1;
    }
</script>

<!-- START FORM -->
<form id="create_workout_form" action="{{ route('workout.store') }}" method="POST">
    @csrf

    <div class="flex-col w-full bg-gray-50 p-3  rounded-lg">
    
    <!-- Script for JS Alert -->
    @if(session('success'))
        <script>
            alert("{{ session('success') }}");
        </script>
    @endif

    @if(session('error'))
        <script>
            alert("{{ session('error') }}");
        </script>
    @endif

    <div class="flex justify-center text-center items-center font-bold mb-5 text-2xl">Create</div>

    <input type="hidden" id="common_workout_id" name="common_workout_id" value="">
    
    <!-- Type -->
    <div class="flex items-center border-b mt-2">
        <label for="common_type" class="w-60 block mb-1 text-base font-medium">Type <span class="text-red-500">*</span></label>
        <select id="common_type" name="common_type" class="flex-1 px-3 py-3 border flex rounded mb-2 text-base" required>
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
            class="flex-1 px-3 py-3 border flex rounded mb-2 text-base" required>
    </div>

    <!-- Format -->
    <div class="flex items-center border-b mt-2">
        <label for="common_format" class="w-60 block mb-1 text-base font-medium">Format <span class="text-red-500">*</span></label>
        <select id="common_format" name="common_format" class="flex-1 px-3 py-3 border flex rounded mb-2 text-base" required>
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
    
    <!-- Add Save Button at the bottom -->
    <div class="mt-6 flex justify-end">
        <button type="submit" id="save_workout_btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-lg">
            Save Workout
        </button>
    </div>

    </div>
</form>
<!-- END FORM -->

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

        window.superSetCount = 0;

        window.getUnitSelectOptionsHtml = function(selected) {
             const units = ['%', 'Kg', 'Cal', 'RPE', 'BW', 'N/A'];
             return units.map(u => `<option value="${u}" ${u === selected ? 'selected' : ''}>${u}</option>`).join('');
        }

        window.availableWorkouts = []; 

        // --- Custom Searchable Dropdown Logic ---

        // 1. Helper to generate the HTML
        window.getSearchableDropdownHtml = function(name, selectedValue) {
             const id = name.replace(/[^a-zA-Z0-9]/g, '_');
             const val = selectedValue || '';
             
             // Initial Display Text
             let displayText = val || '-- Select Exercise --';

             // List Items
             const itemsHtml = window.availableWorkouts.map(w => {
                 const txt = w.workout || w;
                 return `<div class="p-2 hover:bg-gray-100 cursor-pointer text-sm font-bold text-gray-700 block option-item" onclick="selectOption('${id}', '${txt}')">${txt}</div>`;
             }).join('');

             return `
                <div class="relative w-full custom-dropdown group" id="dropdown_${id}">
                    <input type="hidden" name="${name}" id="input_${id}" value="${val}">
                    
                     <!-- TRIGGER (Display Only) -->
                    <div onclick="toggleDropdown('${id}')"
                         class="w-full bg-gray-100 border border-gray-300 rounded p-2.5 h-11 text-sm font-bold flex items-center justify-between cursor-pointer focus:ring-2 focus:ring-blue-500"
                         tabindex="0">
                        <span id="display_${id}" title="${displayText}" class="${val ? 'text-gray-900' : 'text-gray-500'} block truncate flex-1 min-w-0 text-left pr-2">${displayText}</span>
                        <!-- Chevron Icon -->
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    
                    <!-- DROPDOWN CONTAINER (Hidden by default) -->
                    <div id="list_${id}" class="hidden absolute left-0 right-0 top-[100%] mt-1 bg-white border border-gray-300 rounded shadow-xl z-[9999] overflow-hidden">
                        
                        <!-- SEARCH BAR (Inside Dropdown) -->
                        <div class="p-2 border-b border-gray-100 bg-white sticky top-0">
                            <input type="text" 
                                   id="search_${id}"
                                   class="w-full border border-gray-300 rounded p-2 text-sm font-normal focus:outline-none focus:border-blue-500"
                                   placeholder="Search..."
                                   oninput="filterDropdown('${id}')"
                                   onclick="event.stopPropagation()" 
                                   autocomplete="off">
                        </div>

                        <!-- SCROLLABLE OPTIONS -->
                        <div id="scroll_${id}" class="max-h-40 overflow-y-auto">
                            ${itemsHtml || '<div class="p-2 text-gray-500 text-sm">Loading...</div>'}
                        </div>
                    </div>
                </div>
             `;
        }

        // 2. Logic to Toggle/Filter/Select
        window.toggleDropdown = function(id) {
            const list = document.getElementById(`list_${id}`);
            const search = document.getElementById(`search_${id}`);
            const trigger = document.getElementById(`dropdown_${id}`); // The relative parent

            // Close all others
            document.querySelectorAll('[id^="list_"]').forEach(el => {
                if(el.id !== `list_${id}`) {
                    el.classList.add('hidden');
                    // Reset others style just in case
                    el.style.position = ''; 
                    el.style.top = ''; 
                    el.style.left = '';
                    el.style.width = '';
                }
            });

            if(list && trigger) {
                const isHidden = list.classList.contains('hidden');
                if(isHidden) {
                    // Show
                    list.classList.remove('hidden');
                    
                    // FIXED POSITIONING LOGIC
                    // Calculate coordinates relative to viewport
                    const rect = trigger.getBoundingClientRect();
                    
                    list.style.position = 'fixed';
                    list.style.top = rect.bottom + 'px';
                    list.style.left = rect.left + 'px';
                    // Allow dropdown to be wider than the trigger if needed (fit content)
                    list.style.minWidth = rect.width + 'px';
                    list.style.width = 'auto'; 
                    list.style.maxWidth = '320px'; // Cap width to prevent covering the whole row
                    list.style.zIndex = '9999';

                    // Focus search
                    setTimeout(() => { if(search) search.focus(); }, 50);
                } else {
                    // Hide
                    list.classList.add('hidden');
                    // Reset CSS to avoid interference if moved or layout changes
                    list.style.position = '';
                }
            }
        }

        // Close dropdowns on global scroll to prevent detached floating
        window.addEventListener('scroll', function(e) {
             // If scrolling inside the dropdown itself, don't close it
             if(e.target.closest && e.target.closest('[id^="list_"]')) {
                 return;
             }
             
             document.querySelectorAll('[id^="list_"]:not(.hidden)').forEach(el => {
                 el.classList.add('hidden');
                 el.style.position = '';
             });
        }, true); // Capture phase to catch scroll on any element

        window.openDropdown = function(id) {
            const list = document.getElementById(`list_${id}`);
            if(list) list.classList.remove('hidden');
        }

        window.filterDropdown = function(id) {
            const input = document.getElementById(`search_${id}`);
            const scrollContainer = document.getElementById(`scroll_${id}`);
             if (!input || !scrollContainer) return;

            const filter = input.value.toLowerCase();
            const divs = scrollContainer.children;
            
            // Re-populate if empty (late fetch handling)
             if (divs.length <= 1 && window.availableWorkouts.length > 0 && (!divs[0] || divs[0].classList.contains('text-gray-500'))) {
                  scrollContainer.innerHTML = window.availableWorkouts.map(w => {
                     const txt = w.workout || w;
                     return `<div class="p-2 hover:bg-gray-100 cursor-pointer text-sm font-bold text-gray-700 block option-item" onclick="selectOption('${id}', '${txt}')">${txt}</div>`;
                 }).join('');
             }

            for (let i = 0; i < divs.length; i++) {
                const txt = divs[i].innerText;
                if (txt.toLowerCase().indexOf(filter) > -1) {
                    divs[i].classList.remove('hidden');
                } else {
                    divs[i].classList.add('hidden');
                }
            }
        }

        window.selectOption = function(id, value) {
            const input = document.getElementById(`input_${id}`);
            const display = document.getElementById(`display_${id}`);
            const list = document.getElementById(`list_${id}`);
            
            if(input) {
                input.value = value;
                // Trigger change event manually for the hidden input
                const event = new Event('change', { bubbles: true });
                input.dispatchEvent(event);
            }
            if(display) {
                display.innerText = value;
                display.title = value; // Update tooltip
                display.classList.remove('text-gray-500');
                display.classList.add('text-gray-900');
            }
            if(list) {
                list.classList.add('hidden');
                list.style.position = ''; // Reset
            }

            // Hook for external logic
            if (window.onGlobalSelectOption) {
                window.onGlobalSelectOption(id, value);
            }
        }
        
        // Close on click outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-dropdown') && !e.target.closest('[id^="list_"]')) {
                document.querySelectorAll('[id^="list_"]').forEach(el => {
                    el.classList.add('hidden');
                    el.style.position = '';
                });
            }
        });

        async function fetchWorkouts() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch('/get-workout', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({})
                });
                const data = await response.json();
                if(data.workouts) {
                    window.availableWorkouts = data.workouts;
                }
            } catch (error) {
                console.error('Error fetching workouts:', error);
            }
        }
        fetchWorkouts();

        // Universal Table Generator
        function getUniversalTableHtml(prefix) {
            let headerText = 'Exercises'; // Default
            if(prefix === 'round') headerText = 'Round';
            
            return `
                <!-- Header -->
                <div class="mb-1 text-base font-medium">${headerText}</div>
                
                <div class="flex gap-3 mb-2 px-2">
                    <div class="w-6"></div> <!-- Number col -->
                    <div class="flex-1 font-bold text-lg text-gray-800">Exercise <span class="text-red-500">*</span></div>
                    <div class="w-36 font-bold text-lg text-center text-gray-800">Training Load <span class="text-red-500">*</span></div>
                    <div class="w-28 font-bold text-lg text-center text-gray-700">REPS <span class="text-red-500">*</span></div>
                    <div class="min-w-[80px]"></div> <!-- Action col -->
                </div>

                <!-- Rows Container -->
                <div id="${prefix}_rows_container" class="space-y-3 max-h-96 overflow-y-auto pr-2 custom-scroll">
                    ${getUniversalRowHtml(1, true, prefix)}
                </div>
            `;
        }

        window.getUniversalRowHtml = function(index, isLast, prefix) {
             let btnHtml = '';
             if (isLast) {
                 btnHtml = `
                    <button type="button" onclick="addNewUniversalRow('${prefix}')" class="border border-black bg-white hover:bg-gray-50 text-black font-bold rounded px-4 py-2 flex items-center justify-center h-11 min-w-[70px] text-sm">
                        + Add
                    </button>
                 `;
             } else {
                 btnHtml = `
                    <button type="button" onclick="removeUniversalRow(${index}, '${prefix}')" class="text-red-500 hover:text-red-700 font-bold text-xl px-2 h-11 flex items-center justify-center min-w-[70px]">
                        &times;
                    </button>
                 `;
             }

             return `
                <div class="flex items-center gap-3 ${prefix}-row" id="${prefix}_row_${index}">
                    <!-- Row Number -->
                    <div class="w-7 text-center font-bold text-gray-500 row-number text-sm">
                    </div>

                    <!-- Exercise -->
                    <div class="flex-1 min-w-0">
                        ${getSearchableDropdownHtml(`${prefix}_exercise_${index}`, '')}
                    </div>
                    
                    <!-- Training Load -->
                    <div class="w-32">
                        <div class="flex gap-1">
                            <input type="text" name="${prefix}_load_${index}" placeholder="80" class="w-2/3 border border-gray-300 rounded p-2.5 h-11 text-center text-sm">
                            <select name="${prefix}_unit_${index}" class="w-1/3 border border-gray-300 rounded bg-white h-11 px-0 text-xs text-center font-bold">
                                ${getUnitSelectOptionsHtml('%')}
                            </select>
                        </div>
                    </div>
                    
                    <!-- REPS -->
                    <div class="w-28">
                         <div class="flex items-center">
                            <button type="button" onclick="decrementValue('${prefix}_reps_${index}')"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 w-8 flex justify-center items-center">-</button>
                            <input type="text" id="${prefix}_reps_${index}" name="${prefix}_reps_${index}" value="0" readonly
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-full text-sm">
                            <button type="button" onclick="incrementValue('${prefix}_reps_${index}')"
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

        window.addNewUniversalRow = function(prefix) {
             const container = document.getElementById(`${prefix}_rows_container`);
             if(!container) return;

             // Find last row to change button
             // We can assume format ${prefix}_row_{index}
             const rows = container.querySelectorAll(`.${prefix}-row`);
             if(rows.length > 0) {
                 const lastRow = rows[rows.length - 1];
                 const lastRowId = lastRow.id; 
                 const parts = lastRowId.split('_');
                 const lastIdx = parseInt(parts[parts.length - 1]);
                 
                 // Update last row button to Remove
                 const actionCol = lastRow.querySelector('.action-col');
                 if(actionCol) {
                     actionCol.innerHTML = `
                        <button type="button" onclick="removeUniversalRow(${lastIdx}, '${prefix}')" class="text-red-500 hover:text-red-700 font-bold text-xl px-2 h-11 flex items-center justify-center min-w-[70px]">
                            &times;
                        </button>
                     `;
                 }
                 
                 // Add new row
                 const newIdx = lastIdx + 1;
                 container.insertAdjacentHTML('beforeend', getUniversalRowHtml(newIdx, true, prefix));
             } else {
                 // Should not happen if we always have 1 row, but just in case
                 container.insertAdjacentHTML('beforeend', getUniversalRowHtml(1, true, prefix));
             }
             
             // Update row numbers if we implement that
             updateUniversalRowNumbers(prefix);
        }

        window.removeUniversalRow = function(index, prefix) {
            const row = document.getElementById(`${prefix}_row_${index}`);
            if(row) row.remove();
            updateUniversalRowNumbers(prefix);
        }
        
        window.updateUniversalRowNumbers = function(prefix) {
             const container = document.getElementById(`${prefix}_rows_container`);
             if(!container) return;
             const rows = container.querySelectorAll(`.${prefix}-row`);
             rows.forEach((row, idx) => {
                 const numDiv = row.querySelector('.row-number');
                 if(numDiv) numDiv.textContent = idx + 1;
             });
        }


        // Universal Table Generator
        function getUniversalTableHtml(prefix) {
            let headerText = 'Exercises'; // Default
            if(prefix === 'round') headerText = 'Round';
            
            return `
                <!-- Header -->
                <div class="mb-1 text-base font-medium">${headerText}</div>
                
                <div class="flex gap-3 mb-2 px-2">
                    <div class="w-6"></div> <!-- Number col -->
                    <div class="flex-1 font-bold text-lg text-gray-700">Exercise <span class="text-red-500">*</span></div>
                    <div class="w-36 font-bold text-lg text-center text-gray-700">Training Load <span class="text-red-500">*</span></div>
                    <div class="w-28 font-bold text-lg text-center text-gray-700">REPS <span class="text-red-500">*</span></div>
                    <div class="min-w-[80px]"></div> <!-- Action col -->
                </div>

                <!-- Rows Container -->
                <div id="${prefix}_rows_container" class="space-y-3 max-h-96 overflow-y-auto pr-2 custom-scroll">
                    ${getUniversalRowHtml(1, true, prefix)}
                </div>
            `;
        }

        window.getUniversalRowHtml = function(index, isLast, prefix) {
             let btnHtml = '';
             if (isLast) {
                 btnHtml = `
                    <button type="button" onclick="addNewUniversalRow('${prefix}')" class="border border-black bg-white hover:bg-gray-50 text-black font-bold rounded px-0 py-2 flex items-center justify-center h-11 w-[80px] text-sm">
                        + Add
                    </button>
                 `;
             } else {
                 btnHtml = `
                    <button type="button" onclick="removeUniversalRow(${index}, '${prefix}')" class="border border-red-500 bg-red-500 hover:bg-red-600 text-white font-bold rounded px-0 py-2 flex items-center justify-center h-11 w-[80px] text-sm">
                        Remove
                    </button>
                 `;
             }

             return `
                <div class="flex items-center gap-3 ${prefix}-row" id="${prefix}_row_${index}">
                    <!-- Row Number -->
                    <div class="w-7 text-center font-bold text-gray-500 row-number text-sm">
                    </div>

                    <!-- Exercise -->
                    <div class="flex-1 min-w-0">
                        ${getSearchableDropdownHtml(`${prefix}_exercise_${index}`, '')}
                    </div>
                    
                    <!-- Training Load -->
                    <div class="w-32">
                        <div class="flex gap-1">
                            <input type="text" name="${prefix}_load_${index}" placeholder="80" class="w-2/3 border border-gray-300 rounded p-2.5 h-11 text-center text-sm">
                            <select name="${prefix}_unit_${index}" class="w-1/3 border border-gray-300 rounded bg-white h-11 px-0 text-xs text-center font-bold">
                                ${getUnitSelectOptionsHtml('%')}
                            </select>
                        </div>
                    </div>
                    
                    <!-- REPS -->
                    <div class="w-28">
                         <div class="flex items-center">
                            <button type="button" onclick="decrementValue('${prefix}_reps_${index}')"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 w-8 flex justify-center items-center">-</button>
                            <input type="text" id="${prefix}_reps_${index}" name="${prefix}_reps_${index}" value="0" readonly
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-full text-sm">
                            <button type="button" onclick="incrementValue('${prefix}_reps_${index}')"
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

        window.addNewUniversalRow = function(prefix) {
             const container = document.getElementById(`${prefix}_rows_container`);
             if(!container) return;

             // Find last row to change button
             // We can assume format ${prefix}_row_{index}
             const rows = container.querySelectorAll(`.${prefix}-row`);
             if(rows.length > 0) {
                 const lastRow = rows[rows.length - 1];
                 const lastRowId = lastRow.id; 
                 const parts = lastRowId.split('_');
                 const lastIdx = parseInt(parts[parts.length - 1]);
                 
                 // Update last row button to Remove
                 const actionCol = lastRow.querySelector('.action-col');
                 if(actionCol) {
                     actionCol.innerHTML = `
                        <button type="button" onclick="removeUniversalRow(${lastIdx}, '${prefix}')" class="border border-red-500 bg-red-500 hover:bg-red-600 text-white font-bold rounded px-0 py-2 flex items-center justify-center h-11 w-[80px] text-sm">
                            Remove
                        </button>
                     `;
                 }
                 
                 // Add new row
                 const newIdx = lastIdx + 1;
                 const html = getUniversalRowHtml(newIdx, true, prefix);
                 container.insertAdjacentHTML('beforeend', html);
                 
             } else {
                 container.insertAdjacentHTML('beforeend', getUniversalRowHtml(1, true, prefix));
             }
             
             updateUniversalRowNumbers(prefix);
        }

        window.removeUniversalRow = function(index, prefix) {
            const row = document.getElementById(`${prefix}_row_${index}`);
            if(row) row.remove();
            updateUniversalRowNumbers(prefix);
        }
        
        window.updateUniversalRowNumbers = function(prefix) {
             const container = document.getElementById(`${prefix}_rows_container`);
             if(!container) return;
             const rows = container.querySelectorAll(`.${prefix}-row`);
             rows.forEach((row, idx) => {
                 const numDiv = row.querySelector('.row-number');
                 if(numDiv) numDiv.textContent = idx + 1;
             });
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

                    ${getUniversalTableHtml('round')}
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

                    ${getUniversalTableHtml('amrap')}
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

                    ${getUniversalTableHtml('ft')}
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

                    <div id="interval_blocks_container" class="space-y-6 max-h-96 overflow-y-auto pr-2 custom-scroll">
                        ${getIntervalBlockHtml(1)}
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

                    <div id="pyramid_rows_container" class="space-y-3 max-h-96 overflow-y-auto pr-2 custom-scroll">
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
                 ${getSearchableDropdownHtml('pyramid_exercise', '')}
            ` : `<div class="h-11"></div>`;

            return `
                <div class="pyramid-row flex gap-4 items-center" id="pyramid_row_${index}">
                    <!-- Exercise Column -->
                    <div class="flex-1">
                        ${exerciseHtml}
                    </div>

                    <!-- Training Load Column -->
                    <div class="w-32 flex gap-1 justify-center">
                        <input type="text" name="pyramid_load_${index}" placeholder="80" class="w-16 border border-gray-300 rounded-s p-2.5 h-11 text-center text-sm font-bold">
                        <select name="pyramid_unit_${index}" class="bg-gray-100 border border-l-0 border-gray-300 rounded-e px-1 h-11 text-center text-sm font-bold w-14">
                            ${getUnitSelectOptionsHtml('%')}
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

                    <div id="emom_rows_container" class="space-y-3 max-h-96 overflow-y-auto pr-2 custom-scroll">
                        ${getEmomRowHtml(1)}
                    </div>
                </div>
            `;
            container.innerHTML = html;
        }

        // --- Straight Set State Management & Helpers ---

        window.collectStraightSetData = function() {
            const data = {};
            const container = document.getElementById('straight_set_rows_container');
            if(!container) return data;

            // Find all set rows
            const rows = container.querySelectorAll('[id^="ss_row_"]');
            rows.forEach(row => {
                const rowId = row.id;
                const setIndex = rowId.replace('ss_row_', ''); // e.g. "1", "2"
                data[setIndex] = {};
                
                // Check for single view inputs (legacy names)
                const singleReps = document.getElementById(`ss_reps_${setIndex}`);
                const singleLoad = document.querySelector(`[name="ss_load_${setIndex}"]`);
                const singleUnit = document.querySelector(`[name="ss_unit_${setIndex}"]`);

                if (singleReps && singleLoad) {
                    // Start at index 1 for the first exercise
                    data[setIndex][1] = {
                        reps: singleReps.value || singleReps.getAttribute('placeholder'),
                        load: singleLoad.value || singleLoad.getAttribute('placeholder'),
                        unit: singleUnit ? singleUnit.value : '%'
                    };
                }

                // Check for super set view inputs (ss_reps_{set}_{ex})
                // We iterate until we stop finding inputs
                let exIndex = 1;
                while(true) {
                    const reps = document.getElementById(`ss_reps_${setIndex}_${exIndex}`);
                    const load = document.querySelector(`[name="ss_load_${setIndex}_${exIndex}"]`);
                    const unit = document.querySelector(`[name="ss_unit_${setIndex}_${exIndex}"]`);

                    if (reps && load) {
                        data[setIndex][exIndex] = {
                            reps: reps.value || reps.getAttribute('placeholder'),
                            load: load.value || load.getAttribute('placeholder'),
                            unit: unit ? unit.value : '%'
                        };
                        exIndex++;
                    } else {
                        
                        if (exIndex === 1 && data[setIndex][1]) {
                             
                             break;
                        }
                        break;
                    }
                }
            });
            return data;
        };

        window.restoreStraightSetData = function(data) {
            if (!data) return;
            Object.keys(data).forEach(setIndex => {
                const exercises = data[setIndex];
                Object.keys(exercises).forEach(exIndex => {
                    const exData = exercises[exIndex];
                    
                    // Try targeting super set input first (preferred new standard)
                    let reps = document.getElementById(`ss_reps_${setIndex}_${exIndex}`);
                    let load = document.querySelector(`[name="ss_load_${setIndex}_${exIndex}"]`);
                    let unit = document.querySelector(`[name="ss_unit_${setIndex}_${exIndex}"]`);

                    // If not found and exIndex is 1, try single view (legacy) target
                    if ((!reps || !load) && exIndex == 1) {
                         reps = document.getElementById(`ss_reps_${setIndex}`);
                         load = document.querySelector(`[name="ss_load_${setIndex}"]`);
                         unit = document.querySelector(`[name="ss_unit_${setIndex}"]`);
                    }

                    if (reps) reps.value = exData.reps;
                    if (load) load.value = exData.load;
                    if (unit) unit.value = exData.unit;
                });
            });
        };

        window.getStraightSetExerciseIds = function() {
            const ids = [1]; // Main exercise always exists
            const container = document.getElementById('ss_supersets_container');
            if(container) {
                const rows = container.children;
                for(let i=0; i<rows.length; i++) {
                    const id = rows[i].id; // ss_superset_row_X
                    if(id.startsWith('ss_superset_row_')) {
                        ids.push(parseInt(id.replace('ss_superset_row_', '')));
                    }
                }
            }
            return ids;
        };

        window.getExerciseName = function(index) {
             // Main Row Exercise (index 1) or Super Set Row (index > 1)
             const selectName = index == 1 ? 'ss_exercise_1' : `ss_exercise_${index}`;
             
             // 1. Try standard select
             const select = document.querySelector(`[name="${selectName}"]`);
             if (select) {
                 if (select.tagName === 'SELECT' && select.selectedIndex >= 0) {
                     const text = select.options[select.selectedIndex].text;
                     if (text && !text.includes('Select Exercise')) return text;
                 }
                 // If it's an input (searchable), check value
                 if (select.tagName === 'INPUT' && select.value) {
                     return select.value;
                 }
             }

             
             
             return `Exercise ${index}`;
        };



        window.removeStraightSetRow = function(id) {
            const row = document.getElementById(id);
            if(!row) return;

            // 1. Collect Data BEFORE removing
            const currentData = window.collectStraightSetData();
            
            // 2. Identify Index to Remove
            const indexToRemove = id.replace('ss_row_', '');
            
            const newData = {};
            let newIndex = 1;
            
            const keys = Object.keys(currentData).sort((a,b) => a-b);
            
            keys.forEach(key => {
                if (key !== indexToRemove) {
                    // Move data from 'key' to 'newIndex'
                    newData[newIndex] = currentData[key];
                    newIndex++;
                }
            });

            // 4. Refresh UI with New Data
            window.refreshStraightSetRows(newData);
        }

        // Remove only specific exercise data from a SET row
        window.removeExerciseFromSet = function(setIndex, exerciseId) {
            // Get the SET row
            const row = document.getElementById(`ss_row_${setIndex}`);
            if (!row) return;

            // Get exercise IDs to check if this is the last exercise
            const exerciseIds = window.getStraightSetExerciseIds();
            
            // If this is the only exercise left, remove the entire row
            if (exerciseIds.length === 1) {
                window.removeStraightSetRow(`ss_row_${setIndex}`);
                return;
            }

            // Find the specific exercise div within the SET row
            const suffix = `_${exerciseId}`;
            
            // Find the exercise container div (the one with border-b or no border)
            // It contains the reps input with id ss_reps_{setIndex}_{exerciseId}
            const repsInput = document.getElementById(`ss_reps_${setIndex}${suffix}`);
            if (!repsInput) return;
            
            // Find the main container that holds this exercise's row
            // It should be the div with class containing "flex items-center gap-2 py-2 px-3"
            let exerciseContainer = repsInput.closest('.flex.items-center.gap-2');
            
            
            const actionColumn = row.querySelector('.w-\\[140px\\].flex.flex-col');
            if (actionColumn && exerciseContainer) {
                // Get all exercise containers and all remove buttons
                const contentColumn = row.querySelector('.flex-1.flex.flex-col');
                if (contentColumn) {
                    const allExerciseContainers = Array.from(contentColumn.querySelectorAll('.flex.items-center.gap-2'));
                    const allRemoveButtons = Array.from(actionColumn.querySelectorAll('.flex.items-center'));
                    
                    // Find the index of this exercise container
                    const exerciseIndex = allExerciseContainers.indexOf(exerciseContainer);
                    
                    if (exerciseIndex !== -1 && exerciseIndex < allRemoveButtons.length) {
                        const removeButtonContainer = allRemoveButtons[exerciseIndex];
                        
                        // Remove both the exercise row and its Remove button
                        exerciseContainer.remove();
                        removeButtonContainer.remove();

                        const remainingExercises = contentColumn.querySelectorAll('.flex.items-center.gap-2');
                        if (remainingExercises.length === 0) {
                           
                             window.removeStraightSetRow(`ss_row_${setIndex}`);
                        }
                    }
                }
            }
        }

        // --- Main Logic ---

        window.renderStraightSetUI = function() {
            const setsCounterHtml = `
                <div class="flex items-center mb-4 border-b pb-2 mt-2">
                    <label class="w-60 block text-base font-semibold text-black">Number of Sets</label>
                    <div class="flex items-center">
                        <button type="button" onclick="updateStraightSetCount(-1)" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 w-10 flex justify-center items-center font-bold text-lg">-</button>
                        <input type="text" id="num_sets" name="num_sets" value="1" readonly class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-16 text-base font-medium">
                        <button type="button" onclick="updateStraightSetCount(1)" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 w-10 flex justify-center items-center font-bold text-lg">+</button>
                    </div>
                </div>
            `;

            // Header HTML
            const headersHtml = `
                <div class="flex gap-2 mb-1 pl-2 pr-6 items-end">
                    <div class="w-[14rem] lg:w-[13rem] font-bold text-sm text-gray-700">Exercise <span class="text-red-500">*</span></div>
                    <div class="w-32 font-bold text-sm text-center text-gray-700">Training Load <span class="text-red-500">*</span></div>
                    <div class="w-32 font-bold text-sm text-center text-gray-700">REPS <span class="text-red-500">*</span></div>
                    <div class="w-[140px] pr-0 ml-auto"></div>
                </div>
            `;

            // Main Exercise Row (Static First Row)
            const mainRowHtml = `
                <div class="flex gap-2 mb-3 pl-2 pr-6 items-center">
                    <div class="w-[14rem] lg:w-[19rem]">
                         ${getSearchableDropdownHtml('ss_exercise_1', '')}
                    </div>
                    <div class="w-32 text-center">
                         <div class="flex gap-1 justify-center">
                            <input type="text" name="ss_load_1" placeholder="80" class="w-16 border border-gray-300 rounded-s p-2.5 h-11 text-center text-sm font-bold">
                            <select name="ss_unit_1" class="bg-gray-100 border border-l-0 border-gray-300 rounded-e px-1 h-11 text-center text-sm font-bold w-12">
                                ${getUnitSelectOptionsHtml('%')}
                            </select>
                         </div>
                    </div>
                    <div class="w-32 text-center">
                         <div class="flex items-center justify-center">
                            <button type="button" onclick="decrementValue('ss_reps_main')" class="bg-gray-100 border border-gray-300 rounded-s-lg p-3 h-11 w-10 flex justify-center items-center font-bold text-lg">-</button>
                            <input type="text" id="ss_reps_main" name="ss_reps_main" placeholder="6" readonly class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-12 text-sm font-bold">
                            <button type="button" onclick="incrementValue('ss_reps_main')" class="bg-gray-100 border border-gray-300 rounded-e-lg p-3 h-11 w-10 flex justify-center items-center font-bold text-lg">+</button>
                        </div>
                    </div>
                    <div class="w-[140px] flex justify-end pb-0 pr-0 ml-auto">
                          <button type="button" onclick="addSuperSet()" class="border border-black bg-white hover:bg-gray-50 text-black rounded px-2 flex items-center justify-center h-11 text-sm shadow-sm transition-all whitespace-nowrap w-[100px]">
                               <span class="font-bold">+ Add</span>&nbsp;Super Set
                          </button>
                    </div>
                </div>
            `;

            const html = `
                <div id="straight_set_container">
                    ${setsCounterHtml}
                    <div class="mb-3 border-b pb-4">
                        ${headersHtml}
                        ${mainRowHtml}
                        <div id="ss_supersets_container"></div>
                    </div>

                     <div class="flex">
                          <!-- Left Label -->
                          <div class="w-16 flex justify-center items-start pt-3 font-bold text-gray-800 text-base mr-4">SET <span class="text-red-500">*</span></div>
                          
                          <!-- Right List -->
                          <div class="flex-1">
                              <div id="straight_set_rows_container" class="overflow-y-auto max-h-96 pt-1 custom-scroll pb-1">
                                 <!-- Rows generated by refreshStraightSetRows -->
                              </div>
                          </div>
                     </div>
                 </div>
            `;
            container.innerHTML = html;
            window.superSetCount = 0; // Reset count
            
            // Generate Initial Rows based on Counter (4)
            window.refreshStraightSetRows();

            // ADDED: Live Sync Listeners for Top Inputs
            const topContainer = document.getElementById('straight_set_container');
            if(topContainer) {
                topContainer.addEventListener('input', function(e) { handleStraightSetSync(e); });
                topContainer.addEventListener('click', function(e) { handleStraightSetSync(e); }); // For +/- buttons
            }
        }

        window.handleStraightSetSync = function(e) {
            const target = e.target;
            let name = target.getAttribute('name') || target.id;
            
            
            if (!name) return;

            
            let exIndex = null;
            let type = null; // 'load', 'unit', 'reps'

            if (name === 'ss_reps_main') {
                exIndex = 1; type = 'reps';
            } else if (name.startsWith('ss_load_')) {
               
            }
            
            
            // Helper to update all bottom
            const updateBottom = (idx, field, value) => {
                 const container = document.getElementById('straight_set_rows_container');
                 if(!container) return;
                 const rows = container.querySelectorAll('[id^="ss_row_"]');
                 
                 rows.forEach(row => {
                      const rowId = row.id; 
                      const setNum = rowId.replace('ss_row_', ''); // 1, 2, 3...
                      
                      let targetInput = null;
                      
                      // Try Super Set Pattern First: ss_{field}_{setNum}_{idx}
                      let suffix = `_${idx}`;
                      let targetId = `ss_${field}_${setNum}${suffix}`;
                      let targetName = `ss_${field}_${setNum}${suffix}`;
                      
                      if (field === 'reps') {
                          targetInput = document.getElementById(targetId);
                      } else {
                          targetInput = row.querySelector(`[name="${targetName}"]`);
                      }
                      
                      // If NOT found, try Single View Pattern (only if idx===1)
                      if (!targetInput && idx === 1) {
                           targetId = `ss_${field}_${setNum}`;
                           targetName = `ss_${field}_${setNum}`;
                           if (field === 'reps') {
                               targetInput = document.getElementById(targetId);
                           } else {
                               targetInput = row.querySelector(`[name="${targetName}"]`);
                           }
                      }
                      
                      if (targetInput) {
                          targetInput.value = value;
                      }
                 });
            };
            
           
            // Top elements are NOT inside `straight_set_rows_container`.
            if (target.closest('#straight_set_rows_container')) return; // Ignore bottom changes
            
          
            if (target.id === 'ss_reps_main' || target.name === 'ss_reps_main') {
                 updateBottom(1, 'reps', target.value);
                 return;
            }
            // Super: ss_reps_{k}
            if (target.id && target.id.startsWith('ss_reps_')) {
                 const part = target.id.replace('ss_reps_', '');
                 // part is integer k?
                 if (!part.includes('_')) {
                      updateBottom(parseInt(part), 'reps', target.value);
                      return;
                 }
            }

           
            if (target.name && target.name.startsWith('ss_load_')) {
                 const part = target.name.replace('ss_load_', '');
                 if (!part.includes('_')) { // Ensure it's not some other combo
                      updateBottom(parseInt(part), 'load', target.value);
                      return;
                 }
            }

            // 3. Unit
             if (target.name && target.name.startsWith('ss_unit_')) {
                 const part = target.name.replace('ss_unit_', '');
                 if (!part.includes('_')) { 
                      updateBottom(parseInt(part), 'unit', target.value);
                      return;
                 }
            }
        }

        // Global Option Select Handler (called by modified selectOption)
        window.onGlobalSelectOption = function(id, value) {
             
             if (id.startsWith('ss_exercise_')) {
                 const el = document.getElementById(`input_${id}`);
                 if (el && !el.closest('#straight_set_rows_container')) {
                      
                      
                      window.refreshStraightSetRows();
                 }
             }
        }


        window.updateStraightSetCount = function(delta) {
            const input = document.getElementById('num_sets');
            if(!input) return;
            let val = parseInt(input.value) || 0;
            val += delta;
            if(val < 1) val = 1; // Minimum 1 set
            input.value = val;
            window.refreshStraightSetRows();
        }

        window.addSuperSet = function() {
            // 1. Add Super Set Row (Top)
            window.superSetCount++;
            const index = window.superSetCount + 1; // 1 is main, so start at 2
            const container = document.getElementById('ss_supersets_container');
            
            const rowHtml = `
                <div class="flex gap-2 mb-3 pl-2 pr-6 items-center bg-gray-50 p-2 rounded relative" id="ss_superset_row_${index}">
                    <div class="w-[12rem] lg:w-[12rem]">
                         ${getSearchableDropdownHtml(`ss_exercise_${index}`, '')}
                    </div>
                    <div class="w-32 text-center">
                         <div class="flex gap-1 justify-center">
                            <input type="text" name="ss_load_${index}" placeholder="60" class="w-16 border border-gray-300 rounded-s p-2.5 h-11 text-center text-sm font-bold">
                            <select name="ss_unit_${index}" class="bg-gray-100 border border-l-0 border-gray-300 rounded-e px-1 h-11 text-center text-sm font-bold w-12">
                                ${getUnitSelectOptionsHtml('kg')}
                            </select>
                         </div>
                    </div>
                    <div class="w-32 text-center">
                         <div class="flex items-center justify-center">
                            <button type="button" onclick="decrementValue('ss_reps_${index}')" class="bg-gray-100 border border-gray-300 rounded-s-lg p-3 h-11 w-10 flex justify-center items-center font-bold text-lg">-</button>
                            <input type="text" id="ss_reps_${index}" name="ss_reps_${index}" placeholder="6" readonly class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-12 text-sm font-bold">
                            <button type="button" onclick="incrementValue('ss_reps_${index}')" class="bg-gray-100 border border-gray-300 rounded-e-lg p-3 h-11 w-10 flex justify-center items-center font-bold text-lg">+</button>
                        </div>
                    </div>
                    <div class="w-[140px] flex justify-end pb-0 pr-0 ml-auto">
                         <button type="button" onclick="removeSuperSet(${index})" class="text-red-500 hover:text-red-700 font-bold text-sm px-2 h-11 flex items-center justify-center w-[100px]">Remove</button>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', rowHtml);
            
            // 2. Trigger Refresh of Bottom Rows to match new Structure
            window.refreshStraightSetRows();
        }
        
        window.removeSuperSet = function(index) {
             const row = document.getElementById(`ss_superset_row_${index}`);
             if(row) {
                 row.remove();
                 window.superSetCount--; 
                 window.refreshStraightSetRows();
             }
        }

        window.refreshStraightSetRows = function(dataOverride = null) {
            const container = document.getElementById('straight_set_rows_container');
            if(!container) return;
            
            let countInput = document.getElementById('num_sets');
            let targetCount = 1;
            
            // 1. Determine Data and Count
            let currentData = null;
            
            if (dataOverride) {
                currentData = dataOverride;
                targetCount = Object.keys(currentData).length;
                // Update Input to match new data count
                if (countInput) countInput.value = targetCount;
            } else {
                currentData = window.collectStraightSetData();
                targetCount = countInput ? (parseInt(countInput.value) || 1) : 1;
            }
            
            // 2. Clear Container (Force Re-render)
            container.innerHTML = '';
            
            // 3. Re-render Layout
            for(let i = 1; i <= targetCount; i++) {
                container.insertAdjacentHTML('beforeend', getStraightSetRowHtml(i));
            }
            
            // 4. Restore Data
            window.restoreStraightSetData(currentData);
        }

        window.getStraightSetRowHtml = function(index, isLast) {
            const exerciseIds = window.getStraightSetExerciseIds();
            const hasSuperSets = exerciseIds.length > 1;
            
            if (!hasSuperSets) {
                 // Standard View (Single Line)
                 return `
                    <div class="flex items-center gap-0 py-1" id="ss_row_${index}">
                         <!-- Content Part -->
                         <div class="flex-1 flex items-center gap-4">
                             <div class="w-8 text-center font-bold text-gray-600 text-base">${index}</div>
                             
                             <!-- Reps Control -->
                             <div class="flex items-center gap-1">
                                  <button type="button" onclick="decrementValue('ss_reps_${index}')" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-l-md rounded-r-none h-10 w-10 flex justify-center items-center font-bold text-xl text-gray-700">-</button>
                                  <input type="text" id="ss_reps_${index}" name="ss_reps_${index}" placeholder="${document.getElementById('ss_reps_main')?.value || 6}" class="bg-transparent border-none h-10 w-12 text-center text-sm font-bold focus:ring-0 text-gray-800">
                                  <button type="button" onclick="incrementValue('ss_reps_${index}')" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-r-md rounded-l-none h-10 w-10 flex justify-center items-center font-bold text-xl text-gray-700">+</button>
                             </div>
                             
                             <!-- Load Display -->
                             <div class="flex items-center gap-1">
                                 <input type="text" name="ss_load_${index}" value="${document.querySelector('[name=ss_load_1]')?.value || 80}" class="border border-gray-300 rounded p-2 h-10 w-14 text-center text-sm font-bold">
                                 <select name="ss_unit_${index}" class="border border-gray-300 rounded h-10 w-12 text-center text-sm font-bold bg-white">
                                     ${getUnitSelectOptionsHtml(document.querySelector('[name=ss_unit_1]')?.value || '%')}
                                 </select>
                             </div>
                         </div>
                    </div>
                 `;
            } else {
                 // Super Set View (Boxed)
                 let contentHtml = '';
                 let actionButtonsHtml = '';
                 
                 // Generate a row for EACH exercise based on actual IDs
                 exerciseIds.forEach((exId, arrayIndex) => {
                     // Get Exercise Name from Top
                     const exName = window.getExerciseName(exId);
                     const suffix = `_${exId}`;
                     const isLastLine = arrayIndex === exerciseIds.length - 1;
                     
                     // Get Defaults from Top
                     let defaultReps = '6';
                     let defaultLoad = '80';
                     let defaultUnit = '%';
                     
                     if (exId === 1) {
                         const r = document.getElementById('ss_reps_main');
                         const l = document.querySelector('[name="ss_load_1"]');
                         const u = document.querySelector('[name="ss_unit_1"]');
                         if(r) defaultReps = r.value;
                         if(l) defaultLoad = l.value;
                         if(u) defaultUnit = u.value;
                     } else {
                         const r = document.getElementById(`ss_reps_${exId}`);
                         const l = document.querySelector(`[name="ss_load_${exId}"]`);
                         const u = document.querySelector(`[name="ss_unit_${exId}"]`);
                         if(r) defaultReps = r.value;
                         if(l) defaultLoad = l.value;
                         if(u) defaultUnit = u.value;
                     }

                     contentHtml += `
                         <div class="flex items-center gap-2 py-2 px-3 ${!isLastLine ? 'border-b border-gray-200' : ''}">
                             <!-- Exercise Name -->
                             <div class="flex-1 min-w-0 font-bold text-sm text-gray-800 truncate" title="${exName}">${exName}</div>
                             
                             <!-- Controls Container -->
                             <div class="flex items-center ml-auto gap-3">
                                  <!-- Reps -->
                                  <div class="flex items-center">
                                      <button type="button" onclick="decrementValue('ss_reps_${index}${suffix}')" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s p-1 h-8 w-6 flex justify-center items-center font-bold text-lg">-</button>
                                      <input type="text" id="ss_reps_${index}${suffix}" name="ss_reps_${index}${suffix}" value="${defaultReps}" placeholder="6" class="border-y border-gray-300 h-8 w-10 text-center text-xs font-bold text-black placeholder-gray-400">
                                      <button type="button" onclick="incrementValue('ss_reps_${index}${suffix}')" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e p-1 h-8 w-6 flex justify-center items-center font-bold text-lg">+</button>
                                  </div>
                                  
                                  <div class="text-[10px] font-bold text-gray-500 uppercase">REPS</div>
                                  
                                  <!-- Load -->
                                  <div class="flex items-center gap-1">
                                      <div class="relative">
                                          <input type="text" name="ss_load_${index}${suffix}" value="${defaultLoad}" placeholder="80" class="border border-gray-300 rounded p-1 h-8 w-14 text-center text-xs font-bold text-black placeholder-gray-400">
                                      </div>
                                      <div class="relative">
                                          <select name="ss_unit_${index}${suffix}" class="border border-gray-300 rounded h-8 w-12 text-center text-[10px] font-bold bg-white focus:outline-none px-0 text-black">
                                              ${getUnitSelectOptionsHtml(defaultUnit)}
                                          </select>
                                      </div>
                                  </div>
                             </div>
                         </div>
                     `;

                     // Button Logic
                     if (index == 1 && arrayIndex === 0) {
                         actionButtonsHtml += `
                             <div class="flex items-center h-[50px]"> 
                             </div>
                         `;
                     } else {
                         actionButtonsHtml += `
                             <div class="flex items-center h-[50px]"> 
                                 <button type="button" onclick="removeExerciseFromSet(${index}, ${exId})" class="bg-red-500 hover:bg-red-600 text-white font-bold rounded px-4 h-10 text-sm w-[100px]">
                                      Remove
                                 </button>
                             </div>
                         `;
                     }
                 });
                 
                 let borderClass = 'border border-gray-400';
                 if (index > 1) {
                      if (index % 2 == 0) {
                          borderClass = 'border-l border-b border-gray-400'; // No Right
                      } else {
                          borderClass = 'border-x border-b border-gray-400'; // Has Right
                      }
                 }

                 return `
                    <div class="flex items-start" id="ss_row_${index}">
                        <!-- Table Part (Index + Content) -->
                        <div class="ss-table-part flex-1 flex ${borderClass} bg-white mr-2">
                            <!-- Index Column -->
                            <div class="w-8 flex items-center justify-center border-r border-gray-400 font-bold text-gray-700 bg-gray-50 text-base">
                                ${index}
                            </div>

                            <!-- Content Column -->
                            <div class="flex-1 flex flex-col justify-center">
                                ${contentHtml}
                            </div>
                        </div>

                        <!-- Action Column (Outside Table) -->
                        <div class="w-[140px] flex flex-col">
                             ${actionButtonsHtml}
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
                        ${getSearchableDropdownHtml(`emom_exercise_${index}`, '')}
                    </div>
                    
                    <!-- Training Load -->
                    <div class="w-32">
                        <div class="flex gap-1">
                            <input type="text" name="emom_load_${index}" placeholder="80" class="w-2/3 border border-gray-300 rounded p-2.5 h-11 text-center text-sm">
                            <select name="emom_unit_${index}" class="w-1/3 border border-gray-300 rounded bg-white h-11 px-0 text-xs text-center font-bold">
                                ${getUnitSelectOptionsHtml('%')}
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

                    <div id="circuit_stations_container" class="space-y-6 max-h-96 overflow-y-auto pr-2 custom-scroll">
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
                         <div class="flex-1 font-bold text-sm text-gray-700">Exercise <span class="text-red-500">*</span></div>
                         <div class="w-32 font-bold text-sm text-center text-gray-700">Training Load <span class="text-red-500">*</span></div>
                         <div class="w-28 font-bold text-sm text-center text-gray-700">REPS <span class="text-red-500">*</span></div>
                         <div class="min-w-[80px]"></div>
                    </div>
                    
                    <div id="station_${index}_rows" class="space-y-3 max-h-96 overflow-y-auto pr-2 custom-scroll">
                         ${getCircuitRowHtml(index, 1, true)}
                    </div>
                </div>
            `;
        }

        window.getCircuitRowHtml = function(stationIndex, rowIndex, isLast) {
             let btnHtml = '';
             if (isLast) {
                 btnHtml = `
                    <button type="button" onclick="addStationRow(${stationIndex})" class="border border-black bg-white hover:bg-gray-50 text-black font-bold rounded px-0 py-2 flex items-center justify-center h-11 w-[80px] text-sm">
                        + Add
                    </button>
                 `;
             } else {
                 btnHtml = `
                    <button type="button" onclick="removeStationRow(${stationIndex}, ${rowIndex})" class="border border-red-500 bg-red-500 hover:bg-red-600 text-white font-bold rounded px-0 py-2 flex items-center justify-center h-11 w-[80px] text-sm">
                        Remove
                    </button>
                 `;
             }

             return `
                <div class="flex items-center gap-3" id="station_${stationIndex}_row_${rowIndex}">
                    <!-- Exercise -->
                    <div class="flex-1 min-w-0">
                        ${getSearchableDropdownHtml(`station_${stationIndex}_exercise_${rowIndex}`, '')}
                    </div>
                    
                    <!-- Training Load -->
                    <div class="w-32">
                        <div class="flex gap-1">
                            <input type="text" name="station_${stationIndex}_load_${rowIndex}" placeholder="80" class="w-2/3 border border-gray-300 rounded p-2.5 h-11 text-center text-sm">
                            <select name="station_${stationIndex}_unit_${rowIndex}" class="w-1/3 border border-gray-300 rounded bg-white h-11 px-0 text-xs text-center font-bold">
                                ${getUnitSelectOptionsHtml('%')}
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
                const btnContainer = lastRow.querySelector('.min-w-\\[80px\\]') || lastRow.lastElementChild;
                if(btnContainer) {
                    btnContainer.innerHTML = `
                    <button type="button" onclick="removeStationRow(${stationIndex}, ${lastIdx})" class="border border-red-500 bg-red-500 hover:bg-red-600 text-white font-bold rounded px-0 py-2 flex items-center justify-center h-11 w-[80px] text-sm">
                        Remove
                    </button>
                    `;
                }
            }

            container.insertAdjacentHTML('beforeend', getCircuitRowHtml(stationIndex, rowIndex, true));
        }

        window.removeStationRow = function(stationIndex, rowIndex) {
            const row = document.getElementById(`station_${stationIndex}_row_${rowIndex}`);
            if (row) row.remove();
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
                 updateIntervalBlocks(val);
             }
        }
        
        window.decrementIntervalCount = function() {
             const el = document.getElementById('num_intervals');
             if(el && el.value > 1) {
                 let val = parseInt(el.value) - 1;
                 el.value = val;
                 updateIntervalBlocks(val);
             }
        }

        window.updateIntervalBlocks = function(count) {
             const container = document.getElementById('interval_blocks_container');
             if(!container) return;
             const currentBlocks = container.children.length;
             
             if (count > currentBlocks) {
                 for(let i = currentBlocks + 1; i <= count; i++) {
                     container.insertAdjacentHTML('beforeend', getIntervalBlockHtml(i));
                 }
             } else if (count < currentBlocks) {
                 for(let i = currentBlocks; i > count; i--) {
                     if(container.lastElementChild) container.lastElementChild.remove();
                 }
             }
        }

        window.getIntervalBlockHtml = function(index) {
            return `
                <div class="interval-block" id="interval_block_${index}">
                    <div class="font-bold text-sm mb-2 text-gray-800">Interval ${index}</div>
                    
                    <!-- Headers -->
                    <div class="flex gap-3 mb-2 px-2 items-center">
                         <div class="flex-1 font-bold text-sm text-gray-700">Exercise <span class="text-red-500">*</span></div>
                         <div class="w-24 font-bold text-sm text-center text-gray-700">Training Load <span class="text-red-500">*</span></div>
                         <div class="w-28 font-bold text-sm text-center text-gray-700">Work</div>
                         <div class="w-28 font-bold text-sm text-center text-gray-700">Rest</div>
                         <div class="min-w-[80px]"></div>
                    </div>
                    
                    <div id="interval_block_${index}_rows" class="space-y-3 max-h-96 overflow-y-auto pr-2 custom-scroll">
                         ${getIntervalRowHtml(index, 1, true)}
                    </div>
                </div>
            `;
        }

        window.getIntervalRowHtml = function(intervalIndex, rowIndex, isLast) {
             let btnHtml = '';
             if (isLast) {
                 btnHtml = `
                    <button type="button" onclick="addIntervalRow(${intervalIndex})" class="border border-black bg-white hover:bg-gray-50 text-black font-bold rounded px-0 py-2 flex items-center justify-center h-11 w-[80px] text-sm">
                        + Add
                    </button>
                 `;
             } else {
                 btnHtml = `
                    <button type="button" onclick="removeIntervalRow(${intervalIndex}, ${rowIndex})" class="border border-red-500 bg-red-500 hover:bg-red-600 text-white font-bold rounded px-0 py-2 flex items-center justify-center h-11 w-[80px] text-sm">
                        Remove
                    </button>
                 `;
             }

             return `
                <div class="flex items-center gap-3" id="interval_${intervalIndex}_row_${rowIndex}">
                    <!-- Exercise -->
                    <div class="flex-1 min-w-0">
                        ${getSearchableDropdownHtml(`interval_${intervalIndex}_exercise_${rowIndex}`, '')}
                    </div>
                    
                    <!-- Training Load -->
                    <div class="w-24">
                        <div class="flex gap-1">
                            <input type="text" name="interval_${intervalIndex}_load_${rowIndex}" placeholder="80" class="w-2/3 border border-gray-300 rounded p-2.5 h-11 text-center text-sm">
                            <select name="interval_${intervalIndex}_unit_${rowIndex}" class="w-1/3 border border-gray-300 rounded bg-white h-11 px-0 text-xs text-center font-bold">
                                ${getUnitSelectOptionsHtml('%')}
                            </select>
                        </div>
                    </div>
                    
                    <!-- Work/Rest -->
                    <div class="flex gap-2">
                         <!-- Work -->
                         <div class="flex items-center justify-center w-28">
                              <button type="button" onclick="decrementTime('interval_${intervalIndex}_work_${rowIndex}')" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 w-6 flex items-center justify-center">-</button>
                              <input type="text" id="interval_${intervalIndex}_work_${rowIndex}" name="interval_${intervalIndex}_work_${rowIndex}" value="00:00:00" placeholder="00:00:00" class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-16 text-sm font-medium text-gray-900 px-0">
                              <button type="button" onclick="incrementTime('interval_${intervalIndex}_work_${rowIndex}')" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 w-6 flex items-center justify-center">+</button>
                         </div>
                         <!-- Rest -->
                         <div class="flex items-center justify-center w-28">
                              <button type="button" onclick="decrementTime('interval_${intervalIndex}_rest_${rowIndex}')" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 w-6 flex items-center justify-center">-</button>
                              <input type="text" id="interval_${intervalIndex}_rest_${rowIndex}" name="interval_${intervalIndex}_rest_${rowIndex}" value="00:00:00" placeholder="00:00:00" class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center w-16 text-sm font-medium text-gray-900 px-0">
                              <button type="button" onclick="incrementTime('interval_${intervalIndex}_rest_${rowIndex}')" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 w-6 flex items-center justify-center">+</button>
                         </div>
                    </div>
                    
                    <!-- Action Button -->
                    <div class="h-11 flex items-center min-w-[80px] justify-center">
                        ${btnHtml}
                    </div>
                </div>
             `;
        }

        window.addIntervalRow = function(intervalIndex) {
            const container = document.getElementById(`interval_block_${intervalIndex}_rows`);
            if (!container) return;
            
            const rowIndex = Date.now();
            
            // Change last row button to Remove
            const rows = container.children;
            if (rows.length > 0) {
                 const lastRow = rows[rows.length - 1];
                 const lastRowId = lastRow.id; // interval_{idx}_row_{rowIndex}
                 const idParts = lastRowId.split('_');
                 const lastRowIdx = idParts[idParts.length - 1];
                 
                 const btnCol = lastRow.querySelector('.min-w-\\[80px\\]');
                 if (!btnCol) {
                     // Try another selector if min-w didn't work (h-11 flex items-center min-w-[80px] justify-center)
                     const cols = lastRow.querySelectorAll('div');
                     const lastCol = cols[cols.length - 1];
                     lastCol.innerHTML = `
                        <button type="button" onclick="removeIntervalRow(${intervalIndex}, ${lastRowIdx})" class="border border-red-500 bg-red-500 hover:bg-red-600 text-white font-bold rounded px-0 py-2 flex items-center justify-center h-11 w-[80px] text-sm">
                            Remove
                        </button>
                     `;
                 } else {
                     btnCol.innerHTML = `
                        <button type="button" onclick="removeIntervalRow(${intervalIndex}, ${lastRowIdx})" class="border border-red-500 bg-red-500 hover:bg-red-600 text-white font-bold rounded px-0 py-2 flex items-center justify-center h-11 w-[80px] text-sm">
                            Remove
                        </button>
                     `;
                 }
            }

            container.insertAdjacentHTML('beforeend', getIntervalRowHtml(intervalIndex, rowIndex, true));
        }

        window.removeIntervalRow = function(intervalIndex, rowIndex) {
            const row = document.getElementById(`interval_${intervalIndex}_row_${rowIndex}`);
            if(row) row.remove();
        }

        window.getRoundRowHtml = function(index, isLast) {
             let btnHtml = '';
             if (isLast) {
                 btnHtml = `
                    <button type="button" onclick="addNewRoundRow()" class="border border-black bg-white hover:bg-gray-50 text-black font-bold rounded px-0 py-2 flex items-center justify-center h-11 w-[50px] text-xs">
                        + Add
                    </button>
                 `;
             } else {
                 btnHtml = `
                    <button type="button" onclick="removeRoundRow(${index})" class="bg-red-500 hover:bg-red-600 text-white font-bold rounded px-0 h-11 text-xs w-[50px] flex items-center justify-center">
                        Remove
                    </button>
                 `;
             }

             return `
                <div class="flex items-center gap-3 round-row" id="round_row_${index}">
                    <!-- Row Number -->
                    <div class="w-7 text-center font-bold text-gray-500 row-number text-sm">
                    </div>

                    <!-- Exercise (Reduced Width) -->
                    <!-- Exercise (Reduced Width) -->
                    <div class="flex-1 min-w-0">
                        ${getSearchableDropdownHtml(`round_exercise_${index}`, '')}
                    </div>
                    
                    <!-- Training Load -->
                    <div class="w-32">
                        <div class="flex gap-1">
                            <input type="text" name="round_load_${index}" placeholder="80" class="w-2/3 border border-gray-300 rounded p-2.5 h-11 text-center text-sm">
                            <select name="round_unit_${index}" class="w-1/3 border border-gray-300 rounded bg-white h-11 px-0 text-xs text-center font-bold">
                                ${getUnitSelectOptionsHtml('%')}
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
                <button type="button" onclick="removeRoundRow(${lastIdx})" class="bg-red-500 hover:bg-red-600 text-white font-bold rounded px-0 h-11 text-xs w-[50px] flex items-center justify-center">
                    Remove
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
        if(el) {
            el.value = parseInt(el.value || 0) + 1;
            el.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }
    window.decrementValue = function(id) {
        const el = document.getElementById(id);
        if(el && el.value > 0) {
            el.value = parseInt(el.value) - 1;
            el.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    // --- Edit Mode Logic ---
    window.editUnifiedWorkout = function(id) {
        const workout = window.unifiedWorkoutsMap[id];
        if(!workout) return;

        console.log('Editing Workout:', workout);

        // 1. Populate Common
        const typeSelect = document.getElementById('common_type');
        const nameInput = document.getElementById('common_name');
        const formatSelect = document.getElementById('common_format');
        const hiddenId = document.getElementById('common_workout_id');
        
        // Set Hidden ID
        if(hiddenId) hiddenId.value = workout.id;

        // Change Form Action to Update Mode
        const form = document.getElementById('create_workout_form'); 
        if(form) {
            form.action = "{{ route('workout.update') }}"; // Make sure this route exists
        }
        
        // Update Submit Button & Title
        const submitBtn = document.getElementById('save_workout_btn');
        if(submitBtn) {
            submitBtn.innerText = "Update Workout";
        }
        
        const title = document.querySelector('.text-2xl.font-bold');
        if(title) title.innerText = "Edit Workout";

        if(typeSelect && workout.type) {
             typeSelect.value = workout.type.name; 
        }
        
        if(nameInput) nameInput.value = workout.workout_name;
        
        if(formatSelect && workout.format) {
            formatSelect.value = workout.format.name;
            // Trigger Change to render UI
            formatSelect.dispatchEvent(new Event('change'));
        }
        
        // Wait for UI render
        setTimeout(() => {
            const fmt = workout.format.name;
            
            if (fmt === 'Straight Sets') {
                 const straights = workout.straights || [];
                 if(straights.length > 0) {
                     const mainStraight = straights[0];
                     const secStraight = straights.length > 1 ? straights[1] : null;
                     
                     // Populate Top Input (Main)
                     window.selectOption('ss_exercise_1', mainStraight.workout_library.workout);
                     const l1 = document.querySelector('[name="ss_load_1"]'); if(l1) l1.value = mainStraight.training_load;
                     const u1 = document.querySelector('[name="ss_unit_1"]'); if(u1) u1.value = mainStraight.unit_type;
                     const r1 = document.getElementById('ss_reps_main'); if(r1) r1.value = mainStraight.reps;
                     
                     // If Super Set
                     if (secStraight) {
                         window.addSuperSet(); 
                         if(secStraight.workout_library) window.selectOption('ss_exercise_2', secStraight.workout_library.workout);
                         const l2 = document.querySelector('[name="ss_load_2"]'); if(l2) l2.value = secStraight.training_load;
                         const u2 = document.querySelector('[name="ss_unit_2"]'); if(u2) u2.value = secStraight.unit_type;
                         const r2 = document.getElementById('ss_reps_2'); if(r2) r2.value = secStraight.reps;
                     }
                     
                     // Child Sets
                     const sets = mainStraight.sets || [];
                     const numSets = sets.length; 
                     
                     if (numSets > 0) {
                         const setInput = document.getElementById('num_sets');
                         if(setInput) {
                              // IMPORTANT: `renderStraightSetUI` initializes a listener on `num_sets`? No.
                              // `updateStraightSetCount` updates the input and calls refresh.
                              // So we should update input AND call refresh?
                              // Actually `updateStraightSetCount` takes delta.
                              // We can just set value and call `refreshStraightSetRows`.
                              setInput.value = numSets; 
                              window.refreshStraightSetRows();
                         }
                         
                         const data = {};
                         // Main Sets
                         sets.forEach((set, idx) => {
                             const setNum = idx + 1;
                             if(!data[setNum]) data[setNum] = {};
                             data[setNum][1] = { reps: set.res, load: set.trainload, unit: set.unittype };
                         });
                         // Super Sets
                         if(secStraight && secStraight.sets) {
                             secStraight.sets.forEach((set, idx) => {
                                 const setNum = idx + 1;
                                 if(!data[setNum]) data[setNum] = {};
                                 data[setNum][2] = { reps: set.res, load: set.trainload, unit: set.unittype };
                             });
                         }
                         window.restoreStraightSetData(data);
                     }
                 }
                 
            } else if (fmt === 'Rounds') {
                const rounds = workout.rounds || [];
                if (workout.number) document.getElementById('num_rounds').value = workout.number;
                
                rounds.forEach((row, i) => {
                    const idx = i + 1;
                    if (idx > 1) window.addNewRoundRow();
                    
                    window.selectOption(`round_exercise_${idx}`, row.workout_library.workout);
                    const l = document.querySelector(`[name="round_load_${idx}"]`); if(l) l.value = row.training_load;
                    const u = document.querySelector(`[name="round_unit_${idx}"]`); if(u) u.value = row.unit_type;
                    const r = document.getElementById(`round_reps_${idx}`); if(r) r.value = row.reps;
                });
                
            } else if (fmt === 'AMRAP') {
                const amraps = workout.amraps || [];
                if(workout.number) {
                     let m = workout.number;
                     document.getElementById('time_to_complete').value = (m < 10 ? '0'+m : m) + ':00';
                }
                amraps.forEach((row, i) => {
                    const idx = i + 1;
                    if (idx > 1) window.addNewUniversalRow('amrap');
                    window.selectOption(`amrap_exercise_${idx}`, row.workout_library.workout);
                    const l = document.querySelector(`[name="amrap_load_${idx}"]`); if(l) l.value = row.training_load;
                    const u = document.querySelector(`[name="amrap_unit_${idx}"]`); if(u) u.value = row.unit_type;
                    const r = document.getElementById(`amrap_reps_${idx}`); if(r) r.value = row.reps;
                });

            } else if (fmt === 'EMOM') {
                const emoms = workout.emoms || [];
                if(workout.number) document.getElementById('num_minutes').value = workout.number;
                 emoms.forEach((row, i) => {
                    const idx = i + 1;
                    if (idx > 1) {
                         if (typeof window.addNewUniversalRow === 'function') window.addNewUniversalRow('emom');
                    }
                    window.selectOption(`emom_exercise_${idx}`, row.workout_library.workout);
                    const l = document.querySelector(`[name="emom_load_${idx}"]`); if(l) l.value = row.training_load;
                    const u = document.querySelector(`[name="emom_unit_${idx}"]`); if(u) u.value = row.unit_type;
                    const r = document.getElementById(`emom_reps_${idx}`); if(r) r.value = row.reps;
                });

            } else if (fmt === 'Pyramid') {
                 const pyramids = workout.pyramids || [];
                 if(workout.number) {
                     document.getElementById('num_layers').value = workout.number;
                     window.updatePyramidLayers(workout.number);
                 }
                 if(pyramids.length > 0) {
                     window.selectOption('pyramid_exercise', pyramids[0].workout_library.workout);
                 }
                 pyramids.forEach((row, i) => {
                     const idx = i + 1;
                     const l = document.querySelector(`[name="pyramid_load_${idx}"]`); if(l) l.value = row.training_load;
                     const u = document.querySelector(`[name="pyramid_unit_${idx}"]`); if(u) u.value = row.unit_type;
                     const r = document.getElementById(`pyramid_reps_${idx}`); if(r) r.value = row.reps;
                 });

            } else if (fmt === 'Intervals') {
                const intervals = workout.intervals || [];
                if(workout.number) {
                     document.getElementById('num_intervals').value = workout.number;
                     window.updateIntervalBlocks(workout.number);
                }
                const groups = {};
                intervals.forEach(inv => {
                    const num = inv.stationumber;
                    if(!groups[num]) groups[num] = [];
                    groups[num].push(inv);
                });
                
                Object.keys(groups).forEach(blockNum => {
                    const rows = groups[blockNum];
                    rows.forEach((row, i) => {
                        const rowIdx = i + 1;
                        if(rowIdx > 1) window.addIntervalRow(blockNum);
                        
                        setTimeout(() => {
                            const container = document.getElementById(`interval_block_${blockNum}_rows`);
                            if(container && container.children[i]) {
                                const domRow = container.children[i];
                                const parts = domRow.id.split('_');
                                const finalIdx = parts[parts.length - 1];
                                
                                window.selectOption(`interval_${blockNum}_exercise_${finalIdx}`, row.workout_library.workout);
                                const l = document.querySelector(`[name="interval_${blockNum}_load_${finalIdx}"]`); if(l) l.value = row.training_load;
                                const u = document.querySelector(`[name="interval_${blockNum}_unit_${finalIdx}"]`); if(u) u.value = row.unit_type;
                                
                                const w = document.getElementById(`interval_${blockNum}_work_${finalIdx}`); if(w) w.value = row.work;
                                const rest = document.getElementById(`interval_${blockNum}_rest_${finalIdx}`); if(rest) rest.value = row.rest;
                            }
                        }, 50); 
                    });
                });
                
            } else if (fmt === 'Circuit') {
                 const circuits = workout.circuits || [];
                 if(workout.number) {
                     document.getElementById('num_stations').value = workout.number;
                     window.updateCircuitStations(workout.number);
                 }
                 
                 const groups = {};
                 circuits.forEach(c => {
                     const num = c.stationumber;
                     if(!groups[num]) groups[num] = [];
                     groups[num].push(c);
                 });
                 
                 Object.keys(groups).forEach(stNum => {
                     const rows = groups[stNum];
                     rows.forEach((row, i) => {
                          if(i > 0) window.addStationRow(stNum);
                          
                          setTimeout(() => {
                              const container = document.getElementById(`station_${stNum}_rows`);
                              if(container && container.children[i]) {
                                  const domRow = container.children[i];
                                  const parts = domRow.id.split('_');
                                  const finalIdx = parts[parts.length - 1];
                                  
                                  window.selectOption(`station_${stNum}_exercise_${finalIdx}`, row.workout_library.workout);
                                  const l = document.querySelector(`[name="station_${stNum}_load_${finalIdx}"]`); if(l) l.value = row.training_load;
                                  const u = document.querySelector(`[name="station_${stNum}_unit_${finalIdx}"]`); if(u) u.value = row.unit_type;
                                  const r = document.getElementById(`station_${stNum}_reps_${finalIdx}`); if(r) r.value = row.reps;
                              }
                          }, 50);
                     });
                 });
            } else if (fmt === 'For Time') {
                const fts = workout.for_times || [];
                fts.forEach((row, i) => {
                    const idx = i + 1;
                    if (idx > 1) window.addNewUniversalRow('ft');
                    window.selectOption(`ft_exercise_${idx}`, row.workout_library.workout);
                    const l = document.querySelector(`[name="ft_load_${idx}"]`); if(l) l.value = row.training_load;
                    const u = document.querySelector(`[name="ft_unit_${idx}"]`); if(u) u.value = row.unit_type;
                    const r = document.getElementById(`ft_reps_${idx}`); if(r) r.value = row.reps;
                });
            }

        }, 200);
    }
</script>

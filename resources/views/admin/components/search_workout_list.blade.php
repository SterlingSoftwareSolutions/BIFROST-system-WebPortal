<div class="duplicateUi text-base min-w-0">
    <div class="ui-block flex flex-col text-lg p-2 mr-0 rounded-md gap-4 mb-4 min-w-0">
        <div class="flex gap-5 justify-between">
            {{-- Serach Section --}}
            <div class="flex-col w-full min-w-0">
                <div class="bg-gray-50 p-2 pt-0">
                    <div class="flex justify-center text-center items-center font-bold mb-3 text-2xl">Workout List</div>
                    <div class="flex justify-center items-center gap-2 flex-wrap lg:flex-nowrap">

                        <!-- Category Field -->
                        <div class="flex items-center gap-1 min-w-0">
                            <label for="categoryw_2" class="w-15 text-md">Category</label>
                            <select id="categoryw_2" name="categoryw_2" onchange="// getworkoutw(this)"
                                class="px-1 py-1 border rounded text-sm w-[8.25rem] lg:w-[9.5rem] max-w-full">
                                <option value="" selected>-- All Categories --</option>
                            </select>
                        </div>

                        <!-- Exercise Field (Searchable) -->
                        <div class="flex items-center gap-1 min-w-0">
                            <label for="workoutw_2" class="w-15 text-md pl-3">Exercise</label>

                            <!-- Custom Dropdown Structure -->
                            <div class="relative custom-dropdown group" id="dropdown_workoutw_2">
                                <input type="hidden" name="workoutw_2" id="input_workoutw_2" value="">

                                <!-- TRIGGER -->
                                <div onclick="toggleDropdownSearch('workoutw_2')"
                                    class="px-1 py-1 border rounded text-sm flex items-center justify-between cursor-pointer bg-white w-[8rem] lg:w-[9rem] max-w-full"
                                    tabindex="0">
                                    <span id="display_workoutw_2" class="text-gray-500 block truncate flex-1 min-w-0 text-left pr-2">-- All Exercises --</span>
                                    <svg class="w-3 h-3 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>

                                <!-- DROPDOWN CONTAINER -->
                                <div id="list_workoutw_2" class="hidden absolute left-0 right-0 top-[100%] mt-1 bg-white border border-gray-300 rounded shadow-xl z-[9999] overflow-hidden min-w-[150px]">
                                    <!-- SEARCH BAR -->
                                    <div class="p-2 border-b border-gray-100 bg-white sticky top-0">
                                        <input type="text"
                                            id="search_workoutw_2"
                                            class="w-full border border-gray-300 rounded p-1 text-sm font-normal focus:outline-none focus:border-blue-500"
                                            placeholder="Search..."
                                            oninput="filterDropdownSearch('workoutw_2')"
                                            onclick="event.stopPropagation()"
                                            autocomplete="off">
                                    </div>

                                    <div id="scroll_workoutw_2" class="max-h-60 overflow-y-auto">

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Name Field -->
                        <div class="flex items-center gap-1 min-w-0">
                            <label for="namew_2" class="w-15 text-md pl-3">Name</label>
                            <input type="text" id="namew_2" name="namew_2"
                                class="px-1 py-1 border rounded text-sm w-[7rem] lg:w-[8.5rem] max-w-full">
                        </div>



                        <!-- Clear Button -->
                        <div class="flex items-center">
                            <button id="searchclearsetwarmup_1" onclick="// clearSearchWarmup()" type="button"
                                class="bg-black text-white py-1 px-2 rounded text-xs">Clear</button>
                        </div>

                    </div>
                </div>

                <!-- Scroll Section -->
                <div id="unified_workout_list_container" class="mt-4 max-h-[600px] overflow-y-auto overflow-x-hidden space-y-4 min-w-0">
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    window.unifiedWorkoutsMap = {};

    // Main render function for the unified workout list
    window.renderUnifiedWorkoutList = function(workouts, classes = []) {
        const container = document.getElementById('unified_workout_list_container');

        // Reset map
        window.unifiedWorkoutsMap = {};

        if (!workouts || workouts.length === 0) {
            container.innerHTML = `
            <div class="text-center text-gray-400 py-8 text-md">
                No workouts found.
            </div>
        `;
            return;
        }

        let html = '';
        workouts.forEach(workout => {
            // Store in map for editing
            window.unifiedWorkoutsMap[workout.id] = workout;

            const fmtName = workout.format ? workout.format.name : 'Unknown Format';
            const workoutName = workout.workout_name || 'Unnamed Workout';
            const number = workout.number || '';


            let statsHtml = '';
            if (fmtName === 'AMRAP') {
                statsHtml = `<span class="text-xs text-black font-bold">AMRAP ${number} min</span>`;
            } else if (fmtName === 'Rounds') {
                statsHtml = `<span class="text-xs text-black font-bold">Rounds ${number}</span>`;
            } else if (fmtName === 'EMOM') {
                statsHtml = `<span class="text-xs text-black font-bold">EMOM ${number} min</span>`;
            } else if (fmtName === 'Intervals') {
                statsHtml = `<span class="text-xs text-black font-bold">Intervals ${number}</span>`;
            } else if (fmtName === 'Circuit') {
                statsHtml = `<span class="text-xs text-black font-bold">Stations ${number}</span>`;
            }


            let rowsHtml = generateWorkoutRowsHtml(fmtName, workout);

            // --- 3. Build Card HTML ---
            html += `
        <div class="border border-gray-300 rounded-lg mb-4 bg-white shadow-sm overflow-hidden text-sm">
            <!-- Header -->
            <div class="flex justify-between items-center bg-white border-b border-gray-200 px-3 py-2">
                 <div class="font-bold text-base text-gray-800">${fmtName} <span class="text-gray-500 font-normal ml-1 text-sm">${workout.type ? '- ' + workout.type.name : ''}</span></div>
                 <div class="flex gap-3">
                     <button onclick="editUnifiedWorkout(${workout.id}); openWorkoutModal()" class="text-gray-700 hover:text-black">
                        <svg class="feather feather-edit" fill="none" height="22" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>

                     </button>
                     <button onclick="deleteUnifiedWorkout(${workout.id})" class="text-gray-500 hover:text-red-700">
                        <svg fill="none" height="22" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><g><path d="M10,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625l12-12    c0.390625-0.390625,1.0234375-0.390625,1.4140625,0s0.390625,1.0234375,0,1.4140625l-12,12    C10.5117188,22.9023438,10.2558594,23,10,23z"/></g><g><path d="M22,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688l-12-12c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625    s1.0234375-0.390625,1.4140625,0l12,12c0.390625,0.390625,0.390625,1.0234375,0,1.4140625    C22.5117188,22.9023438,22.2558594,23,22,23z"/></g></g></svg>
                     </button>
                 </div>
            </div>

            <!-- Body -->
            <div class="px-3 py-2">
                 <div class="flex justify-between items-center mb-3">
                     <div class="font-bold text-xl text-gray-800 flex-1">${workoutName}</div>

                     <!-- Right Side Stats -->
                     <div class="text-right">
                        ${statsHtml}
                     </div>
                 </div>


                 <!-- Rows Container -->
                 <div class="space-y-1">
                     ${rowsHtml}
                 </div>
            </div>

            <!-- Footer -->
              <div class="mt-2 text-sm border-t border-gray-200 px-3 py-3 flex flex-wrap items-center bg-gray-50 gap-3 md:gap-4 min-w-0">
                 <div class="font-bold text-black whitespace-nowrap text-base">Assign to Class:</div>
                  <div class="flex flex-wrap gap-2 pb-1 min-w-0">
                      ${generateClassButtons(workout, classes)}
                 </div>
            </div>
        </div>
        `;
        });

        container.innerHTML = html;
    };

    // Helper to generate rows for different formats
    function generateWorkoutRowsHtml(fmt, workout) {
        let rows = [];
        if (fmt === 'Straight Sets') rows = workout.straights || [];
        else if (fmt === 'Rounds') rows = workout.rounds || [];
        else if (fmt === 'Intervals') rows = workout.intervals || [];
        else if (fmt === 'AMRAP') rows = workout.amraps || [];
        else if (fmt === 'EMOM') rows = workout.emoms || [];
        else if (fmt === 'Circuit') rows = workout.circuits || [];
        else if (fmt === 'Pyramid') rows = workout.pyramids || [];
        else if (fmt === 'For Time') rows = workout.for_times || [];

        if (!rows || rows.length === 0) return '';

        return rows.map(row => {

            const exName = (row.workout_library && row.workout_library.workout) ? row.workout_library.workout : 'Unknown Exercise';
            let load = row.training_load || '';
            const unit = row.unit_type || '';
            
            let loadDisplay = load;
            if (unit === 'BW' || unit === 'N/A') {
                loadDisplay = `&mdash; ${unit}`;
            } else {
                loadDisplay = load ? `${load} ${unit}` : '-';
            }

            let reps = row.reps || '';
            let repsDisplay = reps;
            if (unit === 'Cal' || unit === 'm' || !reps || reps == 0 || reps === '-') {
                repsDisplay = '----';
            } else {
                const isNumericReps = !isNaN(reps) && reps !== '';
                repsDisplay = repsDisplay + (isNumericReps ? ' REPS' : '');
            }

            // Intervals specific logic for Work/Rest
            let intervalColsHtml = '';
            if (fmt === 'Intervals') {
                const formatIntervalTime = (t) => {
                    if (!t || t === '00:00:00') return '00:00:00 min';
                    // If it's already in 00:00:00 format, just add min
                    return `${t} min`;
                };

                const work = formatIntervalTime(row.work);
                const rest = formatIntervalTime(row.rest);

                intervalColsHtml = `
                <div class="w-32 text-center text-black text-[17px]">${work}</div>
                <div class="w-32 text-center text-black text-[17px]">${rest}</div>
            `;
            }

            let mainRowHtml = `
             <div class="flex items-center text-[17px] font-weight-500 py-0.5 gap-3 md:gap-6 min-w-0">
                 <div class="flex-1 font-medium text-gray-800">${exName}</div>
                 <div class="w-24 md:w-28 lg:w-32 text-center text-black">${loadDisplay}</div>
                 ${fmt === 'Intervals'
                    ? intervalColsHtml
                    : `<div class="w-20 md:w-24 text-right text-black uppercase">${repsDisplay}</div>`
                 }
             </div>
        `;

            // Nested Sets Logic (for Straight Sets)
            let nestedSetsHtml = '';
            if (fmt === 'Straight Sets' && row.sets && row.sets.length > 0) {
                nestedSetsHtml = row.sets.map(set => {
                    let setLoad = set.training_load || '';
                    const setUnit = set.unittype || '';
                    
                    let setLoadDisplay = setLoad;
                    if (setUnit === 'BW' || setUnit === 'N/A') {
                        setLoadDisplay = `&mdash; ${setUnit}`;
                    } else {
                        setLoadDisplay = setLoad ? `${setLoad} ${setUnit}` : '-';
                    }

                    let setReps = set.res || '';
                    let setRepsDisplay = setReps;
                    if (setUnit === 'Cal' || setUnit === 'm' || !setReps || setReps == 0 || setReps === '-') {
                        setRepsDisplay = '----';
                    } else {
                        const isNum = !isNaN(setReps) && setReps !== '';
                        setRepsDisplay = setRepsDisplay + (isNum ? ' REPS' : '');
                    }

                    return `
                     <div class="flex items-center text-[15px] font-weight-500 pl-2 border-l-2 border-gray-200 my-1 gap-3 md:gap-6 min-w-0">
                         <div class="flex-1 text-gray-500 text-[13px]">${exName} (Set)</div>
                         <div class="w-24 md:w-28 lg:w-32 text-center text-gray-500">${setLoadDisplay}</div>
                         <div class="w-20 md:w-24 text-right text-gray-500 uppercase">${setRepsDisplay}</div>
                     </div>
                `;
                }).join('');
            }

            return mainRowHtml + nestedSetsHtml;
        }).join('');
    }

    // Fetch Logic
    window.fetchUnifiedWorkouts = function() {
        const name = document.getElementById('namew_2').value;
        const categoryId = document.getElementById('categoryw_2').value;
        // Updated to use the hidden input ID for the custom dropdown
        const libraryId = document.getElementById('input_workoutw_2') ? document.getElementById('input_workoutw_2').value : '';

        const activeDayBtn = document.querySelector('.day.bg-\\[\\#EEE8AA\\]');
        let date = activeDayBtn ? activeDayBtn.innerText.trim() : '';
        document.getElementById('common_date').value = date;

        if (!date) {

            const firstDay = document.getElementById('1day');
            if (firstDay && firstDay.innerText.trim() !== 'Day 1') {
                date = firstDay.innerText.trim();
            }
        }

        console.log("Fetching workouts for date:", date);

        const params = new URLSearchParams();
        if (name) params.append('name', name);
        if (categoryId) params.append('category_id', categoryId);
        if (libraryId) params.append('library_id', libraryId);
        if (date) params.append('date', date);

        fetch(`/workout/get-manager-list?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.workouts) {
                    window.renderUnifiedWorkoutList(data.workouts, data.classes);
                } else {
                    console.error('Invalid response structure', data);
                }
            })
            .catch(error => console.error('Error fetching workouts:', error));
    };

    function generateClassButtons(workout, classes) {
        if (!classes || classes.length === 0) return '<span class="text-gray-400 italic">No classes found</span>';

        let html = '';
        const assignedIds = workout.assigned_class_ids || [];

        // Check if ALL assigned
        const allAssigned = classes.length > 0 && classes.every(c => assignedIds.includes(c.id));
        const allBtnClass = allAssigned ?
            'bg-green-100 border-green-500 text-green-700' :
            'bg-white border-gray-300 text-gray-600';

        // "All" Button
        html += `<button onclick="assignUnifiedWorkout(${workout.id}, 'all', '${allAssigned ? 1 : 0}')"
             class="border font-bold px-3 py-1 rounded shadow-sm hover:bg-gray-100 whitespace-nowrap text-sm ${allBtnClass}">All</button>`;

        classes.forEach(cls => {
            // Format Time
            let timeParts = cls.time.split(':');

            let dateObj = new Date();
            dateObj.setHours(parseInt(timeParts[0], 10));
            dateObj.setMinutes(parseInt(timeParts[1], 10));

            let timeString = dateObj
                .toLocaleTimeString([], {
                    hour: 'numeric',
                    minute: '2-digit'
                })
                .toLowerCase();

            const isAssigned = assignedIds.includes(cls.id);
            const btnClass = isAssigned ?
                'bg-green-100 border-green-500 text-green-700' :
                'bg-white border-gray-300 text-gray-600';

            html += `<button onclick="assignUnifiedWorkout(${workout.id}, ${cls.id}, '${isAssigned ? 1 : 0}')"
                 class="border font-bold px-3 py-1 rounded shadow-sm hover:bg-gray-100 whitespace-nowrap text-sm ${btnClass}">${timeString}</button>`;
        });

        return html;
    }

    window.assignUnifiedWorkout = function(workoutId, classId, isAssigned) {
        // Get Date from active day
        const activeDayBtn = document.querySelector('.day.bg-\\[\\#EEE8AA\\]');
        const date = activeDayBtn ? activeDayBtn.innerText.trim() : '';

        if (!date) {
            alert("Please select a date from the Day Planner first.");
            return;
        }

        let action = '';
        if (classId === 'all') {
            action = (isAssigned == 1) ? 'unassign' : 'assign_all';
        } else {
            action = (isAssigned == 1) ? 'unassign' : 'assign';
        }

        if (!confirm(action === 'unassign' ? 'Unassign workout?' : 'Assign workout?')) return;

        fetch('/assign-workout-class', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    workout_id: workoutId,
                    class_id: classId,
                    type: 'workout_manager', // Generic type
                    action: action,
                    date: date
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' || data.message) {
                    // Refresh list to update UI state
                    fetchUnifiedWorkouts();

                    // Refresh Day Planner Left Panel (Classes Table)
                    if (typeof getdateName === 'function') {
                        getdateName(date);
                    }
                    // alert(data.message);
                } else {
                    alert('Failed to update assignment.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error communicating with server.');
            });
    };

    // --- Delete Workout ---
    window.deleteUnifiedWorkout = function(id) {
        if (!confirm('Are you sure you want to delete this workout? This action cannot be undone.')) {
            return;
        }

        fetch(`/workout/delete/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Refresh the list
                    fetchUnifiedWorkouts();
                } else {
                    alert('Failed to delete workout: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the workout.');
            });
    };

    // --- Custom Searchable Dropdown  ---

    window.availableExercisesSearch = []; // Store fetched exercises here

    window.toggleDropdownSearch = function(id) {
        const list = document.getElementById(`list_${id}`);
        const search = document.getElementById(`search_${id}`);
        const trigger = document.getElementById(`dropdown_${id}`);

        // Close others
        document.querySelectorAll('[id^="list_"]').forEach(el => {
            if (el.id !== `list_${id}`) {
                el.classList.add('hidden');
                el.style.position = '';
            }
        });

        if (list && trigger) {
            if (list.classList.contains('hidden')) {
                // Show
                list.classList.remove('hidden');

                // FIXED POSITIONING LOGIC
                // Calculate coordinates relative to viewport to avoid overflow clipping
                const rect = trigger.getBoundingClientRect();

                list.style.position = 'fixed';
                list.style.top = rect.bottom + 'px';
                list.style.left = rect.left + 'px';
                // Match width to trigger or min-width
                list.style.minWidth = rect.width + 'px';
                list.style.width = 'auto';
                list.style.maxWidth = '300px';
                list.style.zIndex = '9999';

                setTimeout(() => {
                    if (search) search.focus();
                }, 50);
            } else {
                // Hide
                list.classList.add('hidden');
                list.style.position = ''; // Reset
            }
        }
    };

    // Close dropdowns on global scroll to prevent floating issues
    window.addEventListener('scroll', function(e) {
        // If scrolling inside the dropdown list, do not close
        if (e.target.closest && e.target.closest('[id^="list_"]')) return;

        document.querySelectorAll('[id^="list_"]:not(.hidden)').forEach(el => {
            el.classList.add('hidden');
            el.style.position = '';
        });
    }, true); // Capture phase

    window.filterDropdownSearch = function(id) {
        const input = document.getElementById(`search_${id}`);
        const scrollContainer = document.getElementById(`scroll_${id}`);
        if (!input || !scrollContainer) return;

        const filter = input.value.toLowerCase();
        const divs = scrollContainer.children;

        for (let i = 0; i < divs.length; i++) {
            const txt = divs[i].innerText;
            if (txt.toLowerCase().indexOf(filter) > -1) {
                divs[i].classList.remove('hidden');
            } else {
                divs[i].classList.add('hidden');
            }
        }
    };

    window.selectOptionSearch = function(id, value, text) {
        const input = document.getElementById(`input_${id}`);
        const display = document.getElementById(`display_${id}`);
        const list = document.getElementById(`list_${id}`);

        if (input) {
            input.value = value;
            // Trigger fetch
            fetchUnifiedWorkouts();
        }
        if (display) {
            display.innerText = text;
            display.classList.remove('text-gray-500');
            display.classList.add('text-gray-900');
        }
        if (list) {
            list.classList.add('hidden');
            list.style.position = ''; // Reset
        }
    };

    // Close on click outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown') && !e.target.closest('[id^="list_"]')) {
            document.querySelectorAll('[id^="list_"]').forEach(el => {
                el.classList.add('hidden');
                el.style.position = '';
            });
        }
    });



    // Fetch categories for search dropdown
    function populateSearchCategories() {
        $.ajax({
            url: "/getCategory",
            type: "POST",
            data: {
                tab: "all", // Fetch ALL categories
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                let options = response.category_options || [];
                if (typeof options === 'object' && !Array.isArray(options)) {
                    options = Object.values(options);
                }

                const select = document.getElementById('categoryw_2');
                select.innerHTML = '<option value="" selected>-- All Categories --</option>';
                options.forEach(opt => {
                    const o = document.createElement('option');
                    o.value = opt.id;
                    o.text = opt.category_name;
                    select.add(o);
                });
            },
            error: function(err) {
                console.error("Error fetching categories:", err);
            }
        });
    }

    // Fetch exercises for search dropdown based on category
    window.getworkoutw_search = function(categoryId) {
        //Use custom dropdown container
        const scrollContainer = document.getElementById('scroll_workoutw_2');
        const display = document.getElementById('display_workoutw_2');
        const input = document.getElementById('input_workoutw_2');
        const searchInput = document.getElementById('search_workoutw_2');

        // Reset selection UI
        if (display) {
            display.innerText = "-- All Exercises --";
            display.classList.add('text-gray-500');
            display.classList.remove('text-gray-900');
        }
        if (input) input.value = "";
        if (searchInput) searchInput.value = "";

        $.ajax({
            url: "/get-workout-filter",
            type: "POST",
            data: {
                tab: "all",
                id: categoryId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                const exercises = response.workouts || [];
                window.availableExercisesSearch = exercises; // Store for filtering if needed later

                // Render Options
                let html = `<div class="p-2 hover:bg-gray-100 cursor-pointer text-sm font-bold text-gray-700 block" onclick="selectOptionSearch('workoutw_2', '', '-- All Exercises --')">-- All Exercises --</div>`;

                exercises.forEach(ex => {
                    const txt = ex.workout;
                    const idVal = ex.id;
                    html += `<div class="p-2 hover:bg-gray-100 cursor-pointer text-sm font-bold text-gray-700 block" onclick="selectOptionSearch('workoutw_2', '${idVal}', '${txt.replace(/'/g, "\\'")}')">${txt}</div>`;
                });

                if (scrollContainer) {
                    scrollContainer.innerHTML = html;
                }

                fetchUnifiedWorkouts(); // Refresh list
            },
            error: function(err) {
                console.error("Error fetching exercises:", err);
            }
        });
    }

    // Bind Events
    document.addEventListener('DOMContentLoaded', () => {
        // Initial Population
        populateSearchCategories();
        fetchUnifiedWorkouts();

        // Bind Category Change
        const catSelect = document.getElementById('categoryw_2');
        if (catSelect) {
            catSelect.onchange = function() {
                getworkoutw_search(this.value);
            };
        }

        // Bind Exercise Change - REMOVED (Handled by selectOptionSearch)

        // Bind Name Input
        const nameInput = document.getElementById('namew_2');
        if (nameInput) {
            nameInput.oninput = () => fetchUnifiedWorkouts();
        }

        // Bind "Clear" button
        const clearBtn = document.getElementById('searchclearsetwarmup_1');
        if (clearBtn) {
            clearBtn.onclick = () => {
                document.getElementById('namew_2').value = '';
                document.getElementById('categoryw_2').value = '';
                // Fetch all will reset the UI for exercise dropdown too
                getworkoutw_search('');
            };
        }

        // Listen for Date Clicks from the Day Planner
        const dayButtons = document.querySelectorAll('.day');
        dayButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Wait slightly for the 'active' class to update in session.blade.php
                setTimeout(() => {
                    fetchUnifiedWorkouts();
                }, 100);
            });
        });

        // Initial Fetch (also delayed slightly to ensure date text is populated)
        setTimeout(() => {
            // Populate exercises on load
            getworkoutw_search('');
            fetchUnifiedWorkouts();
        }, 200);
    });
</script>
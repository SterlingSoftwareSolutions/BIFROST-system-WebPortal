<div id="warmup" hidden>

    <div class="flex justify-end items-center ml-auto mr-8 gap-5 font-bold text-xl -mt-5 p-0">
        {{-- hidden input field --}}
        <input type="text" name="selecttabw" id="selecttabw" hidden>
        <form id="deleteWarmupsForm">
            @csrf
            @method('DELETE')
            <input type="text" name="selectdatewd" id="selectdatewd" hidden>
            {{-- <button type="button" class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base"
                onclick="deleteWarmups()">Clear</button> --}}
        </form>
        <script>
            //selected strength delete
            $(document).on('click', '.delete-warmups-btn', function() {
                const date = document.getElementById('selectdatewd').value;
                const id = $(this).data('id');
                const confirmed = confirm("Are you sure you want to delete this weightlifting record?");

                if (!confirmed) return;

                $.ajax({
                    url: "/delete-warmup",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            getwarmup(date);
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", error);
                        alert("An error occurred while deleting the weightlifting record.");
                    }
                });
            });

            function deleteWarmups() {
                // Serialize the form data
                const formData = $('#deleteWarmupsForm').serialize();
                const tab = document.getElementById('warmupTab');

                // AJAX request
                $.ajax({
                    url: '{{ route("warmups.deleteAllBySelectDate") }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Handle the response
                        alert(response.message); // Display a success message or handle UI update
                        if (tab) {
                            tab.click();
                        }

                    },
                    error: function(xhr) {
                        // Handle error
                        console.error(xhr.responseText);
                    }
                });
            }

            function storeWarmup() {
                // Serialize the form data
                const formData = $('#storeWarmupForm').serialize();
                const tab = document.getElementById('warmupTab');
                console.log("call warmap", formData);
                const warmupId = $('#warmup_id').val();
                const url = warmupId ? `/update-warmup` : '/store-warmup';

                // AJAX request
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Handle the response
                        alert(response.message); // Display a success message or handle UI update

                        if (tab) {
                            tab.click();
                        }
                        // Optionally, clear the form fields
                        $('#storeWarmupForm')[0].reset();
                        $('#warmup_id').val('');

                        document.getElementById('savebtnwarmup').textContent = "Save";
                        document.getElementById('clearwarmbtn').classList.add('hidden');
                    },
                    error: function(xhr) {
                        // Handle error
                        console.error(xhr.responseText);
                        alert('An error occurred while saving the data.');
                    }
                });
            }
        </script>
    </div>
    <div id="warmup-info" class="-mt-5 pt-2"></div>
    <form id="storeWarmupForm">
        @csrf
        <input type="text" name="selectdatew" id="selectdatew" hidden>

        <div class="duplicateUi text-base" data-index="1">

            <div class="ui-block flex flex-col text-lg p-4  mr-8 rounded-md gap-4 mb-4 ">
                <div class="flex gap-5 justify-between">
                    {{-- Serach Section --}}
                    <div class="flex-col w-full">
                        <div class="bg-gray-50 p-4">
                            <div class="flex justify-center text-center items-center font-bold mb-3 text-2xl">Workout List</div>
                            <div class="flex items-center space-x-2 flex-nowrap">

                                <!-- Category Field -->
                                <div class="flex items-center space-x-1 flex-[1] min-w-[120px]">
                                    <label for="categoryw_2" class="w-15 text-xs">Category</label>
                                    <select id="categoryw_2" name="categoryw_2" onchange="getworkoutw(this)"
                                        class="flex-1 px-1 py-1 border rounded text-xs">
                                        <option value="" selected disabled>-- Select --</option>
                                    </select>
                                </div>

                                <!-- Exercise Field -->
                                <div class="flex items-center space-x-1 flex-[1] min-w-[120px]">
                                    <label for="workoutw_2" class="w-15 text-xs pl-3">Exercise</label>
                                    <select id="workoutw_2" name="workoutw_2" class="flex-1 px-1 py-1 border rounded text-xs">
                                        <option value="" selected disabled>-- Select --</option>
                                    </select>
                                </div>

                                <!-- Name Field -->
                                <div class="flex items-center space-x-1 flex-[1] min-w-[120px]">
                                    <label for="namew_2" class="w-15 text-xs ">Name</label>
                                    <input type="text" id="namew_2" name="namew_2"
                                        class="flex-1 px-1 py-1 border rounded text-xs">
                                </div>

                                <!-- Go Button -->
                                <div class="flex items-center">
                                    <button id="addsetwarmup_1" onclick="filterWarmup(date)" type="button"
                                        class="bg-black text-white py-1 px-2 rounded text-xs">Go</button>
                                </div>

                                <!-- Clear Button -->
                                <div class="flex items-center">
                                    <button id="searchclearsetwarmup_1" onclick="clearSearchWarmup()" type="button"
                                        class="bg-black text-white py-1 px-2 rounded text-xs">Clear</button>
                                </div>

                            </div>



                        </div>

                        <!-- Scroll Section -->
                        <div id="setwarmups" class="mt-4 max-h-[600px] overflow-y-auto space-y-4">
                        </div>

                    </div>

                    {{-- form section --}}
                    <div class="flex-col w-full bg-gray-50 p-4">
                        <div class="flex justify-center text-center items-center font-bold mb-3 text-2xl">Create</div>

                        <input type="hidden" id="warmup_id" name="warmup_id" value="">
                        <div class="flex items-center border-b mt-2">
                            <label for="namew_1" class="w-60 block mb-1">Workout Name <span class="text-red-500">*</span></label>
                            <input type="text" id="namew_1" name="namew_1"
                                class="w-1/3 px-3 py-3 border flex rounded mb-2" required>

                            </div>
                         <!-- Workout Entry Block -->
                        <div class="workout-entry border-b rounded mb-4" id="workout-entry-1">
                            <div class="flex items-center border-b">
                                <label for="categoryw_1" class="w-60 block mb-1">Category <span class="text-red-500">*</span></label>
                                <select id="categoryw_1" name="categoryw_1" onchange="getworkoutw(this)" class="w-1/3 px-3 py-3 border flex rounded mb-2">
                                <option value="" selected disabled>-- Select Category --</option>
                                </select>
                            </div>

                            <div class="flex items-center border-b mt-2">
                                <label for="workoutw_1" class="w-60 block mb-1">Workout <span class="text-red-500">*</span></label>
                                <select id="workoutw_1" name="workoutw_1" class="w-1/3 px-3 py-3 border flex rounded mb-2">
                                <option value="" selected disabled>-- Select Workout --</option>
                                </select>
                            </div>

                            <div class="flex items-center border-b mt-2">
                                <label for="repsw_1" class="w-60 block mb-1">Volume <span class="text-red-500">*</span></label>
                                <div class="relative flex items-center max-w-[8rem] mb-2">
                                <button type="button" onclick="decrement(this.parentNode.querySelector('input').id)" id="decrement-repsw_1"
                                    class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11">-</button>
                                <input type="text" id="repsw_1" name="repsw_1"
                                    class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-sm w-full py-2.5" placeholder="0"
                                    readonly />
                                <button type="button" onclick="increment(this.parentNode.querySelector('input').id)" id="increment-repsw_1"
                                    class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11">+</button>
                                </div>
                            </div>

                            <div class="flex items-center border-b mt-2">
                                <label class="w-60 block mb-1">
                                    Training Load <span class="text-red-500">*</span>
                                </label>

                                <!-- Same width as workout select -->
                                <div class="w-1/3 flex items-center gap-3">

                                    <!-- Main input -->
                                    <input
                                        type="number"
                                        name="weigthc_1"
                                        class="flex-1 px-3 py-3 border rounded mb-2"
                                        data-main-input
                                        required
                                    >

                                    <!-- Gender inputs -->
                                    <div class="hidden flex items-center gap-2 mb-2" data-gender-inputs>
                                        <label class="text-sm">M</label>
                                        <input type="number" name="male_1" class="w-20 px-2 py-2 border rounded">
                                        <label class="text-sm">F</label>
                                        <input type="number" name="female_1" class="w-20 px-2 py-2 border rounded">
                                    </div>

                                    <!-- Unit selector -->
                                    <select
                                        name="unit_1"
                                        class="px-3 py-3 border rounded mb-2"
                                        onchange="toggleWarmupGenderInputs(this)">
                                        <option value="RPE">RPE</option>
                                        <option value="Cal">Cal</option>
                                        <option value="%">%</option>
                                        <option value="Kg">Kg</option>
                                        <option value="BW">BW</option>
                                        <option value="N/A">N/A</option>
                                    </select>
                                </div>

                                <!-- Info icon stays aligned right -->
                                <div class="relative ml-auto mr-10 mb-2">
                                    <span onclick="toggleInfoPopup(this, event)" class="cursor-pointer select-none">
                                        <svg width="20px" height="20px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11 12H9v-.148c0-.876.306-1.499 1-1.852.385-.195 1-.568 1-1a1.001 1.001 0 00-2 0H7c0-1.654 1.346-3 3-3s3 1 3 3-2 2.165-2 3zm-2 3h2v-2H9v2z" fill="#9CA3AF"/>
                                            <path d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1116 0 8 8 0 01-16 0z" fill="#9CA3AF"/>
                                        </svg>
                                    </span>

                                    <div class="hidden absolute left-[-100px] top-5 w-48 bg-white border border-gray-200 shadow-lg p-3 rounded text-left text-xs z-50 info-popup font-normal normal-case">
                                        <p class="mb-1"><b>RPE</b> – Rate of Perceived Exertion (1-10)</p>
                                        <p class="mb-1"><b>%</b> – Percentage of 1RM</p>
                                        <p class="mb-1"><b>Cal</b> – Number of calories</p>
                                        <p class="mb-1"><b>Kg</b> – Weight</p>
                                        <p class="mb-1"><b>BW</b> – Body Weight</p>
                                        <p><b>N/A</b> – N/A</p>
                                    </div>
                                </div>
                            </div>



                        </div>


        <!-- Add Another Button -->
        {{-- <div class="flex flex-row justify-end gap-4 mt-5" id="add-another-btn">
            <button type="button" onclick="addAnotherWorkout()" class="bg-[#000000] text-white py-2 px-4 rounded mr-8 hover:bg-gray-800 w-30">
                + Add Another
            </button>
        </div> --}}

        <!-- Save & Cancel -->
        <div class="flex flex-row justify-end gap-4 mt-5">
            <button type="button" id="clearwarmbtn" onclick="clearWarmupForm()" class="bg-gray-200 text-gray-800 py-2 px-4 rounded hover:bg-gray-300 w-24">
                Cancel
            </button>

            <button type="button" id="savebtnwarmup" onclick="storeWarmup()" class="bg-[#FB1018] text-white py-2 px-4 rounded mr-8 hover:bg-red-700 w-24">
                Save
            </button>
        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- <a class="duplicateBtn bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base cursor-pointer">Another</a> --}}


    </form>
</div>
<script>
    function toggleWarmupGenderInputs(selectElement) {
    // Get the main row container
    const row = selectElement.closest('.flex');

    const defaultInput = row.querySelector('[data-main-input]');
    const genderDiv = row.querySelector('[data-gender-inputs]');

    if (!defaultInput || !genderDiv) return;

    const selected = selectElement.value;

    if (selected === 'Cal') {
        genderDiv.classList.remove('hidden');
        defaultInput.classList.add('hidden');
        defaultInput.removeAttribute('required');
    } else {
        genderDiv.classList.add('hidden');
        defaultInput.classList.remove('hidden');
        defaultInput.setAttribute('required', 'true');
    }
}

    function toggleInfoPopup(el, event) {
    event.stopPropagation(); // Prevent document click listener from firing

    const popup = el.nextElementSibling;
    if (!popup) return;

    document.querySelectorAll('.info-popup').forEach(p => {
        if (p !== popup) p.classList.add('hidden');
    });

    popup.classList.toggle('hidden');
}


    document.addEventListener("click", function(e) {
        if (!e.target.closest('.info-popup') && !e.target.matches('.text-red-500')) {
            document.querySelectorAll('.info-popup').forEach(p => p.classList.add('hidden'));
        }
    });

    // Function to set category options for warmup
    // Function to set category options for warmup categories
    function setCategoryW(id, categoryName, selectId) {
        const categorySelectW = document.getElementById(selectId);
        const option = document.createElement('option');
        option.value = id;
        option.text = categoryName;
        categorySelectW.add(option);
    }

    // Function to get warmup categories via AJAX
    function getCategoryW() {
        const selectTabW = "warmup";

        $.ajax({
            url: "/getCategory",
            type: "POST",
            data: {
                tab: selectTabW,
                _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
            },
            success: function(response) {
                console.log(response);

                // Convert category_optionsW object into an array, if it's not already an array
                let categoryOptionsW = response.category_options || [];

                // If categoryOptionsW is an object, convert it into an array
                if (categoryOptionsW && typeof categoryOptionsW === 'object') {
                    categoryOptionsW = Object.values(categoryOptionsW); // Convert object to array
                } else {
                    categoryOptionsW = [];
                }

                // Clear existing options in the select element before adding new ones
                ['categoryw_1', 'categoryw_2'].forEach(id => {
                    const categorySelectW = document.getElementById(id);
                    categorySelectW.innerHTML =
                        '<option value="" selected disabled>-- Select Category --</option>';
                });

                // Loop through each category_option and call the setCategoryW() function
                categoryOptionsW.forEach(option => {
                    setCategoryW(option.id, option.category_name, 'categoryw_1');
                    setCategoryW(option.id, option.category_name, 'categoryw_2');
                });

                // Optional: Sort the options alphabetically if needed
                ['categoryw_1', 'categoryw_2'].forEach(id => {
                    sortSelectOptionsW(document.getElementById(id));
                });

            },
            error: function(xhr, status, error) {
                console.error(error); // Handle error
            }
        });
    }


    // Function to sort warmup select options alphabetically
    function sortSelectOptionsW(selectElement) {
        const optionsW = Array.from(selectElement.options);
        optionsW.sort((a, b) => a.text.localeCompare(b.text));
        selectElement.innerHTML = '';
        optionsW.forEach(option => selectElement.add(option));
    }


    // Function to set workoutw options for warmup

    function getworkoutw(selectElement) {
        console.log(selectElement);
        const tab = "warmup"; // Assuming the tab is predefined for warmup
        const selectId = selectElement.value; // Get the value of the selected element

        // Split the ID of the select element by underscores
        const idParts = selectElement.id.split('_');

        // Get the remaining part after the base
        const remainingPart = idParts.slice(1).join('_');

        // Determine the workout select element ID based on the category type
        let workoutSelectId;
        if (idParts[0] === 'categoryw') {
            workoutSelectId = `workoutw_${remainingPart}`;
        } else if (idParts[0] === 'categorywe') {
            workoutSelectId = `workoutwe_${remainingPart}`;
        } else {
            console.error('Unknown category type');
            return;
        }

        $.ajax({
            url: "/get-workout",
            type: "POST",
            data: {
                tab: tab,
                id: selectId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                const workouts = response.workouts || [];
                const workoutSelect = document.getElementById(workoutSelectId);
                clearOptions(workoutSelect);

                workouts.forEach(workout => {
                    setWorkout(workoutSelect, workout.id, workout.workout);
                });
            },
            error: function(xhr, status, error) {
                console.error(error);
            },
        });
    }

    // Function to set workout options
    function setWorkout(selectElement, id, workout) {
        const option = document.createElement('option');
        option.value = id;
        option.textContent = workout;
        selectElement.appendChild(option);
    }

    // Function to clear existing workout options
    function clearOptions(selectElement) {
        selectElement.innerHTML = '<option value="" selected disabled>-- Select Workout --</option>';
    }

    function increment(id) {
        const input = document.getElementById(id);
        const currentValue = parseInt(input.value) || 0;
        input.value = currentValue + 1;
    }

    function decrement(id) {
        const input = document.getElementById(id);
        const currentValue = parseInt(input.value) || 0;
        if (currentValue > 0) {
            input.value = currentValue - 1;
        }
    }

    let allWarmupData = [];
    // get warmup
    function getwarmup(date) {
        console.log("get warmup date: " + date);

        $.ajax({
            url: "/get-wormup",
            type: "POST",
            data: {
                date: date,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log(response);
                allWarmupData = response.result;
                const classesData = response.daily_classes || [];
                setwarmups(response.result, response.categoryOptions, classesData);
            },
            error: function(xhr, status, error) {
                console.error(error);
            },
        });
    }


    //set new warmup cards
    function setwarmups(warmups, categoryOptions, classesData = []) {
        const container = $("#setwarmups"); // Replace with your actual container class or ID
        container.empty(); // Clear previous content

        // Sort classes by time
        classesData.sort((a, b) => {
            return new Date('1970-01-01T' + a.time) - new Date('1970-01-01T' + b.time);
        });

        warmups.forEach((item, index) => {

             // Generate Class Buttons
            let classButtonsHTML = '';
            if (classesData.length > 0) {
                 // Check if item has assigned_class_ids array, if not default to empty
                     const assignedIds = item.assigned_class_ids || [];
                     
                     // Check if ALL classes are assigned
                     const allClassIds = classesData.map(c => c.id);
                     const isAllAssigned = classesData.length > 0 && allClassIds.every(id => assignedIds.includes(id));
                     
                     const allBtnClass = isAllAssigned 
                        ? 'border-green-600 bg-green-50 text-green-700 font-bold' 
                        : 'border-gray-400 text-gray-600';

                     classesData.forEach(cls => {
                         // Format time 24h -> 12h
                         let timeParts = cls.time.split(':');
                         let dateObj = new Date();
                         dateObj.setHours(timeParts[0]);
                         dateObj.setMinutes(timeParts[1]);
                         let timeString = dateObj.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' }).toLowerCase();

                         // Determine if assigned
                         const isAssigned = assignedIds.includes(cls.id);

                         // Style: Green border/text if assigned, else Gray
                         const activeClass = isAssigned
                             ? 'border-green-600 bg-green-50 text-green-700 font-bold'
                             : 'border-gray-300 text-gray-600';
                            
                         classButtonsHTML += `
                             <button type="button"
                                 class="border px-3 py-1 rounded ${activeClass} hover:bg-gray-100 transition-colors text-sm whitespace-nowrap"
                                 onclick="toggleAssignmentWarmup(${item.id}, ${cls.id}, '${timeString}', ${isAssigned})">
                                 ${timeString}
                             </button>
                         `;
                     });

                    /* Prepend All Button Logic */
                    classButtonsHTML = `
                        <button type="button" 
                            class="border px-3 py-1 rounded ${allBtnClass} hover:bg-gray-100 text-sm whitespace-nowrap" 
                            onclick="toggleAllAssignmentsWarmup(${item.id}, ${isAllAssigned})">
                            All
                        </button>
                    ` + classButtonsHTML;

                } else {
                    classButtonsHTML = '<span class="text-sm text-gray-500 italic">No classes for this day</span>';
                }

            let html = `
            <div class="border-2 border-gray-200 rounded-md shadow p-2 bg-white w-full">
                    <div class="pb-2 mb-2 flex justify-between items-center">
                        <div class="text-gray-700">${item.workoutname || 'N/A'} :</div>
                        <div class="space-x-2">
                            <button class="edit-warmups-btn text-black hover:text-gray-700" data-id="${item.id}" type="button">
                                <svg class="feather feather-edit" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>

                            <button class="delete-warmups-btn text-black hover:text-gray-700" data-id="${item.id}" type="button">
                                <svg fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <path d="M10,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625l12-12c0.390625-0.390625,1.0234375-0.390625,1.4140625,0s0.390625,1.0234375,0,1.4140625l-12,12C10.5117188,22.9023438,10.2558594,23,10,23z"/>
                                        <path d="M22,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688l-12-12c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625s1.0234375-0.390625,1.4140625,0l12,12c0.390625,0.390625,0.390625,1.0234375,0,1.4140625C22.5117188,22.9023438,22.2558594,23,22,23z"/>
                                    </g>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-2 text-gray-800 font-semibold">${item.workout_type} at ${item.weightvalu || 0}${item.unit} for ${item.reps} reps</div>


                    <div class="mt-4 flex items-center gap-2 border-t pt-3 w-full">
                        <span class="font-bold text-sm whitespace-nowrap">Assign to Class :</span>
                        <div class="flex flex-nowrap overflow-x-auto gap-2 pb-1 w-0 flex-1 thin-scrollbar">
                            ${classButtonsHTML}
                        </div>
                    </div>
            </div>
            `;

            container.append(html);

            // Bind the Edit button after appending
            container.find('.edit-warmups-btn').off('click').on('click', function() {
                const warmupsId = $(this).data('id');
                populateWarmupsForm(warmupsId);
            });

        });

    }

    // Toggle Assignment Function for Warmup
    function toggleAssignmentWarmup(workoutId, classId, timeString, isCurrentlyAssigned) {
        const action = isCurrentlyAssigned ? 'unassign' : 'assign';
        const confirmMsg = isCurrentlyAssigned
            ? `Are you sure you want to unassign this workout from the ${timeString} class?`
            : `Are you sure you want to assign this workout to the ${timeString} class?`;

        if (!confirm(confirmMsg)) return;

        const date = document.getElementById('selectdatewd').value;

        $.ajax({
            url: "/assign-workout-class", 
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                workout_id: workoutId,
                class_id: classId,
                type: 'warmup', // Correct type
                action: action, // 'assign' or 'unassign'
                date: date
            },
            success: function(response) {
                // Refresh data to show updated status
                getwarmup(date);
                // Also refresh classes list to show green icon if the function exists
                if (typeof getdateName === 'function') {
                    getdateName(date);
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert("Error updating assignment. Please check backend implementation.");
            }
        });
    }

    function toggleAllAssignmentsWarmup(workoutId, isAllAssigned) {
        const action = isAllAssigned ? 'unassign' : 'assign_all';
        
        const confirmMsg = isAllAssigned 
            ? "Are you sure you want to unassign this workout from ALL classes?" 
            : "Are you sure you want to assign this workout to ALL classes?";

        if(!confirm(confirmMsg)) return;

        const date = document.getElementById('selectdatewd').value;

        $.ajax({
            url: "/assign-workout-class",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                workout_id: workoutId,
                class_id: 'all',  // Special flag
                type: 'warmup',
                action: action, 
                date: date
            },
            success: function(response) {
                alert(response.message);
                getwarmup(date);
               // Also refresh classes list to show green icon if the function exists
                if (typeof getdateName === 'function') {
                    getdateName(date);
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert("Error assigning to all classes.");
            }
        });
    }

    // get editing details to form
    function populateWarmupsForm(warmupsId) {

        const data = allWarmupData.find(item => item.id == warmupsId);
        console.log('dataaaaaa', data);

        if (!data) {
            console.warn("No warmup data found for ID:", warmupsId);
            return;
        }

        const card = document.querySelector(`.edit-warmups-btn[data-id="${warmupsId}"]`).closest(
            '.border.border-green-900');
        if (card) {
            card.classList.remove('border-green-900');
            card.classList.remove('border');
            card.classList.add('border-red-600');
            card.classList.add('border-2');
        }
        // Set category and trigger onchange to load workouts
        const categorySelect = document.getElementById('categoryw_1');
        categorySelect.value = data.category_id;
        categorySelect.dispatchEvent(new Event('change')); // To trigger getworkoutS

        // Delay setting workouts to allow async options to load
        setTimeout(() => {
            const workoutSelect = document.getElementById('workoutw_1');
            workoutSelect.value = data.workout_id;
        }, 500); // Adjust based on how long getworkoutS takes to populate


        document.getElementById('weigthw_1').value = data.weight;
        document.getElementById('repsw_1').value = data.reps;
        document.getElementById('namew_1').value = data.workoutname;
        document.getElementById('warmup_id').value = warmupsId;


        document.getElementById('savebtnwarmup').textContent = "Save Edit";
        document.getElementById('clearwarmbtn').classList.remove('hidden');

    }

    // clear edit form
    function clearWarmupForm() {
        // Clear input fields
        $('#storeWarmupForm')[0].reset();
        document.getElementById('savebtnwarmup').innerHTML = "Save";
        // Show Clear button
        document.getElementById('clearwarmbtn').classList.add('hidden');
        const redCard = document.querySelector('.border-red-600.border-2');
        if (redCard) {
            redCard.classList.remove('border-red-600', 'border-2');
            redCard.classList.add('border', 'border-green-900');
        }
    }

    // search warmup
    function filterWarmup(date) {
        const dateOnClick = document.getElementById('selectdatewd').value;
        console.log('first', dateOnClick)

        //console.log('bbbbbbbb',buttonId)
        let categoryId = document.getElementById("categoryw_2").value;
        let exerciseId = document.getElementById("workoutw_2").value;
        let nameSearch = document.getElementById("namew_2").value;

        $.ajax({
            url: "/search-setwarmup",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                date: dateOnClick,
                name: nameSearch,
                category_id: categoryId,
                workout_id: exerciseId
            },
            success: function(response) {
                console.log("this is filteerd warmup response", response);
                allWarmupData = response.warmup;
                const warmupArray = Object.values(response.warmup);
                const categoryArray = Object.values(response.categoryOptions);
                const classesData = response.daily_classes || [];
                setwarmups(warmupArray, categoryArray, classesData);
                // Assuming response is an array of arrays
                // response.forEach(subArray => {
                //setstrengths(response.Strength, response.categoryOptions);
                // });
            },
            error: function(xhr) {
                alert("Error occurred: " + xhr.responseText);
            }
        });
    }

    function clearSearchWarmup() {
        const date = document.getElementById('selectdatewd').value;

        document.getElementById('categoryw_2').value = '';
        document.getElementById('workoutw_2').value = '';
        document.getElementById('namew_2').value = '';
        console.log('dateeeee', date);
        getwarmup(date);

    }

    // set warmup
    function setwarmup(array, categoryArray) {
        console.log(array);

        // Get the container where the information will be displayed
        const warmupInfoDiv = document.getElementById('warmup-info');

        // Clear any existing content
        warmupInfoDiv.innerHTML = '';

        const categoryOptionsArray = Object.values(categoryArray);

        // Build HTML content
        let htmlContent = '';
        array.forEach(item => {
            // Create a string with the desired HTML structure
            let categoryOptionsHTML = '<option value="" selected disabled>-- Select Category --</option>';

            // Add options from categoryOptionsArray
            categoryOptionsArray.forEach(category => {
                categoryOptionsHTML +=
                    `<option value="${category.id}">${category.category_name}</option>`;
            });

            // Add the selected category option
            categoryOptionsHTML +=
                `<option value="${item.category_id}" selected>${item.category_name}</option>`;

            htmlContent += `
            <form  id="warmupEdit_${item.id}">
            @csrf
                <input type="text" name="id" value="${item.id}" hidden>
                <div class="flex flex-col text-lg p-4 bg-gray-50 mr-8 rounded-md gap-4 mb-4">
                    <div class="flex-col w-full">
                        <div class="flex items-center border-b">
                            <label for="categorywe_${item.id}" class="w-60 block mb-1">Category <span class="text-red-500">*</span></label>
                            <select id="categorywe_${item.id}" name="categorywe_${item.id}" onchange="getworkoutw(this)"
                                class="w-1/3 px-3 py-3 border flex rounded mb-2">
                                ${categoryOptionsHTML}
                            </select>
                            <div class="flex justify-end items-center ml-auto mr-8">
                                <button type="button" class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base" onclick="editWarmup(${item.id})">Edit</button>
                            </div>
                        </div>
                        <div class="flex items-center border-b mt-2">
                            <label for="workoutwe_${item.id}" class="w-60 block mb-1">Workout <span class="text-red-500">*</span></label>
                            <select id="workoutwe_${item.id}" name="workoutwe_${item.id}" class="w-1/3 px-3 py-3 border flex rounded mb-2">
                                <option value="" selected disabled>-- Select Workout --</option>
                                <!-- Populate options dynamically -->
                                <option value="${item.workout_id}" selected>${item.workout_type}</option>
                            </select>
                        </div>
                        <div class="flex items-center border-b mt-2">
                            <label for="repswe_${item.id}" class="w-60 block mb-1">Volume <span class="text-red-500">*</span></label>
                            <div class="relative flex items-center max-w-[8rem] mb-2">
                                <!-- Decrement Button -->
                                <button type="button" onclick="decrement(this.parentNode.querySelector('input').id)"
                                    id="decrement-repswe_${item.id}"
                                    class="decrement-repswe_${item.id} bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                    <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M1 1h16" />
                                    </svg>
                                </button>
                                <!-- Input Field -->
                                <input type="text" id="repswe_${item.id}" name="repswe_${item.id}" data-input-counter
                                    class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                    placeholder="0" value="${item.reps}" readonly />
                                <!-- Increment Button -->
                                <button type="button" onclick="increment(this.parentNode.querySelector('input').id)"
                                    id="increment-repswe_${item.id}"
                                    class="increment-repswe_${item.id} bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                    <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M9 1v16M1 9h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center mt-2">
                            <label for="weightwe_${item.id}" class="w-60 block mb-1">Weight <span class="text-red-500">*</span></label>
                            <input type="number" id="weightwe_${item.id}" name="weightwe_${item.id}" value="${item.weight}"
                                class="w-1/3 px-3 py-3 border flex rounded ">
                            <label for="" class="border bg-white py-3 px-3 ">%</label>
                        </div>
                    </div>
                    <hr>
                </div>
            </form>
        `;
        });

        // Set the HTML content to the container
        warmupInfoDiv.innerHTML = htmlContent;
    }



    // Call getCategoryW on page load
    document.addEventListener('DOMContentLoaded', function() {
        getCategoryW();
    });
</script>
<script>
    function editWarmup(id) {
        // Construct the form ID dynamically
        const formId = `#warmupEdit_${id}`;
        const tab = document.getElementById('warmupTab');

        // Serialize the form data
        const formData = $(formId).serialize();

        // AJAX request
        $.ajax({
            url: '/update-warmup',
            type: 'POST',
            data: formData,
            success: function(response) {
                // Handle the response
                alert(response.message); // Display a success message or handle UI update
                if (tab) {
                    tab.click();
                }
                // Optionally, clear the form fields or update the UI
                $(formId)[0].reset();
            },
            error: function(xhr) {
                // Handle error
                console.error(xhr.responseText);
                alert('An error occurred while updating the data.');
            }
        });
    }
</script>

<script>
    let workoutCount = 1;

    function addAnotherWorkout() {
      workoutCount++;

      const original = document.querySelector('.workout-entry');
      const clone = original.cloneNode(true);

      // Update IDs and names
      clone.id = `workout-entry-${workoutCount}`;
      clone.querySelectorAll('[id], [name], label, select').forEach(el => {
        if (el.id) el.id = el.id.replace(/_\d+/, `_${workoutCount}`);
        if (el.name) el.name = el.name.replace(/_\d+/, `_${workoutCount}`);
        if (el.htmlFor) el.htmlFor = el.htmlFor.replace(/_\d+/, `_${workoutCount}`);
      });

      // Clear input values
      clone.querySelectorAll('input').forEach(input => {
        if (input.type === 'text' || input.type === 'number') input.value = '';
      });
      clone.querySelectorAll('select').forEach(select => {
        select.selectedIndex = 0;
      });

      // Add Remove button if not present
      if (!clone.querySelector('.remove-btn')) {
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.textContent = 'Remove';
        removeBtn.className = 'remove-btn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 ml-auto mt-1 mb-2';
        removeBtn.onclick = function () {
          removeWorkoutEntry(clone);
        };
        const removeContainer = document.createElement('div');
        removeContainer.className = 'flex justify-end mt-2';
        removeContainer.appendChild(removeBtn);
        clone.appendChild(removeContainer);
      }

      const addBtn = document.getElementById('add-another-btn');
      addBtn.parentNode.insertBefore(clone, addBtn);
    }

    function removeWorkoutEntry(entry) {
      const allEntries = document.querySelectorAll('.workout-entry');
      if (allEntries.length > 1) {
        entry.remove();
      } else {
        alert('At least one workout entry is required.');
      }
    }
</script>

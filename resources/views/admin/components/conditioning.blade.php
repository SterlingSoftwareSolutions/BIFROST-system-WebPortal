<div id="conditioning" hidden>
    {{-- hidden input field --}}
    <input type="text" name="selecttabc" id="selecttabc" hidden>
    <div class="flex justify-end items-center ml-auto mr-8 gap-5 -m-5 p-4 font-bold text-xl">
        {{-- <button class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base" onclick="deleteCond()"
            type="button">Clear</button> --}}
    </div>
    <div class="ui-block flex flex-col text-lg -m-5 px-4  mr-8 rounded-md">
        <div class="flex gap-5 justify-between ">
            {{-- Serach Section --}}
            <div class="flex-col w-full">
                <div class="bg-gray-50 p-4">
                    <div class="flex justify-center text-center items-center font-bold mb-3 text-2xl">Workout
                        List</div>
                    <div class="flex items-start space-x-6">
                        <!-- Category Field -->
                        <div class="flex flex-row items-center w-1/2 space-x-2">
                            <label for="categoryc_2" class="w-28">Category</label>
                            <select id="categoryc_2" name="categoryc_2" onchange="getworkoutC(this)"
                                class="flex-1 px-3 py-2 border rounded">
                                <option value="" selected disabled>-- Select Category --</option>
                            </select>
                        </div>

                        <!-- Exercise Field -->
                        <div class="flex flex-row items-center w-1/2 space-x-2">
                            <label for="workoutc_2" class="w-28">Exercise</label>
                            <select id="workoutc_2" name="workoutc_2" class="flex-1 px-3 py-2 border rounded">
                                <option value="" selected disabled>-- Select Exercise --</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-start space-x-6 mt-3">
                        <!-- Name Field -->
                        <div class="flex flex-row items-center flex-[2] space-x-2">
                            <label for="namec_2" class="w-28">Name Search</label>
                            <input type="text" id="namec_2" name="namec_2"
                                class="flex-1 px-3 py-2 border rounded mb-2">
                        </div>

                        <!-- Go Button -->
                        <div class="flex flex-row items-center flex-1 space-x-2">
                            <button id="addsetwarmup_1" onclick="filterConditioning(date)" type="button"
                                class="bg-black text-white py-3 px-4 rounded mb-2  text-base">Go</button>
                        </div>

                        <div class="flex flex-row items-center flex-1 space-x-2">
                            <button id="searchclearsetwarmup_1" onclick="clearSearchConditioning()" type="button"
                                class="bg-black text-white py-3 px-4 rounded mb-2  text-base">Clear</button>
                        </div>
                    </div>
                </div>

                <!-- Scroll Section -->
                <div id="setconditionings" class="mt-4 max-h-[600px] overflow-y-auto space-y-4">
                </div>

            </div>

            {{-- Form Section --}}
            <div class="flex-col w-full bg-gray-50 p-4 ">
                <form id="conditioning-form">
                    @csrf
                    <input type="text" name="selectdatec" id="selectdatec" hidden>

                    <input type="hidden" id="rounds" name="rounds" data-input-counter
                        class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" />

                    <input type="checkbox" id="amrapCheckbox" name="amrap"
                        class="h-11 w-11 px-3 py-3 border border-gray-300 rounded mb-2 checked:bg-blue-600 checked:border-transparent"
                        hidden>

                    <div class="duplicateUiC" data-index="1">
                        <div
                            class="ui-block flex flex-col text-lg pt-2 px-4 pb-4 bg-gray-50 mr-8 rounded-md gap-4 mb-4">
                            <div class="flex-col w-full">
                                <div class="flex justify-center text-center items-center font-bold mb-3 text-2xl">Create
                                </div>
                                <input type="hidden" id="conditioning_id" name="conditioning_id" value="">
                                <div class="flex items-center border-b mt-2">
                                    <label for="namec_1" class="w-60 block mb-1">Workout Name <span class="text-red-500">*</span></label>
                                    <input type="text" id="namec_1" name="namec_1"
                                        class="w-1/3 px-3 py-3 border flex rounded mb-2" required>

                                </div>
                                <div class="flex items-center border-b mt-2">
                                    <label for="timeTC_1" class="w-60 block mb-1">Time To Complete<span
                                            class="text-red-500">*</span></label>
                                    <div class="relative flex items-center max-w-[8rem] mb-2">
                                        <button type="button"
                                            onclick="adjustRestTime(this.parentNode.querySelector('input').id, -1)"
                                            class="decrement-timeTC bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" id="timeTC_1" name="timeTC_1" data-input-counter
                                            placeholder="00:00" value="04:00"
                                            class="bg-gray-50 timeTC border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                            placeholder="0" readonly required />
                                        <button type="button"
                                            onclick="adjustRestTime(this.parentNode.querySelector('input').id, 1)"
                                            class="increment-timeTC bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex items-center border-b mt-2">
                                    <label for="rounds_1" class="w-60 block mb-1">Rounds <span
                                            class="text-red-500">*</span></label>
                                    <div class="relative flex items-center max-w-[8rem] mb-2">
                                        <button type="button"
                                            onclick="decrement(this.parentNode.querySelector('input#rounds_1').id)"
                                            class="decrement-repsc bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" id="rounds_1" name="rounds_1" data-input-counter
                                            class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                            placeholder="0" readonly />
                                        <button type="button"
                                            onclick="incrementR(this.parentNode.querySelector('input#rounds_1').id)"
                                            class="increment-roundCond bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>
                                     <div class="ml-4 flex items-center w-24 space-x-2">
                                        <label for="amrapCheckboxCon" class="block">AMRAP</label>
                                        <input type="checkbox" id="amrapCheckboxCon" name="amrapCheckboxCon" class="h-11 w-11" onchange="syncCheckboxes()">
                                    </div>
                                    <div class="ml-4 flex items-center w-28 space-x-2">
                                        <label for="pyramidCheckboxCon" class="block">Pyramid</label>
                                        <input type="checkbox" id="pyramidCheckboxCon" name="pyramidCheckboxCon" class="h-9 w-9" onchange="togglePyramidSection()">
                                    </div>
                                    <!-- Hidden by default -->

                               </div>
                            <div id="pyramidSection" class="border-b mt-4 ml-2 hidden">
                            <div class="p-4 border border-gray-300 rounded bg-gray-50">
                                    <p class="mb-2 font-semibold">Pyramid SET</p>
                                    <div class="relative flex items-center max-w-[12rem] gap-2" id="duplicateRepsUIStrength">
                                        <!-- Optional extra input (first one) -->
                                        <input type="text" id="pyramidSet_1" name="pyramidSet_1" value="1"
                                            data-input-counter
                                            class="w-5 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                            placeholder="0" readonly required />

                                        <!-- Decrement Button -->
                                        <button type="button"
                                            onclick="decrement(this.parentNode.querySelector('input#pyreps_1').id)"
                                            class="decrement-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>

                                        <!-- REPS Counter Input -->
                                        <input type="text" id="pyreps_1" name="reps_1" data-input-counter
                                            class="w-7 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                            placeholder="0" readonly required />

                                        <!-- Increment Button -->
                                        <button type="button"
                                            onclick="increment(this.parentNode.querySelector('input#pyreps_1').id)"
                                            class="increment-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                        <label for="pyreps_1" class="text-sm mr-1">REPS</label>
                                       <div class="flex items-center gap-1 min-w-0">
                                            <label for="weigthPy_1" class="w-28 block mb-1 whitespace-nowrap">Training Load <span class="text-red-500">*</span></label>

                                            <select name="unit_1" class="border bg-white py-3.5 mb-2 rounded" onchange="toggleGenderInputs(this)">
                                            <option value="/10">/10</option>
                                            <option value="Cal">Cal</option>
                                            <option value="%">%</option>
                                            <option value="Kg">Kg</option>
                                            </select>

                                            <div class="relative h-[60px] min-w-0">
                                                <input type="number" name="weigthPy_1"
                                                class="w-20 py-3 border rounded mb-2 absolute top-0 left-0" required>

                                                <div class="flex space-x-2 items-center absolute top-0 left-0 invisible" data-gender-inputs>
                                                    <label for="pyramidmale_1" class="text-sm">M</label>
                                                    <input type="number" name="pyramidmale_1" class="w-20 px-2 py-2 border rounded mb-2">
                                                    <label for="pyramidfemale_1" class="text-sm">F</label>
                                                    <input type="number" name="pyramidfemale_1" class="w-20 px-2 py-2 border rounded mb-2">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="duplicate-sets-pyramid" id="duplicate-sets-pyramid_1"></div>
                                <div>
                                    <button id="duplicatesetPyramid_1" onclick="duplicatePyramid(this.id)"
                                        type="button"
                                        class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base">
                                        <i class="fas fa-plus text-[12px]"></i> Add set</button>
                                </div>
                            </div>
                            </div>


                    <div id="exerciseGroupsContainer">
                        <div class="exerciseGroup pb-4" id="exerciseGroup_1">
                        <!-- Category -->
                           <div class="flex items-center border-b mt-2">
                                                    <label for="categoryc_1" class="w-60 block mb-1">Category <span
                                                            class="text-red-500">*</span></label>
                                                    <select id="categoryc_1" name="categoryc_1" onchange="getworkoutC(this)"
                                                        class="w-1/3 px-3 py-3 border flex rounded mb-2 ">
                                                        <option value="" selected disabled>-- Select Category --</option>
                                                    </select>
                                                    <!-- Remove button (hidden for first) -->
                            <button type="button" onclick="removeExerciseGroup(this)"
                                class="text-red-500 font-semibold ml-4 hidden">Remove</button>
                                                </div>

                        <!-- Exercise -->
                         <div class="flex items-center border-b">
                            <label for="workoutc_1" class="w-60 block mb-1">Exercise <span class="text-red-500">*</span></label>
                                <select id="workoutc_1" name="workoutc_1"
                                class="w-1/3 px-3 py-3 border flex rounded mb-2 mt-2">
                                    <option value="" selected disabled>-- Select Exercise --</option>
                                </select>
                        </div>

                        <div class="flex items-center border-b mt-2">
                                    <label for="repsc_1" class="w-60 block mb-1">REPS<span
                                            class="text-red-500">*</span></label>
                                    <div class="relative flex items-center max-w-[8rem] mb-2">
                                        <button type="button"
                                            onclick="decrement(this.parentNode.querySelector('input#repsc_1').id)"
                                            class="decrement-repsc bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" id="repsc_1" name="repsc_1" data-input-counter
                                            class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                            placeholder="0" readonly />
                                        <button type="button"
                                            onclick="increment(this.parentNode.querySelector('input#repsc_1').id)"
                                            class="increment-repsc bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>
                        </div>

                        <!-- Training Load -->
                        <div class="flex items-center border-b mt-2">
                            <label class="w-60 block mb-1 whitespace-nowrap">Training Load <span class="text-red-500">*</span></label>
                            <div class="relative flex items-center max-w-[8rem] mb-2">
                                    <select name="unit_1" class="border bg-white py-3.5 mb-2 rounded" onchange="toggleGenderInputs(this)">
                                        <option value="/10">/10</option>
                                        <option value="Cal">Cal</option>
                                        <option value="%">%</option>
                                        <option value="Kg">Kg</option>
                                </select>
                                <div class="relative h-[60px] min-w-0">
                                    <input type="number" name="weigthc_1" class="w-20 py-3 border rounded mb-2 absolute top-0 left-0" required>
                                    <div class="flex space-x-2 items-center absolute top-0 left-1 invisible" data-gender-inputs>
                                        <label class="text-sm">M</label>
                                        <input type="number" name="male_1" class="w-20 px-2 py-2 border rounded mb-2">
                                        <label class="text-sm">F</label>
                                        <input type="number" name="female_1" class="w-20 px-2 py-2 border rounded mb-2">
                                    </div>
                                </div>
                            </div>

                        </div>
                        </div>
                </div>

                <!-- Add Button -->
                <div class="flex justify-end mt-4">
                    <button type="button" onclick="addExerciseGroup()"
                        class="bg-white border border-black text-black font-semibold py-2 px-4 rounded hover:bg-gray-100 hover:shadow-md transition duration-200 ease-in-out">
                        + Add
                    </button>
                </div>
                                {{-- <button class=" bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base "
                        onclick="addAnotherClick()" id="idfake_1">Another</button> --}}

                                {{-- <div class="flex items-center border-b ">
                                <label for="intensityc_1" class="w-60 block mb-1">Intensity</label>
                                <select id="intensityc_1" name="intensityc_1"
                                    class="w-1/3 px-3 py-3 border flex rounded my-2">
                                    <option value="" selected disabled>-- Select Intensity --</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="extreme">Extreme</option>
                                </select>
                                </div> --}}
                            </div>

                        </div>
                    </div>
                    <div class="showUIC" id="showUIC"></div>

                    <div class="flex flex-row justify-end gap-4 mt-5">
                        <button type="button" id="clearcbtn" onclick="clearCForm()"
                            class="bg-gray-200 text-gray-800 py-2 px-4 rounded hover:bg-gray-300 w-24 hidden">
                            Cancel
                        </button>

                        <button type="submit" id="savebtnc"
                            class="bg-[#FB1018] text-white py-2 px-4 rounded mr-8 hover:bg-red-700 w-24">
                            Save
                        </button>
                    </div>
                </form>

                <button class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base hidden" id="another"
                    type="button">Another</button>
            </div>
        </div>

    </div>

    {{-- fetch data --}}
    <div id="info-conditioning" class="info-conditioning">

    </div>

    <script>
        function syncCheckboxes() {
            var checkboxCon = document.getElementById('amrapCheckboxCon');
            var checkbox = document.getElementById('amrapCheckbox');

            if (checkboxCon.checked) {
                checkbox.checked = true; // Check the checkbox if checkboxCon is checked
            } else {
                checkbox.checked = false; // Uncheck the checkbox if checkboxCon is not checked
            }
        }

        function decrementR(id) {
            const input = document.getElementById(id);
            const val = document.getElementById("rounds");
            const currentValue = parseInt(input.value) || 0;
            if (currentValue > 0) {
                input.value = currentValue - 1;
                val.value = input.value;
            }
        }

        function incrementR(id) {
            const input = document.getElementById(id);
            const val = document.getElementById("rounds");
            const currentValue = parseInt(input.value) || 0;
            input.value = currentValue + 1;
            val.value = input.value;
        }

        function assgin() {
            const input = document.getElementById("roundCond");
            const val = document.getElementById("rounds");
            console.log("value :" + input.value);
            val.value = input.value;
        }
    </script>
    <script>
        //assign warmup to class
    $(document).on('change', '.conditionings-toggle', function() {
        const date = document.getElementById('selectdatec').value;
        const workoutId = $(this).data('workout-id');
        const workoutType = $(this).data('workout-type');
        const assigned = $(this).is(':checked') ? 1 : 0;

        // Get and conditionally remove class_id from local storage
        let selectedClassId = localStorage.getItem("selected_class_id");
        if (assigned) {
            if (!selectedClassId) {
                alert("Please select a class first.");
                $(this).prop('checked', false);
                return;
            }
        }

        // Only send class_id if assigning
        const payload = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            workout_id: workoutId,
            workout_type: workoutType,
            date: date,
            assigned: assigned
        };

        if (assigned) {
            payload.class_id = selectedClassId;
            localStorage.removeItem("selected_class_id");
        }

        $.ajax({
            url: "/assign-weightlifting-to-class",
            type: "POST",
            data: payload,
            success: function(response) {
                alert(response.message);
                getdateName(date); // Refresh classes or UI
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", xhr.responseText);
                alert("An error occurred while assigning the workout.");
            }
        });
    });

        //selected condtitioning delete
            $(document).on('click', '.delete-conditionings-btn', function() {
                const date = document.getElementById('selectdatec').value;
                const id = $(this).data('id');
                const confirmed = confirm("Are you sure you want to delete this Conditioning record?");

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
                            getconditioning(date);
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", error);
                        alert("An error occurred while deleting the Conditioning record.");
                    }
                });
            });

        $(document).ready(function() {
            $('#conditioning-form').on('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission
                const formData = $('#conditioning-form').serialize();
                console.log("call conditioning", formData);
                console.log("hellloooo");

                const conditioningId = $('#conditioning_id').val();
                const url = conditioningId ? `/update-conditioning` : '/store-conditioning';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $(this).serialize(), // Serialize form data
                    success: function(response) {
                        // Handle the response
                        alert(response.message);

                        // Clear input fields
                        $('#conditioning-form')[0].reset();
                        $('#conditioning_id').val('');

                        document.getElementById('savebtnc').textContent = "Save";
                        document.getElementById('clearcbtn').classList.add('hidden');
                        const showUIC = document.getElementById('showUIC');
                        showUIC.innerHTML = ''; // Clears the content inside the div

                        // Example: Switch tabs or update content
                        const tab = document.getElementById('conditioningTab');
                        if (tab) {
                            tab.click();
                        }
                    },
                    error: function(xhr) {
                        // Handle error
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>


</div>


{{-- UI Duplicate --}}
<script>
    function addAnotherClick() {
        // Simulate click on the "another" button
        document.getElementById('another').click();
        showMaxIdFake();
    }

    function createRemoveButton() {
        var removeButton = document.createElement("button");
        removeButton.innerText = "Remove";
        removeButton.classList.add(
            'removeBtn',
            'bg-[#FB1018]',
            'text-white',
            'py-2',
            'px-4',
            'rounded',
            'mb-2',
            'w-24',
            'flex-shrink-0'
        );
        removeButton.addEventListener("click", function() {
            this.parentElement.remove();
            showMaxIdFake();
        });

        return removeButton;
    }

    function updateAttributes(element, index) {
        element.querySelectorAll('input, select, button').forEach(function(el) {
            var baseName = el.name.split('_')[0];
            var baseId = el.id.split('_')[0];
            el.name = baseName + '_' + index;
            el.id = baseId + '_' + index;
        });
    }

    function clearData(element) {
        element.querySelectorAll('input, select, textarea').forEach(function(el) {
            if (el.tagName.toLowerCase() === 'input') {
                el.value = '';
            } else if (el.tagName.toLowerCase() === 'select') {
                el.selectedIndex = 0;
            } else if (el.tagName.toLowerCase() === 'textarea') {
                el.value = '';
            }
        });

        // Set the value for the element with the class 'timeTC' to '04:00'
        let timeElement = element.querySelector('.timeTC');
        if (timeElement) {
            timeElement.value = '04:00';
        }

    }

    function cloneAndAppendUI() {
        const originalElement = document.querySelector('.duplicateUiC');

        if (!originalElement) {
            console.error('Original element not found.');
            return;
        }

        // Clone the element
        const clone = originalElement.cloneNode(true);

        // Determine the new index based on the current number of elements
        const index = document.querySelectorAll('.duplicateUiC').length + 1;

        // Clear data in the cloned element
        clearData(clone);
        // Update the data-indexc attribute
        clone.setAttribute('data-indexc', index);

        // Update attributes of input, select, and button elements
        updateAttributes(clone, index);

        // Create and add the remove button
        const removeButton = createRemoveButton();
        clone.appendChild(removeButton);

        // Append the clone to the .showUIC container
        document.querySelector('.showUIC').appendChild(clone);
    }

    function initializeEventListeners() {
        document.getElementById('another').addEventListener('click', cloneAndAppendUI);
    }
    // Initialize event listeners once the DOM is fully loaded
    document.addEventListener('DOMContentLoaded', initializeEventListeners);

    function showMaxIdFake() {
        console.log("object")
        const buttons = document.querySelectorAll('button[id^="idfake_"]');
        let maxId = 0;
        let maxButton = null;

        buttons.forEach(button => {
            const idNumber = parseInt(button.id.split('_')[1]);
            if (idNumber > maxId) {
                maxId = idNumber;
                maxButton = button;
            }
            button.style.display = 'none'; // Hide all buttons
        });

        if (maxButton) {
            maxButton.style.display = 'block'; // Show only the button with the max ID
        }
    }

    // Add event listener to the "another" button
    document.getElementById('another').addEventListener('click', function() {
        console.log('Another button clicked');
        // Add your code for the "another" button click event here
    });

    // Initial call to show only the button with the highest ID
    showMaxIdFake();
</script>


<script>
    // {{-- Get category --}}
    function getcategoryC() {
        const selectTab = "conditioning";

        $.ajax({
            url: "/getCategory",
            type: "POST",
            data: {
                tab: selectTab,
                _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
            },
            success: function(response) {
                // console.log(response);
                // Extract the category_options array from the response
                const categoryOptions = response.category_options || [];

                // Clear existing options in the select elements before adding new ones
                ['categoryc_1','categoryc_2'].forEach(id => {
                    const categorySelect = document.getElementById(id);
                    categorySelect.innerHTML =
                        '<option value="" selected disabled>-- Select Category --</option>';
                });

                // Loop through each category_option and call the setCategory() function
                categoryOptions.forEach(option => {
                    setCategory(option.id, option.category_name, 'categoryc_1');
                    setCategory(option.id, option.category_name, 'categoryc_2');
                });

                // Optional: Sort the options alphabetically if needed
                ['categoryc_1','categoryc_2'].forEach(id => {
                    sortSelectOptions(document.getElementById(id));
                });

                // Set the default selected category to "hinge" (case-insensitive) and trigger onchange
                ['categoryc_1','categoryc_2'].forEach(id => {
                    const categorySelect = document.getElementById(id);
                    const options = categorySelect.options;
                    const defaultCategory = "Cardio".toLowerCase();
                    let optionFound = false;

                    for (let i = 0; i < options.length; i++) {
                        if (options[i].text.toLowerCase() === defaultCategory) {
                            categorySelect.selectedIndex = i;
                            optionFound = true;
                            break;
                        }
                    }

                    // Trigger the onchange event if the option was found
                    if (optionFound) {
                        const event = new Event('change', {
                            bubbles: true
                        });
                        categorySelect.dispatchEvent(event);
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error(error); // Handle error
            }
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        getcategoryC();
    });
    // {{-- Get workout --}}
    function getworkoutC(selectElement) {
        console.log(selectElement);
        const tab = "conditioning"; // Assuming the tab is predefined for warmup
        const selectId = selectElement.value; // Get the value of the selected element

        // Split the ID of the select element by underscores
        const idParts = selectElement.id.split('_');

        // Determine the workout select element ID based on the category type
        let workoutSelectId;
        if (idParts[0] === 'categoryc') {
            workoutSelectId = `workoutc_${idParts[1]}`;
        } else if (idParts[0] === 'categorycon') {
            workoutSelectId = `workoutcon_${idParts[1]}`;
        } else {
            console.error('Unknown category type');
            return;
        }

        // Ensure the workout select element exists before performing operations
        const workoutSelect = document.getElementById(workoutSelectId);
        if (!workoutSelect) {
            console.error('Workout select element not found');
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
                clearOptions(workoutSelect);

                workouts.forEach(workout => {
                    setWorkout(workoutSelect, workout.id, workout.workout);
                });
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
            }
        });
    }
    // Time to complete
    function adjustRestTime(id, adjustment) {
        // Get the current time value
        let input = document.getElementById(id); // Ensure you get the input element using the id
        let [minutes, seconds] = input.value.split(':').map(Number);

        // Convert to total seconds
        if (isNaN(minutes)) minutes = 0;
        if (isNaN(seconds)) seconds = 0;
        let totalSeconds = (minutes * 60) + seconds;

        // Update the time based on adjustment
        totalSeconds += adjustment;
        if (totalSeconds < 0) totalSeconds = 0; // Prevent negative values

        // Convert back to minutes and seconds
        minutes = Math.floor(totalSeconds / 60);
        seconds = totalSeconds % 60;

        // Format the value as MM:SS
        input.value = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }
</script>

{{-- data fetch and update --}}
<script>
    let allConditioningData = [];
    function getconditioning(date) {
        var selectDate = date;
        console.log(selectDate);
        $.ajax({
            url: "/getConditioning",
            type: "POST",
            data: {
                date: selectDate,
                _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
            },
            success: function(response) {
                console.log(response);
                allConditioningData = response.result;
                setConditionings(response.result, response.categoryOptions);
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    }
    function setConditionings(conditionings, categoryOptions) {
        const container = $("#setconditionings"); // Replace with your actual container class or ID
        container.empty(); // Clear previous content

        conditionings.forEach((item, index) => {

            let html = `
            <div class="border border-green-900 rounded-2xl shadow p-2 bg-white mx-10">
                <div class="border border-black rounded-xl shadow p-4 bg-white">
                    <div class="pb-2 mb-2 flex justify-between items-center">
                        <div class="text-gray-700">${item.workoutname || 'N/A'} :</div>
                        <div class="space-x-2">
                            <button class="edit-conditionings-btn" data-id="${item.id}" type="button">
                                <svg class="feather feather-edit" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>

                            <button class="delete-conditionings-btn" data-id="${item.id}" type="button">
                                <svg fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <path d="M10,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625l12-12c0.390625-0.390625,1.0234375-0.390625,1.4140625,0s0.390625,1.0234375,0,1.4140625l-12,12C10.5117188,22.9023438,10.2558594,23,10,23z"/>
                                        <path d="M22,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688l-12-12c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625s1.0234375-0.390625,1.4140625,0l12,12c0.390625,0.390625,0.390625,1.0234375,0,1.4140625C22.5117188,22.9023438,22.2558594,23,22,23z"/>
                                    </g>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-2 text-gray-800 font-semibold">${item.workout_type} at ${item.weight || 0}${item.unit} for ${item.roundsnamrap} sets of ${item.reps} reps in ${item.time_to_complete} mins</div>


                    <div class="mt-4 flex justify-end">
                        <p class="mr-4 font-bold">Assign Workout to Class</p>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="" class="sr-only peer conditionings-toggle" data-workout-id="${item.id}" data-workout-type="conditioning" ${item.is_assigned ? 'checked' : ''}>
                             <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600 dark:peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </div>
            </div>
            `;

            container.append(html);

            // Bind the Edit button after appending
            container.find('.edit-conditionings-btn').off('click').on('click', function() {
                const conditioningsId = $(this).data('id');
                populateConditioningsForm(conditioningsId);
            });
        });
    }

    //populate conditioning form
    // get editing details to form
    function populateConditioningsForm(conditioningsId) {

        const data = allConditioningData.find(item => item.id == conditioningsId);
        console.log('dataaaaaa', data);

        if (!data) {
            console.warn("No conditionings data found for ID:", conditioningsId);
            return;
        }

        const card = document.querySelector(`.edit-conditionings-btn[data-id="${conditioningsId}"]`).closest(
            '.border.border-green-900');
        if (card) {
            card.classList.remove('border-green-900');
            card.classList.remove('border');
            card.classList.add('border-red-600');
            card.classList.add('border-2');
        }
        // Set category and trigger onchange to load workouts
        const categorySelect = document.getElementById('categoryc_1');
        categorySelect.value = data.category_id;
        categorySelect.dispatchEvent(new Event('change')); // To trigger getworkoutS

        // Delay setting workouts to allow async options to load
        setTimeout(() => {
            const workoutSelect = document.getElementById('workoutc_1');
            workoutSelect.value = data.workout_id;
        }, 500); // Adjust based on how long getworkoutS takes to populate


        document.getElementById('weigthc_1').value = data.weight;
        document.getElementById('repsc_1').value = data.reps;
        document.getElementById('namec_1').value = data.workoutname;
        document.getElementById('unit_1').value = data.unit;
        document.getElementById(data.amrap == 1 ? 'amrapCheckboxCon' : 'roundCond').value = data.amrap == 1 ? data.amrap : data.rounds ?? '';
        document.getElementById('timeTC_1').value = data.time_to_complete;
        document.getElementById('intensityc_1').value = data.intensity;
        document.getElementById('conditioning_id').value = conditioningsId;

        document.getElementById('savebtnc').textContent = "Save Edit";
        document.getElementById('clearcbtn').classList.remove('hidden');

    }

    // clear edit form
    function clearCForm() {
        // Clear input fields
        $('#conditioning-form')[0].reset();
        document.getElementById('savebtnc').innerHTML = "Save";
        // Show Clear button
        document.getElementById('clearcbtn').classList.add('hidden');
        const redCard = document.querySelector('.border-red-600.border-2');
        if (redCard) {
            redCard.classList.remove('border-red-600', 'border-2');
            redCard.classList.add('border', 'border-green-900');
        }
    }

    function setConditioning(array, categoryOptionsArray) {
        console.log(categoryOptionsArray);
        // Get the container where the information will be displayed
        const conditioningDiv = document.getElementById('info-conditioning');
        // const rounds = document.getElementById('roundCond');
        // rounds.value = array.length > 0 && array[0].rounds ? array[0].rounds : 0;
        // Clear any existing content
        assgin();

        // checkbox
        var checkbox = document.getElementById('amrapCheckboxCon');
        if (array.length > 0) {
            if (array[0].amrap == 1) {
                checkbox.checked = true; // Check the checkbox if amrap is 1
            } else {
                checkbox.checked = false; // Uncheck the checkbox if amrap is not 1
            }
        } else {
            checkbox.checked = false; // Uncheck the checkbox if array is empty
        }

        // Retrieve the value of the input field with ID 'roundCond'
        const roundCondValue = document.getElementById('roundCond').value;

        conditioningDiv.innerHTML = '';

        // Build HTML content
        let htmlContent = '';
        array.forEach(item => {
            // Create a string with the desired HTML structure
            let categoryOptionsHTML = '<option value="" selected disabled>-- Select Category --</option>';

            // Add options from categoryOptionsArray
            categoryOptionsArray.forEach(category => {
                categoryOptionsHTML +=
                    `<option value="${category.id}" ${category.id === item.category_id ? 'selected' : ''}>${category.category_name}</option>`;
            });

            htmlContent += `
            <input type="text" name="id" value="${item.id}" hidden>
            <div class="flex flex-col text-lg p-4 bg-gray-50 mr-8 rounded-md gap-4 mb-4">
                <div class="flex-col w-full">
                    <div class="flex items-center border-b">
                        <label for="categorycon_${item.id}" class="w-60 block mb-1">Category <span class="text-red-500">*</span></label>
                        <select id="categorycon_${item.id}" name="categorycon_${item.id}" onchange="getworkoutC(this)"
                            class="w-1/3 px-3 py-3 border flex rounded mb-2">
                            ${categoryOptionsHTML}
                        </select>
                        <div class="flex justify-end items-center ml-auto mr-8">
                            <button class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base" type="button" onclick="conEdit(${item.id})">Edit</button>
                        </div>
                    </div>
                    <div class="flex items-center border-b mt-2">
                        <label for="workoutcon_${item.id}" class="w-60 block mb-1">Workout <span class="text-red-500">*</span></label>
                        <select id="workoutcon_${item.id}" name="workoutcon_${item.id}" class="w-1/3 px-3 py-3 border flex rounded mb-2">
                            <option value="" selected disabled>-- Select Workout --</option>
                            <!-- Populate options dynamically -->
                            <option value="${item.workout_id}" selected>${item.workout_type}</option>
                        </select>
                    </div>
                    <div class="flex items-center border-b mt-2">
                        <label for="repscon_${item.id}" class="w-60 block mb-1">REPS <span class="text-red-500">*</span></label>
                        <div class="relative flex items-center max-w-[8rem] mb-2">
                            <!-- Decrement Button -->
                            <button type="button" onclick="decrement(this.parentNode.querySelector('input').id)"
                                id="decrement-repscon_${item.id}"
                                class="decrement-repscon_${item.id} bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M1 1h16" />
                                </svg>
                            </button>
                            <!-- Input Field -->
                            <input type="text" id="repscon_${item.id}" name="repscon_${item.id}" data-input-counter
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                placeholder="0" value="${item.reps}" readonly />
                            <!-- Increment Button -->
                            <button type="button" onclick="increment(this.parentNode.querySelector('input').id)"
                                id="increment-repscon_${item.id}"
                                class="increment-repscon_${item.id} bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M9 1v16M1 9h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center border-b mt-2">
                        <label for="weightcon_${item.id}" class="w-60 block mb-1">Weight <span class="text-red-500">*</span></label>
                        <input type="number" id="weightcon_${item.id}" name="weightcon_${item.id}" value="${item.weight}"
                            class="w-1/3 px-3 py-3 border flex rounded mb-2">
                        <select id="unitcon_${item.id}" name="unit_${item.id}" class="border bg-white py-3 px-3 mb-2 rounded">
                            <option value="%" ${item.unit === '%' ? 'selected' : ''}>%</option>
                            <option value="Kg" ${item.unit === 'Kg' ? 'selected' : ''}>Kg</option>
                        </select>
                    </div>
                    <div class="flex items-center border-b mt-2">
                    <label for="timeTCcon_${item.id}" class="w-60 block mb-1">Time To Complete <span class="text-red-500">*</span></label>
                    <div class="relative flex items-center max-w-[8rem] mb-2">
                        <!-- Decrement Button -->
                        <button type="button" onclick="adjustRestTime(this.parentNode.querySelector('input').id, -1)"
                            id="decrement-timeTCcon_${item.id}"
                            class="decrement-timeTCcon_${item.id} bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M1 1h16" />
                            </svg>
                        </button>
                        <!-- Input Field -->
                        <input type="text" id="timeTCcon_${item.id}" name="timeTCcon_${item.id}" data-input-counter
                            class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                            placeholder="0" value="${item.complete_time}" readonly />
                        <!-- Increment Button -->
                        <button type="button" onclick="adjustRestTime(this.parentNode.querySelector('input').id, 1)"
                            id="increment-timeTCcon_${item.id}"
                            class="increment-timeTCcon_${item.id} bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M9 1v16M1 9h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                </div>
                <hr>
            </div>

        `;
        });
        conditioningDiv.innerHTML = htmlContent;
    }

    // search Conditioning
    function filterConditioning(date) {
        const dateOnClick = document.getElementById('selectdatec').value;
        console.log('first', dateOnClick)

        //console.log('bbbbbbbb',buttonId)
        let categoryId = document.getElementById("categoryc_2").value;
        let exerciseId = document.getElementById("workoutc_2").value;
        let nameSearch = document.getElementById("namec_2").value;

        $.ajax({
            url: "/search-conditioning",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                date: dateOnClick,
                name: nameSearch,
                category_id: categoryId,
                workout_id: exerciseId
            },
            success: function(response) {
                console.log("this is filteerd Conditioning response", response);
                allConditioningData = response.conditioning;
                const conditioningArray = Object.values(response.conditioning);
                const categoryArray = Object.values(response.categoryOptions);
                setConditionings(conditioningArray, categoryArray);
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

    // search conditioning
    function clearSearchConditioning() {
        const date = document.getElementById('selectdatec').value;

        document.getElementById('categoryc_2').value = '';
        document.getElementById('workoutc_2').value = '';
        document.getElementById('namec_2').value = '';
        console.log('dateeeee', date);
        getconditioning(date);

    }
    // edit
    function conEdit(id) {
        console.log(id);

        // Fetch values from the DOM elements
        const category = document.getElementById(`categorycon_${id}`).value;
        const workout = document.getElementById(`workoutcon_${id}`).value;
        const reps = document.getElementById(`repscon_${id}`).value;
        const weight = document.getElementById(`weightcon_${id}`).value;
        const timeToComplete = document.getElementById(`timeTCcon_${id}`).value;
        const unit = document.getElementById(`unitcon_${id}`).value;
        const rounds = document.getElementById('roundCond').value;
        const date = document.getElementById('selectdatec').value;

        // Get the checkbox state
        const amrapCheckbox = document.getElementById('amrapCheckboxCon');
        const isChecked = amrapCheckbox.checked;
        console.log(isChecked);

        // Configure and send the AJAX request
        $.ajax({
            url: '/update-conditioning',
            type: 'POST',
            data: {
                id: id,
                category: category,
                workout: workout,
                reps: reps,
                weight: weight,
                timeToComplete: timeToComplete,
                rounds: rounds,
                date: date,
                unit: unit,
                isChecked: isChecked,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Handle successful response
                console.log('Success:', response);
                alert(response.message);
            },
            error: function(xhr, status, error) {
                // Handle error response
                console.error('Error:', status, error);
            }
        });
    }
    // delete
    function deleteCond() {
        const date = document.getElementById('selectdatec').value;
        $.ajax({
            url: '/delete-conditioning',
            type: 'POST',
            data: {
                date: date,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert(response.message);
                getconditioning(date);
                // Optionally, refresh the page or update the UI to reflect the deletion
            },
            error: function(xhr) {
                alert('An error occurred while deleting the records.');
            }
        });
    }
</script>

<script>
let groupCount = 1;

function increment(id) {
    const input = document.getElementById(id);
    input.value = parseInt(input.value || 0) + 1;
}

function decrement(id) {
    const input = document.getElementById(id);
    const current = parseInt(input.value || 0);
    if (current > 0) input.value = current - 1;
}

function toggleGenderInputs(selectElement) {
    const container = selectElement.closest('div'); // Or more specific if needed
    const defaultInput = container.querySelector('input[type="number"][name^="weigthPy_"]') || container.querySelector('input[type="number"][name^="weigthc_"]');
    const genderDiv = container.querySelector('[data-gender-inputs]');

    const selected = selectElement.value;

    if (selected === 'cal' || selected === 'kg') {
        genderDiv.classList.remove('invisible');
        defaultInput.classList.add('invisible');
        defaultInput.removeAttribute('required');
    } else {
        genderDiv.classList.add('invisible');
        defaultInput.classList.remove('invisible');
        defaultInput.setAttribute('required', 'true');
    }
}


function addExerciseGroup() {
    groupCount++;
    const container = document.getElementById('exerciseGroupsContainer');
    const original = document.getElementById('exerciseGroup_1');
    const clone = original.cloneNode(true);

    clone.id = `exerciseGroup_${groupCount}`;

    // Update all IDs & names
    clone.querySelectorAll('[name], [id]').forEach(el => {
        if (el.name) el.name = el.name.replace(/\d+$/, groupCount);
        if (el.id) el.id = el.id.replace(/\d+$/, groupCount);
        if (el.tagName === 'INPUT') el.value = ''; // clear inputs
    });

    const repsInput = clone.querySelector('input[id^="repsc_"]');
    if (repsInput) {
        const inputId = repsInput.id;

        const decBtn = clone.querySelector('button.decrement-repsc');
        const incBtn = clone.querySelector('button.increment-repsc');

        if (decBtn) decBtn.setAttribute('onclick', `decrement('${inputId}')`);
        if (incBtn) incBtn.setAttribute('onclick', `increment('${inputId}')`);
    }

    // Show remove button
    const removeBtn = clone.querySelector('button[onclick^="removeExerciseGroup"]');
    removeBtn.classList.remove('hidden');

    container.appendChild(clone);
}

function removeExerciseGroup(button) {
    const group = button.closest('.exerciseGroup');
    group.remove();
}
</script>

<script>
  function togglePyramidSection() {
    const checkbox = document.getElementById('pyramidCheckboxCon');
    const section = document.getElementById('pyramidSection');
    section.classList.toggle('hidden', !checkbox.checked);
  }

  let setCounterPyramid = 1;
    function duplicatePyramid(id) {
        console.log(id)
        const idPartfind = id.split('_');

        // Get the remaining part after the base
        const remainingfind = idPartfind.slice(1).join('_');
        console.log("idsss " + remainingfind)

        setCounterPyramid++;
        const originalSet = document.getElementById(id);
        console.log(originalSet);

        if (!originalSet) {
            console.error('Original set element with id "' + id + '" not found');
            return;
        }

        // Clone the original set
        const clone = originalSet.cloneNode(true);

        // Update the id and name attributes of the input field
        const input = clone.querySelector('input');
        if (input) {
            input.id = `sets_${remainingfind}${setCounterPyramid}`;
            input.name = `sets_${remainingfind}${setCounterPyramid}`;
            input.value = '0';
        }

        // Add remove button
        const removeButton = document.createElement('button');
        removeButton.textContent = 'Remove';
        removeButton.className = 'remove-set-strength bg-red-500 text-white p-2 rounded';
        removeButton.onclick = function() {
            clone.remove();
        };

        // Append the remove button to the clone
        const buttonContainer = document.createElement('div');
        buttonContainer.className = 'flex justify-end mb-2'; // Ensure the button is aligned properly
        buttonContainer.appendChild(removeButton);
        clone.appendChild(buttonContainer);

        // Create a new element from the setui string and append it to the duplicate-sets container
        const setPyramidElement = document.createElement('div');
        setPyramidElement.innerHTML = `
                                    <div class="flex items-center sets-view mt-1 flex-nowrap">
                                    <div class="block mb-1"></div>
                                    <div class="flex items-center max-w-[12rem] gap-2">
                                        <!-- reps inputs/buttons -->
                                        <input type="text" id="sets_${remainingfind}${setCounterPyramid}" name="sets_${remainingfind}${setCounterPyramid}" value="${setCounterPyramid}" data-input-counter
                                        class="w-5 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                        placeholder="0" readonly required />
                                        <!-- decrement button -->
                                        <button type="button"
                                        onclick="decrement(this.parentNode.querySelector('input#reps_${remainingfind}${setCounterPyramid}').id)"
                                        class="decrement-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 18 2">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                        </svg>
                                        </button>
                                        <!-- reps input -->
                                        <input type="text" id="reps_${remainingfind}${setCounterPyramid}" name="reps_${remainingfind}${setCounterPyramid}" data-input-counter
                                        class="w-7 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                        placeholder="0" readonly required />
                                        <!-- increment button -->
                                        <button type="button"
                                        onclick="increment(this.parentNode.querySelector('input#reps_${remainingfind}${setCounterPyramid}').id)"
                                        class="increment-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 18 18">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 1v16M1 9h16" />
                                        </svg>
                                        </button>
                                        <label for="sets_${remainingfind}${setCounterPyramid}" class="text-sm mr-2 whitespace-nowrap">REPS</label>
                                    </div>

                                    <!-- Training Load + Remove button container -->
                                    <div class="flex items-center mt-2 gap-3 flex-grow min-w-0 justify-between">
                                    <div class="flex items-center gap-1 min-w-0">
                                    <label class="w-28 block mb-1 whitespace-nowrap ml-1">Training Load <span class="text-red-500">*</span></label>

                                    <select name="unit_${remainingfind}${setCounterPyramid}" class="border bg-white py-3.5 mb-2 rounded" onchange="toggleGenderInputs(this)">
                                        <option value="/10">/10</option>
                                            <option value="Cal">Cal</option>
                                            <option value="%">%</option>
                                            <option value="Kg">Kg</option>
                                    </select>

                                    <div class="relative h-[60px] min-w-0">
                                        <input type="number" name="weigthPy_${remainingfind}${setCounterPyramid}"
                                        class="w-20 py-3 border rounded mb-2 absolute top-0 left-0" required>

                                        <div class="flex space-x-2 items-center absolute top-0 left-0 invisible" data-gender-inputs>
                                        <label class="text-sm">M</label>
                                        <input type="number" name="pyramidmale_${remainingfind}${setCounterPyramid}" class="w-20 px-2 py-2 border rounded mb-2">
                                        <label class="text-sm">F</label>
                                        <input type="number" name="pyramidfemale_${remainingfind}${setCounterPyramid}" class="w-20 px-2 py-2 border rounded mb-2">
                                        </div>
                                    </div>
                                    </div>

                                    <button type="button"
                                    class="remove-set-strength bg-red-500 text-white py-2 px-4 rounded flex-shrink-0 whitespace-nowrap -mt-2">
                                    X
                                    </button>
                                </div>
                                </div>
        `;
        // Add functionality to the remove button
        setPyramidElement.querySelector('.remove-set-strength').addEventListener('click', function() {
            setPyramidElement.remove();
        });

        // Find the container element with class 'duplicate-sets' using parentNode traversal
        const container = originalSet
            .closest(
                '.border-b').querySelector('.duplicate-sets-pyramid');
        console.log(container);

        if (container) {
            container.appendChild(setPyramidElement);
        } else {
            console.error('Container element with class "duplicate-sets-pyramid" not found');
        }

        console.log(`Duplicated Set ID: ${input ? input.id : 'N/A'}`);

    }
</script>

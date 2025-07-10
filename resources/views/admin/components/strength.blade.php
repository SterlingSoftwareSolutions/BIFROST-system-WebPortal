<div id="strength" hidden>
    <input type="text" name="selecttabss" id="selecttabss" hidden>
    {{-- hidden input field --}}
    <div class="flex gap-5 p-4 mr-8 rounded-md mb-4 font-bold text-xl">
        <div class="flex justify-center text-center items-center w-1/2">Workout List</div>
        <div class="flex justify-center text-center items-center w-1/2">Create</div>
        <form id="deleteforstrenght">
            @csrf
            @method('DELETE')
            <input type="text" name="selectdatestrenghtDelete" id="selectdatestrenghtDelete" hidden>
            {{-- <button class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base">Clear</button> --}}
        </form>
        <script>
            //selected strength delete
            $(document).on('click', '.delete-strength-btn', function () {
                const date = document.getElementById('selectdatestrenghtDelete').value;
                const id = $(this).data('id');
                const confirmed = confirm("Are you sure you want to delete this strength record?");

                if (!confirmed) return;

                $.ajax({
                    url: "/delete-strength",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            getstrength(date); // Or whatever date picker you're using
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX error:", error);
                        alert("An error occurred while deleting the strength record.");
                    }
                });
            });

            $(document).ready(function() {
                const date = document.getElementById('selectdatestrenghtDelete').value;
                $('#deleteforstrenght').on('submit', function(event) {
                    event.preventDefault(); // Prevent the default form submission

                    $.ajax({
                        url: '{{ route('deletestrength') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE',
                            selectdatestrenghtDelete: $('#selectdatestrenghtDelete').val()
                        },
                        success: function(response) {
                            // Handle the response
                            // alert(response.status);
                            getstrength(date);
                            // Optionally, update the UI to reflect the changes
                        },
                        error: function(xhr) {
                            // Handle error
                            console.error(xhr.responseText);
                        }
                    });
                });
            });
            $(document).ready(function() {
                $('#storeformss').on('submit', function(event) {
                    event.preventDefault(); // Prevent the default form submission

                    $.ajax({
                        url: '/save-strength',
                        type: 'POST',
                        data: $(this).serialize(), // Serialize form data
                        success: function(response) {
                            // Clear input fields
                            $('#storeformss')[0].reset();

                            // Clear the clone display container
                            const cloneDisplayContainerStrength = document.getElementById(
                                'cloneDisplayContainerStrength');
                            if (cloneDisplayContainerStrength) {
                                cloneDisplayContainerStrength.innerHTML = '';
                            }
                            alert(response.message);
                            var duplicateSets = document.querySelector(".duplicate-sets-strength");
                            if (duplicateSets) {
                                duplicateSets.innerHTML = ""; // Clear content
                            }

                            var altDuplicateSets = document.querySelector(
                                ".altduplicate-setsstrength");
                            if (altDuplicateSets) {
                                altDuplicateSets.innerHTML = ""; // Clear content
                            }

                            // Trigger the tab click
                            const tab = document.getElementById('strenghtTab');
                            if (tab) {
                                tab.click();
                            }

                            // Optionally, update the UI to reflect the changes
                            // alert(response.message); // Uncomment if you want to show a message
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
    {{-- display  strength --}}
    <div id="strength-container"></div>
    <form id="storeformss">
        @csrf
        <input type="text" name="selectdates" id="selectdates" hidden>
        <input type="text" name="selecttabs" id="selecttabs" hidden>

        <div class="duplicateUiStrength" data-index="1" id="uiContainerStrength">
            <div class="ui-block flex flex-col text-lg p-4 mr-8 rounded-md gap-4 mb-4 ">

                <!-- Your UI block content here -->

                <div class="flex gap-6 justify-between ">
                    {{-- start alternative --}}
                    <div class="flex-col w-full">
                        <div class="bg-gray-50 p-4">
                            <div class="flex items-start space-x-6">
                                <!-- Category Field -->
                                <div class="flex flex-row items-center w-1/2 space-x-2">
                                    <label for="categorys_2" class="w-28">Category</label>
                                    <select id="categorys_2" name="categorys_2" onchange="getworkoutS(this)"
                                        class="flex-1 px-3 py-2 border rounded">
                                        <option value="" selected disabled>-- Select Category --</option>
                                    </select>
                                </div>

                                <!-- Exercise Field -->
                                <div class="flex flex-row items-center w-1/2 space-x-2">
                                    <label for="workouts_2" class="w-28">Exercise</label>
                                    <select id="workouts_2" name="workouts_2"
                                        class="flex-1 px-3 py-2 border rounded">
                                        <option value="" selected disabled>-- Select Exercise --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-start space-x-6 mt-3">
                                <!-- Name Field -->
                                <div class="flex flex-row items-center flex-[2] space-x-2">
                                    <label for="name_1" class="w-28">Name Search</label>
                                    <input type="text" id="name_1" name="name_1"
                                        class="flex-1 px-3 py-2 border rounded mb-2">
                                </div>

                                <!-- Go Button -->
                                <div class="flex flex-row items-center flex-1 space-x-2">
                                    <button id="addsetstrength_1" onclick="filterStrength(date)"
                                        type="button"
                                        class="bg-black text-white py-3 px-4 rounded mb-2  text-base">Go</button>
                                </div>
                            </div>
                        </div>

                        <!-- Scroll Section -->
                        <div id="setstrengths" class="mt-4 max-h-[600px] overflow-y-auto space-y-4">
                            {{-- <!-- Assigned border / one card -->
                            <div class="border border-green-900 rounded-2xl shadow p-2 bg-white mx-10">
                                <div class="border border-black rounded-xl shadow p-4 bg-white">
                                    <div class="pb-2 mb-2 flex justify-between items-center">
                                        <div class=" text-gray-700">Workout Name - Test Workout</div>
                                        <div class="space-x-2">
                                            <button class="">
                                                <svg class="feather feather-edit" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            </button>
                                            <button class="">
                                                <svg fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><g><path d="M10,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625l12-12    c0.390625-0.390625,1.0234375-0.390625,1.4140625,0s0.390625,1.0234375,0,1.4140625l-12,12    C10.5117188,22.9023438,10.2558594,23,10,23z"/></g><g><path d="M22,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688l-12-12c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625    s1.0234375-0.390625,1.4140625,0l12,12c0.390625,0.390625,0.390625,1.0234375,0,1.4140625    C22.5117188,22.9023438,22.2558594,23,22,23z"/></g></g></svg>

                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-2 text-gray-800 font-semibold">Deadlift at 80% for 5 sets</div>

                                    <div class="grid grid-cols-2 gap-4  text-gray-700">
                                        <!-- Set Details -->
                                        <div>
                                            <table class="w-full text-left ">
                                                <tbody>
                                                    <tr class="">
                                                        <td class="py-1 pr-4">Set 1</td>
                                                        <td class="py-1">8 Reps</td>
                                                    </tr>
                                                    <tr class="">
                                                        <td class="py-1 pr-4">Set 2</td>
                                                        <td class="py-1">8 Reps</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-1 pr-4">Set 3</td>
                                                        <td class="py-1">6 Reps</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Rest Stages -->
                                        <div>
                                            <table class="w-full text-left ">
                                                <thead>
                                                    <tr class="">
                                                        <th class="py-1 ">Rest:</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="">
                                                        <td class="py-1 pr-4">Stage 1</td>
                                                        <td class="py-1">4 min</td>
                                                    </tr>
                                                    <tr class="">
                                                        <td class="py-1 pr-4">Stage 2</td>
                                                        <td class="py-1">4 min</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-1 pr-4">Stage 3</td>
                                                        <td class="py-1">4 min</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex justify-end">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" value="" class="sr-only peer">
                                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 dark:peer-checked:bg-blue-600"></div>
                                          </label>
                                    </div>
                                </div>
                            </div> --}}
                        </div>

                    </div>



                    {{-- end primary --}}
                    {{-- start primary --}}
                    <div class=" flex-col w-full bg-gray-50 p-4">
                        <div class="flex items-center border-b mt-2">
                            <label for="names_1" class="w-60 block mb-1">Workout Name </label>
                            <input type="text" id="names_1" name="names_1"
                                class="w-1/3 px-3 py-3 border flex rounded mb-2" required>
                        </div>
                        <div class="flex items-center border-b ">
                            <label for="categorys_1" class="w-60 block mb-1">Category <span
                                    class="text-red-500">*</span></label>
                            <select id="categorys_1" name="categorys_1" onchange="getworkoutS(this)"
                                class="w-1/3 px-3 py-3 border flex rounded mb-2">
                                <option value="" selected disabled>-- Select Category --</option>
                            </select>
                        </div>
                        <div class="flex items-center border-b mt-2 ">
                            <label for="workouts_1" class="w-60 block mb-1">Exercise <span
                                    class="text-red-500">*</span></label>
                            <select id="workouts_1" name="workouts_1" class="w-1/3 px-3 py-3 border flex rounded mb-2">
                                <option value="" selected disabled>-- Select Exercise --</option>
                            </select>
                        </div>
                        <div class="flex items-center border-b mt-2 ">
                            <label for="weigths_1" class="w-60 block mb-1">Weight Precentage <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="weigths_1" name="weigths_1"
                                class="w-1/3 px-3 py-3 border flex rounded mb-2">
                            <label for="" class="border bg-white py-3 px-3 mb-2 ">%</label>
                        </div>
                        <div class="border-b mt-2" id="duplicateSetUI">
                                <div class="">
                                    <div class="flex items-center sets-view">
                                        <label for="sets_1" class="w-60 block mb-1">SET <span class="text-red-500">*</span></label>
                                        <div class="relative flex items-center max-w-[12rem] gap-2" id="duplicateRepsUIStrength">
                                            <!-- Optional extra input (first one) -->
                                            <input type="text" id="sets_1" name="sets_1" value="1" data-input-counter
                                                class="w-5 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                placeholder="0" readonly required />

                                            <label for="reps_1" class="text-sm mr-2">REPS</label>

                                            <!-- Decrement Button -->
                                            <button type="button"
                                                onclick="decrement(this.parentNode.querySelector('input#reps_1').id)"
                                                class="decrement-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>

                                            <!-- REPS Counter Input -->
                                            <input type="text" id="reps_1" name="reps_1" data-input-counter
                                                class="w-7 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                placeholder="0" readonly required />

                                            <!-- Increment Button -->
                                            <button type="button"
                                                onclick="increment(this.parentNode.querySelector('input#reps_1').id)"
                                                class="increment-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="duplicate-sets-strength" id="duplicate-sets-strength_1"></div>
                                    <div class="ml-60">
                                        <button id="duplicatesetstrength_1" onclick="duplicateStrengthSet(this.id)" type="button"
                                            class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base">
                                            <i class="fas fa-plus text-[12px]"></i> Add set</button>
                                    </div>
                                </div>
                            </div>
                        

                        <div class="flex items-center border-b ">
                            <label for="intensity" class="w-60 block mb-1">Intensity</label>
                            <select id="intensitys_1" name="intensitys_1"
                                class="w-1/3 px-3 py-3 border flex rounded my-2">
                                <option value=""selected disabled>-- Select Intensity --
                                </option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="extreme">Extreme</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-5 mt-5">
                            {{-- <a class=" bg-black text-white py-2 px-4 rounded mt-2 text-center text-base w-32"
                                id="cloneButtonstrength">Another</a> --}}
                            <button type="submit" id="savebtn"
                                class="bg-[#FB1018] text-white py-2 px-4 rounded mb-2 hover:bg-red-700 w-24 self-end">Save</button>
                        </div>
                    </div>
                    {{-- end primary --}}

                    {{-- start alternative --}}
                    {{-- <div class="flex-col w-full"> --}}
                        <!-- Alternate Category and Workouts -->

                       {{--<div class="flex items-center border-b ">
                            <label for="alt-categorys_1" class="w-60 block mb-1">Category </label>
                            <select id="alt-categorys_1" name="alt-categorys_1" onchange="getworkoutS(this)"
                                class="w-1/3 px-3 py-3 border flex rounded mb-2">
                                <option value="" selected disabled>-- Select Category --</option>
                            </select>
                        </div>
                        <div class="flex items-center border-b mt-2 ">
                            <label for="alt-workouts_1" class="w-60 block mb-1">Exercise </label>
                            <select id="alt-workouts_1" name="alt-workouts_1"
                                class="w-1/3 px-3 py-3 border flex rounded mb-2">
                                <option value="" selected disabled>-- Select Exercise --</option>
                            </select>
                        </div>
                        <div class="flex items-center border-b mt-2">
                            <label for="weigth" class="w-60 block mb-1">Weight </label>
                            <input type="text" id="alt-weigths_1" name="alt-weigths_1"
                                class="w-1/3 px-3 py-3 border flex rounded mb-2">
                            <label for="" class="border bg-white py-3 px-3 mb-2 ">%</label>
                        </div>--}}

                        {{-- alternate sets --}}
                        {{-- <div class="border-b" id="altduplicateSetUIStrength">
                            <div class="">
                                <div class="flex items-center">
                                    <label for="alt-sets" class="w-60 block">SETS </label>
                                    <div class="relative flex items-center max-w-[8rem] my-1">
                                        <button type="button"
                                            onclick="decrement(this.parentNode.querySelector('input').id)"
                                            class="decrement-custom bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" id="alt-sets_1" name="alt-sets_1" data-input-counter
                                            class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full my-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                            placeholder="0" readonly />
                                        <button type="button"
                                            onclick="increment(this.parentNode.querySelector('input').id)"
                                            class="increment-custom bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div> --}}
                                {{-- Altertnative reps --}}
                                {{-- <div class="flex items-center border-b">
                                    <label for="alt-reps" class="w-60 block mb-1">REPS </label>
                                    <div class="relative flex items-center max-w-[8rem] my-2">
                                        <button type="button"
                                            onclick="decrement(this.parentNode.querySelector('input').id)"
                                            class="decrement-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" id="alt-reps_1" name="alt-reps_1" data-input-counter
                                            class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                            placeholder="0" readonly />
                                        <button type="button"
                                            onclick="increment(this.parentNode.querySelector('input').id)"
                                            class="increment-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="altduplicate-setsstrength" id="alt-duplicatestrength-sets_1"></div>
                                <div class="ml-60">
                                    <button id="altaddsetstrength_1" onclick="altduplicateSetStrength(this.id)"
                                        type="button"
                                        class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base">
                                        <i class="fas fa-plus text-[12px]"></i> Add set</button>
                                </div>
                            </div>
                        </div> --}}

                        {{-- alternate Rest --}}

                        {{-- <div class="flex items-center border-b mt-4">
                            <label for="alt-rest" class="w-60 block mb-1">Rest </label>
                            <div class=""> --}}
                                <!-- Red Section -->
                                {{-- <div class="relative flex items-center max-w-[12rem] mb-4 py-2">
                                    <label class="text-red-500 font-bold w-16 text-right pr-8">Stage&nbsp;1</label>
                                    <button type="button"onclick="changeRestTime(this, -15)"
                                        class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                        </svg>
                                    </button>
                                    <input type="text" id="alt-restreds_1" name="alt-restreds_1"
                                        placeholder="00:00" value="04:00"
                                        class="bg-gray-50 border-x-0 restsared border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                        readonly />
                                    <button type="button" onclick="changeRestTime(this, 15)"
                                        class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                        </svg>
                                    </button>
                                </div> --}}

                                {{-- Yellow Section --}}
                                {{-- <div class="relative flex items-center max-w-[12rem] mb-4 py-2">
                                    <label class="text-yellow-500 font-bold w-16 text-right pr-8">Yellow</label>
                                    <button type="button"onclick="changeRestTime(this, -15)"
                                        class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                        </svg>
                                    </button>
                                    <input type="text" id="alt-restyellows_1" name="alt-restyellows_1"
                                        placeholder="00:00" value="04:00"
                                        class="bg-gray-50 border-x-0 restsayellow border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                        readonly />
                                    <button type="button" onclick="changeRestTime(this, 15)"
                                        class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                        </svg>
                                    </button>
                                </div> --}}

                                {{-- green section --}}
                                {{-- <div class="relative flex items-center max-w-[12rem] mb-4 py-2">
                                    <label class="text-green-500 font-bold w-16 text-right pr-8">Stage&nbsp;3</label>
                                    <button type="button"onclick="changeRestTime(this, -15)"
                                        class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                        </svg>
                                    </button>
                                    <input type="text" id="alt-restgreens_1" name="alt-restgreens_1"
                                        placeholder="00:00" value="04:00"
                                        class="bg-gray-50 border-x-0 restsagreen border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                        readonly />
                                    <button type="button" onclick="changeRestTime(this, 15)"
                                        class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center border-b">
                            <label for="alt-intensitys" class="w-60 block mb-1">Intensity</label>
                            <select id="alt-intensitys_1" name="alt-intensitys_1"
                                class="w-1/3 px-3 py-3 border flex rounded mb-2 mt-2">
                                <option value="" selected disabled>-- Select Intensity --
                                </option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="extreme">Extreme</option>
                            </select>
                        </div>
                    </div> --}}
                    {{-- end  alternative --}}
                </div>

            </div>
        </div>

        <div id="cloneDisplayContainerStrength"></div>
        {{-- <div class="flex flex-col gap-5">
            <a class=" bg-black text-white py-2 px-4 rounded mt-2 text-center text-base w-32"
                id="cloneButtonstrength">Another</a>
            <button type="submit"
                class="bg-[#FB1018] text-white py-2 px-4 rounded mb-2 hover:bg-red-700 w-24 mr-8 self-end">Save</button>
        </div> --}}

    </form>

</div>



{{-- get category and workout --}}
<script>
    // Function to set category options
    function setCategory(id, categoryName, selectId) {
        console.log('first')
        const categorySelect = document.getElementById(selectId);
        const option = document.createElement('option');
        option.value = id;
        option.text = categoryName;
        categorySelect.add(option);
    }

    // Function to get categories via AJAX
    function getCategoryS() {
        const selectTab = "strength";

        $.ajax({
            url: "/getCategory",
            type: "POST",
            data: {
                tab: selectTab,
                _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
            },
            success: function(response) {
                console.log('catttttttttttt',response);

                // Convert category_options object into an array, if it's not an array already
                let categoryOptions = response.category_options;

                // If category_options is an object, convert it into an array
                if (categoryOptions && typeof categoryOptions === 'object') {
                    categoryOptions = Object.values(categoryOptions); // Convert object to array
                } else {
                    categoryOptions = [];
                }

                // Clear existing options in the select elements before adding new ones
                ['categorys_1','categorys_2'].forEach(id => {
                    const categorySelect = document.getElementById(id);
                    categorySelect.innerHTML =
                        '<option value="" selected disabled>-- Select Category --</option>';
                });

                // Loop through each category_option and call the setCategory() function
                categoryOptions.forEach(option => {
                    setCategory(option.id, option.category_name, 'categorys_1');
                    setCategory(option.id, option.category_name, 'categorys_2');
                });

                // Optional: Sort the options alphabetically if needed
                ['categorys_1','categorys_2'].forEach(id => {
                    sortSelectOptions(document.getElementById(id));
                });
            },
            error: function(xhr, status, error) {
                console.error(error); // Handle error
            }
        });
    }


    // Function to sort select options alphabetically
    function sortSelectOptions(selectElement) {
        const options = Array.from(selectElement.options);
        options.sort((a, b) => a.text.localeCompare(b.text));
        selectElement.innerHTML = '';
        options.forEach(option => selectElement.add(option));
    }


    // Function to get workoutwe via AJAX based on selected category
    function getworkoutS(selectElement) {
        console.log("this is select element", selectElement);
        const tab = "strength";
        const selectId = selectElement.value; // Get the value of the selected element

        // Split the ID of the select element by underscores
        const idParts = selectElement.id.split('_');

        // Get the remaining part after the base
        const remainingPart = idParts.slice(1).join('_');
        const isCategory = selectElement.id === `categorys_${remainingPart}`;

        const isECategory = selectElement.id === `categorystrength_${remainingPart}`;


        let workoutSelectId;

        if (isCategory) {
            workoutSelectId = `workouts_${remainingPart}`;
        } else if (isECategory) {
            workoutSelectId = `workoutstrength_${remainingPart}`;
        }


        console.log(`Constructed workoutSelectId: ${workoutSelectId}`);

        const workoutSelect = document.getElementById(workoutSelectId);

        if (!workoutSelect) {
            console.error(`Element with ID ${workoutSelectId} not found in the DOM.`);
            return;

            // Fetch workouts via AJAX
            fetchWorkouts(selectId, workoutSelect);
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
                console.log(response);
                const workouts = response.workouts || [];
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
    function clearOptionstrength(selectElement) {
        selectElement.innerHTML = '<option value="" selected disabled>-- Select Workout --</option>';
    }

    let allStrengthData = [];

    function getstrength(date) {
        console.log("Fetching strength data for date: " + date);
        $.ajax({
            url: "/get-strengthdata",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                date: date
            },
            success: function(response) {
                console.log("this is strenth response", response);
                allStrengthData = response.Strength;
                // Assuming response is an array of arrays
                // response.forEach(subArray => {
                    setstrengths(response.Strength, response.categoryOptions);
                // });
            },

            error: function(xhr, status, error) {
                console.error("Error during AJAX request:", {
                    status: status,
                    xhr: xhr,
                    error: error
                });
            }
        });
    }

    function updatestrength(id) {
        // Construct the form ID dynamically
        const formId = `#updatestrenght_${id}`;
        const tab = document.getElementById('strenghtTab');
        // Serialize form data
        const formData = $(formId).serialize();
        console.log(formData);
        // AJAX request
        $.ajax({
            url: '{{ route('updatestrength') }}',
            type: 'POST',
            data: formData,

            success: function(response) {
                // Handle the response
                if (tab) {
                    tab.click();
                }
                alert(response.message);
                $(formId)[0].reset();
            },
            error: function(xhr) {
                // Handle error
                console.error(xhr.responseText);
            }
        });
    }

    // assigned to class
    $(document).on('change', '.strength-toggle', function () {
        const date = document.getElementById('selectdatestrenghtDelete').value;
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
            success: function (response) {
                alert(response.message);
                getdateName(date); // Refresh classes or UI
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", xhr.responseText);
                alert("An error occurred while assigning the workout.");
            }
        });
    });

    function setstrengths(strengthData, categoryOptions) {
        const container = $("#setstrengths"); // Replace with your actual container class or ID
        container.empty(); // Clear previous content

        strengthData.forEach((item, index) => {
            let setsHTML = '';
            console.log('ndnvdnldnvv',)
            if (Array.isArray(item.sets) && item.sets.length > 0) {
                const setObj = item.sets[0]; // usually only one object with `sets` and `reps`

                for (let i = 0; i < setObj.sets; i++) {
                    setsHTML += `
                        <tr>
                            <td class="py-1 pr-4">Set ${i + 1}</td>
                            <td class="py-1">${setObj.reps} Reps</td>
                        </tr>
                    `;
                }
            } else {
                setsHTML = '<tr><td colspan="2">No set data</td></tr>';
            }

            let html = `
            <div class="border border-green-900 rounded-2xl shadow p-2 bg-white mx-10">
                <div class="border border-black rounded-xl shadow p-4 bg-white">
                    <div class="pb-2 mb-2 flex justify-between items-center">
                        <div class="text-gray-700">Workout Name - ${item.workoutname || 'N/A'}</div>
                        <div class="space-x-2">
                            <button class="edit-strength-btn" data-id="${item.id}" type="button">
                                <svg class="feather feather-edit" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>

                            <button class="delete-strength-btn" data-id="${item.id}" type="button">
                                <svg fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <path d="M10,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625l12-12c0.390625-0.390625,1.0234375-0.390625,1.4140625,0s0.390625,1.0234375,0,1.4140625l-12,12C10.5117188,22.9023438,10.2558594,23,10,23z"/>
                                        <path d="M22,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688l-12-12c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625s1.0234375-0.390625,1.4140625,0l12,12c0.390625,0.390625,0.390625,1.0234375,0,1.4140625C22.5117188,22.9023438,22.2558594,23,22,23z"/>
                                    </g>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-2 text-gray-800 font-semibold">${item.workout_type} at ${item.weight || 0}% for ${item.sets[0]?.sets || 0} sets</div>

                    <div class="grid grid-cols-2 gap-4 text-gray-700">
                        <div>
                            <table class="w-full text-left">
                                <tbody>
                                    ${setsHTML}
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <table class="w-full text-left">
                                <thead><tr><th class="py-1">Rest:</th><th></th></tr></thead>
                                <tbody>
                                    <tr><td class="py-1 pr-4">Stage 1</td><td class="py-1">${item.restred || '-'} min</td></tr>
                                    <tr><td class="py-1 pr-4">Stage 2</td><td class="py-1">${item.restyellow || '-'} min</td></tr>
                                    <tr><td class="py-1 pr-4">Stage 3</td><td class="py-1">${item.restgreen || '-'} min</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <p class="mr-4 font-bold">Assign Workout to Class</p>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="" class="sr-only peer strength-toggle" data-workout-id="${item.id}" data-workout-type="strength" ${item.is_assigned ? 'checked' : ''}>
                             <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600 dark:peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </div>
            </div>
            `;

            container.append(html);

            // Bind the Edit button after appending
            container.find('.edit-strength-btn').off('click').on('click', function () {
                const strengthId = $(this).data('id');
                populateStrengthForm(strengthId);
            });

        });
    }

    function populateStrengthForm(strengthId) {

        const data = allStrengthData.find(item => item.id == strengthId);
        console.log('dataaaaaa',data);

        if (!data) {
            console.warn("No strength data found for ID:", strengthId);
            return;
        }

        // Set category and trigger onchange to load workouts
        const categorySelect = document.getElementById('categorys_1');
        categorySelect.value = data.category_id;
        categorySelect.dispatchEvent(new Event('change')); // To trigger getworkoutS

        // Delay setting workouts to allow async options to load
        setTimeout(() => {
            const workoutSelect = document.getElementById('workouts_1');
            workoutSelect.value = data.workout_id;
        }, 500); // Adjust based on how long getworkoutS takes to populate

        // Set weight
        document.getElementById('weigths_1').value = data.weight;

        if (data.sets.length > 0) {
            const setData = data.sets[0]; // Get first item

            const setsInput = document.getElementById('sets_1');
            const repsInput = document.getElementById('reps_1');

            if (setsInput) setsInput.value = setData.sets;
            if (repsInput) repsInput.value = setData.reps;
        }

        document.getElementById('names_1').value = data.workoutname;
        document.getElementById('restreds_1').value = data.restred;
        document.getElementById('restyellows_1').value = data.restyellow;
        document.getElementById('restgreens_1').value = data.restgreen;
        document.getElementById('restgreens_1').value = data.restgreen;
        document.getElementById('intensitys_1').value = data.intensity;
        document.getElementById('savebtn').innerHTML = "Edit";

    }

    function filterStrength(date) {
        const dateOnClick = document.getElementById('selectdatestrenghtDelete').value;
        console.log('first',dateOnClick)

        //console.log('bbbbbbbb',buttonId)
        let categoryId = document.getElementById("categorys_2").value;
        let exerciseId = document.getElementById("workouts_2").value;
        let nameSearch = document.getElementById("name_1").value;

        $.ajax({
            url: "/search-setstrength",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                date: dateOnClick,
                name: nameSearch,
                category_id: categoryId,
                workout_id: exerciseId
            },
            success: function (response) {
                console.log("this is filteerd strenth response", response);
                allStrengthData = response.Strength;
                const strengthArray = Object.values(response.Strength);
                const categoryArray = Object.values(response.categoryOptions);
                setstrengths(strengthArray, categoryArray);
                // Assuming response is an array of arrays
                // response.forEach(subArray => {
                    //setstrengths(response.Strength, response.categoryOptions);
                // });
            },
            error: function (xhr) {
                alert("Error occurred: " + xhr.responseText);
            }
        });
    }

    function setstrengthing(Strength, categoryArray) {
        console.log("this is strength", Strength);
        console.log("this is categoryOptions", categoryArray);

        // Get the container where the information will be displayed
        const container = document.getElementById('strength-container');

        // Clear the container
        container.innerHTML = '';

        // Convert categoryOptions to an array
        const categoryOptions = Object.values(categoryArray);

        // Initialize the htmlContent variable
        let htmlContent = '';

        // Build HTML content
        Strength.forEach(item => {
            // Create a string with the primary category options
            let strengthcategoryOptionsHTML =
                '<option value="" selected disabled>-- Select Category --</option>';
            categoryOptions.forEach(category => {
                strengthcategoryOptionsHTML +=
                    `<option value="${category.id}" ${category.id == item.category_id ? 'selected' : ''}>${category.category_name}</option>`;
            });

            // Create a string with the alternative category options
            let strengthaltCategoryOptionsHTML =
                '<option value="" selected disabled>-- Select Category --</option>';
            categoryOptions.forEach(category => {
                strengthaltCategoryOptionsHTML +=
                    `<option value="${category.id}" ${item.alt_category_id == category.id ? 'selected' : ''}>${category.category_name}</option>`;
            });

            // Build the setsHTML for each set in the item
            const setsHTML = Array.isArray(item.sets) ? item.sets.map((set, index) => {
                // Check if sets or reps are null and set visibility accordingly
                const isSetsVisible = set.sets !== null && set.sets !== undefined;
                const isRepsVisible = set.reps !== null && set.reps !== undefined;

                return `
                    <div>
                        <input name="setsid_${index}" value="${set.id}" hidden>

                        ${isSetsVisible ? `
                        <div class="flex items-center sets-view">
                            <label for="custom-numberstrength_${index}" class="w-60 block mb-1">SETS <span class="text-red-500">*</span></label>
                            <div class="relative flex items-center max-w-[8rem]">
                                <button type="button" onclick="decrement(this.parentNode.querySelector('input').id)" class="decrement-custom bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                    <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                    </svg>
                                </button>
                                <input type="text" value="${set.sets}" id="setsstrength_${index}" name="setsstrength_${index}" data-input-counter class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center my-2 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" placeholder="0" readonly required />
                                <button type="button" onclick="increment(this.parentNode.querySelector('input').id)" class="increment-custom bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                    <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>` : ''}

                        ${isRepsVisible ? `
                        <div class="flex items-center border-b">
                            <label for="repsstrength_${index}" class="w-60 block mb-1">REPS <span class="text-red-500">*</span></label>
                            <div class="relative flex items-center max-w-[8rem]">
                                <button type="button" onclick="decrement(this.parentNode.querySelector('input').id)" class="decrement-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                    <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                    </svg>
                                </button>
                                <input type="text" value="${set.reps}" id="repsstrength_${index}" name="repsstrength_${index}" data-input-counter class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" placeholder="0" readonly required />
                                <button type="button" onclick="increment(this.parentNode.querySelector('input').id)" class="increment-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                    <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>` : ''}
                    </div>
                `;
            }).join('') : '';


            // Build the altSetsHTML for each alt set in the item
            const altSetsHTML = Array.isArray(item.sets) ? item.sets.map((set, index) => {
                // Check if alt_sets or alt_reps are null and set visibility accordingly
                const isAltSetsVisible = set.alt_sets !== null && set.alt_sets !== undefined;
                const isAltRepsVisible = set.alt_reps !== null && set.alt_reps !== undefined;

                return `
                <div>
                    ${isAltSetsVisible ? `
                    <div class="flex items-center sets-view">
                        <label for="altcustomnumberweight_${index}" class="w-60 block mb-1">SETS <span class="text-red-500">*</span></label>
                        <div class="relative flex items-center max-w-[8rem]">
                            <button type="button" onclick="decrement(this.parentNode.querySelector('input').id)" class="decrement-custom bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                </svg>
                            </button>
                            <input type="text" value="${set.alt_sets}" id="altsetsstrength_${index}" name="altsetsstrength_${index}" data-input-counter class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center my-2 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" placeholder="0" readonly required />
                            <button type="button" onclick="increment(this.parentNode.querySelector('input').id)" class="increment-custom bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                </svg>
                            </button>
                        </div>
                    </div>` : ''}

                    ${isAltRepsVisible ? `
                    <div class="flex items-center border-b">
                        <label for="altrepssstrength_${index}" class="w-60 block mb-1">REPS <span class="text-red-500">*</span></label>
                        <div class="relative flex items-center max-w-[8rem]">
                            <button type="button" onclick="decrement(this.parentNode.querySelector('input').id)" class="decrement-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                </svg>
                            </button>
                            <input type="text" value="${set.alt_reps}" id="altrepssstrength_${index}" name="altrepssstrength_${index}" data-input-counter class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" placeholder="0" readonly required />
                            <button type="button" onclick="increment(this.parentNode.querySelector('input').id)" class="increment-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                </svg>
                            </button>
                        </div>
                    </div>` : ''}
                </div>`;
            }).join('') : '';


            // Build the final HTML for the current item
            htmlContent += `
                <form  id="updatestrenght_${item.id}">
                    @csrf

                    <input name="id_${item.id}" value="${item.id}" hidden>
                    <div class="flex flex-col text-lg p-4 bg-gray-50 mr-8 rounded-md gap-4 mb-4">
                        <div class="flex gap-5 justify-between">
                            {{-- Primary Category and Workouts --}}
                            <div class="flex-col w-full border-r border-r-black">
                                <div class="item-container">
                                    <div hidden><h3>Item ID: ${item.id}</h3></div>
                                    <div class="flex border-b mt-4">
                                    <label for="categorystrength_${item.id}" class="w-60 block mb-1">Category <span class="text-red-500">*</span></label>
                                    <select id="categorystrength_${item.id}" name="categorystrength_${item.id}" onchange="getworkoutS(this)" class="w-1/3 px-3 py-3 border rounded mb-2" required>
                                        ${strengthcategoryOptionsHTML}
                                    </select>
                                    </div>
                                    <div class="flex items-center border-b">
                                        <label for="workoutstrength_${item.id}" class="w-60 block mb-1">
                                            Workout<span class="text-red-500">*</span>
                                        </label>
                                        <select id="workoutstrength_${item.id}" name="workoutstrength_${item.id}" class="w-1/3 px-3 py-3 border mt-2 flex rounded mb-2">
                                            <option value="" selected disabled>-- Select Workout --</option>
                                            <!-- Populate options dynamically -->
                                            <option value="${item.workout_id}" selected>${item.workout_type}</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center border-b mt-2">
                                        <label for="weigthstrength_${item.id}" class="w-60 block mb-1">Weight Precentage <span
                                                class="text-red-500">*</span></label>
                                        <input type="number" id="weigthstrength_${item.id}" name="weigthstrength_${item.id}" value="${item.weight}"
                                            class="w-1/3 px-3 py-3 border flex rounded mb-2" required>
                                        <label for="" class="border bg-white py-3 px-3 mb-2 ">%</label>
                                    </div>
                                    <div class="border-b" id="duplicateSetUIStrength">
                                        <div class="sets-container">
                                            ${setsHTML}
                                        </div>
                                        <div class="duplicate-sets-strengthclone" id="duplicate-sets_${item.id}"></div>
                                        <div class="ml-60">
                                            <button id="addset_${item.id}" onclick="duplicateSetStrength(this.id)" type="button" class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base">
                                                <i class="fas fa-plus text-[12px]"></i> Add set
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-center border-b">
                                        <label for="restredstrength_${item.id}" class="w-60 block mb-1">Rest <span class="text-red-500">*</span></label>
                                        <div class="">
                                        <div class="relative flex items-center max-w-[12rem] mb-4">
                                            <label class=" text-red-500 font-bold w-16 text-right pr-8">Stage&nbsp;1</label>
                                            <button type="button" onclick="decrementRest(this.parentNode.querySelector('input').id)" class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>
                                            <input type="text" value="${item.restred}" id="restredstrength_${item.id}" name="restredstrength_${item.id}" placeholder="00:00" class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" readonly required>
                                            <button type="button" onclick="incrementRest(this.parentNode.querySelector('input').id)" class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="relative flex items-center max-w-[12rem] mb-4">
                                            <label class=" text-yellow-500 font-bold w-16 text-right pr-8">Yellow</label>
                                            <button type="button" onclick="decrementRest(this.parentNode.querySelector('input').id)" class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>
                                            <input type="text" value="${item.restyellow}" id="restyellowstrength_${item.id}" name="restyellowstrength_${item.id}" placeholder="00:00" class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" readonly required>
                                            <button type="button" onclick="incrementRest(this.parentNode.querySelector('input').id)" class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="relative flex items-center max-w-[12rem] mb-4">
                                            <label class=" text-green-500 font-bold w-16 text-right pr-8">Stage&nbsp;3</label>
                                            <button type="button" onclick="decrementRest(this.parentNode.querySelector('input').id)" class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>
                                            <input type="text" value="${item.restgreen}" id="restgreenstrength_${item.id}" name="restgreenstrength_${item.id}" placeholder="00:00" class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" readonly required>
                                            <button type="button" onclick="incrementRest(this.parentNode.querySelector('input').id)" class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                        </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center border-b">
                                        <label for="intensitystrength_${item.id}" class="w-60 block mb-1">
                                            Intensity
                                        </label>
                                        <select id="intensitystrength_${item.id}" name="intensitystrength_${item.id}"
                                            class="w-1/3 px-3 py-3 border flex rounded my-2" required>
                                            <!-- Dynamically setting the selected option -->
                                            <option value="low" ${item.intensity === 'low' ? 'selected' : ''}>Low</option>
                                            <option value="medium" ${item.intensity === 'medium' ? 'selected' : ''}>Medium</option>
                                            <option value="high" ${item.intensity === 'high' ? 'selected' : ''}>High</option>
                                            <option value="extreme" ${item.intensity === 'extreme' ? 'selected' : ''}>Extreme</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            {{-- Alternate Category and Workouts --}}
                            <div class="flex-col w-full">

                                    <div class="flex items-center border-b mt-4">
                                        <label for=altcategorystrength_${item.id}" class="w-60 block mb-1">Category </label>
                                        <select id="altcategorystrength_${item.id}" name="altcategorystrength_${item.id}" onchange="getworkoutS(this)" class="w-1/3 px-3 py-3 border rounded mb-2 mr-5" required>
                                            ${strengthaltCategoryOptionsHTML}
                                        </select>
                                        <button type="button" onclick="updatestrength(${item.id})" class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base">Edit</button>
                                    </div>

                                <div class="flex items-center border-b">
                                    <label for="altworkoutstrengths_${item.category_id}" class="w-60 block mb-1">
                                        Workout
                                    </label>
                                    <select id="altworkoutstrengths_${item.id}" name="altworkoutstrengths_${item.id}" class="w-1/3 px-3 py-3 mt-2 border flex rounded mb-2">
                                        <option value="" selected disabled>-- Select Workout --</option>
                                        <!-- Populate options dynamically -->
                                        <option value="${item.alt_workout_id}" selected>${item.alt_workout_type}</option>
                                    </select>
                                </div>
                                <div class="flex items-center border-b mt-2">
                                    <label for="altweigthstrength_${item.id}" class="w-60 block mb-1">Weight Precentage </label>
                                    <input type="number" id="altweigthstrength_${item.id}" name="altweigthstrength_${item.id}" value="${item.alt_weight}"
                                        class="w-1/3 px-3 py-3 border flex rounded mb-2" required>
                                    <label for="" class="border bg-white py-3 px-3 mb-2 ">%</label>
                                </div>
                                ${altSetsHTML ? `
                                    <div class="border-b" id="duplicateSetUIAlterEdit">
                                        <div class="">
                                            <div>
                                                ${altSetsHTML}
                                            </div>
                                            <div class="altduplicate-setsstrength" id="alt-duplicate-sets_${item.id}"></div>
                                            <div class="ml-60">
                                                <button id="altaddset_${item.id}" onclick="altduplicateSetStrength(this.id)" type="button" class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base">
                                                    <i class="fas fa-plus text-[12px]"></i> Add set
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                ` : ''}

                                <div class="flex items-center border-b">
                                    <label for="altrestredstrength_${item.id}" class="w-60 block mb-1">Rest </label>
                                      <div class="">
                                    <div class="relative flex items-center max-w-[12rem]  py-2">
                                         <label class="text-red-500 font-bold w-16 text-right pr-8">Stage&nbsp;1</label>
                                        <button type="button" onclick="decrementRest(this.parentNode.querySelector('input').id)" class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" value="${item.alt_restred}" id="altrestredstrength_${item.id}" name="altrestredstrength_${item.id}" placeholder="00:00" class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" readonly required>
                                        <button type="button" onclick="incrementRest(this.parentNode.querySelector('input').id)" class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>

                                     <div class="relative flex items-center max-w-[12rem] ">
                                         <label class="text-yellow-500 font-bold w-16 text-right pr-8">Yellow</label>
                                        <button type="button" onclick="decrementRest(this.parentNode.querySelector('input').id)" class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" value="${item.alt_restyellow}" id="altrestyellowstrength_${item.id}" name="altrestyellowstrength_${item.id}" placeholder="00:00" class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" readonly required>
                                        <button type="button" onclick="incrementRest(this.parentNode.querySelector('input').id)" class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>


                                     <div class="relative flex items-center max-w-[12rem] mb-4 py-2">
                                         <label class="text-green-500 font-bold w-16 text-right pr-8">Stage&nbsp;3</label>
                                        <button type="button" onclick="decrementRest(this.parentNode.querySelector('input').id)" class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" value="${item.alt_restgreen}" id="altrestgreenstrength_${item.id}" name="altrestgreenstrength_${item.id}" placeholder="00:00" class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" readonly required>
                                        <button type="button" onclick="incrementRest(this.parentNode.querySelector('input').id)" class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                </div>
                               <div class="flex items-center border-b">
                                    <label for="altintensitystrength_${item.id}" class="w-60 block mb-1">
                                        Intensity
                                    </label>
                                    <select id="altintensitystrength_${item.id}" name="altintensitystrength_${item.id}"
                                        class="w-1/3 px-3 py-3 border flex rounded my-2" required>
                                        <!-- Dynamically setting the selected option based on item.alt_intensity -->
                                        <option value="" selected disabled>-- Select Intensity --
                                </option>
                                        <option value="low" ${item.alt_intensity === 'low' ? 'selected' : ''}>Low</option>
                                        <option value="medium" ${item.alt_intensity === 'medium' ? 'selected' : ''}>Medium</option>
                                        <option value="high" ${item.alt_intensity === 'high' ? 'selected' : ''}>High</option>
                                        <option value="extreme" ${item.alt_intensity === 'extreme' ? 'selected' : ''}>Extreme</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            `;


        });

        // Update the container with the built HTML content
        container.innerHTML = htmlContent;



    }
</script>


{{-- rest time calculate --}}
<script>
    function changeRestTime(button, delta) {
        // Find the input element within the same parent container
        const input = button.parentNode.querySelector('input');

        // Get the current time value
        let [minutes, seconds] = input.value.split(':').map(Number);

        // Convert to total seconds
        if (isNaN(minutes)) minutes = 0;
        if (isNaN(seconds)) seconds = 0;
        let totalSeconds = (minutes * 60) + seconds;

        // Update the time
        totalSeconds += delta;
        if (totalSeconds < 0) totalSeconds = 0; // Prevent negative values

        // Convert back to minutes and seconds
        minutes = Math.floor(totalSeconds / 60);
        seconds = totalSeconds % 60;

        // Format the value as MM:SS
        input.value = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }
</script>


{{-- clear function and other --}}
<script>
    // Function to update input names and IDs for the cloned element
    function updateNamesAndIdss(element, index) {
        // Update names and IDs for inputs, selects, and buttons
        element.querySelectorAll("input, select, button").forEach(function(el) {
            var baseName = el.name.split('_')[0];
            var baseId = el.id.split('_')[0];
            el.name = baseName + '_' + index;
            el.id = baseId + '_' + index;
        });

        // List of class names to update values for
        const timeClassNames = [
            'restsa', 'restsp',
            'restspred', 'restspyellow', 'restspgreen',
            'restsared', 'restsayellow', 'restsagreen'
        ];

        // Update values for elements with specific classes
        timeClassNames.forEach(className => {
            let timeElement = element.querySelector(`.${className}`);
            if (timeElement) {
                timeElement.value = '04:00';
            }
        });
    }

    // Function to add a remove button to the cloned element
    function addRemoveButtonToElementstrength(element) {
        // Check if a remove button already exists
        if (!element.querySelector('.removeBtnStrength')) {
            var removeButton = document.createElement("button");
            removeButton.innerText = "Remove";
            removeButton.classList.add(
                'removeBtnStrength',
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
                element.remove();
            });
            element.appendChild(removeButton);
        }
    }

    // Function to handle the duplication of UI elements
    function handleUiDuplications(event) {
        var cloneButton = event.target;
        var originalUiElement = cloneButton.closest('.duplicateUiStrength');

        // Retrieve or set the index for the new clone
        var index = parseInt(originalUiElement.dataset.index) || 1;
        index += 1;
        originalUiElement.dataset.index = index;

        // Clone the original UI element
        var clonedElement = originalUiElement.cloneNode(true);

        // Clear content of duplicate-sets in the cloned element
        var duplicateSets = clonedElement.querySelector(".duplicate-sets-strength");
        if (duplicateSets) {
            duplicateSets.innerHTML = ""; // Clear content
        }

        // Reset input values in the cloned element
        clonedElement.querySelectorAll("input").forEach(function(input) {
            input.value = '';
        });

        // Update names and IDs in the cloned element
        updateNamesAndIdss(clonedElement, index);

        // Remove old buttons and add a new remove button
        clonedElement.querySelectorAll(".removeBtnStrength").forEach(function(button) {
            button.remove();
        });
        addRemoveButtonToElementstrength(clonedElement);

        // Append the cloned element to the display container
        document.getElementById('cloneDisplayContainerStrength').appendChild(clonedElement);
    }

    // Event listener for the main clone button
    document.getElementById('cloneButtonstrength').addEventListener('click', function() {
        const uiContainerStrength = document.getElementById('uiContainerStrength');
        const cloneDisplayContainerStrength = document.getElementById('cloneDisplayContainerStrength');
        const newContainer = uiContainerStrength.cloneNode(true);

        // Retrieve or set the index for the new clone
        var index = parseInt(uiContainerStrength.dataset.index) || 1;
        index += 1;
        uiContainerStrength.dataset.index = index;

        // Clear content of duplicate-sets in the cloned container
        var duplicateSets = newContainer.querySelector(".duplicate-sets-strength");
        if (duplicateSets) {
            duplicateSets.innerHTML = ""; // Clear content
        }

        // Clear content of altduplicate-sets in the cloned container
        var altduplicateSets = newContainer.querySelector(".altduplicate-setsstrength");
        if (altduplicateSets) {
            altduplicateSets.innerHTML = ""; // Clear content
        }

        // Reset input values in the new container
        newContainer.querySelectorAll("input").forEach(function(input) {
            input.value = '';
        });

        // Update names and IDs in the new container
        updateNamesAndIdss(newContainer, index);

        // Add the remove button to the new container
        addRemoveButtonToElementstrength(newContainer);

        // Append the new container to the display container
        cloneDisplayContainerStrength.appendChild(newContainer);
    });

    // Add event listeners to all duplicate buttons
    document.querySelectorAll(".duplicateBtnStrength").forEach(function(button) {
        button.addEventListener("click", handleUiDuplications);
    });
</script>


{{-- create duplicate sets and reps --}}
<script>
    // Function to clear existing workout options
    function clearOptionstrength(selectElement) {
        selectElement.innerHTML = '<option value="" selected disabled>-- Select Workout --</option>';
    }


    // duplicate Set
    let setCounterstrength = 1;

    function duplicateStrengthSet(id) {
        console.log(id)
        const idPartfind = id.split('_');

        // Get the remaining part after the base
        const remainingfind = idPartfind.slice(1).join('_');
        console.log("idsss " + remainingfind)

        setCounterstrength++;
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
            input.id = `sets_${remainingfind}${setCounterstrength}`;
            input.name = `sets_${remainingfind}${setCounterstrength}`;
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
        const setstrengthuiElement = document.createElement('div');
        setstrengthuiElement.innerHTML = `
        <div class="flex items-center sets-view mt-1">
                                        <div class="w-60 block mb-1"></div>
        <div class="relative flex items-center max-w-[12rem] gap-2" id="duplicateRepsUIStrength">
                                            <!-- Optional extra input (first one) -->
                                            <input type="text" id="sets_${remainingfind}${setCounterstrength}" name="sets_${remainingfind}${setCounterstrength}" value="${setCounterstrength}" data-input-counter
                                                class="w-5 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                placeholder="0" readonly required />

                                            <label for="sets_${remainingfind}${setCounterstrength}" class="text-sm mr-2">REPS</label>

                                            <!-- Decrement Button -->
                                            <button type="button"
                                                onclick="decrement(this.parentNode.querySelector('input#reps_${remainingfind}${setCounterstrength}').id)"
                                                class="decrement-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>

                                            <!-- REPS Counter Input -->
                                            <input type="text" id="reps_${remainingfind}${setCounterstrength}" name="reps_${remainingfind}${setCounterstrength}" data-input-counter
                                                class="w-7 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                placeholder="0" readonly required />

                                            <!-- Increment Button -->
                                            <button type="button"
                                                onclick="increment(this.parentNode.querySelector('input#reps_${remainingfind}${setCounterstrength}').id)"
                                                class="increment-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                        </div>

            <button type="button" class="remove-set-strength bg-red-500 text-white p-2 rounded ml-2">
                    Remove
            </button>
        </div>
                                </div>

        `;
        // Add functionality to the remove button
        setstrengthuiElement.querySelector('.remove-set-strength').addEventListener('click', function() {
            setstrengthuiElement.remove();
        });

        // Find the container element with class 'duplicate-sets' using parentNode traversal
        const container = originalSet.closest('.border-b').querySelector('.duplicate-sets-strengthclone') || originalSet.closest(
            '.border-b').querySelector('.duplicate-sets-strength');
        console.log(container);

        if (container) {
            container.appendChild(setstrengthuiElement);
        } else {
            console.error('Container element with class "duplicate-sets-strength" not found');
        }

        console.log(`Duplicated Set ID: ${input ? input.id : 'N/A'}`);

    }

    // function duplicateSetStrength(id) {
    //     console.log(id)
    //     const idPartfind = id.split('_');

    //     // Get the remaining part after the base
    //     const remainingfind = idPartfind.slice(1).join('_');
    //     console.log("idsss " + remainingfind)

    //     setCounterstrength++;
    //     const originalSet = document.getElementById(id);
    //     console.log(originalSet);

    //     if (!originalSet) {
    //         console.error('Original set element with id "' + id + '" not found');
    //         return;
    //     }

    //     // Clone the original set
    //     const clone = originalSet.cloneNode(true);

    //     // Update the id and name attributes of the input field
    //     const input = clone.querySelector('input');
    //     if (input) {
    //         input.id = `sets_${remainingfind}${ setCounterstrength}`;
    //         input.name = `sets_${remainingfind}${ setCounterstrength}`;
    //         input.value = '0';
    //     }

    //     // Add remove button
    //     const removeButton = document.createElement('button');
    //     removeButton.textContent = 'Remove';
    //     removeButton.className = 'remove-set bg-red-500 text-white p-2 rounded';
    //     removeButton.onclick = function() {
    //         clone.remove();
    //     };

    //     // Append the remove button to the clone
    //     const buttonContainer = document.createElement('div');
    //     buttonContainer.className = 'flex justify-end mb-2'; // Ensure the button is aligned properly
    //     buttonContainer.appendChild(removeButton);
    //     clone.appendChild(buttonContainer);

    //     // Create a new element from the setui string and append it to the duplicate-sets container
    //     const setuiElement = document.createElement('div');
    //     setuiElement.innerHTML = `
    //     <div class="flex items-center sets-view">
    //         <label for="custom-numberwe_${remainingfind}${ setCounterstrength}" class="w-60 block mb-1">
    //             SETS <span class="text-red-500">*</span>
    //         </label>
    //         <div class="relative flex items-center max-w-[8rem]">
    //             <button type="button" class="decrement-custom bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none"  onclick="decrement(this.parentNode.querySelector('input').id)">
    //                 <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
    //                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
    //                 </svg>
    //             </button>
    //             <input type="text" id="sets_${remainingfind}${ setCounterstrength}" name="sets_${remainingfind}${ setCounterstrength}" data-input-counter class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center my-2 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" placeholder="0" readonly />
    //             <button type="button" class="increment-custom bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none"  onclick="increment(this.parentNode.querySelector('input').id)">
    //                 <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
    //                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
    //                 </svg>
    //             </button>
    //         </div>

    //     </div>
    //     <div class="flex items-center border-b">
    //                                 <label for="reps_${remainingfind}${ setCounterstrength}" class="w-60 block mb-1">REPS <span
    //                                         class="text-red-500">*</span></label>
    //                                 <div class="relative flex items-center max-w-[8rem]">
    //                                     <button type="button"
    //                                         onclick="decrement(this.parentNode.querySelector('input').id)"
    //                                         class="decrement-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
    //                                         <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
    //                                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
    //                                             <path stroke="currentColor" stroke-linecap="round"
    //                                                 stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
    //                                         </svg>
    //                                     </button>
    //                                     <input type="text" id="reps_${remainingfind}${ setCounterstrength}" name="reps_${remainingfind}${ setCounterstrength}" data-input-counter
    //                                         class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
    //                                         placeholder="0" readonly />
    //                                     <button type="button"
    //                                         onclick="increment(this.parentNode.querySelector('input').id)"
    //                                         class="increment-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
    //                                         <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
    //                                             xmlns="http://www.w3.org/2000/svg" fill="none"
    //                                             viewBox="0 0 18 18">
    //                                             <path stroke="currentColor" stroke-linecap="round"
    //                                                 stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
    //                                         </svg>
    //                                     </button>
    //                                 </div>

    //         <button type="button" class="remove-set bg-red-500 text-white p-2 rounded ml-2">
    //                 Remove
    //             </button>
    //                             </div>

    //     `;
    //     // Add functionality to the remove button
    //     setuiElement.querySelector('.remove-set').addEventListener('click', function() {
    //         setuiElement.remove();
    //     });

    //     // Find the container element with class 'duplicate-sets' using parentNode traversal
    //     const container = originalSet.closest('.border-b').querySelector('.duplicate-sets-strength');
    //     console.log(container);

    //     if (container) {
    //         container.appendChild(setuiElement);
    //     } else {
    //         console.error('Container element with class "duplicate-sets-strength" not found');
    //     }

    //     console.log(`Duplicated Set ID: ${input ? input.id : 'N/A'}`);
    // }


    // altduplicate Set
    let altsetCounterstrength = 1;

    function altduplicateSetStrength(id) {
        console.log(id);
        const idPartfind = id.split('_');
        const remainingfind = idPartfind.slice(1).join('_');
        console.log("idsss " + remainingfind);

        altsetCounterstrength++;
        const originalSet = document.getElementById(id);
        console.log(originalSet);

        if (!originalSet) {
            console.error('Original set element with id "' + id + '" not found');
            return;
        }

        // Clone the original set
        const altclone = originalSet.cloneNode(true);

        // Update the id and name attributes of the input fields
        const altinputs = altclone.querySelectorAll('input');
        altinputs.forEach((input) => {
            input.id = input.id.replace(/_\d+$/, `_${altsetCounterstrength}`);
            input.name = input.name.replace(/_\d+$/, `_${altsetCounterstrength}`);
            input.value = '0'; // Reset the value
        });

        // Create new HTML for the set and append it to the container
        const altsetuiElement = document.createElement('div');
        altsetuiElement.innerHTML = `
        <div class="flex items-center sets-view">
            <label for="alt-custom-numberwe_${remainingfind}${altsetCounterstrength}" class="w-60 block mb-1">
                SETS <span class="text-red-500">*</span>
            </label>
            <div class="relative flex items-center max-w-[8rem]">
                <button type="button" class="decrement-custom bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none" onclick="decrement(this.parentNode.querySelector('input').id)">
                    <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                    </svg>
                </button>
                <input type="text" id="alt-sets_${remainingfind}${altsetCounterstrength}" name="alt-sets_${remainingfind}${altsetCounterstrength}" data-input-counter class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center my-2 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" placeholder="0" readonly />
                <button type="button" class="increment-custom bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none" onclick="increment(this.parentNode.querySelector('input').id)">
                    <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="flex items-center border-b">
            <label for="alt-reps_${remainingfind}${altsetCounterstrength}" class="w-60 block mb-1">
                REPS <span class="text-red-500">*</span>
            </label>
            <div class="relative flex items-center max-w-[8rem]">
                <button type="button" onclick="decrement(this.parentNode.querySelector('input').id)" class="decrement-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                    <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                    </svg>
                </button>
                <input type="text" id="alt-reps_${remainingfind}${altsetCounterstrength}" name="alt-reps_${remainingfind}${altsetCounterstrength}" data-input-counter class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" placeholder="0" readonly />
                <button type="button" onclick="increment(this.parentNode.querySelector('input').id)" class="increment-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                    <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                    </svg>
                </button>
            </div>
            <button type="button" class="altremove-buttonstrength bg-red-500 text-white p-2 rounded ml-2">
                Remove
            </button>
        </div>
    `;

        // Add the new set UI element to the container
        const altcontainer = originalSet.closest('.border-b').querySelector('.altduplicate-setsstrength');
        console.log(altcontainer);

        if (altcontainer) {
            altcontainer.appendChild(altsetuiElement);

            // Add remove button functionality
            altsetuiElement.querySelector('.altremove-buttonstrength').onclick = function() {
                altsetuiElement.remove();
            };
        } else {
            console.error('Container element with class "altduplicate-setsstrength" not found');
        }

        console.log(`Duplicated Set ID: ${altinputs.length > 0 ? altinputs[0].id : 'N/A'}`);
    }

    // Call getCategoryS on page load
    document.addEventListener('DOMContentLoaded', function() {
        getCategoryS();
    });
</script>

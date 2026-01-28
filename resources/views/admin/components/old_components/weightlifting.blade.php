<div id="weightlifting" hidden>
    {{-- hidden input field --}}
    <input type="text" name="selecttabwe" id="selecttabwe" hidden>


    <div class="flex gap-5 mr-8 rounded-md font-bold text-xl -mt-5 p-0">
        {{-- <div class="flex justify-center text-center items-center w-1/2">Workout List</div>
        <div class="flex justify-center text-center items-center w-1/2">Create</div> --}}
        <form id="deleteFormWe">
            @csrf
            @method('DELETE')
            <input type="text" name="selectdateweDelete" id="selectdateweDelete" hidden>
            {{-- <button type="submit" class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base">Clear</button> --}}
        </form>
        <script>
            //delete selected weightlifting data
            //selected strength delete
            $(document).on('click', '.delete-weightlifting-btn', function () {
                const date = document.getElementById('selectdateweDelete').value;
                const id = $(this).data('id');
                const confirmed = confirm("Are you sure you want to delete this weightlifting record?");

                if (!confirmed) return;

                $.ajax({
                    url: "/delete-weightlifting",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            getWeightlifting(date);
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX error:", error);
                        alert("An error occurred while deleting the weightlifting record.");
                    }
                });
            });

            $(document).ready(function() {
                const date = document.getElementById('selectdateweDelete').value;
                $('#deleteFormWe').on('submit', function(event) {
                    event.preventDefault(); // Prevent the default form submission

                    $.ajax({
                        url: '{{ route('Weightlifting.deleteAllBySelectDate') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE',
                            selectdateweDelete: $('#selectdateweDelete').val()
                        },
                        success: function(response) {
                            // Handle the response
                            alert(response.message);
                            getWeightlifting(date);
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
                const tab = document.getElementById('weightliftingTab');

                $('#storeFromWe').on('submit', function(event) {
                    event.preventDefault(); // Prevent the default form submission
                    const formDataArray = $(this).serializeArray();
                    console.log('Form data as object:', formDataArray);
                    const weightliftingId = $('#weightlifting_id').val();

                    const url = weightliftingId ? `/update-Weightlifting` : '/store-weightlifting';
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: $(this).serialize(), // Serialize form data
                        success: function(response) {
                            // Handle the response
                            // Clear input fields
                            $('#storeFromWe')[0].reset();
                            $('#weightlifting_id').val('');

                            document.getElementById('savebtnwe').textContent = "Save"; // Reset button
                            document.getElementById('clearwbtn').classList.add('hidden');
                            // Clear specific elements
                            const cloneDisplayContainer = document.getElementById(
                                'cloneDisplayContainer');
                            if (cloneDisplayContainer) {
                                cloneDisplayContainer.innerHTML = ''; // Clear content
                            }

                            console.log('Response from server:', response);
                            // Optionally, update the UI to reflect the changes
                            alert(response.message); // Uncomment if you want to show a message

                            var duplicateSets = document.querySelector(".duplicate-sets");
                            if (duplicateSets) {
                                duplicateSets.innerHTML = "";
                                setCounter = 1; // Clear content
                            }

                            var altDuplicateSets = document.querySelector(".altduplicate-setss");
                            if (altDuplicateSets) {
                                altDuplicateSets.innerHTML = ""; // Clear content
                            }
                            // Optionally, update the UI or trigger other actions
                            // Trigger the tab click
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
    {{-- display  weightlifting --}}
    <div id="weightlifting-container" class="-mt-5 pt-2"></div>


    <form id="storeFromWe">
        @csrf
        <input type="text" name="selectdatewe" id="selectdatewe" hidden>
        <div class="duplicateUi flex flex-col text-lg mr-8 gap-4 mb-4 " id="uiContainer">
            <div class="" data-index="1">
                <div class="ui-block flex flex-col text-lg px-4 gap-4 mb-4 ">
                    <!-- Your UI block content here -->
                    <div class="flex gap-5 justify-between">
                        {{-- Serach Section --}}
                        <div class="flex-col w-1/2">
                            <div class="bg-gray-50 p-4">
                                <div class="flex justify-center text-center items-center font-bold mb-3 text-2xl">Workout List</div>
                                <div class="flex items-center space-x-2 flex-nowrap">
                                    <!-- Category Field -->
                                    <div class="flex items-center space-x-1 flex-[1] min-w-[120px]">
                                        <label for="categorywe_2" class="w-15 text-xs">Category</label>
                                        <select id="categorywe_2" name="categorywe_2" onchange="getworkoutWe(this)"
                                            class="flex-1 px-1 py-1 border rounded text-xs bg-white">
                                            <option value="" selected disabled>-- Select --</option>
                                        </select>
                                    </div>
                                    <!-- Exercise Field -->
                                    <div class="flex items-center space-x-1 flex-[1] min-w-[120px]">
                                        <label for="workoutwe_2" class="w-15 text-xs pl-3 ml-0">Exercise</label>
                                        <select id="workoutwe_2" name="workoutwe_2"
                                            class="flex-1 px-1 py-1 border rounded text-xs bg-white">
                                            <option value="" selected disabled>-- Select --</option>
                                        </select>
                                    </div>
                                    <!-- Name Field -->
                                    <div class="flex items-center space-x-1 flex-[1] min-w-[120px]">
                                        <label for="namewe_2" class="w-15 text-xs">Name</label>
                                        <input type="text" id="namewe_2" name="namewe_2"
                                            class="flex-1 px-1 py-1 border rounded text-xs">
                                    </div>
                                    <!-- Go Button -->
                                    <div class="flex items-center">
                                        <button id="addsetstrength_1" onclick="filterWeightlifting(date)" type="button"
                                            class="bg-black text-white py-1 px-2 rounded text-xs">Go</button>
                                    </div>
                                    <!-- Clear Button -->
                                    <div class="flex items-center">
                                        <button id="searchclearsetweightlifting_1" onclick="clearSearchWeightlifting()" type="button"
                                            class="bg-black text-white py-1 px-2 rounded text-xs">Clear</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Scroll Section -->
                            <div id="setweights" class="mt-4 max-h-[600px] overflow-y-auto space-y-4">
                            </div>

                        </div>
                        {{-- Primary Category and Workouts --}}

                        <div class="flex-col w-1/2 bg-gray-50 p-4">
                            <div class="flex justify-center text-center items-center font-bold mb-3 text-2xl">Create</div>
                            <input type="hidden" id="weightlifting_id" name="weightlifting_id" value="">
                            <div class="flex items-center border-b mt-2">
                                <label for="namewe_1" class="w-40 block mb-1">Workout Name <span class="text-red-500">*</span></label>
                                <input type="text" id="namewe_1" name="namewe_1"
                                    class="w-2/3 px-3 py-3 border flex rounded mb-2" required>

                            </div>
                            <div class="flex items-center border-b">
                                <label for="categorywe_1" class="w-40 block mb-1">Category <span
                                        class="text-red-500">*</span></label>
                                <select id="categorywe_1" name="categorywe_1" onchange="getworkoutWe(this)"
                                    class="w-2/3 px-3 py-3 border flex rounded mb-2" required>
                                    <option value="" selected disabled>-- Select Category --</option>
                                </select>
                            </div>
                            <div class="flex items-center border-b mt-2">
                                <label for="workoutwe_1" class="w-40 block mb-1">Workout <span
                                        class="text-red-500">*</span></label>
                                <select id="workoutwe_1" name="workoutwe_1"
                                    class="w-2/3 px-3 py-3 border flex rounded mb-2" required>
                                    <option value="" selected disabled>-- Select Workout --</option>
                                </select>
                            </div>
                            <div class="flex items-center border-b mt-2 overflow-visible">
                                <label for="weigthwe_1" class="w-40 block mb-1">Training Load <span class="text-red-500">*</span></label>
                                <div class="flex items-center w-2/3">
                                    <input type="text" id="weigthwe_1" name="weigthwe_1" class="flex-grow px-1 py-2 border border-r-0 mr-2" required>
                                    <select name="unitwe_1" class="border bg-white py-3 px-2 w-16 text-center border-l-0">
                                        <option value="%">%</option>
                                        <option value="/10">/10</option>
                                        <option value="Cal">Cal</option>
                                        <option value="Kg">Kg</option>
                                    </select>
                                </div>
                                
                                <div class="relative ml-4 flex items-center">
                                    <span onclick="toggleInfoPopup(this, event)" class="text-red-500 text-xl cursor-pointer select-none">
                                        <svg width="20px" height="20px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11 12H9v-.148c0-.876.306-1.499 1-1.852.385-.195 1-.568 1-1a1.001 1.001 0 00-2 0H7c0-1.654 1.346-3 3-3s3 1 3 3-2 2.165-2 3zm-2 3h2v-2H9v2z" fill="#5C5F62"/>
                                            <path d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1116 0 8 8 0 01-16 0z" fill="#5C5F62"/>
                                        </svg>
                                    </span>
                                    <!-- Popup -->
                                    <div class="hidden absolute left-[-110px] top-[45px] w-44 bg-white border shadow-lg p-3 rounded text-sm z-50 info-popup">
                                        <p><b>/10</b> – Effort out of 10</p>
                                        <p><b>%</b> – Percentage of effort</p>
                                        <p><b>Cal</b> – Number of calories</p>
                                        <p><b>Kg</b> – Weight</p>
                                    </div>
                                </div>
                            </div>
                            <div class="border-b mt-2" id="duplicateSetUI">
                                <div class="">
                                    <div class="flex items-center sets-view">
                                        <label for="setswe_1" class="w-40 block mb-1">SET <span class="text-red-500">*</span></label>
                                        <input type="text" id="setwid_1" name="setwid_1"  class="hidden"/>
                                        <div class="relative flex items-center gap-1" id="duplicateRepsUI">
                                            <!-- Optional extra input (first one) -->
                                            <input type="text" id="setswe_1" name="setswe_1" value="1" data-input-counter
                                                class="w-5 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                placeholder="0" readonly required />

                                            {{-- <label for="repswe_1" class="text-sm mr-2">REPS</label> --}}

                                            <!-- Decrement Button -->
                                            <button type="button"
                                                onclick="decrement(this.parentNode.querySelector('input#repswe_1').id)"
                                                class="decrement-reps bg-gray-700 text-white hover:bg-gray-700 border border-gray-600 rounded-s-lg p-3 h-11 focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>

                                            <!-- REPS Counter Input -->
                                            <input type="text" id="repswe_1" name="repswe_1" data-input-counter
                                                class="w-7 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                placeholder="0" readonly required />

                                            <!-- Increment Button -->
                                            <button type="button"
                                                onclick="increment(this.parentNode.querySelector('input#repswe_1').id)"
                                                class="increment-reps bg-gray-700 text-white hover:bg-gray-700 border border-gray-600 rounded-e-lg p-3 h-11 focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                            <label for="repswe_1" class="text-sm mr-2">REPS</label>

                                            <!-- Weight Stepper (Vertical) -->
                                            <div class="relative w-24 ml-2">
                                                <input type="text" id="setweightwe_1" name="setweightwe_1" data-input-counter
                                                    class="peer w-full bg-gray-50 border border-gray-300 rounded-lg h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 pr-8 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                    placeholder="80%" required />
                                                <div class="absolute inset-y-0 right-9 flex items-center pointer-events-none text-gray-500 dark:text-gray-400 transition-opacity duration-200 peer-placeholder-shown:opacity-0">
                                                    %
                                                </div>
                                                <div class="absolute inset-y-0 right-0 flex flex-col w-6">
                                                    <button type="button" onclick="increment('setweightwe_1')" class="h-1/2 flex items-center justify-center text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline-none">
                                                        <svg class="w-2.5 h-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 10">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 9l8-8 8 8"/>
                                                        </svg>
                                                    </button>
                                                    <button type="button" onclick="decrement('setweightwe_1')" class="h-1/2 flex items-center justify-center text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline-none">
                                                        <svg class="w-2.5 h-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 10">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l8 8 8-8"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            {{-- <label for="setweightwe_1" class="text-sm mr-2">%</label> --}}
                                        </div>
                                    </div>

                                    <div class="duplicate-sets" id="duplicate-sets_1"></div>
                                    <div class="ml-40">
                                        <button id="addset_1" onclick="duplicateSet(this.id)" type="button"
                                            class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base">
                                            <i class="fas fa-plus text-[12px]"></i> Add set</button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center border-b">
                                <label for="restredwe_1" class="w-40 block mb-1">Rest <span
                                        class="text-red-500">*</span></label>
                                <div class="">
                                    <div class="relative flex items-center max-w-[12rem] mb-4">
                                        <label class="text-red-500 font-bold w-16 text-right pr-8">RSet</label>
                                        <button type="button"
                                            onclick="decrementRest(this.parentNode.querySelector('input').id)"
                                            class="decrement-rest bg-gray-700 text-white hover:bg-gray-700 border border-gray-600 rounded-s-lg p-3 h-11 focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" id="restredwe_1" name="restredwe_1"
                                            placeholder="00:00" value="00:04:00"
                                            class="bg-gray-50 border-x-0 restwered border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                            readonly required>
                                        <button type="button"
                                            onclick="incrementRest(this.parentNode.querySelector('input').id)"
                                            class="increment-rest bg-gray-700 text-white hover:bg-gray-700 border border-gray-600 rounded-e-lg p-3 h-11 focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>


                                    <div class="relative flex items-center max-w-[12rem] mb-4">
                                        <label class="text-yellow-500 font-bold w-16 text-right pr-8">RSet</label>
                                        <button type="button"
                                            onclick="decrementRest(this.parentNode.querySelector('input').id)"
                                            class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" id="restyellowwe_1" name="restyellowwe_1"
                                            placeholder="00:00" value="00:04:00"
                                            class="bg-gray-50 border-x-0 restweyellow border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                            readonly required>
                                        <button type="button"
                                            onclick="incrementRest(this.parentNode.querySelector('input').id)"
                                            class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>


                                    <div class="relative flex items-center max-w-[12rem] mb-4">
                                        <label class="text-green-500 font-bold w-16 text-right pr-8">RSet</label>
                                        <button type="button"
                                            onclick="decrementRest(this.parentNode.querySelector('input').id)"
                                            class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" id="restgreenwe_1" name="restgreenwe_1"
                                            placeholder="00:00" value="00:04:00"
                                            class="bg-gray-50 border-x-0 restwegreen border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                            readonly required>
                                        <button type="button"
                                            onclick="incrementRest(this.parentNode.querySelector('input').id)"
                                            class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>

                                </div>
                            </div>
                            <div class="flex items-center border-b ">
                                <label for="intensitywe_1" class="w-40 block mb-1">Intensity</label>
                                <select id="intensitywe_1" name="intensitywe_1"
                                    class="w-2/3 px-3 py-3 border flex rounded my-2">
                                    <option value="" selected disabled>-- Select Intensity --</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="extreme">Extreme</option>
                                </select>
                            </div>
                        </div>

                        {{-- <div class="flex-col w-full"> --}}
                            <!-- Alternate Category and Workouts -->
                            {{-- <div class="flex items-center border-b mt-2">
                                <label for="alt-namewe_1" class="w-60 block mb-1">Workout Name </label>
                                <input type="text" id="alt-namewe_1" name="alt-namewe_1"
                                    class="w-1/3 px-3 py-3 border flex rounded mb-2">
                            </div>
                            <div class="flex items-center border-b">
                                <label for="alt-categorywe_1" class="w-60 block mb-1">Category </label>
                                <select id="alt-categorywe_1" name="alt-categorywe_1" onchange="getworkoutWe(this)"
                                    class="w-1/3 px-3 py-3 border flex rounded mb-2">
                                    <option value="" selected disabled>-- Select Category --</option>
                                </select>
                            </div>
                            <div class="flex items-center border-b mt-2">
                                <label for="alt-workoutwe_1" class="w-60 block mb-1">Exercise </label>
                                <select id="alt-workoutwe_1" name="alt-workoutwe_1"
                                    class="w-1/3 px-3 py-3 border flex rounded mb-2">
                                    <option value="" selected disabled>-- Select Exercise --</option>
                                </select>
                            </div>
                            <div class="flex items-center border-b mt-2">
                                <label for="alt-weigthwe_1" class="w-60 block mb-1">Weight Precentage </label>
                                <input type="number" id="alt-weigthwe_1" name="alt-weigthwe_1"
                                    class="w-1/3 px-3 py-3 border flex rounded mb-2">
                                <label for="" class="border bg-white py-3 px-3 mb-2">%</label>
                            </div>

                            <div class="border-b mt-2" id="altduplicateSetUI">
                                <div class="">
                                    <div class="flex items-center sets-view">
                                        <label for="alt-repsnowe_1" class="w-60 block mb-1">SET <span class="text-red-500">*</span></label>
                                        <div class="relative flex items-center max-w-[12rem] gap-2" id="duplicateRepsUI"> --}}
                                            <!-- Optional extra input (first one) -->
                                            {{-- <input type="text" id="alt-repsnowe_1" name="alt-repsnowe_1" value="1" data-input-counter
                                                class="w-5 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                placeholder="0" readonly required />

                                            <label for="alt-repswe" class="text-sm mr-2">REPS</label> --}}

                                            <!-- Decrement Button -->
                                            {{-- <button type="button"
                                                onclick="decrement(this.parentNode.querySelector('input#alt-repswe_1').id)"
                                                class="decrement-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button> --}}

                                            <!-- REPS Counter Input -->
                                            {{-- <input type="text" id="alt-repswe_1" name="alt-repswe_1" data-input-counter
                                                class="w-7 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                placeholder="0" readonly required /> --}}

                                            <!-- Increment Button -->
                                            {{-- <button type="button"
                                                onclick="increment(this.parentNode.querySelector('input#alt-repswe_1').id)"
                                                class="increment-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="altduplicate-setss" id="alt-duplicate-sets_1"></div>
                                    <div class="ml-60">
                                        <button id="altaddset_1" onclick="altduplicateSet(this.id)" type="button"
                                            class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base">
                                            <i class="fas fa-plus text-[12px]"></i> Add set</button>
                                    </div>
                                </div>
                            </div> --}}


                            {{-- alternaterest --}}
                            {{-- <div class="flex items-center border-b ">
                                <label for="alt-restwe_1" class="w-60 block mb-1">Rest </label>
                                <div class="">
                                    <div class="relative flex items-center max-w-[12rem] mb-4 py-2">
                                        <label class="text-red-500 font-bold w-16 text-right pr-8">Stage&nbsp;1</label>
                                        <button type="button"
                                            onclick="decrementRest(this.parentNode.querySelector('input').id)"
                                            class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" id="alt-restredwe_1" name="alt-restredwe_1"
                                            placeholder="00:00" value="04:00"
                                            class="bg-gray-50 border-x-0 restaltwered border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                            readonly required>
                                        <button type="button"
                                            onclick="incrementRest(this.parentNode.querySelector('input').id)"
                                            class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>


                                    <div class="relative flex items-center max-w-[12rem] mb-4 py-2">
                                        <label class="text-yellow-500 font-bold w-16 text-right pr-8">Stage&nbsp;2</label>
                                        <button type="button"
                                            onclick="decrementRest(this.parentNode.querySelector('input').id)"
                                            class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" id="alt-restyellowwe_1" name="alt-restyellowwe_1"
                                            placeholder="00:00" value="04:00"
                                            class="bg-gray-50 border-x-0 restaltweyellow border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                            readonly required>
                                        <button type="button"
                                            onclick="incrementRest(this.parentNode.querySelector('input').id)"
                                            class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>



                                    <div class="relative flex items-center max-w-[12rem] mb-4 py-2">
                                        <label class="text-green-500 font-bold w-16 text-right pr-8">Stage&nbsp;3</label>
                                        <button type="button"
                                            onclick="decrementRest(this.parentNode.querySelector('input').id)"
                                            class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" id="alt-restgreenwe_1" name="alt-restgreenwe_1"
                                            placeholder="00:00" value="04:00"
                                            class="bg-gray-50 border-x-0 restaltgreen border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                            readonly required>
                                        <button type="button"
                                            onclick="incrementRest(this.parentNode.querySelector('input').id)"
                                            class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>

                                </div>
                            </div>
                            <div class="flex items-center border-b">
                                <label for="alt-intensitywe_1" class="w-60 block mb-1 ">Intensity</label>
                                <select id="alt-intensitywe_1" name="alt-intensitywe_1"
                                    class="w-1/3 px-3 py-3 border flex rounded mb-2 mt-2">
                                    <option value="" selected disabled>-- Select Intensity --</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="extreme">Extreme</option>
                                </select>
                            </div> --}}
                        {{-- </div> --}}
                    </div>
                </div>
            </div>

        </div>
        <div id="cloneDisplayContainer"></div>
        <div class="flex flex-row justify-end gap-4 mt-5">
            <button type="button" id="clearwbtn" onclick="clearWeightliftingForm()"
                class="bg-gray-200 text-gray-800 py-2 px-4 rounded hover:bg-gray-300 w-24 hidden">
                Cancel
            </button>

            <button type="submit" id="savebtnwe" class="bg-[#FB1018] text-white py-2 px-4 rounded mr-8 hover:bg-red-700 w-24">
                Save
            </button>
        </div>

    </form>
</div>



{{-- Clone Ui Script --}}
<script>
    // Function to update input names and IDs for the cloned element
    function updateNamesAndIds(element, index) {
        element.querySelectorAll("input, select, button").forEach(function(el) {
            var baseName = el.name.split('_')[0];
            var baseId = el.id.split('_')[0];
            el.name = baseName + '_' + index;
            el.id = baseId + '_' + index;
        });
        // List of class names to update values for
        const timeClassNames = [
            'restwered', 'restweyellow', 'restwegreen',
            'restaltwered', 'restaltgreen', 'restaltweyellow'
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
    function addRemoveButtonToElement(element) {
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
            element.remove();
        });
        element.appendChild(removeButton);
    }

    // Function to handle the duplication of UI elements
    function handleUiDuplication(event) {
        var cloneButton = event.target;
        var originalUiElement = cloneButton.closest(
            '.duplicateUi'); // Adjusted to find the closest element with class `duplicateUi`

        // Retrieve or set the index for the new clone
        var index = parseInt(originalUiElement.dataset.index) || 1;
        index += 1;
        originalUiElement.dataset.index = index;

        // Clone the original UI element
        var clonedElement = originalUiElement.cloneNode(true);

        // Clear content of duplicate-sets in the cloned element
        var duplicateSets = clonedElement.querySelector(".duplicate-sets");
        if (duplicateSets) {
            duplicateSets.innerHTML = ""; // Clear content
        }

        // Reset input values in the cloned element
        clonedElement.querySelectorAll("input").forEach(function(input) {
            input.value = '';
        });

        // Update names and IDs in the cloned element
        updateNamesAndIds(clonedElement, index);

        // Remove old buttons and add a new remove button
        clonedElement.querySelectorAll(".duplicateBtn, .removeBtn").forEach(function(button) {
            button.remove();
        });
        addRemoveButtonToElement(clonedElement);

        // Append the cloned element to the display container
        document.getElementById('cloneDisplayContainer').appendChild(clonedElement);
    }

    // Event listener for the main clone button
    document.getElementById('cloneButton').addEventListener('click', function() {
        const uiContainer = document.getElementById('uiContainer');
        const cloneDisplayContainer = document.getElementById('cloneDisplayContainer');
        const newContainer = uiContainer.cloneNode(true);

        // Retrieve or set the index for the new clone
        var index = parseInt(uiContainer.dataset.index) || 1;
        index += 1;
        uiContainer.dataset.index = index;

        // Clear content of duplicate-sets in the cloned container
        var duplicateSets = newContainer.querySelector(".duplicate-sets");
        if (duplicateSets) {
            duplicateSets.innerHTML = ""; // Clear content
        }

        // Clear content of duplicate-sets in the cloned container
        var altduplicateSets = newContainer.querySelector(".altduplicate-setss");
        if (altduplicateSets) {
            altduplicateSets.innerHTML = ""; // Clear content
        }

        // Reset input values in the new container
        newContainer.querySelectorAll("input").forEach(function(input) {
            input.value = '';
        });

        // Update names and IDs in the new container
        updateNamesAndIds(newContainer, index);

        // Add the remove button to the new container
        addRemoveButtonToElement(newContainer);

        // Append the new container to the display container
        cloneDisplayContainer.appendChild(newContainer);
    });

    // Add event listeners to all duplicate buttons
    document.querySelectorAll(".duplicateBtn").forEach(function(button) {
        button.addEventListener("click", handleUiDuplication);
    });
</script>






















<script>
    // Rest increment Increment
    function incrementRest(id) {
    let input = document.getElementById(id);
    let [hours, minutes, seconds] = input.value.split(':').map(Number);

    if (isNaN(hours)) hours = 0;
    if (isNaN(minutes)) minutes = 0;
    if (isNaN(seconds)) seconds = 0;

    let totalSeconds = (hours * 3600) + (minutes * 60) + seconds + 15;

    hours = Math.floor(totalSeconds / 3600);
    minutes = Math.floor((totalSeconds % 3600) / 60);
    seconds = totalSeconds % 60;

    input.value = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}


    // Rest increment Decrement
    function decrementRest(id) {
    let input = document.getElementById(id);
    let [hours, minutes, seconds] = input.value.split(':').map(Number);

    if (isNaN(hours)) hours = 0;
    if (isNaN(minutes)) minutes = 0;
    if (isNaN(seconds)) seconds = 0;

    let totalSeconds = (hours * 3600) + (minutes * 60) + seconds - 15;
    totalSeconds = Math.max(0, totalSeconds); // Prevent negative time

    hours = Math.floor(totalSeconds / 3600);
    minutes = Math.floor((totalSeconds % 3600) / 60);
    seconds = totalSeconds % 60;

    input.value = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}



    // Function to set category options
    function setCategory(id, categoryName, selectId) {
        const categorySelect = document.getElementById(selectId);
        const option = document.createElement('option');
        option.value = id;
        option.text = categoryName;
        categorySelect.add(option);
    }

    // Function to get categories via AJAX
    // Function to get categories via AJAX
    function getcategorywe() {
        const selectTab = "weightlifting";

        $.ajax({
            url: "/getCategory",
            type: "POST",
            data: {
                tab: selectTab,
                _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
            },
            success: function(response) {
                console.log('weeeeeeee',response);
                // Extract the category_options array from the response
                const categoryOptions = response.category_options || [];

                // Clear existing options in the select elements before adding new ones
                ['categorywe_1','categorywe_2'].forEach(id => {
                    const categoryweelect = document.getElementById(id);
                    categoryweelect.innerHTML =
                        '<option value="" selected disabled>-- Select Category --</option>';
                });

                // Loop through each category_option and call the setCategory() function
                categoryOptions.forEach(option => {
                    setCategory(option.id, option.category_name, 'categorywe_1');
                    setCategory(option.id, option.category_name, 'categorywe_2');
                });

                // Optional: Sort the options alphabetically if needed
                ['categorywe_1','categorywe_2'].forEach(id => {
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
    function getworkoutWe(selectElement) {
        console.log(selectElement);
        const tab = "weightlifting";
        const selectId = selectElement.value; // Get the value of the selected element

        // Split the ID of the select element by underscores
        const idParts = selectElement.id.split('_');

        // Get the remaining part after the base
        const remainingPart = idParts.slice(1).join('_');
        const isCategory = selectElement.id === `categorywe_${remainingPart}`;
        const isECategory = selectElement.id === `categoryweight_${remainingPart}`;

        let workoutSelectId;

        if (isCategory) {
            workoutSelectId = `workoutwe_${remainingPart}`;
        } else if (isECategory) {
            workoutSelectId = `workoutweight_${remainingPart}`;
        }

        console.log(`Constructed workoutSelectId: ${workoutSelectId}`);

        const workoutSelect = document.getElementById(workoutSelectId);

        if (!workoutSelect) {
            console.error(`Element with ID ${workoutSelectId} not found in the DOM.`);
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
    function clearOptions(selectElement) {
        selectElement.innerHTML = '<option value="" selected disabled>-- Select Workout --</option>';
    }




    // duplicate Set
    let setCounter = 1;

    function duplicateSet(id) {
        console.log(id)
        const idPartfind = id.split('_');

        // Get the remaining part after the base
        const remainingfind = idPartfind.slice(1).join('_');
        console.log("idsss " + remainingfind)

        setCounter++;
        const originalSet = document.getElementById(id);
        console.log(originalSet);

        // Determine default weight based on context (Create vs Edit)
        let defaultWeight = '';
        if (originalSet.closest('#weightlifting-container')) {
            // Edit mode (items loaded dynamically)
            const input = document.getElementById(`weigthweight_${remainingfind}`);
            if (input) defaultWeight = input.value;
        } else {
            // Create mode (static form)
            const input = document.getElementById('weigthwe_1');
            if (input) defaultWeight = input.value;
        }

        if (!originalSet) {
            console.error('Original set element with id "' + id + '" not found');
            return;
        }

        // Clone the original set
        const clone = originalSet.cloneNode(true);

        // Update the id and name attributes of the input field
        const input = clone.querySelector('input');
        if (input) {
            input.id = `setswe_${remainingfind}${setCounter}`;
            input.name = `setswe_${remainingfind}${setCounter}`;
            input.value = '0';
        }

        // Add remove button
        const removeButton = document.createElement('button');
        removeButton.textContent = 'Remove';
        removeButton.className = 'remove-set bg-red-500 text-white p-2 rounded w-0';
        removeButton.onclick = function() {
            clone.remove();
        };

        // Append the remove button to the clone
        const buttonContainer = document.createElement('div');
        buttonContainer.className = 'flex justify-end mb-2'; // Ensure the button is aligned properly
        buttonContainer.appendChild(removeButton);
        clone.appendChild(buttonContainer);

        // Create a new element from the setui string and append it to the duplicate-sets container
        const setuiElement = document.createElement('div');
        setuiElement.innerHTML = `
        <div class="flex items-center sets-view mt-1">
                                        <div class="w-40 block mb-1"></div>
        <div class="relative flex items-center gap-1" id="duplicateRepsUI">
                                            <!-- Optional extra input (first one) -->
                                            <input type="text" id="setswe_${remainingfind}${setCounter}" name="setswe_${remainingfind}${setCounter}" value="${setCounter}" data-input-counter
                                                class="w-5 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                placeholder="0" readonly required />



                                            <!-- Decrement Button -->
                                            <button type="button"
                                                onclick="decrement(this.parentNode.querySelector('input#repswe_${remainingfind}${setCounter}').id)"
                                                class="decrement-reps bg-gray-800 text-white hover:bg-gray-700 border border-gray-600 rounded-s-lg p-3 h-11 focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>

                                            <!-- REPS Counter Input -->
                                            <input type="text" id="repswe_${remainingfind}${setCounter}" name="repswe_${remainingfind}${setCounter}" data-input-counter
                                                class="w-7 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                placeholder="0" readonly required />

                                            <!-- Increment Button -->
                                            <button type="button"
                                                onclick="increment(this.parentNode.querySelector('input#repswe_${remainingfind}${setCounter}').id)"
                                                class="increment-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                            <label for="repsnowe_${remainingfind}${setCounter}" class="text-sm mr-2">REPS</label>

                                            <!-- Weight Stepper (Vertical) -->
                                            <div class="relative w-24 ml-2">
                                                <input type="text" id="setweightwe_${remainingfind}${setCounter}" name="setweightwe_${remainingfind}${setCounter}" data-input-counter
                                                 value="${defaultWeight}"
                                                    class="peer w-full bg-gray-50 border border-gray-300 rounded-lg h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 pr-8 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                    placeholder="80%" required />
                                                <div class="absolute inset-y-0 right-9 flex items-center pointer-events-none text-gray-500 dark:text-gray-400 transition-opacity duration-200 peer-placeholder-shown:opacity-0">
                                                    %
                                                </div>
                                                <div class="absolute inset-y-0 right-0 flex flex-col w-6">
                                                    <button type="button" onclick="increment('setweightwe_${remainingfind}${setCounter}')" class="h-1/2 flex items-center justify-center text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline-none">
                                                        <svg class="w-2.5 h-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 10">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 9l8-8 8 8"/>
                                                        </svg>
                                                    </button>
                                                    <button type="button" onclick="decrement('setweightwe_${remainingfind}${setCounter}')" class="h-1/2 flex items-center justify-center text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline-none">
                                                        <svg class="w-2.5 h-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 10">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l8 8 8-8"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                        </div>

            <button type="button" class="remove-set bg-red-500 text-white p-2 rounded ml-1 w-15">
                    Remove
            </button>
        </div>
                                </div>

        `;
        // Add functionality to the remove button
        setuiElement.querySelector('.remove-set').addEventListener('click', function() {
            setuiElement.remove();
        });

        // Find the container element with class 'duplicate-sets' using parentNode traversal
        const container = originalSet.closest('.border-b').querySelector('.duplicate-setsClone') || originalSet.closest(
            '.border-b').querySelector('.duplicate-sets');
        console.log(container);

        if (container) {
            container.appendChild(setuiElement);
        } else {
            console.error('Container element with class "duplicate-sets" not found');
        }

        console.log(`Duplicated Set ID: ${input ? input.id : 'N/A'}`);

    }


    // altduplicate Set
    let altsetCounter = 1;

    function altduplicateSet(id) {
        console.log(id);
        const idPartfind = id.split('_');
        const remainingfind = idPartfind.slice(1).join('_');
        console.log("idsss " + remainingfind);

        altsetCounter++;
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
            input.id = input.id.replace(/_\d+$/, `_${altsetCounter}`);
            input.name = input.name.replace(/_\d+$/, `_${altsetCounter}`);
            input.value = '0'; // Reset the value
        });

        // Create new HTML for the set and append it to the container
        const altsetuiElement = document.createElement('div');
        altsetuiElement.innerHTML = `
        <div class="flex items-center sets-view mt-1">
                                        <div class="w-32 block mb-1"></div>
        <div class="relative flex items-center gap-2" id="altduplicateSetUI">
                                            <!-- Optional extra input (first one) -->
                                            <input type="text" id="alt-repsnowe_${remainingfind}${altsetCounter}" name="alt-repsnowe_${remainingfind}${altsetCounter}" value="${altsetCounter}" data-input-counter
                                                class="w-5 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                placeholder="0" readonly required />

                                            <label for="repsnowe_${remainingfind}${altsetCounter}" class="text-sm mr-2">REPS</label>

                                            <!-- Decrement Button -->
                                            <button type="button"
                                                onclick="decrement(this.parentNode.querySelector('input#alt-repswe_${remainingfind}${altsetCounter}').id)"
                                                class="decrement-reps bg-gray-800 text-white hover:bg-gray-700 border border-gray-600 rounded-s-lg p-3 h-11 focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>

                                            <!-- REPS Counter Input -->
                                            <input type="text" id="alt-repswe_${remainingfind}${altsetCounter}" name="alt-repswe_${remainingfind}${altsetCounter}" data-input-counter
                                                class="w-7 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                placeholder="0" readonly required />

                                            <!-- Increment Button -->
                                            <button type="button"
                                                onclick="increment(this.parentNode.querySelector('input#alt-repswe_${remainingfind}${altsetCounter}').id)"
                                                class="increment-reps bg-gray-800 text-white hover:bg-gray-700 border border-gray-600 rounded-e-lg p-3 h-11 focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                        </div>

            <button type="button" class="remove-set bg-red-500 text-white p-2 rounded ml-2">
                    Remove
            </button>
        </div>
                                </div>
    `;

        // Add the new set UI element to the container
        const altcontainer = originalSet.closest('.border-b').querySelector('.altduplicate-setssClone') || originalSet
            .closest('.border-b').querySelector('.altduplicate-setss');
        console.log(altcontainer);

        if (altcontainer) {
            altcontainer.appendChild(altsetuiElement);

            // Add remove button functionality
            altsetuiElement.querySelector('.altremove-button').onclick = function() {
                altsetuiElement.remove();
            };
        } else {
            console.error('Container element with class "altduplicate-setss" not found');
        }

        console.log(`Duplicated Set ID: ${altinputs.length > 0 ? altinputs[0].id : 'N/A'}`);

    }





    // Call getcategorywe on page load
    document.addEventListener('DOMContentLoaded', function() {
        getcategorywe();
    });



    // Update first set weight when Training Load changes
    document.getElementById('weigthwe_1').addEventListener('input', function() {
        const val = this.value;
        const set1Input = document.getElementById('setweightwe_1');
        if (set1Input) {
            set1Input.value = val;
        }
    });

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
</script>










{{-- get weightlifting --}}
<script>
    let allWeightliftingData = [];
    function getWeightlifting(date) {
        console.log(date);
        console.log("get Weightlifting date: " + date);
        $.ajax({
            url: "/get-Weightlifting",
            type: "POST",
            data: {
                date: date,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // console.log(response);
                allWeightliftingData = response.weightlifting;
                // Assuming response is an array of arrays
                // response.forEach(subArray => {
                    setWeightliftings(response.weightlifting, response.categoryOptions, response.daily_classes);
                console.log('aaaaaaaaaa',response.weightlifting);
                // });
            },

            error: function(xhr, status, error) {
                console.error(error);
            },
        });
    }

    function setWeightlifting(weightlifting, categoryOptions) {
        console.log(weightlifting);

        // Get the container where the information will be displayed
        const container = document.getElementById('weightlifting-container');

        // Clear the container
        container.innerHTML = '';

        // Initialize the htmlContent variable
        let htmlContent = '';

        // Build HTML content
        weightlifting.forEach(item => {
            // Create a string with the primary category options
            let categoryOptionsHTML = '<option value="" selected disabled>-- Select Category --</option>';
            categoryOptions.forEach(category => {
                categoryOptionsHTML +=
                    `<option value="${category.id}" ${category.id == item.category_id ? 'selected' : ''}>${category.category_name}</option>`;
            });

            // Create a string with the alternative category options
            let altCategoryOptionsHTML =
                '<option value="" selected disabled>-- Select Category --</option>';
            categoryOptions.forEach(category => {
                altCategoryOptionsHTML +=
                    `<option value="${category.id}" ${category.id == item.alt_category_id ? 'selected' : ''}>${category.category_name}</option>`;
            });


            // Build the setsHTML for each set in the item
            const setsHTML = Array.isArray(item.sets) ? item.sets.map((set, index) => {
                // Check if sets or reps are null and set visibility accordingly
                const isSetsVisible = set.sets !== null && set.sets !== undefined;
                const isRepsVisible = set.reps !== null && set.reps !== undefined;

                return `
                    <div>
                        ${isRepsVisible ? `
                        <input name="setsid_${index}" value="${set.id}" hidden>
                            <div class="relative flex items-center gap-1" id="duplicateRepsUI">
                                <input type="text" id="repsnowe_${index}" name="repsnowe_${index}" value="${index}" data-input-counter
                                                    class="mb-1 w-5 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                    placeholder="0" readonly required />
                                <label for="custom-numberweight_${index}" class="w-60 block mb-1 text-sm">REPS </label>

                                    <button type="button" onclick="decrement(this.parentNode.querySelector('input').id)" class="decrement-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                        </svg>
                                    </button>
                                    <input type="text" value="${set.reps}" id="repsweight_${index}" name="repsweight_${index}" data-input-counter class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" placeholder="0" readonly required />
                                    <button type="button" onclick="increment(this.parentNode.querySelector('input').id)" class="increment-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                        </svg>
                                    </button>
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
                        <div class="relative flex items-center">
                            <button type="button" onclick="decrement(this.parentNode.querySelector('input').id)" class="decrement-custom bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                </svg>
                            </button>
                            <input type="text" value="${set.alt_sets}" id="altsetsweight_${index}" name="altsetsweight_${index}" data-input-counter class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center my-2 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" placeholder="0" readonly required />
                            <button type="button" onclick="increment(this.parentNode.querySelector('input').id)" class="increment-custom bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                </svg>
                            </button>
                        </div>
                    </div>` : ''}

                    ${isAltRepsVisible ? `
                    <div class="flex items-center border-b">
                        <label for="altrepsweight_${index}" class="w-60 block mb-1">REPS <span class="text-red-500">*</span></label>
                        <div class="relative flex items-center">
                            <button type="button" onclick="decrement(this.parentNode.querySelector('input').id)" class="decrement-reps bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                </svg>
                            </button>
                            <input type="text" value="${set.alt_reps}" id="altrepsweight_${index}" name="altrepsweight_${index}" data-input-counter class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" placeholder="0" readonly required />
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
                <form action="{{ route('updateWeightlifting') }}" method="POST" id="updateWeightlifting_${item.id}">
                    @csrf

                    <input name="id_${item.id}" value="${item.id}" hidden>
                    <div class="flex flex-col text-lg p-4 bg-gray-50 mr-8 rounded-md gap-4 mb-4">
                        <div class="flex gap-5 justify-between">
                            {{-- Primary Category and Workouts --}}
                            <div class="flex-col w-full border-r border-r-black">
                                <div class="item-container">
                                    <div hidden><h3>Item ID: ${item.id}</h3></div>
                                    <div class="flex items-center border-b mt-2">
                                        <label for="nameweight_${item.id}" class="w-60 block mb-1">Workout Name </label>
                                        <input type="test" id="nameweight_${item.id}" name="nameweight_${item.id}" value="${item.workoutname}"
                                            class="w-1/3 px-3 py-3 border flex rounded mb-2" required>
                                    </div>
                                    <div class="flex border-b mt-4">
                                    <label for="categoryweight_${item.id}" class="w-60 block mb-1">Category <span class="text-red-500">*</span></label>
                                    <select id="categoryweight_${item.id}" name="categoryweight_${item.id}" onchange="getworkoutWe(this)" class="w-1/3 px-3 py-3 border rounded mb-2" required>
                                        ${categoryOptionsHTML}
                                    </select>
                                    </div>
                                    <div class="flex items-center border-b">
                                        <label for="workoutweight_${item.id}" class="w-60 block mb-1">
                                            Exercise<span class="text-red-500">*</span>
                                        </label>
                                        <select id="workoutweight_${item.id}" name="workoutweight_${item.id}" class="w-1/3 px-3 py-3 border mt-2 flex rounded mb-2">
                                            <option value="" selected disabled>-- Select Exercise --</option>
                                            <!-- Populate options dynamically -->
                                            <option value="${item.workout_id}" selected>${item.workout_type}</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center border-b mt-2">
                                        <label for="weigthweight_${item.id}" class="w-60 block mb-1">Weight Precentage <span
                                                class="text-red-500">*</span></label>
                                        <div class="relative w-1/3 mb-2">
                                            <input type="number" id="weigthweight_${item.id}" name="weigthweight_${item.id}" value="${item.weight}"
                                                class="peer w-full bg-gray-50 border border-gray-300 rounded-lg h-11 text-right text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block px-3 py-3 pr-8 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                                required placeholder="0">
                                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-500 dark:text-gray-400 transition-opacity duration-200 peer-placeholder-shown:opacity-0">
                                                %
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border-b" id="duplicateSetUI">
                                        <div class="flex items-center">
                                            <label for="setswe_1" class="w-60 block">SETS <span class="text-red-500">*</span></label>
                                            <div class="sets-container">
                                                ${setsHTML}
                                            </div>
                                            <div class="duplicate-setsClone" id="duplicate-sets_${item.id}"></div>
                                        </div>
                                        <div class="ml-60 mt-2"> <!-- move button to separate row -->
                                            <button id="addset_${item.id}" onclick="duplicateSet(this.id)" type="button" class="bg-black text-white py-2 px-4 rounded mb-2 text-base">
                                                <i class="fas fa-plus text-[12px]"></i> Add set
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-center border-b">
                                        <label for="restredweight_${item.id}" class="w-60 block mb-1">Rest <span class="text-red-500">*</span></label>
                                        <div class="">
                                        <div class="relative flex items-center max-w-[12rem] mb-4">
                                             <label class=" text-red-500 font-bold w-16 text-right pr-8">Stage&nbsp;1</label>
                                            <button type="button" onclick="decrementRest(this.parentNode.querySelector('input').id)" class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>
                                            <input type="text" value="${item.restwered}" id="restredweight_${item.id}" name="restredweight_${item.id}" placeholder="00:00" class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" readonly required>
                                            <button type="button" onclick="incrementRest(this.parentNode.querySelector('input').id)" class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="relative flex items-center max-w-[12rem] mb-4">
                                             <label class=" text-yellow-500 font-bold w-16 text-right pr-8">Stage&nbsp;2</label>
                                            <button type="button" onclick="decrementRest(this.parentNode.querySelector('input').id)" class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>
                                            <input type="text" value="${item.restweyellow}" id="restyellowweight_${item.id}" name="restyellowweight_${item.id}" placeholder="00:00" class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" readonly required>
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
                                            <input type="text" value="${item.restwegreen}" id="restgreenweight_${item.id}" name="restgreenweight_${item.id}" placeholder="00:00" class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" readonly required>
                                            <button type="button" onclick="incrementRest(this.parentNode.querySelector('input').id)" class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                        </div>


                                        </div>


                                    </div>


                                    <div class="flex items-center border-b">
                                        <label for="intensityweight_${item.id}" class="w-60 block mb-1">
                                            Intensity <span class="text-red-500">*</span>
                                        </label>
                                        <select id="intensityweight_${item.id}" name="intensityweight_${item.id}"
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
                                <div class="flex items-center border-b mt-2">
                                    <label for="altnameweight_${item.id}" class="w-60 block mb-1">Workout Name </label>
                                    <input type="text" id="altnameweight_${item.id}" name="altnameweight_${item.id}" value="${item.alt_workoutname}"
                                        class="w-1/3 px-3 py-3 border flex rounded mb-2" required>
                                </div>
                                    <div class="flex items-center border-b mt-4">
                                        <label for="altcategoryweight_${item.id}" class="w-60 block mb-1">Category </label>
                                        <select id="altcategoryweight_${item.id}" name="altcategoryweight_${item.id}" onchange="getworkoutWe(this)" class="w-1/3 px-3 py-3 border rounded mb-2 mr-5" required>
                                            ${altCategoryOptionsHTML}
                                        </select>
                                        <button type="button" class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base" onclick="updateweightlifting(${item.id})">Edit</button>
                                    </div>

                                <div class="flex items-center border-b">
                                    <label for="altworkoutweight_${item.category_id}" class="w-60 block mb-1">
                                        Exercise
                                    </label>
                                    <select id="altworkoutweight_${item.id}" name="altworkoutweight_${item.id}" class="w-1/3 px-3 py-3 mt-2 border flex rounded mb-2">
                                        <option value="" selected disabled>-- Select Exercise --</option>
                                        <!-- Populate options dynamically -->
                                        <option value="${item.alt_workout_id}" selected>${item.alt_workout_type}</option>
                                    </select>
                                </div>
                                <div class="flex items-center border-b mt-2">
                                    <label for="altweigthweight_${item.id}" class="w-60 block mb-1">Weight </label>
                                    <div class="relative w-1/3 mb-2">
                                        <input type="number" id="altweigthweight_${item.id}" name="altweigthweight_${item.id}" value="${item.alt_weight}"
                                            class="peer w-full bg-gray-50 border border-gray-300 rounded-lg h-11 text-right text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block px-3 py-3 pr-8 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                            required placeholder="0">
                                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-500 dark:text-gray-400 transition-opacity duration-200 peer-placeholder-shown:opacity-0">
                                            %
                                        </div>
                                    </div>
                                </div>
                                ${altSetsHTML ? `
                                    <div class="border-b" id="duplicateSetUIAlterEdit">
                                        <div class="">
                                            <div>
                                                ${altSetsHTML}
                                            </div>
                                            <div  class="altduplicate-setssClone" id="alt-duplicate-sets_${item.id}"></div>
                                            <div class="ml-60">
                                                <button id="altaddset_${item.id}" onclick="altduplicateSet(this.id)" type="button" class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base">
                                                    <i class="fas fa-plus text-[12px]"></i> Add set
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                ` : ''}
                                <div class="flex items-center border-b">
                                    <label for="altrestredweight_${item.id}" class="w-60 block mb-1">Rest </label>
                                    <div class="">
                                    <div class="relative flex items-center max-w-[12rem]  py-2">
                                          <label class="text-red-500 font-bold w-16 text-right pr-8">Stage&nbsp;1</label>
                                        <button type="button" onclick="decrementRest(this.parentNode.querySelector('input').id)" class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" value="${item.alt_restwered}" id="altrestredweight_${item.id}" name="altrestredweight_${item.id}" placeholder="00:00" class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" readonly required>
                                        <button type="button" onclick="incrementRest(this.parentNode.querySelector('input').id)" class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="relative flex items-center max-w-[12rem]  py-2">
                                         <label class="text-yellow-500 font-bold w-16 text-right pr-8">Stage&nbsp;2</label>
                                        <button type="button" onclick="decrementRest(this.parentNode.querySelector('input').id)" class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" value="${item.alt_restweyellow}" id="altrestyellowweight_${item.id}" name="altrestyellowweight_${item.id}" placeholder="00:00" class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" readonly required>
                                        <button type="button" onclick="incrementRest(this.parentNode.querySelector('input').id)" class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="relative flex items-center max-w-[12rem]  py-2">
                                         <label class="text-green-500 font-bold w-16 text-right pr-8">Stage&nbsp;3</label>
                                        <button type="button" onclick="decrementRest(this.parentNode.querySelector('input').id)" class="decrement-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" value="${item.alt_restwegreen}" id="altrestgreenweight_${item.id}" name="altrestgreenweight_${item.id}" placeholder="00:00" class="bg-gray-50 border-x-0 border-gray-300 h-11 my-2 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" readonly required>
                                        <button type="button" onclick="incrementRest(this.parentNode.querySelector('input').id)" class="increment-rest bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>


                                 </div>
                                </div>
                               <div class="flex items-center border-b">
                                    <label for="altintensityweight_${item.id}" class="w-60 block mb-1">
                                        Intensity
                                    </label>
                                    <select id="altintensityweight_${item.id}" name="altintensityweight_${item.id}"
                                        class="w-1/3 px-3 py-3 border flex rounded my-2" required>
                                        <!-- Dynamically setting the selected option based on item.alt_intensity -->
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


    function filterWeightlifting(date) {
        const dateOnClick = document.getElementById('selectdateweDelete').value;
        console.log('first',dateOnClick)

        //console.log('bbbbbbbb',buttonId)
        let categoryId = document.getElementById("categorywe_2").value;
        let exerciseId = document.getElementById("workoutwe_2").value;
        let nameSearch = document.getElementById("namewe_2").value;

        $.ajax({
            url: "/search-setweightlifting",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                date: dateOnClick,
                name: nameSearch,
                category_id: categoryId,
                workout_id: exerciseId
            },
            success: function (response) {
                console.log("this is filteerd weightlifting response", response);
                allWeightliftingData = response.weightlifting;
                const weightliftingArray = Object.values(response.weightlifting);
                const categoryArray = Object.values(response.categoryOptions);
                const dailyClasses = response.daily_classes || [];
                setWeightliftings(weightliftingArray, categoryArray, dailyClasses);
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
    function clearSearchWeightlifting(){
        const date = document.getElementById('selectdateweDelete').value;

        document.getElementById('categorywe_2').value = '';
        document.getElementById('workoutwe_2').value = '';
        document.getElementById('namewe_2').value = '';
        console.log('dateeeee',date);
        getWeightlifting(date);

    }
    // get filterd weigths
    function setWeightliftings(weightlifting, categoryOptions, availableClasses = []) {
        const container = $("#setweights"); // Replace with your actual container class or ID
        container.empty(); // Clear previous content

        weightlifting.forEach((item, index) => {
            let setsHTML = '';
            // console.log('fffgfgf',)
            if (Array.isArray(item.sets) && item.sets.length > 0) {
                item.sets.forEach((set, idx) => {
                    setsHTML += `
                        <tr>
                            <td class="py-1 pr-4">Set ${idx + 1}</td>
                            <td class="py-1">${set.reps} Reps</td>
                        </tr>
                    `;
                });
            } else {
                setsHTML = '<tr><td colspan="2">No set data</td></tr>';
            }

            const assignedClassIds = item.assigned_class_ids || [];
            
            // Generate Class Buttons
            let classButtonsHTML = '';
            
            if (availableClasses && availableClasses.length > 0) {
                 availableClasses.forEach(cls => {
                     // Check if assigned
                     const isAssigned = assignedClassIds.includes(cls.id);
                     
                     // Format time (e.g. 09:00:00 -> 9:00am)
                     let timeParts = cls.time.split(':');
                     let dateObj = new Date();
                     dateObj.setHours(timeParts[0]);
                     dateObj.setMinutes(timeParts[1]);
                     let timeString = dateObj.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' }).toLowerCase();

                     // Style: Green border/text if assigned, else Gray
                     // Style: Green border/text if assigned, else Gray
                     const activeClass = isAssigned
                         ? 'border-green-600 bg-green-50 text-green-700 font-bold'
                         : 'border-gray-300 bg-white text-gray-600';

                     classButtonsHTML += `
                         <button type="button"
                             class="border px-3 py-1 rounded ${activeClass} hover:bg-gray-100 transition-colors text-sm whitespace-nowrap"
                             onclick="assignWorkoutToClass(${item.id}, ${cls.id}, '${isAssigned ? 'unassign' : 'assign'}', '${item.date || ''}')">
                             ${timeString}
                         </button>
                     `;
                 });
            } else {
                classButtonsHTML = '<span class="text-sm text-gray-500 italic">No classes for this day</span>';
            }
            
            // "All" Button Logic
            const availableClassIds = availableClasses.map(c => c.id);
            const allAssigned = availableClassIds.length > 0 && availableClassIds.every(id => assignedClassIds.includes(id));
            const allButtonClass = allAssigned 
                ? 'border-green-600 bg-green-50 text-green-700 font-bold'
                : 'border-gray-300 text-gray-600';

            // Prepend "All" button
            classButtonsHTML = `
                <button type="button"
                    class="border px-3 py-1 rounded ${allButtonClass} hover:bg-gray-100 transition-colors text-sm"
                    onclick="assignWorkoutToClass(${item.id}, 'all', '${allAssigned ? 'unassign' : 'assign_all'}', '${item.date || ''}')">
                    All
                </button>
            ` + classButtonsHTML;


            let html = `
            <div class="border-2 border-gray-300 rounded-md shadow p-4 bg-white w-full">
                    <div class="pb-2 mb-2 flex justify-between items-center">
                        <div class="text-gray-700 font-bold text-lg">${item.workoutname || 'N/A'} :</div>
                        <div class="space-x-2 flex">
                            <button class="edit-weightlifting-btn text-black hover:text-gray-700" data-id="${item.id}" type="button">
                                <svg class="feather feather-edit" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>

                            <button class="delete-weightlifting-btn text-black hover:text-gray-700" data-id="${item.id}" type="button">
                                <svg fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <path d="M10,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625l12-12c0.390625-0.390625,1.0234375-0.390625,1.4140625,0s0.390625,1.0234375,0,1.4140625l-12,12C10.5117188,22.9023438,10.2558594,23,10,23z"/>
                                        <path d="M22,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688l-12-12c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625s1.0234375-0.390625,1.4140625,0l12,12c0.390625,0.390625,0.390625,1.0234375,0,1.4140625C22.5117188,22.9023438,22.2558594,23,22,23z"/>
                                    </g>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-2 text-gray-800 font-semibold text-base">${item.workout_type} at ${item.weight || 0}${item.unit} for ${item.sets?.length || 0} sets</div>

                    <div class="grid grid-cols-2 gap-4 text-gray-700">
                        <div>
                            <table class="w-full text-left text-sm">
                                <tbody>
                                    ${setsHTML}
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <table class="w-full text-left text-sm">
                                <thead><tr><th class="py-1">Rest:</th><th></th></tr></thead>
                                <tbody>
                                    <tr><td class="py-1 pr-4">Stage 1</td><td class="py-1">${item.restwered || '-'} min</td></tr>
                                    <tr><td class="py-1 pr-4">Stage 2</td><td class="py-1">${item.restweyellow || '-'} min</td></tr>
                                    <tr><td class="py-1 pr-4">Stage 3</td><td class="py-1">${item.restwegreen || '-'} min</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

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
            container.find('.edit-weightlifting-btn').off('click').on('click', function () {
                const weightliftingId = $(this).data('id');
                populateWeightliftingForm(weightliftingId);
            });

        });
    }

    // get editing details to form
    function populateWeightliftingForm(weightliftingId) {

        const data = allWeightliftingData.find(item => item.id == weightliftingId);
        console.log('dataaaaaa',data);

        if (!data) {
            console.warn("No strength data found for ID:", weightliftingId);
            return;
        }

        const card = document.querySelector(`.edit-weightlifting-btn[data-id="${weightliftingId}"]`).closest('.border-2.border-gray-300');
        if (card) {
            card.classList.remove('border-gray-300');
            card.classList.add('border-red-600');
        }
        // Set category and trigger onchange to load workouts
        const categorySelect = document.getElementById('categorywe_1');
        categorySelect.value = data.category_id;
        categorySelect.dispatchEvent(new Event('change')); // To trigger getworkoutS

        // Delay setting workouts to allow async options to load
        setTimeout(() => {
            const workoutSelect = document.getElementById('workoutwe_1');
            workoutSelect.value = data.workout_id;
        }, 500); // Adjust based on how long getworkoutS takes to populate

        // Set weight
        document.getElementById('weigthwe_1').value = data.weight;

       // Clear old duplicated sets
        document.getElementById('duplicate-sets_1').innerHTML = '';
        document.getElementById('setswe_1').value = data.sets[0].sets;
        document.getElementById('repswe_1').value = data.sets[0].reps;
        document.getElementById('setwid_1').value = data.sets[0].id;

        if (data.sets.length > 1) {
            for (let i = 1; i < data.sets.length; i++) {
                const suffix = `1${i + 1}`; // e.g., 12, 13, etc.
                const set = data.sets[i];

                // Create the HTML just like duplicateSet()
                const setuiElement = document.createElement('div');
                setuiElement.innerHTML = `
                <div class="flex items-center sets-view mt-1">
                    <div class="w-60 block mb-1"></div>
                    <input type="text" id="setwid_${suffix}" name="setwid_${suffix}" value="${set.id}" class="hidden"/>
                    <div class="relative flex items-center max-w-[12rem] gap-2">
                        <input type="text" id="setswe_${suffix}" name="setswe_${suffix}" value="${i + 1}" data-input-counter
                            class="w-5 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5"
                            placeholder="80" readonly required />

                        <button type="button"
                            onclick="decrement(this.parentNode.querySelector('input#repswe_${suffix}').id)"
                            class="decrement-reps bg-gray-100 border border-gray-300 rounded-s-lg p-3 h-11">
                            <svg class="w-3 h-3 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                            </svg>
                        </button>

                        <input type="text" id="repswe_${suffix}" name="repswe_${suffix}" value="${set.reps}" data-input-counter
                            class="w-7 bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5"
                            placeholder="80" readonly required />

                        <button type="button"
                            onclick="increment(this.parentNode.querySelector('input#repswe_${suffix}').id)"
                            class="increment-reps bg-gray-100 border border-gray-300 rounded-e-lg p-3 h-11">
                            <svg class="w-3 h-3 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                            </svg>
                        </button>

                        <label for="repswe_${suffix}" class="text-sm mr-2">REPS</label>

                        <!-- Weight Stepper (Vertical) -->
                        <div class="relative w-24 ml-2">
                            <input type="text" id="setweightwe_${suffix}" name="setweightwe_${suffix}" value="${set.weight || 0}" data-input-counter
                                class="peer w-full bg-gray-50 border border-gray-300 rounded-lg h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block py-2.5 pr-8 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                placeholder="80" required />
                            <div class="absolute inset-y-0 right-9 flex items-center pointer-events-none text-gray-500 dark:text-gray-400 transition-opacity duration-200 peer-placeholder-shown:opacity-0">
                                %
                            </div>
                            <div class="absolute inset-y-0 right-0 flex flex-col w-6">
                                <button type="button" onclick="increment('setweightwe_${suffix}')" class="h-1/2 flex items-center justify-center text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline-none">
                                    <svg class="w-2.5 h-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 9l8-8 8 8"/>
                                    </svg>
                                </button>
                                <button type="button" onclick="decrement('setweightwe_${suffix}')" class="h-1/2 flex items-center justify-center text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline-none">
                                    <svg class="w-2.5 h-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l8 8 8-8"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="remove-set bg-red-500 text-white p-2 rounded ml-2">Remove</button>
                </div>`;

                // Add functionality to the remove button
                setuiElement.querySelector('.remove-set').addEventListener('click', function () {
                    setuiElement.remove();
                });

                // Append to container
                const container = document.querySelector('.duplicate-sets');
                container.appendChild(setuiElement);
            }
        }


        document.getElementById('restredwe_1').value = data.restwered;
        document.getElementById('restyellowwe_1').value = data.restweyellow;
        document.getElementById('restgreenwe_1').value = data.restwegreen;
        document.getElementById('intensitywe_1').value = data.intensity;
        document.getElementById('namewe_1').value = data.workoutname;
        document.getElementById('weightlifting_id').value = weightliftingId;
        document.getElementById('savebtnwe').textContent = "Save Edit";


     // Show Clear button
        document.getElementById('clearwbtn').classList.remove('hidden');

    }
    function clearWeightliftingForm() {
        // Clear input fields
        $('#storeFromWe')[0].reset();
        document.getElementById('savebtn').innerHTML = "Save";
        // Show Clear button
        document.getElementById('clearwbtn').classList.add('hidden');
        const redCard = document.querySelector('.border-red-600.border-2');
        if (redCard) {
            redCard.classList.remove('border-red-600');
            redCard.classList.add('border-gray-300');
        }
        // remove duplicat
        const duplicateContainer = document.getElementById('duplicate-sets_1');
        if (duplicateContainer) {
            duplicateContainer.innerHTML = ''; // Remove all appended set blocks
        }
        window.setCounter = 1;
    }

    function updateweightlifting(id) {
        // Construct the form ID dynamically
        const formId = `#updateWeightlifting_${id}`;
        const tab = document.getElementById('weightliftingTab');
        // Serialize form data
        const formData = $(formId).serialize();

        console.log(formData);
        // AJAX request
        $.ajax({
            url: '{{ route('updateWeightlifting') }}',
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

    function assignWorkoutToClass(workoutId, classId, action, date) {
        let confirmMsg = '';
        if (action === 'assign_all') {
            confirmMsg = "Are you sure you want to assign this workout to ALL classes?";
        } else if (action === 'unassign' && classId === 'all') {
            confirmMsg = "Are you sure you want to unassign this workout from ALL classes?";
        } else {
             // For single class, we try to find the button text if possible, or generic message
             // Since we don't easily have the time string here without passing it, generic is okay,
             // or we rely on the specific action context.
             const actionText = action === 'assign' ? 'assign' : 'unassign';
             confirmMsg = `Are you sure you want to ${actionText} this workout?`;
        }

        if (confirm(confirmMsg)) {
            $.ajax({
                url: "{{ route('workout.assign_class') }}",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    workout_id: workoutId,
                    class_id: classId,
                    type: 'weightlifting',
                    action: action,
                    date: date
                },
                success: function(response) {
                     if (response.status === 'success') {
                        // Refresh to show updated button states
                        filterWeightlifting(date);
                        
                        // Also refresh classes list to show green icon if the function exists
                        if (typeof getdateName === 'function') {
                             getdateName(date);
                        }
                     } else {
                         alert(response.message || 'Action failed.');
                     }
                },
                error: function(xhr) {
                    console.error("AJAX error:", xhr.responseText);
                    alert("An error occurred while assigning the workout.");
                }
            });
        }
    }

</script>

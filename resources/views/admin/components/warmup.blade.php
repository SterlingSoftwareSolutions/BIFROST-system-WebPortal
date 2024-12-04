<div id="warmup" hidden>

    <div class="flex justify-end items-center ml-auto mr-8 gap-5 p-4 font-bold text-xl">
        {{-- hidden input field --}}
        <input type="text" name="selecttabw" id="selecttabw" hidden>
        <form id="deleteWarmupsForm">
            @csrf
            @method('DELETE')
            <input type="text" name="selectdatewd" id="selectdatewd" hidden>
            <button type="button" class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base"
                onclick="deleteWarmups()">Clear</button>
        </form>
        <script>
            function deleteWarmups() {
                // Serialize the form data
                const formData = $('#deleteWarmupsForm').serialize();
                const tab = document.getElementById('warmupTab');

                // AJAX request
                $.ajax({
                    url: '{{ route('warmups.deleteAllBySelectDate') }}',
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
                console.log("call warmap")
                // AJAX request
                $.ajax({
                    url: '/store-warmup',
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
    <div id="warmup-info"></div>
    <form id="storeWarmupForm">
        @csrf
        <input type="text" name="selectdatew" id="selectdatew" hidden>
        <div class="ui-block flex flex-col text-lg p-4 bg-gray-50 mr-8 rounded-md gap-4 mb-4 ">
            <div class="duplicateUi text-base" data-index="1">
                <div class="flex-col w-full ">
                    <div class="flex items-center border-b">
                        <label for="categoryw_1" class="w-60 block mb-1">Category <span
                                class="text-red-500">*</span></label>
                        <select id="categoryw_1" name="categoryw_1" onchange="getworkoutw(this)"
                            class="w-1/3 px-3 py-3 border flex rounded mb-2">
                            <option value="" selected disabled>-- Select Category --
                            </option>
                        </select>
                        <!-- This element will push the button to the right -->
                    </div>
                    <div class="flex items-center border-b mt-2">
                        <label for="workoutw_1" class="w-60 block mb-1">Workout <span
                                class="text-red-500">*</span></label>
                        <select id="workoutw_1" name="workoutw_1" class="w-1/3 px-3 py-3 border flex rounded mb-2">
                            <option value="" selected disabled>-- Select Workout --
                            </option>
                        </select>
                    </div>
                    <div class="flex items-center border-b mt-2">
                        <label for="repsw_1" class="w-60 block mb-1">REPS <span class="text-red-500">*</span></label>
                        <div class="relative flex items-center max-w-[8rem] mb-2">
                            <!-- Decrement Button -->
                            <button type="button" onclick="decrement(this.parentNode.querySelector('input').id)"
                                id="decrement-repsw_1"
                                class="decrement-repsw_1 bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M1 1h16" />
                                </svg>
                            </button>

                            <!-- Input Field -->
                            <input type="text" id="repsw_1" name="repsw_1" data-input-counter
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                placeholder="0" readonly />

                            <!-- Increment Button -->
                            <button type="button" onclick="increment(this.parentNode.querySelector('input').id)"
                                id="increment-repsw_1"
                                class="increment-repsw_1 bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M9 1v16M1 9h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center border-b mt-2">
                        <label for="weigthw_1" class="w-60 block mb-1">Weight <span
                                class="text-red-500">*</span></label>
                        <input type="number" id="weigthw_1" name="weigthw_1"
                            class="w-1/3 px-3 py-3 border flex rounded mb-2">
                        <label for="" class="border bg-white py-3 px-3 mb-2 ">%</label>
                    </div>

                </div>
            </div>
        </div>
        <a class="duplicateBtn bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base cursor-pointer">Another</a>
        <div class="flex mt-12">
            @if ($accessType == 'write')
                <button type="button" class="bg-[#FB1018] text-white py-2 px-4 rounded mb-2 hover:bg-red-700"
                    onclick="storeWarmup()">Save</button>
            @else
                <button type="button" class="bg-[#FB1018] text-white py-2 px-4 rounded mb-2 hover:bg-red-700"
                    onclick="storeWarmup()" disabled>Save</button>
            @endif
        </div>
    </form>
</div>
<script>
    // Function to set category options for warmup
    function setCategoryW(id, categoryName) {
        const categorySelectW = document.getElementById('categoryw_1');
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
                // Extract the category_options array from the response
                const categoryOptionsW = response.category_options || [];

                // Clear existing options in the select element before adding new ones
                const categorySelectW = document.getElementById('categoryw_1');
                categorySelectW.innerHTML =
                    '<option value="" selected disabled>-- Select Category --</option>';

                // Loop through each category_option and call the setCategoryW() function
                categoryOptionsW.forEach(option => {
                    setCategoryW(option.id, option.category_name);
                });

                // Optional: Sort the options alphabetically if needed
                sortSelectOptionsW(categorySelectW);
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
                // Assuming response is an array of arrays
                // response.forEach(subArray => {
                setwarmup(response.result, response.categoryOptions);
                // });
            },

            error: function(xhr, status, error) {
                console.error(error);
            },
        });
    }


    // set warmup
    function setwarmup(array, categoryOptionsArray) {
        console.log(array);

        // Get the container where the information will be displayed
        const warmupInfoDiv = document.getElementById('warmup-info');

        // Clear any existing content
        warmupInfoDiv.innerHTML = '';

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
                            <label for="repswe_${item.id}" class="w-60 block mb-1">REPS <span class="text-red-500">*</span></label>
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

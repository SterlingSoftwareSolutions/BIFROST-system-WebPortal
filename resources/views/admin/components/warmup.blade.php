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
                            <div class="flex justify-center text-center items-center font-bold mb-3 text-2xl">Workout
                                List</div>
                            <div class="flex items-start space-x-6">
                                <!-- Category Field -->
                                <div class="flex flex-row items-center w-1/2 space-x-2">
                                    <label for="categoryw_2" class="w-28">Category</label>
                                    <select id="categoryw_2" name="categoryw_2" onchange="getworkoutw(this)"
                                        class="flex-1 px-3 py-2 border rounded">
                                        <option value="" selected disabled>-- Select Category --</option>
                                    </select>
                                </div>

                                <!-- Exercise Field -->
                                <div class="flex flex-row items-center w-1/2 space-x-2">
                                    <label for="workoutw_2" class="w-28">Exercise</label>
                                    <select id="workoutw_2" name="workoutw_2" class="flex-1 px-3 py-2 border rounded">
                                        <option value="" selected disabled>-- Select Exercise --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-start space-x-6 mt-3">
                                <!-- Name Field -->
                                <div class="flex flex-row items-center flex-[2] space-x-2">
                                    <label for="namew_2" class="w-28">Name Search</label>
                                    <input type="text" id="namew_2" name="namew_2"
                                        class="flex-1 px-3 py-2 border rounded mb-2">
                                </div>

                                <!-- Go Button -->
                                <div class="flex flex-row items-center flex-1 space-x-2">
                                    <button id="addsetwarmup_1" onclick="filterWarmup(date)" type="button"
                                        class="bg-black text-white py-3 px-4 rounded mb-2  text-base">Go</button>
                                </div>

                                <div class="flex flex-row items-center flex-1 space-x-2">
                                    <button id="searchclearsetwarmup_1" onclick="clearSearchWarmup()" type="button"
                                        class="bg-black text-white py-3 px-4 rounded mb-2  text-base">Clear</button>
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
                                <label for="repsw_1" class="w-60 block mb-1">REPS <span class="text-red-500">*</span></label>
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
                                <label for="weigthw_1" class="w-60 block mb-1">Weight <span class="text-red-500">*</span></label>
                                <select name="unit_1" class="border bg-white py-3.5 px-2 mb-2 rounded" onchange="toggleGenderInputs(this)">
                                <option value="outof10">/10</option>
                                <option value="cal">Cal</option>
                                <option value="percent">%</option>
                                <option value="kg">Kg</option>
                                </select>
                                <div class="relative h-[60px] min-w-0">
                                <input type="number" name="weigthc_1" class="w-20 py-3 border rounded mb-2 absolute top-0 left-0" required>
                                <div class="flex space-x-2 items-center absolute top-0 left-0 invisible" data-gender-inputs>
                                    <label class="text-sm ml-1">M</label>
                                    <input type="number" name="male_1" class="w-20 px-2 py-2 border rounded mb-2">
                                    <label class="text-sm">F</label>
                                    <input type="number" name="female_1" class="w-20 px-2 py-2 border rounded mb-2">
                                </div>
                                </div>
                            </div>
                        </div>


        <!-- Add Another Button -->
        <div class="flex flex-row justify-end gap-4 mt-5" id="add-another-btn">
            <button type="button" onclick="addAnotherWorkout()" class="bg-[#000000] text-white py-2 px-4 rounded mr-8 hover:bg-gray-800 w-30">
                + Add Another
            </button>
        </div>

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
                // Assuming response is an array of arrays
                // response.forEach(subArray => {
                setwarmups(response.result, response.categoryOptions);
                // });
            },

            error: function(xhr, status, error) {
                console.error(error);
            },
        });
    }


    //set new warmup cards
    function setwarmups(warmups, categoryOptions) {
        const container = $("#setwarmups"); // Replace with your actual container class or ID
        container.empty(); // Clear previous content

        warmups.forEach((item, index) => {

            let html = `
            <div class="border border-green-900 rounded-2xl shadow p-2 bg-white mx-10">
                <div class="border border-black rounded-xl shadow p-4 bg-white">
                    <div class="pb-2 mb-2 flex justify-between items-center">
                        <div class="text-gray-700">${item.workoutname || 'N/A'} :</div>
                        <div class="space-x-2">
                            <button class="edit-warmups-btn" data-id="${item.id}" type="button">
                                <svg class="feather feather-edit" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>

                            <button class="delete-warmups-btn" data-id="${item.id}" type="button">
                                <svg fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <path d="M10,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625l12-12c0.390625-0.390625,1.0234375-0.390625,1.4140625,0s0.390625,1.0234375,0,1.4140625l-12,12C10.5117188,22.9023438,10.2558594,23,10,23z"/>
                                        <path d="M22,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688l-12-12c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625s1.0234375-0.390625,1.4140625,0l12,12c0.390625,0.390625,0.390625,1.0234375,0,1.4140625C22.5117188,22.9023438,22.2558594,23,22,23z"/>
                                    </g>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-2 text-gray-800 font-semibold">${item.workout_type} at ${item.weight || 0}% for ${item.reps} reps</div>


                    <div class="mt-4 flex justify-end">
                        <p class="mr-4 font-bold">Assign Workout to Class</p>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="" class="sr-only peer warmups-toggle" data-workout-id="${item.id}" data-workout-type="warmup" ${item.is_assigned ? 'checked' : ''}>
                             <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600 dark:peer-checked:bg-green-600"></div>
                        </label>
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
                setwarmups(warmupArray, categoryArray);
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

    //assign warmup to class
    $(document).on('change', '.warmups-toggle', function() {
        const date = document.getElementById('selectdatewd').value;
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

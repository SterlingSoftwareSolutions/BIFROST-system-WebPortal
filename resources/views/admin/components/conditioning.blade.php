<div id="conditioning" hidden>
    {{-- hidden input field --}}
    <input type="text" name="selecttabc" id="selecttabc" hidden>
    <div class="flex justify-end items-center ml-auto mr-8 gap-5 p-4 font-bold text-xl">
        <button class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base" onclick="deleteCond()"
            type="button">Clear</button>
    </div>
    <div class="ui-block flex flex-col text-lg pt-4 px-4 bg-gray-50 mr-8 rounded-md">
        <div class="flex-col w-full">

            <div class="flex items-center border-b">
                <label for="roundCond" class="w-60 block">Rounds</label>
                <div class="relative flex items-center max-w-[8rem] mb-2 w-1/2">
                    <button type="button" onclick="decrementR(this.parentNode.querySelector('input').id)"
                        class="decrement-roundCond bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 1h16" />
                        </svg>
                    </button>
                    <input type="text" id="roundCond" name="roundCond" data-input-counter
                        class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                        placeholder="0" readonly />
                    <button type="button" onclick="incrementR(this.parentNode.querySelector('input').id)"
                        class="increment-roundCond bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 1v16M1 9h16" />
                        </svg>
                    </button>
                </div>
                <div class="ml-10 flex items-center w-1/2">
                    <label for="amrapCheckboxCon" class="w-24 block">AMRAP</label>
                    <input type="checkbox" id="amrapCheckboxCon" name="amrapCheckboxCon"
                        class="h-11 w-11 px-3 py-3 border border-gray-300 rounded mb-2 checked:bg-blue-600 checked:border-transparent"
                        onchange="syncCheckboxes()">
                </div>
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
        $(document).ready(function() {
            $('#conditioning-form').on('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission
                console.log("hellloooo")
                $.ajax({
                    url: '/store-conditioning',
                    type: 'POST',
                    data: $(this).serialize(), // Serialize form data
                    success: function(response) {
                        // Handle the response
                        alert(response.message);

                        // Clear input fields
                        $('#conditioning-form')[0].reset();
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

    <form id="conditioning-form">
        @csrf
        <input type="text" name="selectdatec" id="selectdatec" hidden>

        <input type="hidden" id="rounds" name="rounds" data-input-counter
            class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none" />

        <input type="checkbox" id="amrapCheckbox" name="amrap"
            class="h-11 w-11 px-3 py-3 border border-gray-300 rounded mb-2 checked:bg-blue-600 checked:border-transparent"
            hidden>

        <div class="duplicateUiC" data-index="1">
            <div class="ui-block flex flex-col text-lg pt-2 px-4 pb-4 bg-gray-50 mr-8 rounded-md gap-4 mb-4">
                <div class="flex-col w-full">
                    <div class="flex items-center border-b">
                        <label for="categoryc_1" class="w-60 block mb-1">Category <span
                                class="text-red-500">*</span></label>
                        <select id="categoryc_1" name="categoryc_1" onchange="getworkoutC(this)"
                            class="w-1/3 px-3 py-3 border flex rounded mb-2 ">
                            <option value="" selected disabled>-- Select Category --</option>
                        </select>
                    </div>
                    <div class="flex items-center border-b">
                        <label for="workoutc_1" class="w-60 block mb-1">Workout <span
                                class="text-red-500">*</span></label>
                        <select id="workoutc_1" name="workoutc_1" class="w-1/3 px-3 py-3 border flex rounded mb-2 mt-2">
                            <option value="" selected disabled>-- Select Workout --</option>
                        </select>
                    </div>
                    <div class="flex items-center border-b mt-2">
                        <label for="repsc_1" class="w-60 block mb-1">REPS <span class="text-red-500">*</span></label>
                        <div class="relative flex items-center max-w-[8rem] mb-2">
                            <button type="button" onclick="decrement(this.parentNode.querySelector('input').id)"
                                class="decrement-repsc bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M1 1h16" />
                                </svg>
                            </button>
                            <input type="text" id="repsc_1" name="repsc_1" data-input-counter
                                class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 focus:outline-none"
                                placeholder="0" readonly />
                            <button type="button" onclick="increment(this.parentNode.querySelector('input').id)"
                                class="increment-repsc bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M9 1v16M1 9h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center border-b mt-2">
                        <label for="weigthc_1" class="w-60 block mb-1">
                            Weight <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="weigthc_1" name="weigthc_1"
                            class="w-1/3 px-3 py-3 border rounded mb-2" required>
                        <select id="unit_1" name="unit_1" class="border bg-white py-3 px-3 mb-2 rounded">
                            <option value="%">%</option>
                            <option value="kg">Kg</option>
                        </select>
                    </div>

                    <button class=" bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base "
                        onclick="addAnotherClick()" id="idfake_1">Another</button>
                    <div class="flex items-center border-b mt-2">
                        <label for="timeTC_1" class="w-60 block mb-1">Time To Complete<span
                                class="text-red-500">*</span></label>
                        <div class="relative flex items-center max-w-[8rem] mb-2">
                            <button type="button"
                                onclick="adjustRestTime(this.parentNode.querySelector('input').id, -1)"
                                class="decrement-timeTC bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M1 1h16" />
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
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M9 1v16M1 9h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="showUIC" id="showUIC"></div>
        <button id="save-button" type="submit"
            class="bg-[#FB1018] text-white py-2 px-4 rounded mb-2 hover:bg-red-700">Save</button>
    </form>

    <button class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base hidden" id="another"
        type="button">Another</button>
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
                ['categoryc_1'].forEach(id => {
                    const categorySelect = document.getElementById(id);
                    categorySelect.innerHTML =
                        '<option value="" selected disabled>-- Select Category --</option>';
                });

                // Loop through each category_option and call the setCategory() function
                categoryOptions.forEach(option => {
                    setCategory(option.id, option.category_name, 'categoryc_1');
                });

                // Optional: Sort the options alphabetically if needed
                ['categoryc_1'].forEach(id => {
                    sortSelectOptions(document.getElementById(id));
                });

                // Set the default selected category to "hinge" (case-insensitive) and trigger onchange
                ['categoryc_1'].forEach(id => {
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
                setConditioning(response.result, response.categoryOptions);
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    }

    function setConditioning(array, categoryOptionsArray) {
        console.log(categoryOptionsArray);
        // Get the container where the information will be displayed
        const conditioningDiv = document.getElementById('info-conditioning');
        const rounds = document.getElementById('roundCond');
        rounds.value = array.length > 0 && array[0].rounds ? array[0].rounds : 0;
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

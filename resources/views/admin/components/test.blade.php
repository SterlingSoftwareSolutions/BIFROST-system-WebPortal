<!-- HTML Structure -->
<div id="test" hidden>
    <input type="text" name="selecttabt" id="selecttabt" hidden>
    <div class="flex gap-5 mr-8 rounded-md mb-4 justify-end font-bold text-xl -mt-7 p-0">

        <form id="deletefortest">
            @csrf
            @method('DELETE')
            <input type="text" name="selectdatetestDelete" id="selectdatetestDelete" hidden>
            {{-- <button class="bg-black text-white py-2 px-4 rounded mt-2 text-base">Clear</button> --}}
        </form>

        <script>
            //selected test delete
            $(document).on('click', '.delete-tests-btn', function () {
                const date = document.getElementById('selectdatetestDelete').value;
                const id = $(this).data('id');
                const confirmed = confirm("Are you sure you want to delete this test record?");

                if (!confirmed) return;

                $.ajax({
                    url: "/delete-tests",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            gettest(date);
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX error:", error);
                        alert("An error occurred while deleting the test record.");
                    }
                });
            });
            //assign test to class
    $(document).on('change', '.test-toggle', function () {
        console.log('test asssssignnnnnnnnnnnnn');
        const date = document.getElementById('selectdatet').value;
        const workoutId = $(this).data('workout-id');
        const workoutType = $(this).data('workout-type');
        const assigned = $(this).is(':checked') ? 1 : 0;

        // Get and conditionally remove class_id from local storage
        let selectedClassId = localStorage.getItem("selected_class_id");
        if (assigned) {
            if (!selectedClassId) {
                alert("Please select a test class first.");
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

            $(document).ready(function () {
                const date = document.getElementById('selectdatetestDelete').value;
                $('#deletefortest').on('submit', function (event) {
                    event.preventDefault(); // Prevent the default form submission

                    $.ajax({
                        url: '{{ route('delete-test') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE',
                            selectdatetestDelete: $('#selectdatetestDelete').val()
                        },
                        success: function (response) {
                            // Handle the response
                            // alert(response.status);
                            gettest(date);
                            // Optionally, update the UI to reflect the changes
                        },
                        error: function (xhr) {
                            // Handle error
                            console.error(xhr.responseText);
                        }
                    });
                });
            });
            $(document).ready(function () {
                $('#storeformtest').on('submit', function (event) {
                    event.preventDefault(); // Prevent the default form submission

                    const testId = $('#test_id').val();

                    const url = testId ? `/update-test` : '/save-test';
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: $(this).serialize(), // Serialize form data
                        success: function (response) {

                            alert(response.message);
                            // Clear input fields
                            $('#storeformtest')[0].reset();
                            $('#test_id').val('');

                            // Clear the clone display container
                            const cloneDisplayContainerTest = document.getElementById(
                                'cloneDisplayContainerTest');
                            if (cloneDisplayContainerTest) {
                                cloneDisplayContainerTest.innerHTML = '';
                            }

                            // Trigger the tab click
                            const tab = document.getElementById('testTab');
                            if (tab) {
                                tab.click();
                            }

                            // Optionally, update the UI to reflect the changes
                            // alert(response.message); // Uncomment if you want to show a message
                        },
                        error: function (xhr) {
                            // Handle error
                            console.error(xhr.responseText);
                        }
                    });
                });
            });
        </script>


    </div>
    <div id="test-container" class="-mt-5 "></div>

    <input type="text" name="selecttabtt" id="selecttabtt" hidden>

    <div class="duplicateUiTest" data-index="1" id="uiContainerTest">
        <div class="ui-block flex flex-col text-lg p-4 mr-8 rounded-md gap-4 mb-4 ">
            <div class="flex gap-5 justify-between">
                {{-- Serach Section --}}
                <div class="flex-col w-full">
                    <div class="bg-gray-50 p-4">
                        <div class="flex justify-center text-center items-center font-bold mb-3 text-2xl">Workout
                            List</div>
                        <div class="flex items-start space-x-6">
                            <!-- Category Field -->
                            <div class="flex flex-row items-center w-1/2 space-x-2">
                                <label for="test-category_2" class="w-28">Category</label>
                                <select id="test-category_2" name="test-category_2" onchange="getworkoutT(this)"
                                    class="flex-1 px-3 py-2 border rounded">
                                    <option value="" selected disabled>-- Select Category --</option>
                                </select>
                            </div>

                            <!-- Exercise Field -->
                            <div class="flex flex-row items-center w-1/2 space-x-2">
                                <label for="test-workout_2" class="w-28">Exercise</label>
                                <select id="test-workout_2" name="test-workout_2"
                                    class="flex-1 px-3 py-2 border rounded">
                                    <option value="" selected disabled>-- Select Exercise --</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-start space-x-6 mt-3">
                            <!-- Name Field -->
                            <div class="flex flex-row items-center flex-[2] space-x-2">
                                <label for="namet_2" class="w-28">Name Search</label>
                                <input type="text" id="namet_2" name="namet_2"
                                    class="flex-1 px-3 py-2 border rounded mb-2">
                            </div>

                            <!-- Go Button -->
                            <div class="flex flex-row items-center flex-1 space-x-2">
                                <button id="addsettest_1" onclick="filterTest(date)" type="button"
                                    class="bg-black text-white py-3 px-4 rounded mb-2  text-base">Go</button>
                            </div>

                            <div class="flex flex-row items-center flex-1 space-x-2">
                                <button id="searchclearsettest_1" onclick="clearSearchTest()" type="button"
                                    class="bg-black text-white py-3 px-4 rounded mb-2  text-base">Clear</button>
                            </div>
                        </div>
                    </div>

                    <!-- Scroll Section -->
                    <div id="settests" class="mt-4 max-h-[600px] overflow-y-auto space-y-4">
                    </div>

                </div>
                {{-- form section --}}
                <div class="flex flex-col text-lg bg-gray-50 mr-8 rounded-md gap-2 w-full  p-4">
                    <form id="storeformtest">
                        @csrf
                        <input type="text" name="selectdatet" id="selectdatet" hidden>
                        <div class="">
                            <div class="flex justify-center text-center items-center font-bold mb-3 text-2xl">Create
                            </div>
                            <input type="hidden" id="test_id" name="test_id" value="">
                            <div class="flex items-center border-b mt-2">
                                <label for="namet_1" class="w-60 block mb-1">Workout Name </label>
                                <input type="text" id="namet_1" name="namet_1"
                                    class="w-1/3 px-3 py-3 border flex rounded mb-2" required>
                            </div>
                            <div class="flex items-center border-b">
                                <label for="test-category_1" class="w-60 block mb-1">Category <span
                                        class="text-red-500">*</span></label>
                                <select id="test-category_1" name="test-category_1" onchange="getworkoutT(this)"
                                    class="w-[41%] px-3 py-3 border rounded mb-2">
                                    <option value="" selected disabled>-- Select Category --</option>
                                </select>
                            </div>
                            <div class="flex items-center border-b">
                                <label for="test-workout_1" class="w-60 block mb-1">Exercise <span
                                        class="text-red-500">*</span></label>
                                <select id="test-workout_1" name="test-workout_1"
                                    class="w-[41%] px-3 py-3 border rounded mb-2">
                                    <option value="" selected disabled>-- Select Exercise --</option>
                                </select>
                            </div>
                            <div class="flex items-center border-b">
                                <label for="test-member_1" class="w-60 block mb-1">Member <span
                                        class="text-red-500">*</span></label>
                                <select id="test-member_1" name="test-member_1"
                                    class="w-[41%] px-3 py-3 border rounded mb-2">
                                    <option value="" selected disabled>-- Select Member --</option>
                                </select>
                            </div>
                            <div class="flex flex-row justify-end gap-4 mt-5">
                                <button type="button" id="cleartestbtn" onclick="clearTestForm()"
                                    class="bg-gray-200 text-gray-800 py-2 px-4 rounded hover:bg-gray-300 w-24 hidden">
                                    Cancel
                                </button>

                                <button type="submit" id="savebtntest"
                                    class="bg-[#FB1018] text-white py-2 px-4 rounded mr-8 hover:bg-red-700 w-24">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <div id="cloneDisplayContainerTest"></div>
    {{-- <button class="bg-black text-white py-2 px-4 rounded mb-2 text-base" id="clonebuttonTest"
        type="button">Another</button> --}}
    {{-- <button id="submitButton" type="submit"
        class="bg-[#FB1018] text-white py-2 px-4 rounded mb-2 hover:bg-red-700 w-24">Save</button> --}}

</div>

<script>
    // Function to set category options
    function setCategory(id, categoryName, selectId) {
        const categorySelect = document.getElementById(selectId);
        const option = document.createElement('option');
        option.value = id;
        option.text = categoryName;
        categorySelect.add(option);
    }

    // Function to get categories via AJAX
    function getCategoryT() {
        const selectTab = "test";
        $.ajax({
            url: "/getCategory",
            type: "POST",
            data: {
                tab: selectTab,
                _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
            },
            success: function (response) {
                const categoryOptions = response.category_options || [];
                // Clear existing options in the select elements before adding new ones
                ['test-category_1', 'test-category_2'].forEach(id => {
                    const categorySelect = document.getElementById(id);
                    categorySelect.innerHTML =
                        '<option value="" selected disabled>-- Select Category --</option>';
                });

                // Loop through each category_option and call the setCategory() function
                categoryOptions.forEach(option => {
                    setCategory(option.id, option.category_name, 'test-category_1');
                    setCategory(option.id, option.category_name, 'test-category_2');
                });

                // Optional: Sort the options alphabetically if needed
                ['test-category_1', 'test-category_2'].forEach(id => {
                    sortSelectOptions(document.getElementById(id));
                });
            },
            error: function (xhr, status, error) {
                console.error(error);
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

    // Function to get workouts via AJAX based on selected category
    function getworkoutT(selectElement) {
        console.log("this is select element", selectElement);
        const tab = "test";
        const selectId = selectElement.value; // Get the value of the selected element

        // Split the ID of the select element by underscores
        const idParts = selectElement.id.split('_');

        // Get the remaining part after the base
        const remainingPart = idParts.slice(1).join('_');
        const isCategorytT = selectElement.id === `test-category_${remainingPart}`;
        const isECategoryT = selectElement.id === `categorytest_${remainingPart}`;


        let workoutSelectId;

        if (isCategorytT) {
            workoutSelectId = `test-workout_${remainingPart}`;
        } else if (isECategoryT) {
            workoutSelectId = `workouttest_${remainingPart}`;
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
            success: function (response) {
                console.log(response);
                const workouts = response.workouts || [];
                clearOptions(workoutSelect);

                workouts.forEach(workout => {
                    setWorkout(workoutSelect, workout.id, workout.workout);
                });
            },
            error: function (xhr, status, error) {
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

    // Call getCategoryS on page load
    document.addEventListener('DOMContentLoaded', function () {
        getCategoryT();
    });


    // Function to get members via AJAX
    function getMemberT() {
        $.ajax({
            url: "/get-members",
            type: "POST",
            data: {
                tab: 'test',
                _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
            },
            success: function (response) {
                const select = $('#test-member_1');
                select.empty();
                select.append('<option value="" selected disabled>-- Select Member --</option>');
                if (response.members && response.members.length > 0) {
                    response.members.forEach(member => select.append(
                        `<option value="${member.id}">${member.name}</option>`));
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }

    // Call the functions on page load
    document.addEventListener('DOMContentLoaded', function () {
        getCategoryT();
        getMemberT();
    });

    // Function to update input names and IDs for the cloned element
    function updateNamesAndIdTT(element, index) {
        element.querySelectorAll("input, select").forEach(el => {
            const baseName = el.name.split('_')[0];
            const baseId = el.id.split('_')[0];
            el.name = `${baseName}_${index}`;
            el.id = `${baseId}_${index}`;
        });
    }

    // Function to add a remove button to the cloned element
    function addRemoveButtonToElementtest(element) {
        let existingRemoveButton = element.querySelector(".removeBtnTest");
        if (!existingRemoveButton) {
            let removeButton = document.createElement("button");
            removeButton.innerText = "Remove";
            removeButton.classList.add('removeBtnTest', 'bg-[#FB1018]', 'text-white', 'py-2', 'px-4', 'rounded', 'mb-2',
                'w-24', 'flex-shrink-0');
            removeButton.addEventListener("click", () => element.remove());
            element.appendChild(removeButton);
        }
    }

    // Function to handle the duplication of UI elements
    function handleUiDuplications(event) {
        const cloneButton = event.target;
        const originalUiElement = cloneButton.closest('.duplicateUiTest');
        let index = parseInt(originalUiElement.dataset.index) || 1;
        index += 1;
        originalUiElement.dataset.index = index;

        const clonedElement = originalUiElement.cloneNode(true);
        clonedElement.querySelectorAll("input").forEach(input => input.value = '');
        updateNamesAndIdTT(clonedElement, index);
        clonedElement.querySelectorAll(".duplicateBtnTest, .removeBtnTest").forEach(button => button.remove());
        addRemoveButtonToElementtest(clonedElement);

        document.getElementById('cloneDisplayContainerTest').appendChild(clonedElement);
    }

    // Event listener for the main clone button
    document.getElementById('clonebuttonTest').addEventListener('click', function () {
        const uiContainerTest = document.getElementById('uiContainerTest');
        const newContainer = uiContainerTest.cloneNode(true);
        let index = parseInt(uiContainerTest.dataset.index) || 1;
        index += 1;
        uiContainerTest.dataset.index = index;
        newContainer.querySelectorAll("input").forEach(input => input.value = '');
        updateNamesAndIdTT(newContainer, index);
        addRemoveButtonToElementtest(newContainer);
        document.getElementById('cloneDisplayContainerTest').appendChild(newContainer);
    });

    // Add event listeners to all duplicate buttons
    document.querySelectorAll(".duplicateBtnTest").forEach(button => button.addEventListener("click",
        handleUiDuplications));


    // search test
    function filterTest(date) {
        const dateOnClick = document.getElementById('selectdatet').value;
        console.log('first', dateOnClick)

        //console.log('bbbbbbbb',buttonId)
        let categoryId = document.getElementById("test-category_2").value;
        let exerciseId = document.getElementById("test-workout_2").value;
        let nameSearch = document.getElementById("namet_2").value;

        $.ajax({
            url: "/search-test",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                date: dateOnClick,
                name: nameSearch,
                category_id: categoryId,
                workout_id: exerciseId
            },
            success: function (response) {
                console.log("this is filteerd test response", response);
                allTestData = response.test;
                const testArray = Object.values(response.test);
                const categoryArray = Object.values(response.categoryOptions);
                // settests(testArray, categoryArray);
                setsTests(testArray, categoryArray, response.Members);
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

    function clearSearchTest() {
        const date = document.getElementById('selectdatet').value;

        document.getElementById('test-category_2').value = '';
        document.getElementById('test-workout_2').value = '';
        document.getElementById('namet_2').value = '';
        console.log('dateeeee', date);
        gettest(date);
    }

    var allTestData = [];
    function gettest(date) {
        console.log("Fetching test data for date: " + date);
        $.ajax({
            url: "/get-testdata",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                date: date
            },
            success: function (response) {
                console.log("Test response:", response);
                if (response.Test && response.categoryOptions && response.Members) {
                    // Process the test data
                    allTestData = response.Test;
                    setsTests(response.Test, response.categoryOptions, response.Members);
                } else {
                    console.error("Unexpected response format:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error during AJAX request:", {
                    status: status,
                    xhr: xhr,
                    error: error
                });
            }
        });
    }

    //set new test cards
    function setsTests(tests, categoryOptions) {
        const container = $("#settests"); // Replace with your actual container class or ID
        container.empty(); // Clear previous content

        tests.forEach((item, index) => {

            let html = `
            <div class="border border-green-900 rounded-2xl shadow p-2 bg-white mx-10">
                <div class="border border-black rounded-xl shadow p-4 bg-white">
                    <div class="pb-2 mb-2 flex justify-between items-center">
                        <div class="text-gray-700">${item.workoutname || 'N/A'} :</div>
                        <div class="space-x-2">
                            <button class="edit-tests-btn" data-id="${item.id}" type="button">
                                <svg class="feather feather-edit" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>

                            <button class="delete-tests-btn" data-id="${item.id}" type="button">
                                <svg fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <path d="M10,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625l12-12c0.390625-0.390625,1.0234375-0.390625,1.4140625,0s0.390625,1.0234375,0,1.4140625l-12,12C10.5117188,22.9023438,10.2558594,23,10,23z"/>
                                        <path d="M22,23c-0.2558594,0-0.5117188-0.0976563-0.7070313-0.2929688l-12-12c-0.390625-0.390625-0.390625-1.0234375,0-1.4140625s1.0234375-0.390625,1.4140625,0l12,12c0.390625,0.390625,0.390625,1.0234375,0,1.4140625C22.5117188,22.9023438,22.2558594,23,22,23z"/>
                                    </g>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-2 text-gray-800 font-semibold">${item.workout_type} 1 REP MAX for ${item.member_name}</div>


                    <div class="mt-4 flex justify-end">
                        <p class="mr-4 font-bold">Assign Workout to Class</p>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="" class="sr-only peer test-toggle" data-workout-id="${item.id}" data-workout-type="test" ${item.is_assigned ? 'checked' : ''}>
                             <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600 dark:peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </div>
            </div>
            `;

            container.append(html);

            // Bind the Edit button after appending
            container.find('.edit-tests-btn').off('click').on('click', function () {
                const testId = $(this).data('id');
                populateTestsForm(testId);
            });

        });

    }

    // get editing details to form
    function populateTestsForm(testId) {

        const data = allTestData.find(item => item.id == testId);
        console.log('dataaaaaa', data);

        if (!data) {
            console.warn("No warmup data found for ID:", testId);
            return;
        }

        const card = document.querySelector(`.edit-tests-btn[data-id="${testId}"]`).closest(
            '.border.border-green-900');
        if (card) {
            card.classList.remove('border-green-900');
            card.classList.remove('border');
            card.classList.add('border-red-600');
            card.classList.add('border-2');
        }
        // Set category and trigger onchange to load workouts
        const categorySelect = document.getElementById('test-category_1');
        categorySelect.value = data.category_id;
        categorySelect.dispatchEvent(new Event('change')); // To trigger getworkoutS

        // Delay setting workouts to allow async options to load
        setTimeout(() => {
            const workoutSelect = document.getElementById('test-workout_1');
            workoutSelect.value = data.workout_id;

            const memberSelect = document.getElementById('test-member_1');
            memberSelect.value = data.member_id;
        }, 500); // Adjust based on how long getworkoutS takes to populate

        document.getElementById('namet_1').value = data.workoutname;
        document.getElementById('test_id').value = testId;


        document.getElementById('savebtntest').textContent = "Save Edit";
        document.getElementById('cleartestbtn').classList.remove('hidden');

    }


    function updatest(id) {

        // Construct the form ID dynamically
        const formId = `#updatetest_${id}`;
        const tab = document.getElementById('testTab');

        // Serialize form data
        const formData = $(formId).serialize();

        // AJAX request
        $.ajax({
            url: '/update-test', // Ensure this matches your route definition
            type: 'POST',
            data: formData,
            success: function (response) {
                // Handle the response
                if (tab) {
                    tab.click(); // Trigger tab click if needed
                }
                alert(response.message);
                $(formId)[0].reset(); // Reset the form
            },
            error: function (xhr) {
                // Handle error
                console.error(xhr.responseText);
            }
        });
    }

    function setsTest(Test, categoryOptions, Members) {
        console.log("this is strenght",);
        // Get the container where the information will be displayed
        const container = document.getElementById('test-container');


        // Clear the container
        container.innerHTML = '';

        // Initialize the htmlContent variable
        let htmlContent = '';

        Test.forEach(item => {
            // Create a string with the primary category options
            let testcategoryOptionsHTML =
                '<option value="" selected disabled>-- Select Category --</option>';
            categoryOptions.forEach(category => {
                testcategoryOptionsHTML +=
                    `<option value="${category.id}" ${category.id == item.category_id ? 'selected' : ''}>${category.category_name}</option>`;
            });


            // Create a string with the member options
            let memberOptionsHTML =
                '<option value="" selected disabled>-- Select Member --</option>';
            Members.forEach(member => {
                memberOptionsHTML +=
                    `<option value="${member.id}" ${member.id == item.member_id ? 'selected' : ''}>${member.firstname}</option>`;
            });



            // Build the final HTML for the current item
            htmlContent += `
                <form id="updatetest_${item.id}">
                    @csrf

                    <input name="id_${item.id}" value="${item.id}" hidden>
                    <div class="flex flex-col text-lg p-4 bg-gray-50 mr-8 rounded-md gap-4 mb-4">
                        <div class="flex gap-5 justify-between">
                            {{-- Primary Category and Workouts --}}
                            <div class="flex-col w-full border-r border-r-black">
                                <div class="item-container">
                                  <div hidden><h3>Item ID: ${item.id}</h3></div>
                                      <div class="flex border-b mt-4">
                                          <label for="categorytest_${item.id}" class="w-60 block mb-1">Category <span class="text-red-500">*</span></label>
                                            <select id="categorytest_${item.id}" name="categorytest_${item.id}" onchange="getworkoutT(this)" class="w-1/3 px-3 py-3 border rounded mb-2" required>
                                               ${testcategoryOptionsHTML}
                                            </select>

                                            <div class="flex justify-end items-center ml-auto mr-8"><button type="button" class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base" onclick="updatest(${item.id})">Edit</button></div>

                                     </div>


                                    <div class="flex items-center border-b">
                                        <label for="workouttest_${item.id}" class="w-60 block mb-1">
                                            Workout<span class="text-red-500">*</span>
                                        </label>
                                        <select id="workouttest_${item.id}" name="workouttest_${item.id}" class="w-1/3 px-3 py-3 border mt-2 flex rounded mb-2">
                                            <option value="" selected disabled>-- Select Workout --</option>
                                            <!-- Populate options dynamically -->
                                            <option value="${item.workout_id}" selected>${item.workout_name}</option>
                                        </select>
                                    </div>

                                 <div class="flex items-center border-b">
                                     <label for="test-member_${item.id}" class="w-60 block mb-1">Member <span class="text-red-500">*</span></label>
                                       <select id="test-member_${item.id}" name="test-member_${item.id}" class="w-1/3 px-3 py-3 border flex rounded mb-2">
                                         ${memberOptionsHTML}
                                        </select>
                                </div>
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

    //assign test to class
    $(document).on('change', '.test-toggle', function () {
        console.log('test asssssignnnnnnnnnnnnn');
        const date = document.getElementById('selectdatet').value;
        const workoutId = $(this).data('workout-id');
        const workoutType = $(this).data('workout-type');
        const assigned = $(this).is(':checked') ? 1 : 0;

        // Get and conditionally remove class_id from local storage
        let selectedClassId = localStorage.getItem("selected_class_id");
        if (assigned) {
            if (!selectedClassId) {
                alert("Please select a test class first.");
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
</script>

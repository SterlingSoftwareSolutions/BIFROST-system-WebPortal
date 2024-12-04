<!-- HTML Structure -->
<div id="test" hidden>
    <input type="text" name="selecttabt" id="selecttabt" hidden>
    <div class="flex gap-5 p-4 mr-8 rounded-md mb-4 justify-end font-bold text-xl">

        <form id="deletefortest">
            @csrf
            @method('DELETE')
            <input type="text" name="selectdatetestDelete" id="selectdatetestDelete" hidden>
            <button class="bg-black text-white py-2 px-4 rounded mb-2 mt-2 text-base">Clear</button>
        </form>

        <script>
            $(document).ready(function() {
                const date = document.getElementById('selectdatetestDelete').value;
                $('#deletefortest').on('submit', function(event) {
                    event.preventDefault(); // Prevent the default form submission

                    $.ajax({
                        url: '{{ route('delete-test') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE',
                            selectdatetestDelete: $('#selectdatetestDelete').val()
                        },
                        success: function(response) {
                            // Handle the response
                            // alert(response.status);
                            gettest(date);
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
                $('#storeformtest').on('submit', function(event) {
                    event.preventDefault(); // Prevent the default form submission

                    $.ajax({
                        url: '/save-test',
                        type: 'POST',
                        data: $(this).serialize(), // Serialize form data
                        success: function(response) {
                            // Clear input fields
                            $('#storeformtest')[0].reset();

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
                        error: function(xhr) {
                            // Handle error
                            console.error(xhr.responseText);
                        }
                    });
                });
            });
        </script>


    </div>
    <div id="test-container"></div>
    <form id="storeformtest">
        @csrf
        <input type="text" name="selectdatet" id="selectdatet" hidden>
        <input type="text" name="selecttabtt" id="selecttabtt" hidden>

        <div class="duplicateUiTest" data-index="1" id="uiContainerTest">
            <div class="ui-block flex flex-col text-lg p-4 bg-gray-50 mr-8 rounded-md gap-4 mb-4 ">

                <div class="flex flex-col text-lg bg-gray-50 mr-8 rounded-md gap-2 ">
                    <div class="flex items-center border-b">
                        <label for="test-category_1" class="w-60 block mb-1">Category <span
                                class="text-red-500">*</span></label>
                        <select id="test-category_1" name="test-category_1" onchange="getworkoutT(this)"
                            class="w-[41%] px-3 py-3 border rounded mb-2">
                            <option value="" selected disabled>-- Select Category --</option>
                        </select>
                    </div>
                    <div class="flex items-center border-b">
                        <label for="test-workout_1" class="w-60 block mb-1">Workout <span
                                class="text-red-500">*</span></label>
                        <select id="test-workout_1" name="test-workout_1" class="w-[41%] px-3 py-3 border rounded mb-2">
                            <option value="" selected disabled>-- Select Workout --</option>
                        </select>
                    </div>
                    <div class="flex items-center border-b">
                        <label for="test-member_1" class="w-60 block mb-1">Member <span
                                class="text-red-500">*</span></label>
                        <select id="test-member_1" name="test-member_1" class="w-[41%] px-3 py-3 border rounded mb-2">
                            <option value="" selected disabled>-- Select Member --</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div id="cloneDisplayContainerTest"></div>
        <button class="bg-black text-white py-2 px-4 rounded mb-2 text-base" id="clonebuttonTest"
            type="button">Another</button>
        <button id="submitButton" type="submit"
            class="bg-[#FB1018] text-white py-2 px-4 rounded mb-2 hover:bg-red-700 w-24">Save</button>
    </form>
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
            success: function(response) {
                const categoryOptions = response.category_options || [];
                // Clear existing options in the select elements before adding new ones
                ['test-category_1'].forEach(id => {
                    const categorySelect = document.getElementById(id);
                    categorySelect.innerHTML =
                        '<option value="" selected disabled>-- Select Category --</option>';
                });

                // Loop through each category_option and call the setCategory() function
                categoryOptions.forEach(option => {
                    setCategory(option.id, option.category_name, 'test-category_1');

                });

                // Optional: Sort the options alphabetically if needed
                ['test-category_1'].forEach(id => {
                    sortSelectOptions(document.getElementById(id));
                });
            },
            error: function(xhr, status, error) {
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

    // Call getCategoryS on page load
    document.addEventListener('DOMContentLoaded', function() {
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
            success: function(response) {
                const select = $('#test-member_1');
                select.empty();
                select.append('<option value="" selected disabled>-- Select Member --</option>');
                if (response.members && response.members.length > 0) {
                    response.members.forEach(member => select.append(
                        `<option value="${member.id}">${member.name}</option>`));
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    // Call the functions on page load
    document.addEventListener('DOMContentLoaded', function() {
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
    document.getElementById('clonebuttonTest').addEventListener('click', function() {
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


    function gettest(date) {
        console.log("Fetching test data for date: " + date);
        $.ajax({
            url: "/get-testdata",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                date: date
            },
            success: function(response) {
                console.log("Test response:", response);
                if (response.Test && response.categoryOptions && response.Members) {
                    // Process the test data
                    setsTest(response.Test, response.categoryOptions, response.Members);
                } else {
                    console.error("Unexpected response format:", response);
                }
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
            success: function(response) {
                // Handle the response
                if (tab) {
                    tab.click(); // Trigger tab click if needed
                }
                alert(response.message);
                $(formId)[0].reset(); // Reset the form
            },
            error: function(xhr) {
                // Handle error
                console.error(xhr.responseText);
            }
        });
    }

    function setsTest(Test, categoryOptions, Members) {
        console.log("this is strenght", );
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
</script>

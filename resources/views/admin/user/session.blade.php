<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Include jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <title>Workout Manager</title>
    <style>
        .w-1\/3 {
            width: 50% !important;
        }
        /* Custom Thin Scrollbar */
        .thin-scrollbar::-webkit-scrollbar {
            height: 6px; /* Horizontal scrollbar height */
            width: 6px;  /* Vertical scrollbar width */
        }
        .thin-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1; 
            border-radius: 4px;
        }
        .thin-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1; /* gray-300 */
            border-radius: 4px;
        }
        .thin-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; /* gray-400 */
        }
    </style>
</head>

<body>
    @extends('layout.layout')
    @section('content')
        <!-- Main content (Dashboard) -->
        <div class="container transition-width mt-24 flex-grow mx-4" id="container">
            <div class="breadcrumb text-sm mb-4">
                <div><a href="#" class="text-gray-500 no-underline hover:underline">Home</a> / <span><strong> Workout Manager
                        </strong></span></div>
                <div class="flex items-start w-full relative">
                    <div class="wo text-3xl mt-3">Workout Manager</div>
                    <div class="dt flex flex-col items-center gap-2 mb-6 absolute left-1/2 transform -translate-x-1/2">
                        <div class="flex justify-center text-2xl font-semibold items-center gap-4">
                            <p class="cursor-pointer" id="prevWeek">&larr;</p>
                            <h2 id="weekDisplay"></h2>
                            <p class="cursor-pointer" id="nextWeek">&rarr;</p>
                        </div>
                        <div class="text-lg">
                            <h2 id="weekRangeDisplay"></h2>
                        </div>
                    </div>
                </div>

            </div>
            <div class="border rounded-b-lg bg-white shadow-md mt-6 text-sm">
                <div>
                    {{-- Tab Names --}}
                    <div class="rounded w-12/12 mx-auto mt-3 mb-2 px-5">
                        <!-- Tabs -->
                        {{-- <ul id="tabs" class="flex justify-center  px-1 w-full gap-5">

                            <li class="tab-item text-black">
                                <a href="warmup" id="warmupTab" class="inline-flex items-center p-4 rounded text-black bg-gray-200 border-2 border-transparent hover:bg-gray-300">
                                    <img src="{{ asset('icon/warmup.png') }}" alt="Warmup Icon" class="w-7 h-7 mr-2" />
                                    <span>Warmup</span>
                                </a>
                            </li>

                            <li class="tab-item text-black" id="default-tab">
                                <a href="strength" id="strenghtTab" class="inline-flex items-center p-4 rounded text-black bg-white border-2 border-yellow-400 px-6">
                                    <img src="{{ asset('icon/strength.png') }}" alt="Strength Icon" class="w-6 h-6 mr-2" />
                                    <span>Strength</span>
                                </a>
                            </li>
                            <li class="tab-item text-black">
                                <a href="weightlifting" id="weightliftingTab" class="inline-flex items-center p-4 rounded text-black bg-gray-200 border-2 border-transparent hover:bg-gray-300">
                                    <img src="{{ asset('icon/weightliftingnew.png') }}" alt="Weightlifting Icon" class="w-6 h-6 mr-2" />
                                    <span>Weightlifting</span>
                                </a>
                            </li>
                            <li class="tab-item text-black">
                                <a href="conditioning" id="conditioningTab" class="inline-flex items-center p-4 rounded text-black bg-gray-200 border-2 border-transparent hover:bg-gray-300">
                                    <img src="{{ asset('icon/conditioning.png') }}" alt="Conditioning Icon" class="w-6 h-6 mr-2" />
                                    <span>Conditioning</span>
                                </a>
                            </li>
                        </ul> --}}
                    </div>
                    {{-- Separator Line with Gap --}}
                    <!-- <div class="w-full border-b-[3px] border-gray-500 mb-3"></div> -->

                    {{-- Top Week and year display --}}
                    {{-- <div class="flex flex-col items-center  gap-2 mb-6">
                        <div class="flex justify-center text-2xl font-semibold items-center gap-4">
                            <p class="cursor-pointer" id="prevWeek">&larr;</p>
                            <h2 id="weekDisplay"></h2>
                            <p class="cursor-pointer" id="nextWeek">&rarr;</p>
                        </div>
                        <div class="text-lg">
                            <h2 id="weekRangeDisplay"></h2>
                        </div>
                    </div> --}}
                </div>
                <div class="w-full flex mx-4">
                    {{-- side week calender --}}
                    <div class="w-[30%]">

                        <div class="flex flex-col text-lg weekday pr-5">
                            <h1 class="text-2xl font-bold mb-4 text-center">Day Planner</h1>
                            <button
                                class="border-l border-t border-b border-r border-black w-full px-2 py-3 rounded-t-md text-start hover:bg-black hover:text-white day"
                                id="1day">Day 1</button>
                            <button
                                class="weekday border-l border-t border-b border-r border-black w-full px-2 py-3 text-start hover:bg-black hover:text-white day"
                                id="2day">Day 2</button>
                            <button
                                class="weekday border-l border-t border-b border-r border-black w-full px-2 py-3 text-start hover:bg-black hover:text-white day"
                                id="3day">Day 3</button>
                            <button
                                class="weekday border-l border-t border-b border-r border-black w-full px-2 py-3 text-start hover:bg-black hover-text-white day"
                                id="4day">Day 4</button>
                            <button
                                class="weekday border-l border-t border-b border-r border-black w-full px-2 py-3 text-start hover:bg-black hover-text-white day"
                                id="5day">Day 5</button>
                            <button
                                class="weekday border-l border-t border-b border-r border-black w-full px-2 py-3 text-start hover:bg-black hover-text-white day"
                                id="6day">Day 6</button>
                            <button
                                class="weekday border-l border-t border-b border-r border-black w-full px-2 py-3 rounded-b-md text-start hover:bg-black hover-text-white day"
                                id="7day">Day 7</button>
                        </div>
                        {{-- Classess --}}
                        <div class="mt-5 mb-5">
                            @include('admin.components.classes', ['classes' => $classes])
                        </div>
                    </div>
                    <div class="w-[70%] tabs">
                        <div class="flex flex-row gap-4 w-full items-start">
                            {{-- Unified Search List (Visible always) --}}
                            <div class="w-[55%]">
                                @include('admin.components.search_workout_list')
                            </div>
                            
                            {{-- Common Create Form --}}
                            <div class="w-[42%]">
                                @include('admin.components.create_workout_form')
                            </div>
                        </div>

                        {{-- Hidden Components for Create/Edit Modal Logic if needed or just kept for reference 
                        <div hidden>
                            @include('admin.components.warmup')
                            @include('admin.components.strength')
                            @include('admin.components.conditioning')
                            @include('admin.components.weightlifting')
                            @include('admin.components.test')
                        </div>
                        --}}
                    </div>

                    <script>
                        // Function to update input names and ids
                        function updateInputNamesAndIds(element, index) {
                            element.querySelectorAll("input").forEach(function(input) {
                                var baseName = input.name.split('_')[0];
                                var baseId = input.id.split('_')[0];
                                input.name = baseName + '_' + index;
                                input.id = baseId + '_' + index;
                            });
                            element.querySelectorAll("select").forEach(function(input) {
                                var baseName = input.name.split('_')[0];
                                var baseId = input.id.split('_')[0];
                                input.name = baseName + '_' + index;
                                input.id = baseId + '_' + index;
                            });
                            element.querySelectorAll("button").forEach(function(input) {
                                var baseName = input.name.split('_')[0];
                                var baseId = input.id.split('_')[0];
                                input.name = baseName + '_' + index;
                                input.id = baseId + '_' + index;
                            });
                        }

                        // Function to add a Remove button
                        function addRemoveButton(element) {
                            var removeButton = document.createElement("button");
                            removeButton.innerText = "Remove";
                            removeButton.classList.add('removeBtn', 'bg-[#FB1018]', 'text-white', 'py-2', 'px-4', 'rounded', 'mb-2',
                                'remove-btn', 'w-24',
                                'flex-shrink-0');
                            removeButton.addEventListener("click", function() {
                                element.remove();
                            });
                            element.appendChild(removeButton);
                        }

                        // Function to duplicate the UI
                        function duplicateUI(event) {
                            var duplicateButton = event.target;
                            var duplicateUi = duplicateButton.previousElementSibling;

                            // Check if dataset.index is null or undefined, and set it to 1 if true
                            var index = parseInt(duplicateUi.dataset.index) || 1;

                            // Increment the index
                            index += 1;

                            // Update the dataset.index
                            duplicateUi.dataset.index = index;

                            var clone = duplicateUi.cloneNode(true);

                            // Reset the values of the inputs in the cloned element
                            clone.querySelectorAll("input").forEach(function(input) {
                                input.value = '';
                            });

                            // Update names and ids in the cloned inputs
                            updateInputNamesAndIds(clone, index);

                            // Remove old buttons and add new Remove button
                            clone.querySelectorAll(".duplicateBtn, .removeBtn").forEach(function(button) {
                                button.remove();
                            });
                            addRemoveButton(clone);

                            // Insert the cloned element before the duplicate button
                            duplicateUi.parentElement.insertBefore(clone, duplicateButton);
                        }

                        // Add event listeners to all duplicate buttons
                        document.querySelectorAll(".duplicateBtn").forEach(function(button) {
                            button.addEventListener("click", duplicateUI);
                        });
                    </script>

                </div>
            </div>

        </div>
    @endsection

    {{-- Date Script --}}
    <script>
        // Initialize variables
        let currentDate = new Date();
        let dayOfWeek = currentDate.getDay(); // Get the current day of the week (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
        // let selectedDate = "1day"; // Set default date to "1day"
        let selectedDate;
        let selectedTab = null;
        // Map the dayOfWeek to the corresponding selectedDate value
        switch (dayOfWeek) {
            case 0:
                selectedDate = "7day"; // Sunday
                break;
            case 1:
                selectedDate = "1day"; // Monday
                break;
            case 2:
                selectedDate = "2day"; // Tuesday
                break;
            case 3:
                selectedDate = "3day"; // Wednesday
                break;
            case 4:
                selectedDate = "4day"; // Thursday
                break;
            case 5:
                selectedDate = "5day"; // Friday
                break;
            case 6:
                selectedDate = "6day"; // Saturday
                break;
            default:
                selectedDate = "1day"; // Default to Monday if something goes wrong
        }

        // When the document content is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Update the week and dates display
            updateWeekAndDates();

            // Event listener for previous week button
            document.getElementById('prevWeek').addEventListener('click', function(event) {
                event.preventDefault();
                changeWeek(-1); // Change to previous week
            });

            // Event listener for next week button
            document.getElementById('nextWeek').addEventListener('click', function(event) {
                event.preventDefault();
                changeWeek(1); // Change to next week
            });

            // Event listeners for tab toggling
            let tabsContainer = document.querySelector("#tabs");
            if (tabsContainer) {
                let tabTogglers = tabsContainer.querySelectorAll("a");
                tabTogglers.forEach(function(toggler, index) {
                    toggler.addEventListener("click", function(e) {
                        e.preventDefault();
                        let tabName = this.getAttribute("href");
                        selectedTab = tabName;
                        changeui(selectedTab, selectedDate);
                        //getdate(selectedDate);
                        date = document.getElementById(selectedDate);

                    });

                    // Activate default tab
                    if (index === 0) {
                        toggler.click();
                    }
                });
            }

            // Event listeners for date selection
            let dateLinks = document.querySelectorAll(".day");
            dateLinks.forEach(function(link) {
                link.addEventListener("click", function(e) {
                    e.preventDefault();
                    let dateId = this.getAttribute("id");
                    selectedDate = dateId;
                    changeui(selectedTab, selectedDate);
                    //getdate(selectedDate);
                });
            });

            // Default select Current day
            document.getElementById(selectedDate).click();
        });

        // Function to change the week
        function changeWeek(weekChange) {
            currentDate.setDate(currentDate.getDate() + weekChange * 7);
            updateWeekAndDates();
            logSelection(selectedTab, selectedDate);
            //getdate(selectedDate);
        }

        // Function to update the week and dates display
        function updateWeekAndDates() {
            const currentYear = currentDate.getFullYear();
            const weekNumber = getWeekNumber(currentDate);

            // Set week number and year
            document.getElementById('weekDisplay').innerText = `Week ${weekNumber} - ${currentYear}`;

            // Get the start date of the current week (Monday)
            const firstDayOfWeek = getStartOfWeek(currentDate);

            // Calculate the end date of the week (Sunday)
            const lastDayOfWeek = new Date(firstDayOfWeek);
            lastDayOfWeek.setDate(firstDayOfWeek.getDate() + 6);

            // Format week range as DD/MM/YY - DD/MM/YY
            const formatRangeDate = (date) => {
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
                const year = String(date.getFullYear()).slice(-2); // Get last 2 digits
                return `${day}/${month}/${year}`;
            };

            const weekRange = `${formatRangeDate(firstDayOfWeek)} - ${formatRangeDate(lastDayOfWeek)}`;
            document.getElementById('weekRangeDisplay').innerText = weekRange;

            // Populate dates for the current week
            for (let i = 0; i < 7; i++) {
                const dayDate = new Date(firstDayOfWeek);
                dayDate.setDate(firstDayOfWeek.getDate() + i);

                const dayElement = document.getElementById(`${i + 1}day`);
                dayElement.innerText = `${formatDate(dayDate)} ${getDayName(dayDate)}`;
                dayElement.value = `${formatDate(dayDate)} ${getDayName(dayDate)}`;
            }
        }

        // Function to get the week number
        function getWeekNumber(date) {
            const startOfYear = new Date(date.getFullYear(), 0, 1);
            const pastDaysOfYear = (date - startOfYear) / 86400000 + startOfYear.getDay() + 1;
            return Math.ceil(pastDaysOfYear / 7);
        }

        // Function to get the start of the week
        function getStartOfWeek(date) {
            const day = date.getDay();
            const diff = date.getDate() - day + (day === 0 ? -6 : 1); // adjust when day is Sunday
            return new Date(date.setDate(diff));
        }

        // Function to format the date
        function formatDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = String(date.getFullYear()).slice(-2);
            return `${day}/${month}/${year}`;
        }

        // Function to get the day name
        function getDayName(date) {
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            return days[date.getDay()];
        }

        // Function to change the UI based on selected tab and date
        function changeui(tabName, selectedDate) {
            // set hiden fields to value
            // setHiddenData(tabName,selectedDate);
            let tabTogglers = document.querySelectorAll("#tabs a");
            
            if (tabTogglers.length > 0) {
                 // Remove active styles from all tabs
                // Remove active styles from all tabs
                tabTogglers.forEach(function(toggler) {
                    // Remove active classes
                    toggler.classList.remove("bg-white", "border-yellow-400");
                    // Add inactive classes
                    toggler.classList.add("bg-gray-200", "border-transparent", "hover:bg-gray-300");
                });

                // Add active styles to the clicked tab
                if (tabName) {
                    let toggler = document.querySelector(`#tabs a[href="${tabName}"]`);
                    if (toggler) {
                         toggler.classList.remove("bg-gray-200", "border-transparent", "hover:bg-gray-300");
                         toggler.classList.add("bg-white", "border-yellow-400");
                    }
                }
            }
           

            // Remove active styles from all date links
            let dateLinks = document.querySelectorAll(".day");
            dateLinks.forEach(function(link) {
                link.classList.add("hover:bg-black", "hover:text-white");
                link.classList.remove("bg-[#EEE8AA]");
            });

            // Add active styles to the selected date link -updated
            let selectedDateElement = document.getElementById(selectedDate);
            selectedDateElement.classList.remove("hover:bg-black", "hover:text-white");
            selectedDateElement.classList.add("bg-[#EEE8AA]");
            // Log selected tab and date
            logSelection(tabName, selectedDate);
            if (tabName) {
                changeTab(tabName, selectedDate);
            }
            //getdate(selectedDate);
        }

        function changeTab(tabName, selectedDate) {

            // Get all tabs
            var tabs = document.querySelectorAll(".tabs > div");

            // Hide all tabs
            tabs.forEach(function(tab) {
                tab.setAttribute("hidden", true);
            });

            // Show the selected tab
            var tab = document.getElementById(tabName);
            if (tab) {
                 tab.removeAttribute("hidden");
            }
        }


        function logSelection(tabId, dayId) {
            console.log("selected tab is: " + tabId);
            let dayName = document.getElementById(dayId).innerText;

            switch (tabId) {
                case "warmup":
                    document.getElementById('selectdatew').value = dayName;
                    document.getElementById('selectdatewd').value = dayName;
                    document.getElementById('selecttabw').value = tabId;
                    getwarmup(dayName);
                    break;
                case "strength":
                    document.getElementById('selectdates').value = dayName;
                    document.getElementById('selecttabs').value = tabId;
                    document.getElementById('selectdatestrenghtDelete').value = dayName;
                    getstrength(dayName);
                    break;
                case "conditioning":
                    document.getElementById('selectdatec').value = dayName;
                    document.getElementById('selecttabc').value = tabId;
                    getconditioning(dayName);
                    break;
                case "weightlifting":
                    document.getElementById('selectdatewe').value = dayName;
                    document.getElementById('selecttabwe').value = tabId;
                    document.getElementById('selectdateweDelete').value = dayName;
                    getWeightlifting(dayName);
                    break;
                case "test":
                    document.getElementById('selectdatet').value = dayName;
                    document.getElementById('selecttabtt').value = tabId;
                    document.getElementById('selectdatetestDelete').value = dayName;
                    gettest(dayName)
                    break;
                default:
                    console.log("Unknown tabId: " + tabId);
                    break;
            }
            getdateName(dayName);
            //getdate(selectedDate);
            // Call AJAX function here if needed
            // getdata(tabName, dayName);
        }


        function setCategory(id, categoryName) {
            const categorySelectW = document.getElementById('categoryw');
            const option = document.createElement('option');
            option.value = id;
            option.text = categoryName;
            categorySelectW.add(option);
        }
    </script>
<!-- {{-- <li
                                class="tab-item text-black rounded-t hover:border-t hover:border-l hover:border-r hover:border-black w-100">
                                <a href="test" id="testTab" class="inline-flex items-center p-4 bg-gray-100 rounded-t-lg active dark:bg-gray-800 dark:text-blue-500 px-10">
                                    <img src="{{ asset('icon/test.png') }}" alt="Test Icon" class="w-5 h-5 mr-2" />
                                    <span>Test</span>
                                </a>
                            </li> --}} -->
</body>

</html>
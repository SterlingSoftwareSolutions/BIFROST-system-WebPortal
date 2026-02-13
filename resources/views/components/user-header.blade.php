<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Header</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha384-uXf6sr8AaXNpfLE4u1jL1EB5Yc5zH06smif1RtZWm1sC2C2Mi41WIebh5q1Q0ynP" crossorigin="anonymous">
</head>

<body>
    <header class='fixed z-20 flex items-center w-full bg-white header md:mb-4 lg:h-20 md:h-20'>
        <div class="container bg-black w-full">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-between p-5 gap-x-3 sm:gap-x-8">
                    <!-- Logo -->
                    <div class='md:mt-2'>
                        <img src="{{ asset('images/valhalla-logo.png') }}" alt="Logo" class='w-16 md:w-20' />
                    </div>
                    <div class="text-white font-bold">
                        <!-- Toggle button -->
                        <button onclick="toggleSidebar()" class="cursor-pointer border-2 border-black ">
                            <i id="toggle-icon"
                                class="fa fa-bars text-2xl h-6 w-6 transform transition-transform duration-300"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </i>
                        </button>
                        <!-- Toggle button -->
                    </div>
                </div>
                @if (Request::is('user/dashboard'))
                    <!-- Date navigation container -->
                    <!-- Date navigation container with fixed size for the date view -->
                    <div class="flex items-center justify-center bg-black p-2 rounded-lg space-x-4 sm:space-x-1">
                        <!-- Previous day button -->
                        <div class="text-white text-2xl cursor-pointer md:block" id="prev-day">
                            <i class="bi bi-chevron-left" onclick="getday(-1)"></i>
                        </div>
                        <!-- Date display section -->
                        <div class="flex items-center flex-grow justify-center">
                            <div class="text-center">
                                <div class="text-red-500 text-xs sm:text-xs" id="day-week">Day X, WEEK Y</div>
                                <input type="text" id="dayshort" hidden>
                                <div class="text-white text-xs sm:text-sm w-48 mx-auto" id="full-date">Today Xth Month,
                                    Year</div>
                            </div>
                        </div>
                        <!-- Next day button -->
                        <div class="text-white text-2xl cursor-pointer w-2" id="next-day">
                            <i class="bi bi-chevron-right" onclick="getday(1)"></i>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            // Get references to HTML elements
                            const dayWeekElement = document.getElementById('day-week');
                            const fullDateElement = document.getElementById('full-date');
                            const shortDayInput = document.getElementById('dayshort'); // New input field
                            const prevDayButton = document.getElementById('prev-day');
                            const nextDayButton = document.getElementById('next-day');

                            // Initialize the current date and today's date
                            let currentDate = new Date();
                            const today = new Date();

                            // Function to update the date display based on the current date
                            function updateDateDisplay(date) {
                                const options = {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                };
                                const formattedDate = date.toLocaleDateString('en-US', options);

                                // Display "Today" for the current date, otherwise show the formatted date
                                if (isSameDay(date, today)) {
                                    fullDateElement.textContent =
                                        `Today ${date.getDate()}th ${date.toLocaleString('en-US', { month: 'long' })}, ${date.getFullYear()}`;
                                } else {
                                    fullDateElement.textContent = formattedDate;
                                }

                                // Calculate the week number (starting from Monday)
                                const startOfYear = new Date(date.getFullYear(), 0, 1);
                                const dayOfYear = Math.floor((date - startOfYear + (startOfYear.getTimezoneOffset() - date
                                    .getTimezoneOffset()) * 60000) / 86400000) + 1;
                                const weekNumber = Math.ceil((dayOfYear + (startOfYear.getDay() + 6) % 7) /
                                    7); // Adjust for weeks starting from Monday

                                // Update the day-week element
                                const dayOfWeek = (date.getDay() + 6) % 7 + 1; // Adjust for Monday being the first day of the week
                                dayWeekElement.textContent = `Day ${dayOfWeek}, WEEK ${weekNumber}`;

                                // Set the short name of the day (e.g., "Mon" for Monday) in the input field
                                const shortDay = formatDateAsYYYYMMDD(date);
                                shortDayInput.value = shortDay;
                            }

                            // Function to format a date as yyyy-mm-dd
                            function formatDateAsYYYYMMDD(date) {
                                const year = date.getFullYear();
                                const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
                                const day = String(date.getDate()).padStart(2, '0');
                                return `${year}-${month}-${day}`;
                            }

                            // Function to change the date by a specified number of days
                            function changeDate(days) {
                                currentDate.setDate(currentDate.getDate() + days);
                                updateDateDisplay(currentDate);
                                setDate();
                            }

                            // Helper function to check if two dates are the same
                            function isSameDay(date1, date2) {
                                return date1.getDate() === date2.getDate() &&
                                    date1.getMonth() === date2.getMonth() &&
                                    date1.getFullYear() === date2.getFullYear();
                            }

                            // Event listeners for previous and next day buttons
                            prevDayButton.addEventListener('click', () => changeDate(-1));
                            nextDayButton.addEventListener('click', () => changeDate(1));

                            // Initial display of the current date
                            updateDateDisplay(currentDate);
                        });
                    </script>
                @else
                    <div class="navigation  text-white text-2xl font-bold hidden md:block">
                        User Portal
                    </div>
                @endif

                <!-- nav-right -->
                <div class="flex items-center h-20 justify-center ">
                    <div class="flex items-center md:gap-2">
                        <!-- Container for displaying the logged-in user information -->
                        <div class="text-gray-800 mr-20 hidden md:block">
                            <!-- Inner container for the text, setting text color to gray -->
                            <div class="text-gray-400">
                                Logged in as |
                                <!-- Span to highlight the user's name in white color -->
                                <span class="text-white">
                                    @if (Auth::check())
                                        <!-- Check if the user is authenticated -->
                                        {{ Auth::user()->name }} <!-- Display the authenticated user's name -->
                                    @else
                                        Guest <!-- Display 'Guest' if no user is authenticated -->
                                    @endif
                                </span>
                            </div>
                        </div>
                        <a href="#" class=" text-gray-800">
                            <!-- component -->
                            <div class="flex justify-center items-center mr-10 mt-2">
                                <div class="relative md:mb-4 mb-4">
                                    <div class="bottom-4 absolute left-6">
                                        <p
                                            class="flex h-1 w-1 items-center justify-center rounded-full bg-red-500 p-3 text-xs text-white">
                                            0
                                        </p>
                                    </div>
                                    <img src="{{ asset('images/bell.png') }}" class="w-8 h-8"alt="">
                                    {{-- <i class="fa fa-bell text-white text-2xl" aria-hidden="true"></i> --}}
                                </div>
                            </div>
                        </a>
                        <!-- Link that triggers the logout form submission when clicked -->
                        <a href="#" class="text-gray-800"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <!-- Container for centering the logout icon -->
                            <div class="flex justify-center items-center mr-5 mt-2">
                                <!-- Inner container for the icon with margin adjustments for different screen sizes -->
                                <div class="relative md:mb-4 mb-4">
                                    <!-- Logout icon styled with Font Awesome -->
                                    <i class="fa fa-power-off text-red-500 text-2xl" aria-hidden="true"></i>
                                </div>
                            </div>
                        </a>
                        <!-- Hidden form for logging out, triggered by the above link -->
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf <!-- Laravel CSRF token for security -->
                            <input type="text" value="web" name="type" hidden>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <script>
        window.onload = function() {
            getday(0);
        };

        function getday(val) {
            // Get the date string from the 'dayshort' element
            var dateString = document.getElementById('dayshort').value;

            // Check if the input field is empty (initial load case)
            if (!dateString) {
                // Set default date to today if empty
                dateString = new Date().toISOString().split('T')[0];
            }

            // Create a Date object from the date string
            var date = new Date(dateString);

            // Calculate the new date by adding the offset
            date.setDate(date.getDate() + val);

            // Format the new date as a string (e.g., YYYY-MM-DD)
            var newDateString = date.toISOString().split('T')[0];

            console.log(newDateString);

            // Optionally, update the input field or other UI elements here
            document.getElementById('dayshort').value = newDateString;
        }
    </script>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="sweetalert2.all.min.js"></script>
    <script src="sweetalert2.min.js"></script>
    <link rel="stylesheet" href="sweetalert2.min.css">
</head>

<body>
    @extends('layout.user-layout')
    @section('user-content')
        <div class="container  bg-[#fafafa] w-full" id="container">
            <div class="mt-24 mx-4">
                <div><span class="text-gray-700 text-sm">Home / </span><strong class="text-sm">Goal (SMART)</strong></div>
                <div class="flex flex-col gap-0 mt-4">
                    <div class="flex  flex-row justify-center w-full">
                        <span class="text-4xl text-center w-[10%] md:w-[40%]"></span>
                        <span class="text-xl text-center w-[20%] md:w-[30%]"></span>
                        <span class="flex text-center w-[30%] md:w-[25%]">Add Goal</span>
                        <span class="flex text-center w-[30%] md:w-[15%]">View Goal</span>
                    </div>


                    <form id="myForm" method="POST" action="{{ route('save.goal') }}">
                        @csrf




                        <div class="flex flex-row space-x-4 md:space-y-0 md:space-x-4 w-full mt-8 ">
                            <span class="md:text-4xl md:h-14  text-md text-center w-[30%] md:w-[57%] ">SMART Goal</span>
                            <div class="flex flex-row md:w-[20%] w-[25%]">
                                <span class="text-2xl justify-center md:mt-3">+</span>
                                <input type="text" name="goal_name" placeholder="Goal Name"
                                    class="md:text-xl border border-black text-md flex text-center w-full md:w-full">

                            </div>
                            <div class="md:text-xl text-md flex text-center w-[40%] md:w-[20%] ">
                                <span class="w-2/3 flex justify-center">
                                    <div class=" text-black" id="specificDisplay">
                                        @if (isset($goal))
                                            {{ $goal['goal_name'] }}
                                        @endif
                                    </div>
                                </span>
                                <span class="w-1/3 flex-1">progress</span>
                            </div>
                        </div>

                        <!-- Specific -->
                        <div class="flex flex-row  w-full items-center mt-8">
                            <div class="flex items-center md:w-[60%] flex-1">
                                <div
                                    class="w-16 h-20 hidden md:flex items-center justify-center md:rounded-tl-full bg-[#CFE8E1]">
                                    <img src="{{ asset('img/target.png') }}" alt="Goal Icon" class="w-6 h-6">
                                </div>
                                <div
                                    class="hidden md:flex w-20 h-20 text-white text-6xl px-2 items-center justify-center font-bold bg-[#04AE88]">
                                    S</div>
                                <div
                                    class="w-11/12 md:w-full md:h-20 h-14 bg-[#94DBC3] flex flex-col items-center justify-center md:justify-start md:items-start">
                                    <img src="{{ asset('img/target.png') }}" alt="Goal Icon"
                                        class="w-8 h-8 md:w-8 md:h-8 md:ml-9 md:hidden">
                                    <span class="md:text-xl text-md md:ml-2 md:mt-3"><strong>Specific</strong></span>
                                    <div class="ml-2 hidden md:block">
                                        <span class="text-sm">The goal must be very specific and grounded in something
                                            that's
                                            significant to you.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="w-16 h-20 hidden md:flex items-center justify-center rounded-tr-full bg-[#94DBC3]">
                            </div>

                            <div class="ml-2 md:ml-5 flex items-center w-[25%] md:w-[20%]">
                                <input type="text" name="specific" placeholder=""
                                    class="border border-black px-3 flex-1 h-14 my-2 w-full">
                            </div>
                            <div class="ml-2 md:ml-5 flex text-center w-[40%] md:w-[20%] h-20 mt-2 md:mt-0">
                                <div class="border border-black flex items-center h-14 my-2 w-full md:w-2/3">
                                    <div
                                        class="ml-2 mr-2 text-white md:text-4xl text-xs px-2 h-8 w-8 flex items-center justify-center font-bold bg-[#04AE88]">
                                        S</div>
                                    <div class="text-xs text-black ga" id="specificDisplay">
                                        @if (isset($goal))
                                            {{ $goal['specific'] }}
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center h-14 my-2 w-1/3"></div>
                            </div>
                        </div>

                        <!-- Measurable -->
                        <div class="flex flex-row  w-full items-center">
                            <div class="flex items-center md:w-[60%] flex-1">
                                <div class="w-16 h-20 hidden md:flex items-center justify-center bg-[#F8EAC7]">
                                    <img src="{{ asset('img/progress.png') }}" alt="Goal Icon" class="w-6 h-6">
                                </div>
                                <div
                                    class="hidden md:flex w-20 h-20 text-white text-6xl px-2 items-center justify-center font-bold bg-[#F2C65C]">
                                    M</div>
                                <div
                                    class="w-11/12 md:w-full md:h-20 h-14 bg-[#F7D895] flex flex-col items-center justify-center md:justify-start md:items-start">
                                    <img src="{{ asset('img/progress.png') }}" alt="Goal Icon"
                                        class="w-8 h-8 md:w-8 md:h-8 md:ml-9 md:hidden">
                                    <span class="md:text-xl text-md md:ml-2 md:mt-3"><strong>Measurable</strong></span>
                                    <div class="ml-2 hidden md:block">
                                        <span class="text-sm">The goal must have some sort of measurement
                                            (days,pounds,miles,etc.).</span>
                                    </div>
                                </div>
                            </div>
                            <div class="w-16 h-20 hidden md:flex items-center justify-center  bg-[#F7D895]"></div>
                            <div class="ml-2 md:ml-5 flex items-center w-[25%] md:w-[20%]">
                                <input type="text" name="measurable"
                                    class="border px-3  border-black flex-1 h-14 my-2 w-full">
                            </div>
                            <div class="ml-2 md:ml-5 flex text-center w-[40%] md:w-[20%] h-20 mt-2 md:mt-0">
                                <div class="border border-black flex items-center h-14 my-2 w-full md:w-2/3">
                                    <div
                                        class="ml-2 mr-2 text-white md:text-4xl text-xs px-2 h-8 w-8 flex items-center justify-center font-bold bg-[#F2C65C]">
                                        M</div>
                                    <div class="text-xs text-black" id="measurableDisplay">
                                        @if (isset($goal))
                                            {{ $goal['measurable'] }}
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center h-14 my-2 w-1/3"></div>
                            </div>
                        </div>

                        <!-- Achievable -->
                        <div class="flex flex-row  w-full items-center">
                            <div class="flex items-center md:w-[60%] flex-1">
                                <div class="w-16 h-20 hidden md:flex items-center justify-center  bg-[#D8E8F0]">
                                    <img src="{{ asset('img/achivement.png') }}" alt="Goal Icon" class="w-6 h-6">
                                </div>
                                <div
                                    class="hidden md:flex w-20 h-20 text-white text-6xl px-2 items-center justify-center font-bold bg-[#36B4E6]">
                                    A</div>
                                <div
                                    class="w-11/12 md:w-full md:h-20 h-14 bg-[#ABD9F3] flex flex-col items-center justify-center md:justify-start md:items-start">
                                    <img src="{{ asset('img/achivement.png') }}" alt="Goal Icon"
                                        class="w-8 h-8 md:w-8 md:h-8 md:ml-9 md:hidden">
                                    <span class="md:text-xl text-md md:ml-2 md:mt-3"><strong>Achievable</strong></span>
                                    <div class="ml-2 hidden md:block">
                                        <span class="text-sm">The goal must be realistic and reasonable.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="w-16 h-20 hidden md:flex items-center justify-center  bg-[#ABD9F3]"></div>
                            <div class="ml-2 md:ml-5 flex items-center w-[25%] md:w-[20%]">
                                <input type="text" name="achievable"
                                    class="border border-black px-3  flex-1 h-14 my-2 w-full">
                            </div>
                            <div class="ml-2 md:ml-5 flex text-center w-[40%] md:w-[20%] h-20 mt-2 md:mt-0">
                                <!-- Display Achievable Goal -->
                                <div class="border border-black flex items-center h-14 my-2 w-full md:w-2/3">
                                    <div
                                        class="ml-2 mr-2 text-white md:text-4xl text-xs px-2 h-8 w-8 flex items-center justify-center font-bold bg-[#36B4E6]">
                                        A
                                    </div>
                                    <div class="text-xs text-black" id="measurableDisplay">
                                        @if (isset($goal))
                                            {{ $goal['achievable'] }}
                                        @endif
                                    </div>
                                </div>

                                <!-- Input field to display or disable -->
                                <div class="border border-black flex text-center items-center h-14 my-2  w-1/3">
                                    @if (isset($goal))
                                        <input type="text" name="achievable_progress"
                                            class="text-xs mx-1 bg-transparent w-full h-full border-none focus:outline-none"
                                            value="{{ $goal['achievable_progress'] }}">
                                    @else
                                        <input type="text"
                                            class="text-xs mx-1 bg-transparent w-full h-full border-none focus:outline-none"
                                            disabled>
                                    @endif
                                </div>
                            </div>

                        </div>

                        <!-- Relevant -->
                        <div class="flex flex-row  w-full items-center">
                            <div class="flex items-center md:w-[60%] flex-1">
                                <div class="w-16 h-20 hidden md:flex items-center justify-center  bg-[#FBD1B1]">
                                    <img src="{{ asset('img/human-brain.png') }}" alt="Goal Icon" class="w-6 h-6">
                                </div>
                                <div
                                    class="hidden md:flex w-20 h-20 text-white text-6xl px-2 items-center justify-center font-bold bg-[#ED4A28]">
                                    R</div>
                                <div
                                    class="w-11/12 md:w-full md:h-20 h-14 bg-[#FCBCA1] flex flex-col items-center justify-center md:justify-start md:items-start">
                                    <img src="{{ asset('img/human-brain.png') }}" alt="Goal Icon"
                                        class="w-8 h-8 md:w-8 md:h-8 md:ml-9 md:hidden">
                                    <span class="md:text-xl text-md md:ml-2 md:mt-3"><strong>Relevant</strong></span>
                                    <div class="ml-2 hidden md:block">
                                        <span class="text-sm">The goal must relate to what you're hoping to
                                            accomplish</span>
                                    </div>
                                </div>
                            </div>
                            <div class="w-16 h-20 hidden md:flex items-center justify-center bg-[#FCBCA1]"></div>
                            <div class="ml-2 md:ml-5 flex items-center w-[25%] md:w-[20%]">
                                <input type="text" name="relevant"
                                    class="border border-black flex-1 px-3   h-14 my-2 w-full">
                            </div>
                            <div class="ml-2 md:ml-5 flex text-center w-[40%] md:w-[20%] h-20 mt-2 md:mt-0">
                                <div class="border border-black flex items-center h-14 my-2 w-full md:w-2/3">
                                    <div
                                        class="ml-2 mr-2 text-white md:text-4xl text-xs px-2 h-8 w-8 flex items-center justify-center font-bold bg-[#EF492B]">
                                        R</div>
                                    <!-- Display Relevant Goal -->
                                    <div class="text-xs text-black" id="measurableDisplay">
                                        @if (isset($goal))
                                            {{ $goal['relevant'] }}
                                        @endif
                                    </div>

                                </div>
                                <div class="flex items-center h-14 my-2 w-1/3"></div>
                            </div>
                        </div>

                        <!-- Time-bound -->
                        <div class="flex flex-row  w-full items-center">
                            <div class="flex items-center md:w-[60%] flex-1">
                                <div
                                    class="w-16 h-20 hidden md:flex items-center justify-center md:rounded-bl-full bg-[#D4DAE6]">
                                    <img src="{{ asset('img/clock.png') }}" alt="Goal Icon" class="w-6 h-6">
                                </div>
                                <div
                                    class="hidden md:flex w-20 h-20 text-white text-6xl px-2 items-center justify-center font-bold bg-[#481E70]">
                                    T</div>
                                <div
                                    class="w-11/12 md:w-full md:h-20 h-14 bg-[#D1C5D9] flex flex-col items-center justify-center md:justify-start md:items-start">
                                    <img src="{{ asset('img/clock.png') }}" alt="Goal Icon"
                                        class="w-8 h-8 md:w-8 md:h-8 md:ml-9 md:hidden">
                                    <span class="md:text-xl text-md md:ml-2 md:mt-3"><strong>Time-bound</strong></span>
                                    <div class="ml-2 hidden md:block">
                                        <span class="text-sm">The goal must have a timeframe and that timeframe must be
                                            reasonable.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="w-16 h-20 hidden md:flex items-center justify-center rounded-br-full bg-[#D1C5D9]">
                            </div>
                            <div class="ml-2 md:ml-5 flex items-center w-[25%] md:w-[20%]">
                                <input type="text" name="time_bound" id="time_bound" placeholder="SET TIME"
                                    class="border text-center border-black flex-1 h-14 my-2 w-full">
                            </div>


                            <div class="ml-2 md:ml-5 flex text-center w-[40%] md:w-[20%] h-20 mt-2 md:mt-0">
                                <div class="border border-black flex items-center h-14 my-2 w-full md:w-2/3">
                                    <div
                                        class="ml-2 mr-2 text-white md:text-4xl text-xs px-2 h-8 w-8 flex items-center justify-center font-bold bg-[#481E70]">
                                        T</div>
                                    <!-- Display Time Bound Goal -->
                                    <div class="text-xs text-black" id="timeboundDisplay">
                                        @if (isset($goal))
                                            {{ $goal->time_bound }}
                                        @endif
                                    </div>
                                </div>
                                <div class="border border-black flex items-center h-14 my-2 w-1/3">
                                    <div id="remainingDaysDisplay"
                                        class="text-xs text-black flex-1 bg-transparent h-full flex items-center px-2">
                                        @if (isset($goal))
                                            {{-- {{ $goal['achievable_progress'] }} --}}
                                            <input type="text" name="time_progress" id="remainingDaysDisplay"
                                                class="text-xs mx-1 bg-transparent w-full h-full border-none focus:outline-none"
                                                value="{{ $goal['time_progress'] }}">
                                        @else
                                            <input type="text"
                                                class="text-xs mx-1 bg-transparent w-full h-full border-none focus:outline-none"
                                                disabled>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- hidden input field id --}}
                        @if (isset($goal))
                            <input type="text" name="goal_id"
                                class="text-xs mx-1 bg-transparent w-full h-full hidden border-none focus:outline-none"
                                value="{{ $goal['id'] }}">
                        @endif


                        {{-- update and save button --}}
                        <div class="flex flex-row  w-full items-center">
                            <div class="flex items-center md:w-[60%] flex-1">

                            </div>
                            <div class="w-16 h-20 hidden md:flex items-center justify-center rounded-br-full"></div>
                            <div class="ml-2 md:ml-5 flex justify-center items-center w-[25%] md:w-[20%]">
                                @if (isset($goal))
                                    <button type="submit" class="bg-black text-white p-2 px-4 rounded-md">Update</button>
                                @else
                                    <button id="saveButton" type="submit"
                                        class="bg-black text-white p-2 px-4 rounded-md">Save</button>
                                @endif
                            </div>
                            <div class="ml-2 md:ml-5 flex text-center w-[40%] md:w-[20%] h-20 mt-2 md:mt-0">

                            </div>
                        </div>
                    </form>

                    <!-- Save Button -->
                    <div class="flex justify-end mt-10 mb-4">

                    </div>
                </div>
            </div>
        </div>
    @endsection
</body>

{{-- date update script --}}
<script>
    $(function() {
        // Function to format the date as 'Month Day' with ordinal suffix
        function formatDate(date) {
            const options = {
                month: 'long'
            };
            const day = date.getDate();
            const month = date.toLocaleDateString(undefined, options);

            // Function to get ordinal suffix
            function getOrdinalSuffix(day) {
                if (day > 3 && day < 21) return 'th'; // 11th, 12th, 13th
                switch (day % 10) {
                    case 1:
                        return 'st';
                    case 2:
                        return 'nd';
                    case 3:
                        return 'rd';
                    default:
                        return 'th';
                }
            }

            return `${month} ${day}${getOrdinalSuffix(day)}`;
        }

        // Function to calculate remaining days from today to selected end date
        function calculateRemainingDays(selectedDate) {
            const endDate = new Date(selectedDate); // Set end date based on selected date
            const today = new Date(); // Current date

            // Check if selected date is valid (not NaN and after or equal to today)
            if (!isNaN(endDate.getTime()) && endDate >= today) {
                // Calculate difference in milliseconds
                const diffTime = endDate.getTime() - today.getTime();

                // TO DO Some time we need to do save remming date to database
                // Calculate difference in days
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                $('#remainingDaysDisplay').text(diffDays + ' days left');
            } else {
                $('#remainingDaysDisplay').text(''); // Clear display if invalid date selected or in the past
            }
        }

        // Initialize date picker on #time_bound input
        $("#time_bound").datepicker({
            dateFormat: "yy-mm-dd", // Date format
            minDate: +1, // Disable today and previous days
            onSelect: function(dateText) {
                calculateRemainingDays(dateText);
                calculateWeeks(dateText); // Also calculate weeks as per previous requirement
            }
        });

        // Function to calculate weeks from the selected date
        function calculateWeeks(selectedDate) {
            const endDate = new Date(selectedDate); // Set end date based on selected date
            const today = new Date(); // Current date

            // Check if selected date is valid (not NaN and after or equal to today)
            if (!isNaN(endDate.getTime()) && endDate >= today) {
                // Calculate difference in milliseconds
                const diffTime = endDate.getTime() - today.getTime();

                // Calculate difference in weeks
                const diffWeeks = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 7));

                $('#timeboundDisplay').text(diffWeeks + ' weeks start: ' + formatDate(today) + '');
            } else {
                $('#timeboundDisplay').text(''); // Clear display if invalid date selected or in the past
            }
        }

        // Initial calculation if a date is already selected
        @if (isset($goal) && isset($goal->time_bound))
            calculateRemainingDays("{{ $goal->time_bound }}");
            calculateWeeks("{{ $goal->time_bound }}");
        @endif
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const saveButton = document.getElementById('saveButton');
        const myForm = document.getElementById('myForm');

        saveButton.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default form submission

            Swal.fire({
                title: "Do you want to save the goal?",
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: "Save",
                denyButtonText: `Don't save`
            }).then((result) => {
                if (result.isConfirmed) {
                    // Optionally, you can validate form fields here before submission
                    myForm.submit(); // Submit the form
                } else if (result.isDenied) {
                    Swal.fire("Goal not saved", "", "info");
                    // Optionally, handle the case where user chooses not to save
                }
            });
        });
    });
</script>

</html>

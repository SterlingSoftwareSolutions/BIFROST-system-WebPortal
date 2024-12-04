<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- Include jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
    <style>
        .image {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .image img {
            width: 100px;
            height: 100px;
            object-cover: cover;
        }
    </style>
    <script>
        window.storagePath = "{{ asset('storage') }}";
    </script>
</head>

<body>
    @extends('layout.user-layout')
    @section('user-content')
        <!-- Container with overflow for scrolling and full width -->
        <div class="container overflow-y-auto w-full bg-[#fafafa] flex-grow" id="container">
            <!-- Main content area with top margin for large screens and smaller margin for medium screens -->
            <div class="w-full mt-36 lg:mt-24 md:mt-24">
                <!-- Content section with gray background -->
                <div class="mx-4 ">
                    <!-- Header with breadcrumb and add new profile button -->
                    <div>
                        <div class="justify-between flex mx-4 text-sm">
                            <div><span class="text-gray-700">Home / </span><strong>Profile</strong></div>
                            {{-- 
                            To DO -> button move admin panel
                            <div>
                                <a href="{{ route('usernewprofile') }}">
                                    <button class="bg-black text-white p-2 px-4 rounded-md">Add New Profile</button>
                                </a>
                            </div> --}}
                        </div>
                    </div>
                    <!-- Profile and session dashboard section -->
                    <div class="w-full lg:flex mt-5">
                        <div class="lg:w-[30%] flex-col w-full">
                            <div class="bg-white shadow rounded-lg p-6 mx-4 px-4">
                                <div class="flex justify-between">
                                    <a href="#"
                                        class="btn p-2 rounded-lg text-white bg-black mb-4 text-sm lg:text-base">Profile</a>
                                    <a href="#" class="btn text-red-500 mb-4 text-sm lg:text-base">Edit</a>
                                </div>
                                <div class="flex items-center mt-4 space-x-4 ">
                                    <div>
                                        <img src="{{ asset($profileImage) }}" alt="Profile Picture"
                                            class="w-12 h-12 rounded-[50%] lg:w-20 lg:h-20">
                                    </div>
                                    <div class="flex flex-col">
                                        {{-- here name dispaly below ajex trough --}}
                                        <h5 id="nameDisplay"
                                            class="text-lg font-semibold overflow-hidden text-ellipsis lg:text-2xl">
                                            <!-- User's name will be displayed here -->
                                            {{ $user->name }}
                                        </h5>
                                        <h6 class="text-gray-500 text-sm lg:text-lg"><span>{{ $member->gender }}</span>,
                                            <span>{{ $member->age }}</span> years
                                        </h6>
                                    </div>
                                </div>
                                <div class="flex justify-between border-t border-black items-center mt-6">
                                    <div class="text-center">
                                        <h6 class="text-black text-sm lg:text-lg">HEIGHT</h6>
                                        <p class="text-gray-800 text-xl lg:text-3xl">{{ $member->height }} cm</p>
                                    </div>
                                    <div class="border-l border-black h-12"></div> <!-- Divider -->
                                    <div class="text-center">
                                        <h6 class="text-black text-sm lg:text-lg">WEIGHT</h6>
                                        <p class="text-gray-800 text-xl lg:text-3xl">{{ $member->weight }} kg</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Session dashboard section -->
                        <div class="mb-5 lg:w-[70%] mt-4 lg:mt-0">

                            <div class="bg-white shadow rounded-lg p-6 mx-4 px-4 flex gap-3">
                                <div>
                                    <label
                                        class="btn p-2 rounded-lg text-white bg-black mb-4 text-sm lg:text-base whitespace-nowrap">Progress
                                        Pics</label>
                                </div>
                                <div class="flex flex-col items-center w-full">
                                    <div>
                                        <label id="currentMonthLabel" class="justify-center"></label>
                                    </div>
                                    <div class="flex h-80 items-center justify-between">
                                        <button id="prevButton" onclick="getImage('prev')"
                                            class="p-4 text-gray-600 hover:text-gray-800">
                                            <i class="fas fa-chevron-left"></i> <!-- Font Awesome icon for 'Previous' -->
                                        </button>
                                        <div class="flex flex-col items-center">
                                            <h4 class="text-center">Front</h4>
                                            <div class="p-4" id="currentFrontImage">
                                                display front image
                                            </div>
                                        </div>

                                        <div class="flex flex-col items-center">
                                            <h4 class="text-center">Side</h4>
                                            <div class="p-4" id="currentSideImage">
                                                display side image
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-center">
                                            <h4 class="text-center">Back</h4>
                                            <div class="p-4" id="currentBackImage">
                                                display back image
                                            </div>
                                        </div>
                                        <button id="nextButton" onclick="getImage('next')"
                                            class="p-4 text-gray-600 hover:text-gray-800">
                                            <i class="fas fa-chevron-right"></i> <!-- Font Awesome icon for 'Next' -->
                                        </button>
                                    </div>

                                    <div class="flex flex-row gap-2">
                                        <!-- Display the images for the current and previous months -->
                                        <div class="flex" id="some-element">
                                            <!-- Content will be dynamically inserted here -->

                                        </div>
                                        <button id="openPopupBtn"
                                            class="bg-black h-10 px-6 text-white rounded-md mt-24 whitespace-nowrap">+
                                            ADD</button>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        {{-- Start popup --}}
        <div id="popupForm" class="popup fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center hidden">
            <div class="popup-content inset-0 rounded-lg shadow-lg w-4/12 p-6">
                <div class="bg-black text-white p-4 flex justify-between items-center rounded-t-lg flex-row">
                    <h4 class="text-xl font-semibold">Add Images</h4>
                    <div class="flex items-center space-x-4">
                        <a href="#" class="text-white hover:underline">Edit</a>
                        <a class="text-white">|</a>
                        <span class="close text-4xl cursor-pointer">&times;</span>
                    </div>
                </div>
                <div class="p-6 bg-white">
                    <form class="space-y-4" action="{{ route('monthly_images.store') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="flex gap-5 items-center">
                            <label for="month" class="w-20">Month</label>
                            <input type="date" id="month" name="month" class="border w-64" required>
                        </div>
                        <div class="flex gap-5 items-center">
                            <label for="front" class="w-20">Front</label>
                            <input type="file" id="front" name="front_image" accept=".png,.jpg,.jpeg,.gif,.img"
                                class="border w-64" required>
                        </div>
                        <div class="flex gap-5 items-center">
                            <label for="side" class="w-20">Side</label>
                            <input type="file" id="side" name="side_image" accept=".png,.jpg,.jpeg,.gif,.img"
                                class="border w-64" required>
                        </div>
                        <div class="flex gap-5 items-center">
                            <label for="back" class="w-20">Back</label>
                            <input type="file" id="back" name="back_image" accept=".png,.jpg,.jpeg,.gif,.img"
                                class="border w-64" required>
                        </div>
                        <input type="hidden" name="user_id" id="user_id" value="{{ $user->id ?? null }}">
                        <button type="submit"
                            class="bg-[#FB1018] text-white py-2 px-4 rounded mb-2 hover:bg-red-700">Upload</button>
                    </form>


                </div>
            </div>
        </div>
        {{-- End popup --}}
        <script>
            window.onload = function() {
                console.log("Page has fully loaded");

                // Get the current date in yyyy-mm-dd format
                let currentDate = new Date().toISOString().split('T')[0];
                console.log(currentDate);
                prevFunction(currentDate);
            };

            function prevFunction(currentDate) {

                // Get the user ID from the hidden input field
                let userId = document.getElementById('user_id').value;

                // Perform the AJAX request
                $.ajax({
                    url: "/prevdata",
                    type: "POST",
                    data: {
                        date: currentDate,
                        id: userId,
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        console.log(response);
                        appendData(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(error); // Handle error
                    },
                });
            }

            function nextFunction(currentDate) {

                // Get the user ID from the hidden input field
                let userId = document.getElementById('user_id').value;

                // Perform the AJAX request
                $.ajax({
                    url: "/nextvdata",
                    type: "POST",
                    data: {
                        date: currentDate,
                        id: userId,
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        console.log(response);
                        updateImageList(response); 
                    },
                    error: function(xhr, status, error) {
                        console.error(error); // Handle error
                    },
                });
            }
            function updateImageList(response) {
    // Sort the response array by year and month
    response.sort((a, b) => {
        const [yearA, monthA] = a.month.split('-').map(Number);
        const [yearB, monthB] = b.month.split('-').map(Number);
        const dateA = new Date(yearA, monthA - 1); // Convert monthIndex to 0-based
        const dateB = new Date(yearB, monthB - 1);
        return dateA - dateB;
    });

    let imageContainer = document.getElementById('some-element');
    imageContainer.innerHTML = ""; // Clear previous content

    let html = '';
    response.forEach(function(month) {
        // Extract year and month from the month string and format it as 'Month YY'
        const [year, monthIndex] = month.month.split('-').map(Number);
        const date = new Date(year, monthIndex - 1); // Convert monthIndex to 0-based
        const monthName = date.toLocaleString('default', {
            month: 'long'
        });
        const yearSuffix = String(year).slice(-2); // Get last two digits of the year
        const formattedMonth = `${monthName} ${yearSuffix}`;

        html += '<div class="month mx-5 border" data-month="' + month.month + '" data-front="' + month
            .front_image +
            '" data-side="' + month.side_image + '" data-back="' + month.back_image + '">';
        html += '<h2 class="text-center">' + formattedMonth + '</h2>'; // Display formatted month
        html += '<div class="images-container flex">';
        html += '<div class="image"><p>Front</p><img src="' +
            `${window.storagePath}/${month.front_image}` +
            '" alt="Front Image" class="image"></div>';
        html += '<div class="image"><p>Side</p><img src="' +
            `${window.storagePath}/${month.side_image}` +
            '" alt="Side Image" class="image"></div>';
        html += '<div class="image"><p>Back</p><img src="' +
            `${window.storagePath}/${month.back_image}` +
            '" alt="Back Image" class="image"></div>';
        html += '</div>'; // Close images-container
        html += '</div>'; // Close month
    });
    imageContainer.innerHTML = html; // Append the generated HTML to the container

    // Add click event listeners to dynamically generated monthDiv elements
    document.querySelectorAll('.month').forEach(div => {
        div.addEventListener('click', () => {
            showImageDetails(div.dataset.month, div.dataset.front, div.dataset.side, div.dataset
                .back);
        });
    });

    // Call showFirstImageDetails for the first image in the response
    if (response.length > 0) {
        const firstMonth = response[0];
        showFirstImageDetails(firstMonth.month, firstMonth.front_image, firstMonth.side_image, firstMonth.back_image);
    }
}
function showFirstImageDetails(month, frontImage, sideImage, backImage) {
    // Extract year and month from the month string
    const [year, monthIndex] = month.split('-').map(Number);

    // Create a new Date object with the given year and month
    const date = new Date(year, monthIndex - 1);

    // Get the full month name and the last two digits of the year
    const options = {
        month: 'long',
        year: 'numeric'
    };
    const formattedDate = date.toLocaleDateString('en-US', options);

    // Update the label text with the formatted month and year
    document.getElementById('currentMonthLabel').textContent = formattedDate;

    // Update the images
    document.getElementById('currentFrontImage').innerHTML = '<img src="' + window.storagePath + '/' +
        frontImage + '" alt="Front Image" class="w-64 h-64 object-cover">';
    document.getElementById('currentSideImage').innerHTML = '<img src="' + window.storagePath + '/' +
        sideImage + '" alt="Side Image" class="w-64 h-64 object-cover">';
    document.getElementById('currentBackImage').innerHTML = '<img src="' + window.storagePath + '/' +
        backImage + '" alt="Back Image" class="w-64 h-64 object-cover">';
}

            function appendData(response) {
                // Sort the response array by year and month
                response.sort((a, b) => {
                    const [yearA, monthA] = a.month.split('-').map(Number);
                    const [yearB, monthB] = b.month.split('-').map(Number);
                    const dateA = new Date(yearA, monthA - 1); // Convert monthIndex to 0-based
                    const dateB = new Date(yearB, monthB - 1);
                    return dateA - dateB;
                });

                let imageContainer = document.getElementById('some-element');
                imageContainer.innerHTML = ""; // Clear previous content

                let html = '';
                response.forEach(function(month) {
                    // Extract year and month from the month string and format it as 'Month YY'
                    const [year, monthIndex] = month.month.split('-').map(Number);
                    const date = new Date(year, monthIndex - 1); // Convert monthIndex to 0-based
                    const monthName = date.toLocaleString('default', {
                        month: 'long'
                    });
                    const yearSuffix = String(year).slice(-2); // Get last two digits of the year
                    const formattedMonth = `${monthName} ${yearSuffix}`;

                    html += '<div class="month mx-5 border" data-month="' + month.month + '" data-front="' + month
                        .front_image +
                        '" data-side="' + month.side_image + '" data-back="' + month.back_image + '" data-date="' +
                        month.date + '">';
                    html += '<h2 class="text-center">' + formattedMonth + '</h2>'; // Display formatted month
                    html += '<div class="images-container flex">';
                    html += '<div class="image"><p>Front</p><img src="' +
                        `${window.storagePath}/${month.front_image}` +
                        '" alt="Front Image" class="image"></div>';
                    html += '<div class="image"><p>Side</p><img src="' +
                        `${window.storagePath}/${month.side_image}` +
                        '" alt="Side Image" class="image"></div>';
                    html += '<div class="image"><p>Back</p><img src="' +
                        `${window.storagePath}/${month.back_image}` +
                        '" alt="Back Image" class="image"></div>';
                    html += '</div>'; // Close images-container
                    html += '</div>'; // Close month
                });
                imageContainer.innerHTML = html; // Append the generated HTML to the container

                // Add click event listeners to dynamically generated monthDiv elements
                document.querySelectorAll('.month').forEach(div => {
                    div.addEventListener('click', () => {
                        displayImageDetails(div.dataset.month, div.dataset.front, div.dataset.side, div.dataset
                            .back);
                    });
                });

                // Call displayImageDetails for the last image in the response
                if (response.length > 0) {
                    const lastMonth = response[response.length - 1];
                    displayImageDetails(lastMonth.month, lastMonth.front_image, lastMonth.side_image, lastMonth.back_image);
                }
            }



            function displayImageDetails(month, frontImage, sideImage, backImage) {
                // Extract year and month from the month string
                const [year, monthIndex] = month.split('-').map(Number);

                // Create a new Date object with the given year and month
                const date = new Date(year, monthIndex - 1);

                // Get the full month name and the last two digits of the year
                const options = {
                    month: 'long',
                    year: 'numeric'
                };
                const formattedDate = date.toLocaleDateString('en-US', options);

                // Update the label text with the formatted month and year
                document.getElementById('currentMonthLabel').textContent = formattedDate;

                // Update the images
                document.getElementById('currentFrontImage').innerHTML = '<img src="' + window.storagePath + '/' +
                    frontImage + '" alt="Front Image" class="w-64 h-64 object-cover">';
                document.getElementById('currentSideImage').innerHTML = '<img src="' + window.storagePath + '/' +
                    sideImage + '" alt="Side Image" class="w-64 h-64 object-cover">';
                document.getElementById('currentBackImage').innerHTML = '<img src="' + window.storagePath + '/' +
                    backImage + '" alt="Back Image" class="w-64 h-64 object-cover">';
            }


            function getImage(value) {
                // Get the label element
                const labelElement = document.getElementById('currentMonthLabel');

                // Get the text content of the label
                let labelText = labelElement.textContent;

                // Initialize the date object based on the label text
                // Ensure the labelText is in the format 'Month Year' like 'May 2024'
                // Convert 'Month Year' to 'YYYY-MM' format
                const [monthName, year] = labelText.split(' '); // Split into month and year parts
                const monthIndex = new Date(Date.parse(monthName + " 1, 2021")).getMonth();
                const date = new Date(year, monthIndex); // JavaScript months are 0-based

                if (value === 'prev') {
                    // Get the previous month
                    date.setMonth(date.getMonth() - 1);
                } else if (value === 'next') {
                    // Get the next month
                    date.setMonth(date.getMonth() + 1);
                } else {
                    console.error('Invalid value passed to getImage function');
                    return;
                }

                // Format the new month and year as 'YYYY-MM'
                const newYear = date.getFullYear();
                const newMonth = String(date.getMonth() + 1).padStart(2, '0'); // Adding leading zero if necessary
                const updatedLabelText = `${newYear}-${newMonth}-01`; // Updated date for data fetching

                // Convert 'YYYY-MM' to 'Month YYYY' format for display
                const newMonthName = date.toLocaleString('default', {
                    month: 'long'
                });
                const newLabelText = `${newMonthName} ${newYear}`; // Updated month and year format

                // Update the label with the new month and year
                labelElement.textContent = newLabelText;

                console.log('Updated Label Text:', updatedLabelText);

                // Perform your action based on the value
                if (value === 'prev') {
                    console.log('Previous action for month:', updatedLabelText);
                    // Add your logic to fetch data for the previous month
                    prevFunction(updatedLabelText); // Call a function to fetch data for the previous month
                } else if (value === 'next') {
                    console.log('Next action for month:', updatedLabelText);
                    // Add your logic to fetch data for the next month
                    nextFunction(updatedLabelText); // Call a function to fetch data for the next month
                }
            }
        </script>

        <script src="{{ asset('js/admin.js') }}" defer></script>
    @endsection
</body>

</html>

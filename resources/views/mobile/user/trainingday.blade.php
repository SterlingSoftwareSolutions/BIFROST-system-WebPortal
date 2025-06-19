<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF token -->
    <!-- Include jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <!-- Extend mobile layout -->
    @extends('mobile.layout.mobile-layout')

    <!-- Define content section -->
    @section('content')
        <div class="w-full flex flex-col justify-between min-h-screen h-full ">
            <!-- Background image container -->
            <div class="flex-grow items-center justify-center m-0 p-4 bg-cover bg-center bg-no-repeat"
                style="background-image: url('{{ asset('img/valhalla-bg.jpg') }}');">
                <!-- Day buttons container -->
                <div class="flex flex-col justify-center items-center gap-2.5 pt-32 text-white">
                    <!-- Loop through days -->
                    @php
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    @endphp
                    @foreach ($days as $index => $day)
                        @php
                            $date = $dates[$index];
                            $isSelected = $date == $selectedDay ? 'border-white' : 'border-black';
                        @endphp
                        <!-- Day button -->
                        <button id="{{ strtolower($date) }}" onclick="selctDate(this)">
                            <div
                                class="day bg-transparent border w-72 {{ $isSelected }} hover:border-white text-white p-5 rounded-lg flex justify-between items-center transition duration-300 ease-in-out">
                                <span class="day-name text-lg font-bold">{{ $day }}</span>
                                <span class="date text-sm" id="{{ strtolower($day) }}-date">{{ $date }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endsection

    <!-- JavaScript -->
    <script>
        // Function to select date
        function selctDate(button) {
            console.log(button);
            var day = button.id;
            console.log(day);

            // AJAX request to select day
            $.ajax({
                url: "/select-day",
                type: "POST",
                data: {
                    day: day,
                    _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                },
                success: function(response) {
                    console.log(response);
                    // Remove border-white class from all days
                    document.querySelectorAll('.day').forEach(dayElement => {
                        dayElement.classList.remove('border-white');
                        dayElement.classList.add('border-black');
                    });

                    // Add border-white class to the selected button
                    button.querySelector('.day').classList.remove('border-black');
                    button.querySelector('.day').classList.add('border-white');

                    // Redirect to readinessscore page
                    window.location.href = '/mobile/readinessscore';
                },
                error: function(xhr, status, error) {
                    console.error(error); // Handle error
                }
            });
        }
    </script>
</body>

</html>

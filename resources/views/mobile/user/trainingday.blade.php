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
                    {{-- Time Slot Buttons --}}
                    <div class="flex justify-center gap-2 mb-6 pt-10">
                        @foreach(['06:00:00' => '6am', '09:00:00' => '9am', '12:00:00' => '12pm', '17:00:00' => '5pm'] as $timeVal => $label)
                            <button onclick="filterByTime('{{ $timeVal }}')"
                                class="border border-black text-white rounded-lg px-4 py-2 font-semibold hover:bg-gray-200 hover:text-black transition">
                                {{ $label }}<br>class
                            </button>
                        @endforeach
                    </div>
                    <!-- Loop through days -->
                    {{-- @php
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
                                class="day bg-transparent border w-72 {{ $isSelected }} hover:border-white text-white p-5 rounded-lg flex flex-col gap-2 justify-between items-center transition duration-300 ease-in-out">
                                <div class="w-full flex justify-between items-center">
                                    <span class="day-name text-lg font-bold">{{ $day }}</span>
                                    <span class="date text-sm" id="{{ strtolower($day) }}-date">{{ $date }}</span>
                                </div>
                                <div class="w-full flex justify-between items-center">
                                    <span class="slots text-sm text-white">20 slots available</span>
                                    <span class="text-sm text-white px-3 py-1 rounded-xl border border-white">Reserve</span>
                                </div>
                            </div>
                        </button>
                    @endforeach --}}
                    <div id="classSlots" class="flex flex-col justify-center items-center gap-2.5 text-white">
                        @foreach($dates as $date)
                            @php
                                $formatted = $date->format('d/m/Y');
                                $class = $classesByDate[$formatted] ?? null;
                                $isToday = $date->isToday();
                            @endphp

                            <div
                            class="day bg-transparent border w-72 {{ $isToday ? 'border-white' : 'border-black' }} text-white p-5 rounded-lg flex flex-col gap-2 justify-between items-center cursor-pointer"
                            id="{{ $formatted }}"
                            onclick="selectDate(this)">
                                <div class="w-full flex justify-between items-center">
                                    <span class="day-name text-lg font-bold">{{ $date->format('l') }}</span>
                                    <span class="date text-sm">{{ $formatted }}</span> <!-- Now 23/06/2025 -->
                                </div>
                                <div class="w-full flex justify-between items-center">
                                    @if($class)
                                        <span class="slots text-sm text-white">{{ $class->spots }} spots available</span>

                                        @php
                                            $userReservation = \App\Models\ReservationSession::where('user_id', Auth::id())
                                                ->where('classes_id', $class->id)
                                                ->first();
                                        @endphp

                                        @if($userReservation)
                                            {{-- Cancel Button --}}
                                            <form action="{{ route('class.cancel') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="class_id" value="{{ $class->id }}">
                                                <button type="submit" class="text-sm text-white px-3 py-1 rounded-xl border border-red-300 hover:bg-red-500 hover:text-white transition">
                                                    X
                                                </button>
                                            </form>
                                        @else
                                            {{-- Reserve Button --}}
                                            <form action="{{ route('class.reserve') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="class_id" value="{{ $class->id }}">
                                                <button type="submit" class="text-sm text-white px-3 py-1 rounded-xl border border-white hover:bg-white hover:text-black transition">
                                                    Reserve
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="slots text-sm text-red-300">No class</span>
                                        <span class="text-sm text-white px-3 py-1 rounded-xl border border-white">--</span>
                                    @endif

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endsection

    <!-- JavaScript -->
    <script>
        // Function to select date
        document.addEventListener('DOMContentLoaded', function() {
            window.selectDate = function selectDate(dayDiv) {
                const day = dayDiv.id;
                console.log("Selected:", day);

                // Send AJAX
                $.ajax({
                    url: "/select-day",
                    type: "POST",
                    data: {
                        day: day,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log(response);

                        // Reset all borders
                        document.querySelectorAll('.day').forEach(div => {
                            div.classList.remove('border-white');
                            div.classList.add('border-black');
                        });

                        // Highlight selected
                        dayDiv.classList.remove('border-black');
                        dayDiv.classList.add('border-white');

                        // Redirect
                        window.location.href = '/mobile/readinessscore';
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                    }
                });
            };
        });

        function filterByTime(time) {
            $.ajax({
                url: '/get-class-slots',
                type: 'GET',
                data: { time: time },
                success: function(response) {
                    const container = document.getElementById('classSlots');
                    container.innerHTML = '';

                    response.forEach(slot => {
                        const hasClass = !!slot.class;

                        const isTodayClass = slot.is_today ? 'border-white' : 'border-black';

                        const reserveHtml = hasClass
                            ? (slot.reserved
                                ? `<form method="POST" action="/cancel">
                                        <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                                        <input type="hidden" name="class_id" value="${slot.id}">
                                        <button type="submit" class="text-sm text-white px-3 py-1 rounded-xl border border-red-300 hover:bg-red-500 hover:text-white transition">X</button>
                                </form>`
                                : `<form method="POST" action="/reserve">
                                        <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                                        <input type="hidden" name="class_id" value="${slot.id}">
                                        <button type="submit" class="text-sm text-white px-3 py-1 rounded-xl border border-white hover:bg-white hover:text-black transition">Reserve</button>
                                </form>`)
                            : `<span class="text-sm text-white px-3 py-1 rounded-xl border border-white">--</span>`;

                        const html = `
                            <div class="day bg-transparent border w-72 ${isTodayClass} text-white p-5 rounded-lg flex flex-col gap-2 justify-between items-center cursor-pointer">
                                <div class="w-full flex justify-between items-center">
                                    <span class="day-name text-lg font-bold">${slot.day_name}</span>
                                    <span class="date text-sm">${slot.date}</span>
                                </div>
                                <div class="w-full flex justify-between items-center">
                                    ${hasClass
                                        ? `<span class="slots text-sm text-white">${slot.class.spots} spots available</span>`
                                        : `<span class="slots text-sm text-red-300">No class</span>`}
                                    ${reserveHtml}
                                </div>
                            </div>
                        `;

                        container.innerHTML += html;
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching time slots:', error);
                }
            });
        }

    </script>
</body>

</html>

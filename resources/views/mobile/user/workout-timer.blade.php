<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body class="overflow-y-auto">
    @extends('mobile.layout.mobile-layout')

    @section('content')
        <div class="w-full  flex flex-col justify-between h-screen h-100% overflow-y-auto ">
            <div class="flex-grow items-center justify-center m-0 p-4 bg-cover bg-center bg-no-repeat"
                style="background-image: url('{{ asset('img/valhalla-bg.jpg') }}');">
                <div class="justify-center items-center text-white text-center h-full pt-20">
                    <div class="text-xl mb-5">Conditioning</div>
                    @if (isset($detailsconditioning[0]->amrap) && $detailsconditioning[0]->amrap == 1)
                        <div class="text-base mb-5">AMRAP</div>
                    @else
                        <div class="text-base mb-5">Rounds</div>
                    @endif
                    @if (isset($detailsconditioning[0]->workout->categoryOption->category_name))
                        <div class="text-base mb-12">{{ $detailsconditioning[0]->workout->categoryOption->category_name }}
                        </div>
                    @endif

                    <div class="border-b border-white mb-12"></div>
                    <div class="mb-16 text-xl">
                        <table class="w-full text-white tablee">
                            <thead class="justify-between">
                                <tr>
                                    <th class="py-2">Workout</th>
                                    <th class="py-2">Rep</th>
                                    <th class="py-2">Weight</th>
                                </tr>
                            </thead>
                            <tbody class="table-body"> <!-- Initially hidden table body -->
                                @foreach ($detailsconditioning as $index => $conditioningdetail)
                                    <tr class="border-b border-gray-300">
                                        <td class="py-4">{{ $conditioningdetail->workout->workout }}</td>
                                        <td class="py-4 workouts">{{ $conditioningdetail->reps }}</td>
                                        <td class="py-4">
                                            <input type="text" value="{{ $conditioningdetail->weight }}"
                                                class="w-24 h-8 text-center text-3xl text-black">
                                        </td>
                                        <td id="timeToComplete-{{ $index }}" class="py-4 hidden">
                                            {{ $conditioningdetail->time_to_complete }} </td>

                                    </tr>
                                @endforeach
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-center items-center mb-10">
                        <button id="decreaseRounds"
                            class="bg-transparent border border-white text-3xl text-white w-16 h-20 cursor-pointer"
                            onclick="decrementValue(this)">-</button>
                            <input id="roundsInput" value="{{ isset($conditioningdetail->rounds) ? $conditioningdetail->rounds : '' }}" min="1"
                            class="w-24 h-20 text-center text-3xl text-black">
                        
                        <button id="increaseRounds"
                            class="bg-transparent border border-white text-3xl text-white w-16 h-20 cursor-pointer"
                            onclick="incrementValue(this)">+</button>
                    </div>
                    <div class="mb-0">
                        <div class="text-base mb-2">TIME TO COMPLETE</div>
                        <div id="timer" class="text-5xl text-black bg-white p-2">00:00:00</div>
                        {{-- <div class="flex w-full">
                                <button id="startButton" class="w-1/2 h-16 bg-green-600 text-white cursor-pointer ">START</button>
                                <button id="stopButton" class="w-1/2 h-16 bg-red-600 text-white cursor-pointer">STOP</button>
                            </div>   --}}
                    </div>
                </div>
            </div>
        </div>
        <div id="popup"
            class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-center z-50 cursor-pointer">
            <div class="bg-white rounded-full w-48 h-48 flex justify-center items-center text-2xl text-black text-center"
                onclick="startCountdown()">
                Ready?
            </div>
        </div>
        <script>
            class Timer {
                constructor(displayElement) {
                    this.displayElement = displayElement;
                    this.seconds = 0;
                    this.timer = null;
                }

                updateDisplay() {
                    const hrs = Math.floor(this.seconds / 3600);
                    const mins = Math.floor((this.seconds % 3600) / 60);
                    const secs = this.seconds % 60;
                    this.displayElement.textContent =
                        `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                }

                start() {
                    clearInterval(this.timer);
                    this.timer = setInterval(() => {
                        this.seconds++;
                        this.updateDisplay();
                    }, 1000);
                }

                stop() {
                    clearInterval(this.timer);
                }

                reset(seconds) {
                    this.seconds = seconds;
                    this.updateDisplay();
                }
            }

            const timerDisplay = document.getElementById('timer');
            const timer = new Timer(timerDisplay);

            function startCountdown() {
                const popup = document.getElementById('popup');
                const popupContent = popup.querySelector('.bg-white');
                popup.classList.remove(
                    'cursor-pointer'); // Remove the pointer cursor during the countdown to prevent multiple clicks
                popupContent.onclick = null;

                let countdown = 10;
                const countdownInterval = setInterval(() => {
                    if (countdown > 0) {
                        popupContent.textContent = countdown;
                        countdown--;
                    } else {
                        clearInterval(countdownInterval);
                        popupContent.textContent = 'Go!';
                        setTimeout(() => {
                            popup.classList.add('hidden');
                            const timeToComplete = parseInt(document.querySelector('#timeToComplete-0')
                                .textContent, 10);
                            console.log(timeToComplete)
                            timer.reset(timeToComplete); // Start timer with the time_to_complete value
                            timer.start();
                        }, 1000);
                    }
                }, 1000);
            }

            const increaseRoundsButton = document.getElementById('increaseRounds');
            const decreaseRoundsButton = document.getElementById('decreaseRounds');
            const roundsInput = document.getElementById('roundsInput');

            increaseRoundsButton.addEventListener('click', () => {
                roundsInput.value = parseInt(roundsInput.value) + 1;
            });

            decreaseRoundsButton.addEventListener('click', () => {
                if (roundsInput.value > 1) {
                    roundsInput.value = parseInt(roundsInput.value) - 1;
                }
            });

            function incrementValue(element) {
                var span = element.parentNode.querySelector('input');
                var value = parseInt(span.value, 10);
                value = isNaN(value) ? 0 : value;
                value++;
                span.value = value;
            }

            function decrementValue(element) {
                var span = element.parentNode.querySelector('input');
                var value = parseInt(span.value, 10);
                value = isNaN(value) ? 0 : value;
                value = value <= 0 ? 0 : value - 1;
                span.value = value;
            }
        </script>
    @endsection
</body>


</html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Include jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
</head>

<body class=" overflow-y-auto">
    @extends('mobile.layout.mobile-layout')
    @section('content')
        <div class="w-full flex flex-col justify-between min-h-screen h-full ">
            <div class="flex-grow w-full flex items-center justify-center m-0 p-4 bg-cover bg-center bg-no-repeat"
                style="background-image: url('{{ asset('img/valhalla-bg.jpg') }}');">
                <div id="ring" class="border border-gray-600 rounded-3xl flex items-start">
                    <div class="flex w-full flex-col justify-center items-center gap-2.5 p-5 text-white">

                        {{-- Category Icons (Static) --}}
                        {{-- Category Icons (Static) --}}
                        <div class="flex gap-4 justify-start mb-6 overflow-x-auto w-full px-2">
                            {{-- Warmup --}}
                            <button
                                class="category-tab flex flex-col items-center justify-center w-16 h-16 border border-white rounded-lg px-1 py-2 bg-black bg-opacity-50 text-white hover:bg-opacity-80 transition duration-300 ease-in-out transform hover:scale-105 text-xs text-center break-words leading-tight"
                                data-target="#section-warmup" data-icon-white="{{ asset('icon/warmupwhite.png') }}"
                                data-icon-black="{{ asset('icon/warmup.png') }}">
                                <img src="{{ asset('icon/warmupwhite.png') }}" alt="Warmup Icon"
                                    class="w-5 h-5 mb-1 icon-img">
                                <span class="text-xs leading-tight text-center">Warm<br>Up</span>
                            </button>

                            {{-- Strength --}}
                            <button
                                class="category-tab flex flex-col items-center justify-center w-16 h-16 border border-white rounded-lg px-1 py-2 bg-black bg-opacity-50 text-white hover:bg-opacity-80 transition duration-300 ease-in-out transform hover:scale-105 text-xs text-center break-words leading-tight"
                                data-target="#section-strength" data-icon-white="{{ asset('icon/strengthwhite.png') }}"
                                data-icon-black="{{ asset('icon/strength.png') }}">
                                <img src="{{ asset('icon/strengthwhite.png') }}" alt="Strength Icon"
                                    class="w-5 h-5 mb-1 icon-img">
                                <span class="text-xs leading-tight text-center">Strength</span>
                            </button>

                            {{-- Weightlifting --}}
                            <button
                                class="category-tab flex flex-col items-center justify-center w-16 h-16 border border-white rounded-lg px-1 py-2 bg-black bg-opacity-50 text-white hover:bg-opacity-80 transition duration-300 ease-in-out transform hover:scale-105 text-xs text-center break-words leading-tight"
                                data-target="#section-weightlifting"
                                data-icon-white="{{ asset('icon/weightliftingWhite.png') }}"
                                data-icon-black="{{ asset('icon/weightlifting.png') }}">
                                <img src="{{ asset('icon/weightliftingWhite.png') }}" alt="Weightlifting Icon"
                                    class="w-5 h-5 mb-1 icon-img">
                                <span class="text-xs leading-tight text-center">Weight<br>Lifting</span>
                            </button>

                            {{-- Conditioning --}}
                            <button
                                class="category-tab flex flex-col items-center justify-center w-16 h-16 border border-white rounded-lg px-1 py-2 bg-black bg-opacity-50 text-white hover:bg-opacity-80 transition duration-300 ease-in-out transform hover:scale-105 text-xs text-center break-words leading-tight"
                                data-target="#section-conditioning"
                                 data-icon-white="{{ asset('icon/conditioningWhite.png') }}"
                                data-icon-black="{{ asset('icon/conditioning.png') }}">
                                <img src="{{ asset('icon/conditioningWhite.png') }}" alt="Conditioning Icon"
                                    class="w-5 h-5 mb-1 icon-img">
                                <span class="text-xs leading-tight text-center">Condi<br>tioning</span>
                            </button>

                            {{-- Test --}}
                            <button
                                class="category-tab flex flex-col items-center justify-center w-16 h-16 border border-white rounded-lg px-1 py-2 bg-black bg-opacity-50 text-white hover:bg-opacity-80 transition duration-300 ease-in-out transform hover:scale-105 text-xs text-center break-words leading-tight"
                                data-target="#section-test" data-icon-white="{{ asset('icon/testWhite.png') }}"
                                data-icon-black="{{ asset('icon/test.png') }}">
                                <img src="{{ asset('icon/testWhite.png') }}" alt="Test Icon" class="w-5 h-5 mb-1 icon-img">
                                <span class="text-xs leading-tight text-center">Test</span>
                            </button>
                        </div>
                        {{-- Category Workout Sections --}}

                        <div id="categoryDisplayZone">
                            {{-- Warmup --}}
                            <div id="section-warmup" class="category-section">
                                <div class="w-full p-8 bg-black text-xs bg-opacity-50 rounded-lg mb-6">
                                    @if ($detailswarmup->isEmpty())
                                        <p class="text-white text-center">No warm-up workouts assigned.</p>
                                    @else
                                        <table class="w-full text-white ">
                                            <thead class="justify-between">
                                                <tr>
                                                    <th class="py-2">Workout</th>
                                                    <th class="px-2">Weight</th>
                                                    <th class="px-2">Rep</th>
                                                    <th class="col-span-2"></th>

                                                </tr>
                                            </thead>
                                            <tbody class="table-body">
                                                @foreach ($detailswarmup as $warmupdetail)
                                                    <tr class="border-b border-gray-300">
                                                        {{-- Category --}}
                                                        <td class="py-4" onclick="savewarmup(this)"
                                                            data-workout-id="{{ $warmupdetail->id }}"
                                                            data-daily-warmup-id="">
                                                            {{ $warmupdetail->workouts->categoryOption->category_name }} -
                                                            {{ $warmupdetail->workouts->workout }}</td>

                                                        {{-- Workout --}}
                                                        {{-- <td class="py-4 workouts">{{ $warmupdetail->workouts->workout }}</td> --}}
                                                        {{-- Weight --}}
                                                        <td class="py-4 text-center">{{ $warmupdetail->weight }}</td>
                                                        {{-- Reps --}}
                                                        <td class="py-4">
                                                            <div class="flex items-center justify-center space-x-4">
                                                                <button class="px-2 py-1 text-white"
                                                                    onclick="decrementValue(this)">-</button>
                                                                <span
                                                                    class="w-16 bg-white text-black text-center rounded border-none p-1 reps">{{ $warmupdetail->reps }}</span>
                                                                <button class="px-2 py-1 text-white"
                                                                    onclick="incrementValue(this)">+</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>

                            {{-- Strength --}}
                            <div id="section-strength" class="category-section hidden">
                                <div class="w-full p-8 bg-black text-xs bg-opacity-50 rounded-lg mb-6">
                                    {{-- Actual Strength Content --}}
                                    <div class="content p-4 text-white">

                                        @if ($detailsstrength->isEmpty())
                                            <p class="text-center text-white">No assigned strength workouts.</p>
                                        @else
                                            @foreach ($detailsstrength as $strengthIndex => $strengthdetail)
                                                <div class="content p-4 mt-3 text-white border border-gray-600 rounded-3xl" id="ringdiv">
                                                    <div class="flex flex-col justify-center items-center gap-2.5 pt-5">
                                                        <button class="px-4 py-2 rounded primary-btn bg-white text-black"
                                                            data-target="#primary-weight-{{ $strengthIndex }}">
                                                            {{ $strengthdetail->workout?->categoryOption?->category_name ?? 'No Category' }}
                                                            -
                                                            {{ $strengthdetail->workout?->workout ?? 'Unnamed Workout' }}
                                                        </button>
                                                    </div>

                                                    {{-- Primary Table --}}
                                                    <div id="primary-weight-{{ $strengthIndex }}"
                                                        class="table-body primary-body">
                                                        @php
                                                            // Get max number of sets from all related setdetails
                                                            $maxSetDetail = $strengthdetail->sets
                                                                ->sortByDesc('sets')
                                                                ->first();
                                                            $numberOfSets = $maxSetDetail->sets ?? 0;
                                                            $baseWeight = $strengthdetail->weight ?? 0;
                                                            $totalPercentageIncrease = 0.7;
                                                            $percentageIncreasePerSet =
                                                                $numberOfSets > 1
                                                                    ? $totalPercentageIncrease / ($numberOfSets - 1)
                                                                    : 0;
                                                            $percentages = array_map(
                                                                fn($i) => 0.3 + $percentageIncreasePerSet * $i,
                                                                range(0, $numberOfSets - 1),
                                                            );

                                                            // Build reps list per set index
                                                            $repsList = [];
                                                            $setIndex = 0;
                                                            foreach ($strengthdetail->sets as $setDetail) {
                                                                for ($i = 0; $i < $setDetail->sets; $i++) {
                                                                    $repsList[$setIndex] = $setDetail->reps;
                                                                    $setIndex++;
                                                                }
                                                            }
                                                        @endphp

                                                        @if ($numberOfSets > 0)
                                                            <table class="w-full text-white tablee my-4">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="px-2">Set</th>
                                                                        <th class="px-2">Weight</th>
                                                                        <th class="px-2">Rep</th>
                                                                        <th></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @for ($setNumber = 1; $setNumber <= $numberOfSets; $setNumber++)
                                                                        @php
                                                                            $setPercentage =
                                                                                $percentages[$setNumber - 1] ?? 0.3;
                                                                            $calculatedWeight =
                                                                                $baseWeight * $setPercentage;
                                                                            $reps = $repsList[$setNumber - 1] ?? '-';
                                                                        @endphp
                                                                        <tr class="border-b border-gray-300">
                                                                            <td class="py-4">{{ $setNumber }}</td>
                                                                            <td id="weight-{{ $strengthIndex }}-{{ $setNumber }}"
                                                                                class="py-4 text-center">
                                                                                {{ number_format($calculatedWeight, 2) }}
                                                                            </td>
                                                                            <td class="py-4">
                                                                                <div
                                                                                    class="flex items-center justify-center space-x-4">
                                                                                    <button class="px-2 py-1 text-white"
                                                                                        onclick="decrementValue(this)">-</button>
                                                                                    <span
                                                                                        class="w-16 h-7 bg-white text-black text-center rounded border-none p-1"
                                                                                        id="repsValue{{ $strengthIndex }}-{{ $setNumber }}">
                                                                                        {{ $reps }}
                                                                                    </span>
                                                                                    <button class="px-2 py-1 text-white"
                                                                                        onclick="incrementValue(this)">+</button>
                                                                                </div>
                                                                            </td>
                                                                            <td class="py-4">
                                                                                <label
                                                                                    class="inline-flex items-center cursor-pointer">
                                                                                    <input type="checkbox"
                                                                                        id="toggleTimerStrength{{ $strengthIndex }}-{{ $setNumber }}"
                                                                                        class="sr-only peer timer-checkbox"
                                                                                        data-rest-time="{{ $strengthdetail->rest }}"
                                                                                        data-restred="{{ $strengthdetail->restred }}"
                                                                                        data-restyellow="{{ $strengthdetail->restyellow }}"
                                                                                        data-restgreen="{{ $strengthdetail->restgreen }}"
                                                                                        data-strength-detail-id="{{ $strengthdetail->id }}"
                                                                                        data-type="Primary"
                                                                                        data-weight-id="weight-{{ $strengthIndex }}-{{ $setNumber }}"
                                                                                        onclick="saveStrengthWorkout(this)">
                                                                                    <div id="toggleBackground{{ $strengthIndex }}-{{ $setNumber }}"
                                                                                        class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all">
                                                                                    </div>
                                                                                </label>
                                                                            </td>
                                                                        </tr>
                                                                    @endfor
                                                                </tbody>
                                                            </table>
                                                        @else
                                                            <p class="text-center text-sm text-white mt-4">No sets assigned for
                                                                this workout.</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                    </div>
                                </div>
                            </div>



                            {{-- Weightlifting --}}
                            <div id="section-weightlifting" class="category-section hidden">
                                <div class="w-full p-8 bg-black text-xs bg-opacity-50 rounded-lg mb-6">
                                    {{-- Dynamic Weightlifting Content --}}
                                    <div class="content pt-4 text-white">

                                        @if ($detailsweight->isEmpty())
                                            <p class="text-center text-white">No assigned weightlifting workouts.</p>
                                        @else
                                            @foreach ($detailsweight as $weightIndex => $weightdetail)
                                                <div class="flex flex-col justify-center items-center gap-2.5 pt-5">
                                                    <button class="px-4 py-2 rounded primary-btn bg-white text-black"
                                                        data-target="#primary-weight-{{ $weightIndex }}">
                                                        {{ $weightdetail->workouts?->categoryOption?->category_name ?? 'No Category' }}
                                                        -
                                                        {{ $weightdetail->workouts?->workout ?? 'Unnamed Workout' }}
                                                    </button>
                                                </div>

                                                {{-- Primary Table --}}
                                                <div id="primary-weight-{{ $weightIndex }}"
                                                    class="table-body primary-body">
                                                    @php
                                                        $baseWeight = $weightdetail->weight ?? 0;
                                                        $totalPercentageIncrease = 0.7;

                                                        // Get max sets
                                                        $maxSetDetail = $weightdetail->sets
                                                            ->sortByDesc('sets')
                                                            ->first();
                                                        $numberOfSets = $maxSetDetail->sets ?? 0;

                                                        // Weight increments
                                                        $percentageIncreasePerSet =
                                                            $numberOfSets > 1
                                                                ? $totalPercentageIncrease / ($numberOfSets - 1)
                                                                : 0;
                                                        $percentages = array_map(
                                                            fn($i) => 0.3 + $percentageIncreasePerSet * $i,
                                                            range(0, $numberOfSets - 1),
                                                        );

                                                        // Reps per set
                                                        $repsList = [];
                                                        $setIndex = 0;
                                                        foreach ($weightdetail->sets as $setDetail) {
                                                            for ($i = 0; $i < $setDetail->sets; $i++) {
                                                                $repsList[$setIndex] = $setDetail->reps;
                                                                $setIndex++;
                                                            }
                                                        }
                                                    @endphp

                                                    @if ($numberOfSets > 0)
                                                        <table class="w-full text-white tablee my-4">
                                                            <thead>
                                                                <tr>
                                                                    <th class="px-2">Set</th>
                                                                    <th class="px-2">Weight</th>
                                                                    <th class="px-2">Rep</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @for ($setNumber = 1; $setNumber <= $numberOfSets; $setNumber++)
                                                                    @php
                                                                        $setPercentage =
                                                                            $percentages[$setNumber - 1] ?? 0.3;
                                                                        $calculatedWeight =
                                                                            $baseWeight * $setPercentage;
                                                                        $reps = $repsList[$setNumber - 1] ?? '-';
                                                                    @endphp
                                                                    <tr class="border-b border-gray-300">
                                                                        <td class="py-4">{{ $setNumber }}</td>
                                                                        <td class="py-4 text-center">
                                                                            {{ number_format($calculatedWeight, 2) }}
                                                                        </td>
                                                                        <td class="py-4">
                                                                            <div
                                                                                class="flex items-center justify-center space-x-4">
                                                                                <button class="px-2 py-1 text-white"
                                                                                    onclick="decrementValue(this)">-</button>
                                                                                <span
                                                                                    class="w-16 h-7 bg-white text-black text-center rounded border-none p-1">
                                                                                    {{ $reps }}
                                                                                </span>
                                                                                <button class="px-2 py-1 text-white"
                                                                                    onclick="incrementValue(this)">+</button>
                                                                            </div>
                                                                        </td>
                                                                        <td class="py-4">
                                                                            <label
                                                                                class="inline-flex items-center cursor-pointer">
                                                                                <input type="checkbox"
                                                                                    id="toggleTimerWeight{{ $weightIndex }}-{{ $setNumber }}"
                                                                                    class="sr-only peer timer-checkbox"
                                                                                    data-rest-time="{{ $weightdetail->rest ?? 0 }}"
                                                                                    data-restred="{{ $weightdetail->restredwe }}"
                                                                                    data-restyellow="{{ $weightdetail->restyellowwe }}"
                                                                                    data-restgreen="{{ $weightdetail->restgreenwe }}"
                                                                                    data-strength-detail-id="{{ $weightdetail->id }}">
                                                                                <div id="toggleBackground{{ $weightIndex }}-{{ $setNumber }}"
                                                                                    class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all">
                                                                                </div>
                                                                            </label>
                                                                        </td>
                                                                    </tr>
                                                                @endfor
                                                            </tbody>
                                                        </table>
                                                    @else
                                                        <p class="text-center text-sm text-white mt-4">No sets assigned for
                                                            this workout.</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif

                                    </div>
                                </div>
                            </div>




                            {{-- Conditioning --}}
                            <div id="section-conditioning" class="category-section hidden">
                                <div>
                                    <div>
                                        <div class="justify-center items-center text-white text-center h-full pt-20">
                                            <div class="text-xl mb-5">Conditioning</div>
                                            @if (isset($detailsconditioning[0]->amrap) && $detailsconditioning[0]->amrap == 1)
                                                <div class="text-base mb-5">AMRAP</div>
                                            @else
                                                <div class="text-base mb-5">Rounds</div>
                                            @endif
                                            @if (isset($detailsconditioning[0]->workout->categoryOption->category_name))
                                                <div class="text-base mb-12">
                                                    {{ $detailsconditioning[0]->workout->categoryOption->category_name }}
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
                                                                <td class="py-4">
                                                                    {{ $conditioningdetail->workout->workout }}</td>
                                                                <td class="py-4 workouts">{{ $conditioningdetail->reps }}
                                                                </td>
                                                                <td class="py-4">
                                                                    <input type="text"
                                                                        value="{{ $conditioningdetail->weight }}"
                                                                        class="w-24 h-8 text-center text-3xl text-black">
                                                                </td>
                                                                <td id="timeToComplete-{{ $index }}"
                                                                    class="py-4 hidden">
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
                                                <input id="roundsInput"
                                                    value="{{ isset($conditioningdetail->rounds) ? $conditioningdetail->rounds : '' }}"
                                                    min="1" class="w-24 h-20 text-center text-3xl text-black">

                                                <button id="increaseRounds"
                                                    class="bg-transparent border border-white text-3xl text-white w-16 h-20 cursor-pointer"
                                                    onclick="incrementValue(this)">+</button>
                                            </div>
                                            <div class="mb-0">
                                                <div class="text-base mb-2">TIME TO COMPLETE</div>
                                                <div id="timer" class="text-5xl text-black bg-white p-2">00:00:00
                                                </div>
                                                {{-- <div class="flex w-full">
                                                        <button id="startButton" class="w-1/2 h-16 bg-green-600 text-white cursor-pointer ">START</button>
                                                        <button id="stopButton" class="w-1/2 h-16 bg-red-600 text-white cursor-pointer">STOP</button>
                                                    </div>   --}}
                                            </div>
                                        </div>
                                        <div id="popup"
                                            class="fixed -inset-96 bg-black bg-opacity-70 flex justify-center items-center z-50 cursor-pointer">
                                            <div class="bg-white rounded-full w-48 h-48 flex justify-center items-center text-2xl text-black text-center"
                                                onclick="startCountdown()">
                                                Ready?
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>


                            {{-- Test --}}
                            <div id="section-test" class="category-section hidden">
                                <div class="w-full p-8 bg-black text-xs bg-opacity-50 rounded-lg mb-6">
                                    @if ($detailstest->isEmpty())
                                        <p class="text-center text-white">No test records assigned.</p>
                                    @else
                                        @foreach ($detailstest as $testIndex => $testdetail)
                                            <div class="flex flex-col justify-center items-center gap-2.5 pt-5">
                                                <button class="px-4 py-2 rounded primary-btn bg-white text-black"
                                                    data-target="#primary-weight-{{ $testIndex }}">
                                                    {{ $testdetail->workouts?->categoryOption?->category_name ?? 'No Category' }}
                                                    -
                                                    {{ $testdetail->workouts?->workout ?? 'Unnamed Workout' }}
                                                </button>
                                            </div>
                                            <div class="flex flex-col items-center justify-center gap-2 pt-4">
                                                <p class="text-center text-white font-bold text-lg">1 REP MAX</p>

                                                <div class="flex items-center gap-2">
                                                    <input type="number"
                                                        class="w-32 md:w-48 px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-black text-center text-lg"
                                                        placeholder="Enter weight" />
                                                    <span class="text-white text-sm">kg</span>
                                                </div>

                                                <button
                                                    class="mt-3 px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-all">
                                                    Save
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>


                            <button id="resetButton" class="px-4 rounded w-full">REST TIMER</button>

                            <div id="timerest"
                                class="timer text-center w-full bg-orange-600 p-4 rounded-lg text-2xl mb-8 mt-5">
                                00:00:00
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const sets = document.querySelectorAll('.set');

                    sets.forEach(set => {
                        const plusButton = set.querySelector('.plus');
                        const minusButton = set.querySelector('.minus');
                        const input = set.querySelector('.quantity input');

                        plusButton.addEventListener('click', () => {
                            input.value = parseInt(input.value) + 1;
                        });

                        minusButton.addEventListener('click', () => {
                            if (parseInt(input.value) > 0) {
                                input.value = parseInt(input.value) - 1;
                            }
                        });
                    });

                    const resetButton = document.querySelector('.reset-timer');
                    resetButton.addEventListener('click', () => {
                        // Timer reset logic goes here
                        console.log('Timer reset');
                    });
                });
            </script>

            {{-- increase decrease quntity function --}}
            <script>
                function incrementValue(element) {
                    var span = element.parentNode.querySelector('span');
                    var value = parseInt(span.textContent, 10);
                    value = isNaN(value) ? 0 : value;
                    value++;
                    span.textContent = value;
                }

                function decrementValue(element) {
                    var span = element.parentNode.querySelector('span');
                    var value = parseInt(span.textContent, 10);
                    value = isNaN(value) ? 0 : value;
                    value = value <= 0 ? 0 : value - 1;
                    span.textContent = value;
                }
            </script>
            {{-- end increase decrease quntity function --}}


            {{-- drop down script --}}
            <script>
                document.querySelectorAll('.toggle-icon').forEach(icon => {
                    icon.addEventListener('click', function() {
                        // Determine the element to toggle
                        let toggleTarget;

                        // Check if the next sibling is a table or look for the '.content' div
                        const parentDiv = this.closest('div');
                        const table = parentDiv.nextElementSibling; // For Warmup section
                        const content = parentDiv.parentElement.querySelector(
                            '.content'); // For Strength and Weightlifting

                        if (table && table.tagName === 'TABLE') {
                            // If it's the Warmup section
                            toggleTarget = table;
                        } else if (content) {
                            // If it's Strength or Weightlifting
                            toggleTarget = content;
                        }

                        // Toggle the visibility
                        if (toggleTarget) {
                            if (toggleTarget.classList.contains('hidden')) {
                                toggleTarget.classList.remove('hidden');
                                this.classList.remove('fa-chevron-down');
                                this.classList.add('fa-minus');
                            } else {
                                toggleTarget.classList.add('hidden');
                                this.classList.remove('fa-minus');
                                this.classList.add('fa-chevron-down');
                            }
                        }
                    });
                });
            </script>
            {{-- end drop down script --}}


            {{-- strenght save and colur change --}}
            <script>
                let timerIntervals = {};
                let elapsedTimes = {};
                const timerThresholds = {};

                function startTimer(setNumber, red, yellow, green) {
                    const redSec = parseTimeToSeconds(red);
                    const yellowSec = parseTimeToSeconds(yellow);
                    const greenSec = parseTimeToSeconds(green);

                    timerThresholds[setNumber] = {
                        red: redSec,
                        yellow: yellowSec,
                        green: greenSec
                    };
                    elapsedTimes[setNumber] = 0;

                    runStageTimer(setNumber, 'red');
                }

                function runStageTimer(setNumber, stage) {
                    const {
                        red,
                        yellow,
                        green
                    } = timerThresholds[setNumber];
                    let stageLimit = 0;

                    // Set stage color
                    const timerElement = document.getElementById(`timerest`);
                    const ringElement = document.getElementById(`ringdiv`);
                    const toggleBackground = document.getElementById(`toggleBackground${setNumber}`);

                    // Clean up previous styles
                    if (timerElement && toggleBackground) {
                        timerElement.classList.remove('bg-orange-600', 'bg-red-600', 'bg-yellow-600', 'bg-green-600');
                        ringElement.classList.remove('border-orange-600', 'border-red-600', 'border-yellow-600',
                            'border-green-600');
                        toggleBackground.classList.remove('bg-orange-600', 'bg-red-600', 'bg-yellow-600', 'bg-green-600');
                    }

                    console.log('changeTimerColor', timerElement, ringElement, toggleBackground);
                    // Determine current stage limits and color
                    switch (stage) {
                        case 'red':
                            stageLimit = red;
                            timerElement?.classList.add('bg-red-600');
                            ringElement?.classList.add('border-red-600');
                            toggleBackground?.classList.add('bg-red-600');
                            break;
                        case 'yellow':
                            stageLimit = yellow;
                            timerElement?.classList.add('bg-yellow-600');
                            ringElement?.classList.add('border-yellow-600');
                            toggleBackground?.classList.add('bg-yellow-600');
                            break;
                        case 'green':
                            stageLimit = green;
                            timerElement?.classList.add('bg-green-600');
                            ringElement?.classList.add('border-green-600');
                            toggleBackground?.classList.add('bg-green-600');
                            break;
                    }

                    let remainingTime = stageLimit;

                    // Initial display
                    updateTimerDisplay(setNumber, remainingTime);

                    timerIntervals[setNumber] = setInterval(() => {
                        remainingTime--;

                        updateTimerDisplay(setNumber, remainingTime);

                        if (remainingTime <= 0) {
                            clearInterval(timerIntervals[setNumber]);

                            if (stage === 'red') {
                                runStageTimer(setNumber, 'yellow');
                            } else if (stage === 'yellow') {
                                runStageTimer(setNumber, 'green');
                            } else if (stage === 'green') {
                                beep(3);
                                activeTimerSetNumber = null;
                            }
                        }
                    }, 1000);
                }



                function parseTimeToSeconds(timeStr) {
                    if (!timeStr) return 0;

                    const parts = timeStr.split(':').map(Number);
                    if (parts.length === 3) {
                        const [hours, minutes, seconds] = parts;
                        return hours * 3600 + minutes * 60 + seconds;
                    } else if (parts.length === 2) {
                        const [minutes, seconds] = parts;
                        return minutes * 60 + seconds;
                    } else if (parts.length === 1) {
                        return Number(parts[0]);
                    }

                    return 0;
                }

                function stopTimer(setNumber) {
                    clearInterval(timerIntervals[setNumber]);
                }

                function resetTimer(setNumber) {
                    clearInterval(timerIntervals[setNumber]);
                    elapsedTimes[setNumber] = 0;
                    updateTimerDisplay(setNumber, elapsedTimes[setNumber]);

                    const ringElement = document.getElementById(`ringdiv`);
                    if (ringElement) {
                        ringElement.classList.remove('border-red-600', 'border-yellow-600', 'border-green-600');
                        ringElement.classList.add('border-orange-600');
                    }

                    const timerElement = document.getElementById(`timerest`);
                    if (timerElement) {
                        timerElement.classList.remove('bg-red-600', 'bg-yellow-600', 'bg-green-600');
                        timerElement.classList.add('bg-orange-600');
                    }

                    const toggleBackground = document.getElementById(`toggleBackground${setNumber}`);
                    if (toggleBackground) {
                        toggleBackground.classList.remove('bg-red-600', 'bg-yellow-600', 'bg-green-600');
                        toggleBackground.classList.add('bg-gray-600');
                    }
                }

                function updateTimerDisplay(setNumber, secondsRemaining) {
                    const minutes = Math.floor(secondsRemaining / 60);
                    const seconds = secondsRemaining % 60;
                    const displayMinutes = minutes < 10 ? '0' + minutes : minutes;
                    const displaySeconds = seconds < 10 ? '0' + seconds : seconds;

                    const timerElement = document.getElementById(`timerest`);
                    if (timerElement) {
                        timerElement.textContent = `00:${displayMinutes}:${displaySeconds}`;
                    }
                }

                function changeTimerColor(setNumber, seconds) {
                    const timerElement = document.getElementById(`timerest`);
                    const ringElement = document.getElementById(`ringdiv`);
                    const toggleBackground = document.getElementById(`toggleBackground${setNumber}`);


                    console.log('changeTimerColor', timerElement, ringElement);

                    if (!timerThresholds[setNumber]) return;

                    const {
                        red,
                        yellow,
                        green
                    } = timerThresholds[setNumber];

                    if (timerElement && toggleBackground) {
                        timerElement.classList.remove('bg-orange-600', 'bg-red-600', 'bg-yellow-600', 'bg-green-600');
                        ringElement.classList.remove('border-orange-600', 'border-red-600', 'border-yellow-600',
                            'border-green-600');
                        toggleBackground.classList.remove('bg-orange-600', 'bg-red-600', 'bg-yellow-600', 'bg-green-600');
                        //removeBgClasses(toggleBackground);

                        if (seconds <= red) {
                            timerElement.classList.add('bg-red-600');
                            ringElement.classList.add('border-red-600');
                            toggleBackground.classList.add('bg-red-600');
                        } else if (seconds <= yellow) {
                            timerElement.classList.add('bg-yellow-600');
                            ringElement.classList.add('border-yellow-600');
                            toggleBackground.classList.add('bg-yellow-600');
                        } else if (seconds <= green) {
                            timerElement.classList.add('bg-green-600');
                            ringElement.classList.add('border-green-600');
                            toggleBackground.classList.add('bg-green-600');
                        }
                    }
                }

                function beep(times) {
                    for (let i = 0; i < times; i++) {
                        setTimeout(() => {
                            const audio = new Audio('/audio/beep.mp3');
                            audio.play();
                        }, i * 1000);
                    }
                }
                let activeTimerSetNumber = null;
                document.querySelectorAll('.tablee').forEach((table) => {
                    const checkboxes = table.querySelectorAll('.timer-checkbox');
                    if (checkboxes.length > 0) {
                        checkboxes.forEach((checkbox) => {
                            checkbox.addEventListener('change', (event) => {
                                const id = checkbox
                                    .id; // e.g. toggleTimerStrength0-1 or toggleTimerWeight0-1
                                const typeMatch = id.match(/^toggleTimer(Strength|Weight)/);
                                const type = typeMatch ? typeMatch[1] : 'Unknown';
                                const setNumber = id.replace(`toggleTimer${type}`, '');

                                if (event.target.checked) {
                                    if (activeTimerSetNumber !== null && activeTimerSetNumber !==
                                        setNumber) {
                                        alert(
                                            "One rest is already on. Please stop it before starting a new one."
                                        );
                                        checkbox.checked = false;
                                        return;
                                    }

                                    const red = checkbox.dataset.restred;
                                    const yellow = checkbox.dataset.restyellow;
                                    const green = checkbox.dataset.restgreen;

                                    activeTimerSetNumber = setNumber;
                                    startTimer(setNumber, red, yellow, green);
                                } else {
                                    stopTimer(setNumber);
                                    resetTimer(setNumber);

                                    if (activeTimerSetNumber === setNumber) {
                                        activeTimerSetNumber = null;
                                    }
                                }

                                // Optional branching if you need different saving logic
                                if (type === 'Strength') {
                                    saveStrengthWorkout(checkbox);
                                } else if (type === 'Weight') {
                                    saveWeightWorkout(checkbox);
                                }
                            });
                        });
                    }
                });

                function removeBgClasses(element) {
                    [...element.classList].forEach(cls => {
                        if (cls.startsWith('bg-')) {
                            element.classList.remove(cls);
                        }
                    });
                }

                // Function to be called when a checkbox is clicked
                function saveStrengthWorkout(checkbox) {
                    if (checkbox.dataset.processed) {
                        console.log('Already processed, skipping...');
                        return;
                    }

                    checkbox.dataset.processed = true; // Mark as processed to prevent re-entry

                    // Check if the checkbox is checked
                    if (checkbox.checked) {
                        // Find the closest row
                        const row = checkbox.closest('tr');
                        if (!row) {
                            console.error('Row not found');
                            return;
                        }

                        // Get the set number
                        const setNumberElement = row.querySelector('td:first-child');
                        if (!setNumberElement) {
                            console.error('Set number element not found');
                            return;
                        }
                        const setNumber = setNumberElement.textContent.trim(); // Assuming set number is in the first <td>

                        // Get the type from the checkbox data attribute
                        const type = checkbox.getAttribute('data-type');

                        // Extract the strengthIndex from the parent container of the checkbox
                        const strengthIndex = checkbox.closest('.table-body').id.match(/-(\d+)/)[1];

                        // Get the reps value based on type
                        const repsElement = type === 'Primary' ?
                            row.querySelector(`#repsValue${strengthIndex}-${setNumber}`) :
                            row.querySelector(`#alt-repsValue${strengthIndex}-${setNumber}`);
                        const reps = repsElement ? repsElement.textContent.trim() : null;

                        // Get the weight or alternative weight value based on type
                        const weightId = type === 'Primary' ? `weight-${strengthIndex}-${setNumber}` :
                            `alweight-${strengthIndex}-${setNumber}`;
                        const weightElement = document.getElementById(weightId);
                        const weight = weightElement ? weightElement.textContent.trim() : null;

                        // Get the strength ID from the checkbox's data attribute
                        const strengthId = checkbox.getAttribute('data-strength-detail-id');

                        // Get the rest time from the checkbox data attribute
                        const restTime = checkbox.getAttribute('data-rest-time');

                        if (!strengthId) {
                            console.error('Strength ID not found');
                            return;
                        }

                        // Make the AJAX request
                        $.ajax({
                            url: "/save-strength-workout",
                            type: "POST",
                            data: {
                                strength_id: strengthId,
                                reps: reps,
                                weight: weight,
                                type: type, // Ensure type is included
                                _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                            },
                            success: function(response) {
                                console.log('Success:', response.message);
                            },
                            error: function(xhr, status, error) {
                                console.error('Error:', error);
                            },
                            complete: function() {
                                // Reset processed state after request completes to allow future processing
                                checkbox.dataset.processed = false;
                            }
                        });
                    }
                }
            </script>


            {{-- end strenght save and colur change --}}

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.primary-btn').forEach(function(button) {
                        button.addEventListener('click', function() {
                            const targetId = button.getAttribute('data-target');
                            const primaryBody = document.querySelector(targetId); // Primary table body
                            const altBody = document.querySelector(targetId.replace('#primary-',
                                '#alt-')); // Alternate table body

                            if (primaryBody && altBody) {
                                primaryBody.classList.remove('hidden');
                                primaryBody.classList.add('block');
                                altBody.classList.add('hidden');
                                altBody.classList.remove('block');
                            } else {
                                console.error(`Element with ID ${targetId} or its alternate not found.`);
                            }
                        });
                    });

                    document.querySelectorAll('.alt-btn').forEach(function(button) {
                        button.addEventListener('click', function() {
                            const targetId = button.getAttribute('data-target');
                            const altBody = document.querySelector(targetId); // Alternate table body
                            const primaryBody = document.querySelector(targetId.replace('#alt-',
                                '#primary-')); // Primary table body

                            if (primaryBody && altBody) {
                                altBody.classList.remove('hidden');
                                altBody.classList.add('block');
                                primaryBody.classList.add('hidden');
                                primaryBody.classList.remove('block');
                            } else {
                                console.error(`Element with ID ${targetId} or its primary not found.`);
                            }
                        });
                    });
                });




                function toggleContent(icon) {
                    const content = icon.closest('.w-full').querySelector('.content');
                    content.classList.toggle('hidden');
                    if (content.classList.contains('hidden')) {
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    } else {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                    }
                }
            </script>


            {{-- warm up  save --}}
            <script>
                //save warm-up after toching
                function savewarmup(element) {
                    var workoutId = element.getAttribute('data-workout-id');
                    var dailyWarmupId = element.getAttribute('data-daily-warmup-id');
                    var reps = $(element).closest('tr').find('.reps').text().trim();
                    console.log('Workout ID:', workoutId);
                    console.log('Reps:', reps);

                    $.ajax({
                        url: dailyWarmupId ? "/warmup-daily/" + dailyWarmupId : "/warmup-daily",
                        type: dailyWarmupId ? "PUT" : "POST",
                        data: {
                            warmup_id: workoutId,
                            reps: reps,
                            _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                        },
                        success: function(response) {
                            console.log(response);

                            //set saved daily warmup-id to warmup workout
                            if (response.daily_warmup_id) {
                                element.setAttribute('data-daily-warmup-id', response.daily_warmup_id);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error); // Handle error
                        }
                    });
                }
            </script>
            {{-- end warumup save --}}

            <script>
                document.querySelectorAll('.category-tab').forEach(button => {
                    button.addEventListener('click', () => {
                        const targetId = button.dataset.target;

                        // Hide all sections
                        document.querySelectorAll('.category-section').forEach(section =>
                            section.classList.add('hidden')
                        );
                        document.querySelector(targetId).classList.remove('hidden');

                        // Reset all tabs
                        document.querySelectorAll('.category-tab').forEach(tab => {
                            tab.classList.remove('bg-white', 'text-black');
                            tab.classList.add('bg-black', 'bg-opacity-50', 'text-white');

                            const icon = tab.querySelector('.icon-img');
                            if (icon && tab.dataset.iconWhite) {
                                icon.src = tab.dataset.iconWhite;
                            }
                        });

                        // Set active styles
                        button.classList.remove('bg-black', 'bg-opacity-50', 'text-white');
                        button.classList.add('bg-white', 'text-black');

                        const icon = button.querySelector('.icon-img');
                        if (icon && button.dataset.iconBlack) {
                            icon.src = button.dataset.iconBlack;
                        }
                    });
                });

                function incrementValue(btn) {
                    const span = btn.parentElement.querySelector('.reps');
                    span.innerText = parseInt(span.innerText) + 1;
                }

                function decrementValue(btn) {
                    const span = btn.parentElement.querySelector('.reps');
                    span.innerText = Math.max(0, parseInt(span.innerText) - 1);
                }
            </script>
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

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    @extends('mobile.layout.mobile-layout')
    @section('content')
        <div class="w-full flex flex-col justify-between min-h-screen h-full">
            <div class="flex-grow w-full flex items-center justify-center m-0 p-4 bg-cover bg-center bg-no-repeat"
                style="background-image: url('{{ asset('img/valhalla-bg.jpg') }}');">
                <div class="flex w-full flex-col justify-center items-center gap-2.5 pt-20 text-white">
                    <div class=" flex justify-between items-center mb-6">
                        <button class=" px-4 py-2 rounded">DEADLIFT</button>
                        <button class=" px-4 py-2 border rounded">ALT</button>
                    </div>
                     {{-- warmup  --}}
                     
                     <div class="relative w-full max-w-sm  px-10 bg-black bg-opacity-50 p-4 rounded-lg text-white">
                        <h3 class="text-md mb-4">Set</h3>
                        <div class="absolute -rotate-90 transform -translate-y-1/2 top-1/2 text-lg left-[-1rem] font-bold">Warmup</div>
                        <div class="set-item flex items-center mb-1 justify-between">
                            <span>1.</span>
                            <span>Bar Only 10 Reps</span>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                            </label>
                        </div>
                        <div class="set-item flex  items-center mb-1 justify-between">
                            <span>2.</span>
                            <span>80kg 3 Reps</span>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                            </label>
                        </div>
                        <div class="set-item flex  items-center justify-between">
                            <span>3.</span>
                            <span>100kg 3 Reps</span>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                            </label>
                        </div>
                    </div>
                    {{-- end warmup --}}

                    {{-- table --}}
                    <div class=" w-full bg-black bg-opacity-50 p-4 rounded-lg mb-6">
                        <table class="w-full text-white">
                            <thead class=" justify-between ">
                                <tr>
                                    <th class="py-2">Set</th>

                                    <th class="px-2 ">Reps</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-300">
                                    <td class="py-4">1.</td>
                                    <td class="py-4">150kg</td>
                                    <td class="py-4">
                                        <div class="flex items-center justify-center space-x-4">
                                            <!-- Increased space-x to 4 -->
                                            <button class="px-2 py-1 text-white" onclick="decrementValue(this)">-</button>
                                            <span
                                                class="w-16 bg-white text-black text-center rounded border-none p-1">5</span>
                                            <button class="px-2 py-1 text-white" onclick="incrementValue(this)">+</button>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <label class="inline-flex items-center me-5 cursor-pointer">
                                            <input type="checkbox" value="" class="sr-only peer" checked>
                                            <div
                                                class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
                                            </div>
                                        </label>
                                    </td>
                                </tr>


                                <tr class="border-b border-gray-300">
                                    <td class="py-4">1.</td>
                                    <td class="py-4">150kg</td>
                                    <td class="py-4">
                                        <div class="flex items-center justify-center space-x-4">
                                            <!-- Increased space-x to 4 -->
                                            <button class="px-2 py-1 text-white" onclick="decrementValue(this)">-</button>
                                            <span
                                                class="w-16 bg-white text-black text-center rounded border-none p-1">5</span>
                                            <button class="px-2 py-1 text-white" onclick="incrementValue(this)">+</button>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <label class="inline-flex items-center me-5 cursor-pointer">
                                            <input type="checkbox" value="" class="sr-only peer" checked>
                                            <div
                                                class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
                                            </div>
                                        </label>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-300">
                                    <td class="py-4">1.</td>
                                    <td class="py-4">150kg</td>
                                    <td class="py-4">
                                        <div class="flex items-center justify-center space-x-4">
                                            <!-- Increased space-x to 4 -->
                                            <button class="px-2 py-1 text-white" onclick="decrementValue(this)">-</button>
                                            <span
                                                class="w-16 bg-white text-black text-center rounded border-none p-1">5</span>
                                            <button class="px-2 py-1 text-white" onclick="incrementValue(this)">+</button>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <label class="inline-flex items-center me-5 cursor-pointer">
                                            <input type="checkbox" value="" class="sr-only peer" checked>
                                            <div
                                                class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
                                            </div>
                                        </label>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-300">
                                    <td class="py-4">1.</td>
                                    <td class="py-4">150kg</td>
                                    <td class="py-4">
                                        <div class="flex items-center justify-center space-x-4">
                                            <!-- Increased space-x to 4 -->
                                            <button class="px-2 py-1 text-white" onclick="decrementValue(this)">-</button>
                                            <span
                                                class="w-16 bg-white text-black text-center rounded border-none p-1">5</span>
                                            <button class="px-2 py-1 text-white" onclick="incrementValue(this)">+</button>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <label class="inline-flex items-center me-5 cursor-pointer">
                                            <input type="checkbox" value="" class="sr-only peer" checked>
                                            <div
                                                class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
                                            </div>
                                        </label>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-300">
                                    <td class="py-4">1.</td>
                                    <td class="py-4">150kg</td>
                                    <td class="py-4">
                                        <div class="flex items-center justify-center space-x-4">
                                            <!-- Increased space-x to 4 -->
                                            <button class="px-2 py-1 text-white" onclick="decrementValue(this)">-</button>
                                            <span
                                                class="w-16 bg-white text-black text-center rounded border-none p-1">5</span>
                                            <button class="px-2 py-1 text-white" onclick="incrementValue(this)">+</button>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <label class="inline-flex items-center me-5 cursor-pointer">
                                            <input type="checkbox" value="" class="sr-only peer" checked>
                                            <div
                                                class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
                                            </div>
                                        </label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- end table --}}

                    <div class="notes mb-6 w-full">
                        <textarea placeholder="Notes" class="w-full h-20 bg-white rounded-lg p-2 border-none"></textarea>
                    </div>

                    <button class=" px-4 py-2 rounded w-full mb-6">RESET TIMER</button>

                    <div class="timer text-center w-full bg-orange-600 p-4 rounded-lg text-2xl mb-10">
                        00:00:00
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
    @endsection
</body>

</html>

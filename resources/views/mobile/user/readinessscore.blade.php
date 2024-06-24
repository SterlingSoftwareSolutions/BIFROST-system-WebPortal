<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UI Design</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class=" overflow-y-auto">
    @extends('mobile.layout.mobile-layout')
    @section('content')
        <div class="w-full  flex flex-col justify-between h-screen  overflow-y-auto">
            <div class="flex-grow items-center justify-center m-0 p-4 bg-cover bg-center  bg-no-repeat"
                style="background-image: url('{{ asset('img/valhalla-bg.jpg') }}');">
                <div class="bg-transparent p-6 rounded-lg shadow-md pt-24">
                    <div class="mb-4 flex flex-col text-center"> <!-- Change flex direction to column -->
                         <!-- Sleep -->
                        <div class="w-full mb-2 text-white text-center items-center justify-center flex">Hrs of Sleep</div>
                        <!-- Move this div here -->
                        <div class="flex flex-grow h-8 rounded overflow-hidden">
                            <button class="flex-1 bg-red-500 text-white text-center focus:outline-none">&lt;5</button>
                            <button class="flex-1 bg-orange-500 text-white text-center focus:outline-none">5-6</button>
                            <button class="flex-1 bg-yellow-500 text-white text-center focus:outline-none">6-7</button>
                            <button class="flex-1 bg-green-500 text-white text-center focus:outline-none">7-8</button>
                            <button class="flex-1 bg-green-700 text-white text-center focus:outline-none">8+</button>
                        </div>
                    </div>
                       <!-- Alertness -->
                    <div class="mb-4 flex flex-col"> <!-- Change flex direction to column -->
                        <div class="w-full mb-2 text-white text-center items-center justify-center">Alertness</div>
                        <!-- Move this div here -->
                        <div class="flex flex-grow h-8 rounded overflow-hidden">
                            <button class="flex-1 bg-red-500 text-white text-center focus:outline-none">&lt;5</button>
                            <button class="flex-1 bg-orange-500 text-white text-center focus:outline-none">5-6</button>
                            <button class="flex-1 bg-yellow-500 text-white text-center focus:outline-none">6-7</button>
                            <button class="flex-1 bg-green-500 text-white text-center focus:outline-none">7-8</button>
                            <button class="flex-1 bg-green-700 text-white text-center focus:outline-none">8+</button>
                        </div>
                        <div class="w-full mb-2 text-white text-xs text-center items-center justify-center">1= lights are on
                            but no one is home.</div> <!-- Move this div here -->
                    </div>
                     <!-- Excitement -->
                    <div class="mb-4 flex flex-col"> <!-- Change flex direction to column -->
                        <div class="w-full text-center items-center justify-center mb-2 text-white">Excitement</div>
                        <!-- Move this div here -->
                        <div class="flex flex-grow h-8 rounded overflow-hidden">
                            <button class="flex-1 bg-red-500 text-white text-center focus:outline-none">&lt;5</button>
                            <button class="flex-1 bg-orange-500 text-white text-center focus:outline-none">5-6</button>
                            <button class="flex-1 bg-yellow-500 text-white text-center focus:outline-none">6-7</button>
                            <button class="flex-1 bg-green-500 text-white text-center focus:outline-none">7-8</button>
                            <button class="flex-1 bg-green-700 text-white text-center focus:outline-none">8+</button>
                        </div>
                        <div class="w-full text-center items-center justify-center mb-2 text-white text-xs">1= Not
                            interested in weights today.</div> <!-- Move this div here -->
                    </div>
                    <!-- Stress -->
                    <div class="mb-4 flex flex-col"> <!-- Change flex direction to column -->
                        <div class="w-full text-center items-center justify-center mb-2 text-white">Stress</div>
                        <!-- Move this div here -->
                        <div class="flex flex-grow h-8 rounded overflow-hidden">
                            <button class="flex-1 bg-red-500 text-white text-center focus:outline-none">&lt;5</button>
                            <button class="flex-1 bg-orange-500 text-white text-center focus:outline-none">5-6</button>
                            <button class="flex-1 bg-yellow-500 text-white text-center focus:outline-none">6-7</button>
                            <button class="flex-1 bg-green-500 text-white text-center focus:outline-none">7-8</button>
                            <button class="flex-1 bg-green-700 text-white text-center focus:outline-none">8+</button>
                        </div>
                        <div class="w-full text-center items-center justify-center mb-2 text-white text-xs">1= Pulling my
                            hair out.</div> <!-- Move this div here -->
                    </div>
                    <div class="mb-4 flex flex-col"> <!-- Change flex direction to column -->
                        <div class="w-full text-center items-center justify-center mb-2 text-white">Soreness</div>
                        <!-- Move this div here -->
                        <div class="flex flex-grow h-8 rounded overflow-hidden">
                            <button class="flex-1 bg-red-500 text-white text-center focus:outline-none">&lt;5</button>
                            <button class="flex-1 bg-orange-500 text-white text-center focus:outline-none">5-6</button>
                            <button class="flex-1 bg-yellow-500 text-white text-center focus:outline-none">6-7</button>
                            <button class="flex-1 bg-green-500 text-white text-center focus:outline-none">7-8</button>
                            <button class="flex-1 bg-green-700 text-white text-center focus:outline-none">8+</button>
                        </div>
                        <div class="w-full text-center items-center justify-center mb-2 text-white text-xs">1= Crippled..
                        </div>
                    </div>
                    <div
                        class="flex w-full md:items-center md:justify-center flex-col items-center justify-center text-center font-bold mb-4 text-white ">
                        <div class="border border-white flex-col flex w-48 h-28  items-center justify-center rounded-lg "><span>SCORE</span>
                            <span class="text-3xl">75%</span>
                        </div>
                    </div>


                    <button class="block mx-auto py-2 px-4 b text-white rounded mb-4  focus:outline-none">SAVE</button>
                </div>
            </div>
        </div>
    @endsection
</body>

</html>

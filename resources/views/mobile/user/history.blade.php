<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <title>Document</title>
</head>

<body>
    @extends('mobile.layout.mobile-layout')

    @section('content')
        <div class="flex flex-col justify-between h-screen w-screen ">
            <div class="flex-grow items-center justify-center m-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('img/valhalla-bg.jpg') }}');">
                <div class="justify-center items-center text-white text-center h-full pt-8">
                    <div class="container mx-auto px-4 py-20">
                        <div class="section">
                            <div class="flex flex-row justify-between items-center mb-8">
                                <h2 class="text-xl font-bold ">DEADLIFT</h2>
                                <div class="flex space-x-4">
                                    <i class="bi bi-list-ul  text-2xl"></i>
                                    <i class="bi bi-graph-up text-2xl"></i>
                                </div>
                            </div>
                            <table class="w-full text-white text-center bg-black border-b">
                                <thead>
                                    <tr class="border-b">
                                        <th class="p-2">Rep</th>
                                        <th class="p-2">Oct</th>
                                        <th class="p-2">Nov</th>
                                        <th class="p-2">Dec</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b">
                                        <td class="p-2">1</td>
                                        <td class="p-2">150kg</td>
                                        <td class="p-2">130kg</td>
                                        <td class="p-2">100kg</td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-2">3</td>
                                        <td class="p-2">120kg</td>
                                        <td class="p-2">100kg</td>
                                        <td class="p-2">80kg</td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-2">5</td>
                                        <td class="p-2">100kg</td>
                                        <td class="p-2">80kg</td>
                                        <td class="p-2">60kg</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="section mt-8">
                            <div class="flex flex-row justify-between  items-center mb-8">
                                <h2 class="text-xl font-bold ">AMRAP 5</h2>
                                <div class="flex space-x-4">
                                    <i class="bi bi-list-ul  text-2xl"></i>
                                    <i class="bi bi-graph-up text-2xl"></i>
                                </div>
                            </div>
                            <table class="w-full text-white text-center bg-black">
                                <thead>
                                    <tr class="border-b">
                                        <th class="p-2"></th>
                                        <th class="p-2">Oct</th>
                                        <th class="p-2">Nov</th>
                                        <th class="p-2">Dec</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b">
                                        <td class="p-2">Rounds</td>
                                        <td class="p-2">8</td>
                                        <td class="p-2">6</td>
                                        <td class="p-2">5</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</body>


</html>

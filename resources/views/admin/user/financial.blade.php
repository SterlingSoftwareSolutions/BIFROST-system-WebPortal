<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Financial</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./node_modules/apexcharts/dist/apexcharts.css">
    <script src="./node_modules/lodash/lodash.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha384-uXf6sr8AaXNpfLE4u1jL1EB5Yc5zH06smif1RtZWm1sC2C2Mi41WIebh5q1Q0ynP" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>
</head>



<body>
    @extends('layout.layout')
    @section('content')
        <div class="container overflow-y-auto overflow-x-auto w-full" id="container">

            <div class="flex flex-col md:mt-24 md:flex-row text-start mt-24">
                <span class=" text-gray-500"> Home <span> /</span> <span
                        class="text-black font-bold">Financial</span></span>
            </div>
            {{-- tabmenu --}}
            <div class=" rounded-sm shadow-xm mt-3 md:text-lg text-sm font-medium text-center ">

                    <ul class="flex flex-wrap -mb-px    items-center justify-center ">
                        <a href="#" aria-current="false"
                            class="inline-block   bg-black text-white  rounded-none shadow  p-1 border-b-2 border-transparent px-5 pt-0 pb-0 hover:border-gray-200   border-gray-200 hover:bg-black hover:text-white hover:font-bold font-sans text-base leading-10 mb-4" style="font-size:14px; font-weight: 600; ">
                            DASHBOARD
                        </a>
                        <a href="#" aria-current="page"
                            class="inline-block p-4  bg-lightgrey shadow  border-b-2 rounded-none border-transparent px-5 pt-0 pb-0 dark:text-blue-500 dark:border-blue-500  text-gray-800 border-gray-200 hover:bg-black hover:text-white hover:font-bold font-bold font-sans text-base leading-10 mb-4" style="font-size:14px; font-weight: 600;">
                            INVOICES
                        </a>
                        <a href="#" aria-current="false"
                            class="inline-block p-4  bg-lightgrey shadow  border-b-2 rounded-none border-transparent px-5 pt-0 pb-0  text-gray-800 border-gray-200 hover:bg-black hover:text-white hover:font-bold font-bold font-sans text-base leading-10 mb-4" style="font-size:14px; font-weight: 600;">
                            JOURNAL
                        </a>
                        <a href="#" aria-current="false"
                            class="inline-block p-4  shadow bg-lightgrey border-b-2 rounded-none border-transparent px-5 pt-0 pb-0  text-gray-800 border-gray-200 hover:bg-black hover:text-white hover:font-bold font-bold font-sans text-base leading-10 mb-4" style="font-size:14px; font-weight: 600;">
                            LEDGER
                        </a>
                        <a href="#" aria-current="false"
                            class="inline-block p-4  shadow bg-lightgrey border-b-2 rounded-none border-transparent px-5 pt-0 pb-0  text-gray-800 border-gray-200 hover:bg-black hover:text-white hover:font-bold font-bold font-sans text-base leading-10 mb-4" style="font-size:14px; font-weight: 600;">
                            ASSETS
                        </a>
                        <a href="#" aria-current="false"
                            class="inline-block p-4  shadow bg-lightgrey border-b-2 rounded-none border-transparent px-5 pt-0 pb-0   text-gray-800 border-gray-200 hover:bg-black hover:text-white hover:font-bold font-bold font-sans text-base leading-10 mb-4" style="font-size:14px; font-weight: 600;">
                            END OF YEAR
                        </a>
                    </ul>
                </div>
                {{-- end tab menu --}}


                {{-- year data segement --}}
                <div class="relative  flex flex-wrap -mb-px   items-center justify-center mt-28 ">
                    <div
                        class="tooltiptext absolute left-1/2 transform -translate-x-1/2 bottom-full mb-2 bg-black text-white text-center px-2 py-2 text-sm opacity-100 w-auto">
                        Year to Date
                        <div
                            class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-t-8 border-t-black border-x-8 border-x-transparent">
                        </div>
                    </div>

                <div
                    class="relative w-96 h-20 font-bold text-center pt-4 text-white text-3xl bg-littlegreen lg:mr-16 md:mr-16">
                    $1,500.00</div>
                <div class="relative w-96 h-20 font-bold text-center pt-4 text-black text-3xl shadow-md bg-white">
                    $500.00
                    <div class="absolute top-0 left-0 ml-2 mt-2 text-sm font-normal text-black">GST</div>
                    <div class="absolute bottom-0 right-0 mr-2 mb-2 text-sm font-normal text-black">PAYABLE</div>
                </div>
            </div>
            <div class="flex flex-wrap items-center justify-center"><a href=""
                    class=" text-black  mt-10 underline ">View to Date</a></div>
            {{-- end year date segement --}}

                {{-- year naviagate --}}
                <div class=" w-full container whitespace-nowrap  ">
                    <div class=" w-auto flex-nowrap whitespace-nowrap mt-10 " id="conatiner">
                        <div class="flex justify-between  border-black border-b py-2">
                            <span class="text-xl "> June 2017</span>
                            <span class="  text-2xl font-bold">
                                <i class="fa fa-angle-left mr-2" aria-hidden="true" style="font-size: 1.5rem;"></i>
                                2017-2018
                                <i class="fa fa-angle-right ml-2" aria-hidden="true" style="font-size: 1.5rem;"></i>
                            </span>

                            <span class="text-xl ">June 2018 </span>
                        </div>

                    <div
                        class="md:w-full grid grid-cols-4 md:flex flex-nowrap whitespace-nowrap border-black border-b py-2">
                        <div
                            class="w-auto flex-1 text-center p-2 border  flex-nowrap border-black relative  hover:bg-black hover:text-white hover:font-bold">
                            <div class="font-medium text-xs ">July</div>
                            <div class="text-xs">$1,110,000</div>
                        </div>
                        <div
                            class="w-auto flex-1 text-center p-2 border flex-nowrap border-black relative  hover:bg-black hover:text-white hover:font-bold">
                            <div class="text-xs">Aug.</div>
                            <div class="text-xs">$0.00</div>
                        </div>
                        <div
                            class="w-auto flex-1 text-center p-2 border flex-nowrap border-black relative  hover:bg-black hover:text-white hover:font-bold">
                            <div class="text-xs">Sept.</div>
                            <div class="text-xs">$1,110,000</div>
                        </div>
                        <div class="w-auto flex-1 text-center p-2 border flex-nowrap border-black relative bg-darkyellow ">
                            <div class=" top-1 text-center left-1 text-xs">BAS</div>
                            <div class=" justify-center items-center text-xs text-littlegreen">RECEIVABLE</div>
                            <div class=" bottom-1 right-1 text-xs">$499</div>
                        </div>
                        <div
                            class="w-auto flex-1 text-center p-2 border flex-nowrap border-black  hover:bg-black hover:text-white hover:font-bold">
                            <div class="text-xs">Oct.</div>
                            <div class="text-xs">$1,110,000</div>
                        </div>
                        <div
                            class="w-auto flex-1 text-center p-2 border flex-nowrap border-black  hover:bg-black hover:text-white hover:font-bold">
                            <div class="text-xs">Nov.</div>
                            <div class="text-xs">$10,000</div>
                        </div>
                        <div
                            class="w-auto flex-1 text-center p-2 border flex-nowrap border-black  hover:bg-black hover:text-white hover:font-bold">
                            <div class="text-xs">Dec.</div>
                            <div class="text-xs">$10,000</div>
                        </div>
                        <div class="w-auto flex-1 text-center p-2 border flex-nowrap border-black bg-darkyellow relative">
                            <div class=" top-1 left-1 text-xs">BAS</div>
                            <div class=" justify-center items-center text-xs text-red-600">PAYABLE</div>
                            <div class=" bottom-1 right-1 text-xs">$1250.00</div>
                        </div>
                        <div
                            class="w-full flex-1 text-center p-2 border flex-nowrap border-black  hover:bg-black hover:text-white hover:font-bold">
                            <div class="text-xs">Jan.</div>
                            <div class="text-xs">$1,110,000</div>
                        </div>
                        <div
                            class="w-full flex-1 text-center p-2 border flex-nowrap border-black  hover:bg-black hover:text-white hover:font-bold">
                            <div class="text-xs">Feb.</div>
                            <div class="text-xs">$0,000,000</div>
                        </div>
                        <div
                            class="w-auto flex-1 text-center p-2 border flex-nowrap border-black  hover:bg-black hover:text-white hover:font-bold">
                            <div class="text-xs">March</div>
                            <div class="text-xs">$10,000</div>
                        </div>
                        <div class="w-auto flex-1 text-center p-2 border flex-nowrap border-black bg-darkyellow relative">
                            <div class=" top-1 left-1 text-xs">BAS</div>
                            <div class="justify-center items-center text-xs text-littlegreen">RECEIVABLE</div>
                            <div class=" bottom-1 right-1 text-xs">$499.25</div>
                        </div>
                        <div
                            class="w-auto flex-1 text-center p-2 border flex-nowrap border-black hover:bg-black hover:text-white hover:font-bold">
                            <div class="text-xs">April</div>
                            <div class="text-xs">$1,110,000</div>
                        </div>
                        <div
                            class="w-auto flex-1 text-center p-2 border flex-nowrap border-black  hover:bg-black hover:text-white hover:font-bold">
                            <div class="text-xs">May</div>
                            <div class="text-xs">$10,000</div>
                        </div>
                        <div class="w-auto flex-1 text-center p-2 border flex-nowrap border-black ">
                            <div class="text-xs">June</div>
                            <div class="text-xs">$0,000,000</div>
                        </div>
                        <div
                            class="w-auto  flex-1 text-center p-2 border flex-nowrap whitespace-nowrap border-black bg-darkyellow relative">
                            <div class=" top-1 left-1 text-xs">BAS</div>
                            <div class="justify-center items-center text-xs text-littlegreen">RECEIVABLE</div>
                            <div class=" bottom-1 right-1 text-xs">$499.25</div>
                        </div>
                    </div>
                </div>
                {{-- end year naviagte --}}
            </div>

            {{-- chart and profit segement 1 --}}
            <div class="w-full mt-20">
                <div class=" w-auto md:flex gap-4">
                    <div class="w-4/12 md:flex gap-4 bg-white rounded-lg shadow-lg ">
                        <!-- Content for the first part (4/12 width) -->
                        <div class="w-1/2 grid md:flex flex-col gap-4 flex-1">
                            <div class="bg-white p-4 rounded shadow flex-1">
                                <h2 class="text-xl font-bold mb-4 text-center">Profit and Loss</h2>
                                <div>
                                    <div class="flex justify-between w-full border-black border-b py-2 ">
                                        <span>Name</span><span>Amount</span>
                                    </div>
                                    <div class="flex justify-between w-full border-black border-b py-2"><span>Total
                                            Income</span><span>$2500.00</span></div>
                                    <div class="flex justify-between w-full border-black border-b py-2">
                                        <span>Wages</span><span>$1500.00</span>
                                    </div>
                                    <div class="flex justify-between w-full  border-black border-b py-2"><span>Gross
                                            Profit</span><span>$1000.00</span></div>
                                    <div class="flex justify-between w-full border-black border-b py-2">
                                        <span>Expenses</span><span>$500.00</span>
                                    </div>
                                    <div class="flex justify-between w-full  border-black border-b py-2 font-bold">
                                        <span>Net
                                            Profit</span><span>$1000.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded shadow flex-1 flex-grow">
                                <h2 class="text-xl font-bold mb-4">Invoices</h2>
                                <div class="">
                                    <canvas id="donut-chart"></canvas>
                                </div>
                            </div>
                        </div>

                            <div class="w-1/2 grid md:flex flex-col gap-4 flex-1 h-[675px] ">
                                <div class="bg-whit p-2 rounded shadow flex-1  ">
                                    <h2 class="text-xl font-bold mb-4 text-center">Cash Balance</h2>
                                    <p>Last Month</p>
                                    <p class="text-[#31c7a2] text-2xl font-bold text-center mt-8">$3,051.00</p>
                                    <p class=" mt-10">Cash Balance</p>
                                    <p class="text-[#fb1018] text-2xl font-bold text-center  mt-10">$3,051.00</p>
                                </div>

                                <div class="bg-white p-4 rounded shadow flex-1 flex-grow">
                                    <div class="space-y-5">
                                        <!-- Progress -->
                                        <div>
                                            <h2 class="text-xl font-bold mb-4 text-center">Owed</h2>
                                            <div class="mb-2 flex justify-between items-center">
                                                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Owed 0 - 14
                                                    days
                                                </h3>
                                                <span class="text-xs text-gray-800 dark:text-white">
                                                    $26,4523.25</span>
                                            </div>
                                            <div class="flex w-full h-2 bg-gray-200 rounded-full overflow-hidden dark:bg-neutral-700"
                                                role="progressbar" aria-valuenow="25" aria-valuemin="0"
                                                aria-valuemax="100">
                                                <div class="flex flex-col justify-center rounded-full overflow-hidden bg-[#14befd] text-xs text-white text-center whitespace-nowrap transition duration-500 dark:bg-blue-500"
                                                    style="width: 95%"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="mb-2 flex justify-between items-center">
                                                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Owed 14 -
                                                    30
                                                    days
                                                </h3>
                                                <span class="text-xs text-gray-800 dark:text-white">$26,4523.25</span>
                                            </div>
                                            <div class="flex w-full h-2 bg-gray-200 rounded-full overflow-hidden dark:bg-neutral-700"
                                                role="progressbar" aria-valuenow="25" aria-valuemin="0"
                                                aria-valuemax="100">
                                                <div class="flex flex-col justify-center rounded-full overflow-hidden bg-[#14befd] text-xs text-white text-center whitespace-nowrap transition duration-500 dark:bg-blue-500"
                                                    style="width: 45%"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="mb-2 flex justify-between items-center">
                                                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Owed 30 -
                                                    60
                                                    days
                                                </h3>
                                                <span class="text-xs text-gray-800 dark:text-white">$2,4523.25</span>
                                            </div>
                                            <div class="flex w-full h-2 bg-gray-200 rounded-full overflow-hidden dark:bg-neutral-700"
                                                role="progressbar" aria-valuenow="25" aria-valuemin="0"
                                                aria-valuemax="100">
                                                <div class="flex flex-col justify-center rounded-full overflow-hidden bg-[#14befd] text-xs text-white text-center whitespace-nowrap transition duration-500 dark:bg-blue-500"
                                                    style="width: 25%"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="mb-2 flex justify-between items-center">
                                                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Owed 60 -
                                                    90
                                                    days
                                                </h3>
                                                <span class="text-xs text-gray-800 dark:text-white">$2,4523.25</span>
                                            </div>
                                            <div class="flex w-full h-2 bg-gray-200 rounded-full overflow-hidden dark:bg-neutral-700"
                                                role="progressbar" aria-valuenow="25" aria-valuemin="0"
                                                aria-valuemax="100">
                                                <div class="flex flex-col justify-center rounded-full overflow-hidden bg-[#14befd] text-xs text-white text-center whitespace-nowrap transition duration-500 dark:bg-blue-500"
                                                    style="width: 45%"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="mb-2 flex justify-between items-center">
                                                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Owed 90+
                                                    days
                                                </h3>
                                                <span class="text-xs text-gray-800 dark:text-white">$2,4523.25</span>
                                            </div>
                                            <div class="flex w-full h-2 bg-gray-200 rounded-full overflow-hidden dark:bg-neutral-700"
                                                role="progressbar" aria-valuenow="25" aria-valuemin="0"
                                                aria-valuemax="100">
                                                <div class="flex flex-col justify-center rounded-full overflow-hidden bg-[#14befd] text-xs text-white text-center whitespace-nowrap transition duration-500 dark:bg-blue-500"
                                                    style="width: 55%"></div>
                                            </div>
                                        </div>


                                    <!-- End Progress -->

                                    <!-- Progress -->
                                    <!-- Remaining progress divs -->
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="md:w-4/12 bg-white rounded-lg shadow-lg  ">

                        <h2 class="text-xl font-bold mb-4 text-center">Graph Heading</h2>
                        <div class=" items-center justify-center flex"> <select
                                class="mb-4 p-2 border rounded justify-center flex items-center">
                                <option>View by Month</option>
                                <!-- other options -->
                            </select></div>

                        <div class=" mt-20 w-full">
                            <canvas id="myChart" height="300"></canvas>
                            <script>
                                var ctx = document.getElementById('myChart').getContext('2d');
                                var myChart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: ['2018', '2019', '2020', '2021', '2022', '2023'],
                                        datasets: [

                                            {
                                                label: 'Height',
                                                data: [500, 700, 600, 800, 500, 1200],
                                                borderColor: 'rgba(255, 99, 132, 1)',
                                                borderWidth: 2,
                                                fill: false,
                                                tension: 0.4
                                            },
                                            {
                                                label: 'Heading 1',
                                                data: [800, 600, 1200, 900, 1300, 500],
                                                borderColor: 'rgba(54, 162, 235, 1)',
                                                borderWidth: 2,
                                                fill: false,
                                                tension: 0.4
                                            },
                                            {
                                                label: 'Heading 2',
                                                data: [600, 900, 500, 700, 800, 1500],
                                                borderColor: 'rgba(255, 159, 64, 1)',
                                                borderWidth: 2,
                                                fill: false,
                                                tension: 0.4
                                            }
                                        ]
                                    },
                                    options: {
                                        responsive: true,
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            }
                                        },
                                        plugins: {
                                            annotation: {
                                                annotations: [{
                                                        type: 'point',
                                                        xValue: '2021',
                                                        yValue: 1500,
                                                        backgroundColor: 'rgba(0, 255, 0, 0.5)',
                                                        borderColor: 'rgb(0, 255, 0)',
                                                        borderWidth: 2,
                                                        radius: 5,
                                                    },
                                                    {
                                                        type: 'point',
                                                        xValue: '2023',
                                                        yValue: 1200,
                                                        backgroundColor: 'rgba(255, 0, 0, 0.5)',
                                                        borderColor: 'rgb(255, 0, 0)',
                                                        borderWidth: 2,
                                                        radius: 5,
                                                    }
                                                ]
                                            }
                                        }
                                    }
                                });
                            </script>
                        </div>

                    </div>
                    <div class=" bg-white rounded-lg shadow-lg   md:w-4/12 md:flex gap-4">
                        <!-- Content for the first part (4/12 width) -->
                        <div class="w-1/2  flex flex-col  gap-4">
                            <div class=" bg-lightyellow p-4 rounded text-center shadow flex-1">
                                <h2 class="text-xl font-bold mb-4 text-center">Accounts Receivable</h2>
                                <p class="text-3xl font-bold text-center mt-20">$2,356.12</p>
                                <button class="text-black text-center mt-4">Manage</button>
                            </div>
                            <div class="bg-lightyellow p-4 text-center rounded shadow flex-1">
                                <h2 class="text-xl font-bold mb-4 text-center">Receivable Overdue</h2>
                                <p class="text-3xl font-bold text-center mt-20">$2,356.12</p>
                                <button class="text-black  mt-4">Debtors List</button>
                            </div>
                        </div>
                        <div class="w-1/2 flex flex-col gap-4">
                            <!-- Accounts Payable -->
                            <div class=" bg-lightyellow p-4 rounded text-center shadow flex-1">
                                <h2 class="text-xl font-bold mb-4 text-center">Accounts Payable</h2>
                                <p class="text-3xl font-bold text-center mt-20">$2,356.12</p>
                                <button class="text-black text-center mt-4">Manage</button>
                            </div>
                            <div class="bg-lightyellow p-4 rounded text-center shadow flex-1">
                                <h2 class="text-xl font-bold mb-4 text-center">Payable Overdue</h2>
                                <p class="text-3xl font-bold text-center mt-20">$2,356.12</p>
                                <button class="text-black   text-center mt-4">Creditors List</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            {{-- chart and profit segment 1 end --}}

            {{-- barchar segement  --}}
            <div class=" w-full mt-20">
                <div class="w-auto md:flex gap-4">
                    {{-- Revenue div  --}}
                    <div class="w-4/12 md:flex">
                        <div class="bg-white rounded-lg shadow-lg p-5 w-full">
                            <h2 class="text-center text-2xl font-bold mb-5">Revenue</h2>
                            <div class="space-y-4">
                                <div class="flex items-center">

                                    <div class="w-40"><span class="text-sm ">JPLMS Internal Works</span></div>

                                    <div class="ml-8 flex-1">
                                        <div
                                            class="progress-bar w-10/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 90%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>

                                    <span class="ml-1">$90</span>
                                </div>

                                <div class="flex items-center">

                                    <div class=" w-40"><span class="text-sm ">Beach Avenue Wholesalers</span></div>

                                    <div class="ml-8 flex-1">
                                        <div
                                            class="progress-bar w-10/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 90%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>

                                    <span class="ml-1">$90</span>
                                </div>


                                <div class="flex items-center">

                                    <div class="w-40"><span class="text-sm ">G Terlato</span></div>

                                    <div class="ml-8 flex-1">
                                        <div
                                            class="progress-bar w-10/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 90%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>

                                    <span class="ml-1">$90</span>
                                </div>


                                <div class="flex items-center">

                                    <div class="w-40"><span class="text-sm ">Alliance Financial Group</span></div>

                                    <div class="ml-8 flex-1">
                                        <div
                                            class="progress-bar w-10/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 90%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>

                                    <span class="ml-1">$90</span>
                                </div>

                                <div class="flex items-center">

                                    <div class="w-40"><span class="text-sm ">Alliance Financial Group</span></div>

                                    <div class="ml-8 flex-1">
                                        <div
                                            class="progress-bar w-10/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 90%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>

                                    <span class="ml-1">$90</span>
                                </div>
                            </div>
                            <div class="mb-4 flex space-x-2 mt-16">
                                <button class="tab-button active bg-blue-500 text-white py-2 px-4 rounded">Invoices
                                    Paid</button>
                                <button class="tab-button bg-gray-200 text-black py-2 px-4 rounded">Invoices
                                    Unpaid</button>
                            </div>
                            <div>
                                <table class="min-w-full border-collapse border border-gray-200">
                                    <thead>
                                        <tr>
                                            <th
                                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-sm leading-4 font-medium text-gray-500">
                                                Date</th>
                                            <th
                                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-sm leading-4 font-medium text-gray-500">
                                                Client</th>
                                            <th
                                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-sm leading-4 font-medium text-gray-500">
                                                Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                                20/05/2019
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">TC
                                                Printing
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">$150.00
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                                04/03/2019
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">G Terlato
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">$2,500.00
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                                04/03/2019
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">G Terlato
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">$2,500.00
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                                20/05/2019
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">TC
                                                Printing
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">$150.00
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>


                            <div class="bg-yellow-400 text-center text-white font-bold py-2 mt-5 rounded-md">
                                No Data Found
                            </div>
                        </div>
                    </div>
                    {{-- end Revenue div  --}}

                    {{-- start Expenses div --}}
                    <div class="w-4/12  md:flex">
                        <div class="bg-white rounded-lg shadow-lg p-5 w-full">
                            <h2 class="text-center text-2xl font-bold mb-5">Expenses</h2>

                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <i class="fa fa-car mr-2" aria-hidden="true"></i>
                                    <div class=" w-32"><span class="text-sm ">Vehicle</span></div>

                                    <div class=" flex-1">
                                        <div
                                            class="progress-bar w-11/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 40%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class=" mx-3 ">
                                        <i class="fa fa-file-text " aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>

                                <div class="flex items-center">
                                    <i class="fa fa-podcast mr-2" aria-hidden="true"></i>
                                    <div class=" w-32 "><span class="text-sm ">Payroll</span></div>

                                    <div class=" flex-1">
                                        <div
                                            class="progress-bar w-11/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 60%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class=" mx-3 ">
                                        <i class="fa fa-file-text " aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fa fa-globe mr-2" aria-hidden="true"></i>
                                    <div class=" w-32"><span class="text-sm ">Internet/Phone</span></div>

                                    <div class=" flex-1">
                                        <div
                                            class="progress-bar w-11/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 80%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class=" mx-3 ">
                                        <i class="fa fa-file-text " aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fa fa-bath mr-2" aria-hidden="true"></i>
                                    <div class=" w-32"><span class="text-sm ">Accomodation</span></div>

                                    <div class=" flex-1">
                                        <div
                                            class="progress-bar w-11/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 60%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class=" mx-3 ">
                                        <i class="fa fa-file-text " aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>

                                <div class="flex items-center">
                                    <i class="fa fa-music mr-2" aria-hidden="true"></i>
                                    <div class=" w-32"><span class="text-sm ">Entertainment</span></div>

                                    <div class=" flex-1">
                                        <div
                                            class="progress-bar w-11/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 20%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class=" mx-3 ">
                                        <i class="fa fa-file-text " aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fa fa-globe mr-2" aria-hidden="true"></i>
                                    <div class=" w-32"><span class="text-sm ">Internet/Phone</span></div>

                                    <div class=" flex-1">
                                        <div
                                            class="progress-bar w-11/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 80%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class=" mx-3 ">
                                        <i class="fa fa-file-text " aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fa fa-truck mr-2 text-sm" aria-hidden="true"></i>
                                    <div class=" w-32"><span class="text-sm ">Distribution</span></div>

                                    <div class=" flex-1">
                                        <div
                                            class="progress-bar w-11/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 80%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class=" mx-3 ">
                                        <i class="fa fa-file-text " aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fa fa-bed text-sm " aria-hidden="true"></i>
                                    <div class=" w-32 ml-2"><span
                                            class="text-[9.8px] ">Equipment/Furniture/Supplies</span></div>

                                    <div class=" flex-1">
                                        <div
                                            class="progress-bar w-11/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 80%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class=" mx-3 ">
                                        <i class="fa fa-file-text " aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fa fa-globe mr-2" aria-hidden="true"></i>
                                    <div class=" w-32"><span class="text-sm ">Membership Fees</span></div>

                                    <div class=" flex-1">
                                        <div
                                            class="progress-bar w-11/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 80%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class=" mx-3 ">
                                        <i class="fa fa-file-text " aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fa fa-desktop text-sm" aria-hidden="true"></i>
                                    <div class=" w-32 ml-2"><span class="text-sm ">Software/Research</span></div>

                                    <div class=" flex-1">
                                        <div
                                            class="progress-bar w-11/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 80%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class=" mx-3 ">
                                        <i class="fa fa-file-text " aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fa fa-car mr-2" aria-hidden="true"></i>
                                    <div class=" w-32"><span class="text-sm ">JPL Automotive</span></div>

                                    <div class=" flex-1">
                                        <div
                                            class="progress-bar w-11/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 80%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class=" mx-3 ">
                                        <i class="fa fa-file-text " aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fa fa-check-square" aria-hidden="true"></i>
                                    <div class="w-32 ml-2"><span class="text-sm ">Accountant Fee</span></div>

                                    <div class=" flex-1">
                                        <div
                                            class="progress-bar w-11/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 80%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class=" mx-3 ">
                                        <i class="fa fa-file-text " aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fa fa-globe mr-2" aria-hidden="true"></i>
                                    <div class=" w-32"><span class="text-sm ">Director Fee</span></div>

                                    <div class=" flex-1">
                                        <div
                                            class="progress-bar w-11/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 80%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class=" mx-3 ">
                                        <i class="fa fa-file-text " aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fa fa-university mr-2" aria-hidden="true"></i>
                                    <div class=" w-32"><span class="text-sm ">Loan Repayment</span></div>

                                    <div class=" flex-1">
                                        <div
                                            class="progress-bar w-11/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 80%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class=" mx-3 ">
                                        <i class="fa fa-file-text " aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fa fa-user mr-2" aria-hidden="true"></i>
                                    <div class=" w-32"><span class="text-sm ">Wages</span></div>

                                    <div class=" flex-1">
                                        <div
                                            class="progress-bar w-11/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 80%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class=" mx-3 ">
                                        <i class="fa fa-file-text " aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>








                            </div>
                            <div class="bg-yellow-400 text-center text-white font-bold py-2 mt-5 rounded-md">
                                No Data Found
                            </div>
                        </div>
                    </div>
                    {{-- end Expenses div --}}

                    {{-- start summary   --}}
                    <div class="w-4/12  md:flex">
                        <div class="bg-white rounded-lg shadow-lg p-5 w-full">
                            <h2 class="text-center text-2xl font-bold mb-5">Summary</h2>

                            <div class="space-y-4">

                                <div class="flex items-center">
                                    <div class=" w-28"><span class="text-sm ">Income</span></div>

                                    <div class="ml-8 flex-1">
                                        <div
                                            class="progress-bar w-10/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 55%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class="mx-3 flex items-center">
                                        <i class="fa fa-file-text mr-2" aria-hidden="true"></i>
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>

                                <div class="flex items-center">
                                    <div class=" w-28"><span class="text-sm ">Outgoing</span></div>

                                    <div class="ml-8 flex-1">
                                        <div
                                            class="progress-bar w-10/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width:20%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class="mx-3 flex items-center">
                                        <i class="fa fa-file-text mr-2" aria-hidden="true"></i>
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>

                                <div class="flex items-center">
                                    <div class=" w-28"><span class="text-sm ">Gross Profit</span></div>

                                    <div class="ml-8 flex-1">
                                        <div
                                            class="progress-bar w-10/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 10%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class="mx-3 flex items-center">
                                        <i class="fa fa-file-text mr-2" aria-hidden="true"></i>
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>
                                <div class="flex items-center">
                                    <div class=" w-28"><span class="text-sm ">GST Paid</span></div>

                                    <div class="ml-8 flex-1">
                                        <div
                                            class="progress-bar w-10/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 70%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class="mx-3 flex items-center">
                                        <i class="fa fa-file-text mr-2" aria-hidden="true"></i>
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>

                                <div class="flex items-center">
                                    <div class=" w-28"><span class="text-sm ">GST Received</span></div>

                                    <div class="ml-8 flex-1">
                                        <div
                                            class="progress-bar w-10/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 30%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class="mx-3 flex items-center">
                                        <i class="fa fa-file-text mr-2" aria-hidden="true"></i>
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>

                                <div class="flex items-center">
                                    <div class=" w-28"><span class="text-sm ">GST Position</span></div>

                                    <div class="ml-8 flex-1">
                                        <div
                                            class="progress-bar w-10/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 80%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class="mx-3 flex items-center">
                                        <i class="fa fa-file-text mr-2" aria-hidden="true"></i>
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>

                                <div class="flex items-center">
                                    <div class=" w-28"><span class="text-sm ">Income Tax</span></div>

                                    <div class="ml-8 flex-1">
                                        <div
                                            class="progress-bar w-10/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 60%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class="mx-3 flex items-center">
                                        <i class="fa fa-file-text mr-2" aria-hidden="true"></i>
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>

                                <div class="flex items-center">
                                    <div class=" w-28"><span class="text-sm ">Net Profit</span></div>

                                    <div class="ml-8 flex-1">
                                        <div
                                            class="progress-bar w-10/12 h-5 bg-gray-300 rounded-md overflow-hidden relative">
                                            <div class="progress bg-prograsblue h-full" style="width: 40%;"></div>
                                            <span
                                                class="amount absolute top-0 right-0 text-black text-sm px-1">$1200</span>
                                        </div>
                                    </div>
                                    <div class="mx-3 flex items-center">
                                        <i class="fa fa-file-text mr-2" aria-hidden="true"></i>
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </div>
                                    <span class="ml-1">$90</span>
                                </div>
                            </div>

                            <div class="bg-yellow-400 text-center text-white font-bold py-2 mt-6 rounded-md">
                                No Data Found
                            </div>
                            <div class="border border-1 mt-10"></div>
                            <div class="flex justify-center space-x-4 mt-7">
                                <div class="bg-gray-200 p-4 w-48 h-36 flex flex-col justify-center items-center shadow-md">
                                    <div class="font-bold">Bank Ledger</div>
                                    <div class="mt-2">$1200</div>
                                </div>
                                <div class="bg-gray-200 p-4 w-48 h-36 flex flex-col justify-center items-center shadow-md">
                                    <div class="font-bold">Bank Ledger</div>
                                    <div class="mt-2">$1200</div>
                                </div>
                                <div class="bg-gray-200 p-4 w-48 h-36 flex flex-col justify-center items-center shadow-md">
                                    <div class="font-bold">Bank Ledger</div>
                                    <div class="mt-2">$1200</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end summary --}}
                </div>
            </div>
        </div>
    @endsection
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('donut-chart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Red', 'Blue', 'Yellow'],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    });
</script>

</html>

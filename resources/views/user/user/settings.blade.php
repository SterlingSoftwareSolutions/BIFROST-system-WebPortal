<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <title>Document</title>
</head>

<body>
    @extends('layout.user-layout')
    @section('user-content')
        <!-- Main content (Dashboard) -->
        <div class="container overflow-y-auto w-full bg-[#fafafa] flex-grow" id="container">
            <!-- Main content area with top margin for large screens and smaller margin for medium screens -->
            <div class="w-full mt-36 lg:mt-24 md:mt-24">
                <!-- Content section with gray background -->
                <div class="mx-4">
                    <div>
                        <div><span class="text-gray-700">Home / </span><strong>Settings</strong></div>
                    </div>
                    <div class="mt-5 flex mx-10 gap-5">
                        <div class="w-1/2 border border-gray-500 flex">
                            <div class="w-1/2 p-3">
                                <h2 class="mb-3">SUBSCRIPTION</h2>
                                <div class="border-r-2 border-gray-500 flex flex-col gap-16">
                                    <div class="">
                                        <p>Strentgth and Conditioning</p>
                                        <p>$49 per Week</p>
                                        <p>Commencement:Junuary Week 1 2024</p>
                                        <p>Next Paytment:Monday 15 th July 2024</p>
                                    </div>
                                    <div class="">
                                        <a href="#" class="underline underline-offset-3"> Transaction History</a>
                                    </div>
                                    <div class=" flex flex-row gap-16">
                                        <a href="#">James@jplms.com.au</a>
                                        <a href="#">edit</a>
                                    </div>
                                </div>
                                <div class="mt-5 flex gap-5">
                                    <button class="text-red-500">Cancel</button>
                                    <button class="text-orange-500">pause</button>
                                </div>

                            </div>
                            <div class="w-1/2  p-3 px-8">
                                <h2 class="font-semibold bg-slate-200 w-fit py-1 px-3">Payment Method</h2>
                                <div class="mt-5 border-green-500 border-2 p-3 rounded-xl flex flex-col">
                                    <div class="flex justify-between">
                                        <div class="text-blue-900 font-extrabold text-2xl border px-4 py-1">Visa</div>
                                        <div class="text-green-500">active</div>
                                    </div>
                                    <div class="mb-3">
                                        <p class="text-gray-600">****8888 | Expires 08/28</p>
                                        <p>Nicholas Jackson</p>
                                    </div>
                                    <div class="text-right border-t">
                                        <button class="text-red-500">
                                            remove x
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-5 border-blue-300 border-2 p-3 rounded-lg flex flex-col h-28 justify-center text-center bg-blue-100">
                                    <p class="text-blue-600">+</p>
                                    <p class="text-blue-600">Add a Payment Method</p>
                                </div>
                                
                            </div>
                        </div>
                        <div class="w-1/2 flex flex-col gap-5 ">
                            <div class="flex  gap-5">
                                <div class="w-3/5  border border-gray-500 p-3">
                                    <h2 class="mb-3">Sharing Preferences</h2>
                                    <div class="flex">
                                        <input type="checkbox">
                                        <label for="">Share Pb's withj other members</label>
                                    </div>
                                    <div class="flex">
                                        <input type="checkbox">
                                        <label for="">Allow data for use in case study</label>
                                    </div>
                                    <div class="flex">
                                        <input type="checkbox">
                                        <label for="">Allow data to be shared on social media</label>
                                    </div>
                                </div>
                                <div class="w-2/5  border border-gray-500 p-3">
                                    <h2 class="mb-3">Weights</h2>
                                    <div class="flex">
                                        <input type="checkbox">
                                        <label for="">Imperial</label>
                                    </div>
                                    <div class="flex">
                                        <input type="checkbox">
                                        <label for="">Metric</label>
                                    </div>
                                </div>
                            </div>
                            <div class="flex  gap-5">
                                <div class="w-3/5  border border-gray-500 p-3">
                                    <h2 class="mb-3">Communication Preferences</h2>
                                    <div class="flex">
                                        <input type="checkbox">
                                        <label for="">News and Offers</label>
                                    </div>
                                    <div class="flex">
                                        <input type="checkbox">
                                        <label for="">Email preference</label>
                                    </div>
                                    <div class="flex">
                                        <input type="checkbox">
                                        <label for="">Text Preference</label>
                                    </div>
                                    <div class="flex">
                                        <input type="checkbox">
                                        <label for="">Alerts</label>
                                    </div>
                                    <div class="flex">
                                        <input type="checkbox">
                                        <label for="">Member PB notifications</label>
                                    </div>
                                </div>
                                <div class="w-2/5  border border-gray-500 p-3 flex flex-col gap-3">
                                    <a href="#" class="underline-offset-3 underline">Privacy Policy</a>
                                    <a href="#" class="underline-offset-3 underline">Terms and Conditions</a>
                                    <a href="#" class="underline-offset-3 underline">Contract terms</a>
                                    <a href="#" class="underline-offset-3 underline">Member Guidelines</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</body>

</html>

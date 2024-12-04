<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <title>Document</title>
</head>

<body class="font-sans">
    @extends('layout.layout')
    @section('content')
        <div class="container overflow-y-auto w-full bg-slate-50 flex-grow" id="container">
            <div class=" ">
                <div class="mt-24 mx-4">
                    {{-- title1 cheack edit or add --}}
                    <div>
                        <div class="justify-between flex">
                            <div><span class="text-gray-700">Home / </span>
                                View Profile
                            </div>
                            <div>
                                <a href="{{ route('viewadminclientmanagement') }}">
                                    <button class="bg-black text-white p-2 px-4 rounded-md">Back</button>
                                </a>
                            </div>
                        </div>
                    </div>
                    {{-- title2 cheack edit or add --}}
                    <div>
                        <h2 class="text-black text-lg">
                            <strong>
                                View Profile
                            </strong>
                        </h2>
                    </div>
                    {{-- route cheacking --}}
                    <div class="space-y-6 text-xs  rounded-lg shadow-lg bg-white p-2">
                        <div class="text-xs">
                            {{-- User Profile View Page --}}
                            <div class="p-4">
                                {{-- First Name and Last Name Row --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div class="form-group">
                                        <label for="firstname" class="text-gray-700 font-semibold block mb-1">First
                                            Name</label>
                                        <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                            {{ old('firstname', isset($member) ? $member->firstname : '') }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="lastname" class="text-gray-700 font-semibold block mb-1">Last
                                            Name</label>
                                        <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                            {{ old('lastname', isset($member) ? $member->lastname : '') }}
                                        </div>
                                    </div>
                                </div>

                                {{-- DOB, Gender, and Age --}}
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                    <div class="form-group">
                                        <label for="dob" class="text-gray-700 font-semibold block mb-1">Date of
                                            Birth</label>
                                        <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                            {{ old('dob', isset($member) && $member->dob ? \Carbon\Carbon::parse($member->dob)->format('Y-m-d') : 'N/A') }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="gender" class="text-gray-700 font-semibold block mb-1">Gender</label>
                                        <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                            {{ old('gender', isset($member) ? $member->gender : 'Not specified') }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="age" class="text-gray-700 font-semibold block mb-1">Age</label>
                                        <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                            {{ old('age', isset($member) ? $member->age : 'N/A') }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Phone Number, Email, and Address --}}
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                    <div class="form-group">
                                        <label for="phone" class="text-gray-700 font-semibold block mb-1">Phone</label>
                                        <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                            {{ old('phone', isset($member) ? $member->phone : 'N/A') }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="text-gray-700 font-semibold block mb-1">Email</label>
                                        <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                            {{ old('email', isset($member) ? $member->email : 'N/A') }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="address" class="text-gray-700 font-semibold block mb-1">Address</label>
                                        <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                            {{ old('address', isset($member) ? $member->address : 'N/A') }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Height, Weight, and BMR --}}
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                    <div class="form-group">
                                        <label for="height" class="text-gray-700 font-semibold block mb-1">Height
                                            (cm)</label>
                                        <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                            {{ old('height', isset($member) ? $member->height : 'N/A') }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="weight" class="text-gray-700 font-semibold block mb-1">Weight
                                            (kg)</label>
                                        <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                            {{ old('weight', isset($member) ? $member->weight : 'N/A') }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="bmr" class="text-gray-700 font-semibold block mb-1">BMR</label>
                                        <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                            {{ old('bmr', isset($member) ? $member->bmr : 'N/A') }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Primary Goal and Subscription Level --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div class="form-group">
                                        <label for="primary-goal" class="text-gray-700 font-semibold block mb-1">Primary
                                            Goal</label>
                                        <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                            {{ old('primary-goal', isset($member) ? $member->primary_goal : 'Not specified') }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="subscription_level"
                                            class="text-gray-700 font-semibold block mb-1">Subscription Level</label>
                                        <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                            {{ old('subscription_level', isset($member) ? $member->subscription_level : 'N/A') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Progress Photo --}}
                            <div class="flex flex-col md:flex-row gap-4 px-4">
                                <div class="w-full md:w-1/2 px-4 bg-white">
                                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Profile Photos</h2>
                                    <div
                                        class="progress-photos flex flex-col items-center justify-center min-h-40 rounded w-full h-80 p-4 border-2 border-dashed border-gray-300">
                                        <!-- Uploaded files will be shown here -->
                                        @if (isset($member) && $member->image_paths)
                                            @foreach (json_decode($member->image_paths) as $image)
                                                <img src="{{ asset('storage/' . $image) }}" alt="Progress Photo"
                                                    class="w-auto h-full rounded mb-2">
                                            @endforeach
                                        @else
                                            <p class="text-gray-600">No images uploaded.</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="w-full md:w-1/2 p-4 bg-white rounded-lg shadow">
                                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Payment Information</h2>
                                    
                                    <div class="mb-4">
                                        <label class="text-sm text-gray-700 font-bold">Credit / Debit Card</label>
                                        <div class="flex items-center mt-2 mb-4">
                                            <span class="mr-4">
                                                <img src="https://img.icons8.com/color/48/000000/visa.png" alt="Visa" class="w-10 inline">
                                            </span>
                                            <span class="mr-4">
                                                <img src="https://img.icons8.com/color/48/000000/mastercard.png" alt="MasterCard" class="w-10 inline">
                                            </span>
                                            <span class="text-gray-700"></span>
                                        </div>
                                    </div>
                                
                                    <div class="form-group mb-4">
                                        <label for="nameOnCard" class="text-gray-700 font-semibold block mb-1">Name on Card</label>
                                        <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                            <input type="text" id="nameOnCard" 
                                                class="w-full bg-transparent border-0 focus:outline-none cursor-default" 
                                                placeholder="John Doe" 
                                                value="" readonly>
                                        </div>
                                    </div>
                                
                                    <div class="form-group mb-4">
                                        <label for="cardNumber" class="text-gray-700 font-semibold block mb-1">Card Number</label>
                                        <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                            <input type="text" id="cardNumber" 
                                                class="w-full bg-transparent border-0 focus:outline-none cursor-default" 
                                                placeholder="4111 1111 1111 1111" 
                                                value="" readonly>
                                        </div>
                                    </div>
                                
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div class="form-group mb-4">
                                            <label for="cvv" class="text-gray-700 font-semibold block mb-1">CVV</label>
                                            <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                                <input type="text" id="cvv" 
                                                    class="w-full bg-transparent border-0 focus:outline-none cursor-default" 
                                                    placeholder="123" 
                                                    value="" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="exp" class="text-gray-700 font-semibold block mb-1">Expiration Date</label>
                                            <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                                <input type="text" id="exp" 
                                                    class="w-full bg-transparent border-0 focus:outline-none cursor-default" 
                                                    placeholder="12/26" 
                                                    value="" readonly>
                                            </div>
                                        </div>
                                    </div>
                                
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="form-group">
                                            <label for="firstBillableDate" class="text-gray-700 font-semibold block mb-1">First Billable Date</label>
                                            <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                                <input type="text" id="firstBillableDate" 
                                                    class="w-full bg-transparent border-0 focus:outline-none cursor-default" 
                                                    placeholder="01/11/2024" 
                                                    value="" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="commencement" class="text-gray-700 font-semibold block mb-1">Commencement</label>
                                            <div class="form-control rounded border px-4 py-2 bg-gray-100">
                                                <input type="text" id="commencement" 
                                                    class="w-full bg-transparent border-0 focus:outline-none cursor-default" 
                                                    placeholder="01/10/2024" 
                                                    value="" readonly>
                                            </div>
                                        </div>
                                    </div>
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

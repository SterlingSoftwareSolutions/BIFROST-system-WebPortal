<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Include jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <title>Document</title>
</head>

<body>
    @extends('layout.layout')
    @section('content')
        <!-- Main content (Dashboard) -->
        <div class="container transition-width mt-24 flex-grow mx-4" id="container">
            <div class="breadcrumb text-sm mb-4">
                <div><a href="#" class="text-gray-500 no-underline hover:underline">Home</a> / <span><strong> Client
                        </strong></span></div>
                <div class="flex ">
                    <div class="text-2xl  font-medium mb-5 font-source-sans w-1/2">Client Management</div>
                    <div class="w-1/2 flex items-center justify-end">
                        <a href="{{ route('addnewclientedit', ['action' => 'add']) }}">
                            <button class="bg-black text-white p-2 px-4 rounded-md whitespace-nowrap">Add New
                                Profile</button>
                        </a>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-lg shadow-md">
                    <table class="w-full border-collapse mb-5 text-sm">
                        <thead>
                            <tr>
                                <th class="p-3 border-s-2 border-y-2 border-gray-300 bg-white text-left" dir="ltr">
                                    First Name</th>
                                <th class="p-3 border-y-2 border-gray-300 bg-white text-left">Last Name</th>
                                <th class="p-3 border-y-2 border-gray-300 bg-white text-left">Contact Number</th>
                                <th class="p-3 border-y-2 border-gray-300 bg-white text-left">Subscription Level</th>
                                <th class="p-3 border-y-2 border-gray-300 bg-white text-left">Subscription Start</th>
                                <th class="p-3  border-y-2 border-gray-300 bg-white text-left" dir="rtl">
                                    Member Since</th>
                                <th class="p-3  border-y-2 px justify-end  border-gray-300 bg-white text-left"
                                    dir="rtl">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Example rows -->
                            @foreach ($members as $member)
                                <tr class="bg-gray-100">
                                    <td dir="ltr" class="p-3 border-s-2 border-y-2 border-gray-300 text-left">
                                        {{ $member->firstname }}
                                    </td>
                                    <td class="p-3 border-y-2 border-gray-300 text-left"> {{ $member->lastname }}</td>
                                    <td class="p-3 border-y-2 border-gray-300 text-left">{{ $member->phone }} </td>
                                    <td class="p-3 border-y-2 border-gray-300 text-left">{{ $member->subscription_level }}
                                    </td>
                                    <td class="p-3 border-y-2 border-gray-300 text-left">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" value="{{ $member->is_subsactive }}" class="sr-only peer" {{ $member->is_subsactive ? 'checked' : '' }}
                                            onchange="updateSubscriptionStatus({{ $member->id }}, this.checked)" @disabled(true)>
                                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        </label>
                                    </td>
                                    <td class="p-3 border-y-2 border-gray-300 text-left">
                                        {{ \Carbon\Carbon::parse($member->created_at)->format('Y F') }} <!-- Format the date -->
                                    </td>

                                    <td dir="rtl" class="p-3 border-y-2 justify-between  border-gray-300 text-left">
                                        <div class="flex space-x">
                                            <a href="{{ route('clientview', ['action' => 'edit', 'id' => $member->id]) }}" class="mr-8" data-client-id="{{ $member->id }}">
                                                <i class="text-[#fd8300] bi bi-eye"></i>
                                                <span class="text-black">View</span>
                                            </a>
                                            <a href="{{ route('addnewclientedit', ['action' => 'edit', 'id' => $member->id]) }}" class="mr-8" data-client-id="{{ $member->id }}">
                                                <i class="text-[#fd8300] bi bi-pencil"></i>
                                                <span class="text-black">Edit</span>
                                            </a>

                                            <form action="{{ route('deleteProfile', $member->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this profile?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="mr-7">
                                                    <i class="text-[#fd8300] bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    @endsection



</body>

</html>

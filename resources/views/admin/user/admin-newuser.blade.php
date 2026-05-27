<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/admin.js') }}" defer></script>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
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
                                @if ($action == 'edit')
                                    Edit Admin User
                                @else
                                    Add New Admin User
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('admindaaccess') }}">
                                    <button class="bg-black text-white p-2 px-4 rounded-md">Back</button>
                                </a>
                            </div>
                        </div>
                    </div>
                    {{-- title2 cheack edit or add --}}
                    <div>
                        <h2 class="text-black text-lg">
                            <strong>
                                @if ($action == 'edit')
                                    Edit Admin User
                                @else
                                    Add New Admin User
                                @endif
                            </strong>
                        </h2>
                    </div>
                    {{-- route cheacking --}}
                    <form
                        action="{{ $action == 'edit' ? route('updateadmin', ['id' => $user->id]) : route('newaminsave') }}"
                        method="POST" enctype="multipart/form-data"
                        class="space-y-6 text-xs  rounded-lg shadow-lg bg-white p-2">
                        @csrf
                        @if ($errors->any())
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    @foreach ($errors->all() as $error)
                                        Toastify({
                                            text: "{{ $error }}",
                                            duration: 5000,
                                            close: true,
                                            gravity: "top",
                                            position: "right",
                                            backgroundColor: "#ef4444",
                                        }).showToast();
                                    @endforeach
                                });
                            </script>
                        @endif
                        <div class="text-xs">
                            {{-- name and email Row --}}
                            <div class="w-full h-full p-2 grid grid-cols-1 md:grid-cols-6 md:border-b gap-4">
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center w-full md:col-span-1">
                                    <label for="name"
                                        class="block text-gray-700 font-bold w-full md:w-full mb-1 md:mb-0 pr-4">Name <span
                                            class="text-red-500">*</span></label>
                                </div>
                                <div class="w-full md:col-span-1">
                                    <input type="text" id="name" name="name"
                                        class="form-control w-full md:w-full rounded px-4 py-2 border" required
                                        value="{{ old('name', isset($user) ? $user->name : '') }}">
                                </div>
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center w-full md:col-span-1">
                                    <label for="email"
                                        class="block text-gray-700 font-bold w-full md:w-full mb-1 md:mb-0 md:ml-8 md:col-span-1">Email</label>
                                </div>
                                <div class="w-full md:col-span-1">
                                    <input type="text" id="email" name="email"
                                        class="form-control w-full md:w-full border rounded px-4 py-2"
                                        value="{{ old('email', isset($user) ? $user->email : '') }}">
                                </div>
                            </div>
                            <div class="w-full h-full p-2 grid grid-cols-1 md:grid-cols-6 md:border-b gap-4">
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center md:w-3/4 md:col-span-1">
                                    <label for="pin"
                                        class="block text-gray-700 font-bold w-full md:w-full mb-1 md:mb-0  md:col-span-1">Pin <span class="text-red-500">*</span></label>
                                </div>
                                <div class="w-full md:col-span-1 flex items-center space-x-2">
                                    <input type="text" id="pin" name="pin"
                                           class="form-control flex-grow border rounded px-4 py-2"
                                           inputmode="numeric"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                           value="{{ old('pin', isset($user) ? $pin : '') }}">
                                    <div id="resetPinAction" class="form-control text-black items-center px-4 py-2 cursor-pointer bg-gray-100 rounded flex-shrink-0 flex justify-center"
                                         data-action="{{ $action }}"
                                         data-user-id="{{ isset($user) && $user->id ? $user->id : '' }}"
                                        @if (isset($user) && $user->id)

                                        @endif>
                                        <i class="fa-solid fa-rotate-right mr-1"></i>
                                        <span>
                                            @if ($action == 'edit')
                                                Reset
                                            @else
                                                Generate
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="w-full h-full p-2 grid grid-cols-1 md:grid-cols-6 md:border-b gap-4">
                                <!-- Access Label -->
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center md:w-3/4 md:col-span-1">
                                    <label for="access" class="block text-gray-700 font-bold w-full md:w-full mb-1 md:mb-0">Access</label>
                                </div>
                                <div class="access-buttons w-full md:col-span-5 flex flex-wrap items-center gap-2">
                                    <div class="input-container mb-0 relative">
                                        <input type="checkbox" name="access_fields[dashboard]" id="dashboard" {{ isset($access) && $access->dashboard == 'enable' ? 'checked' : '' }}/>
                                        <label for="dashboard" >Dashboard</label>
                                    </div>
                                    <div class="input-container mb-0">
                                        <input type="checkbox" name="access_fields[access]" id="access" {{ isset($access) && $access->access == 'enable' ? 'checked' : '' }}/>
                                        <label for="access">Access</label>
                                    </div>
                                    <div class="input-container mb-0">
                                        <input type="checkbox"
                                            name="access_fields[client_management]" id="client_management" {{ isset($access) && $access->client_management == 'enable' ? 'checked' : '' }}/>
                                        <label for="client_management">Client Management</label>
                                    </div>
                                    <div class="input-container mb-0" >
                                        <input type="checkbox"
                                            name="access_fields[workout_library]" id="workout_library" {{ isset($access) && $access->workout_library == 'enable' ? 'checked' : '' }}/>
                                        <label for="workout_library" >Exercise Library</label>
                                    </div>
                                    <div class="input-container mb-0" >
                                        <input type="checkbox" name="access_fields[session]" id="session" {{ isset($access) && $access->session == 'enable' ? 'checked' : '' }}/>
                                        <label for="session">Session</label>
                                    </div>
                                    <div class="input-container mb-0" >
                                        <input type="checkbox" name="access_fields[financial]" id="financial" {{ isset($access) && $access->financial == 'enable' ? 'checked' : '' }}/>
                                        <label for="financial">Financial</label>
                                    </div>
                                    <div class="input-container mb-0" >
                                        <input type="checkbox" name="access_fields[communication]" id="communication" {{ isset($access) && $access->communication == 'enable' ? 'checked' : '' }}/>
                                        <label for="communication">Communication</label>
                                    </div>
                                    <div class="input-container mb-0" >
                                        <input type="checkbox" name="access_fields[statistics]" id="statistics" {{ isset($access) && $access->statistics == 'enable' ? 'checked' : '' }}/>
                                        <label for="statistics">Statistics</label>
                                    </div>
                                </div>

                            </div>
                        </div>

                            {{-- Submint Button --}}
                            <div
                                class="w-full h-full p-1 grid grid-cols-1 md:grid-cols-2 gap-4 whitespace-nowrap ">
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center w-full ">
                                    <label for=""
                                        class="block text-gray-700 font-bold w-full md:w-[34%] mb-1 md:mb-0 pr-4">
                                        <!-- Empty label left as it is -->
                                    </label>
                                    <div class="w-full md:w-[26%] ">
                                        <button type="submit" id="submit" required
                                            class="form-control w-1/4 md:w-[50%] p-2 text-center border rounded px-4 py-2 bg-black text-white">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center w-full">
                                    <!-- Additional content can be added here -->
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endsection
</body>

</html>

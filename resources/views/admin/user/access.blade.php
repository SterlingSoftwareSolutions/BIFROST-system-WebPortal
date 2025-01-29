<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <title>Access</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Access Page</title>
    <!-- Include jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Include your custom JavaScript file -->
    <script src="{{ asset('js/admin.js') }}" defer></script>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>


<body>
    @extends('layout.layout')
    @section('content')
        <!-- Main content (Dashboard) -->
        <div class="container transition-width mt-24 flex-grow mx-4" id="container">
            <div class="breadcrumb text-sm mb-4">
                <a href="#" class="text-gray-500 no-underline hover:underline">Home</a> / <span><strong> Access
                    </strong></span>
            </div>
            <div>
                <h1 class="text-2xl font-bold mb-5"> Access</h1>
            </div>
            @foreach ($users as $user)
                <div class="card bg-white p-4 rounded-lg shadow-md border mb-4">
                    <table class="w-full">
                        <tr class="border-b flex text-sm flex-col sm:table-row">
                            <td class="py-2 pr-6 text-gray-700 sm:w-1/4">Name</td>
                            <td class="py-2 sm:pl-0 sm:w-3/4">
                                <input type="text" name="name" id="name_{{ $user->id }}"
                                    value="{{ $user->name }}" class="bg-transparent flex w-full" disabled>
                            </td>
                        </tr>
                        <tr class="border-b flex text-sm flex-col sm:table-row">
                            <td class="py-2 pr-6 text-gray-700 sm:w-1/4">Email</td>
                            <td class="py-2 sm:pl-0 sm:w-3/4">
                                <input type="text" name="email" id="email_{{ $user->id }}"
                                    value="{{ $user->email }}" class="bg-transparent flex w-full" disabled>
                            </td>
                        </tr>

                        <tr class="border-b flex text-sm flex-col sm:table-row">
                            <td class="py-2 pr-6 text-gray-700 sm:w-1/4">Pin Number</td>
                            <td class="py-2  sm:pl-0 sm:w-3/4">
                                <input type="text" name="pin" id="pin_{{ $user->id }}"
                                    value="{{ $user->pin }}" class="bg-transparent" disabled>
                            </td>
                        </tr>
                        <tr class=" flex flex-col sm:table-row border-b">
                            <td class="py-2 pr-6 text-gray-700 sm:w-1/5">Access</td>
                            <td class="py-1 pl-2 sm:pl-0 sm:w-full">
                                <div class="access-buttons text-xs flex flex-wrap gap-1 sm:gap-2 my-1">
                                    <div class="input-container mb-0">
                                        <input type="checkbox" id="dashboard_{{ $user->id }}" name="dashboard"
                                            {{ $user->dashboard == 'enable' ? 'checked' : '' }}
                                            onclick="accesspage({{ $user->id }},  this.name, '{{ $user->access_type }}')"
                                            {{ in_array($user->user_type, ['client', 'worker']) ? 'disabled' : '' }} />
                                        <label for="dashboard_{{ $user->id }}">Dashboard</label>
                                    </div>
                                    <div class="input-container mb-0">
                                        <input type="checkbox" id="access_{{ $user->id }}" name="access"
                                            {{ $user->access == 'enable' ? 'checked' : '' }}
                                            onclick="accesspage({{ $user->id }}, this.name, '{{ $user->access_type }}')"
                                            {{ in_array($user->user_type, ['client', 'worker']) ? 'disabled' : '' }} />
                                        <label for="access_{{ $user->id }}">Access</label>
                                    </div>
                                    <div class="input-container mb-0">
                                        <input type="checkbox" id="clientManagement_{{ $user->id }}"
                                            name="client_management"
                                            {{ $user->client_management == 'enable' ? 'checked' : '' }}
                                            onclick="accesspage({{ $user->id }}, this.name, '{{ $user->access_type }}')"
                                            {{ in_array($user->user_type, ['client', 'worker']) ? 'disabled' : '' }} />
                                        <label for="clientManagement_{{ $user->id }}">Client Management</label>
                                    </div>
                                    <div class="input-container mb-0">
                                        <input type="checkbox" id="workoutLibrary_{{ $user->id }}"
                                            name="workout_library"
                                            {{ $user->workout_library == 'enable' ? 'checked' : '' }}
                                            onclick="accesspage({{ $user->id }}, this.name, '{{ $user->access_type }}')"
                                            {{ in_array($user->user_type, ['client', 'worker']) ? 'disabled' : '' }} />
                                        <label for="workoutLibrary_{{ $user->id }}">Exercise Library</label>
                                    </div>
                                    <div class="input-container mb-0">
                                        <input type="checkbox" id="session_{{ $user->id }}" name="session"
                                            {{ $user->session == 'enable' ? 'checked' : '' }}
                                            onclick="accesspage({{ $user->id }}, this.name, '{{ $user->access_type }}')"
                                            {{ in_array($user->user_type, ['client', 'worker']) ? 'disabled' : '' }} />
                                        <label for="session_{{ $user->id }}">Session</label>
                                    </div>
                                    <div class="input-container mb-0">
                                        <input type="checkbox" id="financial_{{ $user->id }}" name="financial"
                                            {{ $user->financial == 'enable' ? 'checked' : '' }}
                                            onclick="accesspage({{ $user->id }}, this.name, '{{ $user->access_type }}')"
                                            {{ in_array($user->user_type, ['client', 'worker']) ? 'disabled' : '' }} />
                                        <label for="financial_{{ $user->id }}">Financial</label>
                                    </div>
                                    <div class="input-container mb-0">
                                        <input type="checkbox" id="communication_{{ $user->id }}" name="communication"
                                            {{ $user->communication == 'enable' ? 'checked' : '' }}
                                            onclick="accesspage({{ $user->id }}, this.name, '{{ $user->access_type }}')"
                                            {{ in_array($user->user_type, ['client', 'worker']) ? 'disabled' : '' }} />
                                        <label for="communication_{{ $user->id }}">Communication</label>
                                    </div>
                                    <div class="input-container mb-0">
                                        <input type="checkbox" id="statistics_{{ $user->id }}" name="statistics"
                                            {{ $user->statistics == 'enable' ? 'checked' : '' }}
                                            onclick="accesspage({{ $user->id }}, this.name , '{{ $user->access_type }}')"
                                            {{ in_array($user->user_type, ['client', 'worker']) ? 'disabled' : '' }} />
                                        <label for="statistics_{{ $user->id }}">Statistics</label>
                                    </div>
                                </div>




                            </td>
                        </tr>
                        {{-- <tr>
                            <td></td>
                            <td>
                                <div class="access-buttons flex flex-wrap gap-2 sm:gap-2 mt-0 mb-1 p-0">
                                    <label
                                        class="ml-2 text-sm border rounded-xl px-4 py-1 bg-[#ffc107] border-[#ffc107]"><strong>User</strong></label>
                                </div>
                            </td>
                        </tr>
                        <tr class="border-b">
                            <td></td>
                            <td>
                                <div class="access-buttons text-xs flex flex-wrap gap-1 sm:gap-2">
                                    <div class="input-container mb-1">
                                        <input type="checkbox" id="user_dashboard_{{ $user->id }}"
                                            name="user_dashboard" {{ $user->user_dashboard == 'enable' ? 'checked' : '' }}
                                            onclick="accesspage({{ $user->id }}, this.name, '{{ $user->access_type }}')"
                                            {{ in_array($user->user_type, ['admin', 'super admin']) ? 'disabled' : '' }} />
                                        <label for="user_dashboard_{{ $user->id }}"
                                            class="border text-gray-500 border-gray-800 bg-white px-4 py-2 rounded-md md:w-auto">Dashboard</label>
                                    </div>
                                    <div class="input-container mb-1">
                                        <input type="checkbox" id="profile_{{ $user->id }}" name="profile"
                                            {{ $user->profile == 'enable' ? 'checked' : '' }}
                                            onclick="accesspage({{ $user->id }}, this.name, '{{ $user->access_type }}')"
                                            {{ in_array($user->user_type, ['admin', 'super admin']) ? 'disabled' : '' }} />
                                        <label for="profile_{{ $user->id }}"
                                            class="border text-gray-500 border-gray-300 bg-white px-4 py-2 rounded-md md:w-auto">Profile</label>
                                    </div>
                                    <div class="input-container mb-1">
                                        <input type="checkbox" id="goals_{{ $user->id }}" name="goals"
                                            {{ $user->goals == 'enable' ? 'checked' : '' }}
                                            onclick="accesspage({{ $user->id }}, this.name, '{{ $user->access_type }}')"
                                            {{ in_array($user->user_type, ['admin', 'super admin']) ? 'disabled' : '' }} />
                                        <label for="goals_{{ $user->id }}"
                                            class="border text-gray-500 border-gray-300 bg-white px-4 py-2 rounded-md md:w-auto">Goals</label>
                                    </div>
                                    <div class="input-container mb-1">
                                        <input type="checkbox" id="achievements_{{ $user->id }}" name="achievements"
                                            {{ $user->achievements == 'enable' ? 'checked' : '' }}
                                            onclick="accesspage({{ $user->id }}, this.name, '{{ $user->access_type }}')"
                                            {{ in_array($user->user_type, ['admin', 'super admin']) ? 'disabled' : '' }} />
                                        <label for="achievements_{{ $user->id }}"
                                            class="border text-gray-500 border-gray-800 bg-white px-2 py-1 sm:px-4 sm:py-2 rounded-md md:w-auto">Achievements</label>
                                    </div>
                                    <div class="input-container mb-1">
                                        <input type="checkbox" id="settings_{{ $user->id }}" name="settings"
                                            {{ $user->settings == 'enable' ? 'checked' : '' }}
                                            onclick="accesspage({{ $user->id }}, this.name, '{{ $user->access_type }}')"
                                            {{ in_array($user->user_type, ['admin', 'super admin']) ? 'disabled' : '' }} />
                                        <label for="settings_{{ $user->id }}"
                                            class="border text-gray-500 border-gray-300 bg-white px-4 py-2 rounded-md md:w-auto">Settings</label>
                                    </div>
                                </div>
                            </td>
                        </tr> --}}

                        <tr class="border-b flex flex-col sm:table-row">
                            <td class="py-2 pr-6  text-gray-700 sm:w-1/4">Access Type</td>
                            <td class="access-type flex items-center py-2  sm:pl-0 sm:w-3/4">
                                <input type="checkbox" id="readOnly" class="mr-2"
                                    {{ $user->access_type == 'read' ? 'checked' : '' }}
                                    onclick="updateAccessType(this, {{ $user->id }}, '{{ $user->access_type }}')">

                                <label for="readOnly">Read Only</label>
                            </td>
                        </tr>
                        <tr class="border-b flex flex-col sm:table-row">
                            <td class="py-2 pr-6  text-gray-700 sm:w-1/4"></td>
                            <td class="access-type flex items-center py-2  sm:pl-0 sm:w-3/4">
                                <div class="flex gap-6">

                                    <button id="editAction_{{ $user->id }}" class="text-black flex items-center"
                                        onclick="editAction({{ $user->id }},'{{ $user->access_type }}')">
                                        <i class="fas fa-edit mr-1"></i>
                                        <span>Edit</span>
                                    </button>
                                    <button id="saveAction_{{ $user->id }}" class="text-black items-center hidden"
                                        onclick="saveAction({{ $user->id }}, '{{ $user->access_type }}')">
                                        <i class="fas fa-save mr-1"></i>
                                        <span>Save</span>
                                    </button>

                                    <button id="deleteAction_{{ $user->id }}" class=" text-black flex items-center"
                                        onclick="deleteAction({{ $user->id }}, '{{ $user->access_type }}')">
                                        <i class="fa-solid fa-trash mr-1"></i>
                                        <i>Delete</i>
                                    </button>

                                    <button id="resetPinAction_{{ $user->id }}" class="text-black flex items-center"
                                        onclick="resetAction({{ $user->id }}, '{{ $user->access_type }}')">
                                        <i class="fa-solid fa-rotate-right mr-1"></i>
                                        <span>Reset Pin</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach
            @php
                $maxId = $users->max('id');
                $nextId = $maxId + 1;
                $minId = $users->min('id');

            @endphp
            {{-- Pagination --}}
            <div class="flex justify-center mt-4">
                <!-- Previous Button -->
                <a href="{{ route('previous.show', $minId) }}"
                    class="flex items-center justify-center px-3 h-8 me-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="w-3.5 h-3.5 me-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 5H1m0 0 4 4M1 5l4-4" />
                    </svg>
                    Previous
                </a>
                <a href="{{ route('user.show', ['user' => $nextId]) }}"
                    class="flex items-center justify-center px-3 h-8 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    Next
                    <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                </a>
            </div>

        </div>
    @endsection

</body>

</html>

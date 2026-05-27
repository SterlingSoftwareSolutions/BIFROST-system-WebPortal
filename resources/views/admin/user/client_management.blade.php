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
            <div class="breadcrumb text-lg mb-4">
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

          {{--
          <!-- Dashboard Summary Blocks -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
              <!-- Active Members Block -->
              <div class="bg-white rounded-md border border-gray-100 shadow-sm p-5 border-l-4 border-l-green-500 flex items-center justify-between">
                  <div>
                      <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Active Members</p>
                      <h3 class="text-2xl font-normal text-gray-800">{{ $activeMembersCount ?? 0 }}</h3>
                  </div>
                  <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-500">
                      <i class="bi bi-person-check text-xl"></i>
                  </div>
              </div>

              <!-- Inactive Members Block -->
              <div class="bg-white rounded-md border border-gray-100 shadow-sm p-5 border-l-4 border-l-red-500 flex items-center justify-between">
                  <div>
                      <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Inactive Members</p>
                      <h3 class="text-2xl font-normal text-gray-800">{{ $inactiveMembersCount ?? 0 }}</h3>
                  </div>
                  <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-500">
                      <i class="bi bi-person-x text-xl"></i>
                  </div>
              </div>

              <!-- Members Not Trained in 7 Days Block -->
              <div class="bg-white rounded-md border border-gray-100 shadow-sm p-5 border-l-4 border-l-yellow-400 flex items-center justify-between">
                  <div>
                      <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Not Trained (> 7 days)</p>
                      <h3 class="text-2xl font-normal text-gray-800">{{ $notTrainedCount ?? 0 }}</h3>
                  </div>
                  <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-500">
                      <i class="bi bi-exclamation-triangle text-xl"></i>
                  </div>
              </div>

              <!-- Revenue Block -->
              <div class="bg-white rounded-md border border-gray-100 shadow-sm p-5 border-l-4 border-l-blue-500 flex items-center justify-between">
                  <div>
                      <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Est. Monthly Revenue</p>
                      <h3 class="text-2xl font-normal text-gray-800">${{ number_format($estimatedRevenue ?? 0) }}</h3>
                  </div>
                  <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                      <i class="bi bi-currency-dollar text-xl"></i>
                  </div>
              </div>
          </div>
          --}}
                <div class="bg-white p-5 rounded-lg shadow-md">
                    <table class="w-full border-collapse mb-5 text-lg">
                        <thead>
                            <tr>
                                <th class="p-3 border-s-2 border-y-2 border-gray-300 bg-white text-left" dir="ltr">
                                    First Name</th>
                                <th class="p-3 border-y-2 border-gray-300 bg-white text-left">Last Name</th>
                                <th class="p-3 border-y-2 border-gray-300 bg-white text-left">Contact Number</th>
                                <th class="p-3 border-y-2 border-gray-300 bg-white text-left">Subscription Level</th>
                                <th class="p-3 border-y-2 border-gray-300 bg-white text-left relative">
                                    Subscription Start
                                    <span class="p-3 cursor-pointer relative" onclick="toggleDropdown(this)">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                        <!-- Dropdown menu -->
                                        <div class="hidden dropdown absolute mt-2 right-0 w-40 bg-white border border-gray-300 shadow-md rounded-md z-10">
                                            <ul class="text-lg">
                                                <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer" onclick="sortTable('on')">Subscription On</li>
                                                <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer" onclick="sortTable('off')">Subscription Off</li>
                                            </ul>
                                        </div>
                                    </span>
                                </th>

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

                {{--
                <div class="w-full lg:w-1/2">
                    <div class="mt-8 flex justify-between items-center mb-4">
                        <div class="text-base font-normal text-red-500 flex items-center">
                            <i class="bi bi-exclamation-circle mr-2"></i> Action Required: Not Trained in Over 7 Days
                        </div>
                    </div>

                    <div class="bg-white rounded-md mb-10 border border-red-100 shadow-sm overflow-hidden p-2">
                        @if(isset($notTrainedMembers) && $notTrainedMembers->count() > 0)
                            <table class="w-full text-sm text-left">
                                <thead class="bg-red-50 text-gray-800 font-semibold border border-red-50">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold">First Name</th>
                                        <th class="px-4 py-3 font-semibold">Last Name</th>
                                        <th class="px-4 py-3 font-semibold">Contact Number</th>
                                        <th class="px-4 py-3 font-semibold">Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notTrainedMembers as $inactiveMember)
                                        <tr class="bg-white border-b border-gray-100 hover:bg-gray-50">
                                            <td class="px-4 py-4 text-gray-700">{{ $inactiveMember->firstname }}</td>
                                            <td class="px-4 py-4 text-gray-700">{{ $inactiveMember->lastname }}</td>
                                            <td class="px-4 py-4 text-gray-700">{{ $inactiveMember->phone }}</td>
                                            <td class="px-4 py-4 text-gray-500">{{ $inactiveMember->email }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="p-8 text-center text-gray-500">
                                <i class="bi bi-emoji-smile text-4xl text-green-500 mb-3 block"></i>
                                <p class="text-base">All members have trained recently. Great job!</p>
                            </div>
                        @endif
                    </div>
                </div>
                --}}

            </div>
        </div>
        <script>
            // Toggle dropdown visibility
            function toggleDropdown(element) {
                const dropdown = element.querySelector('.dropdown');
                // Hide any other open dropdowns
                document.querySelectorAll('.dropdown').forEach((el) => {
                    if (el !== dropdown) el.classList.add('hidden');
                });
                // Toggle the clicked dropdown
                dropdown.classList.toggle('hidden');
            }

            // Sort table based on subscription status
            function sortTable(status) {
                const rows = Array.from(document.querySelectorAll('tbody tr'));
                const tbody = document.querySelector('tbody');
                const sortedRows = rows.sort((a, b) => {
                    const aStatus = a.querySelector('input[type="checkbox"]').checked;
                    const bStatus = b.querySelector('input[type="checkbox"]').checked;
                    if (status === 'on') {
                        return bStatus - aStatus; // Sort by subscription "On" first
                    } else {
                        return aStatus - bStatus; // Sort by subscription "Off" first
                    }
                });
                // Append sorted rows back to the table
                tbody.innerHTML = '';
                sortedRows.forEach((row) => tbody.appendChild(row));
            }

        </script>
        <style>
            .dropdown.hidden {
                display: none;
            }

            .dropdown {
                display: block;
            }
        </style>

    @endsection



</body>

</html>

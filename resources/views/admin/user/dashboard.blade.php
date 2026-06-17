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
    @extends('layout.layout')
    @section('content')

         <!-- Main content (Dashboard) -->
         <div class="container transition-width mt-24 flex-grow mx-4" id="container">
          <div class="breadcrumb text-lg mb-4">
              <a href="#" class="text-gray-500 no-underline hover:underline">Home</a> / <span><strong> Dashboard
                  </strong></span>
          </div>

          <!-- Dashboard Summary Blocks -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
              <!-- Active Members Block -->
              <div class="bg-white p-5 rounded-lg shadow-md flex items-center justify-between">
                  <div>
                      <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Active Members</p>
                      <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $activeMembersCount ?? 0 }}</h3>
                  </div>
                  <div class="p-3">
                      <i class="bi bi-person-check text-[#fd8300] text-3xl"></i>
                  </div>
              </div>

              <!-- Inactive Members Block -->
              <div class="bg-white p-5 rounded-lg shadow-md flex items-center justify-between">
                  <div>
                      <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Inactive Members</p>
                      <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $inactiveMembersCount ?? 0 }}</h3>
                  </div>
                  <div class="p-3">
                      <i class="bi bi-person-x text-[#fd8300] text-3xl"></i>
                  </div>
              </div>

              <!-- Members Not Trained in 7 Days Block -->
              <div class="bg-white p-5 rounded-lg shadow-md flex items-center justify-between">
                  <div>
                      <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Not Trained (> 7 days)</p>
                      <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $notTrainedCount ?? 0 }}</h3>
                  </div>
                  <div class="p-3">
                      <i class="bi bi-exclamation-triangle text-[#fd8300] text-3xl"></i>
                  </div>
              </div>

              <!-- Revenue Block -->
              <div class="bg-white p-5 rounded-lg shadow-md flex items-center justify-between">
                  <div>
                      <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Est. Monthly Revenue</p>
                      <h3 class="text-3xl font-bold text-gray-800 mt-2">${{ number_format($estimatedRevenue ?? 0) }}</h3>
                  </div>
                  <div class="p-3">
                      <i class="bi bi-currency-dollar text-[#fd8300] text-3xl"></i>
                  </div>
              </div>
          </div>

          <div class="w-full lg:w-1/2">
              <div class="mt-8 flex justify-between items-center mb-4">
                  <div class="text-2xl font-medium font-source-sans text-red-600 flex items-center">
                      <i class="bi bi-exclamation-circle mr-2"></i> Action Required: Not Trained in Over 7 Days
                  </div>
              </div>

              <div class="bg-white p-5 rounded-lg shadow-md mb-10">
                  @if(isset($notTrainedMembers) && $notTrainedMembers->count() > 0)
                      <table class="w-full border-collapse mb-5 text-lg">
                          <thead>
                              <tr>
                                  <th class="p-3 border-s-2 border-y-2 border-gray-300 bg-white text-left">First Name</th>
                                  <th class="p-3 border-y-2 border-gray-300 bg-white text-left">Last Name</th>
                                  <th class="p-3 border-y-2 border-gray-300 bg-white text-left">Contact Number</th>
                                  <th class="p-3 border-y-2 border-gray-300 bg-white text-left">Email</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach ($notTrainedMembers as $inactiveMember)
                                  <tr class="bg-gray-100">
                                      <td class="p-3 border-s-2 border-y-2 border-gray-300 text-left">{{ $inactiveMember->firstname }}</td>
                                      <td class="p-3 border-y-2 border-gray-300 text-left">{{ $inactiveMember->lastname }}</td>
                                      <td class="p-3 border-y-2 border-gray-300 text-left">{{ $inactiveMember->phone }}</td>
                                      <td class="p-3 border-y-2 border-gray-300 text-left">{{ $inactiveMember->email }}</td>
                                  </tr>
                              @endforeach
                          </tbody>
                      </table>
                  @else
                      <div class="p-8 text-center text-gray-500">
                          <i class="bi bi-emoji-smile text-4xl text-green-500 mb-3 block"></i>
                          <p class="text-xl">All members have trained recently. Great job!</p>
                      </div>
                  @endif
              </div>
          </div>

      </div>
    @endsection
</body>
</html>

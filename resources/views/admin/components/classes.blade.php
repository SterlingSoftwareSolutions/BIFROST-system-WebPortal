<div class="max-w-4xl mx-auto bg-white rounded-2xl pr-5">
    <h1 class="text-2xl font-bold mb-4 text-center">Classes</h1>

    <div class="border rounded-2xl overflow-hidden">
      <table class="min-w-full table-auto text-left border-collapse">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-4 py-2">Time</th>
            <th class="px-4 py-2">Duration</th>
            <th class="px-4 py-2">Spots</th>
            <th class="px-4 py-2">Workout Assigned</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody class="bg-white" id="classesTableBody">
            <input type="text" name="selectdatecla" id="selectdateclae" hidden >
            {{-- @php
                $selectedDate = "<script>document.getElementById('selectdateclae').value</script>";
            @endphp

            @foreach($classes as $class)
                @if($class->date == $selectedDate)
                <tr class="border-t">
                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($class->time)->format('g:i a') }}</td>
                <td class="px-4 py-2">{{ $class->duration }}<span class="ml-2">hr</span></td>
                <td class="px-4 py-2">{{ $class->spots }}</td>
                <td class="px-4 py-2">
                    <form method="POST" action="{{ route('classes.toggleWorkout', $class->id) }}">
                        @csrf
                        <label class="inline-flex items-center cursor-pointer">
                        <input type="hidden" name="workout_assigned_{{ $class->id }}" value="0">
                        <input type="checkbox"
                                class="sr-only peer"
                                name="workout_assigned_{{ $class->id }}"
                                value="1"
                                onchange="this.form.submit()"
                                {{ $class->workout_assigned ? 'checked' : '' }} >
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                        </label>
                    </form>
                </td>
                <td class="px-4 py-2">
                    <form method="POST" action="{{ route('classes.delete', $class->id) }}" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500">X</button>
                    </form>
                </td>
                </tr>
                @endif
          @endforeach --}}
        </tbody>
      </table>

      <div class="p-4 text-right">
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          + Add Class
        </button>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg">
      <h2 class="text-xl font-bold mb-4">Add New Class</h2>
      <form method="POST" action="{{ route('classes.store') }}" class="space-y-4">
        @csrf
        <input type="text" name="selectdatecla" id="selectdatecla" hidden>
        <div>
            <label class="block font-medium">Time</label>
            <input name="time" type="time" required class="w-full border rounded px-3 py-2 mt-1" />
        </div>
        <div>
            <label class="block font-medium">Duration</label>
            <div class="flex items-center border rounded mt-1 px-3 py-2">
                <input name="duration" type="number" value="1" required class="w-full focus:outline-none" />
                <span class="ml-2 text-gray-500">hr</span>
            </div>
        </div>
        <div>
            <label class="block font-medium">Spots</label>
            <input name="spots" type="number" value="20" required class="w-full border rounded px-3 py-2 mt-1" />
        </div>
        <div class="flex items-center">
            <input name="workout_assigned" type="checkbox" id="workout_assigned" class="mr-2" />
            <label for="workout_assigned" class="text-sm">Workout Assigned</label>
        </div>
        <div class="flex justify-end space-x-2 mt-4">
            <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
        </div>
    </form>

    </div>
  </div>

  <script>
    function getdateName(dayName){
         var selectdateName = dayName;
        // console.log("nameeeeeeeeeeee",selectdateName);
         document.getElementById('selectdatecla').value = selectdateName;
        // document.getElementById('selectdateclae').value = selectdateName;

        console.log("Fetching classes for:", dayName);

        fetch(`/get-classes-by-day?day=${encodeURIComponent(dayName)}`)
            .then(response => response.json())
            .then(classes => {
                const tbody = document.getElementById('classesTableBody');
                tbody.innerHTML = ''; // Clear previous rows

                if (classes.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">No classes found</td></tr>';
                    return;
                }

                classes.forEach(cls => {
                    const time = new Date('1970-01-01T' + cls.time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                    const row = `
                        <tr class="border-t">
                            <td class="px-4 py-2">${time}</td>
                            <td class="px-4 py-2">${cls.duration} <span class="ml-2">hr</span></td>
                            <td class="px-4 py-2">${cls.spots}</td>
                            <td class="px-4 py-2">
                                <form method="POST" action="/classes/toggle-workout/${cls.id}">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="workout_assigned_${cls.id}" value="0">
                                        <input type="checkbox"
                                            class="sr-only peer"
                                            name="workout_assigned_${cls.id}"
                                            value="1"
                                            onchange="this.form.submit()"
                                            ${cls.workout_assigned ? 'checked' : ''}>
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                                    </label>
                                </form>
                            </td>
                            <td class="px-4 py-2">
                                <form method="POST" action="/classes/delete/${cls.id}" onsubmit="return confirm('Are you sure?');">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="text-red-500">X</button>
                                </form>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            })
            .catch(error => {
                console.error("Error fetching classes:", error);
        });
    }

  </script>

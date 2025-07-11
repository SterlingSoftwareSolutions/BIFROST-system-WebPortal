<div class="max-w-4xl mx-auto bg-white rounded-2xl pr-5">
    <h1 class="text-2xl font-bold mb-4 text-center">Classes</h1>

    <div class="border rounded-2xl overflow-hidden">
      <table class="min-w-full table-fixed text-left border-collapse">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-4 py-2 w-[15%]">Time</th>
            <th class="px-4 py-2 w-[15%]">Duration</th>
            <th class="px-4 py-2 w-[15%]">Spots</th>
            <th class="px-4 py-2 w-[45%]">Workout Assigned</th>
            <th class="px-4 py-2 w-[15%]"></th>
          </tr>
        </thead>
        <tbody class="bg-white" id="classesTableBody">
            <input type="text" name="selectdatecla" id="selectdateclae" hidden >
            <input type="text" id="selected_class_id" hidden >
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
    let classdate = null;
    function getdateName(dayName){
    const selectedStoredClassId = localStorage.getItem("selected_class_id");
    console.log('dddddddddd',localStorage.getItem("selected_class_id"));

    document.getElementById('selectdatecla').value = dayName;
    classdate = dayName;
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

                const isSelected = selectedStoredClassId && selectedStoredClassId == cls.id;
                const borderStyle = isSelected ? 'border: 2px solid #22c55e; border-radius: 10px;' : '';

                const row = `
                    <tr class="border-t class-row cursor-pointer" data-id="${cls.id}" onclick="selectClassRow(this)" style="${borderStyle}">
                        <td class="px-4 py-2 w-[15%]">${time}</td>
                        <td class="px-4 py-2 w-[15%]">${cls.duration} <span class="ml-2">hr</span></td>
                        <td class="px-4 py-2 w-[15%]">${cls.spots}</td>
                        <td class="px-4 py-2 flex items-center space-x-2 w-[45%]">
                            <img src="/icon/warmup.png" class="${cls.is_warmup ? 'ring-2 ring-green-500 rounded' : ''} w-6 h-6" title="Warmup">
                            <img src="/icon/strength.png" class="${cls.is_strength ? 'ring-2 ring-green-500 rounded' : ''} w-6 h-6" title="Strength">
                            <img src="/icon/weightlifting.png" class="${cls.is_weightlifting ? 'ring-2 ring-green-500 rounded' : ''} w-6 h-6" title="Weightlifting">
                            <img src="/icon/conditioning.png" class="${cls.is_conditioning ? 'ring-2 ring-green-500 rounded' : ''} w-6 h-6" title="Conditioning">
                            <img src="/icon/test.png" class="${cls.is_test ? 'ring-2 ring-green-500 rounded' : ''} w-6 h-6" title="Test">
                        </td>
                        <td class="px-2 py-2 w-[15%]">
                                <button class="text-red-500 ml-4" onclick="deleteClass(${cls.id}, '${dayName}')">X</button>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });

            // Automatically assign the selected row reference
            const selectedRow = document.querySelector(`tr[data-id="${selectedStoredClassId}"]`);
            if (selectedRow) {
                selectedClassRow = selectedRow;
                selectedClassId = selectedStoredClassId;
            }
        })
        .catch(error => {
            console.error("Error fetching classes:", error);
        });
}

    let selectedClassRow = null;
    let selectedClassId = null;

    function selectClassRow(rowElement) {
        const clickedClassId = rowElement.getAttribute('data-id');

        if (selectedClassId === clickedClassId) {
            // Deselect same row
            rowElement.style.border = '1px solid transparent';
            rowElement.style.borderRadius = '0';
            console.log("Deselecting class ID:", selectedClassId);
            localStorage.removeItem("selected_class_id");
            selectedClassRow = null;
            selectedClassId = null;
            return;
        }

        // Deselect previous
        if (selectedClassRow) {
            selectedClassRow.style.border = '1px solid transparent';
            selectedClassRow.style.borderRadius = '0';
        }

        // Select new row
        rowElement.style.border = '2px solid #22c55e';
        rowElement.style.borderRadius = '10px';
        selectedClassRow = rowElement;
        selectedClassId = clickedClassId;

        console.log("Selected Class ID:", selectedClassId);
        localStorage.setItem("selected_class_id", selectedClassId);
    }

    function deleteClass(classId, date) {
        if (!confirm("Are you sure you want to delete this class?")) return;
        
        console.log('Deleting class on date:', date);
        $.ajax({
            url: `/classes/${classId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Blade directive for CSRF
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    console.log("seeeeeeeeeeeeeeeeeeeeeeeee",date);
                    getdateName(date); // Refresh table
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Delete request failed.');
            }
        });
    }


  </script>

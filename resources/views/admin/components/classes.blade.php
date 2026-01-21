<style>
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="max-w-4xl mx-auto bg-white rounded-2xl pr-5">
  <h1 class="text-2xl font-bold mb-4 text-center">Classes</h1>

  <!-- NOTE: removed overflow-hidden here so icon rings/outlines won't get clipped -->
  <div class="border rounded-2xl">
    <div class="overflow-x-auto rounded-2xl">
      <table class="min-w-full table-fixed text-left border-collapse">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-4 py-2 w-[15%] whitespace-nowrap">Time</th>
            <th class="px-4 py-2 w-[15%]">Duration</th>
            <th class="px-4 py-2 w-[15%]">Spots</th>
            <th class="px-4 py-2 w-[45%]">Workout Assigned</th>
            <th class="px-4 py-2 w-[10%] text-right whitespace-nowrap"></th>
          </tr>
        </thead>

        <tbody class="bg-white" id="classesTableBody">
          <input type="text" name="selectdatecla" id="selectdatecla" hidden>
          <input type="text" id="selected_class_id" hidden>
        </tbody>
      </table>
    </div>

    <div class="p-4 text-right">
      <button
        type="button"
        onclick="openAddModal()"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        + Add Class
      </button>
    </div>
  </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[999]">
  <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg">
    <h2 class="text-xl font-bold mb-4">Add New Class</h2>

    <form method="POST" action="{{ route('classes.store') }}" class="space-y-4">
      @csrf
      <input type="text" name="selectdatecla" id="selectdatecla_modal" hidden>

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

      <!-- Repeat On UI -->
      <div class="mt-4 repeat-section">
        <label class="block font-medium mb-2">Repeat On (Optional)</label>

        <div class="flex items-center gap-2">
          @foreach ([
              'Mon' => 'M',
              'Tue' => 'T',
              'Wed' => 'W',
              'Thu' => 'T',
              'Fri' => 'F',
              'Sat' => 'S',
              'Sun' => 'S',
          ] as $day => $label)
            <label class="cursor-pointer">
              <input type="checkbox" name="days[]" value="{{ $day }}" class="peer sr-only">
              <span
                class="w-8 h-8 rounded-full border flex items-center justify-center text-sm
                       bg-white text-gray-700 border-gray-300
                       peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600
                       hover:border-blue-400 transition"
                title="{{ $day }}"
              >
                {{ $label }}
              </span>
            </label>
          @endforeach
        </div>

        <p class="text-xs text-gray-500 mt-2">
          If selected, class will repeat on these days until the end of the year.
        </p>
      </div>

      <div class="flex justify-end space-x-2 mt-4">
        <button
          type="button"
          onclick="closeModal()"
          class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
        >
          Cancel
        </button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Save
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  let classdate = null;
  let selectedClassRow = null;
  let selectedClassId = null;

  function getdateName(dayName) {
    const selectedStoredClassId = localStorage.getItem("selected_class_id");

    // set hidden date inputs
    document.getElementById('selectdatecla').value = dayName;
    document.getElementById('selectdatecla_modal').value = dayName;
    classdate = dayName;

    fetch(`/get-classes-by-day?day=${encodeURIComponent(dayName)}`)
      .then(response => response.json())
      .then(classes => {
        const tbody = document.getElementById('classesTableBody');

        // rebuild hidden inputs (so tbody clearing doesn't remove them permanently)
        tbody.innerHTML = `
          <input type="text" name="selectdatecla" id="selectdatecla" hidden value="${dayName}">
          <input type="text" id="selected_class_id" hidden value="${selectedStoredClassId || ''}">
        `;

        if (!classes || classes.length === 0) {
          tbody.insertAdjacentHTML('beforeend',
            '<tr><td colspan="5" class="text-center py-4">No classes found</td></tr>'
          );
          return;
        }

        classes.forEach(cls => {
          let time = new Date('1970-01-01T' + cls.time)
            .toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true })
            .toLowerCase()
            .replace(' ', '&nbsp;');

          const isSelected = selectedStoredClassId && selectedStoredClassId == cls.id;
          const outlineStyle = isSelected ? 'outline:2px solid #22c55e; outline-offset:-2px;' : '';

          const row = `
            <tr class="border-t class-row cursor-pointer"
                data-id="${cls.id}"
                onclick="selectClassRow(this)"
                style="${outlineStyle}">
              <td class="px-4 py-2 w-[15%] whitespace-nowrap">${time}</td>
              <td class="px-4 py-2 w-[15%]">${cls.duration} <span class="ml-2">hr</span></td>
              <td class="px-4 py-2 w-[15%]">${cls.spots}</td>

              <!-- Icons (single row, scroll X, allow Y so rings won't clip) -->
              <td class="px-4 py-2 w-[45%] pr-4">
                <div class="flex flex-nowrap items-center gap-2 overflow-x-auto overflow-y-visible whitespace-nowrap no-scrollbar py-1">
                  <div class="p-1">
                    <img src="/icon/warmup.png"
                      class="${cls.is_warmup ? 'ring-2 ring-green-500 rounded' : ''} w-8 h-8"
                      title="Warmup">
                  </div>
                  <div class="p-1">
                    <img src="/icon/strength.png"
                      class="${cls.is_strength ? 'ring-2 ring-green-500 rounded' : ''} w-8 h-8"
                      title="Strength">
                  </div>
                  <div class="p-1">
                    <img src="/icon/weightlifting.png"
                      class="${cls.is_weightlifting ? 'ring-2 ring-green-500 rounded' : ''} w-8 h-8"
                      title="Weightlifting">
                  </div>
                  <div class="p-1">
                    <img src="/icon/conditioning.png"
                      class="${cls.is_conditioning ? 'ring-2 ring-green-500 rounded' : ''} w-8 h-8"
                      title="Conditioning">
                  </div>
                  <div class="p-1">
                    <img src="/icon/test.png"
                      class="${cls.is_test ? 'ring-2 ring-green-500 rounded' : ''} w-8 h-8"
                      title="Test">
                  </div>
                </div>
              </td>

              <!-- Edit + Delete -->
              <td class="px-4 py-2 w-[10%] text-right whitespace-nowrap">
                <div class="inline-flex items-center gap-3">
                  <button type="button"
                    class="text-gray-600 hover:text-blue-600 focus:outline-none"
                    title="Edit"
                    onclick="event.stopPropagation(); openEditClassModal(${cls.id});">
                    <svg class="feather feather-edit" fill="none" height="18" width="18"
                      stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>

                  <button type="button"
                    class="text-red-500 font-bold"
                    title="Delete"
                    onclick="event.stopPropagation(); deleteClass(${cls.id}, '${dayName}')">
                    X
                  </button>
                </div>
              </td>
            </tr>
          `;
          tbody.insertAdjacentHTML('beforeend', row);
        });

        // Restore selection references
        const selectedRow = document.querySelector(`tr[data-id="${selectedStoredClassId}"]`);
        if (selectedRow) {
          selectedClassRow = selectedRow;
          selectedClassId = selectedStoredClassId;
        } else {
          selectedClassRow = null;
          selectedClassId = null;
        }
      })
      .catch(error => console.error("Error fetching classes:", error));
  }

  function selectClassRow(rowElement) {
    const clickedClassId = rowElement.getAttribute('data-id');

    // toggle off if same
    if (selectedClassId === clickedClassId) {
      rowElement.style.outline = 'none';
      localStorage.removeItem("selected_class_id");
      selectedClassRow = null;
      selectedClassId = null;
      return;
    }

    // clear old
    if (selectedClassRow) selectedClassRow.style.outline = 'none';

    // set new
    rowElement.style.outline = '2px solid #22c55e';
    rowElement.style.outlineOffset = '-2px';

    selectedClassRow = rowElement;
    selectedClassId = clickedClassId;
    localStorage.setItem("selected_class_id", selectedClassId);
  }

  function deleteClass(classId, dayName) {
    if (!confirm("Are you sure you want to delete this class?")) return;

    $.ajax({
      url: `/classes/${classId}`,
      type: 'DELETE',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      success: function(response) {
        if (response.success) {
          alert(response.message);

          // If the deleted class was selected, clear it
          const stored = localStorage.getItem("selected_class_id");
          if (stored && stored == classId) localStorage.removeItem("selected_class_id");

          getdateName(dayName);
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

  // Placeholder edit handler
  let isEditMode = false;

  function openEditClassModal(classId) {
    // Ensure hidden date is set from current view
    const currentDayName = document.getElementById('selectdatecla').value;
    document.getElementById('selectdatecla_modal').value = currentDayName;

    fetch(`/classes/${classId}/edit`) 
        .then(res => res.json())
        .then(data => {
            // Populate Fields
            const timeInput = document.querySelector('input[name="time"]');
            timeInput.value = data.time.toLowerCase();
            timeInput.readOnly = true;

            document.querySelector('input[name="duration"]').value = data.duration;
            document.querySelector('input[name="spots"]').value = data.spots;
            
            // Set Edit Mode
            isEditMode = true;
            
            // Update UI
            document.querySelector('#addModal h2').innerText = "Edit Class";
            const submitBtn = document.querySelector('#addModal button[type="submit"]');
            submitBtn.innerText = "Update";
            
            // Disable Repeat Section
            const repeatSection = document.querySelector('.mt-4.repeat-section'); // Will add this class to div
            if(repeatSection) {
                repeatSection.style.pointerEvents = 'none';
                repeatSection.style.opacity = '0.5';
                repeatSection.querySelectorAll('input').forEach(el => el.checked = false);
            }

            // Update Form Action
            const form = document.querySelector('#addModal form');
            form.action = `/classes/${classId}`; 
            
            // Add hidden Request Method Spoofing
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);
            } else {
                methodInput.value = 'PUT';
            }

            document.getElementById('addModal').classList.remove('hidden');
        })
        .catch(err => console.error(err));
  }

  function openAddModal() {
      // 1. Reset form/state (clears inputs including hidden ones)
      closeModal(); 
      
      // 2. Re-populate the hidden date input from the current page state
      const currentDay = document.getElementById('selectdatecla') ? document.getElementById('selectdatecla').value : '';
      if(currentDay) {
          document.getElementById('selectdatecla_modal').value = currentDay;
      } else {
          console.warn("No date selected found in 'selectdatecla'.");
      }

      // 3. Show modal
      document.getElementById('addModal').classList.remove('hidden');
  }

  function closeModal() {
    isEditMode = false;
    
    document.querySelector('#addModal h2').innerText = "Add New Class";
    document.querySelector('#addModal button[type="submit"]').innerText = "Save";
    document.querySelector('#addModal form').reset();
    
    // Reset Time Input
    const timeInput = document.querySelector('input[name="time"]');
    timeInput.readOnly = false;

    // Reset Repeat Section
    const repeatSection = document.querySelector('.mt-4.repeat-section');
    if(repeatSection) {
        repeatSection.style.pointerEvents = 'auto';
        repeatSection.style.opacity = '1';
    }

    // Reset Form Action
    const form = document.querySelector('#addModal form');
    form.action = "{{ route('classes.store') }}";
    const methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();
    
    document.getElementById('addModal').classList.add('hidden');
  }
</script>

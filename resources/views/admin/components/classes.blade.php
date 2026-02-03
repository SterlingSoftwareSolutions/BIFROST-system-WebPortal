<style>
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="max-w-4xl mx-auto bg-white rounded-2xl pr-5">
  <h1 class="text-2xl font-bold mb-4 text-center">Classes</h1>

  <div class="border rounded-2xl">
    <div class="overflow-x-auto rounded-2xl">
      <table class="min-w-full table-fixed text-left border-collapse">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-4 py-2 w-[15%] whitespace-nowrap">Time</th>
            <th class="px-4 py-2 w-[15%]">Duration</th>
            <th class="px-4 py-2 w-[10%]">Spots</th>
            <th class="px-4 py-2 w-[50%]">Workout Assigned</th>
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

        <!-- Custom Time Picker Wrapper -->
        <div class="relative w-full mt-1" id="customTimePicker">
            <!-- Hidden input for form submission (24h format) -->
            <input type="hidden" name="time" id="timeInput" required>

            <!-- Visible input for user interaction -->
            <input
                type="text"
                id="timeDisplay"
                class="w-full border rounded px-3 py-2 cursor-pointer focus:outline-none focus:border-blue-500 bg-white"
                placeholder="--:-- --"
                readonly
                onclick="toggleTimePicker()"
            >

            <!-- Dropdown Container -->
            <div id="timeDropdown" class="hidden absolute top-full left-0 mt-1 w-[200px] bg-white border border-gray-200 rounded-lg shadow-xl z-50 overflow-hidden">

                <!-- Columns Container -->
                <div class="flex h-64 text-sm">
                    <!-- Hours Column -->
                    <div class="w-1/3 overflow-y-auto no-scrollbar border-r border-gray-100" id="hourList">
                        <!-- Generated via JS -->
                    </div>

                    <!-- Minutes Column -->
                    <div class="w-1/3 overflow-y-auto no-scrollbar border-r border-gray-100" id="minuteList">
                        <!-- Generated via JS -->
                    </div>

                    <!-- Period Column -->
                    <div class="w-1/3 overflow-y-auto no-scrollbar bg-gray-50" id="periodList">
                        <div class="p-2 text-center cursor-pointer hover:bg-gray-100 py-3" onclick="selectPeriod('AM')" data-val="AM">AM</div>
                        <div class="p-2 text-center cursor-pointer hover:bg-gray-100 py-3" onclick="selectPeriod('PM')" data-val="PM">PM</div>
                    </div>
                </div>
            </div>
        </div>
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

          const row = `
            <tr class="border-t class-row cursor-pointer"
                data-id="${cls.id}"
                onclick="selectClassRow(this)">
              <td class="px-4 py-2 w-[15%] whitespace-nowrap">${time}</td>
              <td class="px-4 py-2 w-[15%]">${cls.duration} <span class="ml-2">hr</span></td>
              <td class="px-4 py-2 w-[10%]">${cls.spots}</td>

              <!-- Icons (single row, scroll X, allow Y so rings won't clip) -->
              <td class="m-[-10px] py-2 w-[50%] pr-4">
                <div class="flex flex-nowrap items-center gap-2 overflow-x-auto overflow-y-visible whitespace-nowrap no-scrollbar py-1">
                  
                  <!-- Warmup -->
                  <div class="p-1">
                    <img src="/icon/warmup.png"
                      class="w-8 h-8 border-2 rounded-md p-0.5 ${cls.is_warmup ? 'border-green-500' : 'border-transparent'}"
                      title="Warmup">
                  </div>

                  <!-- Strength -->
                  <div class="p-1">
                    <img src="/icon/strength.png"
                      class="w-8 h-8 border-2 rounded-md p-0.5 ${cls.is_strength ? 'border-green-500' : 'border-transparent'}"
                      title="Strength">
                  </div>

                  <!-- Weightlifting -->
                  <div class="p-1">
                    <img src="/icon/weightliftingnew.png"
                      class="w-8 h-8 border-2 rounded-md p-0.5 ${cls.is_weightlifting ? 'border-green-500' : 'border-transparent'}"
                      title="Weightlifting">
                  </div>

                  <!-- Conditioning -->
                  <div class="p-1">
                    <img src="/icon/conditioning.png"
                      class="w-8 h-8 border-2 rounded-md p-0.5 ${cls.is_conditioning ? 'border-green-500' : 'border-transparent'}"
                      title="Conditioning">
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
      // rowElement.style.outline = 'none';
      localStorage.removeItem("selected_class_id");
      selectedClassRow = null;
      selectedClassId = null;
      return;
    }

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
            // Parse 24h time "HH:mm:ss" -> 12h format
            if(data.time) {
                const [h, m] = data.time.split(':');
                let hour = parseInt(h);
                let period = 'AM';
                if(hour >= 12) {
                    period = 'PM';
                    if(hour > 12) hour -= 12;
                }
                if(hour === 0) hour = 12;

                currentHour = hour.toString().padStart(2, '0');
                currentMinute = m;
                currentPeriod = period;

                updatePickerUI();
            }

            // Disable Time Editing in Edit Mode (matching previous behavior)
             const timeDisplay = document.getElementById('timeDisplay');
             timeDisplay.classList.add('bg-gray-100', 'cursor-not-allowed');
             timeDisplay.onclick = null; // Remove click handler

            document.querySelector('input[name="duration"]').value = data.duration;
            document.querySelector('input[name="spots"]').value = data.spots;

            // Set Edit Mode
            isEditMode = true;

            // Update UI
            document.querySelector('#addModal h2').innerText = "Edit Class";
            const submitBtn = document.querySelector('#addModal button[type="submit"]');
            submitBtn.innerText = "Update";

            // Enable Repeat Section & Pre-select Current Day
            const repeatSection = document.querySelector('.mt-4.repeat-section');
            if(repeatSection) {
                repeatSection.style.pointerEvents = 'auto';
                repeatSection.style.opacity = '1';
                
                // Reset checks first
                repeatSection.querySelectorAll('input').forEach(el => el.checked = false);

                // Parse Date to get Day (e.g. "20/05/26 Monday")
                if (data.date) {
                    const parts = data.date.split(' ');
                    if (parts.length >= 2) {
                        const dayFull = parts[1]; // "Monday"
                        // Map to Short (Mon, Tue...) matches value="{{ $day }}"
                        const dayMap = {
                            'Monday': 'Mon',
                            'Tuesday': 'Tue',
                            'Wednesday': 'Wed',
                            'Thursday': 'Thu',
                            'Friday': 'Fri',
                            'Saturday': 'Sat',
                            'Sunday': 'Sun'
                        };
                        const shortDay = dayMap[dayFull];
                        if (shortDay) {
                            const checkbox = repeatSection.querySelector(`input[value="${shortDay}"]`);
                            if (checkbox) checkbox.checked = true;
                        }
                    }
                }
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

      // Reset Picker to Default & Enable
      currentHour = '12';
      currentMinute = '00';
      currentPeriod = 'AM';
      updatePickerUI();

      const timeDisplay = document.getElementById('timeDisplay');
      timeDisplay.classList.remove('bg-gray-100', 'cursor-not-allowed');
      timeDisplay.onclick = toggleTimePicker; // Re-bind click handler

      // 3. Show modal
      document.getElementById('addModal').classList.remove('hidden');
  }

  function closeModal() {
    isEditMode = false;

    document.querySelector('#addModal h2').innerText = "Add New Class";
    document.querySelector('#addModal button[type="submit"]').innerText = "Save";
    document.querySelector('#addModal form').reset();

    // Reset Time Input & Picker State
    const timeInput = document.querySelector('input[name="time"]'); // This is the hidden one now
    if(timeInput) timeInput.value = '';

    document.getElementById('timeDisplay').value = '';

    // Reset picker defaults
    currentHour = '12';
    currentMinute = '00';
    currentPeriod = 'AM';
    updatePickerUI();

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
    document.getElementById('timeDropdown').classList.add('hidden');
  }

  // --- Custom Time Picker Logic ---

  let currentHour = '12';
  let currentMinute = '00';
  let currentPeriod = 'AM';

  document.addEventListener('DOMContentLoaded', () => {
    initTimePicker();

    // Close picker when clicking outside
    document.addEventListener('click', (e) => {
        const picker = document.getElementById('customTimePicker');
        const dropdown = document.getElementById('timeDropdown');
        if (picker && !picker.contains(e.target) && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
        }
    });
  });

  function initTimePicker() {
    const hourList = document.getElementById('hourList');
    const minuteList = document.getElementById('minuteList');

    if(!hourList || !minuteList) return;

    // Populate Hours (01-12)
    hourList.innerHTML = '';
    for(let i=1; i<=12; i++) {
        const val = i.toString().padStart(2, '0');
        const div = document.createElement('div');
        div.className = 'p-2 text-center cursor-pointer hover:bg-gray-100';
        div.innerText = val;
        div.onclick = () => selectHour(val);
        div.setAttribute('data-val', val);
        hourList.appendChild(div);
    }

    // Populate Minutes (00-59)
    minuteList.innerHTML = '';
    for(let i=0; i<60; i++) {
        const val = i.toString().padStart(2, '0');
        const div = document.createElement('div');
        div.className = 'p-2 text-center cursor-pointer hover:bg-gray-100';
        div.innerText = val;
        div.onclick = () => selectMinute(val);
        div.setAttribute('data-val', val);
        minuteList.appendChild(div);
    }

    updatePickerUI();
  }

  function toggleTimePicker() {
    document.getElementById('timeDropdown').classList.toggle('hidden');
    scrollToSelection();
  }

  function selectHour(val) {
    currentHour = val;
    updatePickerUI();
    scrollToSelection();
  }

  function selectMinute(val) {
    currentMinute = val;
    updatePickerUI();
    scrollToSelection();
  }

  function selectPeriod(val) {
    currentPeriod = val;
    updatePickerUI();
  }

  function updatePickerUI() {
    // Highlight Lists
    highlightList('hourList', currentHour);
    highlightList('minuteList', currentMinute);
    highlightList('periodList', currentPeriod);

    // Update Inputs
    const displayVal = `${currentHour}:${currentMinute} ${currentPeriod}`;
    document.getElementById('timeDisplay').value = displayVal;

    // Convert to 24h for hidden input
    let h = parseInt(currentHour);
    if (currentPeriod === 'PM' && h !== 12) h += 12;
    if (currentPeriod === 'AM' && h === 12) h = 0;
    const hStr = h.toString().padStart(2, '0');
    document.getElementById('timeInput').value = `${hStr}:${currentMinute}`;
  }

  function highlightList(listId, val) {
    const list = document.getElementById(listId);
    if(!list) return;
    Array.from(list.children).forEach(child => {
        if(child.getAttribute('data-val') === val) {
            child.classList.add('bg-blue-600', 'text-white');
            child.classList.remove('hover:bg-gray-100');
        } else {
            child.classList.remove('bg-blue-600', 'text-white');
            child.classList.add('hover:bg-gray-100');
        }
    });
  }

  function scrollToSelection() {
    setTimeout(() => {
        ['hourList', 'minuteList'].forEach(id => {
            const list = document.getElementById(id);
            const selected = list.querySelector('.bg-blue-600');
            if(selected) {
                list.scrollTop = selected.offsetTop - list.offsetTop - (list.clientHeight / 2) + (selected.clientHeight / 2);
            }
        });
    }, 0);
  }
</script>

<div class="duplicateUi text-base">
    <div class="ui-block flex flex-col text-lg p-2  mr-8 rounded-md gap-4 mb-4 ">
        <div class="flex gap-5 justify-between">
            {{-- Serach Section --}}
            <div class="flex-col w-full">
                <div class="bg-gray-50 p-2 pt-0">
                    <div class="flex justify-center text-center items-center font-bold mb-3 text-2xl">Workout List</div>
                    <div class="flex items-center space-x-2 flex-nowrap">

                        <!-- Category Field -->
                        <div class="flex items-center space-x-1 flex-[1] min-w-[120px]">
                            <label for="categoryw_2" class="w-15 text-xs">Category</label>
                            <select id="categoryw_2" name="categoryw_2" onchange="// getworkoutw(this)"
                                class="flex-1 px-1 py-1 border rounded text-xs">
                                <option value="" selected disabled>-- Select --</option>
                            </select>
                        </div>

                        <!-- Exercise Field -->
                        <div class="flex items-center space-x-1 flex-[1] min-w-[120px]">
                            <label for="workoutw_2" class="w-15 text-xs pl-3">Exercise</label>
                            <select id="workoutw_2" name="workoutw_2" class="flex-1 px-1 py-1 border rounded text-xs">
                                <option value="" selected disabled>-- Select --</option>
                            </select>
                        </div>

                        <!-- Name Field -->
                        <div class="flex items-center space-x-1 flex-[1] min-w-[120px]">
                            <label for="namew_2" class="w-15 text-xs ">Name</label>
                            <input type="text" id="namew_2" name="namew_2"
                                class="flex-1 px-1 py-1 border rounded text-xs">
                        </div>

                        <!-- Go Button -->
                        <div class="flex items-center">
                            <button id="addsetwarmup_1" onclick="// filterWarmup(date)" type="button"
                                class="bg-black text-white py-1 px-2 rounded text-xs">Go</button>
                        </div>

                        <!-- Clear Button -->
                        <div class="flex items-center">
                            <button id="searchclearsetwarmup_1" onclick="// clearSearchWarmup()" type="button"
                                class="bg-black text-white py-1 px-2 rounded text-xs">Clear</button>
                        </div>

                    </div>
                </div>

                <!-- Scroll Section -->
                <div id="unified_workout_list_container" class="mt-4 max-h-[600px] overflow-y-auto space-y-4">
                </div>

            </div>
        </div>
    </div>
</div>

<script>
// Logic to be implemented
</script>

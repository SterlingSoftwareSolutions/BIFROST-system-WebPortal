<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
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
                                    Edit Profile
                                @else
                                    Add New Profile
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('viewadminclientmanagement') }}">
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
                                    Edit Profile
                                @else
                                    Add New Profile
                                @endif
                            </strong>
                        </h2>
                    </div>
                    {{-- route cheacking --}}
                    <form
                        aaction="{{ $action == 'edit' ? route('updatenewclient', ['id' => $member->id]) : route('newProfileclientsave') }}"
                        method="POST" enctype="multipart/form-data"
                        class="space-y-6 text-xs  rounded-lg shadow-lg bg-white p-2">
                        @csrf
                        <div class="text-xs">
                            {{-- firstname and lastname Row --}}
                            <div class="w-full h-full p-2 grid grid-cols-1 md:grid-cols-2 md:border-b gap-4">
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center w-full">
                                    <label for="firstname"
                                        class="block text-gray-700 font-bold w-full md:w-[38%] mb-1 md:mb-0 pr-4">First Name <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" id="firstname" name="firstname"
                                        class="form-control w-full md:w-3/4 rounded px-4 py-2 border" required
                                        value="{{ old('firstname', isset($member) ? $member->firstname : '') }}">
                                </div>
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center w-full">
                                    <label for="lastname"
                                        class="block text-gray-700 font-bold w-full md:w-1/3 mb-1 md:mb-0 pr-4 md:ml-4">Last Name</label>
                                    <input type="text" id="lastname" name="lastname"
                                        class="form-control w-full md:w-3/4 border rounded px-4 py-2"
                                        value="{{ old('lastname', isset($member) ? $member->lastname : '') }}">
                                </div>
                            </div>
                            {{-- DOB , Gender and Age  --}}
                            <div class="w-full h-full p-2 grid grid-cols-1 md:grid-cols-6 gap-4 items-center md:border-b">
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center w-full md:col-span-1">
                                    <label for="dob" class="text-gray-700 font-bold w-full md:w-full mb-1 md:mb-0 pr-4 flex-nowrap">
                                        Date of birth <span class="text-red-500">*</span>
                                    </label>
                                </div>
                                <div class="w-full md:col-span-1">
                                    <input type="date" id="dob" name="dob" required
                                           class="form-control w-full border rounded px-4 py-2"
                                           value="{{ old('dob', isset($member) && $member->dob ? \Carbon\Carbon::parse($member->dob)->format('Y-m-d') : '') }}"
                                           oninput="calculateAge()">
                                </div>



                                <div class="text-gray-700 font-bold w-full md:w-full mb-1 md:mb-0 md:ml-8 md:col-span-1">
                                    Gender
                                </div>
                                <div class="w-full md:col-span-1 flex items-center">
                                    <label class="mr-4">
                                        <input type="radio" id="gender-male" name="gender" value="Male" required
                                            {{ old('gender', isset($member) && $member->gender == 'Male' ? 'checked' : '') }}>
                                        Male
                                    </label>
                                    <label>
                                        <input type="radio" id="gender-female" name="gender" value="Female" required
                                            {{ old('gender', isset($member) && $member->gender == 'Female' ? 'checked' : '') }}>
                                        Female
                                    </label>
                                </div>

                                <div class="form-group flex flex-wrap md:flex-nowrap items-center w-full md:col-span-1">
                                    <label for="age" class="block text-gray-700 font-bold w-full md:w-full mb-1 md:mb-0 pr-4 md:ml-8">
                                        Age
                                    </label>
                                </div>
                                <div class="w-full md:col-span-1">
                                    <input type="number" id="age" name="age" required
                                           class="form-control w-full border rounded px-4 py-2"
                                           value="{{ old('age', isset($member) ? $member->age : '') }}" readonly>
                                </div>

                            </div>
                            {{-- Phone Number , Email , Address --}}
                            <div class="w-full h-full p-2 grid grid-cols-1 md:grid-cols-6 gap-4 md:border-b">
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center w-full md:col-span-1">
                                    <label for="phone"
                                        class="text-gray-700 font-bold w-full md:w-full mb-1 md:mb-0 pr-4">
                                        Phone <span class="text-red-500">*</span>
                                    </label>
                                </div>
                                <div class="w-full md:col-span-1">
                                    <input type="tel" id="phone" name="phone" required
                                        class="w-full form-control border rounded px-4 py-2"
                                        value="{{ old('phone', isset($member) ? $member->phone : '') }}">

                                </div>
                                <div
                                    class="form-group flex flex-wrap md:flex-nowrap items-center w-full md:col-span-1 text-gray-700 font-bold">
                                    <label for="email" class="w-full md:w-full mb-1 md:mb-0 pr-4 ml-0 md:ml-8">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                </div>
                                <div class="w-full md:col-span-1">
                                    <input type="email" id="email" name="email" required
                                        class="w-full form-control border rounded px-4 py-2"
                                        value="{{ old('email', isset($member) ? $member->email : '') }}">
                                </div>
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center w-full md:col-span-1">
                                    <label for="address"
                                        class="text-gray-700 font-bold w-full md:w-1/4 mb-1 md:mb-0 pr-4 ml-0 md:ml-8">
                                        Address<span class="text-red-500">*</span>
                                    </label>
                                </div>
                                <div class="w-full md:col-span-1">
                                    <input type="text" id="address" name="address" required
                                        class="w-full form-control border rounded px-4 py-2"
                                        value="{{ old('address', isset($member) ? $member->address : '') }}">

                                </div>
                            </div>
                            {{-- Height , Weight and BMR --}}
                            <div class="w-full h-full p-2 grid grid-cols-1 md:grid-cols-6 gap-4 md:border-b">
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center md:col-span-1">
                                    <label for="height"
                                        class="text-gray-700 font-bold w-full md:w-full mb-1 md:mb-0 pr-4">
                                        Height (cm) <span class="text-red-500">*</span>
                                    </label>
                                </div>
                                <div class="w-full md:col-span-1">
                                    <input type="number" id="height" name="height" required
                                        class="form-control w-full border rounded px-4 py-2"
                                        value="{{ old('height', isset($member) ? $member->height : '') }}">

                                </div>
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center md:col-span-1">
                                    <label for="weight"
                                        class="text-gray-700 font-bold w-full md:w-full mb-1 md:mb-0 pr-4 md:ml-8">
                                        Weight (kg)<span class="text-red-500">*</span>
                                    </label>
                                </div>
                                <div class="w-full md:col-span-1">
                                    <input type="number" id="weight" name="weight" required
                                        class="form-control w-full border rounded px-4 py-2"
                                        value="{{ old('weight', isset($member) ? $member->weight : '') }}">

                                </div>
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center md:col-span-1">
                                    <label for="bmr"
                                        class="text-gray-700 font-bold w-full md:w-full mb-1 md:mb-0 pr-4 md:ml-8">
                                        BMR <span class="text-red-500">*</span>
                                    </label>
                                </div>
                                <div class="w-full md:col-span-1">
                                    <input type="number" id="bmr" name="bmr" required
                                        class="form-control w-full border rounded px-4 py-2 outline-none" readonly
                                        value="{{ old('bmr', isset($member) ? $member->bmr : '') }}">

                                </div>
                            </div>
                            {{-- Primary Goal --}}
                            <div class="w-full h-full p-2 grid grid-cols-1 md:grid-cols-2 gap-4 md:border-b">
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center w-full">
                                    <label for="primary-goal"
                                        class="block text-gray-700 font-bold w-full md:w-1/3 mb-1 md:mb-0 pr-4">
                                        Primary Goal
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="w-full md:w-2/3">
                                        <select id="primary-goal" name="primary-goal"
                                            class="form-control w-full rounded px-4 py-2 border" required>
                                            <option value="">Select Primary Goal</option>
                                            <option value="Weight Loss"
                                                {{ old('primary-goal', isset($member) && $member->primary_goal === 'Weight Loss' ? 'selected' : '') }}>
                                                Weight Loss</option>
                                            <option value="Build Muscle"
                                                {{ old('primary-goal', isset($member) && $member->primary_goal === 'Build Muscle' ? 'selected' : '') }}>
                                                Build Muscle</option>
                                            <option value="Competitive Weightlifting"
                                                {{ old('primary-goal', isset($member) && $member->primary_goal === 'Competitive Weightlifting' ? 'selected' : '') }}>
                                                Competitive Weightlifting</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="form-control w-full md:w-2/3 rounded px-4 py-2">
                                    <!-- Add any additional content or form controls here if needed -->
                                </div>
                            </div>
                            {{-- Subscription Level --}}
                            <div
                                class="w-full h-full p-2 grid grid-cols-1 md:grid-cols-2 gap-4 whitespace-nowrap md:border-b">
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center w-full">
                                    <label for="subscription_level"
                                        class="block text-gray-700 font-bold w-full md:w-[34%] mb-1 md:mb-0 pr-4">
                                        Subscription Level<span class="text-red-500">*</span>
                                    </label>
                                    <div class="w-full md:w-[66%]">
                                        <input type="text" id="subscription_level" name="subscription_level" required
                                            class="form-control w-full border rounded px-4 py-2"
                                            value="{{ old('subscription_level', isset($member) ? $member->subscription_level : '') }}">

                                    </div>
                                </div>
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center w-full">
                                    <!-- Additional content can be added here -->
                                </div>
                            </div>
                            {{-- Progress Photo --}}
                            <div class=" flex">
                                <div class="w-1/2 h-full p-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div
                                        class="form-group block text-gray-700 font-bold w-full md:w-[34%] mb-1 md:mb-0 pr-4">
                                        <label for="progress-photos"
                                            class="block text-gray-700 font-bold mb-1 md:mb-0 pr-4 whitespace-nowrap">
                                            Profile Photos <span class="text-red-500">*</span>
                                        </label>
                                    </div>
                                    <div class="items-center -ml-28 flex justify-start">
                                        <div class="progress-photos flex flex-col cursor-pointer items-center justify-center min-h-40 h-auto border bg-[#F8F9FA] rounded w-full px-4 py-6"
                                            id="progressPhotosDropArea">
                                            <div id="uploadedFiles" class="mt-4">
                                                <!-- Uploaded files will be shown here -->
                                                @if (isset($member) && $member->image_paths)
                                                    @foreach (json_decode($member->image_paths) as $image)
                                                        <div
                                                            class="file-item mt-2 flex items-center justify-between w-full">
                                                            <img src="{{ asset('storage/' . $image) }}"
                                                                alt="Progress Photo"
                                                                class="w-16 h-16 object-cover rounded mr-4">
                                                            <button class="text-red-500 hover:text-red-700"
                                                                disabled>Remove</button>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-600 mb-2"></i>
                                            <p class="text-gray-600">Drop files here or click to upload.</p>
                                            <input type="file" name="profile_image[]" id="fileInput"
                                                style="display:none;">
                                        </div>
                                    </div>

                                </div>

                                <div class="w-1/2 ">
                                    <label for="primary-goal"
                                        class=" text-gray-700 font-bold w-full border  mb-1 md:mb-0 pr-4">
                                        Payment Method
                                        <span class="text-red-500 w-full ">
                                            <form class="w-full  ">
                                                <div class="flex">
                                                    <div class="mb-4 w-1/2">
                                                        <label class=" text-sm  text-gray-700 font-bold">Credit /
                                                            Debit
                                                            Card</label>
                                                        <div class="flex items-center mt-2">
                                                            <input type="radio" name="cardType" id="visa"
                                                                class="mr-2">
                                                            <label for="visa" class="mr-6">
                                                                <img src="https://img.icons8.com/color/48/000000/visa.png"
                                                                    alt="Visa" class="w-10 inline">
                                                            </label>
                                                            <input type="radio" name="cardType" id="mastercard"
                                                                class="mr-2">
                                                            <label for="mastercard">
                                                                <img src="https://img.icons8.com/color/48/000000/mastercard.png"
                                                                    alt="MasterCard" class="w-6 inline">
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="w-1/2">
                                                        <div class="mb-4">
                                                            <label for="commencement"
                                                                class="block text-sm  text-gray-700 font-bold">Commencement</label>
                                                            <input type="text" id="commencement"
                                                                class="form-control w-full border rounded px-4 py-2">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-4 flex space-x-4">
                                                    <div class="mb-4 w-1/2">
                                                        <label for="nameOnCard"
                                                            class="block text-sm text-gray-700 font-bold">Name on
                                                            Card</label>
                                                        <input type="text" id="nameOnCard"
                                                            class="form-control w-full border rounded px-4 py-2">
                                                    </div>

                                                    <div>
                                                        <div class="mb-4">
                                                            <label for="firstBillableDate"
                                                                class="block text-sm text-gray-700 font-bold">First
                                                                Billable
                                                                Date</label>
                                                            <input type="date" id="firstBillableDate"
                                                                class="form-control w-full border rounded px-4 py-2">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-4 w-1/2">
                                                    <label for="cardNumber"
                                                        class="block text-sm text-gray-700 font-bold">Card Number</label>
                                                    <input type="text" id="cardNumber"
                                                        class="form-control w-full border rounded px-4 py-2">
                                                </div>
                                                <div class="mb-4 flex space-x-4">
                                                    <div class="w-1/2">
                                                        <label for="cvv"
                                                            class=" text-sm text-gray-700 font-bold">CVV</label>
                                                        <input type="text" id="cvv"
                                                            class=" w-full border rounded px-4 py-2">
                                                    </div>
                                                    <div class="w-1/2">
                                                        <label for="exp"
                                                            class="block text-sm ftext-gray-700 font-bold">EXP</label>
                                                        <input type="text" id="exp"
                                                            class="form-control w-full border rounded px-4 py-2">
                                                    </div>
                                                </div>

                                            </form>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            {{-- Submint Button --}}
                            <div
                                class="w-full h-full p-1 grid grid-cols-1 md:grid-cols-2 gap-4 whitespace-nowrap md:border-b">
                                <div class="form-group flex flex-wrap md:flex-nowrap items-center w-full ">
                                    <label for=""
                                        class="block text-gray-700 font-bold w-full md:w-[34%] mb-1 md:mb-0 pr-4">
                                        <!-- Empty label left as it is -->
                                    </label>
                                    <div class="w-full md:w-[26%]">
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

        {{-- Age calculate --}}
        <script>
            function calculateAge() {
                const dobInput = document.getElementById('dob').value;
                const ageInput = document.getElementById('age');
        
                if (dobInput) {
                    const dob = new Date(dobInput);
                    const today = new Date();
                    let age = today.getFullYear() - dob.getFullYear();
                    const monthDiff = today.getMonth() - dob.getMonth();
                    const dayDiff = today.getDate() - dob.getDate();
        
                    // Adjust age if the birth month and day haven't occurred yet this year
                    if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
                        age--;
                    }
        
                    ageInput.value = age;
                } else {
                    ageInput.value = ''; // Clear age if no DOB is selected
                }
            }
        </script>
        {{-- Upload File Script  --}}
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const dropArea = document.getElementById('progressPhotosDropArea');
                const fileInput = document.getElementById('fileInput');
                const uploadedFilesContainer = document.getElementById('uploadedFiles');
                let selectedFiles = []; // Array to hold selected files

                dropArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    dropArea.classList.add('border-blue-500'); // Add border highlight when dragging over
                });

                dropArea.addEventListener('dragleave', function() {
                    dropArea.classList.remove('border-blue-500'); // Remove border highlight when leaving
                });

                dropArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    dropArea.classList.remove('border-blue-500'); // Remove border highlight when dropping
                    const files = e.dataTransfer.files;
                    handleFiles(files);
                });

                fileInput.addEventListener('change', function(e) {
                    const files = e.target.files;
                    handleFiles(files);
                });

                dropArea.addEventListener('click', function() {
                    fileInput.click();
                });

                function handleFiles(files) {
                    // Clear previous images and selected files
                    uploadedFilesContainer.innerHTML = '';
                    selectedFiles = [];

                    files = Array.from(files); // Convert FileList to Array
                    files.forEach(file => {
                        displayFile(file);
                        selectedFiles.push(file); // Store file in selectedFiles array
                    });
                    updateFileInput(); // Update the hidden file input with selected files
                }

                function displayFile(file) {
                    const fileReader = new FileReader();
                    fileReader.onload = function(e) {
                        const fileUrl = e.target.result;

                        // Create elements for file display
                        const fileItem = document.createElement('div');
                        fileItem.classList.add('file-item', 'mt-2', 'flex', 'items-center', 'justify-between',
                            'w-full');

                        const fileContent = document.createElement('div');
                        fileContent.classList.add('flex', 'items-center');

                        const imgElement = document.createElement('img');
                        imgElement.src = fileUrl;
                        imgElement.alt = file.name;
                        imgElement.classList.add('w-16', 'h-16', 'object-cover', 'rounded', 'mr-4');

                        const fileNameElement = document.createElement('p');
                        fileNameElement.textContent = file.name;
                        fileNameElement.classList.add('text-gray-700');

                        fileContent.appendChild(imgElement);
                        fileContent.appendChild(fileNameElement);

                        const removeButton = document.createElement('button');
                        removeButton.textContent = 'Remove';
                        removeButton.classList.add('text-red-500', 'hover:text-red-700', 'ml-2');
                        removeButton.addEventListener('click', function() {
                            removeFile(fileItem, file);
                        });

                        fileItem.appendChild(fileContent);
                        fileItem.appendChild(removeButton);

                        uploadedFilesContainer.appendChild(fileItem);
                    };
                    fileReader.readAsDataURL(file);
                }

                function removeFile(fileItem, file) {
                    const index = selectedFiles.indexOf(file);
                    if (index !== -1) {
                        selectedFiles.splice(index, 1); // Remove file from selectedFiles array
                        updateFileInput(); // Update the hidden file input with remaining selected files
                    }
                    fileItem.remove();
                }

                function updateFileInput() {
                    const fileList = new DataTransfer();
                    selectedFiles.forEach(file => {
                        fileList.items.add(file);
                    });
                    fileInput.files = fileList.files;
                }
            });
        </script>


        {{-- BMI Calculator Script --}}
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Function to calculate BMR
                function calculateBMR() {
                    const weight = parseFloat(document.getElementById('weight').value) || 0;
                    const height = parseFloat(document.getElementById('height').value) || 0;

                    if(weight && height){
                        const heightInM = height / 100;
                        return weight / heightInM;
                    }
                    return 0;
                }

                // Update BMR when inputs change
                function updateBMR() {
                    const bmrInput = document.getElementById('bmr');
                    if (bmrInput) {
                        bmrInput.value = calculateBMR().toFixed(2);
                    }
                }

                // Event listeners for inputs
                document.getElementById('weight').addEventListener('input', updateBMR);
                document.getElementById('height').addEventListener('input', updateBMR);
            });
        </script>


    @endsection
</body>

</html>

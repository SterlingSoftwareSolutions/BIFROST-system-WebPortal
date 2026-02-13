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
    @extends('layout.user-layout')
    @section('user-content')
        

        {{-- Upload File Script  --}}
        {{-- <script>
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
                        const fileElement = document.createElement('div');
                        fileElement.classList.add('file-item', 'mt-2', 'flex', 'items-center', 'justify-between', 'w-full');
                        const imageName = `img_${selectedFiles.length}`; // Generate custom name for the image
                        fileElement.innerHTML = `
                            <div class="flex items-center">
                                <img src="${fileUrl}" alt="${file.name}" class="w-16 h-16 object-cover rounded mr-4" name="${imageName}">
                                <p class="text-gray-700">${file.name}</p>
                            </div>
                            <button class="text-red-500 hover:text-red-700" onclick="removeFile(this)">Remove</button>
                        `;
                        uploadedFilesContainer.appendChild(fileElement);
                    };
                    fileReader.readAsDataURL(file);
                }
        
                function removeFile(button) {
                    const fileItem = button.closest('.file-item');
                    const fileName = fileItem.querySelector('img').getAttribute('name');
                    const index = selectedFiles.findIndex(file => file.name === fileName);
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
        </script> --}}
        
        
        {{-- BMR Calculator Script --}}
        {{-- <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Function to calculate BMR
                function calculateBMR() {
                    const age = parseInt(document.getElementById('age').value) || 0;
                    const weight = parseFloat(document.getElementById('weight').value) || 0;
                    const height = parseFloat(document.getElementById('height').value) || 0;
                    const gender = document.querySelector('input[name="gender"]:checked');

                    if (gender && gender.value) {
                        if (gender.value === 'Male') {
                            return 88.362 + (13.397 * weight) + (4.799 * height) - (5.677 * age);
                        } else if (gender.value === 'Female') {
                            return 447.593 + (9.247 * weight) + (3.098 * height) - (4.330 * age);
                        }
                    }
                    return 0; // Default case, though gender should always be selected
                }

                // Update BMR when inputs change
                function updateBMR() {
                    const bmrInput = document.getElementById('bmr');
                    if (bmrInput) {
                        bmrInput.value = calculateBMR().toFixed(2);
                    }
                }

                // Event listeners for inputs
                document.getElementById('age').addEventListener('input', updateBMR);
                document.getElementById('weight').addEventListener('input', updateBMR);
                document.getElementById('height').addEventListener('input', updateBMR);
                document.querySelectorAll('input[name="gender"]').forEach(item => {
                    item.addEventListener('change', updateBMR);
                });
            });
        </script> --}}
    @endsection
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center min-h-screen m-0 p-4 bg-cover bg-center bg-no-repeat"
    style="background-image: url('{{ asset('img/valhalla-bg.jpg') }}');">
    <form class="bg-black text-white text-center p-4 sm:p-10 w-full max-w-md rounded-3xl mb-12 sm:mb-44" method="POST" action="{{ route('frogot-password') }}">
        @csrf
        <input type="hidden" name="token" value="{{$token}}">
        <div class="mb-6">
            <div class="flex justify-center mb-8">
                <img src="{{ asset('img/valhalla-logo.png') }}" alt="logo" class="h-16 sm:h-24">
            </div>
        </div>
        <h2 class="text-2xl sm:text-4xl font-bold mb-8">Reset Pin</h2>
        <div class="flex justify-center gap-4 sm:gap-4 mb-4">
            <label for="text" class="text-xl">New Pin</label>
        </div>
        <div class="flex justify-center gap-4 sm:gap-2 mb-8">
            <input type="text" maxlength="1"
                class="w-12 h-12 sm:w-16 sm:h-16 text-black text-center bg-white rounded" name="pin_1" id="pin_1">
            <input type="text" maxlength="1"
                class="w-12 h-12 sm:w-16 sm:h-16 text-black text-center bg-white rounded" name="pin_2" id="pin_2">
            <input type="text" maxlength="1"
                class="w-12 h-12 sm:w-16 sm:h-16 text-black text-center bg-white rounded" name="pin_3" id="pin_3">
            <input type="text" maxlength="1"
                class="w-12 h-12 sm:w-16 sm:h-16 text-black text-center bg-white rounded" name="pin_4" id="pin_4">
        </div>

        </div>
        <div class="flex justify-center  gap-4 sm:gap-4 mb-4">
            <label for="text" class="text-xl">Confirm Pin</label>
        </div>
        <div class="flex justify-center  gap-4 sm:gap-2 mb-8">
            <input type="text" maxlength="1"
                class="w-12 h-12 sm:w-16 sm:h-16 text-black text-center bg-white rounded" name="confirm_pin_1"
                id="confirm_pin_1">
            <input type="text" maxlength="1"
                class="w-12 h-12 sm:w-16 sm:h-16 text-black text-center bg-white rounded" name="confirm_pin_2"
                id="confirm_pin_2">
            <input type="text" maxlength="1"
                class="w-12 h-12 sm:w-16 sm:h-16 text-black text-center bg-white rounded" name="confirm_pin_3"
                id="confirm_pin_3">
            <input type="text" maxlength="1"
                class="w-12 h-12 sm:w-16 sm:h-16 text-black text-center bg-white rounded" name="confirm_pin_4"
                id="confirm_pin_4">
        </div>

        </div>
        <div class="rounded-sm sm:w-32 w-1/3 h-10 flex items-center justify-center mx-auto mt-5  bg-gray-400 transition-colors duration-300">
            <button
                class="text-black text-xs sm:text-lg hover:text-white text-center font-bold hover:bg-black w-full h-full border hover:border-white border-black">CONFIRM</button>
        </div>
        <div class="text-sm mt-4">
            &copy; 2024 Valhalla Performance. All rights reserved. System by JPLMS.
        </div>
    </form>

    <script>
        // Select all input fields
        const inputs = document.querySelectorAll('input[type="text"]');

        // Add an input event listener to each input field
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                // Remove non-numeric characters
                this.value = this.value.replace(/[^0-9]/g, '');
                // Move focus to the next input field if a single digit is entered
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            // add back space
            input.addEventListener('keydown', function(event) {
                if (event.key === 'Backspace') {
                    this.value = ''; // Clear current input field
                    if (index > 0) {
                        inputs[index - 1].focus(); // Focus on previous input field
                    }
                }
            });
        });
    </script>
</body>

</html>

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
    <form class="bg-black text-white text-center p-4 sm:p-10 w-full max-w-md rounded-3xl mb-12 sm:mb-44" method="POST"
        action="{{ route('login') }}" id="form">
        @csrf
        <div class="mb-6">
            <div class="flex justify-center mb-8">
                <img src="img/valhalla-logo.png" alt="logo" class="h-16 sm:h-24">
            </div>
            <div class="flex flex-row justify-center space-x-4">
                <label class="flex items-center">
                    <input type="radio" name="portal" value="admin" class="mr-2" onclick="enablePinFields()">
                    Admin Portal
                </label>
                <label class="flex items-center">
                    <input type="radio" name="portal" value="user" class="mr-2"
                        onclick="enablePinFields()"checked>
                    User Portal
                </label>
            </div>
        </div>
        <h2 class="text-2xl sm:text-4xl font-bold mb-8">Sign In</h2>
        <div class="flex justify-center gap-4 sm:gap-4 mb-8">
            <input type="text" maxlength="1"
                class="w-12 h-12 sm:w-20 sm:h-20 text-black text-center bg-white rounded" name="pin_1" id="pin_1"
                disabled>
            <input type="text" maxlength="1"
                class="w-12 h-12 sm:w-20 sm:h-20 text-black text-center bg-white rounded" name="pin_2" id="pin_2"
                disabled>
            <input type="text" maxlength="1"
                class="w-12 h-12 sm:w-20 sm:h-20 text-black text-center bg-white rounded" name="pin_3" id="pin_3"
                disabled>
            <input type="text" maxlength="1"
                class="w-12 h-12 sm:w-20 sm:h-20 text-black text-center bg-white rounded" name="pin_4" id="pin_4"
                disabled>
        </div>
        <a href="{{ route('forget.password') }}" class="text-white underline mb-4 block">Forget Pin?</a>
        <div class="text-sm">
            &copy; 2024 Valhalla Performance. All rights reserved. System by JPLMS.
        </div>
    </form>
    <script>
        function enablePinFields() {
            const radios = document.getElementsByName('portal');
            const pinFields = document.querySelectorAll('input[name^="pin"]');
            const selectedPortal = Array.from(radios).find(radio => radio.checked);
            const isPortalSelected = selectedPortal && selectedPortal.value;

            pinFields.forEach(field => {
                field.disabled = !isPortalSelected;
                // Reset pin fields to empty when portal changes
                field.value = '';
            });
        }

        function login() {
            console.log("login...");
            const pinFields = document.querySelectorAll('input[name^="pin"]');
            let allPinsFilled = true;

            pinFields.forEach(pinField => {
                if (pinField.value.trim() === '') {
                    allPinsFilled = false;
                    return;
                }
            });
            if (allPinsFilled) {
                document.getElementById('form').submit();
            }
        }



        // Enable pin fields if a portal is already selected on page load
        enablePinFields();

        const inputs = document.querySelectorAll('input[type="text"]');
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                // Remove non-numeric characters
                this.value = this.value.replace(/[^0-9]/g, '');
                // Move focus to the next input field if a single digit is entered
                login();
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                // Call login function when pin_4 is filled
                if (this.id === 'pin_4' && this.value.length === 1) {
                    login();
                }
            });
        });
    </script>
</body>

</html>

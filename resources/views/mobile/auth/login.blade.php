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
    <form class=" text-white text-center p-4 sm:p-10 w-full max-w-md rounded-3xl mb-12 sm:mb-44" method="POST"
        action="{{ route('login') }}" id="form">
        @csrf
        <div class=" ">
            <div class="flex  justify-center ">
                <img src={{ asset('img/valhalla-logo.png') }} alt="logo" class=" h-32 sm:h-24">
            </div>

        </div>
        <div class=" items-center hidden">
            <input type="radio" name="portal" value="client" class="mr-2" onclick="enablePinFields()"checked>
            <input type="text" name="type" value="mobile">
            User Portal
        </div>
        @if (session('error'))
            <div class="bg-red-500 text-white p-4 rounded-md mb-4 mt-10">
                {{ session('error') }}
            </div>
        @endif
        <div class="flex justify-center w-full gap-4 mt-20 sm:gap-4 p-1 sm:p-1">
            <input type="number" pattern="[0-9]*" inputmode="numeric" maxlength="1"
                class="w-20 h-20 text-black text-center bg-white rounded" name="pin_1" id="pin_1">
            <input type="number" pattern="[0-9]*" inputmode="numeric" maxlength="1"
                class="w-20 h-20 text-black text-center bg-white rounded" name="pin_2" id="pin_2">
            <input type="number" pattern="[0-9]*" inputmode="numeric" maxlength="1"
                class="w-20 h-20 text-black text-center bg-white rounded" name="pin_3" id="pin_3">
            <input type="number" pattern="[0-9]*" inputmode="numeric" maxlength="1"
                class="w-20 h-20 text-black text-center bg-white rounded" name="pin_4" id="pin_4">
        </div>


        <a href="{{ route('mobileforget.password') }}" class="text-white underline mt-10 block">Forget Pin?</a>

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



        const inputs = document.querySelectorAll('input[name^="pin"]');
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                // Remove non-numeric characters
                this.value = this.value.replace(/[^0-9]/g, '');

                // Move focus to the next input field if a single digit is entered
                if (this.value.length === 1 && index < inputs.length - 1) {
                    this.type = 'password'; // Change to password to show dot
                    inputs[index + 1].focus();
                }

                // Call login function when all pin fields are filled
                if (this.id === 'pin_4' && this.value.length === 1) {
                    this.type = 'password'; // Change to password to show dot
                    login();
                }
            });

            input.addEventListener('focus', function() {
                this.type = 'text'; // Show text when focusing
            });

            input.addEventListener('blur', function() {
                if (this.value.match(/^[0-9]$/)) {
                    this.type = 'password'; // Hide digit by switching to password
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

    <script>
        $(document).ready(function() {
            var inputs = $("#pin_1, #pin_2, #pin_3, #pin_4");

            inputs.on('input', function(event) {
                var inputId = $(this).attr('id');
                var inputVal = $(this).val();

                // Move to the next input if a digit is entered
                if (inputVal.length == 1) {
                    $(this).next('input').focus();
                }

                console.log("Input changed: ", inputId, inputVal);
            });

            inputs.on('keydown', function(event) {
                // Move to the previous input if backspace is pressed and the current input is empty
                if (event.which == 8 && $(this).val() === '') {
                    $(this).prev('input').focus();
                }
            });

            inputs.focus(function() {
                console.log("Input focused: ", $(this).attr('id'));
            });
        });
    </script>
</body>

</html>

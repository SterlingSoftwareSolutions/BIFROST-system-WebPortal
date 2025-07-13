<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UI Design</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        /* Popup animation styles */
        .popup {
            display: none;
            align-items: center;
            justify-content: center;
        }

        .popup-content {
            animation-duration: 0.4s;
        }

        .popup-content.show {
            animation-name: slideIn;
        }

        .popup-content.hide {
            animation-name: slideOut;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateY(0);
                opacity: 1;
            }

            to {
                transform: translateY(-50%);
                opacity: 0;
            }
        }
    </style>
</head>

<body>
    @extends('mobile.layout.mobile-layout')
    @section('content')
        <div class=" w-full  flex flex-col justify-between h-screen  overflow-y-auto ">
            <div class="   flex-grow items-center justify-center m-0 p-4 bg-cover bg-center bg-no-repeat"
                style="background-image: url('{{ asset('img/valhalla-bg.jpg') }}');">
                <div class="bg-transparent p-6 rounded-lg shadow-md pt-20">

                    <form  method="POST" action="{{ route('mobile.storescore')}}">
                        @csrf
                        <!-- Display selected day and date -->
                        <div class=" flex flex-col text-center">
                            <div class="w-full mb-2 text-white text-center items-center justify-center flex">Training Day
                            </div>
                            <div class="flex items-center justify-center h-8 rounded overflow">
                                <input id="selectedDay" name="selected_day"
                                    class="text-white text-lg font-bold text-center items-center justify-center bg-transparent mt-2 outline-none"
                                    value="{{ $dayWithDate }}">
                            </div>
                        </div>


                        {{-- Hrs of Sleep Div --}}
                        <div class="mb-4 flex flex-col text-center">
                            {{-- Header for Hrs of Sleep --}}
                            <div class="w-full mb-2 text-white text-center items-center justify-center flex">Hrs of Sleep
                            </div>
                            <div class="flex items-center h-8 rounded overflow">
                                <div class="flex flex-grow">
                                    {{-- Buttons for selecting hours of sleep --}}
                                    <button id="sleep-less-than-5" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#F20525] text-white text-center p-2 rounded-l-lg"
                                        onclick="selectButton(this, 'Sleep')">&lt;5</button>
                                    <button id="sleep-5-6" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#FF8F36] text-white text-center focus:outline-none p-2"
                                        onclick="selectButton(this, 'Sleep')">5-6</button>
                                    <button id="sleep-6-7" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#FEE943] text-white text-center focus:outline-none p-2"
                                        onclick="selectButton(this, 'Sleep')">6-7</button>
                                    <button id="sleep-7-8" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#89DD43] text-white text-center focus:outline-none p-2"
                                        onclick="selectButton(this, 'Sleep')">7-8</button>
                                    <button id="sleep-more-than-8" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#1CBA4B] text-white text-center focus:outline-none p-2 rounded-r-lg"
                                        onclick="selectButton(this, 'Sleep')">8+</button>
                                </div>
                                {{-- Info icon button (currently inactive) --}}
                                <div class="flex-none">
                                    <button
                                        class="hover:bg-transparent text-transparent text-center px-2 pointer-events-none"
                                        type="button" onclick=""><i class="bi bi-info-circle"></i></button>
                                </div>
                            </div>
                            {{-- Hidden input field to store the selected value for hours of sleep --}}
                            <input id="sleepInput" name="sleep_input" value="{{ $userscore->sleep_input ?? '' }}"
                                class="w-full mb-2 text-white text-xs text-center items-center justify-center bg-transparent mt-2 outline-none"
                                hidden required>
                        </div>

                        {{-- Alertness Div --}}
                        <div class="mb-4 flex flex-col">
                            {{-- Header for Alertness --}}
                            <div class="w-full mb-2 text-white text-center items-center justify-center">Alertness</div>
                            <div class="flex items-center rounded overflow-hidden">
                                <div class="flex flex-grow">
                                    {{-- Buttons for selecting alertness levels --}}
                                    <button id="alertness-1" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#F20525] text-white text-center p-2 rounded-l-lg"
                                        onclick="selectButton(this, 'Alertness')">1</button>
                                    <button id="alertness-2" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#FF8F36] text-white text-center focus:outline-none p-2"
                                        onclick="selectButton(this, 'Alertness')">2</button>
                                    <button id="alertness-3" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#FEE943] text-white text-center focus:outline-none p-2"
                                        onclick="selectButton(this, 'Alertness')">3</button>
                                    <button id="alertness-4" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#89DD43] text-white text-center focus:outline-none p-2"
                                        onclick="selectButton(this, 'Alertness')">4</button>
                                    <button id="alertness-5" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#1CBA4B] text-white text-center focus:outline-none p-2 rounded-r-lg"
                                        onclick="selectButton(this, 'Alertness')">5</button>
                                </div>
                                {{-- Info icon button for Alertness --}}
                                <div class="flex-none">
                                    <button class="hover:bg-transparent text-white text-center px-2 border-none"
                                        type="button" onclick="showInfo('Alertness')"><i
                                            class="bi bi-info-circle"></i></button>
                                </div>
                            </div>
                            {{-- Description area for selected alertness level --}}
                            <div id="alertnessDescription"
                                class="w-full mb-2 text-white text-xs text-center items-center justify-center"></div>
                            {{-- Hidden input field to store the selected value for alertness --}}
                            <input id="alertnessInput" name="alertness_input"
                                value="{{ $userscore->alertness_input ?? '' }}"
                                class="w-full mb-2 text-white text-xs text-center items-center justify-center bg-transparent mt-2 outline-none"
                                hidden required>
                        </div>


                        {{-- Excitement Div --}}
                        <div class="mb-4 flex flex-col">
                            {{-- Header for Excitement --}}
                            <div class="w-full mb-2 text-white text-center items-center justify-center">Excitement</div>
                            <div class="flex items-center rounded overflow-hidden">
                                <div class="flex flex-grow">
                                    {{-- Buttons for selecting excitement levels --}}
                                    <button id="excitement-1" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#F20525] text-white text-center p-2 rounded-l-md"
                                        onclick="selectButton(this, 'Excitement')">1</button>
                                    <button id="excitement-2" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#FF8F36] text-white text-center focus:outline-none p-2"
                                        onclick="selectButton(this, 'Excitement')">2</button>
                                    <button id="excitement-3" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#FEE943] text-white text-center focus:outline-none p-2"
                                        onclick="selectButton(this, 'Excitement')">3</button>
                                    <button id="excitement-4" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#89DD43] text-white text-center focus:outline-none p-2"
                                        onclick="selectButton(this, 'Excitement')">4</button>
                                    <button id="excitement-5" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#1CBA4B] text-white text-center focus:outline-none p-2 rounded-r-lg"
                                        onclick="selectButton(this, 'Excitement')">5</button>
                                </div>
                                {{-- Info icon button for Excitement --}}
                                <div class="flex-none">
                                    <button class="hover:bg-transparent text-white text-center px-2 border-none"
                                        type="button" onclick="showInfo('Excitement')"><i
                                            class="bi bi-info-circle"></i></button>
                                </div>
                            </div>
                            {{-- Description area for selected excitement level --}}
                            <div id="excitementDescription"
                                class="w-full mb-2 text-white text-xs text-center items-center justify-center"></div>
                            {{-- Hidden input field to store the selected value for excitement --}}
                            <input id="excitementInput" name="excitement_input"
                                value="{{ $userscore->excitement_input ?? '' }}"
                                class="w-full mb-2 text-white text-xs text-center items-center justify-center bg-transparent mt-2 outline-none"
                                hidden required>
                        </div>

                        {{-- Stress Div --}}
                        <div class="mb-4 flex flex-col">
                            {{-- Header for Stress --}}
                            <div class="w-full mb-2 text-white text-center items-center justify-center">Stress</div>
                            <div class="flex items-center rounded overflow-hidden">
                                <div class="flex flex-grow">
                                    {{-- Buttons for selecting stress levels --}}
                                    <button id="stress-1" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#F20525] text-white text-center p-2 rounded-l-md"
                                        onclick="selectButton(this, 'Stress')">1</button>
                                    <button id="stress-2" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#FF8F36] text-white text-center focus:outline-none p-2"
                                        onclick="selectButton(this, 'Stress')">2</button>
                                    <button id="stress-3" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#FEE943] text-white text-center focus:outline-none p-2"
                                        onclick="selectButton(this, 'Stress')">3</button>
                                    <button id="stress-4" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#89DD43] text-white text-center focus:outline-none p-2"
                                        onclick="selectButton(this, 'Stress')">4</button>
                                    <button id="stress-5" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#1CBA4B] text-white text-center focus:outline-none p-2 rounded-r-lg"
                                        onclick="selectButton(this, 'Stress')">5</button>
                                </div>
                                {{-- Info icon button for Stress --}}
                                <div class="flex-none">
                                    <button class="hover:bg-transparent text-white text-center px-2 border-none"
                                        type="button" onclick="showInfo('Stress')"><i
                                            class="bi bi-info-circle"></i></button>
                                </div>
                            </div>
                            {{-- Description area for selected stress level --}}
                            <div id="stressDescription"
                                class="w-full mb-2 text-white text-xs text-center items-center justify-center"></div>
                            {{-- Hidden input field to store the selected value for stress --}}
                            <input id="stressInput" name="stress_input" value="{{ $userscore->stress_input ?? '' }}"
                                class="w-full mb-2 text-white text-xs text-center items-center justify-center bg-transparent mt-2 outline-none"
                                hidden required>
                        </div>


                        {{-- Soreness Div --}}
                        <div class="mb-4 flex flex-col">
                            {{-- Header for Soreness --}}
                            <div class="w-full mb-2 text-white text-center items-center justify-center">Soreness</div>
                            <div class="flex items-center rounded overflow-hidden">
                                <div class="flex flex-grow">
                                    {{-- Buttons for selecting soreness levels --}}
                                    <button id="soreness-1" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#F20525] text-white text-center p-2 rounded-l-lg"
                                        onclick="selectButton(this, 'Soreness')">1</button>
                                    <button id="soreness-2" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#FF8F36] text-white text-center focus:outline-none p-2"
                                        onclick="selectButton(this, 'Soreness')">2</button>
                                    <button id="soreness-3" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#FEE943] text-white text-center focus:outline-none p-2"
                                        onclick="selectButton(this, 'Soreness')">3</button>
                                    <button id="soreness-4" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#89DD43] text-white text-center focus:outline-none p-2"
                                        onclick="selectButton(this, 'Soreness')">4</button>
                                    <button id="soreness-5" type="button"
                                        class="flex-1 hover:bg-transparent bg-[#1CBA4B] text-white text-center focus:outline-none p-2 rounded-r-lg"
                                        onclick="selectButton(this, 'Soreness')">5</button>
                                </div>
                                {{-- Info icon button for Soreness --}}
                                <div class="flex-none">
                                    <button id="Sorenesspopup" type="button"
                                        class="hover:bg-transparent border-none text-white text-center px-2"
                                        onclick="showInfo('Soreness')"><i class="bi bi-info-circle"></i></button>
                                </div>
                            </div>
                            {{-- Description area for selected soreness level --}}
                            <div id="sorenessDescription"
                                class="w-full mb-2 text-white text-xs text-center items-center justify-center">
                            </div>
                            {{-- Hidden input field to store the selected value for soreness --}}
                            <input id="sorenessInput" name="soreness_input"
                                value="{{ $userscore->soreness_input ?? '' }}"
                                class="w-full mb-2 text-white text-xs text-center items-center justify-center bg-transparent mt-2 outline-none"
                                hidden required>
                        </div>

                        {{-- Score Div --}}
                        <div class="mb-4 flex flex-col items-center justify-center text-center font-bold text-white">
                            {{-- Container for displaying the score --}}
                            <div
                                class="border border-white flex-col flex w-48 h-28 items-center justify-center rounded-lg">
                                <span>SCORE</span>
                                {{-- Score input field, read-only --}}
                                <div class="flex text-3xl text-center">
                                    <input type="text" id="score" name="score"
                                        class="text-3xl text-center bg-transparent w-16 border-none outline-none"
                                        value="{{ $userscore->score ?? 0 }}" readonly>%
                                </div>
                            </div>
                        </div>

                        {{-- Save Button --}}
                        <button class="block mx-auto py-2 px-4 b text-white rounded mb-4 focus:outline-none"
                            type="submit">SAVE</button>

                    </form>
                </div>
            </div>
        </div>

        {{-- Modal --}}
        <div id="popup" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex mt-24 h-fit justify-center">
            <div class="modal bg-white w-[90%] rounded-lg shadow-md popup-content">
                <div class="modal-header bg-black text-white py-2 px-4 flex justify-between items-center">
                    <h2 id="modalTitle" class="text-lg font-bold">Modal Title</h2>
                    <button class="close-btn text-white border-none" onclick="closeModal()">×</button>
                </div>
                <div id="modalBody" class="modal-body px-4 py-2">
                    <!-- Modal content will be dynamically inserted here -->
                </div>
                <div class="modal-footer bg-[#EEEEEE] py-2 px-4 text-center">
                    <button class="close-btn bg-[#6C757D] hover:bg-gray-600 text-white px-4 py-2 rounded-lg"
                        onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>

        {{-- Script to handle modal popup and info display --}}
        <script>
            /**
             * Function to show information in the modal and update the description based on the selected category.
             * @param {string} category - The category for which information needs to be displayed in the modal.
             */
            function showInfo(category) {
                var modalTitle = document.getElementById('modalTitle');
                var modalBody = document.getElementById('modalBody');

                // Update the modal title and body content based on the selected category
                switch (category) {
                    case 'Alertness':
                        modalTitle.textContent = 'Alertness';
                        modalBody.innerHTML = `
                <li>1 = lights are on but no one is home.</li>
                <li>2 = attention span of a toddler.</li>
                <li>3 = Can complete a basic sudoku puzzle.</li>
                <li>4 = Ready to tackle the day.</li>
                <li>5 = firing on all cylinders.</li>
            `;
                        break;
                    case 'Excitement':
                        modalTitle.textContent = 'Excitement';
                        modalBody.innerHTML = `
                <li>1 = Not interested in weights today.</li>
                <li>2 = Ill do it because its good for me.</li>
                <li>3 = Not pumped but not upset either.</li>
                <li>4 = I’m keen, lets go!</li>
                <li>5 = Chomping at the bit all day!</li>
            `;

                        break;
                    case 'Stress':
                        modalTitle.textContent = 'Stress';
                        modalBody.innerHTML = `
                <li>1 = Pulling my hair out.</li>
                <li>2 = fairly stressed.</li>
                <li>3 = Feeling ok.</li>
                <li>4 = surfer level stress.</li>
                <li>5 = Zen monk.</li>
            `;
                        break;
                    case 'Soreness':
                        modalTitle.textContent = 'Soreness';
                        modalBody.innerHTML = `
                <li>1 = Crippled.</li>
                <li>2 = very sore.</li>
                <li>3 = Sore but ok to push.</li>
                <li>4 = feeling good!</li>
                <li>5 = like I never trained.</li>
            `;
                        break;
                    default:
                        modalTitle.textContent = 'Information';
                        modalBody.innerHTML = '<p>Information not available.</p>';
                }

                // Show the modal
                toggleModal();
            }

            /**
             * Function to toggle the modal visibility.
             */
            function toggleModal() {
                var modal = document.getElementById('popup');
                modal.classList.toggle('hidden');
                modal.querySelector('.popup-content').classList.toggle('show');
            }

            /**
             * Function to close the modal with a transition effect.
             */
            function closeModal() {
                var modal = document.getElementById('popup');
                modal.querySelector('.popup-content').classList.remove('show');
                modal.querySelector('.popup-content').classList.add('hide');
                setTimeout(function() {
                    modal.classList.add('hidden');
                    modal.querySelector('.popup-content').classList.remove('hide');
                }, 400); // Delay to allow the transition to complete
            }

            /**
             * Set the original background color as a data attribute for each button on page load.
             */
            document.addEventListener('DOMContentLoaded', function() {
                var buttons = document.querySelectorAll('button');
                buttons.forEach(function(btn) {
                    var originalBg = Array.from(btn.classList).find(function(cls) {
                        return cls.startsWith('data-original-bg');
                    });
                    btn.setAttribute('data-original-bg', originalBg);
                });
            });

            /**
             * Function to handle button selection and update the description.
             * @param {object} button - The selected button element.
             * @param {string} category - The category for which the button was selected.
             */
            function selectButton(button, category) {
                // Log the selected button for debugging
                // console.log(button);

                // Check if the clicked button is the info icon button
                if (button.classList.contains('bi') && button.classList.contains('bi-info-circle')) {
                    // Handle click on the info icon button if needed
                    var category = button.getAttribute('data-category');
                    // showInfo(category);
                    return; // Exit function early if it's the info icon button
                }

                // Remove active class from all buttons within their respective row
                var buttons = button.parentElement.querySelectorAll('button');
                buttons.forEach(function(btn) {
                    if (!btn.classList.contains('bi') && !btn.classList.contains('bi-info-circle')) {
                        // Exclude buttons with class 'bi' (bootstrap-icons) and 'bi bi-info-circle' (font-awesome)
                        btn.classList.remove('border', 'bg-opacity-0');
                        btn.classList.add(btn.getAttribute('data-original-bg'));
                    }
                });

                // Add active class to the clicked button, excluding info icon button
                if (!button.classList.contains('bi') && !button.classList.contains('bi-info-circle')) {
                    button.classList.add('border', 'bg-opacity-0');
                    button.classList.remove(button.getAttribute('data-original-bg'));
                    updateDescription(button, category); // Update the description and hidden input field value
                }
            }


            /**
             * Function to update the description and hidden input field value based on the selected button.
             * @param {object} value - The selected button element.
             * @param {string} category - The category for which the description and input value are being updated.
             */
            function updateDescription(value, category) {
                // Calculate the score based on the selected button text content and category
                scoreCal(value.textContent, category);

                // Log the selected button element for debugging
                console.log(value);

                // Get the description element and hidden input field for the given category
                var descriptionElement = document.getElementById(category.toLowerCase() + 'Description');
                var descriptionInput = document.getElementById(category.toLowerCase() + 'Input');

                // Update the description and hidden input field if they exist
                if (descriptionElement || descriptionInput) {
                    switch (category) {
                        case 'Sleep':
                            // Update hidden input field value based on the selected sleep value
                            descriptionInput.value = value.id;
                            break;
                        case 'Alertness':
                            // Update description and hidden input field value based on the selected alertness value
                            switch (value.textContent) {
                                case '1':
                                    descriptionElement.textContent = '1 = lights are on but no one is home.';
                                    descriptionInput.value = value.id;
                                    break;
                                case '2':
                                    descriptionElement.textContent = '2 = attention span of a toddler.';
                                    descriptionInput.value = value.id;
                                    break;
                                case '3':
                                    descriptionElement.textContent = '3 = Can complete a basic sudoku puzzle.';
                                    descriptionInput.value = value.id;
                                    break;
                                case '4':
                                    descriptionElement.textContent = '4 = Ready to tackle the day.';
                                    descriptionInput.value = value.id;
                                    break;
                                case '5':
                                    descriptionElement.textContent = '5 = firing on all cylinders.';
                                    descriptionInput.value = value.id;
                                    break;
                                default:
                                    descriptionElement.textContent = 'Description not available.';
                                    descriptionInput.value = value.id;
                                    break;
                            }
                            break;
                        case 'Excitement':
                        case 'Stress':
                            // Assuming stress and excitement descriptions are the same
                            switch (value.textContent) {
                                case '1':
                                    descriptionElement.textContent = '1 = Pulling my hair out.';
                                    descriptionInput.value = value.id;
                                    break;
                                case '2':
                                    descriptionElement.textContent = '2 = fairly stressed.';
                                    descriptionInput.value = value.id;
                                    break;
                                case '3':
                                    descriptionElement.textContent = '3 = Feeling ok.';
                                    descriptionInput.value = value.id;
                                    break;
                                case '4':
                                    descriptionElement.textContent = '4 = surfer level stress.';
                                    descriptionInput.value = value.id;
                                    break;
                                case '5':
                                    descriptionElement.textContent = '5 = Zen monk.';
                                    descriptionInput.value = value.id;
                                    break;
                                default:
                                    descriptionElement.textContent = 'Description not available.';
                                    descriptionInput.value = value.id;
                                    break;
                            }
                            break;
                        case 'Soreness':
                            // Update description and hidden input field value based on the selected soreness value
                            switch (value.textContent) {
                                case '1':
                                    descriptionElement.textContent = '1 = Crippled.';
                                    descriptionInput.value = value.id;
                                    break;
                                case '2':
                                    descriptionElement.textContent = '2 = very sore.';
                                    descriptionInput.value = value.id;
                                    break;
                                case '3':
                                    descriptionElement.textContent = '3 = Sore but ok to push.';
                                    descriptionInput.value = value.id;
                                    break;
                                case '4':
                                    descriptionElement.textContent = '4 = feeling good!';
                                    descriptionInput.value = value.id;
                                    break;
                                case '5':
                                    descriptionElement.textContent = '5 = like I never trained.';
                                    descriptionInput.value = value.id;
                                    break;
                                default:
                                    descriptionElement.textContent = 'Description not available.';
                                    descriptionInput.value = value.id;
                                    break;
                            }
                            break;
                        default:
                            descriptionElement.textContent = 'Description not available.';
                            break;
                    }
                }
            }


            // Define variables to store scores for each category
            var alertness = 0;
            var excitement = 0;
            var stress = 0;
            var soreness = 0;
            var sleep = 0;

            /**
             * Calculate the score based on the selected value and category.
             * @param {string} value - The selected value for the category.
             * @param {string} category - The category for which the score is being calculated.
             */
            function scoreCal(value, category) {
                // Update the score for the appropriate category based on the value
                switch (category) {
                    case 'Alertness':
                        switch (value) {
                            case '1':
                                alertness = 20;
                                break;
                            case '2':
                                alertness = 40;
                                break;
                            case '3':
                                alertness = 60;
                                break;
                            case '4':
                                alertness = 80;
                                break;
                            case '5':
                                alertness = 100;
                                break;
                            default:
                                alertness = 0;
                                break;
                        }
                        break;
                    case 'Excitement':
                        switch (value) {
                            case '1':
                                excitement = 20;
                                break;
                            case '2':
                                excitement = 40;
                                break;
                            case '3':
                                excitement = 60;
                                break;
                            case '4':
                                excitement = 80;
                                break;
                            case '5':
                                excitement = 100;
                                break;
                            default:
                                excitement = 0;
                                break;
                        }
                        break;
                    case 'Stress':
                        switch (value) {
                            case '1':
                                stress = 20;
                                break;
                            case '2':
                                stress = 40;
                                break;
                            case '3':
                                stress = 60;
                                break;
                            case '4':
                                stress = 80;
                                break;
                            case '5':
                                stress = 100;
                                break;
                            default:
                                stress = 0;
                                break;
                        }
                        break;
                    case 'Soreness':
                        switch (value) {
                            case '1':
                                soreness = 20;
                                break;
                            case '2':
                                soreness = 40;
                                break;
                            case '3':
                                soreness = 60;
                                break;
                            case '4':
                                soreness = 80;
                                break;
                            case '5':
                                soreness = 100;
                                break;
                            default:
                                soreness = 0;
                                break;
                        }
                        break;
                    case 'Sleep':
                        switch (value) {
                            case '<5':
                                sleep = 20;
                                break;
                            case '5-6':
                                sleep = 40;
                                break;
                            case '6-7':
                                sleep = 60;
                                break;
                            case '7-8':
                                sleep = 80;
                                break;
                            case '8+':
                                sleep = 100;
                                break;
                            default:
                                sleep = 0;
                                break;
                        }
                        break;
                    default:
                        break;
                }

                // Calculate the overall score as the average of all category scores
                score.value = (alertness + excitement + stress + soreness + sleep) / 5;
            }



            // data view
            document.addEventListener("DOMContentLoaded", function() {
                const sleepInputValue = document.getElementById('sleepInput').value;
                const alertnessInputValue = document.getElementById('alertnessInput').value;
                const excitementInputValue = document.getElementById('excitementInput').value;
                const stressInputValue = document.getElementById('stressInput').value;
                const sorenessInputValue = document.getElementById('sorenessInput').value;
                console.log(sleepInputValue);
                if (sleepInputValue) {
                    const buttonToClick = document.getElementById(sleepInputValue);

                    if (buttonToClick) {
                        buttonToClick.click();
                    }
                }
                if (alertnessInputValue) {
                    const buttonToClick = document.getElementById(alertnessInputValue);

                    if (buttonToClick) {
                        buttonToClick.click();
                    }
                }
                if (excitementInputValue) {
                    const buttonToClick = document.getElementById(excitementInputValue);

                    if (buttonToClick) {
                        buttonToClick.click();
                    }
                }
                if (stressInputValue) {
                    const buttonToClick = document.getElementById(stressInputValue);

                    if (buttonToClick) {
                        buttonToClick.click();
                    }
                }
                if (sorenessInputValue) {
                    const buttonToClick = document.getElementById(sorenessInputValue);

                    if (buttonToClick) {
                        buttonToClick.click();
                    }
                }
            });
        </script>
    @endsection
</body>

</html>

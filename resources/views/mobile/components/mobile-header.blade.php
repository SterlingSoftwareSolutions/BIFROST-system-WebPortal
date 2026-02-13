<header class='fixed z-20 flex items-center w-full bg-white header md:mb-4 lg:h-20 md:h-20'>
    <div class="container bg-black w-full">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-between p-2 gap-x-8">
                <!-- Logo -->
                <div class='md:mt-2'>
                    <img src="{{ asset('images/valhalla-mobile-logo.png') }}" alt="Logo" class='w-16 md:w-20' />
                </div>
            </div>
            <!-- menu -->
            <div class="navigation text-center  text-white text-xl font-bold ">
                @if (Request::is('mobile/trainingday'))
                    TRAINING DAY
                @elseif(Request::is('mobile/readinessscore'))
                    READINESS SCORE
                @elseif(Request::is('mobile/workout') || Request::is('mobile/workouttimer'))
                    WORKOUT
                @elseif(Request::is('mobile/histroyview'))
                    HISTORY
                @else
                    Default Navigation Text
                @endif
            </div>
            <!-- nav-right -->
            <div class="flex items-center h-20 justify-center ">
                <div class="flex items-center md:gap-2">
                    <!-- Link that triggers the logout form submission when clicked -->
                    <a href="#" class="text-gray-800"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <!-- Container for centering the logout icon -->
                        <div class="flex justify-center items-center mr-5 mt-2">
                            <!-- Inner container for the icon with margin adjustments for different screen sizes -->
                            <div class="relative md:mb-4 mb-4">
                                <!-- Logout icon styled with Font Awesome -->
                                <i class="fa fa-power-off text-red-500 text-2xl" aria-hidden="true"></i>
                            </div>
                        </div>
                    </a>
                    <!-- Hidden form for logging out, triggered by the above link -->
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf <!-- Laravel CSRF token for security -->
                        <input type="text" value="mobile" name="type" hidden>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

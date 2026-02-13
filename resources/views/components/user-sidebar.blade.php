<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Source Sans Pro' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <!-- Sidebar -->
    <aside id="sidebar"
        class="relative bg-white h-full transition-all duration-700 flex flex-col text-[16px]  mt-32 md:mt-20 transition-width">

        <ul class="flex flex-col space-y-1 overflow-y-auto overflow-x-hidden scrollbar">
            <li>
                <a href="{{ route('userdashboard') }}"
                    class="flex items-center space-x-2 py-2 px-4 rounded-md text-black transition-colors duration-300 {{ request()->routeIs('userdashboard') ? 'bg-[#F4F4F4] text-black' : 'text-black' }}">
                    <!-- Icon -->
                    <i class="bi bi-grid-3x3-gap w-5 h-5 m-3" onclick="toggleSidebar()"></i>
                    <!-- Text -->
                    <span class="sidebar-item-text">Dashboard</span>
                </a>
                <hr class="border-t border-black" />
            </li>
            <li>
                <a href="{{ route('userprofile') }}"
                    class="flex items-center space-x-2 py-2 px-4 rounded-md text-black transition-colors duration-300 {{ request()->routeIs('userprofile') ? 'bg-[#F4F4F4] text-black' : 'text-black' }}">
                    <!-- Icon -->
                    <i class="bi bi-people w-5 h-5 m-3" onclick="toggleSidebar()"></i>
                    <!-- Text -->
                    <span class="sidebar-item-text">Profile</span>
                </a>
                <hr class="border-t border-black" />
            </li>
            <li>
                <a href="{{ route('usergoal') }}"
                    class="flex items-center space-x-2 py-2 px-4 rounded-md text-black transition-colors duration-300 {{ request()->routeIs('usergoal') ? 'bg-[#F4F4F4] text-black' : 'text-black' }}">
                    <!-- Icon -->
                    <i class="bi bi-flag w-5 h-5 m-3" onclick="toggleSidebar()"></i>
                    <!-- Text -->
                    <span class="sidebar-item-text whitespace-nowrap">Goals (SMART)</span>
                </a>
                <hr class="border-t border-black" />
            </li>
            <li>
                <a href="{{ route('userachievements') }}"
                    class="flex items-center space-x-2 py-2 px-4 rounded-md text-black transition-colors duration-300 {{ request()->routeIs('userachievements') ? 'bg-[#F4F4F4] text-black' : 'text-black' }}">
                    <!-- Icon -->
                    <i class="bi bi-universal-access-circle w-5 h-5 m-3" onclick="toggleSidebar()"></i>
                    <!-- Text -->
                    <span class="sidebar-item-text">Achievements</span>
                </a>
                <hr class="border-t border-black" />
            </li>
            <li>
                <a href="{{ route('usersetting') }}"
                    class="flex items-center space-x-2 py-2 px-4 rounded-md text-black transition-colors duration-300 {{ request()->routeIs('usersetting') ? 'bg-[#F4F4F4] text-black' : 'text-black' }}">
                    <!-- Icon -->
                    <i class="bi bi-gear w-5 h-5 m-3" onclick="toggleSidebar()"></i>
                    <!-- Text -->
                    <span class="sidebar-item-text">Settings</span>
                </a>
                <hr class="border-t border-black" />
            </li>
        </ul>
    </aside>
</body>

</html>

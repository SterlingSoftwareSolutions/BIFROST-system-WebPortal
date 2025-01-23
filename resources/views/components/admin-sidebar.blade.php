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
        class="relative  bg-white h-full transition-all duration-700 flex flex-col text-lg mt-20 transition-width">

        <ul class="flex flex-col space-y-1 overflow-y-auto overflow-x-hidden scrollbar">
            <li>
                <a href="{{ route('admindashboard') }}"
                    class="flex items-center space-x-2 py-2 px-4 rounded-md text-black transition-colors duration-300">
                    <!-- Icon -->
                    <i class="bi bi-grid-3x3-gap w-5 h-5 m-3" onclick="toggleSidebar()"></i>
                    <!-- Text -->
                    <span class="sidebar-item-text whitespace-nowrap">Dashboard</span>
                </a>
                <hr class="border-t border-black" />
            </li>
            <li>
                <a href="{{ route('admindaaccess') }}"
                    class="flex items-center space-x-2 py-2 px-4 rounded-md text-black transition-colors duration-300">
                    <!-- Icon -->
                    <i class="bi bi-key w-5 h-5 m-3" onclick="toggleSidebar()"></i>
                    <!-- Text -->
                    <span class="sidebar-item-text whitespace-nowrap">Access</span>
                </a>
                <hr class="border-t border-black" />
            </li>
            <li>
                <a href="{{ route('viewadminclientmanagement') }}"
                    class="flex items-center space-x-2 py-2 px-4 rounded-md text-black transition-colors duration-300">
                    <!-- Icon -->
                    <i class="bi bi-people w-5 h-5 m-3" onclick="toggleSidebar()"></i>
                    <!-- Text -->
                    <span class="sidebar-item-text whitespace-nowrap">Client Management</span>
                </a>
                <hr class="border-t border-black" />
            </li>
            <li>
                <a href="{{ route('viewworkoutlibrary') }}"
                    class="flex items-center space-x-2 py-2 px-4 rounded-md text-black transition-colors duration-300">
                    <!-- Icon -->
                    <i class="bi bi-person-gear w-5 h-5 m-3" onclick="toggleSidebar()"></i>
                    <!-- Text -->
                    <span class="sidebar-item-text whitespace-nowrap">Exercise Library</span>
                </a>
                <hr class="border-t border-black" />
            </li>
            <li>
                <a href="{{ route('viewsession') }}"
                    class="flex items-center space-x-2 py-2 px-4 rounded-md text-black transition-colors duration-300">
                    <!-- Icon -->
                    <i class="bi bi-list-check w-5 h-5 m-3" onclick="toggleSidebar()"></i>
                    <!-- Text -->
                    <span class="sidebar-item-text whitespace-nowrap">Workout Builder</span>
                </a>
                <hr class="border-t border-black" />
            </li>
            <li>
                <a href="{{ route('adminfinancial') }}"
                    class="flex items-center space-x-2 py-2 px-4 rounded-md text-black transition-colors duration-300">
                    <!-- Icon -->
                    <i class="bi bi-coin w-5 h-5 m-3" onclick="toggleSidebar()"></i>
                    <!-- Text -->
                    <span class="sidebar-item-text whitespace-nowrap">Financial</span>
                </a>
                <hr class="border-t border-black" />
            </li>
            <li>
                <a href="{{ route('viewviewcommunication') }}"
                    class="flex items-center space-x-2 py-2 px-4 rounded-md text-black transition-colors duration-300">
                    <!-- Icon -->
                    <i class="bi bi-broadcast-pin w-5 h-5 m-3" onclick="toggleSidebar()"></i>
                    <!-- Text -->
                    <span class="sidebar-item-text whitespace-nowrap">Communication</span>
                </a>
                <hr class="border-t border-black" />
            </li>
            <li>
                <a href="{{ route('viewviewstatistics') }}"
                    class="flex items-center space-x-2 py-2 px-4 rounded-md text-black transition-colors duration-300">
                    <!-- Icon -->
                    <i class="bi bi-bar-chart w-5 h-5 m-3" onclick="toggleSidebar()"></i>
                    <!-- Text -->
                    <span class="sidebar-item-text whitespace-nowrap">Statistics</span>
                </a>
                <hr class="border-t border-black" />
            </li>
        </ul>
    </aside>
</body>

</html>

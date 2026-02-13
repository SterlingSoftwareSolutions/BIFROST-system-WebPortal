function checkScreenSize() {
    const screenWidth = window.innerWidth;
    const container = document.getElementById('container');
    const sidebar = document.getElementById('sidebar');

    if (screenWidth <= 720) {
        container.classList.add('w-[100%]');
        sidebar.style.width = '0'; // Hide sidebar fully
    }
}

function toggleSidebar() {
    console.log("Sidebar toggled");
    const container = document.getElementById('container');
    const sidebar = document.getElementById('sidebar');
    const screenWidth = window.innerWidth;

    if (screenWidth <= 720) {
        // Toggle sidebar visibility for mobile view
        if (container.classList.contains('w-[100%]')) {
            container.classList.remove('w-[100%]');
            sidebar.style.width = '16rem'; // Show sidebar fully
        } else {
            container.classList.add('w-[100%]');
            sidebar.style.width = '0'; // Hide sidebar fully
        }
    } else {
        // Toggle sidebar width for larger screens
        if (container.classList.contains('w-[60%]')) {
            container.classList.remove('w-[60%]');
            sidebar.style.width = '16rem'; // Set sidebar width to 16rem
        } else {
            container.classList.add('w-[60%]');
            sidebar.style.width = '4rem'; // Set sidebar width to 4rem
        }
    }
}
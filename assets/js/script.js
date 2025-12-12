document.addEventListener('DOMContentLoaded', function() {
    // Toggle submenu
    const menuToggles = document.querySelectorAll('.menu-toggle');
    
    menuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.parentElement;
            
            // Close other open menus
            document.querySelectorAll('.nav-item.active').forEach(item => {
                if (item !== parent) {
                    item.classList.remove('active');
                }
            });
            
            // Toggle current menu
            parent.classList.toggle('active');
        });
    });

    // Set active menu based on current page
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.nav-item a');
    
    menuItems.forEach(item => {
        if (currentPath.includes(item.getAttribute('href'))) {
            item.closest('.nav-item').classList.add('active');
            if (item.closest('.submenu')) {
                item.closest('.nav-item.has-submenu').classList.add('active');
            }
        }
    });
});
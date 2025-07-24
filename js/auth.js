// Authentication and session management for all pages
$(document).ready(function() {
    // Check session on every page load
    checkAuthStatus();
    
    // Prevent back button access for logged out users
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            checkAuthStatus();
        }
    });
});

// Check authentication status and redirect if needed
function checkAuthStatus() {
    $.ajax({
        url: 'api/check_session.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            const currentPage = window.location.pathname.split('/').pop();
            const protectedPages = ['customer_dashboard.html', 'walker_dashboard.php', 'walker_dashboard_simple.php', 'customer_profile.html', 'walker_profile.html'];
            
            if (!response.logged_in && protectedPages.includes(currentPage)) {
                // User not logged in but trying to access protected page
                window.location.href = 'login.html';
                return;
            }
            
            if (response.logged_in && (currentPage === 'login.html' || currentPage === 'register.html')) {
                // User already logged in but on login/register page
                if (response.user.role === 'walker') {
                    window.location.href = 'walker_dashboard_simple.php';
                } else {
                    window.location.href = 'customer_dashboard.html';
                }
                return;
            }
            
            // Update navigation if on main page
            if (currentPage === 'index.html' || currentPage === '') {
                updateNavigation(response);
            }
        },
        error: function() {
            // If session check fails, assume not logged in
            const currentPage = window.location.pathname.split('/').pop();
            const protectedPages = ['customer_dashboard.html', 'walker_dashboard.php', 'walker_dashboard_simple.php', 'customer_profile.html', 'walker_profile.html'];
            
            if (protectedPages.includes(currentPage)) {
                window.location.href = 'login.html';
            }
        }
    });
}

// Update navigation based on auth status
function updateNavigation(response) {
    if (response.logged_in) {
        showLoggedInNav(response.user);
    } else {
        showGuestNav();
    }
}

// Show navigation for logged-in users (global function for auth.js)
function showLoggedInNav(user) {
    $('#guestNav').hide();
    $('#userName').text(user.first_name);
    
    // Set dashboard and profile links based on role
    if (user.role === 'walker') {
        $('#dashboardLink').attr('href', 'walker_dashboard_simple.php').html('<i class="fas fa-tachometer-alt"></i> Walker Dashboard');
        $('#profileLink').attr('href', 'walker_profile.html').html('<i class="fas fa-user"></i> Walker Profile');
        $('#bookingsLink').attr('href', 'walker_bookings.php').html('<i class="fas fa-calendar-check"></i> My Schedule');
    } else {
        $('#dashboardLink').attr('href', 'customer_dashboard.html').html('<i class="fas fa-tachometer-alt"></i> Dashboard');
        $('#profileLink').attr('href', 'customer_profile.html').html('<i class="fas fa-user"></i> My Profile');
        $('#bookingsLink').attr('href', 'customer_bookings.php').html('<i class="fas fa-calendar-alt"></i> My Bookings');
    }
    
    $('#userNav').show();
    setupUserDropdown();
}

// Show navigation for guests (global function for auth.js)
function showGuestNav() {
    $('#userNav').hide();
    $('#guestNav').show();
}

// Setup user dropdown functionality
function setupUserDropdown() {
    // Toggle dropdown on click
    $('#userDropdownToggle').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.user-dropdown').toggleClass('active');
    });
    
    // Close dropdown when clicking outside
    $(document).off('click.dropdown').on('click.dropdown', function(e) {
        if (!$(e.target).closest('.user-dropdown').length) {
            $('.user-dropdown').removeClass('active');
        }
    });
    
    // Prevent dropdown from closing when clicking inside
    $('.user-dropdown-menu').off('click').on('click', function(e) {
        e.stopPropagation();
    });
    
    // Close dropdown when clicking dropdown items (except logout)
    $('.dropdown-item:not(#logoutBtn)').off('click').on('click', function(e) {
        $('.user-dropdown').removeClass('active');
    });
    
    // Setup logout functionality in dropdown
    $('#logoutBtn').off('click').on('click', function(e) {
        e.preventDefault();
        performLogout();
    });
}

// Logout function that can be called from any page
function performLogout() {
    $.ajax({
        url: 'api/logout.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Clear any cached data
                sessionStorage.clear();
                localStorage.clear();
                
                // Redirect to login page
                window.location.href = 'login.html';
            } else {
                alert('Logout failed: ' + response.message);
            }
        },
        error: function() {
            alert('Logout failed. Please try again.');
        }
    });
}

// Prevent browser back button after logout
function preventBackAfterLogout() {
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
}

// Initialize user dropdown for dashboard pages (specific function for walker_dashboard_simple.php)
function initializeUserDropdown() {
    // Toggle dropdown on click
    $('#userDropdownBtn').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.user-dropdown').toggleClass('active');
    });
    
    // Close dropdown when clicking outside
    $(document).off('click.walkerDropdown').on('click.walkerDropdown', function(e) {
        if (!$(e.target).closest('.user-dropdown').length) {
            $('.user-dropdown').removeClass('active');
        }
    });
    
    // Prevent dropdown from closing when clicking inside menu
    $('.user-dropdown-menu').off('click').on('click', function(e) {
        e.stopPropagation();
    });
}
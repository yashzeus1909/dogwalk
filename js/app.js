$(document).ready(function() {
    checkUserSession();
    loadWalkers();
    setupFilters();
    setupLogout();
});

// Check if user is logged in and update navigation
function checkUserSession() {
    $.ajax({
        url: 'api/check_session.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.logged_in) {
                showLoggedInNav(response.user);
            } else {
                showGuestNav();
            }
        },
        error: function() {
            showGuestNav();
        }
    });
}

// Show navigation for logged-in users
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

// Show navigation for guests
function showGuestNav() {
    $('#userNav').hide();
    $('#guestNav').show();
}

// Setup logout functionality
function setupLogout() {
    $('#logoutBtn').click(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'api/logout.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Clear any cached data
                    sessionStorage.clear();
                    localStorage.clear();
                    
                    // Show guest navigation
                    showGuestNav();
                    
                    // Redirect to home page
                    window.location.href = 'index.html';
                } else {
                    alert('Logout failed: ' + response.message);
                }
            },
            error: function() {
                alert('Logout failed. Please try again.');
            }
        });
    });
}

function setupFilters() {
    // Filter functionality
    $('#serviceFilter, #priceFilter').on('change', function() {
        loadWalkers();
    });
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
    
    // Close dropdown when clicking dropdown items (except logout which is handled separately)
    $('.dropdown-item:not(#logoutBtn)').off('click').on('click', function(e) {
        $('.user-dropdown').removeClass('active');
    });
}

function showWalkers() {
    $('html, body').animate({
        scrollTop: $('#walkers').offset().top - 70
    }, 800);
}

function loadWalkers() {
    const serviceFilter = $('#serviceFilter').val();
    const priceFilter = $('#priceFilter').val();
    
    $.ajax({
        url: 'api/get_walkers.php',
        type: 'GET',
        data: {
            service: serviceFilter,
            price: priceFilter
        },
        success: function(response) {
            if (response.success) {
                displayWalkers(response.walkers);
            } else {
                console.error('Error loading walkers:', response.message);
            }
        },
        error: function() {
            console.error('Failed to load walkers');
        }
    });
}

function displayWalkers(walkers) {
    const grid = $('#walkersGrid');
    grid.empty();
    
    if (walkers.length === 0) {
        grid.html('<p class="no-walkers">No walkers found matching your criteria.</p>');
        return;
    }
    
    walkers.forEach(walker => {
        const badges = JSON.parse(walker.badges || '[]');
        const services = JSON.parse(walker.services || '[]');
        
        const badgeHTML = badges.map(badge => {
            const badgeClass = badge.toLowerCase().includes('verified') ? 'verified' : 
                              badge.toLowerCase().includes('insured') ? 'insured' : '';
            return `<span class="badge ${badgeClass}">${badge}</span>`;
        }).join('');
        
        const starsHTML = generateStars(parseFloat(walker.rating));
        
        const walkerCard = `
            <div class="walker-card">
                <div class="walker-header">
                    <img src="${walker.image}" alt="${walker.first_name} ${walker.last_name}" class="walker-image">
                    <div class="walker-info">
                        <h3>${walker.first_name} ${walker.last_name}</h3>
                        <div class="walker-rating">
                            <span class="stars">${starsHTML}</span>
                            <span>${walker.rating} (${walker.review_count} reviews)</span>
                        </div>
                        <div class="walker-distance">${walker.distance}</div>
                        <div class="walker-price">$${walker.price}/walk</div>
                    </div>
                </div>
                <div class="walker-body">
                    <p class="walker-description">${walker.description}</p>
                    <div class="walker-badges">${badgeHTML}</div>
                    <div class="walker-services">
                        <strong>Services:</strong> ${services.join(', ')}
                    </div>
                    <div class="walker-availability">
                        <strong>Available:</strong> ${walker.availability}
                    </div>
                    <button class="book-button" onclick="bookWalker(${walker.id})">Book Now</button>
                </div>
            </div>
        `;
        
        grid.append(walkerCard);
    });
}

function generateStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    let starsHTML = '';
    
    for (let i = 0; i < fullStars; i++) {
        starsHTML += '<i class="fas fa-star"></i>';
    }
    
    if (hasHalfStar) {
        starsHTML += '<i class="fas fa-star-half-alt"></i>';
    }
    
    const emptyStars = 5 - Math.ceil(rating);
    for (let i = 0; i < emptyStars; i++) {
        starsHTML += '<i class="far fa-star"></i>';
    }
    
    return starsHTML;
}

function bookWalker(walkerId) {
    // Check if user is logged in
    $.ajax({
        url: 'api/check_auth.php',
        type: 'GET',
        success: function(response) {
            if (response.authenticated) {
                showBookingModal(walkerId);
            } else {
                alert('Please log in to book a walker.');
                window.location.href = 'login.html';
            }
        },
        error: function() {
            alert('Please log in to book a walker.');
            window.location.href = 'login.html';
        }
    });
}

function showBookingModal(walkerId) {
    const modalHTML = `
        <div id="bookingModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Book a Dog Walk</h2>
                <form id="bookingForm">
                    <input type="hidden" name="walker_id" value="${walkerId}">
                    
                    <div class="form-group">
                        <label for="dog_name">Dog's Name:</label>
                        <input type="text" id="dog_name" name="dog_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="dog_size">Dog's Size:</label>
                        <select id="dog_size" name="dog_size" required>
                            <option value="">Select size</option>
                            <option value="Small">Small (under 25 lbs)</option>
                            <option value="Medium">Medium (25-60 lbs)</option>
                            <option value="Large">Large (60-90 lbs)</option>
                            <option value="Extra Large">Extra Large (over 90 lbs)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="booking_date">Date:</label>
                        <input type="date" id="booking_date" name="booking_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="booking_time">Time:</label>
                        <input type="time" id="booking_time" name="booking_time" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="duration">Duration (minutes):</label>
                        <select id="duration" name="duration" required>
                            <option value="30">30 minutes</option>
                            <option value="60">1 hour</option>
                            <option value="90">1.5 hours</option>
                            <option value="120">2 hours</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <textarea id="address" name="address" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="special_notes">Special Notes:</label>
                        <textarea id="special_notes" name="special_notes" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" class="form-button">Book Walk</button>
                </form>
            </div>
        </div>
    `;
    
    $('body').append(modalHTML);
    $('#bookingModal').show();
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    $('#booking_date').attr('min', today);
    
    // Close modal functionality
    $('.close, #bookingModal').click(function(e) {
        if (e.target === this) {
            $('#bookingModal').remove();
        }
    });
    
    // Handle form submission
    $('#bookingForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'api/create_booking.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    alert('Booking created successfully!');
                    $('#bookingModal').remove();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Failed to create booking. Please try again.');
            }
        });
    });
}
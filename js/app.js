// Global variables
const API_BASE = 'api/';
let walkers = [];
let bookings = [];
let currentWalker = null;

// Global functions

$(document).ready(function() {

    // Initialize the app
    loadWalkers();
    loadBookings();
    loadProfileData();
    // Remove auth check that was causing redirects
    
    // Check for booking success message
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('booking_success') === '1') {
        showToast('Booking confirmed successfully!', 'success');
    }

    // Navigation handling
    $('.nav-item').click(function() {
        const page = $(this).data('page');
        showPage(page);
        
        // Update active nav item
        $('.nav-item').removeClass('active').addClass('text-gray-500');
        $(this).removeClass('text-gray-500').addClass('active');
    });

    // Profile tab handling
    $('.profile-tab').click(function() {
        const tab = $(this).data('tab');
        showProfileTab(tab);
        
        // Update active tab
        $('.profile-tab').removeClass('border-blue-600 text-blue-600').addClass('border-transparent text-gray-500');
        $(this).removeClass('border-transparent text-gray-500').addClass('border-blue-600 text-blue-600');
    });

    // Search functionality
    $('#searchBtn').click(function() {
        const location = $('#searchLocation').val();
        const serviceType = $('#serviceType').val();
        searchWalkers(location, serviceType);
    });

    // Booking modal code removed - now using separate booking page

    // Profile form submission
    $('#profileForm').submit(function(e) {
        e.preventDefault();
        updateProfile();
    });

    // Functions
    function showPage(page) {
        $('.page').removeClass('active');
        $(`#${page}Page`).addClass('active');
        
        // Load page-specific data
        if (page === 'bookings') {
            loadBookings();
        } else if (page === 'profile') {
            loadProfileBookings();
        }
    }

    function showProfileTab(tab) {
        $('.profile-tab-content').addClass('hidden');
        
        switch(tab) {
            case 'info':
                $('#profileInfoTab').removeClass('hidden');
                break;
            case 'bookings':
                $('#profileBookingsTab').removeClass('hidden');
                loadProfileBookings();
                break;
            case 'settings':
                $('#profileSettingsTab').removeClass('hidden');
                break;
        }
    }

    function loadWalkers() {
        $('#loadingState').addClass('show');
        
        $.ajax({
            url: API_BASE + 'walkers.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                walkers = data;
                displayWalkers(walkers);
                $('#loadingState').removeClass('show');
            },
            error: function() {
                $('#loadingState').removeClass('show');
                showToast('Failed to load walkers', 'error');
            }
        });
    }

    function displayWalkers(walkersToShow) {
        const grid = $('#walkersGrid');
        grid.empty();

        if (walkersToShow.length === 0) {
            $('#emptyState').removeClass('hidden');
            return;
        }

        $('#emptyState').addClass('hidden');
        walkersToShow.forEach(walker => {
            const walkerCard = createWalkerCard(walker);
            grid.append(walkerCard);
        });
        
        bindWalkerEvents();
    }

    function createWalkerCard(walker) {
        const rating = (walker.rating / 10).toFixed(1);
        const badges = walker.badges.map(badge => 
            `<span class="badge bg-blue-100 text-blue-800">${badge}</span>`
        ).join(' ');

        return `
            <div class="walker-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start space-x-4 mb-4">
                        <img src="${walker.image}" alt="${walker.name}" class="w-16 h-16 rounded-full object-cover">
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg">${walker.name}</h3>
                            <div class="flex items-center mt-1">
                                <div class="flex text-yellow-400">
                                    ${Array(5).fill().map((_, i) => 
                                        `<i class="fas fa-star ${i < Math.floor(rating) ? '' : 'text-gray-300'}"></i>`
                                    ).join('')}
                                </div>
                                <span class="ml-2 text-sm text-gray-600">${rating} (${walker.reviewCount} reviews)</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">${walker.distance}</p>
                        </div>
                    </div>
                    
                    <p class="text-gray-700 text-sm mb-4">${walker.description}</p>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        ${badges}
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-bold text-green-600">$${walker.price}</span>
                            <span class="text-gray-500 text-sm">/hour</span>
                        </div>
                        <div class="flex gap-2">
                            <button class="book-walker-btn bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200" 
                                    data-walker-id="${walker.id}">
                                Book Now
                            </button>
                        </div>
                    </div>
                    
                    <div class="mt-3 text-sm text-green-600">
                        <i class="fas fa-clock mr-1"></i>${walker.availability}
                    </div>
                </div>
            </div>
        `;
    }

    function searchWalkers(location, serviceType) {
        $('#loadingState').addClass('show');
        
        const params = new URLSearchParams();
        params.append('search', '1');
        if (location) params.append('location', location);
        if (serviceType) params.append('service_type', serviceType);
        
        $.ajax({
            url: API_BASE + 'walkers.php?' + params.toString(),
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                walkers = data;
                displayWalkers(walkers);
                $('#loadingState').removeClass('show');
            },
            error: function() {
                $('#loadingState').removeClass('show');
                showToast('Search failed. Please try again.', 'error');
            }
        });
    }

    function bindWalkerEvents() {
        $(document).on('click', '.book-walker-btn', function(e) {
            e.preventDefault();
            const walkerId = parseInt($(this).data('walker-id'));
            console.log('Book walker clicked, redirecting to booking page for walker ID:', walkerId);
            window.location.href = `booking.php?walker_id=${walkerId}`;
        });
    }



    // Booking modal functions removed - now using separate booking page

    function updatePricingSummary() {
        if (!currentWalker) return;
        
        const duration = parseInt($('#duration').val()) || 60;
        const hourlyRate = currentWalker.price;
        const serviceFee = Math.round(hourlyRate * (duration / 60) * 100); // in cents
        const appFee = Math.round(serviceFee * 0.15); // 15% app fee
        const total = serviceFee + appFee;

        $('#pricingSummary').html(`
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Service (${duration} min)</span>
                    <span>$${(serviceFee / 100).toFixed(2)}</span>
                </div>
                <div class="flex justify-between">
                    <span>App fee</span>
                    <span>$${(appFee / 100).toFixed(2)}</span>
                </div>
                <div class="border-t pt-2 flex justify-between font-semibold">
                    <span>Total</span>
                    <span>$${(total / 100).toFixed(2)}</span>
                </div>
            </div>
        `);
    }

    function submitBooking() {
        if (!currentWalker) return;

        const duration = parseInt($('#duration').val());
        const hourlyRate = currentWalker.price;
        const serviceFee = Math.round(hourlyRate * (duration / 60) * 100);
        const appFee = Math.round(serviceFee * 0.15);
        const total = serviceFee + appFee;

        const bookingData = {
            walker_id: currentWalker.id,
            dog_name: $('#dogName').val(),
            dog_size: $('#dogSize').val(),
            booking_date: $('#bookingDate').val(),
            booking_time: $('#bookingTime').val(),
            duration: duration,
            customer_name: $('#customerName').val(),
            customer_email: $('#customerEmail').val(),
            phone: $('#phone').val(),
            address: $('#customerAddress').val(),
            special_notes: $('#instructions').val(),
            total_price: (total / 100).toFixed(2)
        };

        // Validate required fields
        if (!bookingData.dog_name || !bookingData.dog_size || !bookingData.booking_date || 
            !bookingData.booking_time || !bookingData.customer_name || !bookingData.customer_email || 
            !bookingData.phone || !bookingData.address) {
            showToast('Please fill in all required fields', 'error');
            return;
        }

        // Disable submit button
        $('#confirmBooking').prop('disabled', true).text('Booking...');

        $.ajax({
            url: API_BASE + 'add_booking.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(bookingData),
            success: function(response) {
                if (response.success) {
                    closeBookingModal();
                    showToast('Booking request submitted successfully!', 'success');
                    loadBookings(); // Refresh bookings
                } else {
                    showToast('Error: ' + response.message, 'error');
                }
            },
            error: function() {
                showToast('Failed to submit booking. Please try again.', 'error');
            },
            complete: function() {
                $('#confirmBooking').prop('disabled', false).text('Book Now');
            }
        });
    }

    function loadBookings() {
        const container = $('#bookingsList');
        container.html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Loading bookings...</div>');

        $.ajax({
            url: API_BASE + 'bookings.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                bookings = data;
                displayBookings(bookings, container);
            },
            error: function() {
                container.html(`
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                        <p>Failed to load bookings</p>
                        <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" onclick="loadBookings()">
                            Try Again
                        </button>
                    </div>
                `);
            }
        });
    }

    function displayBookings(bookingsToShow, container) {
        container.empty();

        if (bookingsToShow.length === 0) {
            container.html(`
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-calendar text-4xl mb-4"></i>
                    <p>No bookings yet</p>
                    <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" onclick="$('.nav-item[data-page=home]').click()">
                        Find a Walker
                    </button>
                </div>
            `);
            return;
        }

        bookingsToShow.forEach(booking => {
            const bookingCard = createBookingCard(booking);
            container.append(bookingCard);
        });

        // Bind status update events
        $('.status-btn').click(function() {
            const bookingId = $(this).data('booking-id');
            const newStatus = $(this).data('status');
            updateBookingStatus(bookingId, newStatus);
        });
    }

    function createBookingCard(booking) {
        const statusColors = {
            pending: 'bg-yellow-100 text-yellow-800',
            confirmed: 'bg-green-100 text-green-800',
            completed: 'bg-blue-100 text-blue-800',
            cancelled: 'bg-red-100 text-red-800'
        };

        const walkerImage = booking.walkerImage || 'https://via.placeholder.com/40x40?text=W';
        const walkerName = booking.walkerName || 'Unknown Walker';

        return `
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <img src="${walkerImage}" alt="${walkerName}" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <h4 class="font-semibold">${walkerName}</h4>
                            <p class="text-sm text-gray-600">Walking ${booking.dogName} (${booking.dogSize})</p>
                        </div>
                    </div>
                    <span class="badge ${statusColors[booking.status] || 'bg-gray-100 text-gray-800'}">
                        ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calendar text-gray-500"></i>
                        <span>${booking.date}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-clock text-gray-500"></i>
                        <span>${booking.time} (${booking.duration}min)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-phone text-gray-500"></i>
                        <span>${booking.phone}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-envelope text-gray-500"></i>
                        <span>${booking.email}</span>
                    </div>
                </div>
                
                <div class="mt-3 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-dollar-sign text-gray-500"></i>
                        Total: $${booking.totalPrice || '0.00'}
                    </div>
                    <div class="text-xs text-gray-500">
                        Booking #${booking.id}
                    </div>
                </div>
                
                <div class="mt-3 flex gap-2">
                    ${booking.status === 'pending' ? `
                        <button class="status-btn px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700" 
                                data-booking-id="${booking.id}" data-status="confirmed">
                            Confirm
                        </button>
                        <button class="status-btn px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700" 
                                data-booking-id="${booking.id}" data-status="cancelled">
                            Cancel
                        </button>
                    ` : ''}
                    ${booking.status === 'confirmed' ? `
                        <button class="status-btn px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700" 
                                data-booking-id="${booking.id}" data-status="completed">
                            Mark Complete
                        </button>
                        <button class="status-btn px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700" 
                                data-booking-id="${booking.id}" data-status="cancelled">
                            Cancel
                        </button>
                    ` : ''}
                    ${booking.status === 'completed' || booking.status === 'cancelled' ? `
                        <span class="px-3 py-1 text-xs bg-gray-200 text-gray-600 rounded">
                            ${booking.status === 'completed' ? 'Service Completed' : 'Booking Cancelled'}
                        </span>
                    ` : ''}
                </div>
                
                ${booking.instructions ? `
                    <div class="mt-3 p-3 bg-white rounded-lg">
                        <p class="text-sm"><strong>Instructions:</strong> ${booking.instructions}</p>
                    </div>
                ` : ''}
            </div>
        `;
    }

    function updateBookingStatus(bookingId, newStatus) {
        const statusText = {
            confirmed: 'confirm',
            cancelled: 'cancel',
            completed: 'complete'
        };

        if (confirm(`Are you sure you want to ${statusText[newStatus]} this booking?`)) {
            $.ajax({
                url: 'api/update_booking_status.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    id: bookingId,
                    status: newStatus
                }),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showToast(`Booking ${statusText[newStatus]}ed successfully!`, 'success');
                        loadBookings(); // Refresh the bookings list
                    } else {
                        showToast('Error: ' + response.message, 'error');
                    }
                },
                error: function() {
                    showToast('Failed to update booking status. Please try again.', 'error');
                }
            });
        }
    }

    function loadProfileData() {
        // In a real app, this would fetch from server
        const profileData = {
            firstName: 'John',
            lastName: 'Doe',
            email: 'john.doe@example.com',
            phone: '555-0123',
            address: '123 Main St, New York, NY 10001'
        };

        $('#profileName').text(`${profileData.firstName} ${profileData.lastName}`);
        $('#profileEmail').text(profileData.email);
        $('#firstName').val(profileData.firstName);
        $('#lastName').val(profileData.lastName);
        $('#email').val(profileData.email);
        $('#phoneNumber').val(profileData.phone);
        $('#address').val(profileData.address);
    }

    function loadProfileBookings() {
        const container = $('#profileBookingsList');
        container.html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Loading your bookings...</div>');

        const userEmail = $('#email').val() || 'john.doe@example.com'; // Default for demo

        $.ajax({
            url: API_BASE + 'profile-bookings.php?email=' + encodeURIComponent(userEmail),
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                displayBookings(data, container);
            },
            error: function() {
                container.html(`
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                        <p>Failed to load your bookings</p>
                    </div>
                `);
            }
        });
    }

    function updateProfile() {
        const profileData = {
            firstName: $('#firstName').val(),
            lastName: $('#lastName').val(),
            email: $('#email').val(),
            phone: $('#phoneNumber').val(),
            address: $('#address').val()
        };

        // Update profile display
        $('#profileName').text(`${profileData.firstName} ${profileData.lastName}`);
        $('#profileEmail').text(profileData.email);

        showToast('Profile updated successfully!', 'success');
    }

    function showToast(message, type = 'success') {
        const toast = $('#toast');
        const toastMessage = $('#toastMessage');
        
        // Set message and style
        toastMessage.text(message);
        
        if (type === 'error') {
            toast.removeClass('bg-green-500').addClass('bg-red-500');
            toast.find('i').removeClass('fa-check-circle').addClass('fa-exclamation-circle');
        } else {
            toast.removeClass('bg-red-500').addClass('bg-green-500');
            toast.find('i').removeClass('fa-exclamation-circle').addClass('fa-check-circle');
        }
        
        // Show toast
        toast.removeClass('translate-x-full');
        
        // Hide after 3 seconds
        setTimeout(() => {
            toast.addClass('translate-x-full');
        }, 3000);
    }

    // Initial event binding
    bindWalkerEvents();
});
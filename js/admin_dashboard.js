$(document).ready(function() {
    // Load bookings on page load
    loadBookings();
    
    // Event handlers
    $('#logoutBtn').click(logout);
    $('#refreshBtn').click(loadBookings);
    $('#statusFilter').change(loadBookings);
});

function loadBookings() {
    const statusFilter = $('#statusFilter').val();
    
    $.ajax({
        url: 'api/get_walker_bookings.php',
        method: 'GET',
        data: { status: statusFilter },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayBookings(response.bookings);
                updateStats(response.stats);
            } else {
                showToast('Error loading bookings: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading bookings:', error);
            showToast('Failed to load bookings. Please try again.', 'error');
        }
    });
}

function displayBookings(bookings) {
    const tbody = $('#bookingsTableBody');
    const noBookings = $('#noBookings');
    
    if (!bookings || bookings.length === 0) {
        tbody.empty();
        noBookings.removeClass('hidden');
        return;
    }
    
    noBookings.addClass('hidden');
    
    let html = '';
    bookings.forEach(booking => {
        const statusColor = getStatusColor(booking.status);
        const statusIcon = getStatusIcon(booking.status);
        
        html += `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    #${booking.id}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                        <div class="text-sm font-medium text-gray-900">${booking.customer_name || 'N/A'}</div>
                        <div class="text-sm text-gray-500">${booking.customer_email || ''}</div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${formatDate(booking.booking_date)}</div>
                    <div class="text-sm text-gray-500">${booking.booking_time}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${booking.duration} hour${booking.duration > 1 ? 's' : ''}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    $${parseFloat(booking.total_price).toFixed(2)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColor}">
                        <i class="${statusIcon} mr-1"></i>
                        ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                    ${getActionButtons(booking)}
                </td>
            </tr>
        `;
    });
    
    tbody.html(html);
    bindBookingEvents();
}

function getStatusColor(status) {
    switch(status.toLowerCase()) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'confirmed': return 'bg-blue-100 text-blue-800';
        case 'completed': return 'bg-green-100 text-green-800';
        case 'cancelled': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function getStatusIcon(status) {
    switch(status.toLowerCase()) {
        case 'pending': return 'fas fa-clock';
        case 'confirmed': return 'fas fa-check';
        case 'completed': return 'fas fa-check-circle';
        case 'cancelled': return 'fas fa-times-circle';
        default: return 'fas fa-question-circle';
    }
}

function getActionButtons(booking) {
    let buttons = '';
    
    switch(booking.status.toLowerCase()) {
        case 'pending':
            buttons = `
                <button class="confirm-btn bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600 transition duration-200" 
                        data-booking-id="${booking.id}">
                    <i class="fas fa-check mr-1"></i>Confirm
                </button>
                <button class="cancel-btn bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition duration-200" 
                        data-booking-id="${booking.id}">
                    <i class="fas fa-times mr-1"></i>Cancel
                </button>
            `;
            break;
        case 'confirmed':
            buttons = `
                <button class="complete-btn bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 transition duration-200" 
                        data-booking-id="${booking.id}">
                    <i class="fas fa-check-circle mr-1"></i>Complete
                </button>
                <button class="cancel-btn bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition duration-200" 
                        data-booking-id="${booking.id}">
                    <i class="fas fa-times mr-1"></i>Cancel
                </button>
            `;
            break;
        default:
            buttons = '<span class="text-gray-400 text-xs">No actions available</span>';
    }
    
    return buttons;
}

function bindBookingEvents() {
    $('.confirm-btn').click(function() {
        const bookingId = $(this).data('booking-id');
        updateBookingStatus(bookingId, 'confirmed');
    });
    
    $('.cancel-btn').click(function() {
        const bookingId = $(this).data('booking-id');
        if (confirm('Are you sure you want to cancel this booking?')) {
            updateBookingStatus(bookingId, 'cancelled');
        }
    });
    
    $('.complete-btn').click(function() {
        const bookingId = $(this).data('booking-id');
        updateBookingStatus(bookingId, 'completed');
    });
}

function updateBookingStatus(bookingId, newStatus) {
    $.ajax({
        url: 'api/update_booking_status.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            booking_id: bookingId,
            status: newStatus
        }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast(`Booking ${newStatus} successfully!`, 'success');
                loadBookings(); // Refresh the bookings
            } else {
                showToast('Error: ' + response.message, 'error');
            }
        },
        error: function() {
            showToast('Failed to update booking status. Please try again.', 'error');
        }
    });
}

function updateStats(stats) {
    $('#totalBookings').text(stats.total || 0);
    $('#pendingBookings').text(stats.pending || 0);
    $('#confirmedBookings').text(stats.confirmed || 0);
    $('#cancelledBookings').text(stats.cancelled || 0);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        $.ajax({
            url: 'api/admin_logout.php',
            method: 'POST',
            success: function() {
                window.location.href = 'admin_login.php';
            },
            error: function() {
                // Even if logout fails, redirect to login
                window.location.href = 'admin_login.php';
            }
        });
    }
}

function showToast(message, type = 'success') {
    const toast = $('#toast');
    const toastMessage = $('#toastMessage');
    
    toastMessage.text(message);
    
    if (type === 'error') {
        toast.find('div').removeClass('bg-green-500').addClass('bg-red-500');
    } else {
        toast.find('div').removeClass('bg-red-500').addClass('bg-green-500');
    }
    
    toast.removeClass('translate-x-full');
    
    setTimeout(() => {
        toast.addClass('translate-x-full');
    }, 3000);
}
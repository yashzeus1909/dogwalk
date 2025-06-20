// Walker Dashboard JavaScript
let currentWalker = null;
let currentBookings = [];

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Check if walker is already logged in (using localStorage)
    const savedWalker = localStorage.getItem('walker_session');
    if (savedWalker) {
        try {
            currentWalker = JSON.parse(savedWalker);
            showDashboard();
            loadBookings();
        } catch (error) {
            console.error('Error parsing saved walker session:', error);
            localStorage.removeItem('walker_session');
        }
    }

    // Setup login form
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
});

// Handle walker login
async function handleLogin(event) {
    event.preventDefault();
    
    const email = document.getElementById('email').value.trim();
    
    if (!email) {
        showMessage('Please enter your email address', 'error');
        return;
    }

    showLoading(true);

    try {
        const formData = new FormData();
        formData.append('email', email);

        const response = await fetch('api/walker_auth.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            currentWalker = result.walker;
            localStorage.setItem('walker_session', JSON.stringify(currentWalker));
            showDashboard();
            loadBookings();
            showMessage('Login successful!', 'success');
        } else {
            showMessage(result.message || 'Login failed', 'error');
        }
    } catch (error) {
        console.error('Login error:', error);
        showMessage('Connection error. Please try again.', 'error');
    } finally {
        showLoading(false);
    }
}

// Show dashboard and hide login
function showDashboard() {
    document.getElementById('login-section').style.display = 'none';
    document.getElementById('dashboard-section').style.display = 'block';
    
    // Update walker info in header and profile
    document.getElementById('walker-name').textContent = currentWalker.name;
    document.getElementById('walker-title').textContent = currentWalker.name;
    document.getElementById('walker-image').src = currentWalker.image || 'https://via.placeholder.com/64';
    document.getElementById('walker-rating').textContent = `★ ${currentWalker.rating} (${currentWalker.review_count} reviews)`;
    document.getElementById('walker-price').textContent = `$${currentWalker.price}/hour`;
}

// Load walker's bookings
async function loadBookings() {
    if (!currentWalker) return;

    showLoading(true);

    try {
        const response = await fetch(`api/walker_bookings.php?walker_id=${currentWalker.id}`);
        const result = await response.json();

        if (result.success) {
            currentBookings = result.bookings;
            updateBookingStats();
            displayBookings();
            document.getElementById('total-bookings').textContent = `${result.total_bookings} total bookings`;
        } else {
            showMessage(result.message || 'Failed to load bookings', 'error');
        }
    } catch (error) {
        console.error('Error loading bookings:', error);
        showMessage('Failed to load bookings', 'error');
    } finally {
        showLoading(false);
    }
}

// Update booking statistics
function updateBookingStats() {
    const stats = {
        pending: 0,
        confirmed: 0,
        completed: 0,
        total_earnings: 0
    };

    currentBookings.forEach(booking => {
        switch (booking.status) {
            case 'pending':
                stats.pending++;
                break;
            case 'confirmed':
            case 'in_progress':
                stats.confirmed++;
                break;
            case 'completed':
                stats.completed++;
                stats.total_earnings += parseFloat(booking.total_price) || 0;
                break;
        }
    });

    document.getElementById('pending-count').textContent = stats.pending;
    document.getElementById('confirmed-count').textContent = stats.confirmed;
    document.getElementById('completed-count').textContent = stats.completed;
    document.getElementById('total-earnings').textContent = `$${stats.total_earnings.toFixed(2)}`;
}

// Display bookings list
function displayBookings() {
    const container = document.getElementById('bookings-container');
    
    if (currentBookings.length === 0) {
        container.innerHTML = `
            <div class="p-6 text-center text-gray-500">
                <p>No bookings found.</p>
            </div>
        `;
        return;
    }

    // Sort bookings by date (newest first)
    const sortedBookings = [...currentBookings].sort((a, b) => new Date(b.booking_date) - new Date(a.booking_date));

    container.innerHTML = sortedBookings.map(booking => createBookingCard(booking)).join('');
}

// Create booking card HTML
function createBookingCard(booking) {
    const statusColors = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-blue-100 text-blue-800',
        in_progress: 'bg-purple-100 text-purple-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
    };

    const statusColor = statusColors[booking.status] || 'bg-gray-100 text-gray-800';
    const canDelete = ['pending', 'cancelled'].includes(booking.status);

    return `
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-3">
                        <h4 class="text-lg font-semibold text-gray-900">${booking.dog_name}</h4>
                        <span class="px-2 py-1 rounded-full text-xs font-medium ${statusColor}">
                            ${booking.status.replace('_', ' ').toUpperCase()}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <p><strong>Customer:</strong> ${booking.customer_name}</p>
                            <p><strong>Dog Size:</strong> ${booking.dog_size}</p>
                            <p><strong>Date & Time:</strong> ${booking.booking_date} at ${booking.booking_time}</p>
                            <p><strong>Duration:</strong> ${booking.duration} minutes</p>
                        </div>
                        <div>
                            <p><strong>Phone:</strong> ${booking.phone}</p>
                            <p><strong>Email:</strong> ${booking.email}</p>
                            <p><strong>Address:</strong> ${booking.address}</p>
                            <p><strong>Price:</strong> $${booking.total_price}</p>
                        </div>
                    </div>
                    
                    ${booking.special_notes ? `
                        <div class="mt-3 p-3 bg-gray-50 rounded-md">
                            <p class="text-sm text-gray-700"><strong>Special Notes:</strong> ${booking.special_notes}</p>
                        </div>
                    ` : ''}
                </div>
                
                <div class="ml-4 flex flex-col space-y-2">
                    ${booking.status !== 'completed' && booking.status !== 'cancelled' ? `
                        <select onchange="updateBookingStatus(${booking.id}, this.value)" 
                                class="text-sm border border-gray-300 rounded-md px-2 py-1">
                            <option value="">Update Status</option>
                            <option value="confirmed" ${booking.status === 'confirmed' ? 'selected' : ''}>Confirm</option>
                            <option value="in_progress" ${booking.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
                            <option value="completed" ${booking.status === 'completed' ? 'selected' : ''}>Complete</option>
                            <option value="cancelled" ${booking.status === 'cancelled' ? 'selected' : ''}>Cancel</option>
                        </select>
                    ` : ''}
                    
                    ${canDelete ? `
                        <button onclick="deleteBooking(${booking.id})" 
                                class="text-xs bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                            Delete
                        </button>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

// Update booking status
async function updateBookingStatus(bookingId, newStatus) {
    if (!newStatus) return;

    showLoading(true);

    try {
        const response = await fetch('api/walker_bookings.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                booking_id: bookingId,
                walker_id: currentWalker.id,
                status: newStatus
            })
        });

        const result = await response.json();

        if (result.success) {
            showMessage(`Booking status updated to ${newStatus}`, 'success');
            loadBookings(); // Reload bookings to update display
        } else {
            showMessage(result.message || 'Failed to update booking status', 'error');
        }
    } catch (error) {
        console.error('Error updating booking status:', error);
        showMessage('Failed to update booking status', 'error');
    } finally {
        showLoading(false);
    }
}

// Delete booking
async function deleteBooking(bookingId) {
    if (!confirm('Are you sure you want to delete this booking? This action cannot be undone.')) {
        return;
    }

    showLoading(true);

    try {
        const response = await fetch('api/walker_bookings.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                booking_id: bookingId,
                walker_id: currentWalker.id
            })
        });

        const result = await response.json();

        if (result.success) {
            showMessage('Booking deleted successfully', 'success');
            loadBookings(); // Reload bookings to update display
        } else {
            showMessage(result.message || 'Failed to delete booking', 'error');
        }
    } catch (error) {
        console.error('Error deleting booking:', error);
        showMessage('Failed to delete booking', 'error');
    } finally {
        showLoading(false);
    }
}

// Logout function
function logout() {
    localStorage.removeItem('walker_session');
    currentWalker = null;
    currentBookings = [];
    
    // Reset form
    document.getElementById('loginForm').reset();
    
    // Show login section and hide dashboard
    document.getElementById('login-section').style.display = 'block';
    document.getElementById('dashboard-section').style.display = 'none';
    
    showMessage('Logged out successfully', 'success');
}

// Show loading spinner
function showLoading(show) {
    const loading = document.getElementById('loading');
    if (show) {
        loading.classList.remove('hidden');
    } else {
        loading.classList.add('hidden');
    }
}

// Show message to user
function showMessage(message, type = 'info') {
    const messageContainer = document.getElementById('login-message');
    const colors = {
        success: 'bg-green-100 border-green-500 text-green-700',
        error: 'bg-red-100 border-red-500 text-red-700',
        info: 'bg-blue-100 border-blue-500 text-blue-700'
    };

    messageContainer.innerHTML = `
        <div class="border-l-4 p-4 ${colors[type]}">
            <p>${message}</p>
        </div>
    `;

    // Clear message after 5 seconds
    setTimeout(() => {
        messageContainer.innerHTML = '';
    }, 5000);
}
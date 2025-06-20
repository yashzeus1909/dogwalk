<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Walker - PawWalk</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .badge { 
            display: inline-flex; 
            align-items: center; 
            padding: 0.25rem 0.5rem; 
            border-radius: 0.375rem; 
            font-size: 0.75rem; 
            font-weight: 500; 
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-md mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="index.html" class="flex items-center space-x-2">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                    <span class="text-gray-600">Back</span>
                </a>
                <h1 class="text-lg font-semibold text-gray-900">Book a Walker</h1>
                <div></div> <!-- Empty div for spacing -->
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-md mx-auto px-4 py-6">
        <!-- Walker Info Card -->
        <div id="walkerCard" class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center space-x-4">
                <img id="walkerImage" src="" alt="" class="w-16 h-16 rounded-full object-cover">
                <div class="flex-1">
                    <h2 id="walkerName" class="text-lg font-semibold text-gray-900"></h2>
                    <div class="flex items-center space-x-2 mt-1">
                        <div class="flex items-center">
                            <i class="fas fa-star text-yellow-500 text-sm"></i>
                            <span id="walkerRating" class="text-sm text-gray-600 ml-1"></span>
                        </div>
                        <span class="text-gray-400">•</span>
                        <span id="walkerDistance" class="text-sm text-gray-600"></span>
                    </div>
                    <div class="text-lg font-semibold text-blue-600 mt-1">
                        $<span id="walkerPrice"></span>/hour
                    </div>
                </div>
            </div>
            
            <div id="walkerBadges" class="flex flex-wrap gap-2 mt-4">
                <!-- Badges will be populated dynamically -->
            </div>
            
            <p id="walkerDescription" class="text-gray-600 text-sm mt-4"></p>
        </div>

        <!-- Booking Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Details</h3>
            
            <form id="bookingForm">
                <div class="space-y-4">
                    <!-- Dog Information -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Dog Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dog Name*</label>
                                <input type="text" id="dogName" name="dogName" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Size*</label>
                                <select id="dogSize" name="dogSize" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select size</option>
                                    <option value="Small">Small (under 25 lbs)</option>
                                    <option value="Medium">Medium (25-60 lbs)</option>
                                    <option value="Large">Large (over 60 lbs)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Special Instructions</label>
                            <textarea id="specialInstructions" name="specialInstructions" rows="3" 
                                      placeholder="Any special needs or instructions for your dog..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                    </div>

                    <!-- Schedule -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Schedule</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date*</label>
                                <input type="date" id="bookingDate" name="date" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Time*</label>
                                <select id="bookingTime" name="time" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select time</option>
                                    <option value="06:00">6:00 AM</option>
                                    <option value="07:00">7:00 AM</option>
                                    <option value="08:00">8:00 AM</option>
                                    <option value="09:00">9:00 AM</option>
                                    <option value="10:00">10:00 AM</option>
                                    <option value="11:00">11:00 AM</option>
                                    <option value="12:00">12:00 PM</option>
                                    <option value="13:00">1:00 PM</option>
                                    <option value="14:00">2:00 PM</option>
                                    <option value="15:00">3:00 PM</option>
                                    <option value="16:00">4:00 PM</option>
                                    <option value="17:00">5:00 PM</option>
                                    <option value="18:00">6:00 PM</option>
                                    <option value="19:00">7:00 PM</option>
                                    <option value="20:00">8:00 PM</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duration*</label>
                            <select id="duration" name="duration" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select duration</option>
                                <option value="30">30 minutes</option>
                                <option value="60">1 hour</option>
                                <option value="90">1.5 hours</option>
                                <option value="120">2 hours</option>
                            </select>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Contact Information</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Your Name*</label>
                                <input type="text" id="ownerName" name="ownerName" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number*</label>
                                <input type="tel" id="phone" name="phone" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email*</label>
                                <input type="email" id="email" name="email" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address*</label>
                                <textarea id="address" name="address" rows="2" required 
                                          placeholder="Where should the walker pick up your dog?"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Summary -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Pricing Summary</h4>
                        <div class="flex justify-between text-sm">
                            <span>Duration:</span>
                            <span id="durationText">-</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span>Rate:</span>
                            <span id="rateText">-</span>
                        </div>
                        <div class="border-t border-gray-300 mt-2 pt-2">
                            <div class="flex justify-between font-semibold">
                                <span>Total:</span>
                                <span id="totalPrice">$0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Optional Account Creation Notice -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-yellow-800">Want to save your information?</h4>
                        <p class="text-sm text-yellow-700 mt-1">
                            <a href="customer_login.php" class="underline font-medium">Create an account</a> 
                            to save your details and track your bookings easily.
                        </p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit" id="submitBooking" 
                            class="w-full px-4 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Book Now
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Toast Notification -->
    <div id="toast" class="fixed top-4 right-4 z-50 translate-x-full transition-transform duration-300">
        <div class="bg-white rounded-lg shadow-lg border p-4 max-w-sm">
            <div class="flex items-center space-x-2">
                <i id="toastIcon" class="text-lg"></i>
                <span id="toastMessage" class="text-sm font-medium"></span>
            </div>
        </div>
    </div>

    <script>
        let walkerData = null;

        $(document).ready(function() {
            // Get walker ID from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const walkerId = urlParams.get('walker_id');

            if (!walkerId) {
                showToast('No walker selected', 'error');
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 2000);
                return;
            }

            // Load walker data
            loadWalkerData(walkerId);

            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            $('#bookingDate').attr('min', today);

            // Update pricing when duration changes
            $('#duration').change(updatePricingSummary);

            // Form submission
            $('#bookingForm').submit(function(e) {
                e.preventDefault();
                submitBooking();
            });
        });

        function loadWalkerData(walkerId) {
            $.ajax({
                url: '/api/walkers',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response && Array.isArray(response)) {
                        const walker = response.find(w => w.id == walkerId);
                        if (walker) {
                            walkerData = walker;
                            displayWalkerInfo(walker);
                        } else {
                            showToast('Walker not found', 'error');
                            setTimeout(() => {
                                window.location.href = 'index.html';
                            }, 2000);
                        }
                    } else {
                        showToast('Failed to load walker data', 'error');
                    }
                },
                error: function() {
                    showToast('Failed to load walker data', 'error');
                }
            });
        }

        function displayWalkerInfo(walker) {
            $('#walkerImage').attr('src', walker.image).attr('alt', walker.name);
            $('#walkerName').text(walker.name);
            $('#walkerRating').text(walker.rating + ' (' + walker.review_count + ' reviews)');
            $('#walkerDistance').text(walker.distance);
            $('#walkerPrice').text(walker.price);
            $('#walkerDescription').text(walker.description);

            // Display badges
            const badgesContainer = $('#walkerBadges');
            badgesContainer.empty();
            if (walker.badges && walker.badges.length > 0) {
                walker.badges.forEach(badge => {
                    const badgeClass = getBadgeClass(badge);
                    badgesContainer.append(`<span class="badge ${badgeClass}">${badge}</span>`);
                });
            }
        }

        function getBadgeClass(badge) {
            switch (badge.toLowerCase()) {
                case 'background checked':
                    return 'bg-green-100 text-green-800';
                case 'insured':
                    return 'bg-blue-100 text-blue-800';
                case 'certified':
                    return 'bg-purple-100 text-purple-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        }

        function updatePricingSummary() {
            const duration = parseInt($('#duration').val());
            if (!duration || !walkerData) {
                $('#durationText').text('-');
                $('#rateText').text('-');
                $('#totalPrice').text('$0.00');
                return;
            }

            const hours = duration / 60;
            const total = hours * walkerData.price;

            $('#durationText').text(duration + ' minutes');
            $('#rateText').text('$' + walkerData.price + '/hour');
            $('#totalPrice').text('$' + total.toFixed(2));
        }

        function submitBooking() {
            if (!walkerData) {
                showToast('Walker data not loaded', 'error');
                return;
            }

            // Collect form data
            const formData = {
                walkerId: walkerData.id,
                dogName: $('#dogName').val(),
                dogSize: $('#dogSize').val(),
                specialInstructions: $('#specialInstructions').val(),
                date: $('#bookingDate').val(),
                time: $('#bookingTime').val(),
                duration: parseInt($('#duration').val()),
                ownerName: $('#ownerName').val(),
                phone: $('#phone').val(),
                email: $('#email').val(),
                address: $('#address').val(),
                totalPrice: parseFloat($('#totalPrice').text().replace('$', ''))
            };

            // Validate required fields
            const requiredFields = ['dogName', 'dogSize', 'date', 'time', 'duration', 'ownerName', 'phone', 'email', 'address'];
            for (let field of requiredFields) {
                if (!formData[field]) {
                    showToast('Please fill in all required fields', 'error');
                    return;
                }
            }

            // Submit booking
            $('#submitBooking').prop('disabled', true).text('Booking...');

            $.ajax({
                url: '/api/bookings',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                dataType: 'json',
                success: function(response) {
                    if (response.success || response.id) {
                        showToast('Booking confirmed! Redirecting...', 'success');
                        setTimeout(() => {
                            window.location.href = 'index.html?booking_success=1';
                        }, 2000);
                    } else {
                        showToast('Booking failed: ' + (response.message || 'Unknown error'), 'error');
                        $('#submitBooking').prop('disabled', false).text('Book Now');
                    }
                },
                error: function() {
                    showToast('Booking failed. Please try again.', 'error');
                    $('#submitBooking').prop('disabled', false).text('Book Now');
                }
            });
        }

        function showToast(message, type = 'success') {
            const toast = $('#toast');
            const icon = $('#toastIcon');
            const messageEl = $('#toastMessage');

            // Set icon and styling based on type
            if (type === 'success') {
                icon.removeClass().addClass('fas fa-check-circle text-green-500');
                toast.find('> div').removeClass().addClass('bg-white rounded-lg shadow-lg border-l-4 border-green-500 p-4 max-w-sm');
            } else if (type === 'error') {
                icon.removeClass().addClass('fas fa-exclamation-circle text-red-500');
                toast.find('> div').removeClass().addClass('bg-white rounded-lg shadow-lg border-l-4 border-red-500 p-4 max-w-sm');
            }

            messageEl.text(message);

            // Show toast
            toast.removeClass('translate-x-full');

            // Hide after 3 seconds
            setTimeout(() => {
                toast.addClass('translate-x-full');
            }, 3000);
        }
    </script>
</body>
</html>
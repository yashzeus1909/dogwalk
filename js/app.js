$(document).ready(function() {
    // Sample data - in a real app, this would come from a database
    const walkers = [
        {
            id: 1,
            name: "Sarah M.",
            image: "https://images.unsplash.com/photo-1494790108755-2616b612b47c?w=150&h=150&fit=crop&crop=face",
            rating: 49,
            reviewCount: 127,
            distance: "0.5 miles away",
            price: 25,
            description: "Experienced dog walker with 5+ years caring for pets of all sizes. I love long walks and ensuring your furry friend gets the exercise they need!",
            availability: "Available today",
            badges: ["Background Check", "Insured", "5-Star Rated"],
            backgroundCheck: true,
            insured: true,
            certified: true
        },
        {
            id: 2,
            name: "Mike T.",
            image: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face",
            rating: 48,
            reviewCount: 89,
            distance: "0.8 miles away",
            price: 22,
            description: "Professional pet care specialist who treats every dog like family. Available for walks, feeding, and basic training.",
            availability: "Available tomorrow",
            badges: ["Certified", "Experienced"],
            backgroundCheck: true,
            insured: false,
            certified: true
        },
        {
            id: 3,
            name: "Emma K.",
            image: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face",
            rating: 50,
            reviewCount: 203,
            distance: "1.2 miles away",
            price: 30,
            description: "Veterinary student with a passion for animal care. Specializing in senior dogs and those with special needs.",
            availability: "Available this week",
            badges: ["Vet Student", "Special Needs", "Top Rated"],
            backgroundCheck: true,
            insured: true,
            certified: true
        }
    ];

    const bookings = [
        {
            id: 1,
            walkerId: 1,
            dogName: "Rocky",
            dogSize: "Medium",
            date: "2024-01-15",
            time: "2:00 PM",
            duration: 60,
            phone: "555-0123",
            email: "john@example.com",
            status: "confirmed",
            instructions: "Rocky loves to play fetch in the park!"
        }
    ];

    let currentWalker = null;

    // Initialize the app
    loadWalkers();
    loadBookings();
    loadProfileData();

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

    // Booking modal handling
    $('#closeModal, #cancelBooking').click(function() {
        closeBookingModal();
    });

    // Booking form submission
    $('#bookingForm').submit(function(e) {
        e.preventDefault();
        submitBooking();
    });

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
        const grid = $('#walkersGrid');
        grid.empty();

        walkers.forEach(walker => {
            const walkerCard = createWalkerCard(walker);
            grid.append(walkerCard);
        });
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
                        <button class="book-walker-btn bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200" 
                                data-walker-id="${walker.id}">
                            Book Now
                        </button>
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
        
        // Simulate API call
        setTimeout(() => {
            $('#loadingState').removeClass('show');
            
            // In a real app, filter walkers based on search criteria
            let filteredWalkers = walkers;
            
            if (location) {
                // Filter by location (simplified)
                filteredWalkers = walkers.filter(walker => 
                    walker.distance.toLowerCase().includes(location.toLowerCase())
                );
            }
            
            const grid = $('#walkersGrid');
            grid.empty();
            
            if (filteredWalkers.length === 0) {
                $('#emptyState').removeClass('hidden');
            } else {
                $('#emptyState').addClass('hidden');
                filteredWalkers.forEach(walker => {
                    const walkerCard = createWalkerCard(walker);
                    grid.append(walkerCard);
                });
            }
            
            // Re-bind click events
            bindWalkerEvents();
        }, 1000);
    }

    function bindWalkerEvents() {
        $('.book-walker-btn').click(function() {
            const walkerId = parseInt($(this).data('walker-id'));
            currentWalker = walkers.find(w => w.id === walkerId);
            showBookingModal();
        });
    }

    function showBookingModal() {
        if (!currentWalker) return;
        
        // Populate walker info
        $('#walkerInfo').html(`
            <div class="flex items-center space-x-3">
                <img src="${currentWalker.image}" alt="${currentWalker.name}" class="w-12 h-12 rounded-full object-cover">
                <div>
                    <h4 class="font-semibold">${currentWalker.name}</h4>
                    <p class="text-sm text-gray-600">$${currentWalker.price}/hour</p>
                </div>
            </div>
        `);

        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        $('#bookingDate').attr('min', today);

        // Update pricing summary
        updatePricingSummary();

        $('#bookingModal').addClass('show');
    }

    function closeBookingModal() {
        $('#bookingModal').removeClass('show');
        $('#bookingForm')[0].reset();
        currentWalker = null;
    }

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

        const bookingData = {
            walkerId: currentWalker.id,
            dogName: $('#dogName').val(),
            dogSize: $('#dogSize').val(),
            date: $('#bookingDate').val(),
            time: $('#bookingTime').val(),
            duration: parseInt($('#duration').val()),
            phone: $('#phone').val(),
            email: $('#bookingEmail').val(),
            instructions: $('#instructions').val(),
            status: 'pending'
        };

        // Validate required fields
        if (!bookingData.dogName || !bookingData.dogSize || !bookingData.date || 
            !bookingData.time || !bookingData.phone || !bookingData.email) {
            showToast('Please fill in all required fields', 'error');
            return;
        }

        // Add to bookings array (in a real app, this would be sent to server)
        const newBooking = {
            id: bookings.length + 1,
            ...bookingData
        };
        bookings.push(newBooking);

        closeBookingModal();
        showToast('Booking request submitted successfully!', 'success');
        
        // Refresh bookings if on bookings page
        if ($('#bookingsPage').hasClass('active')) {
            loadBookings();
        }
    }

    function loadBookings() {
        const container = $('#bookingsList');
        container.empty();

        if (bookings.length === 0) {
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

        bookings.forEach(booking => {
            const walker = walkers.find(w => w.id === booking.walkerId);
            const bookingCard = createBookingCard(booking, walker);
            container.append(bookingCard);
        });
    }

    function createBookingCard(booking, walker) {
        const statusColors = {
            pending: 'bg-yellow-100 text-yellow-800',
            confirmed: 'bg-green-100 text-green-800',
            completed: 'bg-blue-100 text-blue-800',
            cancelled: 'bg-red-100 text-red-800'
        };

        return `
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        ${walker ? `<img src="${walker.image}" alt="${walker.name}" class="w-10 h-10 rounded-full object-cover">` : ''}
                        <div>
                            <h4 class="font-semibold">${walker ? walker.name : 'Unknown Walker'}</h4>
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
                
                ${booking.instructions ? `
                    <div class="mt-3 p-3 bg-white rounded-lg">
                        <p class="text-sm"><strong>Instructions:</strong> ${booking.instructions}</p>
                    </div>
                ` : ''}
            </div>
        `;
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
        container.empty();

        if (bookings.length === 0) {
            container.html(`
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-heart text-4xl mb-4"></i>
                    <p>No bookings yet</p>
                    <p class="text-sm">Book your first dog walker to get started!</p>
                </div>
            `);
            return;
        }

        bookings.forEach(booking => {
            const walker = walkers.find(w => w.id === booking.walkerId);
            const bookingCard = createBookingCard(booking, walker);
            container.append(bookingCard);
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

    // Event bindings
    $(document).on('change', '#duration', updatePricingSummary);
    
    // Initial event binding
    bindWalkerEvents();

    // Set initial date input minimum
    const today = new Date().toISOString().split('T')[0];
    $('#bookingDate').attr('min', today);
});
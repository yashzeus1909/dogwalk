<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walker Dashboard | PawWalk</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <img src="attached_assets/gold bone _1749526479486.jpeg" alt="PawWalk Logo" class="w-8 h-8 rounded-lg object-contain">
                    <h1 class="text-xl font-bold text-gray-900">Walker Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span id="walker-name" class="text-gray-600"></span>
                    <button onclick="logout()" class="text-sm text-red-600 hover:text-red-800 font-medium">
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Login Section (Initially visible) -->
    <div id="login-section" class="max-w-md mx-auto px-4 py-12">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Walker Login</h2>
            
            <div id="login-message" class="mb-4"></div>

            <form id="loginForm" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="walker@example.com">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Login
                </button>
            </form>
        </div>
    </div>

    <!-- Dashboard Section (Initially hidden) -->
    <div id="dashboard-section" class="max-w-6xl mx-auto px-4 py-6" style="display: none;">
        <!-- Walker Profile Summary -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center space-x-4">
                <img id="walker-image" src="" alt="Walker Image" class="w-16 h-16 rounded-full object-cover">
                <div>
                    <h2 id="walker-title" class="text-xl font-bold text-gray-900"></h2>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span id="walker-rating"></span>
                        <span id="walker-price"></span>
                        <span id="total-bookings"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="text-2xl font-bold text-blue-600" id="pending-count">0</div>
                <div class="text-sm text-gray-600">Pending</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="text-2xl font-bold text-green-600" id="confirmed-count">0</div>
                <div class="text-sm text-gray-600">Confirmed</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="text-2xl font-bold text-purple-600" id="completed-count">0</div>
                <div class="text-sm text-gray-600">Completed</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="text-2xl font-bold text-gray-600" id="total-earnings">$0</div>
                <div class="text-sm text-gray-600">Total Earnings</div>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">My Bookings</h3>
            </div>
            
            <div id="bookings-container" class="divide-y divide-gray-200">
                <!-- Bookings will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loading" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-2 text-gray-600">Loading...</p>
        </div>
    </div>

    <script src="js/walker_dashboard.js"></script>
</body>
</html>
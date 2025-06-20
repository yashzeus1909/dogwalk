<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walker Admin Login - PawWalk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                    <i class="fas fa-dog text-blue-600 text-xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Walker Admin Panel
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Sign in to manage your bookings
                </p>
            </div>
            
            <form class="mt-8 space-y-6" id="loginForm">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                               class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                               placeholder="Email address">
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                               class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                               placeholder="Password">
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-lock text-blue-500 group-hover:text-blue-400"></i>
                        </span>
                        Sign in
                    </button>
                </div>

                <div class="text-center">
                    <a href="addWalker.php" class="font-medium text-blue-600 hover:text-blue-500">
                        Don't have an account? Register as a walker
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast notification -->
    <div id="toast" class="fixed top-4 right-4 transform translate-x-full transition-transform duration-300 ease-in-out z-50">
        <div class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            <span id="toastMessage"></span>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#loginForm').submit(function(e) {
                e.preventDefault();
                
                const email = $('#email').val();
                const password = $('#password').val();
                
                $.ajax({
                    url: 'api/admin_login.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        email: email,
                        password: password
                    }),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            window.location.href = 'admin_dashboard.php';
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function() {
                        showToast('Login failed. Please try again.', 'error');
                    }
                });
            });
        });

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
    </script>
</body>
</html>
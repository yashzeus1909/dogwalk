<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Walker | PawWalk</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-md mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <img src="attached_assets/gold bone _1749526479486.jpeg" alt="PawWalk Logo" class="w-8 h-8 rounded-lg object-contain">
    <h1 class="text-xl font-bold text-gray-900">Add Walker</h1>
                </div>
                <button onclick="window.location.href='index.html'" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    ← Back to App
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-2xl mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Add New Dog Walker</h2>
            
            <!-- Success/Error Messages -->
            <div id="message-container" class="mb-4"></div>

            <form id="walkerForm" action="api/add_walker.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                        <input type="text" id="name" name="name" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Walker's full name">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" id="email" name="email" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="walker@example.com">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                        <input type="password" id="password" name="password" required minlength="6"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter password (min. 6 characters)">
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Confirm your password">
                    </div>
                    
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price per Hour ($) *</label>
                        <input type="number" id="price" name="price" required min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="25">
                    </div>
                    
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Profile Image *</label>
                        <input type="file" id="image" name="image" accept="image/*" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               onchange="previewImage(this)">
                        <p class="text-xs text-gray-500 mt-1">Upload JPG, PNG or GIF (max 5MB) - Required</p>
                        <div id="imagePreview" class="mt-2 hidden">
                            <img id="previewImg" src="" alt="Preview" class="w-24 h-24 object-cover rounded-lg border">
                        </div>
                    </div>
                    
                    <div>
                        <label for="distance" class="block text-sm font-medium text-gray-700 mb-2">Distance</label>
                        <input type="text" id="distance" name="distance"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="1.2 miles">
                    </div>
                    
                    <div>
                        <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">Rating (0-5)</label>
                        <input type="number" id="rating" name="rating" min="0" max="5" step="0.1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="4.8">
                    </div>
                    
                    <div>
                        <label for="review_count" class="block text-sm font-medium text-gray-700 mb-2">Review Count</label>
                        <input type="number" id="review_count" name="review_count" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="127">
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Describe the walker's experience and specialties..."></textarea>
                </div>

                <!-- Availability -->
                <div>
                    <label for="availability" class="block text-sm font-medium text-gray-700 mb-2">Availability</label>
                    <input type="text" id="availability" name="availability"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Mon-Fri 9am-5pm">
                </div>

                <!-- Service Badges -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Service Badges</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="badges[]" value="Verified" class="rounded">
                            <span class="text-sm">Verified</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="badges[]" value="Insured" class="rounded">
                            <span class="text-sm">Insured</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="badges[]" value="Background Checked" class="rounded">
                            <span class="text-sm">Background Checked</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="badges[]" value="Dog Walking" class="rounded">
                            <span class="text-sm">Dog Walking</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="badges[]" value="Pet Sitting" class="rounded">
                            <span class="text-sm">Pet Sitting</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="badges[]" value="Pet Boarding" class="rounded">
                            <span class="text-sm">Pet Boarding</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="badges[]" value="Doggy Daycare" class="rounded">
                            <span class="text-sm">Doggy Daycare</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="badges[]" value="Grooming" class="rounded">
                            <span class="text-sm">Grooming</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="badges[]" value="Training" class="rounded">
                            <span class="text-sm">Training</span>
                        </label>
                    </div>
                </div>

                <!-- Certifications -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="background_check" value="1" class="rounded">
                        <span class="text-sm font-medium">Background Check</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="insured" value="1" class="rounded">
                        <span class="text-sm font-medium">Insured</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="certified" value="1" class="rounded">
                        <span class="text-sm font-medium">Certified</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                    Add Walker
                </button>
            </form>
        </div>
    </main>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }

        document.getElementById('walkerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Check if passwords match
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                document.getElementById('message-container').innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <strong>Error!</strong> Passwords do not match.
                    </div>
                `;
                document.getElementById('message-container').scrollIntoView({ behavior: 'smooth' });
                return;
            }
            
            // Check if image is selected
            const imageFile = document.getElementById('image').files[0];
            if (!imageFile) {
                document.getElementById('message-container').innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <strong>Error!</strong> Profile image is required.
                    </div>
                `;
                document.getElementById('message-container').scrollIntoView({ behavior: 'smooth' });
                return;
            }
            
            const formData = new FormData(this);
            
            fetch('api/add_walker.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageContainer = document.getElementById('message-container');
                
                if (data.success) {
                    messageContainer.innerHTML = `
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            <strong>Success!</strong> Walker "${data.walker_name}" has been added successfully.
                        </div>
                    `;
                    document.getElementById('walkerForm').reset();
                } else {
                    messageContainer.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <strong>Error!</strong> ${data.message}
                        </div>
                    `;
                }
                
                // Scroll to message
                messageContainer.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('message-container').innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <strong>Error!</strong> Failed to add walker. Please try again.
                    </div>
                `;
            });
        });
    </script>
</body>
</html>
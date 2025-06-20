<?php
include_once 'config/database.php';
include_once 'models/Walker.php';

// Get walker ID from URL
$walker_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$walker_id) {
    header('Location: index.html');
    exit;
}

// Create database connection
$database = new Database();
$db = $database->getConnection();
$walker = new Walker($db);

// Get walker data
$walker->id = $walker_id;
if (!$walker->readOne()) {
    header('Location: index.html');
    exit;
}

// Convert rating from integer to decimal for display
$display_rating = $walker->rating / 10;

// Handle badges - check if it's already an array or needs JSON decoding
if (is_array($walker->badges)) {
    $badges_array = $walker->badges;
} else {
    $badges_array = json_decode($walker->badges, true) ?: [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Walker - <?php echo htmlspecialchars($walker->name); ?> | PawWalk</title>
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
                    <h1 class="text-xl font-bold text-gray-900">Edit Walker</h1>
                </div>
                <div class="flex gap-2">
                    <button onclick="deleteWalker()" class="text-sm text-red-600 hover:text-red-800 font-medium">
                        Delete
                    </button>
                    <button onclick="window.location.href='index.html'" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        ← Back
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-2xl mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <!-- Success/Error Messages -->
            <div id="message-container" class="mb-4"></div>

            <form id="walkerForm" class="space-y-6">
                <input type="hidden" id="walker_id" value="<?php echo $walker->id; ?>">
                
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo htmlspecialchars($walker->name); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($walker->email ?? ''); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="walker@example.com">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" minlength="6"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Leave blank to keep current password">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password</p>
                    </div>
                    
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price per Hour ($) *</label>
                        <input type="number" id="price" name="price" required min="1"
                               value="<?php echo $walker->price; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Profile Image URL</label>
                        <input type="url" id="image" name="image"
                               value="<?php echo htmlspecialchars($walker->image); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="distance" class="block text-sm font-medium text-gray-700 mb-2">Distance</label>
                        <input type="text" id="distance" name="distance"
                               value="<?php echo htmlspecialchars($walker->distance); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">Rating (0-5)</label>
                        <input type="number" id="rating" name="rating" min="0" max="5" step="0.1"
                               value="<?php echo $display_rating; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="review_count" class="block text-sm font-medium text-gray-700 mb-2">Review Count</label>
                        <input type="number" id="review_count" name="review_count" min="0"
                               value="<?php echo $walker->review_count; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($walker->description); ?></textarea>
                </div>

                <!-- Availability -->
                <div>
                    <label for="availability" class="block text-sm font-medium text-gray-700 mb-2">Availability</label>
                    <input type="text" id="availability" name="availability"
                           value="<?php echo htmlspecialchars($walker->availability); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Service Badges -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Service Badges</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <?php
                        $available_badges = [
                            "Verified", "Insured", "Background Checked", 
                            "Dog Walking", "Pet Sitting", "Pet Boarding", 
                            "Doggy Daycare", "Grooming", "Training"
                        ];
                        
                        foreach ($available_badges as $badge): 
                            $is_checked = in_array($badge, $badges_array) ? 'checked' : '';
                        ?>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="badges[]" value="<?php echo $badge; ?>" class="rounded" <?php echo $is_checked; ?>>
                            <span class="text-sm"><?php echo $badge; ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Certifications -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="background_check" value="1" class="rounded" 
                               <?php echo $walker->background_check ? 'checked' : ''; ?>>
                        <span class="text-sm font-medium">Background Check</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="insured" value="1" class="rounded"
                               <?php echo $walker->insured ? 'checked' : ''; ?>>
                        <span class="text-sm font-medium">Insured</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="certified" value="1" class="rounded"
                               <?php echo $walker->certified ? 'checked' : ''; ?>>
                        <span class="text-sm font-medium">Certified</span>
                    </label>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-4">
                    <button type="submit" 
                            class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                        Update Walker
                    </button>
                    <button type="button" onclick="window.location.href='index.html'"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function updateWalker() {
            const formData = new FormData(document.getElementById('walkerForm'));
            
            fetch('api/update_walker.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageContainer = document.getElementById('message-container');
                
                if (data.success) {
                    messageContainer.innerHTML = `
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            <strong>Success!</strong> Walker updated successfully.
                        </div>
                    `;
                } else {
                    messageContainer.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <strong>Error!</strong> ${data.message}
                        </div>
                    `;
                }
                
                messageContainer.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('message-container').innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <strong>Error!</strong> Failed to update walker. Please try again.
                    </div>
                `;
            });
        }
        
        function deleteWalker() {
            if (!confirm('Are you sure you want to delete this walker? This action cannot be undone.')) {
                return;
            }
            
            const walkerId = document.getElementById('walker_id').value;
            
            fetch('api/delete_walker.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: walkerId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Walker deleted successfully!');
                    window.location.href = 'index.html';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete walker. Please try again.');
            });
        }

        document.getElementById('walkerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            updateWalker();
        });
    </script>
</body>
</html>
<?php
session_start();
require_once 'api/config.php';

// Check if walker is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'walker') {
    header('Location: login.html');
    exit;
}

try {
    // Get database connection
    $db = getDatabaseConnection();
} catch (Exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
    header('Location: login.html');
    exit;
}

// Get walker info
$walker_id = $_SESSION['user_id'];
try {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND role = 'walker'");
    $stmt->execute([$walker_id]);
    $walker = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$walker) {
        session_destroy();
        header('Location: login.html');
        exit;
    }
} catch (Exception $e) {
    error_log("Error fetching walker info: " . $e->getMessage());
    header('Location: login.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walker Dashboard | PawWalk</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation with user dropdown -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <i class="fas fa-paw"></i>
                <span>PawWalk</span>
            </div>
            <div class="nav-menu">
                <div class="user-dropdown">
                    <button class="user-dropdown-toggle" id="userDropdownBtn">
                        <i class="fas fa-user-circle"></i>
                        <span id="userName"><?php echo htmlspecialchars($walker['first_name'] . ' ' . $walker['last_name']); ?></span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </button>
                    <div class="user-dropdown-menu" id="userDropdownMenu">
                        <a href="#" class="dropdown-item" onclick="viewSchedule()">
                            <i class="fas fa-calendar-check"></i>
                            <span>Schedule</span>
                        </a>
                        <a href="#" class="dropdown-item" onclick="editProfile()">
                            <i class="fas fa-user-circle"></i>
                            <span>Profile</span>
                        </a>
                        <a href="#" class="dropdown-item" onclick="viewBookings()">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Bookings</span>
                        </a>
                        <a href="#" class="dropdown-item" onclick="logout()">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Centered Walker Dashboard Display -->
    <div class="form-container" style="margin-top: 120px;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="background: #2c5282; color: white; padding: 0.5rem 1rem; border-radius: 8px; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                <i class="fas fa-walking"></i>
                <span style="font-weight: 600;">Walker Dashboard</span>
            </div>
        </div>

        <h2 style="text-align: center; margin-bottom: 2rem; color: #2d3748;">Walker Login</h2>

        <!-- Walker Info Display (styled like a form) -->
        <div class="form-group">
            <label for="walker_email">Email Address</label>
            <input type="email" id="walker_email" value="<?php echo htmlspecialchars($walker['email']); ?>" readonly
                   style="background-color: #f8f9fa; color: #495057; cursor: default;">
        </div>

        <div class="form-group">
            <label for="walker_name">Walker Name</label>
            <input type="text" id="walker_name" value="<?php echo htmlspecialchars($walker['first_name'] . ' ' . $walker['last_name']); ?>" readonly
                   style="background-color: #f8f9fa; color: #495057; cursor: default;">
        </div>

        <div class="form-group">
            <label for="walker_rate">Hourly Rate</label>
            <input type="text" id="walker_rate" value="$<?php echo number_format($walker['price'], 2); ?>/hour" readonly
                   style="background-color: #f8f9fa; color: #495057; cursor: default;">
        </div>

        <div class="form-group">
            <label for="walker_rating">Rating & Reviews</label>
            <input type="text" id="walker_rating" value="⭐ <?php echo number_format($walker['rating'], 1); ?> (<?php echo $walker['review_count']; ?> reviews)" readonly
                   style="background-color: #f8f9fa; color: #495057; cursor: default;">
        </div>

        <!-- Action Button (styled like the login button) -->
        <button class="form-button" onclick="viewSchedule()" style="background: #4285f4; margin-top: 1rem;">
            View My Schedule
        </button>

        <!-- Quick Stats -->
        <div style="margin-top: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
            <h3 style="margin: 0 0 1rem 0; color: #2d3748; font-size: 1rem; text-align: center;">Today's Overview</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; text-align: center;">
                <div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: #2c5282;" id="todayBookings">0</div>
                    <div style="font-size: 0.8rem; color: #6c757d;">Today's Walks</div>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: #28a745;" id="todayEarnings">$0</div>
                    <div style="font-size: 0.8rem; color: #6c757d;">Today's Earnings</div>
                </div>
            </div>
        </div>

        <!-- Additional Action Buttons -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
            <button class="form-button" onclick="viewBookings()" style="background: #28a745; padding: 0.5rem;">
                My Bookings
            </button>
            <button class="form-button" onclick="editProfile()" style="background: #6c757d; padding: 0.5rem;">
                Edit Profile
            </button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/auth.js"></script>
    <script>
        $(document).ready(function() {
            initializeUserDropdown();
            loadTodayStats();
        });

        function viewSchedule() {
            window.location.href = 'walker_schedule.php';
        }

        function viewBookings() {
            window.location.href = 'walker_bookings.php';
        }

        function editProfile() {
            window.location.href = 'walker_profile.php';
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                $.ajax({
                    url: 'api/logout.php',
                    method: 'POST',
                    success: function() {
                        window.location.href = 'login.html';
                    },
                    error: function() {
                        window.location.href = 'login.html';
                    }
                });
            }
        }

        function loadTodayStats() {
            $.ajax({
                url: 'api/walker_stats.php',
                method: 'GET',
                data: { date: 'today' },
                success: function(response) {
                    if (response.success) {
                        $('#todayBookings').text(response.bookings_count || 0);
                        $('#todayEarnings').text('$' + (response.earnings || 0).toFixed(2));
                    }
                },
                error: function() {
                    console.log('Could not load today\'s stats');
                }
            });
        }
    </script>
</body>
</html>
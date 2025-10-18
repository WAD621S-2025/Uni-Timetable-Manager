<?php
session_start();

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

// Database connection
$host = "localhost";
$dbname = "campus_connect";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed");
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];

// Load schedule
$stmt = $conn->prepare("SELECT * FROM schedule WHERE user_id = ? ORDER BY FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'), time");
$stmt->execute([$user_id]);
$schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate stats
$total_courses = count(array_unique(array_column($schedule, 'module_code')));
$weekly_hours = count($schedule);
$today = date('l');
$classes_today = count(array_filter($schedule, fn($s) => $s['day'] === $today));

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Campus Connect</title>
    <link rel="stylesheet" href="../login/styling.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="logo">Campus Connect</div>
            </div>
            <ul class="sidebar-menu">
                 <li><a href="dashboard.php" class="active"><span></span> Dashboard</a></li>
                 <li><a href="schedule-builder.php"><span></span> Schedule Builder</a></li>
                <li><a href="?logout=1" class="logout"><span></span> Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <header class="main-header">
                <div class="header-content">
                    <h1>My Timetable - Campus Connect</h1>
                    <div class="header-actions">
                        <div class="user-info">
                            <span>Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong>
                        </div>
                        <button class="btn btn-primary" onclick="location.href='schedule-builder.html'">
                            + Create New Schedule
                        </button>
                    </div>
                </div>
            </header>

            <div class="content-area">
                <!-- Quick Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <div class="stat-info">
                            <h3><?php echo $total_courses; ?></h3>
                            <p>Total Courses</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <div class="stat-info">
                            <h3><?php echo $weekly_hours; ?></h3>
                            <p>Weekly Hours</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <div class="stat-info">
                            <h3><?php echo $classes_today; ?></h3>
                            <p>Classes Today</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <div class="stat-info">
                            <h3 id="nextClass">10:00</h3>
                            <p>Next Class</p>
                        </div>
                    </div>
                </div>

                <!-- Timetable View -->
                <div class="timetable-section">
                    <div class="section-header">
                        <h2>Weekly Schedule</h2>
                        <div class="view-controls">
                            <button class="btn btn-outline active" data-view="week">Week View</button>
                            <button class="btn btn-outline" data-view="day">Day View</button>
                            <button class="btn btn-outline" onclick="printTimetable()">Print Schedule</button>
                            <button class="btn btn-primary" onclick="exportTimetable()">Export</button>
                        </div>
                    </div>
                    
                    <div class="timetable-container">
                        <div class="timetable" id="timetable">
                            <!-- Timetable will be generated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/dashboard.js"></script> 
</body> 
</html>
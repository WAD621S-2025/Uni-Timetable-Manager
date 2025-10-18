<?php
// dashboard/dashboard.php
session_start();

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

// Database connection
$db_host = "localhost";
$db_name = "campus_connect";
$db_user = "root";
$db_pass = "";

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $conn = null;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Load user's schedule from database
$schedule = [];
$stats = ['total_courses' => 0, 'weekly_hours' => 0, 'classes_today' => 0];

if ($conn) {
    $stmt = $conn->prepare("SELECT * FROM schedule WHERE user_id = :user_id ORDER BY FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'), time");
    $stmt->execute(['user_id' => $user_id]);
    $schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate stats
    $stats['total_courses'] = count(array_unique(array_column($schedule, 'module_code')));
    $stats['weekly_hours'] = count($schedule);
    
    // Classes today
    $today = date('l'); // Monday, Tuesday, etc.
    $stats['classes_today'] = count(array_filter($schedule, function($class) use ($today) {
        return $class['day'] === $today;
    }));
}

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
                <li><a href="dashboard.html" class="active"><span></span> Dashboard</a></li>
                <li><a href="schedule-builder.html"><span></span> Schedule Builder</a></li>
                <li><a href="../login/login.html" class="logout"><span></span> Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <header class="main-header">
                <div class="header-content">
                    <h1>My Timetable - Campus Connect</h1>
                    <div class="header-actions">
                        <div class="user-info">
                            <span>Welcome, <strong id="userName">Student</strong>!</span>
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
                            <h3 id="totalCourses">5</h3>
                            <p>Total Courses</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <div class="stat-info">
                            <h3 id="weeklyHours">18</h3>
                            <p>Weekly Hours</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <div class="stat-info">
                            <h3 id="completedClasses">3</h3>
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
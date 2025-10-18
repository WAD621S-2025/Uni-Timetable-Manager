<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

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
$message = "";

// Handle Add Module
if (isset($_POST['add_module'])) {
    $code = trim(strtoupper($_POST['moduleCode']));
    $name = trim($_POST['moduleName']);
    $type = $_POST['moduleType'];
    
    if (!empty($code) && !empty($name)) {
        // Check duplicate
        $stmt = $conn->prepare("SELECT id FROM modules WHERE user_id = ? AND code = ?");
        $stmt->execute([$user_id, $code]);
        
        if ($stmt->rowCount() > 0) {
            $message = "error:Module code already exists!";
        } else {
            $stmt = $conn->prepare("INSERT INTO modules (user_id, code, name, type, color) VALUES (?, ?, ?, ?, '#0074D9')");
            if ($stmt->execute([$user_id, $code, $name, $type])) {
                $message = "success:Module added!";
            }
        }
    }
}

// Handle Save Schedule (AJAX)
if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    $scheduleData = json_decode(file_get_contents('php://input'), true);
    
    if ($scheduleData && is_array($scheduleData)) {
        // Delete old schedule
        $stmt = $conn->prepare("DELETE FROM schedule WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // Insert new schedule
        $stmt = $conn->prepare("INSERT INTO schedule (user_id, module_code, module_name, module_type, day, time, color) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($scheduleData as $item) {
            $stmt->execute([
                $user_id,
                $item['code'],
                $item['name'],
                $item['type'],
                $item['day'],
                $item['time'],
                $item['color']
            ]);
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit();
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../login/login.php");
    exit();
}

// Load user modules
$stmt = $conn->prepare("SELECT * FROM modules WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load saved schedule
$stmt = $conn->prepare("SELECT * FROM schedule WHERE user_id = ?");
$stmt->execute([$user_id]);
$schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Builder - Campus Connect</title>
    <link rel="stylesheet" href="../login/styling.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/builder.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="logo">Campus Connect</div>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><span></span> Dashboard</a></li>
                <li><a href="schedule-builder.php" class="active"><span></span> Schedule Builder</a></li>
                <li><a href="?logout=1" class="logout"><span></span> Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <header class="main-header">
                <div class="header-content">
                    <h1>Schedule Builder - Campus Connect</h1>
                    <div class="header-actions">
                        <div class="user-info">
                            <span>Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong>!</span>
                        </div>
                        <button class="btn btn-primary" onclick="saveSchedule()">
                             Save Schedule
                        </button>
                    </div>
                </div>
            </header>

            <div class="content-area">
                <?php if ($message): 
                    list($type, $text) = explode(':', $message);
                ?>
                    <div style="margin: 1rem 2rem; padding: 1rem; border-radius: 4px; background: <?php echo $type === 'success' ? '#28a745' : '#dc3545'; ?>; color: white;">
                        <?php echo htmlspecialchars($text); ?>
                    </div>
                <?php endif; ?>

                <div class="builder-container">
                    <!-- Module Panel -->
                    <div class="module-panel">
                        <h3>Available Modules</h3>
                        
                        <div class="custom-module">
                            <h4>Add New Module</h4>
                            <form method="POST">
                                <div class="form-group">
                                    <input type="text" name="moduleCode" placeholder="Module Code (e.g., WAD621S)" required>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="moduleName" placeholder="Module Name" required>
                                </div>
                                <div class="form-group">
                                    <select name="moduleType">
                                        <option value="Lecture">Lecture</option>
                                        <option value="lab">Lab</option>
                                    </select>
                                </div>
                                <button type="submit" name="add_module" class="btn btn-primary">Add Module</button>
                            </form>
                        </div>

                        <div class="module-list-section">
                            <h4>Your Modules</h4>
                            <div class="module-list" id="moduleList">
                                <?php if (empty($modules)): ?>
                                    <div class="no-modules">No modules added yet. Add your first module above.</div>
                                <?php else: ?>
                                    <?php foreach ($modules as $mod): ?>
                                        <div class="module-item" draggable="true"
                                             data-module-code="<?php echo htmlspecialchars($mod['code']); ?>"
                                             data-module-name="<?php echo htmlspecialchars($mod['name']); ?>"
                                             data-module-type="<?php echo htmlspecialchars($mod['type']); ?>"
                                             data-module-color="<?php echo htmlspecialchars($mod['color']); ?>">
                                            <span class="module-code"><?php echo htmlspecialchars($mod['code']); ?></span>
                                            <span class="module-name"><?php echo htmlspecialchars($mod['name']); ?></span>
                                            <span class="module-type"><?php echo htmlspecialchars($mod['type']); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Timetable Builder -->
                    <div class="builder-main">
                        <div class="builder-tools">
                            <h3>Weekly Timetable Builder</h3>
                            <div class="toolbar">
                                <button class="btn btn-outline" onclick="clearSchedule()">🗑️ Clear All</button>
                                <div class="color-picker">
                                    <label>Module Color:</label>
                                    <input type="color" id="moduleColor" value="#0074D9">
                                </div>
                            </div>
                        </div>

                        <!-- Timetable -->
                        <div class="timetable-builder">
                            <div class="timetable-header-builder">
                                <div class="time-header">Time</div>
                                <div class="day-header">Monday</div>
                                <div class="day-header">Tuesday</div>
                                <div class="day-header">Wednesday</div>
                                <div class="day-header">Thursday</div>
                                <div class="day-header">Friday</div>
                            </div>
                            
                            <div class="timetable-body-builder" id="timetableBuilder">
                                <?php
                                $times = ['8:00', '9:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                                
                                foreach ($times as $time):
                                ?>
                                    <div class="time-slot-builder"><?php echo $time; ?></div>
                                    <?php foreach ($days as $day): 
                                        // Check if there's a saved class at this slot
                                        $class_here = null;
                                        foreach ($schedule as $s) {
                                            if ($s['day'] === $day && $s['time'] === $time) {
                                                $class_here = $s;
                                                break;
                                            }
                                        }
                                    ?>
                                        <div class="day-slot-builder" data-day="<?php echo $day; ?>" data-time="<?php echo $time; ?>">
                                            <?php if ($class_here): ?>
                                                <div class="scheduled-class" 
                                                     style="background-color: <?php echo $class_here['color']; ?>;"
                                                     data-module-code="<?php echo $class_here['module_code']; ?>"
                                                     data-module-name="<?php echo $class_here['module_name']; ?>"
                                                     data-module-type="<?php echo $class_here['module_type']; ?>"
                                                     data-color="<?php echo $class_here['color']; ?>">
                                                    <span class="module-code"><?php echo htmlspecialchars($class_here['module_code']); ?></span>
                                                    <span class="module-time"><?php echo $time; ?></span>
                                                    <span class="module-type"><?php echo htmlspecialchars($class_here['module_type']); ?></span>
                                                    <button class="remove-btn" onclick="this.parentElement.remove(); updateScheduleSummary();">×</button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Current Schedule Summary -->
                        <div class="schedule-summary">
                            <h4>Current Schedule Summary</h4>
                            <div class="summary-list" id="scheduleSummary">
                                <!-- Schedule items will be populated here -->
                            </div>
                            <div class="summary-stats">
                                <div class="stat-item">
                                    <span>Total Modules:</span>
                                    <strong id="totalModulesCount">0</strong>
                                </div>
                                <div class="stat-item">
                                    <span>Weekly Hours:</span>
                                    <strong id="weeklyHoursCount">0</strong>
                                </div>
                                <div class="stat-item">
                                    <span>Conflicts:</span>
                                    <strong id="conflictsCount">0</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Conflict Modal -->
    <div id="conflictModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Schedule Conflicts Detected</h3>
            <div id="conflictList"></div>
            <button class="btn btn-primary" onclick="closeConflictModal()">OK</button>
        </div>
    </div>
    <script src="js/builder.js"></script>
</body>
</html>
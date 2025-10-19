<?php
// test_connection.php
echo "<h2> Campus Connect - Backend Test</h2>";

// Database configuration
$host = "localhost";
$dbname = "campus_connect";
$username = "root";
$password = ""; // Your MySQL password (empty by default in XAMPP)

try {
    // Test database connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo " <strong>Database connection successful!</strong><br><br>";
    
    // Test if all required tables exist
    echo "<h3> Table Check:</h3>";
    $required_tables = ['users', 'modules', 'schedule', 'classes', 'schedules', 'schedule_entries'];
    
    foreach ($required_tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->rowCount() > 0) {
            echo " Table '$table' exists<br>";
        } else {
            echo " Table '$table' is MISSING<br>";
        }
    }
    
    echo "<br><h3> Testing Stored Procedures:</h3>";
    
    // Test 1: User Registration
    try {
        $test_username = "test_student_" . rand(1000, 9999);
        $test_email = $test_username . "@nust.na";
        
        $stmt = $conn->prepare("CALL sp_register_user(?, ?, ?)");
        $stmt->execute([$test_username, $test_email, 'testpassword123']);
        echo " User registration procedure works<br>";
        
        // Get the created user ID
        $user_stmt = $conn->query("SELECT id FROM users WHERE username = '$test_username'");
        $user = $user_stmt->fetch(PDO::FETCH_ASSOC);
        $user_id = $user['id'];
        
    } catch (PDOException $e) {
        echo " User registration failed: " . $e->getMessage() . "<br>";
    }
    
    // Test 2: Add Module
    try {
        if (isset($user_id)) {
            $stmt = $conn->prepare("CALL sp_add_module(?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, 'WAD621S', 'Web Application Development', 'Lecture', '#0074D9']);
            echo " Module addition procedure works<br>";
        }
    } catch (PDOException $e) {
        echo " Module addition failed: " . $e->getMessage() . "<br>";
    }
    
    // Test 3: Add Schedule Entry
    try {
        if (isset($user_id)) {
            $stmt = $conn->prepare("CALL sp_add_schedule_entry(?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, 'WAD621S', 'Web Application Development', 'Lecture', 'Monday', '10:00', '#0074D9']);
            echo " Schedule entry procedure works<br>";
        }
    } catch (PDOException $e) {
        echo " Schedule entry failed: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><h3> Database Content:</h3>";
    
    // Show current data
    $tables_to_show = ['users', 'modules', 'schedule'];
    foreach ($tables_to_show as $table) {
        echo "<strong>Table: $table</strong><br>";
        $result = $conn->query("SELECT COUNT(*) as count FROM $table");
        $count = $result->fetch(PDO::FETCH_ASSOC)['count'];
        echo "Records: $count<br><br>";
    }
    
    echo "<h3> Test Summary:</h3>";
    echo "If you see all green checkmarks above, your backend is working correctly!<br>";
    echo "You can now proceed to test the frontend pages (dashboard.php and schedule-builder.php).";
    
} catch(PDOException $e) {
    echo "<div style='color: red;'><strong> Database connection failed!</strong><br>";
    echo "Error: " . $e->getMessage() . "</div>";
    echo "<br><strong>Troubleshooting steps:</strong>";
    echo "<ol>";
    echo "<li>Make sure MySQL is running in XAMPP</li>";
    echo "<li>Check if the database 'campus_connect' exists</li>";
    echo "<li>Verify your MySQL username and password</li>";
    echo "<li>Run the SQL code above to create the database structure</li>";
    echo "</ol>";
}
?>
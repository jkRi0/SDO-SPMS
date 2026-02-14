<?php
require_once __DIR__ . '/config.php';

echo "<h2>Database Setup</h2>";
echo "<p>Initializing database tables...</p>";

try {
    $db = get_db();
    
    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/init_db.sql');
    
    // Split the SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) { return !empty($stmt); }
    );
    
    // Execute each statement
    foreach ($statements as $stmt) {
        try {
            $db->exec($stmt);
        } catch (Exception $e) {
            // Ignore errors about tables already existing
            if (strpos($e->getMessage(), 'already exists') === false) {
                echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
            }
        }
    }
    
    echo "<p style='color: green;'><strong>Success!</strong> Database tables have been initialized.</p>";
    echo "<p><a href='login.php'>Go to Login</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
}
?>

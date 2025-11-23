<?php
/**
 * Comprehensive database fix script for users table
 * Fixes missing id column and ensures proper table structure
 * Run this once by visiting: http://localhost/CURD_REVISEE/fix_id_column.php
 */

require __DIR__ . '/config.php';

$fixes = [];
$errors = [];
$warnings = [];

try {
    $conn = get_db_connection();
    
    // Check if users table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'users'");
    if ($tableCheck->num_rows === 0) {
        die("<h2 style='color: red;'>✗ Error</h2><p>The 'users' table does not exist. Please import company.sql first.</p>");
    }
    
    // Get current table structure
    $columns = [];
    $result = $conn->query("DESCRIBE users");
    while ($row = $result->fetch_assoc()) {
        $columns[$row['Field']] = $row;
    }
    
    // Check if id column exists
    $hasId = isset($columns['id']);
    $hasPrimaryKey = false;
    
    // Check for primary key
    $pkCheck = $conn->query("SHOW KEYS FROM users WHERE Key_name = 'PRIMARY'");
    $hasPrimaryKey = $pkCheck->num_rows > 0;
    
    if (!$hasId) {
        // Check if table has data
        $countResult = $conn->query("SELECT COUNT(*) as cnt FROM users");
        $countRow = $countResult->fetch_assoc();
        $hasData = $countRow['cnt'] > 0;
        
        if ($hasData && !$hasPrimaryKey) {
            // If table has data but no primary key, we need to be careful
            // First, add id column with temporary values
            $conn->query("ALTER TABLE `users` ADD COLUMN `id` INT(11) NULL FIRST");
            
            // Set temporary sequential IDs
            $updateResult = $conn->query("SET @row_number = 0");
            $conn->query("UPDATE `users` SET `id` = (@row_number:=@row_number + 1) WHERE `id` IS NULL");
            
            // Now make it NOT NULL, AUTO_INCREMENT, and PRIMARY KEY
            $conn->query("ALTER TABLE `users` MODIFY COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`)");
            
            $fixes[] = "Added id column with auto-increment and primary key (table had existing data)";
        } else {
            // No data or has primary key - safe to add directly
            if (!$hasPrimaryKey) {
                $conn->query("ALTER TABLE `users` ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`)");
                $fixes[] = "Added id column as primary key with auto increment";
            } else {
                // Has primary key but no id - add id without making it primary
                $conn->query("ALTER TABLE `users` ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT FIRST");
                $warnings[] = "Added id column but table already has a primary key. Consider reviewing table structure.";
            }
        }
    } else {
        // ID exists - check if it's properly configured
        $idCol = $columns['id'];
        $needsFix = false;
        
        if ($idCol['Null'] === 'YES') {
            $conn->query("ALTER TABLE `users` MODIFY COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT");
            $fixes[] = "Fixed id column: changed to NOT NULL";
            $needsFix = true;
        }
        
        if (strpos($idCol['Extra'], 'auto_increment') === false) {
            $conn->query("ALTER TABLE `users` MODIFY COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT");
            $fixes[] = "Fixed id column: added AUTO_INCREMENT";
            $needsFix = true;
        }
        
        if (!$hasPrimaryKey) {
            $conn->query("ALTER TABLE `users` ADD PRIMARY KEY (`id`)");
            $fixes[] = "Added primary key constraint to id column";
            $needsFix = true;
        }
        
        if (!$needsFix) {
            $fixes[] = "ID column already exists and is properly configured";
        }
    }
    
    // Verify other required columns exist
    $requiredColumns = ['name', 'email', 'password_hash'];
    foreach ($requiredColumns as $col) {
        if (!isset($columns[$col])) {
            $warnings[] = "Missing required column: $col";
        }
    }
    
    // Show results
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Database Fix</title>";
    echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;}";
    echo "h2{margin-top:20px;}table{border-collapse:collapse;width:100%;margin:20px 0;}";
    echo "th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background:#f2f2f2;}</style></head><body>";
    
    if (!empty($fixes)) {
        echo "<h2 style='color: green;'>✓ Fixes Applied:</h2><ul>";
        foreach ($fixes as $fix) {
            echo "<li>" . htmlspecialchars($fix) . "</li>";
        }
        echo "</ul>";
    }
    
    if (!empty($warnings)) {
        echo "<h2 style='color: orange;'>⚠ Warnings:</h2><ul>";
        foreach ($warnings as $warning) {
            echo "<li>" . htmlspecialchars($warning) . "</li>";
        }
        echo "</ul>";
    }
    
    // Show current table structure
    echo "<h3>Current Users Table Structure:</h3>";
    $result = $conn->query("DESCRIBE users");
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($row['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra'] ?? '') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show primary keys
    echo "<h3>Primary Keys:</h3>";
    $pkResult = $conn->query("SHOW KEYS FROM users WHERE Key_name = 'PRIMARY'");
    if ($pkResult->num_rows > 0) {
        echo "<ul>";
        while ($pk = $pkResult->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($pk['Column_name']) . " (Sequence: " . $pk['Seq_in_index'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>No primary key found!</p>";
    }
    
    echo "<p style='margin-top:30px;'><a href='signup.php' style='padding:10px 20px;background:#42b72a;color:white;text-decoration:none;border-radius:6px;'>Go to Sign Up Page</a> ";
    echo "<a href='login.php' style='padding:10px 20px;background:#1877f2;color:white;text-decoration:none;border-radius:6px;margin-left:10px;'>Go to Login Page</a></p>";
    echo "</body></html>";
    
} catch (Exception $e) {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Database Fix Error</title></head><body>";
    echo "<h2 style='color: red;'>✗ Error</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Error Code:</strong> " . $conn->errno . "</p>";
    echo "<p><strong>Please try running this SQL manually in phpMyAdmin:</strong></p>";
    echo "<pre style='background:#f5f5f5;padding:15px;border:1px solid #ddd;'>";
    echo "-- If table has no data:\n";
    echo "ALTER TABLE `users` ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);\n\n";
    echo "-- If table has data, run these in order:\n";
    echo "ALTER TABLE `users` ADD COLUMN `id` INT(11) NULL FIRST;\n";
    echo "SET @row_number = 0;\n";
    echo "UPDATE `users` SET `id` = (@row_number:=@row_number + 1) WHERE `id` IS NULL;\n";
    echo "ALTER TABLE `users` MODIFY COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);";
    echo "</pre>";
    echo "</body></html>";
}
?>


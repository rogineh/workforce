<?php
// diag.php
require_once 'includes/db.php';

echo "<h2>Database Diagnostics</h2>";
echo "Connecting to: " . $dbname . "<br>";

try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables found: " . implode(", ", $tables) . "<br>";

    if (in_array('users', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $count = $stmt->fetchColumn();
        echo "Users count: " . $count . "<br>";

        if ($count > 0) {
            $stmt = $pdo->query("SELECT id, username, role FROM users");
            echo "Users list:<br><ul>";
            while ($row = $stmt->fetch()) {
                echo "<li>ID: {$row['id']} | Username: {$row['username']} | Role: {$row['role']}</li>";
            }
            echo "</ul>";
        } else {
            echo "No users found in the 'users' table.<br>";
        }
    } else {
        echo "CRITICAL: 'users' table does not exist in this database.<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
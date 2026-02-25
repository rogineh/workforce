<?php
// includes/db.php - Database connection management

$host = 'localhost';
$dbname = 'rokitsit_workforce';
$username = 'rokitsit_workforce'; // DEFAULT for many local environments
$password = 'Workforce759459:';     // DEFAULT for many local environments

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
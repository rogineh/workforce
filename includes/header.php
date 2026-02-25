<?php
// includes/header.php
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workforce System - <?php echo $page_title ?? 'Dashboard'; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="content">
        <header>
            <h1><?php echo $page_title ?? 'Dashboard'; ?></h1>
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <!-- Weather Widget in Header -->
                <div id="weather-widget" class="weather-compact" style="display: none;">
                    <span id="weather-icon">☁️</span>
                    <span id="weather-temp">--°C</span>
                    <span id="weather-details" class="weather-desc">Loading...</span>
                </div>

                <div class="user-profile">
                    <span
                        style="font-weight: 600; margin-right: 0.5rem;"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>
        </header>
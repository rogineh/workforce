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
            <div class="user-profile" style="display: flex; gap: 1rem; align-items: center;">
                <span style="font-weight: 600;"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" style="font-size: 0.75rem; color: #ef4444; font-weight: 600; text-decoration: none; padding: 0.25rem 0.5rem; border: 1px solid #fee2e2; border-radius: 4px;">Logout</a>
            </div>
        </header>
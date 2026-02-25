<?php
// index.php - Main Dashboard
require_once 'includes/db.php';
$page_title = 'Dashboard';
include 'includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Employees</h3>
        <?php
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM employees");
            echo "<p>" . $stmt->fetchColumn() . "</p>";
        } catch (Exception $e) {
            echo "<p>0</p>";
        }
        ?>
    </div>
    <div class="stat-card">
        <h3>Today's Shifts</h3>
        <?php
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM shifts WHERE DATE(start_time) = CURDATE()");
            $stmt->execute();
            echo "<p>" . $stmt->fetchColumn() . "</p>";
        } catch (Exception $e) {
            echo "<p>0</p>";
        }
        ?>
    </div>
    <div class="stat-card">
        <h3>Pending Payroll</h3>
        <p>0</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
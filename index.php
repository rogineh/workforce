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
        <?php
        try {
            $stmt = $pdo->query("
                SELECT SUM(s.total_hours * e.base_pay_rate) 
                FROM shifts s 
                JOIN employees e ON s.employee_id = e.id 
                WHERE s.status IN ('completed', 'verified')
            ");
            $pending = $stmt->fetchColumn();
            echo "<p>$" . number_format($pending ?: 0, 2) . "</p>";
        } catch (Exception $e) {
            echo "<p>$0.00</p>";
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
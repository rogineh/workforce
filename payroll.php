<?php
// payroll.php - Payroll Summary
require_once 'includes/db.php';
$page_title = 'Payroll Summary';

$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-14 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

include 'includes/header.php';
?>

<div class="filters"
    style="background: var(--bg-card); padding: 1.5rem; border-radius: 1rem; border: 1px solid var(--border); margin-bottom: 2rem; box-shadow: var(--shadow);">
    <form method="GET" style="display: flex; gap: 1.5rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Start Date</label>
            <input type="date" name="start_date" value="<?php echo $start_date; ?>">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>End Date</label>
            <input type="date" name="end_date" value="<?php echo $end_date; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="export.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>"
            class="btn btn-secondary" style="background: var(--accent); color: white;">Proceed to Export</a>
    </form>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Shift Count</th>
                <th>Total Hours</th>
                <th>Base Rate</th>
                <th>Estimated Gross Pay</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            try {
                $stmt = $pdo->prepare("
                    SELECT 
                        e.id as employee_id,
                        e.first_name, 
                        e.last_name, 
                        e.base_pay_rate,
                        COUNT(s.id) as shift_count,
                        SUM(s.total_hours) as total_hours
                    FROM employees e
                    JOIN shifts s ON e.id = s.employee_id
                    WHERE s.start_time BETWEEN ? AND ?
                    GROUP BY e.id
                ");
                $stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59']);

                $found = false;
                while ($row = $stmt->fetch()) {
                    $found = true;
                    $gross_pay = $row['total_hours'] * $row['base_pay_rate'];
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
                    echo "<td>" . $row['shift_count'] . "</td>";
                    echo "<td>" . number_format($row['total_hours'], 2) . " hrs</td>";
                    echo "<td>$" . number_format($row['base_pay_rate'], 2) . "/hr</td>";
                    echo "<td><strong>$" . number_format($gross_pay, 2) . "</strong></td>";
                    echo "<td><a href='roster.php?employee_id=" . $row['employee_id'] . "' class='text-primary'>View Detailed Shifts</a></td>";
                    echo "</tr>";
                }

                if (!$found) {
                    echo "<tr><td colspan='6' style='text-align: center; padding: 2rem;'>No shifts found for this period.</td></tr>";
                }
            } catch (Exception $e) {
                echo "<tr><td colspan='6'>Error: " . $e->getMessage() . "</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
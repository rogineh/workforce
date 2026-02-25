<?php
// export.php - Payroll Data Export
require_once 'includes/auth.php';
require_once 'includes/db.php';

$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-14 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$system = $_GET['system'] ?? '';

// Handle CSV Generation and Download
if ($system && in_array($system, ['xero', 'myob', 'generic'])) {

    // Fetch shifts for the period
    $stmt = $pdo->prepare("
        SELECT 
            s.start_time, 
            s.total_hours, 
            e.first_name, 
            e.last_name,
            pc.name as category_name
        FROM shifts s
        JOIN employees e ON s.employee_id = e.id
        LEFT JOIN pay_categories pc ON s.pay_category_id = pc.id
        WHERE s.start_time BETWEEN ? AND ?
        ORDER BY e.last_name, s.start_time
    ");
    $stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
    $data = $stmt->fetchAll();

    $filename = "payroll_export_{$system}_" . date('Ymd_His') . ".csv";

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    if ($system == 'xero') {
        // Xero Headers: EmployeeName, Date, Hours, Status, EarningsRate
        fputcsv($output, ['EmployeeName', 'Date', 'Hours', 'Status', 'EarningsRate']);
        foreach ($data as $row) {
            fputcsv($output, [
                $row['first_name'] . ' ' . $row['last_name'],
                date('Y-m-d', strtotime($row['start_time'])),
                $row['total_hours'],
                'DRAFT',
                $row['category_name'] ?? 'Regular Pay'
            ]);
        }
    } elseif ($system == 'myob') {
        // MYOB Headers: LastName, FirstName, PayrollCategory, Date, Units
        fputcsv($output, ['LastName', 'FirstName', 'PayrollCategory', 'Date', 'Units']);
        foreach ($data as $row) {
            fputcsv($output, [
                $row['last_name'],
                $row['first_name'],
                $row['category_name'] ?? 'Base Hourly',
                date('d/m/Y', strtotime($row['start_time'])),
                $row['total_hours']
            ]);
        }
    } else {
        // Generic Export
        fputcsv($output, ['First Name', 'Last Name', 'Date', 'Hours', 'Pay Rate']);
        foreach ($data as $row) {
            fputcsv($output, [
                $row['first_name'],
                $row['last_name'],
                date('Y-m-d', strtotime($row['start_time'])),
                $row['total_hours'],
                '' // Rate not in this query but could be added
            ]);
        }
    }

    fclose($output);
    exit;
}

$page_title = 'Export Payroll Data';
include 'includes/header.php';
?>

<div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <div class="card"
        style="background: var(--bg-card); padding: 2rem; border-radius: 1rem; border: 1px solid var(--border); box-shadow: var(--shadow);">
        <h2 style="margin-bottom: 1.5rem;">Export to CSV</h2>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Select the target accounting system to generate a
            compatible CSV file for the period: <strong>
                <?php echo $start_date; ?>
            </strong> to <strong>
                <?php echo $end_date; ?>
            </strong></p>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <a href="export.php?system=xero&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>"
                class="btn btn-primary" style="justify-content: center; background: #13b5ea;">Export for Xero</a>
            <a href="export.php?system=myob&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>"
                class="btn btn-primary" style="justify-content: center; background: #612e91;">Export for MYOB</a>
            <a href="export.php?system=generic&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>"
                class="btn btn-secondary"
                style="justify-content: center; background: var(--text-muted); color: white;">Export Generic CSV</a>
        </div>
    </div>

    <div class="info-card" style="background: #eff6ff; padding: 2rem; border-radius: 1rem; border: 1px solid #bfdbfe;">
        <h3 style="color: #1e40af; margin-bottom: 1rem;">Export Guide</h3>
        <ul style="color: #1e3a8a; padding-left: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
            <li><strong>Xero:</strong> Go to Payroll > Timesheets > Import. Select the generated file.</li>
            <li><strong>MYOB:</strong> Use the Import/Export Assistant. Select "Timesheets" and match the columns
                accordingly.</li>
            <li>Ensure all employee names in this system match exactly with your accounting software.</li>
            <li>Verify calculated hours before exporting to ensure accuracy.</li>
        </ul>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
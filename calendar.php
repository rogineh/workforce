<?php
// calendar.php - Calendar Report View
require_once 'includes/db.php';
$page_title = 'Calendar Report';

$month = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('m');
$year = isset($_GET['year']) ? (int) $_GET['year'] : (int) date('Y');

// Calculate previous and next month
$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month == 0) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $month + 1;
$next_year = $year;
if ($next_month == 13) {
    $next_month = 1;
    $next_year++;
}

$start_date = "{$year}-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01";
$days_in_month = date('t', strtotime($start_date));
$first_day_of_week = date('w', strtotime($start_date));

// Fetch all shifts for this month
$stmt = $pdo->prepare("
    SELECT s.*, e.first_name, e.last_name 
    FROM shifts s 
    JOIN employees e ON s.employee_id = e.id 
    WHERE s.start_time BETWEEN ? AND ?
    ORDER BY s.start_time ASC
");
$end_date_limit = "{$year}-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-{$days_in_month} 23:59:59";
$stmt->execute([$start_date . " 00:00:00", $end_date_limit]);
$shifts = [];
while ($row = $stmt->fetch()) {
    $day = (int) date('j', strtotime($row['start_time']));
    $shifts[$day][] = $row;
}

include 'includes/header.php';
?>

<div class="calendar-nav">
    <div style="display: flex; gap: 0.5rem;">
        <a href="calendar.php?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" class="btn"
            style="background: #e2e8f0;">&larr; Prev</a>
        <a href="calendar.php?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" class="btn"
            style="background: #e2e8f0;">Next &rarr;</a>
    </div>
    <h2 style="font-size: 1.5rem; font-weight: 700;">
        <?php echo date('F Y', strtotime($start_date)); ?>
    </h2>
    <a href="calendar.php" class="btn btn-secondary" style="background: var(--text-muted); color: white;">Today</a>
</div>

<div class="calendar-grid">
    <!-- Headers -->
    <div class="calendar-day-header">Sun</div>
    <div class="calendar-day-header">Mon</div>
    <div class="calendar-day-header">Tue</div>
    <div class="calendar-day-header">Wed</div>
    <div class="calendar-day-header">Thu</div>
    <div class="calendar-day-header">Fri</div>
    <div class="calendar-day-header">Sat</div>

    <?php
    // Empty slots before first day of month
    for ($i = 0; $i < $first_day_of_week; $i++) {
        echo '<div class="calendar-day other-month"></div>';
    }

    // Days of the month
    for ($day = 1; $day <= $days_in_month; $day++) {
        echo '<div class="calendar-day">';
        echo '<div class="day-number">' . $day . '</div>';

        if (isset($shifts[$day])) {
            foreach ($shifts[$day] as $shift) {
                $time = date('H:i', strtotime($shift['start_time'])) . ' - ' . date('H:i', strtotime($shift['end_time']));
                echo '<div class="calendar-shift">';
                echo '<span class="time">' . $time . '</span>';
                echo '<span class="emp">' . htmlspecialchars($shift['first_name'] . ' ' . $shift['last_name']) . '</span>';
                echo '</div>';
            }
        }

        echo '</div>';
    }

    // Empty slots after last day of month
    $remaining_slots = (7 - (($first_day_of_week + $days_in_month) % 7)) % 7;
    for ($i = 0; $i < $remaining_slots; $i++) {
        echo '<div class="calendar-day other-month"></div>';
    }
    ?>
</div>

<?php include 'includes/footer.php'; ?>
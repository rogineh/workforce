<!-- includes/sidebar.php -->
<div class="sidebar">
    <h2>Workforce</h2>
    <nav>
        <a href="index.php"
            class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a>
        <a href="employees.php"
            class="<?php echo basename($_SERVER['PHP_SELF']) == 'employees.php' ? 'active' : ''; ?>">Employees</a>
        <a href="roster.php"
            class="<?php echo basename($_SERVER['PHP_SELF']) == 'roster.php' ? 'active' : ''; ?>">Rostering</a>
        <a href="calendar.php"
            class="<?php echo basename($_SERVER['PHP_SELF']) == 'calendar.php' ? 'active' : ''; ?>">Calendar Report</a>
        <a href="payroll.php"
            class="<?php echo basename($_SERVER['PHP_SELF']) == 'payroll.php' ? 'active' : ''; ?>">Payroll</a>
        <a href="export.php"
            class="<?php echo basename($_SERVER['PHP_SELF']) == 'export.php' ? 'active' : ''; ?>">Export</a>
    </nav>
</div>
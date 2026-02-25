<?php
// employees.php - Employee Management
require_once 'includes/db.php';
$page_title = 'Employee Management';

// Handle Add Employee
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_employee'])) {
    $first_day_of_week = $_POST['first'];
    $last_name = $_POST['last'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];
    $country = $_POST['country'];
    $pay_rate = $_POST['pay_rate'];
    $pay_category = $_POST['pay_category'];
    $hire_date = $_POST['hire_date'];
    $termination_date = $_POST['termination_date'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];
    $created_at = $_POST['created_at'] = '';
    $updated_at = $_POST['updated_at'] = '';

    try {
        $stmt = $pdo->prepare("INSERT INTO employees (first_day_of_week, last_name, email, phone, address, city, state, zip, country, pay_rate, pay_category, hire_date, termination_date, status, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_day_of_week, $last_name, $email, $phone, $address, $city, $state, $zip, $country, $pay_rate, $pay_category, $hire_date, $termination_date, $status, $notes, $created_at, $updated_at]);
        header("Location: test.php?success=1");
        exit;
    } catch (Exception $e) {
        $error = "Error adding employee: " . $e->getMessage();
    }

}

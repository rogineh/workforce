<?php
// employees.php - Employee Management
require_once 'includes/db.php';
$page_title = 'Employee Management';

// Handle Add Employee
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_employee'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $pay_rate = $_POST['base_pay_rate'];
    $type = $_POST['employment_type'];

    try {
        $stmt = $pdo->prepare("INSERT INTO employees (first_name, last_name, email, phone, base_pay_rate, employment_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $phone, $pay_rate, $type]);
        header("Location: employees.php?success=1");
        exit;
    } catch (Exception $e) {
        $error = "Error adding employee: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="actions" style="margin-bottom: 2rem; display: flex; justify-content: flex-end;">
    <button class="btn btn-primary" onclick="document.getElementById('addModal').style.display='flex'">Add
        Employee</button>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">
        Employee added successfully!
    </div>
<?php endif; ?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Pay Rate</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            try {
                $stmt = $pdo->query("SELECT * FROM employees ORDER BY last_name, first_name");
                while ($row = $stmt->fetch()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                    echo "<td>$" . number_format($row['base_pay_rate'], 2) . "/hr</td>";
                    echo "<td>" . htmlspecialchars($row['employment_type']) . "</td>";
                    echo "<td><a href='#' class='text-primary'>Edit</a></td>";
                    echo "</tr>";
                }
            } catch (Exception $e) {
                echo "<tr><td colspan='6'>No employees found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Add Employee Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem;">Add New Employee</h2>
            <button onclick="document.getElementById('addModal').style.display='none'"
                style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form method="POST">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" required>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone">
            </div>
            <div class="form-group">
                <label>Base Pay Rate ($/hr)</label>
                <input type="number" step="0.01" name="base_pay_rate" value="25.00">
            </div>
            <div class="form-group">
                <label>Employment Type</label>
                <select name="employment_type">
                    <option value="Casual">Casual</option>
                    <option value="Full-time">Full-time</option>
                    <option value="Part-time">Part-time</option>
                </select>
            </div>
            <button type="submit" name="add_employee" class="btn btn-primary" style="width: 100%;">Create
                Employee</button>
        </form>
    </div>
</div>

<script>
    // Close modal when clicking outside
    window.onclick = function (event) {
        let modal = document.getElementById('addModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<?php include 'includes/footer.php'; ?>
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
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];
    $country = $_POST['country'];
    $pay_rate = $_POST['base_pay_rate'];
    $type = $_POST['employment_type'];
    $hire_date = $_POST['hire_date'] ?: null;
    $status = $_POST['status'];
    $notes = $_POST['notes'];

    try {
        $stmt = $pdo->prepare("INSERT INTO employees (first_name, last_name, email, phone, address, city, state, zip, country, base_pay_rate, employment_type, hire_date, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $phone, $address, $city, $state, $zip, $country, $pay_rate, $type, $hire_date, $status, $notes]);
        header("Location: employees.php?success=1");
        exit;
    } catch (Exception $e) {
        $error = "Error adding employee: " . $e->getMessage();
    }
}

// Handle Edit Employee
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_employee'])) {
    $id = $_POST['employee_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];
    $country = $_POST['country'];
    $pay_rate = $_POST['base_pay_rate'];
    $type = $_POST['employment_type'];
    $hire_date = $_POST['hire_date'] ?: null;
    $termination_date = $_POST['termination_date'] ?: null;
    $status = $_POST['status'];
    $notes = $_POST['notes'];

    try {
        $stmt = $pdo->prepare("UPDATE employees SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, city = ?, state = ?, zip = ?, country = ?, base_pay_rate = ?, employment_type = ?, hire_date = ?, termination_date = ?, status = ?, notes = ? WHERE id = ?");
        $stmt->execute([$first_name, $last_name, $email, $phone, $address, $city, $state, $zip, $country, $pay_rate, $type, $hire_date, $termination_date, $status, $notes, $id]);
        header("Location: employees.php?updated=1");
        exit;
    } catch (Exception $e) {
        $error = "Error updating employee: " . $e->getMessage();
    }
}

// Handle Delete Employee
if (isset($_GET['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        header("Location: employees.php?deleted=1");
        exit;
    } catch (Exception $e) {
        $error = "Error deleting employee: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="actions" style="margin-bottom: 2rem; display: flex; justify-content: flex-end;">
    <button class="btn btn-primary" onclick="document.getElementById('addModal').style.display='flex'">Add Employee</button>
</div>

<?php if (isset($_GET['success'])): ?>
        <div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">Employee added successfully!</div>
<?php endif; ?>

<?php if (isset($_GET['updated'])): ?>
        <div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">Employee updated successfully!</div>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">Employee deleted successfully!</div>
<?php endif; ?>

<?php if (isset($error)): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;"><?php echo $error; ?></div>
<?php endif; ?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Location</th>
                <th>Pay Rate</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            try {
                $stmt = $pdo->query("SELECT * FROM employees ORDER BY last_name, first_name");
                while ($row = $stmt->fetch()) {
                    $status_class = $row['status'] == 'Active' ? 'background: #dcfce7; color: #166534;' : 'background: #f1f5f9; color: #475569;';
                    echo "<tr>";
                    echo "<td><strong>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</strong><br><small>" . htmlspecialchars($row['employment_type']) . "</small></td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "<br><small>" . htmlspecialchars($row['phone']) . "</small></td>";
                    echo "<td>" . htmlspecialchars($row['city'] . ($row['state'] ? ', ' . $row['state'] : '')) . "</td>";
                    echo "<td>$" . number_format($row['base_pay_rate'], 2) . "/hr</td>";
                    echo "<td><span style='padding: 0.25rem 0.5rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600; " . $status_class . "'>" . $row['status'] . "</span></td>";
                    echo "<td>
                            <a href='#' onclick='openEditModal(" . json_encode($row) . ")' class='text-primary'>Edit</a> | 
                            <a href='employees.php?delete_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure? This will delete all shifts for this employee.\")' class='text-danger' style='color: #ef4444;'>Delete</a>
                          </td>";
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
    <div class="modal-content" style="max-width: 800px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem;">Add New Employee</h2>
            <button onclick="document.getElementById('addModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="section">
                    <h3 style="font-size: 0.875rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1rem; border-bottom: 1px solid var(--border);">Basic Info</h3>
                    <div class="form-group"><label>First Name</label><input type="text" name="first_name" required></div>
                    <div class="form-group"><label>Last Name</label><input type="text" name="last_name" required></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email"></div>
                    <div class="form-group"><label>Phone</label><input type="text" name="phone"></div>
                </div>
                <div class="section">
                    <h3 style="font-size: 0.875rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1rem; border-bottom: 1px solid var(--border);">Address</h3>
                    <div class="form-group"><label>Address</label><input type="text" name="address"></div>
                    <div style="display: flex; gap: 1rem;">
                        <div class="form-group" style="flex: 2;"><label>City</label><input type="text" name="city"></div>
                        <div class="form-group" style="flex: 1;"><label>State</label><input type="text" name="state"></div>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <div class="form-group"><label>Zip</label><input type="text" name="zip"></div>
                        <div class="form-group"><label>Country</label><input type="text" name="country" value="Australia"></div>
                    </div>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                <div class="section">
                    <h3 style="font-size: 0.875rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1rem; border-bottom: 1px solid var(--border);">Employment</h3>
                    <div class="form-group"><label>Base Pay Rate ($/hr)</label><input type="number" step="0.01" name="base_pay_rate" value="25.00"></div>
                    <div class="form-group"><label>Employment Type</label>
                        <select name="employment_type">
                            <option value="Casual">Casual</option><option value="Full-time">Full-time</option><option value="Part-time">Part-time</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Hire Date</label><input type="date" name="hire_date"></div>
                </div>
                <div class="section">
                    <h3 style="font-size: 0.875rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1rem; border-bottom: 1px solid var(--border);">Status & Notes</h3>
                    <div class="form-group"><label>Status</label>
                        <select name="status">
                            <option value="Active">Active</option><option value="Inactive">Inactive</option><option value="Terminated">Terminated</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Notes</label><textarea name="notes" rows="4"></textarea></div>
                </div>
            </div>
            <button type="submit" name="add_employee" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Create Employee</button>
        </form>
    </div>
</div>

<!-- Edit Employee Modal -->
<div id="editModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem;">Edit Employee</h2>
            <button onclick="document.getElementById('editModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="employee_id" id="edit_employee_id">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="section">
                    <h3 style="font-size: 0.875rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1rem; border-bottom: 1px solid var(--border);">Basic Info</h3>
                    <div class="form-group"><label>First Name</label><input type="text" name="first_name" id="edit_first_name" required></div>
                    <div class="form-group"><label>Last Name</label><input type="text" name="last_name" id="edit_last_name" required></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" id="edit_email"></div>
                    <div class="form-group"><label>Phone</label><input type="text" name="phone" id="edit_phone"></div>
                </div>
                <div class="section">
                    <h3 style="font-size: 0.875rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1rem; border-bottom: 1px solid var(--border);">Address</h3>
                    <div class="form-group"><label>Address</label><input type="text" name="address" id="edit_address"></div>
                    <div style="display: flex; gap: 1rem;">
                        <div class="form-group" style="flex: 2;"><label>City</label><input type="text" name="city" id="edit_city"></div>
                        <div class="form-group" style="flex: 1;"><label>State</label><input type="text" name="state" id="edit_state"></div>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <div class="form-group"><label>Zip</label><input type="text" name="zip" id="edit_zip"></div>
                        <div class="form-group"><label>Country</label><input type="text" name="country" id="edit_country"></div>
                    </div>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                <div class="section">
                    <h3 style="font-size: 0.875rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1rem; border-bottom: 1px solid var(--border);">Employment</h3>
                    <div class="form-group"><label>Base Pay Rate ($/hr)</label><input type="number" step="0.01" name="base_pay_rate" id="edit_base_pay_rate"></div>
                    <div class="form-group"><label>Employment Type</label>
                        <select name="employment_type" id="edit_employment_type">
                            <option value="Casual">Casual</option><option value="Full-time">Full-time</option><option value="Part-time">Part-time</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <div class="form-group"><label>Hire Date</label><input type="date" name="hire_date" id="edit_hire_date"></div>
                        <div class="form-group"><label>Termination</label><input type="date" name="termination_date" id="edit_termination_date"></div>
                    </div>
                </div>
                <div class="section">
                    <h3 style="font-size: 0.875rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1rem; border-bottom: 1px solid var(--border);">Status & Notes</h3>
                    <div class="form-group"><label>Status</label>
                        <select name="status" id="edit_status">
                            <option value="Active">Active</option><option value="Inactive">Inactive</option><option value="Terminated">Terminated</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Notes</label><textarea name="notes" id="edit_notes" rows="4"></textarea></div>
                </div>
            </div>
            <button type="submit" name="edit_employee" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Update Employee</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(emp) {
        document.getElementById('edit_employee_id').value = emp.id;
        document.getElementById('edit_first_name').value = emp.first_name;
        document.getElementById('edit_last_name').value = emp.last_name;
        document.getElementById('edit_email').value = emp.email;
        document.getElementById('edit_phone').value = emp.phone;
        document.getElementById('edit_address').value = emp.address || '';
        document.getElementById('edit_city').value = emp.city || '';
        document.getElementById('edit_state').value = emp.state || '';
        document.getElementById('edit_zip').value = emp.zip || '';
        document.getElementById('edit_country').value = emp.country || '';
        document.getElementById('edit_base_pay_rate').value = emp.base_pay_rate;
        document.getElementById('edit_employment_type').value = emp.employment_type;
        document.getElementById('edit_hire_date').value = emp.hire_date || '';
        document.getElementById('edit_termination_date').value = emp.termination_date || '';
        document.getElementById('edit_status').value = emp.status;
        document.getElementById('edit_notes').value = emp.notes || '';
        document.getElementById('editModal').style.display = 'flex';
    }

    window.onclick = function (event) {
        let addModal = document.getElementById('addModal');
        let editModal = document.getElementById('editModal');
        if (event.target == addModal) addModal.style.display = "none";
        if (event.target == editModal) editModal.style.display = "none";
    }
</script>

<?php include 'includes/footer.php'; ?>
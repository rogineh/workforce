<?php
// roster.php - Shift Management
require_once 'includes/db.php';
$page_title = 'Rostering';

// Handle Add Shift
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_shift'])) {
    $employee_id = $_POST['employee_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $break_minutes = (int) $_POST['break_minutes'];
    $notes = $_POST['notes'];

    // Calculate total hours
    $start = new DateTime($start_time);
    $end = new DateTime($end_time);
    $interval = $start->diff($end);
    $total_hours = ($interval->h + ($interval->i / 60)) - ($break_minutes / 60);

    try {
        $stmt = $pdo->prepare("INSERT INTO shifts (employee_id, start_time, end_time, break_minutes, total_hours, notes, status) VALUES (?, ?, ?, ?, ?, ?, 'scheduled')");
        $stmt->execute([$employee_id, $start_time, $end_time, $break_minutes, $total_hours, $notes]);
        header("Location: roster.php?success=1");
        exit;
    } catch (Exception $e) {
        $error = "Error adding shift: " . $e->getMessage();
    }
}

// Handle Edit Shift
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_shift'])) {
    $shift_id = $_POST['shift_id'];
    $employee_id = $_POST['employee_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $break_minutes = (int) $_POST['break_minutes'];
    $notes = $_POST['notes'];
    $status = $_POST['status'];

    // Calculate total hours
    $start = new DateTime($start_time);
    $end = new DateTime($end_time);
    $interval = $start->diff($end);
    $total_hours = ($interval->h + ($interval->i / 60)) - ($break_minutes / 60);

    try {
        $stmt = $pdo->prepare("UPDATE shifts SET employee_id = ?, start_time = ?, end_time = ?, break_minutes = ?, total_hours = ?, notes = ?, status = ? WHERE id = ?");
        $stmt->execute([$employee_id, $start_time, $end_time, $break_minutes, $total_hours, $notes, $status, $shift_id]);
        header("Location: roster.php?updated=1");
        exit;
    } catch (Exception $e) {
        $error = "Error updating shift: " . $e->getMessage();
    }
}

// Handle Delete Shift
if (isset($_GET['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM shifts WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        header("Location: roster.php?deleted=1");
        exit;
    } catch (Exception $e) {
        $error = "Error deleting shift: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="actions" style="margin-bottom: 2rem; display: flex; justify-content: flex-end;">
    <button class="btn btn-primary" onclick="document.getElementById('shiftModal').style.display='flex'">Add
        Shift</button>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">
        Shift scheduled successfully!
    </div>
<?php endif; ?>

<?php if (isset($_GET['updated'])): ?>
    <div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">
        Shift updated successfully!
    </div>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">
        Shift deleted successfully!
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Date</th>
                <th>Shift Time</th>
                <th>Break</th>
                <th>Total Hours</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            try {
                $stmt = $pdo->query("
                    SELECT s.*, e.first_name, e.last_name 
                    FROM shifts s 
                    JOIN employees e ON s.employee_id = e.id 
                    ORDER BY s.start_time DESC
                ");
                while ($row = $stmt->fetch()) {
                    $date = date('D, M j', strtotime($row['start_time']));
                    $time = date('H:i', strtotime($row['start_time'])) . ' - ' . date('H:i', strtotime($row['end_time']));
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
                    echo "<td>" . $date . "</td>";
                    echo "<td>" . $time . "</td>";
                    echo "<td>" . $row['break_minutes'] . " m</td>";
                    echo "<td>" . number_format($row['total_hours'], 2) . " hrs</td>";
                    echo "<td><span class='badge' style='background: #e2e8f0; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;'>" . ucfirst($row['status']) . "</span></td>";
                    echo "<td>
                            <a href='#' onclick='openEditModal(" . json_encode($row) . ")' class='text-primary'>Edit</a> | 
                            <a href='roster.php?delete_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this shift?\")' class='text-danger' style='color: #ef4444;'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } catch (Exception $e) {
                echo "<tr><td colspan='7'>No shifts found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Add Shift Modal -->
<div id="shiftModal" class="modal">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem;">Schedule Shift</h2>
            <button onclick="document.getElementById('shiftModal').style.display='none'"
                style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form method="POST">
            <div class="form-group">
                <label>Employee</label>
                <select name="employee_id" required>
                    <option value="">Select Employee</option>
                    <?php
                    $emp_stmt = $pdo->query("SELECT id, first_name, last_name FROM employees ORDER BY last_name");
                    while ($emp = $emp_stmt->fetch()) {
                        echo "<option value='{$emp['id']}'>{$emp['first_name']} {$emp['last_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Start Time</label>
                <input type="datetime-local" name="start_time" required>
            </div>
            <div class="form-group">
                <label>End Time</label>
                <input type="datetime-local" name="end_time" required>
            </div>
            <div class="form-group">
                <label>Break (Minutes)</label>
                <input type="number" name="break_minutes" value="30">
            </div>
            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" rows="2"></textarea>
            </div>
            <button type="submit" name="add_shift" class="btn btn-primary" style="width: 100%;">Schedule Shift</button>
        </form>
    </div>
</div>

<!-- Edit Shift Modal -->
<div id="editShiftModal" class="modal">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem;">Edit Shift</h2>
            <button onclick="document.getElementById('editShiftModal').style.display='none'"
                style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="shift_id" id="edit_shift_id">
            <div class="form-group">
                <label>Employee</label>
                <select name="employee_id" id="edit_employee_id" required>
                    <?php
                    $emp_stmt = $pdo->query("SELECT id, first_name, last_name FROM employees ORDER BY last_name");
                    while ($emp = $emp_stmt->fetch()) {
                        echo "<option value='{$emp['id']}'>{$emp['first_name']} {$emp['last_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Start Time</label>
                <input type="datetime-local" name="start_time" id="edit_start_time" required>
            </div>
            <div class="form-group">
                <label>End Time</label>
                <input type="datetime-local" name="end_time" id="edit_end_time" required>
            </div>
            <div class="form-group">
                <label>Break (Minutes)</label>
                <input type="number" name="break_minutes" id="edit_break_minutes">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="edit_status">
                    <option value="scheduled">Scheduled</option>
                    <option value="completed">Completed</option>
                    <option value="verified">Verified</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" id="edit_notes" rows="2"></textarea>
            </div>
            <button type="submit" name="edit_shift" class="btn btn-primary" style="width: 100%;">Update Shift</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(shift) {
        document.getElementById('edit_shift_id').value = shift.id;
        document.getElementById('edit_employee_id').value = shift.employee_id;

        // Format dates for datetime-local input (YYYY-MM-DDTHH:MM)
        const formatDateTime = (dateStr) => {
            const d = new Date(dateStr);
            const pad = (n) => n.toString().padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
        };

        document.getElementById('edit_start_time').value = formatDateTime(shift.start_time);
        document.getElementById('edit_end_time').value = formatDateTime(shift.end_time);
        document.getElementById('edit_break_minutes').value = shift.break_minutes;
        document.getElementById('edit_status').value = shift.status;
        document.getElementById('edit_notes').value = shift.notes;

        document.getElementById('editShiftModal').style.display = 'flex';
    }

    window.onclick = function (event) {
        let addModal = document.getElementById('shiftModal');
        let editModal = document.getElementById('editShiftModal');
        if (event.target == addModal) {
            addModal.style.display = "none";
        }
        if (event.target == editModal) {
            editModal.style.display = "none";
        }
    }
</script>

<?php include 'includes/footer.php'; ?>
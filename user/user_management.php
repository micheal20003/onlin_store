<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'fitness_app';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle update subscription
if (isset($_POST['update_user'])) {
    $edit_id = $_POST['edit_id'];
    $subscription = $_POST['subscription'];

    $sql = "UPDATE users SET subscription = '$subscription' WHERE id = $edit_id";
    if ($conn->query($sql)) {
        echo "<div class='alert alert-success text-center'>User subscription updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Error updating user: " . $conn->error . "</div>";
    }
}

// Handle delete user
if (isset($_POST['delete_user'])) {
    $delete_id = $_POST['delete_id'];

    $delete_sql = "DELETE FROM users WHERE id = $delete_id";
    if ($conn->query($delete_sql)) {
        echo "<div class='alert alert-success text-center'>User deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Delete failed: " . $conn->error . "</div>";
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4" style="font-weight: bold;">User Management</h2>

    <table class="table text-center align-middle shadow-sm" style="border: none;">
        <thead style="background-color: #f8f9fa;">
            <tr style="font-weight: bold; border: none;">
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Subscription</th>
                <th>Subscription end date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM users ORDER BY id DESC");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
                <tr style="background-color: #f1f1f1; border: none;">
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>

                    <!-- Subscription -->
                    <td>
    <?php
        $subscription = isset($row['subscription']) ? strtolower($row['subscription']) : '';

        if ($subscription === 'premium') {
            echo "<span class='badge bg-success'>Premium</span>";
        } else {
            echo "<span class='badge bg-secondary'>Silver</span>";
        }
    ?>
</td>
<td>
    <?php
    $userid = $row['id'];
    $subRes = $conn->query("SELECT end_date FROM subscriptions WHERE user_id = $userid ORDER BY id DESC LIMIT 1");

    if ($subRes && $subRes->num_rows > 0) {
        $subRow = $subRes->fetch_assoc();
        // Convert end_date to timestamp and format as 'd M Y' (e.g., 15 Jun 2025)
        $formattedDate = date('d M Y', strtotime($subRow['end_date']));
        echo $formattedDate;
    } else {
        echo "<span class='text-muted'>No active subscription</span>";
    }
    ?>
</td>



                    <!-- Actions -->
                    <td>
                        <!-- Edit Button -->
                        <button 
                            class="btn btn-primary btn-sm rounded-pill px-3"
                            onclick="openEditModal(
                                '<?php echo $row['id']; ?>',
                                '<?php echo htmlspecialchars($row['subscription'], ENT_QUOTES); ?>'
                            )"
                            data-bs-toggle="modal" 
                            data-bs-target="#editModal">
                            Edit
                        </button>

                        <!-- Delete Button -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('Are you sure you want to delete this user?');">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php
                endwhile;
            else:
                echo "<tr><td colspan='6'>No users found.</td></tr>";
            endif;
            ?>
        </tbody>
    </table>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header" style="background-color: #343a40; color: #fff;">
                <h5 class="modal-title" id="editModalLabel">Edit User Subscription</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <input type="hidden" name="edit_id" id="edit_id">

                <div class="mb-3">
                    <label class="form-label">Subscription Type:</label>
                    <select name="subscription" id="edit_subscription" class="form-select" required>
                        <option value="Premium">Premium</option>
                        <option value="Silver">Silver</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" name="update_user" class="btn btn-success rounded-pill px-4">Save Changes</button>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function openEditModal(id, subscription) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_subscription').value = subscription;
}
</script>

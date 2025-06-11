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

// Handle update order
if (isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    $sql = "UPDATE orders SET order_status = '$order_status' WHERE id = $order_id";
    if ($conn->query($sql)) {
        echo "<div class='alert alert-success text-center'>Order status updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Error updating order: " . $conn->error . "</div>";
    }
}

// Handle delete order
if (isset($_POST['delete_order'])) {
    $delete_id = $_POST['delete_id'];
    $sql = "DELETE FROM orders WHERE id = $delete_id";
    if ($conn->query($sql)) {
        echo "<div class='alert alert-success text-center'>Order deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Delete failed: " . $conn->error . "</div>";
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4" style="font-weight: bold;">Order Management</h2>

    <table class="table text-center align-middle shadow-sm">
        <thead style="background-color: #f8f9fa;">
            <tr style="font-weight: bold;">
                <th>ID</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Address</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
                    <tr style="background-color: #f1f1f1;">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td>$<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>$<?php echo number_format($row['total_price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>

                        <!-- Order Status -->
                        <td>
                            <?php
                            $status = strtolower($row['order_status']);
                            switch ($status) {
                                case 'delivered':
                                    echo "<span class='badge bg-success'>Delivered</span>";
                                    break;
                                case 'ongoing':
                                    echo "<span class='badge bg-warning text-dark'>Ongoing</span>";
                                    break;
                                case 'cancelled':
                                    echo "<span class='badge bg-danger'>Cancelled</span>";
                                    break;
                                default:
                                    echo "<span class='badge bg-secondary'>" . ucfirst($status) . "</span>";
                            }
                            ?>
                        </td>

                        <!-- Actions -->
                        <td>
                            <button
                                class="btn btn-primary btn-sm rounded-pill px-3"
                                onclick="openOrderModal(
                            '<?php echo $row['id']; ?>',
                            '<?php echo htmlspecialchars($row['order_status'], ENT_QUOTES); ?>'
                        )"
                                data-bs-toggle="modal"
                                data-bs-target="#orderModal">
                                Edit
                            </button>

                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete_order" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('Are you sure you want to delete this order?');">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
            <?php
                endwhile;
            else:
                echo "<tr><td colspan='10'>No orders found.</td></tr>";
            endif;
            ?>
        </tbody>
    </table>
</div>

<!-- Edit Order Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="orderModalLabel">Edit Order Status</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <input type="hidden" name="order_id" id="order_id">

                <div class="mb-3">
                    <label class="form-label">Order Status:</label>
                    <select name="order_status" id="order_status" class="form-select" required>
                        <option value="delivered">Delivered</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" name="update_order" class="btn btn-success rounded-pill px-4">Save Changes</button>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function openOrderModal(id, status) {
        document.getElementById('order_id').value = id;
        document.getElementById('order_status').value = status.toLowerCase();
    }
</script>
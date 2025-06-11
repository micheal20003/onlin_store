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

// Get logged-in coach user ID
$logged_in_coach_id = $_SESSION['user_id']; // Make sure it's set when login

// Handle status update from dropdown
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['new_status'];

    $update = "UPDATE bookings SET status = '$new_status' WHERE id = $booking_id AND coach_id = $logged_in_coach_id";
    $conn->query($update);
}

// Fetch bookings for this coach
$sql = "SELECT bookings.id, bookings.user_id, bookings.coach_id, bookings.status, bookings.created_at, users.name AS user_name
        FROM bookings
        JOIN users ON bookings.user_id = users.id
        WHERE bookings.coach_id = $logged_in_coach_id
        ORDER BY bookings.id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Booking Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4 fw-bold">Booking Management</h2>
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-bordered text-center align-middle mb-0">
                <thead class="table-light">
                    <tr class="fw-bold">
                        <th>ID</th>
                        <th>User Name</th>
                        <th>Created At</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr style="background-color: #f9f9f9;">
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td>
                                    <?php
                                    $status = strtolower($row['status']);
                                    if ($status === 'booked') {
                                        echo "<span class='badge bg-success px-3 py-2'>Booked</span>";
                                    } elseif ($status === 'cancelled') {
                                        echo "<span class='badge bg-danger px-3 py-2'>Cancelled</span>";
                                    } else {
                                        echo "<span class='badge bg-secondary px-3 py-2'>" . ucfirst($status) . "</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <form method="POST" class="d-flex justify-content-center align-items-center gap-2">
                                        <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                        <select name="new_status" class="form-select form-select-sm" style="width: 120px;" required>
                                            <option value="booked" <?php if ($row['status'] === 'booked') echo 'selected'; ?>>Booked</option>
                                            <option value="cancelled" <?php if ($row['status'] === 'cancelled') echo 'selected'; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-sm btn-primary rounded-pill px-3">
                                            Update
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No bookings found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
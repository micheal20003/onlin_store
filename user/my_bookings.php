<?php


// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("<div style='color:red; text-align:center;'>❌ User not logged in.</div>");
}

$user_id = $_SESSION['user_id'];

// ✅ Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Get logged-in user's email and subscription type
$user_stmt = $conn->prepare("SELECT email, subscription FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_email = $user ? $user['email'] : "Unknown";
$subscription = $user ? $user['subscription'] : "unknown";

// ✅ Handle booking/cancellation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['coach_id'], $_POST['action'])) {
    $coach_id = intval($_POST['coach_id']);
    $action = $_POST['action'];

    if ($subscription === 'Silver') {
        $message = "❌ You cannot book a coach with a Silver subscription. Please upgrade to Premium.";
    } else {
        if ($action === 'book') {
            $check = $conn->prepare("SELECT id FROM bookings WHERE user_id = ? AND status = 'cancelled'");
            $check->bind_param("i", $user_id);
            $check->execute();
            $check_result = $check->get_result();

            if ($check_result->num_rows > 0) {
                $existing_booking = $check_result->fetch_assoc();
                $booking_id = $existing_booking['id'];

                $update = $conn->prepare("UPDATE bookings SET coach_id = ?, status = 'booked' WHERE id = ?");
                $update->bind_param("ii", $coach_id, $booking_id);
                if ($update->execute()) {
                    $message = "✅ Booking updated successfully!";
                } else {
                    $message = "❌ Failed to update booking.";
                }
                $update->close();
            } else {
                $stmt = $conn->prepare("INSERT INTO bookings (user_id, coach_id, status) VALUES (?, ?, 'booked')");
                $stmt->bind_param("ii", $user_id, $coach_id);
                if ($stmt->execute()) {
                    $message = "✅ Booking successful!";
                } else {
                    $message = "❌ Booking failed: " . $conn->error;
                }
                $stmt->close();
            }
            $check->close();
        } elseif ($action === 'cancel') {
            $cancel = $conn->prepare("UPDATE bookings SET status='cancelled' WHERE user_id = ? AND coach_id = ? AND status = 'booked'");
            $cancel->bind_param("ii", $user_id, $coach_id);
            if ($cancel->execute()) {
                $message = "❌ Booking cancelled.";
            } else {
                $message = "❌ Failed to cancel booking.";
            }
            $cancel->close();
        }
    }
}

// ✅ Get all active bookings for this user
$booking_status = [];
$active_booking_result = $conn->query("SELECT coach_id, status FROM bookings WHERE user_id = $user_id AND status = 'booked'");
while ($row = $active_booking_result->fetch_assoc()) {
    $booking_status[$row['coach_id']] = $row['status'];
}

// ✅ Fetch all coaches
$coaches_result = $conn->query("SELECT * FROM private_coach");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Book a Coach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-top {
            width: 100%;
            height: auto;
            max-height: 300px;
            /* Optional: adjust based on your layout */
            object-fit: contain;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Welcome, <?php echo htmlspecialchars($user_email); ?> 👋</h2>

        <?php if (isset($message)): ?>
            <div class="alert alert-info text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <?php if ($coaches_result->num_rows > 0): ?>
                <?php while ($row = $coaches_result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow rounded">
                            <?php
                            if (!empty($row['picture'])):
                                $image_path = 'http://localhost:8080/onlin%20store/a/trainer/uploads/' . basename($row['picture']);
                            ?>
                                <img src="<?php echo $image_path; ?>" class="card-img-top" alt="Coach Image">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/150x150?text=No+Image" class="card-img-top" alt="No Image">
                            <?php endif; ?>

                            <div class="card-body text-center">
                                <h5 class="card-title text-success fw-bold"><?php echo htmlspecialchars($row['coach_name']); ?></h5>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($row['descr'])); ?></p>
                                <p><strong>Specialty:</strong> <?php echo htmlspecialchars($row['specialty']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                                <p><strong>Available Time:</strong>
                                    <?php echo htmlspecialchars($row['time_start']); ?> -
                                    <?php echo htmlspecialchars($row['time_end']); ?>
                                </p>

                                <!-- Book / Cancel Button -->
                                <form method="POST" action="">
                                    <input type="hidden" name="coach_id" value="<?php echo $row['id']; ?>">

                                    <?php if ($subscription === 'Silver'): ?>
                                        <button type="button" class="btn btn-secondary w-100 mt-2" disabled>You need to upgrade to Premium to book a coach</button>
                                    <?php elseif (isset($booking_status[$row['id']])): ?>
                                        <input type="hidden" name="action" value="cancel">
                                        <button type="submit" class="btn btn-danger w-100 mt-2">Cancel Booking</button>
                                    <?php else: ?>
                                        <input type="hidden" name="action" value="book">
                                        <?php if (count($booking_status) > 0): ?>
                                            <button type="button" class="btn btn-secondary w-100 mt-2" disabled>Booking Already Made</button>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-primary w-100 mt-2">Book This Coach</button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">No coaches found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

<?php $conn->close(); ?>
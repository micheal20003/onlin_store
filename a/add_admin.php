<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_admin'])) {
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Optional: hash the password for security
    // $password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admin (email, password, username) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $email, $password, $username);
        if ($stmt->execute()) {
            $success = "Admin added successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class='container mt-4'>
    <h2 class="text-center mb-4" style="font-weight: bold;">Admin Management</h2>

    <?php if ($success): ?>
        <div class="alert alert-success text-center"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Add Admin Form -->
    <form method='POST' class="border p-4 mb-4 rounded">
        <h5>Add Admin</h5>
        <div class='mb-3'>
            <label class='form-label'>Admin Username:</label>
            <input type='text' name='username' class='form-control' required>
        </div>
        <div class='mb-3'>
            <label class='form-label'>Email:</label>
            <input type='email' name='email' class='form-control' required>
        </div>
        <div class='mb-3'>
            <label class='form-label'>Password:</label>
            <input type='password' name='password' class='form-control' required>
        </div>
        <button type='submit' name='add_admin' class='btn btn-success'>Add Admin</button>
    </form>
</div>

</body>
</html>

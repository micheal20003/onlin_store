<?php
if (!isset($_SESSION["user_id"])) {
    header("Location: /onlin store/a/login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION["user_id"];
$query = "SELECT * FROM admin WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);



if (isset($_POST["change_password"])) {
    $old = $_POST["old_password"];
    $new = $_POST["new_password"];

    if ($old == $admin['password']) {
        $update_pass = "UPDATE admin SET password='$new' WHERE id=$user_id";
        if (mysqli_query($conn, $update_pass)) {
            $success = "Password changed successfully.";
        } else {
            $error = "Error updating password.";
        }
    } else {
        $error = "Old password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
    <style>
        /* Same styles as before */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .profile-header {
            background-color: #343a40;
            color: white;
            padding: 20px;
            border-radius: 5px 5px 0 0;
        }

        .profile-info {
            background-color: white;
            padding: 20px;
            border-radius: 0 0 5px 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .info-box {
            margin-bottom: 15px;
        }

        .form-box {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-box h4 {
            color: #343a40;
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #343a40;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #23272b;
        }

        .success {
            color: #28a745;
            background-color: #d4edda;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .error {
            color: #dc3545;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .info-label {
            font-weight: bold;
            color: #343a40;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="profile-header">
            <h2>Admin Profile</h2>
        </div>

        <?php if (isset($success)) echo "<div class='success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

        <div class="profile-info">
            <div class="info-box">
                <span class="info-label">Username:</span> <?= htmlspecialchars($admin['username']) ?>
            </div>
            <div class="info-box">
                <span class="info-label">Email:</span> <?= htmlspecialchars($admin['email']) ?>
            </div>
        </div>

        <div class="form-box">
            <h4>Change Password</h4>
            <form method="post">
                <input type="password" name="old_password" placeholder="Old Password" required />
                <input type="password" name="new_password" placeholder="New Password" required />
                <button type="submit" name="change_password">Change Password</button>
            </form>
        </div>
    </div>

</body>

</html>
<?php
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
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
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Update phone
if (isset($_POST["update_phone"])) {
    $new_phone = $_POST["phone"];
    $update = "UPDATE users SET phone = '$new_phone' WHERE id = $user_id";
    if (mysqli_query($conn, $update)) {
        $user['phone'] = $new_phone;
        $success = "Phone number updated successfully.";
    } else {
        $error = "Error updating phone.";
    }
}

// Change password
if (isset($_POST["change_password"])) {
    $old = $_POST["old_password"];
    $new = $_POST["new_password"];

    if ($old == $user['password']) {
        $update_pass = "UPDATE users SET password = '$new' WHERE id = $user_id";
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
    <title>User Profile</title>
    <style>
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
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .info-box {
            margin-bottom: 15px;
        }
        .form-box {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .form-box h4 {
            color: #343a40;
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        input[type="text"],
        input[type="password"] {
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
        <h2>Your Profile</h2>
    </div>
    
    <?php if (isset($success)) echo "<div class='success'>$success</div>"; ?>
    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    
    <div class="profile-info">
        <div class="info-box">
            <span class="info-label">Name:</span> <?= htmlspecialchars($user['name']) ?>
        </div>
        <div class="info-box">
            <span class="info-label">Email:</span> <?= htmlspecialchars($user['email']) ?>
        </div>
        <div class="info-box">
            <span class="info-label">Phone:</span> <?= htmlspecialchars($user['phone']) ?>
        </div>
        <div class="info-box">
            <span class="info-label">Gender:</span> <?= htmlspecialchars($user['gender']) ?>
        </div>
        <div class="info-box">
            <span class="info-label">Subscription:</span> <?= htmlspecialchars($user['subscription']) ?>
        </div>
<?php

$conn = new mysqli($servername, $username, $password, $dbname);
$user_id = $_SESSION['user_id'];
$subRes = $conn->query("SELECT end_date FROM subscriptions WHERE user_id = $user_id");
$re =  mysqli_fetch_assoc($subRes,);
?>
        <div class="info-box">
            <span class="info-label">Subscription end date:</span> <?= htmlspecialchars($re['end_date'] ?? 'No active subscription') ?>
        </div>
    </div>
    


    <div class="form-box">
        <h4>Update Phone</h4>
        <form method="post">
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required />
            <button type="submit" name="update_phone">Update Phone</button>
        </form>
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
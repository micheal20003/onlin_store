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
$query = "SELECT * FROM private_coach WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$coach = mysqli_fetch_assoc($result);

if (isset($_POST["update_profile"])) {
    $specialty = $_POST["specialty"];
    $phone = $_POST["phone"];
    $descr = $_POST["descr"];
    $time_start = $_POST["time_start"];
    $time_end = $_POST["time_end"];

    if ($_FILES["picture"]["error"] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        $picture_name = time() . "_" . basename($_FILES["picture"]["name"]);
        $target_file = $target_dir . $picture_name;
        move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file);
    } else {
        $target_file = $coach['picture'];
    }

    $update = "UPDATE private_coach 
               SET specialty='$specialty', phone='$phone', descr='$descr', 
                   time_start='$time_start', time_end='$time_end', picture='$target_file'
               WHERE id=$user_id";

    if (mysqli_query($conn, $update)) {
        $success = "Profile updated successfully.";
        $result = mysqli_query($conn, $query);
        $coach = mysqli_fetch_assoc($result);
    } else {
        $error = "Error updating profile.";
    }
}

if (isset($_POST["change_password"])) {
    $old = $_POST["old_password"];
    $new = $_POST["new_password"];

    if ($old == $coach['password']) {
        $update_pass = "UPDATE private_coach SET password='$new' WHERE id=$user_id";
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
    <title>Coach Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        .profile-header {
            background-color: #343a40;
            color: white;
            padding: 20px;
            border-radius: 5px 5px 0 0;
            margin-bottom: 20px;
        }

        .profile-info,
        .form-box {
            background-color: white;
            padding: 20px;
            border-radius: 0 0 5px 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-box {
            border-radius: 5px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
        }

        input[type="text"],
        input[type="password"],
        input[type="tel"],
        input[type="time"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            margin-top: 15px;
            background-color: #343a40;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
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

        img {
            max-width: 150px;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="profile-header">
            <h2>Coach Profile</h2>
        </div>

        <?php if (isset($success)) echo "<div class='success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

        <div class="profile-info">
            <p><strong>Name:</strong> <?= htmlspecialchars($coach['coach_name']) ?></p>
            <p><strong>Specialty:</strong> <?= htmlspecialchars($coach['specialty']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($coach['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($coach['phone']) ?></p>
            <p><strong>Available:</strong> <?= htmlspecialchars($coach['time_start']) ?> to <?= htmlspecialchars($coach['time_end']) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($coach['descr']) ?></p>
            <?php if (!empty($coach['picture'])): ?>
                <p><strong>Picture:</strong><br><img src="<?= $coach['picture'] ?>"></p>
            <?php endif; ?>
        </div>

        <div class="form-box">
            <h4>Update Profile</h4>
            <form method="post" enctype="multipart/form-data">
                <label for="specialty">Specialty</label>
                <input type="text" id="specialty" name="specialty" value="<?= htmlspecialchars($coach['specialty']) ?>" required>

                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($coach['phone']) ?>" required>

                <label for="descr">Description</label>
                <textarea id="descr" name="descr"><?= htmlspecialchars($coach['descr']) ?></textarea>

                <label for="time_start">Start Time</label>
                <input type="time" id="time_start" name="time_start" value="<?= htmlspecialchars($coach['time_start']) ?>">

                <label for="time_end">End Time</label>
                <input type="time" id="time_end" name="time_end" value="<?= htmlspecialchars($coach['time_end']) ?>">

                <label for="picture">Picture</label>
                <input type="file" id="picture" name="picture" accept="image/*">

                <button type="submit" name="update_profile">Update Profile</button>
            </form>
        </div>

        <div class="form-box">
            <h4>Change Password</h4>
            <form method="post">
                <label for="old_password">Old Password</label>
                <input type="password" id="old_password" name="old_password" required>

                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>

                <button type="submit" name="change_password">Change Password</button>
            </form>
        </div>
    </div>
</body>

</html>
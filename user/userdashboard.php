<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: /onlin store/login/login.php");
    exit();
}

$name = $_SESSION["name"];
?>

<!DOCTYPE html>
<html>

<head>
    <title>User Dashboard</title>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
    <style>
        body {
            display: flex;
            font-family: Arial;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #343a40;
            color: white;
            padding: 20px;
        }

        .sidebar a {
            color: white;
            display: block;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            background: #f8f9fa;
        }

        .topbar {
            background: #fff;
            padding: 10px 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: flex-end;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h4>User Panel</h4>
        <a href='?section=workout'>Workout Plan</a>
        <a href='?section=calories'>Calorie Calculator</a>
        <a href='?section=bookings'>My Bookings</a>
        <a href='?section=profile'>Profile</a>
        <a href='?section=subscription'>subscription</a>
        <a href='?section=Nutrition Plan'>Nutrition Plan</a>
        <a href='logout.php' class='text-danger'>Logout</a>
    </div>
    <div class="content">
        <div class="topbar">
            <div class="topbar-content">
                Welcome, <strong><?= htmlspecialchars($name) ?></strong>
            </div>
        </div>

        <?php
        $section = $_GET['section'] ?? 'workout';

        if ($section == 'workout') {
            include 'workout_plan.html';
        } elseif ($section == 'calories') {
            include 'calorie_calculator.html';
        } elseif ($section == 'bookings') {
            include 'my_bookings.php';
        } elseif ($section == 'profile') {
            include 'profile.php';
        } elseif ($section == 'subscription') {
            include 'subscription.php';
        } elseif ($section == 'Nutrition Plan') {
            include 'Nutrition Plan.php';
        } else {
            echo "<h2>Welcome to your Dashboard!</h2>";
        }
        ?>
    </div>
</body>

</html>
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: /onlin store/a/login.php");
    exit();
}

$name = $_SESSION["name"];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
    <style>
        body {
            display: flex;
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
        }

        .sidebar a:hover {
            background: #495057;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
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
    <div class='sidebar'>
        <h4>Admin Panel</h4>
        <a href='?section=store'>Store Management</a>
        <a href='?section=users'>User Management</a>
        <a href='?section=trainers'>Trainer Management</a>
        <a href='?section=admin'>Admin Management</a>
        <a href='?section=profile'>profile</a>
        <a href='?section=orders'>Orders</a>
        <a href='logout.php' class='text-danger'>Logout</a>
    </div>
    <div class="content">
        <div class="topbar">
            <div class="topbar-content">
                Welcome, <strong><?= htmlspecialchars($name) ?></strong>
            </div>
        </div>
        <?php
        $section = $_GET['section'] ?? 'store';

        if ($section == 'store') {
            include 'store_management.php';
        } elseif ($section == 'users') {
            include 'user_management.php';
        } elseif ($section == 'trainers') {
            include 'trainers_management.php';
        } elseif ($section == 'admin') {
            include 'add_admin.php';
        } elseif ($section == 'profile') {
            include 'profile.php';
        } elseif ($section == 'orders') {
            include 'orders.php';
        } else {
            echo "<h2>Welcome to the Admin Dashboard</h2>";
        }
        ?>
    </div>
</body>

</html>
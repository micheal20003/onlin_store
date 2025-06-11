<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Check admin table
    $stmt = $conn->prepare("SELECT id, username FROM admin WHERE email = ? AND password = ?");
    if ($stmt === false) {
        die("SQL prepare failed (admin): " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $username);
        $stmt->fetch();

        $_SESSION["user_id"] = $user_id;
        $_SESSION["name"] = $username;
        $_SESSION["role"] = $role;

        header("Location: /onlin store/a/admin.php");
        exit();
    } else {
        // Check private_coach table
        $stmt = $conn->prepare("SELECT id, coach_name FROM private_coach WHERE email = ? AND password = ?");
        if ($stmt === false) {
            die("SQL prepare failed (trainer): " . $conn->error);
        }

        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id, $coach_name);
            $stmt->fetch();

            $_SESSION["user_id"] = $user_id;
            $_SESSION["name"] = $coach_name;
            $_SESSION["role"] =  $role;

            header("Location: /onlin store/a/trainer/trainerdashboard.php");
            exit();
        } else {
            $login_error = "Invalid email or password.";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fitness App</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            padding: 50px;
            margin: 0;
        }

        .container {
            background: #fff;
            padding: 40px;
            width: 400px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #007bff;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input {
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            background: #007bff;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #0056b3;
        }

        .error {
            color: #dc3545;
            text-align: center;
            margin-bottom: 15px;
        }

        p {
            text-align: center;
            margin-top: 20px;
        }

        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Welcome Back!</h2>
        <?php if (!empty($login_error)) echo "<p class='error'>$login_error</p>"; ?>
        <form method="post">
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit" name="login">Login</button>
        </form>
    </div>

</body>

</html>
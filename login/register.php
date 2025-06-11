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

$register_error = $register_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $phone    = trim($_POST["phone"]);
    $gender   = $_POST["gender"];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $register_error = "Email is already registered.";
    } else {
        $insert = $conn->prepare("INSERT INTO users (name, email, password, phone, gender) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("sssss", $name, $email, $password, $phone, $gender);

        if ($insert->execute()) {
            $register_success = "Registration successful. Please <a href='login.php'>login</a>.";
        } else {
            $register_error = "Error during registration.";
        }
        $insert->close();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Fitness App</title>
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
        input, select {
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
        .success {
            color: #28a745;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Create Your Fitness Account</h2>
    <?php if (!empty($register_error)) echo "<p class='error'>$register_error</p>"; ?>
    <?php if (!empty($register_success)) echo "<p class='success'>$register_success</p>"; ?>
    <form method="post">
        <input type="text" name="name" placeholder="Full Name" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="text" name="phone" placeholder="Phone Number" required />
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>
        <button type="submit" name="register">Sign Up</button>
    </form>
</div>

</body>
</html>
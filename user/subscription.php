<?php
$plans = [
  "Starter" => 9,
  "Basic" => 20,
  "Pro" => 45,
  "Unlimited" => 100
];
$durations = [
  "Starter" => 30,
  "Basic" => 90,
  "Pro" => 180,
  "Unlimited" => 365
];

$selectedPlan = $_POST['plan'] ?? null;
if (isset($_POST['cancel_payment'])) {
  $selectedPlan = null;
}

$conn = new mysqli("localhost", "root", "", "fitness_app");
if ($conn->connect_error) {
  die("DB Connection Failed: " . $conn->connect_error);
}


$user_id = $_SESSION["user_id"] ?? 0;
$isSubscribed = false;
$subEndDate = null;

$subRes = $conn->query("SELECT end_date FROM subscriptions WHERE user_id = $user_id ORDER BY id DESC LIMIT 1");
if ($subRes && $row = $subRes->fetch_assoc()) {
  $subEndDate = $row['end_date'];
  $today = date('Y-m-d');
  if ($subEndDate && $subEndDate > $today) {
    $isSubscribed = true;
  }
}

if (isset($_POST['confirm_payment'])) {
  $plan = $_POST['plan'];
  $price = $_POST['price'];
  $months = $_POST['months'];
  $start_date = date('Y-m-d');
  $end_date = ($plan === 'Unlimited') ? date('Y-m-d', strtotime('+1 year')) : date('Y-m-d', strtotime("+$months months"));

  $start = new DateTime($start_date);
  $end = new DateTime($end_date);
  $interval = $start->diff($end);
  $days = $interval->days;

  $updateQuery = "UPDATE users SET subscription='Premium' WHERE id=$user_id";
  if (!$conn->query($updateQuery)) {
    die("Failed to update user subscription: " . $conn->error);
  }

  $stmt = $conn->prepare("INSERT INTO subscriptions (user_id, plan, start_date, end_date, remaining_days) VALUES (?, ?, ?, ?, ?)");
  if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
  }
  $stmt->bind_param("isssi", $user_id, $plan, $start_date, $end_date, $days);
  if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
  }

  echo "<script>alert('Payment successful. Welcome to Premium!'); location.href='userdashboard.php';</script>";
  exit;
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Pricing Plan</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #fff;
    }

    h2,
    h3 {
      font-weight: bold;
      text-align: center;
    }

    .pricing-container {
      margin-top: 40px;
    }

    .row {
      display: flex;
      flex-wrap: wrap;
    }

    .col-sm-6.col-md-3 {
      display: flex;
      margin-bottom: 20px;
    }

    .pricing-box {
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 8px;
      text-align: center;
      padding: 30px 15px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      flex-grow: 1;
      width: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    /* Hover effect for zoom */
    .pricing-box:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .pricing-box.pro {
      border: 2px solid black;
    }

    .pricing-box h3 {
      font-size: 24px;
      margin-bottom: 10px;
    }

    .pricing-box .best-offer {
      color: #ff0055;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .pricing-box .price {
      font-size: 28px;
      font-weight: bold;
    }

    .pricing-box p {
      font-size: 14px;
      color: #666;
    }

    .features {
      list-style: none;
      padding: 0;
      margin-top: 15px;
    }

    .features li {
      padding: 5px 0;
    }

    .features li b {
      font-weight: bold;
    }

    .btn-select {
      margin-top: 20px;
      background: #ff3c3c;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 10px 18px;
    }

    .sub-info {
      text-align: center;
      margin: 20px 0;
    }

    /* ... (Keep your payment-form styles unchanged) ... */


    .payment-form {
      background: #fff;
      max-width: 400px;
      margin: 50px auto;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
      text-align: center;
      position: relative;
    }

    .payment-form input {
      width: 100%;
      margin-bottom: 20px;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .payment-form button.confirm {
      background-color: #d6008c;
      color: white;
      font-weight: bold;
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
    }

    .close-button {
      position: absolute;
      right: 10px;
      top: 10px;
      font-size: 24px;
      border: none;
      background: none;
      color: #e60023;
      cursor: pointer;
    }

    label {
      display: inline-block;
      max-width: 100%;
      margin-bottom: 10px;
      font-weight: 700;
    }
  </style>
</head>

<body>

  <?php if (!$selectedPlan): ?>
    <h2>Pricing Plan</h2>
    <p class="text-center">Enjoy your workout with all our features unlocked and the best trainers all at once with our best pricing list.</p>
    <div class="sub-info">
      <strong>Subscription end date:</strong> <?= htmlspecialchars($subEndDate ?? 'No active subscription') ?>
    </div>

    <div class="container pricing-container">
      <div class="row">
        <?php foreach ($plans as $name => $price): ?>
          <div class="col-sm-6 col-md-3">
            <div class="pricing-box <?= $name == 'Pro' ? 'pro' : '' ?>">
              <h3><?= $name ?></h3>
              <?php if ($name == 'Pro'): ?><div class="best-offer">Best Offer</div><?php endif; ?>
              <div class="price">$<?= $price ?>/<?= $name == 'Unlimited' ? 'year' : ($durations[$name] / 30) . ' month' ?></div>
              <p>
                <?php
                if ($name == 'Starter') echo "Once you try our service you will always want more";
                elseif ($name == 'Basic') echo "3 months of commitment will leave your body healthy and strong";
                elseif ($name == 'Pro') echo "This will make you strong flexable back at the younger version of yourself";
                else echo "Explore yourself and reach the better version of yourself and dive deep into your true self";
                ?>
              </p>
              <ul class="features">
                <li>15 Cardio Classes</li>
                <li><b>10 Swimming Lesson</b></li>
                <li>10 Yoga Classes</li>
                <li><b>20 Aerobics</b></li>
                <li>10 Zumba Classes</li>
                <li><b>5 Massage</b></li>
                <li>10 Body Building</li>
              </ul>
              <form method="post">
                <input type="hidden" name="plan" value="<?= $name ?>">
                <button type="submit" class="btn btn-select" <?= $isSubscribed ? 'disabled' : '' ?>>
                  <?= $isSubscribed ? 'Already Subscribed' : 'SELECT PLAN' ?>
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php else: ?>
    <div class="payment-form">
      <form method="post">
        <button type="submit" name="cancel_payment" class="close-button">&times;</button>
      </form>
      <img src="icons/logo.png" alt="Logo" class="logo">
      <h3>Payment Information</h3>
      <form method="post" onsubmit="return validateExpiryDate(document.getElementById('expiry'));">
        <input type="hidden" name="plan" value="<?= $selectedPlan ?>">
        <input type="hidden" name="price" value="<?= $plans[$selectedPlan] ?>">
        <input type="hidden" name="months" value="<?= $durations[$selectedPlan] / 30 ?>">

        <label for="fullname">Full Name</label>
        <input type="text" name="fullname" id="fullname" required>

        <label for="card">Card Number</label>
        <input type="text" name="card" id="card" maxlength="16" placeholder="1234 1234 1234 1234" required oninput="this.value = this.value.replace(/\D/g, '')">

        <div style="display: flex; gap: 10px;">
          <div style="flex:1;">
            <label for="expiry">Expiration Date</label>
            <input type="text" name="expiry" id="expiry" placeholder="MM/YYYY" maxlength="7" required oninput="formatExpiry(this)">
          </div>
          <div style="flex:1;">
            <label for="cvv">CVV</label>
            <input type="text" name="cvv" id="cvv" maxlength="4" placeholder="123" required oninput="this.value = this.value.replace(/\D/g, '')">
          </div>
        </div>
        <span id="expiry-error" style="color:red; display:none;"></span>

        <button type="submit" name="confirm_payment" class="confirm">Confirm Payment</button>
        <p class="note">Verify the information is correct</p>
      </form>
    </div>
  <?php endif; ?>
  </div>

  <script>
    function formatExpiry(input) {
      let value = input.value.replace(/\D/g, '');
      if (value.length >= 2) {
        let mm = parseInt(value.slice(0, 2));
        if (mm > 12) mm = 12;
        let formatted = mm.toString().padStart(2, '0');
        if (value.length > 2) {
          formatted += '/' + value.slice(2, 6);
        }
        input.value = formatted;
      } else {
        input.value = value;
      }
    }

    function validateExpiryDate(input) {
      const error = document.getElementById('expiry-error');
      const value = input.value.trim();
      const regex = /^(0[1-9]|1[0-2])\/\d{4}$/;

      if (!regex.test(value)) {
        error.textContent = "Invalid format. Use MM/YYYY.";
        error.style.display = 'block';
        return false;
      }

      const [month, year] = value.split('/').map(Number);
      const now = new Date();
      const currentMonth = now.getMonth() + 1;
      const currentYear = now.getFullYear();

      if (year > currentYear || (year === currentYear && month > currentMonth)) {
        error.style.display = 'none';
        return true;
      } else {
        error.textContent = "Card has expired.";
        error.style.display = 'block';
        return false;
      }
    }
  </script>

</body>

</html>
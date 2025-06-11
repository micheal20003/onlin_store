<?php
// nutrition_plan.php

// === Database connection ===
// Adjust these to your own DB credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch foods from database
$sql = "SELECT * FROM foods";
$result = $conn->query($sql);

$foods = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $foods[] = [
      'id' => $row['id'],
      'name' => $row['name'],
      'protein' => (float)$row['protein'],
      'carbohydrates' => (float)$row['carbohydrates'],
      'fats' => (float)$row['fats'],
      'fiber' => (float)$row['fiber'],
      'calories' => (float)$row['calories'],
      'quantity' => $row['quantity'],
      'suitable_for_diabetes' => (bool)$row['suitable_for_diabetes'],
    ];
  }
}

$conn->close();
?>
<!-- Nutrition Plan Module -->
<div id="nutrition-plan-module" style="margin-left: 85px;padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px #ccc; max-width: 800px;">
  <h2>Customizable Nutrition Plan</h2>

  <div id="meals-container">
    <?php for ($i = 1; $i <= 3; $i++): ?>
      <div class="meal-section" data-meal="<?= $i ?>" style="margin-bottom: 30px;">
        <h3>Meal <?= $i ?></h3>
        <table class="meal-table" style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
          <thead>
            <tr>
              <th>Food Name</th>
              <th>Fiber</th>
              <th>Fats</th>
              <th>Carbohydrates</th>
              <th>Protein</th>
              <th>Calories</th>
              <th>Quantity</th>
              <th>Suitable for Diabetes</th>
              <th>Delete</th>
            </tr>
          </thead>
          <tbody></tbody>
          <!-- No individual totals footer -->
        </table>
        <button type="button" class="add-food-btn" style="padding: 8px 15px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
          Add Food
        </button>
        <div class="food-dropdown" style="display: none; margin-top: 8px;"></div>
      </div>
    <?php endfor; ?>
  </div>

  <!-- Combined Totals Bar -->
  <div id="combined-totals" style="border-top: 2px solid #444; padding-top: 12px; font-weight: bold; font-size: 16px; text-align: center;">
    Combined Totals:
    <span id="combined-fiber">0</span> Fiber /
    <span id="combined-fat">0</span> Fats /
    <span id="combined-carbs">0</span> Carbohydrates /
    <span id="combined-protein">0</span> Protein /
    <span id="combined-calories">0</span> Calories
  </div>
</div>

<style>
  /* Nutrition Module Table Styling */
  #nutrition-plan-module table,
  #nutrition-plan-module th,
  #nutrition-plan-module td {
    border: 1px solid #ddd;
    padding: 6px 10px;
    font-size: 14px;
    text-align: center;
  }

  #nutrition-plan-module th {
    background-color: #f4f4f4;
  }

  #nutrition-plan-module .food-dropdown select {
    width: 100%;
    padding: 6px;
    font-size: 14px;
    cursor: pointer;
  }

  #nutrition-plan-module .delete-btn {
    background-color: #dc3545;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  #nutrition-plan-module .delete-btn:hover {
    background-color: #c82333;
  }
</style>

<script>
  // Foods data injected by PHP
  const foods = <?= json_encode($foods, JSON_UNESCAPED_UNICODE); ?>;

  document.querySelectorAll('#nutrition-plan-module .add-food-btn').forEach(button => {
    button.addEventListener('click', () => {
      const mealSection = button.closest('.meal-section');
      const dropdown = mealSection.querySelector('.food-dropdown');
      dropdown.innerHTML = ''; // Clear previous dropdown
      dropdown.style.display = 'block';

      const select = document.createElement('select');
      select.size = 6;

      foods.forEach(food => {
        const option = document.createElement('option');
        option.value = food.id;
        option.textContent = `${food.name} ${food.suitable_for_diabetes ? '✅' : '❌'}`;
        select.appendChild(option);
      });

      select.onchange = () => {
        const selectedFood = foods.find(f => f.id == select.value);
        addFoodRow(mealSection, selectedFood);
        dropdown.style.display = 'none';
      };

      dropdown.appendChild(select);
    });
  });

  function addFoodRow(mealSection, food) {
    const tbody = mealSection.querySelector('tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${food.name}</td>
      <td>${food.fiber}</td>
      <td>${food.fats}</td>
      <td>${food.carbohydrates}</td>
      <td>${food.protein}</td>
      <td>${food.calories}</td>
      <td>${food.quantity}</td>
      <td>${food.suitable_for_diabetes ? '✅' : '❌'}</td>
      <td><button class="delete-btn" type="button">🗑️</button></td>
    `;

    // Attach delete handler
    row.querySelector('.delete-btn').addEventListener('click', e => {
      e.target.closest('tr').remove();
      updateCombinedTotals();
    });

    tbody.appendChild(row);
    updateCombinedTotals();
  }

  function updateCombinedTotals() {
    let fiber = 0,
      fats = 0,
      carbs = 0,
      protein = 0,
      calories = 0;

    document.querySelectorAll('#nutrition-plan-module tbody tr').forEach(row => {
      fiber += parseFloat(row.cells[1].textContent);
      fats += parseFloat(row.cells[2].textContent);
      carbs += parseFloat(row.cells[3].textContent);
      protein += parseFloat(row.cells[4].textContent);
      calories += parseFloat(row.cells[5].textContent);
    });

    document.getElementById('combined-fiber').textContent = fiber.toFixed(1);
    document.getElementById('combined-fat').textContent = fats.toFixed(1);
    document.getElementById('combined-carbs').textContent = carbs.toFixed(1);
    document.getElementById('combined-protein').textContent = protein.toFixed(1);
    document.getElementById('combined-calories').textContent = calories.toFixed(1);
  }
</script>
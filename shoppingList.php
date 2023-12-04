<?php
session_start();
require("0conn.php");

if (isset($_GET['meal_id'])) {
    $meal_id = $_GET['meal_id'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
    $stmt = $pdo->prepare("SELECT * FROM meals WHERE meal_id = ?");
    $stmt->execute([$meal_id]);
    $meal = $stmt->fetch(PDO::FETCH_ASSOC);

    $ingredientsStmt = $pdo->prepare("SELECT * FROM ingredients WHERE meal_id = ?");
    $ingredientsStmt->execute([$meal_id]);
    $ingredients = $ingredientsStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    header("Location: 9customer.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        h1 {
            font-size: 24px;
            margin: 0;
        }

        h2 {
            font-size: 20px;
            margin: 10px 0;
        }

        p {
            font-size: 16px;
            margin: 5px 0;
        }

        a {
            text-decoration: none;
            color: #007BFF;
            cursor: pointer;
        }

        .ingredient-list {
            list-style: none;
            padding: 0;
        }

        .ingredient-list li {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .ingredient-list input {
            margin-right: 10px;
        }

        .bought-ingredients {
            margin-top: 20px;
            color: green;
        }

        .bought-ingredients h3 {
            color: #007BFF;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Shopping List</h1>

        <h2>Meal Name: <?php echo $meal['meal_name']; ?></h2>

        <h3>Ingredients</h3>
        <ul class="ingredient-list">
            <?php foreach ($ingredients as $ingredient) { ?>
                <li>
                    <input type="checkbox" id="ingredient_<?php echo $ingredient['ingredient_id']; ?>">
                    <label for="ingredient_<?php echo $ingredient['ingredient_id']; ?>"><?php echo $ingredient['ingredient_name']; ?></label>
                </li>
            <?php } ?>
        </ul>

        <div class="bought-ingredients">
            <h3>Bought Ingredients</h3>
            <ul id="boughtIngredients"></ul>
        </div>

        <p><a href="9customer.php">Back to Categories</a></p>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            var boughtIngredientsList = document.getElementById('boughtIngredients');

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    if (this.checked) {
                        addToBoughtIngredients(this.nextElementSibling.textContent);
                        this.parentNode.remove(); // Remove the ingredient from the list
                    }
                });
            });

            function addToBoughtIngredients(ingredientName) {
                var listItem = document.createElement('li');
                listItem.textContent = ingredientName;
                boughtIngredientsList.appendChild(listItem);
            }
        });
    </script>
</body>
</html>

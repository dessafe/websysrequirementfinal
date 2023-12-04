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

    $instructionsStmt = $pdo->prepare("SELECT * FROM instructions WHERE meal_id = ? ORDER BY step_number");
    $instructionsStmt->execute([$meal_id]);
    $instructions = $instructionsStmt->fetchAll(PDO::FETCH_ASSOC);

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
</head>
<body>
    <div class="container">
        <h1>Edit Meal</h1>

        <form method="post" action="17processedit.php">
            <input type="hidden" name="meal_id" value="<?php echo $meal_id; ?>">
            <label for="meal_name">Meal Name:</label>
            <input type="text" name="meal_name" value="<?php echo $meal['meal_name']; ?>" required>
            
            <label for="video_link">Video Link:</label>
            <input type="text" name="video_link" value="<?php echo $meal['video_link']; ?>" required>
            
            <label for="image_link">Image Link:</label>
            <input type="text" name="image_link" value="<?php echo $meal['image_link']; ?>" required>

            <h3>Instructions</h3>
            <label for="all_steps">All Steps:</label>
            <textarea name="all_steps" rows="10"><?php
                // Output each step on a new line
                foreach ($instructions as $instruction) {
                    echo $instruction['step_description'] . "\n";
                }
            ?></textarea>

            <h3>Ingredients</h3>
            <label for="all_ingredients">All Ingredients:</label>
            <textarea name="all_ingredients" rows="10"><?php
                foreach ($ingredients as $ingredient) {
                    echo $ingredient['ingredient_name'] . "\n";
                }
            ?></textarea>

            <button type="submit" name="update_recipe">Update Recipe</button>
        </form>

        <p><a href="9customer.php">Back to Categories</a></p>
    </div>
</body>
</html>

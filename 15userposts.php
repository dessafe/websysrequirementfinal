
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_recipe'])) {
    $deleteStmt = $pdo->prepare("DELETE FROM meals WHERE meal_id = ?");
    $deleteStmt->execute([$meal_id]);
    header("Location: 9customer.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_recipe'])) {
    header("Location: 16editpost.php?meal_id=$meal_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
    <div class="container">
        <h1>Meal Details</h1>

        <h2>Meal Name: <?php echo $meal['meal_name']; ?></h2>
        <h3>Video</h3>
        <p>Video Link: <a href="<?php echo $meal['video_link']; ?>" target="_blank">Watch Video</a></p>
        <h3>Image</h3>
        <img src="<?php echo $meal['image_link']; ?>" alt="Recipe Image" style="max-width: 50%;">

        <h3>Instructions</h3>
        <ol>
            <?php
            foreach ($instructions as $instruction) {
                echo "<li>{$instruction['step_description']}</li>";
            }
            ?>
        </ol>

        <h3>Ingredients</h3>
        <ul>
            <?php
            foreach ($ingredients as $ingredient) {
                echo "<li>{$ingredient['ingredient_name']}</li>";
            }
            ?>
        </ul>


        <form method="post" action="">
            <button type="submit" name="edit_recipe">Edit</button>
            <button type="submit" name="delete_recipe" onclick="return confirm('Are you sure you want to delete this recipe?')">Delete</button>
        </form>

        <a href="shoppingList.php?meal_id=<?php echo $meal_id; ?>" class="shopping-list-btn">Shopping List</a>
        <p><a href="9customer.php">Back to Categories</a></p>
    </div>
</body>
</html>


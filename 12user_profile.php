<?php
session_start();
require("0conn.php");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if (!isset($_SESSION['username'])) {
    header("Location: 1login.php");
    exit();
}

$username = $_SESSION['username'];

$stmt = $pdo->prepare("SELECT * FROM meals WHERE username = ? ORDER BY date_created DESC");
$stmt->execute([$username]);
$userRecipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        
    </style>
</head>
<body>
    <div class="container">
        <h1>User Profile</h1>

        <h2>Add Recipe</h2>
        <p><a href="13add_recipe.php">Add a New Recipe</a></p>

        <ul>
            <?php foreach ($userRecipes as $recipe) { ?>
                <li>
                    <h2><?php echo $recipe['meal_name']; ?></h2>
                    <p>Category: <?php echo getCategoryName($pdo, $recipe['category_id']); ?></p>
                    <p>Video Link: <a href="<?php echo $recipe['video_link']; ?>" target="_blank">Watch Video</a></p>
                    <p>Image: <img src="<?php echo $recipe['image_link']; ?>" alt="Recipe Image" style="max-width: 50%;"></p>
                    <p>Date Created: <?php echo $recipe['date_created']; ?></p>
                    <p><a href="15userposts.php?meal_id=<?php echo $recipe['meal_id']; ?>">View Details</a></p>
                </li>
            <?php } ?>
        </ul>

        <h2>Back to Recipes</h2>
        <p><a href="9customer.php">Back to Recipes</a></p>
        <h2>Logout</h2>
        <p><a href="4logout.php">Logout</a></p>
    </div>
</body>
</html>

<?php
function getCategoryName($pdo, $category_id) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    return $category ? $category['category_name'] : 'Unknown';
}
?>

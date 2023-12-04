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

    $commentsStmt = $pdo->prepare("SELECT * FROM comments WHERE meal_id = ? ORDER BY created_at DESC");
    $commentsStmt->execute([$meal_id]);
    $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    header("Location: 9customer.php");
    exit();
}

// Check if the user is logged in
$userLoggedIn = isset($_SESSION['username']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userLoggedIn) {
    $comment_text = $_POST['comment'];
    $insertStmt = $pdo->prepare("INSERT INTO comments (meal_id, user_name, comment_text) VALUES (?, ?, ?)");
    $insertStmt->execute([$meal_id, $_SESSION['username'], $comment_text]);
    header("Location: 11meal_details_comments.php?meal_id=$meal_id");
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
        <?php if ($userLoggedIn): ?>
            <a href="shoppingList.php?meal_id=<?php echo $meal_id; ?>" class="shopping-list-btn">Shopping List</a>
        <?php else: ?>
            <p></p>
        <?php endif; ?>

        <h3>Comments</h3>
        <?php if (count($comments) > 0): ?>
            <ul>
                <?php foreach ($comments as $comment): ?>
                    <li>
                        <p><strong><?php echo $comment['user_name']; ?>:</strong> <?php echo $comment['comment_text']; ?><br>
                           <?php echo $comment['created_at']; ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No comments available.</p>
        <?php endif; ?>

        <?php if ($userLoggedIn): ?>
            <form method="post" action="">
                <div>
                    <label for="comment">Add a Comment:</label>
                    <textarea name="comment" id="comment" rows="3" required></textarea>
                </div>
                <button type="submit">Submit Comment</button>
            </form>
        <!-- padesign din -->
        <?php else: ?>
            <p>You need to <a href="1login.php">log in</a> to post comments.</p>
        <?php endif; ?>

        <p><a href="9customer.php">Back to Categories</a></p>
    </div>
</body>
</html>

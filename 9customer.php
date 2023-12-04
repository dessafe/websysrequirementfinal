<?php
session_start();
require("0conn.php");

$loggedInUsername = isset($_SESSION["username"]) ? $_SESSION["username"] : "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if (isset($_GET['search'])) {
    $searchTerm = '%' . $_GET['search'] . '%';
    $stmt = $pdo->prepare("SELECT * FROM meals WHERE meal_name LIKE :searchTerm OR meal_id IN (SELECT meal_id FROM ingredients WHERE ingredient_name LIKE :searchTerm)");
$stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
} else {
    $stmt = $pdo->query("SELECT * FROM meals ORDER BY date_created DESC");
}

$stmt->execute();

$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Recipes</title>
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

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        a {
            text-decoration: none;
            color: #007BFF;
            cursor: pointer;
        }

        form {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }

        button {
            padding: 10px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Customer Recipes</h1>

        <form action="" method="GET">
            <label for="search">Search Ingredients or Meal Name:</label>
            <input type="text" name="search" id="search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <button type="submit">Search</button>
        </form>

        <?php if (!empty($loggedInUsername)) { ?>
            <h2>Profile</h2>
            <p><a href="12user_profile.php">Profile</a></p>
        
        <!-- padesign ako netong Login to keneme -->
        <?php } else { ?>
            <p>Login to view your profile.</p>
        <?php } ?>

        <ul>
            <?php foreach ($recipes as $recipe) { ?>
                <li>
                    <h2><?php echo $recipe['meal_name']; ?></h2>
                    <p>Category: <?php echo getCategoryName($pdo, $recipe['category_id']); ?></p>
                    <p>Video Link: <a href="<?php echo $recipe['video_link']; ?>" target="_blank">Watch Video</a></p>
                    <p>Image: <img src="<?php echo $recipe['image_link']; ?>" alt="Recipe Image" style="max-width: 50%;"></p>
                    <p>Date Created: <?php echo $recipe['date_created']; ?></p>
                    <p><a href="11meal_details_comments.php?meal_id=<?php echo $recipe['meal_id']; ?>">View Details</a></p>
                </li>
            <?php } ?>
        </ul>

        <button onclick="window.location.href='14chat.php';">Open Chat</button>

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


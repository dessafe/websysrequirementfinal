<?php
session_start();
require("0conn.php");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Check if the category_id is provided in the query parameter
if (isset($_GET["category_id"])) {
    $category_id = $_GET["category_id"];

    // Retrieve category details from the database
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category) {
        $category_name = $category["category_name"];
    } else {
        // Category not found, handle accordingly (e.g., show an error message)
        $category_name = "Category Not Found";
    }
} else {
    // category_id not provided, handle accordingly (e.g., redirect or show an error message)
    $category_name = "Category Not Selected";
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Category Page</title>
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
            margin-top: 70px;
        }
        h2 {
            font-size: 20px;
            margin: 10px 0;
        }

        p {
            font-size: 16px;
            margin: 10px 0;
        }
        /* }
        .logo-container {
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 60px;
            padding: 10px;
            width: auto;
            margin-right: 10px;
        }

        .logo h1 {
            font-family: cursive;
            font-size: 24px;
            margin: 0;
            color: #16b978;
        } */
        h3{
            margin-top:90px;
        }
        
        .btn-secondary {
            background-color: #4caf50; /* Green background */
            color: #fff; /* White text */
        }
        .dashboard{
           margin-left: 1100px;
        align-items: center;
        justify-items: center;
       
           
        }
        .logo-container {
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
             justify-content: center; 
             align-items: center;
            background-color: #18392B; /* Green background */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 60px;
            padding: 20px;
            width: auto;
            margin-right: 10px;
        }

        .logo h1 {
            font-family: cursive;
            font-size: 24px;
            margin: 0;
            color: #fff; /* White text */
        }


        .category-details,
        .recipe-list {
            margin-top: 20px;
            font-size: 20px;
        }

        .mb-3{
            
        }
    </style>
</head>
<body>
<div class="logo-container">
        <div class="logo">
            <img src="logo.png" alt="Tastebud Logo">
            <h1>Tastebud</h1>
        </div>
        <div class="dashboard">
    <a href="5admin.php" class="btn btn-secondary">Admin Dashboard</a>
    </div>
    </div>
    <div class="container">

        <div class="category-details">
            <h2 class="mb-3">Category Details</h2>
            <p class="lead">Category Name: <?php echo $category_name; ?></p>
        </div>

        <div class="recipe-list">
            <h2 class="mb-3">Recipes</h2>
            
            <?php
           

           $stmt = $pdo->prepare("SELECT * FROM meals WHERE category_id = ?");
           $stmt->execute([$category_id]);
           $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
           if (count($recipes) > 0) {
               echo "<ul>";
               foreach ($recipes as $recipe) {
                   echo "<li><a href='7recipe_details.php?recipe_id={$recipe['meal_id']}'>{$recipe['meal_name']}</a></li>";
               }
               echo "</ul>";
           } else {
               echo "<p>No recipes found in this category.</p>";
           }
           ?>
           
            
        </div>
    </div>

    </div>
    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>

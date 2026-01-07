<?php
session_start();  // เริ่มต้น session

// ตรวจสอบว่า session ผู้ใช้ถูกตั้งค่าแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    // ถ้ายังไม่มี session ของผู้ใช้ แสดงข้อความและรีไดเรคไปยังหน้า login
    header("Location: login.php");
    exit;
}
// Directly establish database connection
$servername = "localhost";
$username = "std6630251067";  // Adjust username based on your database configuration
$password = "nZ!4pQrt";      // Adjust password based on your configuration
$dbname = "it_std6630251067";  // Replace with your actual database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set up default values for the search criteria
$searchQuery = "";
$priceMin = 0;
$priceMax = 1000000;  // Default high price for range filtering

// Set threshold for low stock alert
$lowStockThreshold = 10; // Number of items to trigger the low stock warning

// Check if a search query is submitted
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = mysqli_real_escape_string($conn, $_GET['search']);
}

// Set price range filter if specified
if (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
    $priceMin = intval($_GET['min_price']);
}
if (isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
    $priceMax = intval($_GET['max_price']);
}

// Construct base SQL query for price range
$query = "SELECT * FROM products WHERE product_price BETWEEN $priceMin AND $priceMax";

// Add search conditions if search query is provided
if (!empty($searchQuery)) {
    // Check if the search query is numeric, and treat it as a price search if it is
    if (is_numeric($searchQuery)) {
        $query .= " AND product_price LIKE '%$searchQuery%' OR product_id LIKE '%$searchQuery%'";
    } else {
        $query .= " AND (product_id LIKE '%$searchQuery%' OR 
                         product_name LIKE '%$searchQuery%' OR 
                         product_description LIKE '%$searchQuery%' OR 
                         product_color LIKE '%$searchQuery%' OR
                         product_size LIKE '%$searchQuery%')";
    }
}

// Fetch products from the database
$result = mysqli_query($conn, $query);

// Close the database connection when done
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIMBERLAND - Show Products</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        /* Add your custom styles here */
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #2F2F2F;
            color: #E8E8E8;
            text-align: center;
        }
        
        .header {
            position: relative;
            min-height: 100vh;  /* Ensure the header takes at least the full viewport height */
            background: url('https://assets.timberland.com/image/upload/c_limit,w_1920/q_auto:best,f_auto:image/v1738773245/020825-hp-hero-d-r2') repeat center center;
            background-size: auto;  /* Let the background repeat as it is */
            background-attachment: fixed;  /* Ensures the background stays fixed while scrolling */
        }

        .search-bar {
            display: flex;
            gap: 10px;
            margin-top: 4px;
            margin-right: 10px;
        }
        .search-bar input {
            margin-right: 10px;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
        }
        .search-bar button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #8D6E63;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: #795548;
        }

        .navbar {
            position: absolute;
            padding: 10px;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.5);
            color: #fff;
        }
        .navbar .title {
            font-size: 32px;
            font-weight: bold;
        }
        .navbar .menu {
            display: flex;
            gap: 15px;
        }
        .navbar .menu a {
            margin-right: 40px;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #8D6E63;
            transition: all 0.3s ease;
        }
        .navbar .menu a:hover {
            background-color: #795548;
        }


        .products {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 40px 20px 20px; /* Adjusted to give space below the navbar */
        }

        .product {
            position: relative; /* Make the product container relative so the edit button can be positioned inside */
            margin-top: 100px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            width: 250px;
            max-width: 100%;
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
        }

        .edit-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 8px 15px;
            background-color: #8D6E63;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .edit-btn:hover {
            background-color: #795548;
        }

        .product img {
            width: 100%;
            border-radius: 10px;
            height: 250px;
            object-fit: cover;
            margin-bottom: 15px; /* Space between image and text */
        }

        .product h4 {
            color: #D2691E;
            margin: 10px 0;
        }

        .product p {
            font-size: 14px;
            color: #E8E8E8;
            margin-bottom: 10px;
        }

        .product .price {
            color: #FFD700;
            font-weight: bold;
        }

        /* Styling the Product ID */
        .product .product-id {
            color: #FF6347;  /* Tomato color */
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .btn-lowstock {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 15px;
            background-color: #FF6347; /* Tomato color */
            color: white;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-lowstock:hover {
            background-color: #FF4500; /* Darker shade of tomato */
        }
    </style>
</head>
<body>

<div class="header">
    <div class="navbar">
        <div class="title">TIMBERLAND</div>
        <div class="menu">
            <div class="search-bar">
                <form action="show_product.php" method="GET">
                    <input type="text" name="search" placeholder="Search by ID, Name, Description, Color, Size..." value="<?php echo $searchQuery; ?>" />
                    <button type="submit">Search</button>
                </form>
            </div>
            <a href="home.php">Home</a>
            <a href="add_product.php">Add Product</a>
            <a href="show_product.php">Show Products</a>
            <a href="logout.php">logout</a>
        </div>
    </div>

    <div class="products">
        <?php
        if (mysqli_num_rows($result) > 0) {
            // Output data for each product
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="product">';
                // Edit Button
                echo '<a href="edit_product.php?product_id=' . $row['product_id'] . '" class="edit-btn">Edit</a>';
                // Product Image
                echo '<img src="image/' . $row['product_image'] . '" alt="Product Image">';
                // Product Details
                echo '<p class="product-id">Product ID: ' . $row['product_id'] . '</p>';
                echo '<h4>' . $row['product_name'] . '</h4>';
                echo '<p>' . $row['product_description'] . '</p>';
                echo '<p class="price">$' . $row['product_price'] . '</p>';
                echo '<p>Color: ' . $row['product_color'] . '</p>';
                echo '<p>Size: ' . $row['product_size'] . '</p>';
                echo '<p>Quantity: ' . $row['product_quantity'] . '</p>';

                // Check if the product_quantity field exists and is set
                if (isset($row['product_quantity']) && $row['product_quantity'] < $lowStockThreshold) {
                    echo '<p style="color: red; font-weight: bold;">Low stock alert: Only ' . $row['product_quantity'] . ' items left!</p>';
                   
                }

                echo '</div>';
            }
        } else {
            echo '<p>No products found.</p>';
        }
        ?>
    </div>
</div>
 <a href="lowstock.php" class="btn-lowstock">Check Low Stock</a>;
</body>
</html>

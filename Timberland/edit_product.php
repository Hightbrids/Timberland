<?php

session_start();  // เริ่มต้น session

// ตรวจสอบว่า session ผู้ใช้ถูกตั้งค่าแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    // ถ้ายังไม่มี session ของผู้ใช้ แสดงข้อความและรีไดเรคไปยังหน้า login
    header("Location: login.php");
    exit;
}
// Connection details
$servername = "localhost";
$username = "std6630251067";  
$password = "nZ!4pQrt";      
$dbname = "it_std6630251067";  

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if product_id is provided in the query string
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Fetch product details from the database
    $query = "SELECT * FROM products WHERE product_id = $product_id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    } else {
        echo "Product not found.";
        exit;
    }
} else {
    echo "No product ID provided.";
    exit;
}

// Update product details
// Update product details
// Update product details
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['delete'])) {  // Exclude delete action
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_description = mysqli_real_escape_string($conn, $_POST['product_description']);
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);
    $product_color = mysqli_real_escape_string($conn, $_POST['product_color']);
    $product_size = mysqli_real_escape_string($conn, $_POST['product_size']);
    $product_quantity = mysqli_real_escape_string($conn, $_POST['product_quantity']);
    $product_image = $product['product_image'];  // Default to existing image if no new image uploaded

    // Handle image upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        // Get image details
        $imageTmpName = $_FILES['product_image']['tmp_name'];
        $imageName = $_FILES['product_image']['name'];
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Check if the image is of a valid format
        if (in_array(strtolower($imageExtension), $allowedExtensions)) {
            // Define target directory for image upload
            $uploadDirectory = 'image/';
            $uploadPath = $uploadDirectory . $imageName;

            // Delete the old image from the server if it exists
            if (!empty($product['product_image'])) {
                $oldImagePath = $uploadDirectory . $product['product_image'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);  // Delete the old image
                }
            }

            // Move the uploaded image to the target directory
            if (move_uploaded_file($imageTmpName, $uploadPath)) {
                // If the image is successfully uploaded, use the original image name
                $product_image = $imageName;
            } else {
                echo "Failed to upload the image.";
                exit;
            }
        } else {
            echo "Invalid image format. Only jpg, jpeg, png, gif are allowed.";
            exit;
        }
    }

    // Ensure the fields are properly escaped and not empty
    $product_name = !empty($product_name) ? "'$product_name'" : "NULL";
    $product_description = !empty($product_description) ? "'$product_description'" : "NULL";
    $product_price = !empty($product_price) ? $product_price : "NULL";
    $product_color = !empty($product_color) ? "'$product_color'" : "NULL";
    $product_size = !empty($product_size) ? "'$product_size'" : "NULL";
    $product_quantity = !empty($product_quantity) ? "'$product_quantity'" : "NULL";
    $product_image = !empty($product_image) ? "'$product_image'" : "NULL";

    // Update the product in the database with new data, including the image path
    $update_query = "UPDATE products SET
        product_name = $product_name,
        product_description = $product_description,
        product_price = $product_price,
        product_color = $product_color,
        product_size = $product_size,
        product_quantity = $product_quantity,
        product_image = $product_image
        WHERE product_id = $product_id";

    // Execute update query
    if (mysqli_query($conn, $update_query)) {
        // Redirect to the same page with a success message in URL
        header("Location: show_product.php");
        exit;
    } else {
        echo "Error updating product: " . mysqli_error($conn);
    }
}

// Delete the product
// Delete the product
if (isset($_POST['delete'])) {
    // Ensure the product_id is valid before proceeding with deletion
    if (isset($product_id)) {
        // Fetch the product details including the image
        $fetch_query = "SELECT product_image FROM products WHERE product_id = $product_id";
        $fetch_result = mysqli_query($conn, $fetch_query);
        
        if (mysqli_num_rows($fetch_result) > 0) {
            $product = mysqli_fetch_assoc($fetch_result);
            $image_to_delete = $product['product_image'];

            // Check if an image exists and delete it from the server
            if (!empty($image_to_delete)) {
                $uploadDirectory = 'image/';
                $imagePath = $uploadDirectory . $image_to_delete;

                // Check if the image exists on the server and delete it
                if (file_exists($imagePath)) {
                    unlink($imagePath);  // Delete the image
                }
            }

            // Prepare the DELETE query
            $delete_query = "DELETE FROM products WHERE product_id = $product_id";

            // Execute the query to delete the product
            if (mysqli_query($conn, $delete_query)) {
                // Redirect to the product list page after successful deletion
                header("Location: show_product.php");
                exit;
            } else {
                echo "Error deleting product: " . mysqli_error($conn);
                exit;
            }
        } else {
            echo "Product not found in the database.";
            exit;
        }
    } else {
        echo "Product ID not provided.";
        exit;
    }
}



mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - TIMBERLAND</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #2F2F2F;
            color: #E8E8E8;
            text-align: center;
        }
        .header {
            position: relative;
            min-height: 100vh;
            background: url('https://assets.timberland.com/image/upload/c_limit,w_1920/q_auto:best,f_auto:image/v1738773245/020825-hp-hero-d-r2') repeat center center;
            background-size: auto;
            background-attachment: fixed;
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
        .edit-product-form {
            margin-top: 100px;
            display: inline-block;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            width: 400px;
            max-width: 100%;
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
            padding: 30px;
            margin-left: auto;
            margin-right: auto;
        }
        .edit-product-form label {
            display: block;
            margin-bottom: 10px;
            color: #E8E8E8;
        }
        .edit-product-form input, .edit-product-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 16px;
            border-radius: 5px;
            border: none;
            background-color: #333;
            color: #E8E8E8;
        }
        .edit-product-form button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #8D6E63;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .edit-product-form button:hover {
            background-color: #795548;
        }
        .edit-product-form textarea {
            resize: vertical;
            min-height: 100px;
        }
        .delete-btn {
            margin-top: 10px;
            background-color:rgb(255, 0, 0);
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #B71C1C;
        }
    </style>
    <script>
        // Confirmation for Update
        function confirmUpdate() {
            return confirm("Are you sure you want to update this product?");
        }

        // Confirmation for Delete
        function confirmDelete() {
            return confirm("Are you sure you want to delete this product?");
        }
    </script>
</head>
<body>

<div class="header">
    <div class="navbar">
        <div class="title">TIMBERLAND</div>
        <div class="menu">
            <a href="home.php">Home</a>
            <a href="add_product.php">Add Product</a>
            <a href="show_product.php">Show Products</a>
            <a href="logout.php">logout</a>
        </div>
    </div>

    <div class="edit-product-form">
        <h1>Edit Product</h1>
        <form action="edit_product.php?product_id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data" onsubmit="return confirmUpdate();">
            <label for="product_name">Product Name:</label>
            <input type="text" name="product_name" id="product_name" value="<?php echo $product['product_name']; ?>" required><br>

            <label for="product_description">Description:</label>
            <textarea name="product_description" id="product_description" required><?php echo $product['product_description']; ?></textarea><br>

            <label for="product_price">Price:</label>
            <input type="number" name="product_price" id="product_price" value="<?php echo $product['product_price']; ?>" required><br>

            <label for="product_color">Color:</label>
            <input type="text" name="product_color" id="product_color" value="<?php echo $product['product_color']; ?>" required><br>

            <label for="product_size">Size:</label>
            <input type="text" name="product_size" id="product_size" value="<?php echo $product['product_size']; ?>" required><br>

            <label for="product_quantity">quantity:</label>
            <input type="text" name="product_quantity" id="product_quantity" value="<?php echo $product['product_quantity']; ?>" required><br>

            <label for="product_image">Product Image:</label>
            <input type="file" name="product_image" id="product_image"><br>

            <button type="submit">Update Product</button>
        </form>

        <!-- Delete Button Form -->
        <form action="edit_product.php?product_id=<?php echo $product_id; ?>" method="POST" onsubmit="return confirmDelete();">
            <button type="submit" name="delete" class="delete-btn">Delete Product</button>
        </form>
    </div>
</div>

</body>
</html>


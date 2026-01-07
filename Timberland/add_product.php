<?php
session_start();  // เริ่มต้น session

// ตรวจสอบว่า session ผู้ใช้ถูกตั้งค่าแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    // ถ้ายังไม่มี session ของผู้ใช้ แสดงข้อความและรีไดเรคไปยังหน้า login
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIMBERLAND - Add Product</title>
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
            min-height: 100vh;  /* Ensure the header takes at least the full viewport height */
            background: url('https://assets.timberland.com/image/upload/c_limit,w_1920/q_auto:best,f_auto:image/v1738773245/020825-hp-hero-d-r2') repeat center center;
            background-size: auto;  /* Let the background repeat as it is */
            background-attachment: fixed;  /* Ensures the background stays fixed while scrolling */
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
        
        .form-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 10px;
            width: 50%;
            text-align: left;
        }
        
        .form-container h3 {
            color: #D2691E;
            font-size: 28px;
            margin-bottom: 20px;
        }
        
        .form-container input, .form-container textarea, .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #555;
            background-color: #333;
            color: #E8E8E8;
        }
        
        .form-container button {
            padding: 10px 20px;
            background-color: #D2691E;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            color: white;
            transition: background-color 0.3s;
        }
        
        .form-container button:hover {
            background-color: #8B4513;
        }
        
    </style>
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

    <div class="form-container">
        <h3>Add New Product</h3>
        <form action="connection.php" method="POST" enctype="multipart/form-data">
            <input type="number" name="product_id" placeholder="Product ID" required>
            <input type="text" name="product_name" placeholder="Product Name" required>
            <textarea name="product_description" placeholder="Product Description" required></textarea>
            <input type="file" name="product_image" accept="image/*" required>
            <input type="number" name="product_price" placeholder="Price in USD" required>
            <input type="text" name="product_color" placeholder="Product Color" required>
            <input type="number" name="product_size" placeholder="Product Size" required>
            <input type="number" name="product_quantity" placeholder="Product quantity" required>
            <button type="submit">Add Product</button>
        </form>
    </div>
</div>

</body>
</html>

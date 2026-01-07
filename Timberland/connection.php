<?php
// Define database connection parameters
$servername = "localhost";
$username = "std6630251067";  // Adjust username based on your database configuration
$password = "nZ!4pQrt";      // Adjust password based on your configuration
$dbname = "it_std6630251067";  // Replace with your actual database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare SQL statement (no need to include product_id because it's AUTO_INCREMENT now)
$stmt = $conn->prepare("INSERT INTO products (product_id, product_name, product_description, product_price, product_color, product_size, product_image, product_quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issdsisi",$product_id, $product_name, $product_description, $product_price, $product_color, $product_size, $product_image, $quantity);

// Check if the form is submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $product_id = (int) $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = (float) $_POST['product_price'];  // Ensure product_price is a float
    $product_color = $_POST['product_color'];
    $product_size = $_POST['product_size'];
    $quantity = (int) $_POST['product_quantity'];  // Ensure quantity is an integer

    // Handle file upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        // Define the directory for image uploads
        $target_dir = "image/"; // Directory for uploaded images
        $target_file = $target_dir . basename($_FILES["product_image"]["name"]);  // Full path with directory

        // Check if the file is an image
        $check = getimagesize($_FILES["product_image"]["tmp_name"]);
        if ($check !== false) {
            // Attempt to upload the file
            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                // Store only the image filename (e.g., "image.png" instead of "picture/image.png")
                $product_image = basename($_FILES["product_image"]["name"]);

                // Execute the prepared statement to insert data into the database
                if ($stmt->execute()) {
                    echo "Product added successfully!";
                } else {
                    echo "Error: " . $stmt->error;
                }

                // Close the prepared statement
                $stmt->close();
            } else {
                die("Error uploading image.");
            }
        } else {
            die("File is not an image.");
        }
    } else {
        die("No image file uploaded.");
    }
}

// Close the database connection
$conn->close();
?>

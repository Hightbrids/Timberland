<?php
session_start();  // เริ่มต้น session

// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "std6630251067";  // Adjust username based on your database configuration
$password = "nZ!4pQrt";      // Adjust password based on your configuration
$dbname = "it_std6630251067";  // Replace with your actual database name

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// การสมัครสมาชิก (Register)
if (isset($_POST['register'])) {
    // รับค่าจากฟอร์ม
    $reg_username = $_POST['reg_username'];
    $reg_email = $_POST['reg_email'];
    $reg_password = $_POST['reg_password'];

    // ป้องกัน SQL Injection
    $reg_username = $conn->real_escape_string($reg_username);
    $reg_email = $conn->real_escape_string($reg_email);
    $reg_password = $conn->real_escape_string($reg_password);

    // ตรวจสอบว่าอีเมลนี้มีผู้ใช้งานแล้วหรือไม่
    $sql_check_email = "SELECT * FROM users WHERE email = '$reg_email'";
    $result = $conn->query($sql_check_email);

    if ($result->num_rows > 0) {
        // ถ้าอีเมลนี้มีผู้ใช้งานแล้ว
        $_SESSION['error_message'] = "This email is already registered.";
    } else {
        // เข้ารหัสรหัสผ่าน
        $hashed_password = password_hash($reg_password, PASSWORD_DEFAULT);

        // สร้าง SQL Query สำหรับการสมัครสมาชิก
        $sql_register = "INSERT INTO users (username, email, password) VALUES ('$reg_username', '$reg_email', '$hashed_password')";

        // ตรวจสอบการสมัครสมาชิก
        if ($conn->query($sql_register) === TRUE) {
            // เก็บข้อความลงใน session เพื่อแสดงผลในหน้าหลังจากสมัคร
            $_SESSION['registration_success'] = "Registration successful! You can now log in.";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('https://assets.timberland.com/image/upload/c_limit,w_1920/q_auto:best,f_auto:image/v1738773245/020825-hp-hero-d-r2');
            background-size: cover;
            background-position: center;
            color: white;
        }

        .register-container {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 40px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }

        label {
            font-size: 14px;
            margin-bottom: 4px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 6px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            text-decoration: none;
            color: #007bff;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        .success-message {
            color: green;
            font-size: 16px;
            margin-top: 20px;
            text-align: center;
        }

        .password-guideline {
            font-size: 12px;
            color: #f1f1f1;
            margin-top: 8px;
        }
    </style>

    <script>
        // ฟังก์ชันตรวจสอบรูปแบบของชื่อผู้ใช้
        function validateUsername() {
            var username = document.getElementById("reg_username").value;
            var regex = /^[A-Za-z0-9!@#$%^&*()_+={}[\]:;"'<>?,./-]+$/;
            var errorMessage = document.getElementById("usernameError");

            if (!regex.test(username)) {
                errorMessage.textContent = "Username can only contain letters (A-Z, a-z), numbers (0-9), and special characters like !@#$%^&*()_+={}[]:;\"'<>?,./-";
                return false;
            } else {
                errorMessage.textContent = "";
            }
            return true;
        }

        // ฟังก์ชันตรวจสอบรหัสผ่าน
        function validatePassword() {
            var password = document.getElementById("reg_password").value;
            var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+={}[\]:;"'<>?,./-]).{8,}$/;
            var errorMessage = document.getElementById("passwordError");

            if (!regex.test(password)) {
                errorMessage.textContent = "Password must be at least 8 characters long and contain at least one lowercase letter, one uppercase letter, one number, and one special character.";
                return false;
            } else {
                errorMessage.textContent = "";
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <form method="POST" action="register.php" onsubmit="return validateUsername() && validatePassword()">
            <label for="reg_username">Username:</label><br>
            <input type="text" id="reg_username" name="reg_username" required><br><br>
            <div id="usernameError" class="error-message"></div><br>

            <label for="reg_email">Email:</label><br>
            <input type="email" id="reg_email" name="reg_email" required><br><br>

            <label for="reg_password">Password:</label><br>
            <input type="password" id="reg_password" name="reg_password" required><br><br>
            <div id="passwordError" class="error-message"></div><br>

            <!-- ข้อความแนะนำใต้ช่องรหัสผ่าน -->
            <div class="password-guideline">
                Password must contain at least:<br>
                - One lowercase letter (a-z)<br>
                - One uppercase letter (A-Z)<br>
                - One number (0-9)<br>
                - One special character (e.g. !@#$%^&*()_+={}[]:;\"'<>?,./-)<br>
                - Minimum length of 8 characters
            </div><br>

            <input type="submit" name="register" value="Register">
        </form>

        <!-- แสดงข้อความถ้าการสมัครสำเร็จหรือมีข้อผิดพลาด -->
        <?php
        if (isset($_SESSION['registration_success'])) {
            echo '<div class="success-message">' . $_SESSION['registration_success'] . '</div>';
            unset($_SESSION['registration_success']); // เคลียร์ข้อความหลังจากแสดงแล้ว
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']); // เคลียร์ข้อความหลังจากแสดงแล้ว
        }
        ?>

        <div class="login-link">
            <p>กลับไปหน้าล๊อคอิน <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>

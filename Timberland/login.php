<?php
session_start();  // เริ่มต้น session

// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "std6630251067";  // ปรับชื่อผู้ใช้ให้ตรงกับการตั้งค่า
$password = "nZ!4pQrt";      // ปรับรหัสผ่านให้ตรงกับการตั้งค่า
$dbname = "it_std6630251067";  // ปรับชื่อฐานข้อมูลให้ตรงกับการตั้งค่า

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตัวแปรสำหรับเก็บข้อความผิดพลาด
$error_message = ''; 

// การเข้าสู่ระบบ (Login)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // ตรวจสอบว่าอีเมลและรหัสผ่านถูกส่งมาหรือไม่
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // ป้องกัน SQL Injection ด้วย prepared statement
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email); // "s" หมายถึงประเภทข้อมูล string
        $stmt->execute();
        $result = $stmt->get_result();

        // ถ้าพบข้อมูลผู้ใช้
        if ($result->num_rows > 0) {
            // ดึงข้อมูลผู้ใช้
            $row = $result->fetch_assoc();
            
            // ตรวจสอบรหัสผ่านที่ผู้ใช้กรอกกับรหัสผ่านที่เก็บไว้ในฐานข้อมูล
            if (password_verify($password, $row['password'])) {
                // รหัสผ่านถูกต้อง

                // สร้าง session สำหรับผู้ใช้ที่เข้าสู่ระบบ
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];

                // รีไดเรกต์ไปยังหน้าอื่นหลังจากเข้าสู่ระบบสำเร็จ
                header("Location: home.php");
                exit;
            } else {
                // รหัสผ่านไม่ถูกต้อง
                $error_message = "Invalid email or password."; // กำหนดข้อความผิดพลาด
            }
        } else {
            // ไม่มีผู้ใช้นี้ในระบบ
            $error_message = "Invalid email or password."; // กำหนดข้อความผิดพลาด
        }

        // ปิด statement หลังใช้งาน
        $stmt->close();
    } else {
        $error_message = "Please fill in both fields."; // ถ้าผู้ใช้ไม่ได้กรอกข้อมูลทั้งสองช่อง
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
    <title>Login</title>
    <style>
        /* ทำให้ body ใช้ full height และจัดตำแหน่งโดยใช้ Flexbox */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh; /* ทำให้ใช้ความสูงเต็มจอ */
            display: flex;
            justify-content: center; /* จัดกลางแนวนอน */
            align-items: center; /* จัดกลางแนวตั้ง */
            background-image: url('https://assets.timberland.com/image/upload/c_limit,w_1920/q_auto:best,f_auto:image/v1738773245/020825-hp-hero-d-r2');
            background-size: cover;
            background-position: center;
            color: white;
        }

        /* สไตล์ของฟอร์ม */
        .login-container {
            background-color: rgba(0, 0, 0, 0.6); /* พื้นหลังโปร่งแสง */
            padding: 40px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px; /* กำหนดความกว้างสูงสุดของฟอร์ม */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }

        label {
            font-size: 14px;
            margin-bottom: 6px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
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

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            text-decoration: none;
            color: #007bff;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* เพิ่มสไตล์ให้กับข้อความผิดพลาด */
        .error-message {
            color: #ff0000; /* สีแดงสำหรับข้อความผิดพลาด */
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <!-- ฟอร์มสำหรับการเข้าสู่ระบบ -->
        <form method="POST" action="login.php">
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
            
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            
            <input type="submit" name="login" value="Login">
        </form>

        <!-- หากมีข้อความผิดพลาดจะแสดงตรงนี้ -->
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- ปุ่มสำหรับการสมัครสมาชิก (ลิงก์ไปหน้า register.php) -->
        <div class="register-link">
            <p>สมัครสมาชิก <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>

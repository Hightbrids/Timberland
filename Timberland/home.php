<?php
session_start();  // เริ่มต้น session

// ตรวจสอบว่า session ผู้ใช้ถูกตั้งค่าแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// รวม autoloader ของ Composer
require '../vendor/autoload.php';  // ใช้เส้นทางที่เหมาะสม

use GuzzleHttp\Client;

// API Key และ API Secret ของ Petfinder
$api_key = 'B8MNx7jiYoyL4zMKMivDYDMpEgISg0jOnO8NZZBOpuU215kAA0';  // เปลี่ยนเป็น API key ของคุณ
$api_secret = 'e9XdypEB9vPJ8i8LrfYCnWC6joAMMcODrQWepUWf';  // เปลี่ยนเป็น API secret ของคุณ

// ฟังก์ชันสำหรับดึงข้อมูลสัตว์แบบสุ่ม
function getRandomPet() {
    global $api_key, $api_secret;

    // สร้าง Client ใหม่
    $client = new Client();

    // URL ของ API ที่ให้ข้อมูลเกี่ยวกับสัตว์
    $petfinder_url = 'https://api.petfinder.com/v2/animals?limit=1';

    try {
        // ส่งคำขอ GET ไปยัง API เพื่อขอข้อมูลสัตว์
        $response = $client->request('GET', $petfinder_url, [
            'headers' => [
                'Authorization' => 'Bearer ' . getAccessToken($api_key, $api_secret)
            ]
        ]);
        
        // ดึงข้อมูล JSON จากคำขอตอบกลับ
        $pet_data = json_decode($response->getBody()->getContents(), true);

        // ตรวจสอบว่า API ตอบกลับข้อมูลหรือไม่
        if ($pet_data && isset($pet_data['animals'][0])) {
            // คืนค่าชื่อและประเภทของสัตว์
            $pet = $pet_data['animals'][0];
            // ตรวจสอบถ้ามีภาพของสัตว์
            $pet_image = isset($pet['photos'][0]['medium']) ? $pet['photos'][0]['medium'] : 'default_image.jpg'; // ใช้ภาพของสัตว์ หรือภาพเริ่มต้นถ้าไม่มี
            
            // ตรวจสอบว่ารูปภาพเข้าถึงได้หรือไม่
            if (!checkImageExists($pet_image)) {
                $pet_image = 'default_image.jpg'; // ถ้ารูปภาพไม่สามารถเข้าถึงได้ให้ใช้ภาพเริ่มต้น
            }

            return [
                "info" => "Pet: " . $pet['name'] . " | Type: " . $pet['species'] . " | Breed: " . $pet['breeds']['primary'],
                "image" => $pet_image
            ];
        } else {
            return [
                "info" => "Unable to fetch pet data.",
                "image" => 'default_image.jpg'
            ];
        }
    } catch (\Exception $e) {
        return [
            "info" => "Error fetching pet data: " . $e->getMessage(),
            "image" => 'default_image.jpg'
        ];
    }
}

// ฟังก์ชันสำหรับดึง Access Token จาก Petfinder API
function getAccessToken($api_key, $api_secret) {
    $client = new Client();
    $token_url = 'https://api.petfinder.com/v2/oauth2/token';

    try {
        // ส่งคำขอ POST เพื่อขอ access token
        $response = $client->request('POST', $token_url, [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => $api_key,
                'client_secret' => $api_secret
            ]
        ]);

        // ดึงข้อมูล JSON ของ access token
        $token_data = json_decode($response->getBody()->getContents(), true);
        return $token_data['access_token'];
    } catch (\Exception $e) {
        return null;
    }
}

// ฟังก์ชันตรวจสอบการเข้าถึงภาพจาก URL
function checkImageExists($url) {
    $headers = @get_headers($url);
    if ($headers && isset($headers[0]) && strpos($headers[0], '200') !== false) {
        return true;
    }
    return false;
}


// เมื่อผู้ใช้กดปุ่ม "Get Another Pet" ฟังก์ชันนี้จะถูกเรียกใหม่เพื่อดึงข้อมูลสัตว์ใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pet_info = getRandomPet();
} else {
    // ถ้าไม่ได้กดปุ่ม ให้แสดงข้อมูลแรกเมื่อโหลดหน้าเว็บ
    $pet_info = getRandomPet();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timberland Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            text-align: center;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
        position: relative;
        min-height: 100vh;  /* เปลี่ยนจาก 60vh เป็น 100vh */
        background: url('https://assets.timberland.com/image/upload/c_limit,w_1920/q_auto:best,f_auto:image/v1738773245/020825-hp-hero-d-r2') no-repeat center center;
        background-size: cover;  /* ให้รูปภาพขยายเต็มพื้นที่ */
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

        .shop-button {
            position: absolute;
            bottom: 20px;
            left: 20px;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            background-color: #8D6E63;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .shop-button:hover {
            background-color: #795548;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: translateY(-3px);
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        .dog-section {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 400px;
            font-size: 16px;
        }

        .dog-section img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 10px;
        }

        .reload-button {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #4CAF50;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .reload-button:hover {
            background-color: #45a049;
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
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <a href="show_product.php" class="shop-button">Shop All New TIMBERLAND</a>
</div>

<div class="content">
    <div class="dog-section">
        <h2>Random Pet Information</h2>
        <p><?php echo isset($pet_info['info']) ? $pet_info['info'] : 'Unable to fetch pet data.'; ?></p>
        <img src="<?php echo isset($pet_info['image']) ? $pet_info['image'] : 'default_image.jpg'; ?>" alt="Pet Image">
        <form method="POST">
            <button type="submit" class="reload-button">Get Another Pet</button>
        </form>
    </div>
</div>
</body>
</html>

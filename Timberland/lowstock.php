<?php

session_start();  // Start session

// Check if user session is set
if (!isset($_SESSION['user_id'])) {
    // If no user session, redirect to login page
    header("Location: login.php");
    exit;
}
$username = $_SESSION['username'];

// Database connection parameters
$servername = "localhost";
$username_db = "std6630251067";
$password_db = "nZ!4pQrt";
$dbname = "it_std6630251067";

// สร้างการเชื่อมต่อกับฐานข้อมูล
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รวมไลบรารีของ mPDF
require_once('vendor/autoload.php'); // ใช้ autoload ของ composer

// กำหนดที่เก็บไฟล์ชั่วคราวสำหรับ mPDF (โฟลเดอร์ที่สามารถเขียนได้)
$tempDir = __DIR__ . '/tmp';
if (!file_exists($tempDir)) {
    mkdir($tempDir, 0777, true);
}

// สร้างอ็อบเจ็กต์ mPDF พร้อมตั้งค่าฟอนต์
$mpdf = new \Mpdf\Mpdf([ 
    'tempDir' => $tempDir,
    'default_font' => 'sarabun',
    'fontDir' => array_merge((new Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'], [
        __DIR__ . '/fonts',
    ]),
    'fontdata' => array_merge((new Mpdf\Config\FontVariables())->getDefaults()['fontdata'], [
        'sarabun' => [
            'R' => 'THSarabunNew.ttf',
            'B' => 'THSarabunNew-Bold.ttf',
            'I' => 'THSarabunNew-Italic.ttf',
            'BI' => 'THSarabunNew-BoldItalic.ttf',
        ]
    ])
]);

// ดึงข้อมูลสินค้าที่ใกล้หมด (stock <= 11)
$sql = "SELECT * FROM products WHERE product_quantity < 10";
$result = $conn->query($sql);

// ตรวจสอบว่ามีสินค้าที่ใกล้หมดหรือไม่
if ($result->num_rows > 0) {
    // สร้างหน้า PDF ใหม่
    $mpdf->AddPage();
    $mpdf->SetTitle('Low Stock Products');
    $mpdf->SetFont('sarabun', '', 20); // ปรับขนาดฟอนต์ให้ใหญ่ขึ้น

    // เพิ่มหัวเรื่องใน PDF
    $mpdf->WriteHTML('<h1 style="text-align: center; font-size: 30px;">Low Stock Products Report</h1>');
    $mpdf->WriteHTML('<br>');

    // สร้างตารางใน PDF
    $html = '<table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center; font-size: 18px;">Product ID</th>
                        <th style="text-align: center; font-size: 18px;">Product Name</th>
                        <th style="text-align: center; font-size: 18px;">Stock Quantity</th>
                        <th style="text-align: center; font-size: 18px;">Price (฿)</th>
                    </tr>
                </thead>
                <tbody>';

    // วนลูปข้อมูลสินค้าและเพิ่มลงในตาราง
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td style="text-align: center; font-size: 16px;">' . $row['product_id'] . '</td>
                    <td style="font-size: 16px;">' . $row['product_name'] . '</td>
                    <td style="text-align: center; font-size: 16px;">' . $row['product_quantity'] . '</td>
                    <td style="text-align: center; font-size: 16px;">฿' . number_format($row['product_price'], 2) . '</td>
                </tr>';
    }

    $html .= '</tbody></table>';

    // เขียน HTML ลงใน PDF
    $mpdf->WriteHTML($html);

    // ส่งออก PDF
    $mpdf->Output('low_stock_products.pdf', 'I');  // ส่งออก PDF ไปยังเบราว์เซอร์
} else {
    echo "<p style='font-family: sarabun; font-size: 18px;'>ไม่มีสินค้าที่ใกล้หมดสต็อก</p>";
}

$conn->close();
?>
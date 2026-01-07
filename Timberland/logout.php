<?php
session_start();  // เริ่มต้น session

// ทำลาย session ทั้งหมด
session_destroy();

// รีไดเรคไปยังหน้า login
header("Location: login.php");
exit;
?>

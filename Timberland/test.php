<?php
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');  // เรียกใช้งาน TCPDF

$pdf = new TCPDF();
$pdf->AddPage();

// ใช้ฟอนต์ไทยที่แปลงแล้ว
$pdf->AddFont('THSarabun', '', 'thsarabun.php', true);
$pdf->SetFont('THSarabun', '', 14);  // ใช้ฟอนต์ THSarabun ขนาด 14

$pdf->Cell(0, 10, 'สวัสดีครับ นี่คือฟอนต์ภาษาไทยใน TCPDF', 0, 1, 'C');

$pdf->Output();
ini_set('display_errors', 1);
error_reporting(E_ALL);

?>

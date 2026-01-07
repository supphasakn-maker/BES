<?php
session_start();


include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

header('Content-Type: application/json');

try {

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('เกิดข้อผิดพลาดในการอัปโหลดไฟล์');
    }

    $uploadFile = $_FILES['image'];
    $recordId = $_POST['record_id'] ?? null;
    $isVerified = $_POST['is_verified'] ?? '0';


    if (!$recordId) {
        throw new Exception('ไม่พบ Record ID');
    }

    $recordId = intval($recordId);
    if ($recordId <= 0) {
        throw new Exception('Record ID ไม่ถูกต้อง');
    }


    $existingRecord = $dbc->GetRecord("bs_purchase_buyfix", "id", "id=" . $recordId);
    if (!$existingRecord) {
        throw new Exception('ไม่พบ Record ที่ต้องการอัปโหลด');
    }


    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024;

    if (!in_array($uploadFile['type'], $allowedTypes)) {
        throw new Exception('ประเภทไฟล์ไม่ถูกต้อง กรุณาใช้ JPG, PNG หรือ GIF');
    }

    if ($uploadFile['size'] > $maxSize) {
        throw new Exception('ขนาดไฟล์เกิน 5MB');
    }


    $uploadDir = '../../../binary/purchase/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }


    $fileExtension = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
    $newFileName = 'buyfix_' . $recordId . '_' . date('YmdHis') . '.' . $fileExtension;
    $uploadPath = $uploadDir . $newFileName;


    $fileNameForDB = $newFileName;


    if (!move_uploaded_file($uploadFile['tmp_name'], $uploadPath)) {
        throw new Exception('ไม่สามารถบันทึกไฟล์ได้');
    }


    $updateData = array(
        'img' => $fileNameForDB,
        'updated' => date('Y-m-d H:i:s')
    );


    $updateResult = $dbc->Update("bs_purchase_buyfix", $updateData, "id=" . $recordId);

    if (!$updateResult) {

        if (file_exists($uploadPath)) {
            unlink($uploadPath);
        }
        throw new Exception('ไม่สามารถอัปเดตฐานข้อมูลได้');
    }


    $message = 'อัปโหลดรูปภาพสำเร็จ';
    if ($isVerified === '1') {
        $message .= ' (ข้อมูลตรงกัน ✅)';
    } else {
        $message .= ' (ไม่ได้ตรวจสอบ ⚠️)';
    }


    $response = array(
        'success' => true,
        'message' => $message,
        'file_name' => $fileNameForDB,
        'file_path' => 'binary/purchase/' . $fileNameForDB,
        'verified' => $isVerified === '1'
    );

    echo json_encode($response);
} catch (Exception $e) {

    error_log("Upload Error: " . $e->getMessage());

    http_response_code(400);
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}

<?php
session_start();

include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);


$dbc = new datastore();
$dbc->Connect();

header('Content-Type: application/json');

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Invalid request method');
    }


    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('Record ID is required');
    }

    $record_id = (int)$_GET['id'];


    $record = $dbc->GetRecord("bs_purchase_buy", "*", "id=" . $record_id);

    if (!$record) {
        throw new Exception('Record not found');
    }


    $response = [
        'id' => (int)$record['id'],
        'amount' => $record['amount'],
        'date' => $record['date'],
        'method' => $record['method'],
        'img' => $record['img'],
        'image_path' => $record['img'],
        'has_image' => !empty($record['img']) && $record['img'] !== '*NULL*',
        'user' => $record['user'],
        'status' => $record['status'],
        'created' => $record['created'],
        'updated' => $record['updated']
    ];


    if ($response['has_image'] && !empty($response['img'])) {
        $response['image_exists'] = file_exists($response['img']);
    } else {
        $response['image_exists'] = false;
    }

    echo json_encode($response);
} catch (Exception $e) {

    error_log("Get Record Error: " . $e->getMessage());

    http_response_code(400);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}


$dbc->Close();

<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);


error_log("POST data: " . print_r($_POST, true));

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);


if (!isset($_POST['platform']) || !isset($_POST['price']) || $_POST['price'] == "") {
    $response = array(
        'success' => false,
        'msg' => 'Please input price!',
        'debug' => array(
            'post_data' => $_POST,
            'platform_isset' => isset($_POST['platform']),
            'price_isset' => isset($_POST['price']),
            'price_empty' => $_POST['price'] == ""
        )
    );
    error_log("Error response: " . json_encode($response));
    echo json_encode($response);
} else {
    $platform = $_POST['platform'];
    $price = $_POST['price'];


    error_log("Platform: $platform, Price: $price");


    $allowedPlatforms = ['Tiktok', 'Shopee', 'Lazada'];
    if (!in_array($platform, $allowedPlatforms)) {
        echo json_encode(array(
            'success' => false,
            'msg' => 'Invalid platform!',
            'debug' => array(
                'platform' => $platform,
                'allowed' => $allowedPlatforms
            )
        ));
    } else {
        try {

            $result = $os->save_variable($platform, $price);
            error_log("Save result: " . ($result ? 'true' : 'false'));


            if (isset($_SESSION['auth']['user_id'])) {
                $os->save_log(0, $_SESSION['auth']['user_id'], "change-market-price-" . strtolower($platform), $price, array(
                    'platform' => $platform,
                    'price' => $price,
                    'date' => date('Y-m-d H:i:s')
                ));
            }

            $updated_value = $os->load_variable($platform);
            error_log("Updated value from DB: $updated_value");

            echo json_encode(array(
                'success' => true,
                'platform' => $platform,
                'value' => $updated_value,
                'updated_time' => date('Y-m-d H:i:s'),
                'debug' => array(
                    'save_result' => $result,
                    'original_price' => $price,
                    'db_value' => $updated_value
                )
            ));
        } catch (Exception $e) {
            error_log("Exception: " . $e->getMessage());
            echo json_encode(array(
                'success' => false,
                'msg' => 'Database error: ' . $e->getMessage()
            ));
        }
    }
}

$dbc->Close();

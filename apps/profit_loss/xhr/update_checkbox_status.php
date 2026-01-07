<?php

error_reporting(E_ALL);
ini_set('display_errors', 0);


ob_start();

try {
    session_start();


    header('Content-Type: application/json');


    include_once "../../../config/define.php";
    include_once "../../../include/db.php";
    include_once "../../../include/session.php";

    $response = ['success' => false, 'message' => '', 'debug' => []];


    $report_date = $_POST['report_date'] ?? '';
    $is_checked = intval($_POST['is_checked'] ?? 0);
    $report_type = $_POST['report_type'] ?? 'daily';


    $response['debug']['received_data'] = [
        'report_date' => $report_date,
        'is_checked' => $is_checked,
        'report_type' => $report_type
    ];


    if (empty($report_date)) {
        throw new Exception('Missing report_date parameter');
    }


    if ($report_type === 'daily' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $report_date)) {
        throw new Exception('Invalid daily date format. Expected YYYY-MM-DD');
    }

    if ($report_type === 'monthly' && !preg_match('/^\d{4}-\d{2}$/', $report_date)) {
        throw new Exception('Invalid monthly date format. Expected YYYY-MM');
    }


    $dbc = new dbc;
    $connection_result = $dbc->Connect();

    if (!$connection_result) {
        throw new Exception('Database connection failed');
    }


    if ($report_type === 'daily') {
        $date_condition_usd = "DATE(mapped) = '$report_date'";
        $date_condition_thb = "DATE(mapped) = '$report_date'";
    } else {
        $date_condition_usd = "DATE_FORMAT(mapped, '%Y-%m') = '$report_date'";
        $date_condition_thb = "DATE_FORMAT(mapped, '%Y-%m') = '$report_date'";
    }


    $sql_usd = "UPDATE bs_mapping_profit_sumusd SET is_checked = $is_checked WHERE $date_condition_usd";
    $sql_thb = "UPDATE bs_mapping_profit SET is_checked = $is_checked WHERE $date_condition_thb";

    $response['debug']['queries'] = [$sql_usd, $sql_thb];


    $result_usd = $dbc->Query($sql_usd);
    $result_thb = $dbc->Query($sql_thb);

    if (!$result_usd) {
        throw new Exception('Failed to update bs_mapping_profit_sumusd table');
    }

    if (!$result_thb) {
        throw new Exception('Failed to update bs_mapping_profit table');
    }


    $response['success'] = true;
    $response['message'] = "Successfully updated checkbox status for $report_date";
    $response['date'] = $report_date;
    $response['status'] = $is_checked ? 'checked' : 'unchecked';

    $dbc->Close();
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    $response['error'] = true;

    if (isset($dbc)) {
        $dbc->Close();
    }
}


ob_clean();


echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;

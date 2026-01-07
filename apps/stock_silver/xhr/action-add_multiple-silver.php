<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');
ini_set('display_errors', 0);
error_reporting(E_ALL);

ob_start();

session_start();

try {
    include_once "../../../config/define.php";
    include_once "../../../include/db.php";
    include_once "../../../include/oceanos.php";

    date_default_timezone_set(DEFAULT_TIMEZONE);

    $dbc = new dbc;
    $dbc->Connect();
    $os = new oceanos($dbc);

    if (empty($_POST['code'])) {
        throw new Exception('โปรดใส่หมายเลขแท่ง');
    }

    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if ($quantity < 1 || $quantity > 100) {
        throw new Exception('จำนวนต้องอยู่ระหว่าง 1-100 แท่ง');
    }

    $base_code = trim($_POST['code']);
    if (!preg_match('/^([A-Za-z]+)(\d+)$/', $base_code, $matches)) {
        throw new Exception('รูปแบบหมายเลขแท่งไม่ถูกต้อง (ต้องเป็นตัวอักษรตามด้วยตัวเลข เช่น A011987)');
    }

    $prefix = $matches[1];
    $start_number = intval($matches[2]);
    $number_length = strlen($matches[2]);

    $codes_to_insert = array();
    for ($i = 0; $i < $quantity; $i++) {
        $current_number = $start_number + $i;
        $current_code = $prefix . str_pad($current_number, $number_length, '0', STR_PAD_LEFT);
        $codes_to_insert[] = $current_code;
    }

    $escaped_codes = array();
    foreach ($codes_to_insert as $code) {
        $escaped_code = addslashes($code);
        $escaped_codes[] = "'" . $escaped_code . "'";
    }
    $codes_check = implode(",", $escaped_codes);

    $existing = $dbc->GetRecord("bs_stock_silver", "code", "code IN ({$codes_check})");

    if (!empty($existing)) {
        $duplicate_codes = array();
        foreach ($existing as $row) {
            $duplicate_codes[] = $row['code'];
        }
        throw new Exception('หมายเลขต่อไปนี้มีในระบบแล้ว: ' . implode(', ', $duplicate_codes));
    }

    $inserted_ids = array();
    $inserted_codes = array();
    $success_count = 0;

    foreach ($codes_to_insert as $current_code) {
        try {
            $data = array(
                '#id' => "DEFAULT",
                'code' => $current_code,
                'customer_po' => isset($_POST['customer_po']) ? $_POST['customer_po'] : '',
                'pack_name' => isset($_POST['pack_name']) ? $_POST['pack_name'] : 'SILVER BAR 1 KG',
                'pack_type' => isset($_POST['pack_type']) ? $_POST['pack_type'] : 'แท่ง',
                '#weight_actual' => isset($_POST['weight_actual']) ? $_POST['weight_actual'] : '1.0000',
                '#weight_expected' => isset($_POST['weight_expected']) ? $_POST['weight_expected'] : '1.0000',
                '#status' => 0,
                'stock' => isset($_POST['stock']) ? $_POST['stock'] : 'BWS',
                'submited' => isset($_POST['date']) ? $_POST['date'] : date('Y-m-d'),
                '#product_id' => 2,
                '#created' => 'NOW()',
                '#supplier_id' => 14
            );

            if (!$dbc->Insert("bs_stock_silver", $data)) {
                error_log("Failed to insert code: {$current_code}");
                continue;
            }

            $silver_id = $dbc->GetID();
            $inserted_ids[] = $silver_id;
            $inserted_codes[] = $current_code;

            $data2 = array(
                '#id' => "DEFAULT",
                "#production_id" => "NULL",
                'code' => $current_code,
                'pack_name' => isset($_POST['pack_name']) ? $_POST['pack_name'] : 'SILVER BAR 1 KG',
                'pack_type' => isset($_POST['pack_type']) ? $_POST['pack_type'] : 'แท่ง',
                '#weight_actual' => isset($_POST['weight_actual']) ? $_POST['weight_actual'] : '1.0000',
                '#weight_expected' => isset($_POST['weight_expected']) ? $_POST['weight_expected'] : '1.0000',
                "#parent" => "NULL",
                "#status" => 0,
                "#delivery_id" => "NULL",
                '#created' => 'NOW()'
            );

            $dbc->Insert("bs_packing_items", $data2);

            if (isset($_SESSION['auth']['user_id'])) {
                try {
                    $silver = $dbc->GetRecord("bs_stock_silver", "*", "id=" . $silver_id);
                    if ($silver) {
                        $os->save_log(
                            0,
                            $_SESSION['auth']['user_id'],
                            "bs_stock_silver-add",
                            $silver_id,
                            array("bs_stock_silver" => $silver)
                        );
                    }
                } catch (Exception $log_error) {
                    error_log("Log error: " . $log_error->getMessage());
                }
            }

            $success_count++;
        } catch (Exception $e) {
            error_log("Error processing code {$current_code}: " . $e->getMessage());
            continue;
        }
    }

    ob_end_clean();

    header('Content-Type: application/json; charset=utf-8');

    if ($success_count > 0) {
        echo json_encode(array(
            'success' => true,
            'msg' => "เพิ่มแท่งเงินสำเร็จ {$success_count} แท่ง",
            'count' => $success_count,
            'ids' => $inserted_ids,
            'codes' => $inserted_codes
        ), JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "ไม่สามารถเพิ่มแท่งเงินได้"
        ), JSON_UNESCAPED_UNICODE);
    }

    if (isset($dbc)) {
        $dbc->Close();
    }
} catch (Exception $e) {
    error_log("Fatal error: " . $e->getMessage());
    error_log("Stack: " . $e->getTraceAsString());

    ob_end_clean();

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'success' => false,
        'msg' => $e->getMessage()
    ), JSON_UNESCAPED_UNICODE);
}

exit;

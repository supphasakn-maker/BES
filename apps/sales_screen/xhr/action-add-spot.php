<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";


@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

$rate_exchange = $os->load_variable("rate_exchange");
$rate_spot = $os->load_variable("rate_spot");
$rate_pmdc = $os->load_variable("rate_pmdc");

$rate_difference = $os->load_variable("rate_difference");

$price1 = $_POST['price1'];
$newprice1 = str_replace(",", "", $price1);
$dd = date('Y-m-d');
$time = date("H:i:s");



$sql1 = "SELECT * FROM `bs_price` order by id desc limit 1";
$rss = $dbc->Query($sql1);
$lastprice = $dbc->Fetch($rss);

$previous = $newprice1 + -$lastprice['price1'];


if ($previous > $rate_difference) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'มากกว่า'
    ));

    $token = "7891135995:AAEEwyoEp2_-68p0E6Y84DUEzNQKPJq-avQ"; // ใส่ Token ของคุณ
    $chat_id = "-4734852819"; // ใส่ Chat ID ที่ต้องการส่งข้อความ
    $message = 'แจ้งเตือนราคา' . "\n" . 'วันที่: ' . $dd . " / " . $time . "\n" .
        'Rate Spot: ' . $_POST['rate_spot'] . "\n" .
        'อัตราแลกเปลี่ยน: ' . $_POST['rate_exchange'] . "\n" .
        'ราคาก่อนหน้า: ' . number_format($lastprice['price1'], 2) . "\n" .
        'ราคาปัจจุบัน: ' . number_format($newprice1, 2) . "\n" .
        'ส่วนต่าง: ' . number_format($previous, 2) . "\n";

    // ตั้งค่า URL ของ API
    $url = "https://api.telegram.org/bot$token/sendMessage";

    // ตั้งค่าข้อมูลที่ต้องการส่ง
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML' // ใช้ HTML ได้ เช่น <b>ตัวหนา</b>
    ];

    // ส่ง HTTP Request ด้วย cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    curl_close($ch);

    // แสดงผลลัพธ์
    echo "Response: " . $result;

    // $url        = 'https://notify-api.line.me/api/notify';
    // $token      = 'chMUgmPauWKTSV3bXXm2DDhmIm4tbjn1AjAdujHbHGt';
    // $headers    = [
    //     'Content-Type: application/x-www-form-urlencoded',
    //     'Authorization: Bearer ' . $token
    // ];
    // $fields  =   'message=แจ้งเตือนราคา' . "\n" . 'วันที่: ' . $dd . " / " . $time . "\n" .
    //     'Rate Spot: ' . $_POST['rate_spot'] . "\n" .
    //     'อัตราแลกเปลี่ยน: ' . $_POST['rate_exchange'] . "\n" .
    //     'ราคาก่อนหน้า: ' . number_format($lastprice['price1'], 2) . "\n" .
    //     'ราคาปัจจุบัน: ' . number_format($newprice1, 2) . "\n" .
    //     'ส่วนต่าง: ' . number_format($previous, 2) . "\n";

    // $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_POST, 1);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // $result = curl_exec($ch);
    // curl_close($ch);

    // var_dump($result);
    // $result = json_decode($result, TRUE);
    $data = array(
        '#id' => "DEFAULT",
        '#created' => 'NOW()',
        "date" => $dd,
        "#rate_spot" => $_POST['rate_spot'] != "" ? $_POST['rate_spot'] : $rate_spot,
        "#rate_exchange" => $_POST['rate_exchange'] != "" ? $_POST['rate_exchange'] : $rate_exchange,
        "price1" => $newprice1,
        "#user" => $os->auth['id']
    );

    if ($dbc->Insert("bs_price", $data)) {
        $spot_id = $dbc->GetID();
        echo json_encode(array(
            'success' => true,
            'msg' => $spot_id
        ));

        $spot = $dbc->GetRecord("bs_price", "*", "id=" . $spot_id);
        $os->save_log(0, $_SESSION['auth']['user_id'], "bs_price-add", $spot_id, array("bs_price" => $spot));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "Insert Error"
        ));
    }
} else if ($previous < -$rate_difference) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'มากกว่า'
    ));

    $token = "7891135995:AAEEwyoEp2_-68p0E6Y84DUEzNQKPJq-avQ"; // ใส่ Token ของคุณ
    $chat_id = "-4734852819"; // ใส่ Chat ID ที่ต้องการส่งข้อความ
    $message = 'แจ้งเตือนราคา' . "\n" . 'วันที่: ' . $dd . " / " . $time . "\n" .
        'Rate Spot: ' . $_POST['rate_spot'] . "\n" .
        'อัตราแลกเปลี่ยน: ' . $_POST['rate_exchange'] . "\n" .
        'ราคาก่อนหน้า: ' . number_format($lastprice['price1'], 2) . "\n" .
        'ราคาปัจจุบัน: ' . number_format($newprice1, 2) . "\n" .
        'ส่วนต่าง: ' . number_format($previous, 2) . "\n";

    // ตั้งค่า URL ของ API
    $url = "https://api.telegram.org/bot$token/sendMessage";

    // ตั้งค่าข้อมูลที่ต้องการส่ง
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML' // ใช้ HTML ได้ เช่น <b>ตัวหนา</b>
    ];

    // ส่ง HTTP Request ด้วย cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    curl_close($ch);

    // แสดงผลลัพธ์
    echo "Response: " . $result;
    $data = array(
        '#id' => "DEFAULT",
        '#created' => 'NOW()',
        "date" => $dd,
        "#rate_spot" => $_POST['rate_spot'] != "" ? $_POST['rate_spot'] : $rate_spot,
        "#rate_exchange" => $_POST['rate_exchange'] != "" ? $_POST['rate_exchange'] : $rate_exchange,
        "price1" => $newprice1,
        "#user" => $os->auth['id']
    );

    if ($dbc->Insert("bs_price", $data)) {
        $spot_id = $dbc->GetID();
        echo json_encode(array(
            'success' => true,
            'msg' => $spot_id
        ));

        $spot = $dbc->GetRecord("bs_price", "*", "id=" . $spot_id);
        $os->save_log(0, $_SESSION['auth']['user_id'], "bs_price-add", $spot_id, array("bs_price" => $spot));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "Insert Error"
        ));
    }
} else {
    $data = array(
        '#id' => "DEFAULT",
        '#created' => 'NOW()',
        "date" => $dd,
        "#rate_spot" => $_POST['rate_spot'] != "" ? $_POST['rate_spot'] : $rate_spot,
        "#rate_exchange" => $_POST['rate_exchange'] != "" ? $_POST['rate_exchange'] : $rate_exchange,
        "price1" => $newprice1,
        "#user" => $os->auth['id']
    );

    if ($dbc->Insert("bs_price", $data)) {
        $spot_id = $dbc->GetID();
        echo json_encode(array(
            'success' => true,
            'msg' => $spot_id
        ));

        $spot = $dbc->GetRecord("bs_price", "*", "id=" . $spot_id);
        $os->save_log(0, $_SESSION['auth']['user_id'], "bs_price-add", $spot_id, array("bs_price" => $spot));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "Insert Error"
        ));
    }
}


$dbc->Close();

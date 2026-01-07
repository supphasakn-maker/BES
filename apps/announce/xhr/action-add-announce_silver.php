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

$sell = $_POST['sell'];
$newsell = str_replace(",", "", $sell);

$buy = $_POST['buy'];
$newbuy = str_replace(",", "", $buy);
$dd = date('Y-m-d');
$time = date("H:i");

if ($dbc->HasRecord("bs_announce_silver", "no = " . $_POST['no'] . " AND date = '" . $_POST['date'] . "'")) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'ครั้งที่ ' . $_POST['no'] . ' ในวันที่ ' . $_POST['date'] . ' มีอยู่แล้ว'
	));
} else if (empty($_SESSION['auth']['user_id'])) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง',
	));
	exit();
} else if ($_POST['sell'] == "" || $_POST['sell'] == 0) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'โปรดระบุราคาขายออก'
	));
} else if ($_POST['no'] == "" || $_POST['no'] == 0) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'โปรดระบุครั้งที่'
	));
} else if ($_POST['date'] == "" || $_POST['date'] == 0) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'โปรดระบุวันที่'
	));
} else {
	$data = array(
		'#id' => "DEFAULT",
		"#no" => $_POST['no'],
		'#created' => 'NOW()',
		"date" => $_POST['date'],
		"#rate_spot" => $_POST['rate_spot'] != "" ? $_POST['rate_spot'] : $rate_spot,
		"#rate_exchange" => $_POST['rate_exchange'] != "" ? $_POST['rate_exchange'] : $rate_exchange,
		"#rate_pmdc" => $_POST['rate_pmdc'] != "" ? $_POST['rate_pmdc'] : $rate_pmdc,
		"sell" => $newsell,
		"buy" => $newbuy,
		"#status" => 1
	);

	if ($dbc->Insert("bs_announce_silver", $data)) {
		$spot_id = $dbc->GetID();
		echo json_encode(array(
			'success' => true,
			'msg' => $spot_id
		));

		$spot = $dbc->GetRecord("bs_announce_silver", "*", "id=" . $spot_id);
		$os->save_log(0, $_SESSION['auth']['user_id'], "announce-silver-add", $spot_id, array("bs_announce_silver" => $spot));

		$sql1 = "select *, (lag(buy) OVER (order by created)) as previous , '" . $newbuy . "'-(lag(buy) OVER (order by created)) as changeprevious from bs_announce_silver order by created desc limit 1";
		// "select *, ('".$newbuy."'+-lag(buy) OVER (order by created)) as previous from bs_announce_silver order by created desc limit 1";
		$rss = $dbc->Query($sql1);
		$lastprice = $dbc->Fetch($rss);

		$previous = number_format($lastprice['changeprevious'], 2);

		$token = "7891135995:AAEEwyoEp2_-68p0E6Y84DUEzNQKPJq-avQ"; // ใส่ Token ของคุณ
		$chat_id = "-4734852819"; // ใส่ Chat ID ที่ต้องการส่งข้อความ
		$message = 'ประกาศราคาแท่งเงิน' . "\n" . 'วันที่: ' . $dd . " / " . $time . "\n" .
			'ราคารับซื้อ: ' . number_format($newbuy, 2) . "\n" .
			'ราคาขาย: ' . number_format($newsell, 2) . "\n" .
			'ครั้งที่: ' . $_POST['no'] . "\n" .
			'SPOT: ' . $_POST['rate_spot'] . "\n" .
			'EXCHANGE: ' . $_POST['rate_exchange'] . "\n" .
			'ส่วนต่าง: ' . $previous . "\n";
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

		echo "Response: " . $result;
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "Insert Error"
		));
	}
}

$dbc->Close();

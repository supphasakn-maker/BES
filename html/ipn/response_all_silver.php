<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
@ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

mysqli_report(MYSQLI_REPORT_OFF);
$link = mysqli_init();
if (!$link) {
    header('HTTP/1.0 500 Internal Server Error');
    echo json_encode(["error" => "mysqli_init failed"]);
    exit;
}
$link->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3); 
if (!$link->real_connect('localhost', 'silver_now', '\aMc"v655AbF', 'erp_main')) {
    header('HTTP/1.0 500 Internal Server Error');
    echo json_encode(["error" => "Database connect error: " . mysqli_connect_error()]);
    exit;
}
mysqli_set_charset($link, 'utf8mb4'); 

$sql = "SELECT *, 
               (LAG(buy) OVER (ORDER BY id)) AS `PREVIOUS`, 
               (buy - LAG(buy) OVER (ORDER BY id)) AS `PREVIOUS_PRICE`
        FROM bs_announce_silver
        WHERE status = 1
        ORDER BY id DESC";
$result = mysqli_query($link, $sql);
if (!$result) {
    header('HTTP/1.0 500 Internal Server Error');
    echo json_encode(["error" => "Database error: " . mysqli_error($link)]);
    mysqli_close($link);
    exit;
}

$arr = [];
while ($row = mysqli_fetch_assoc($result)) {
    $arr[] = $row;
}
echo json_encode($arr);

mysqli_free_result($result);
mysqli_close($link);

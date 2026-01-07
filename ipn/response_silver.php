<?php
//กำหนดค่า Access-Control-Allow-Origin ให้ เครื่อง อื่น ๆ สามารถเรียกใช้งานหน้านี้ได้
header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");

header("Access-Control-Max-Age: 3600");

header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

@ini_set('display_errors', 1);

$requestMethod = $_SERVER["REQUEST_METHOD"];

$link = mysqli_connect('192.168.1.92', 'erp-user', 'T6p$4u4vcf', 'erp_main');

mysqli_set_charset($link, 'utf8');

$requestMethod = $_SERVER["REQUEST_METHOD"];


$sql = "SELECT * , (LAG(buy) OVER (ORDER BY id)) AS 'PREVIOUS' , (buy +- LAG(buy) OVER (ORDER BY id)) AS 'PREVIOUS_PRICE' FROM bs_announce_silver WHERE status = 1 ORDER BY id DESC LIMIT 1";


$result = mysqli_query($link, $sql);


$arr = array();

while ($row = mysqli_fetch_assoc($result)) {

    $arr[] = $row;
}

echo json_encode($arr);

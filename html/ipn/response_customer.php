<?php
header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");

header("Access-Control-Max-Age: 3600");

header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

@ini_set('display_errors', 1);

$requestMethod = $_SERVER["REQUEST_METHOD"];

$link = mysqli_connect('localhost', 'silver_now', '\aMc"v655AbF', 'erp_main');

mysqli_set_charset($link, 'utf8');

$requestMethod = $_SERVER["REQUEST_METHOD"];



if ($requestMethod == 'GET') {

    if (isset($_GET['id']) && !empty($_GET['id'])) {

        $id = $_GET['id'];


        $sql = "SELECT id,name,	default_vat_type,silvernow_no FROM bs_customers WHERE id = $id";

        $result = mysqli_query($link, $sql);


        $arr = array();

        while ($row = mysqli_fetch_assoc($result)) {

            $arr[] = $row;
        }

        echo json_encode($arr);
    } else {

        echo 'Error : Not Found!';
    }
}

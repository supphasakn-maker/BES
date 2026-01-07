<?php

session_start();
ini_set('display_errors', 1);
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => 'os_address.id',
    "address" => 'os_address.address',
    "country" => 'db_countries.name',
    "city" => 'db_cities.name',
    "district" => 'db_districts.name',
    "subdistrict" => 'db_subdistricts.name',
    "postal" => 'os_address.postal',
    "created" => 'os_address.created',
    "updated" => 'os_address.updated',
    "remark" => 'os_address.remark',
    "priority" => 'os_address.priority',
    "type" => 'os_address.organization'
);
$table = array(
    "index" => "id",
    "name" => "os_address",
    "where" => ($_GET['type'] == "organization" ? ("os_address.organization = " . $_GET['id']) : ("os_address.contact = " . $_GET['id'])),
    "join" => array(
        array(
            "field" => "country",
            "table" => "db_countries",
            "with" => "id"
        ),
        array(
            "field" => "city",
            "table" => "db_cities",
            "with" => "id"
        ),
        array(
            "field" => "district",
            "table" => "db_districts",
            "with" => "id"
        ),
        array(
            "field" => "subdistrict",
            "table" => "db_subdistricts",
            "with" => "id"
        )
    )
);

if ($_GET['type'] == "customer") {
    $customer = $dbc->GetRecord("customers", "contact,organization", "id=" . $_GET['id']);
    $where = "(";
    $where .= "os_address.contact = " . $customer['contact'];
    if (!is_null($customer['organization'])) {
        $where .= " OR os_address.organization = " . $customer['organization'];
    }
    $where .= ")";
    $table["where"] = $where;
} else if ($_GET['type'] == "supplier") {
    $supplier = $dbc->GetRecord("suppliers", "contact,organization", "id=" . $_GET['id']);
    $where = "(";
    $where .= "os_address.contact = " . $supplier['contact'];
    if (!is_null($supplier['organization'])) {
        $where .= " OR os_address.organization = " . $supplier['organization'];
    }
    $where .= ")";
    $table["where"] = $where;
}


$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
?>










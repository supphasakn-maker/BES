<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id"             => "bs_purchase_buy.id",
    "supplier_id"    => "bs_purchase_buy.supplier_id",
    "product_id"     => "bs_purchase_buy.product_id",
    "type"           => "bs_purchase_buy.type",
    "amount"         => "FORMAT(bs_purchase_buy.amount,4)",
    "ounces"         => "FORMAT(bs_purchase_buy.ounces,3)",
    "date"           => "bs_purchase_buy.date",
    "created"        => "bs_purchase_buy.created",
    "updated"        => "bs_purchase_buy.updated",
    "method"         => "bs_purchase_buy.method",
    "img"            => "bs_purchase_buy.img",
    "user"           => "os_users.display",
    "name"           => "bs_products.name",
    "supplier"       => "bs_suppliers.name",
    "status"         => "bs_purchase_buy.status",
    "purchase_spot"     => "bs_purchase_buy.purchase_spot",
);

$table = array(
    "index" => "id",
    "name" => "bs_purchase_buy",
    "join" => array(
        array(
            "field" => "user",
            "table" => "os_users",
            "with" => "id"
        ),
        array(
            "field" => "supplier_id",
            "table" => "bs_suppliers",
            "with" => "id"
        ),
        array(
            "field" => "product_id",
            "table" => "bs_products",
            "with" => "id"
        )
    )
);

// Build WHERE condition
$whereCondition = "bs_purchase_buy.type IN ('Buy')";

if (isset($_GET['where'])) {
    $table['where'] = "(" . $_GET['where'] . ") AND (" . $whereCondition . ")";
} else {
    $table['where'] = $whereCondition;
}

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();

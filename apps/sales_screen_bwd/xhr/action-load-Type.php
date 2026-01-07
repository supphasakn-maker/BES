<?php
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

$dbc = new dbc;
$dbc->Connect();

if (!empty($_POST["id"])) {
    $product_id = intval($_POST["id"]);

    $query = "SELECT id, name FROM bs_products_type WHERE product_id = {$product_id} AND status = 1 ORDER BY id ASC";
    $result = $dbc->query($query);

    if ($result && $result->num_rows > 0) {
        echo '<option value="">เลือกประเภทสินค้า</option>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</option>';
        }
    } else {
        echo '<option value="">ไม่มีประเภทสินค้า</option>';
    }
}
$dbc->Close();

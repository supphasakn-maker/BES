<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

class myModel extends imodal
{
    function body()
    {
        $dbc = $this->dbc;
        $items = isset($this->param['items']) ? $this->param['items'] : array();
        $removable = true;

        // Debug: แสดงข้อมูล items ที่ได้รับ
        error_log("Received items: " . print_r($items, true));

        if (count($items) == 0) {
            $removable = false;
        }

        if ($removable) {
            echo '<ul>';
            foreach ($items as $key => $item) {
                // Debug แต่ละ item
                error_log("Processing item[$key]: " . var_export($item, true));

                // เพิ่มการตรวจสอบ item
                if (empty($item) || !is_numeric($item) || intval($item) <= 0) {
                    echo "<li>Invalid item ID: '" . htmlspecialchars($item) . "' (key: $key)</li>";
                    continue;
                }

                $item_id = intval($item);

                // ตรวจสอบว่ามี record หรือไม่
                if (!$dbc->HasRecord("bs_orders_bwd", "id=" . $item_id)) {
                    echo "<li>Order not found: ID " . $item_id . "</li>";
                    continue;
                }

                $order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $item_id);

                if (!$order) {
                    echo "<li>Failed to get order: ID " . $item_id . "</li>";
                    continue;
                }

                echo "<li>" . $order['id'] . ' : ' . htmlspecialchars($order['code']);

                if (!is_null($order['parent']) && !empty($order['parent'])) {
                    $deletable = true;
                    $parent_id = intval($order['parent']);

                    $sql = "SELECT * FROM bs_orders_bwd WHERE parent = " . $parent_id . " AND id != " . $item_id;
                    $rst = $dbc->Query($sql);
                    $extra_string = "<br>";

                    while ($line = $dbc->Fetch($rst)) {
                        if ($dbc->HasRecord("bs_orders_bwd", "parent=" . $line['id'])) {
                            $deletable = false;
                        }
                        $extra_string .= " <span class='badge badge-warning'>เอกสาร " . htmlspecialchars($line['code']) . " จะถูกลบด้วย</span>";
                    }

                    if ($deletable) {
                        echo $extra_string;
                    } else {
                        echo " <span class='badge badge-danger'>ไม่สามารถลบได้เนื่องจากมีรายการย่อย</span>";
                    }
                } else {
                    // ตรวจสอบว่ามีออเดอร์ลูกหรือไม่
                    if ($dbc->HasRecord("bs_orders_bwd", "parent=" . $item_id)) {
                        echo "<br><span class='badge badge-warning'>ออเดอร์ลูกทั้งหมดจะถูกลบด้วย</span>";
                    }
                }
                echo "</li>";
            }
            echo '</ul>';
        } else {
            echo '<div class="alert alert-warning">Please select item to remove!</div>';
            // Debug: แสดงข้อมูลที่ได้รับ
            echo '<small class="text-muted">Debug - Received data: ' . htmlspecialchars(print_r($this->param, true)) . '</small>';
        }
    }
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_remove_order", "Remove Order");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-danger", "Remove", "fn.app.sales_screen_bwd_2.multiorder.remove()")
));
$modal->EchoInterface();

$dbc->Close();

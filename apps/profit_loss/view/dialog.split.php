<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

// Include OrderSplitManager
include_once "../include/order_split_manager.php";

class splitOrderModal extends imodal
{
    function body()
    {
        $dbc = $this->dbc;

        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;

        if (!$order_id) {
            echo '<div class="alert alert-danger">ไม่พบ Order ID</div>';
            return;
        }

        $order = $dbc->GetRecord("bs_orders_profit", "*", "order_id=" . $order_id);

        if (!$order) {
            echo '<div class="alert alert-danger">ไม่พบข้อมูล Order</div>';
            return;
        }

        // ตรวจสอบสถานะ split
        $splitManager = new OrderSplitManager($dbc);
        $status = $splitManager->getSplitStatus($order_id);

?>

        <?php if ($status['can_unsplit']): ?>
            <div class="alert alert-info mb-3">
                <strong><i class="fas fa-info-circle"></i> Split Order</strong><br>
                Order นี้ถูก split แล้ว เป็นส่วนที่ <?php echo $order['split_sequence']; ?> จากทั้งหมด <?php echo count($status['split_records']); ?> รายการ
                <div class="mt-2">
                    <button type="button" class="btn btn-warning btn-sm"
                        onclick="fn.app.profit_loss.profitloss.unsplit(<?php echo $order['parent']; ?>)">
                        <i class="fas fa-undo"></i> Unsplit Orders
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($status['can_split']): ?>
            <form name="form_split">
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

                <div class="row mb-3">
                    <div class="col-12">
                        <h5>Split Order: <?php echo htmlspecialchars($order['code']); ?></h5>
                        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <p><strong>Original Amount:</strong> <span id="original_amount"><?php echo $order['amount']; ?></span> kg</p>
                        <p><strong>Total:</strong> <?php echo number_format($order['total'], 2); ?></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <h6>Split Amounts:</h6>
                        <div id="split_amounts_container">
                            <!-- Default 2 split rows -->
                            <div class="split-row row mb-2">
                                <div class="col-8">
                                    <input type="number" step="0.0001" class="form-control" name="split_amount[]"
                                        placeholder="Amount" onchange="fn.app.profit_loss.profitloss.calculateSplitTotal()">
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="$(this).closest('.split-row').remove(); fn.app.profit_loss.profitloss.calculateSplitTotal();">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-secondary"
                            onclick="fn.app.profit_loss.profitloss.addSplitRow()">
                            <i class="fas fa-plus"></i> Add Row
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <strong>Total Split:</strong> <span id="split_total">0.0000</span> kg<br>
                            <strong>Remaining:</strong> <span id="split_remaining" class="text-danger"><?php echo $order['amount']; ?></span> kg
                        </div>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Order นี้ไม่สามารถ split ได้
            </div>
        <?php endif; ?>

<?php
    }
}

$modal = new splitOrderModal($dbc, $os->auth);
$modal->setModel("dialog_split_order", "Split Order");
$modal->initiForm("form_split");
$modal->setExtraClass("modal-lg");
$modal->setParam($_POST);

// เพิ่ม button ตามสถานะ
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$splitManager = new OrderSplitManager($dbc);
$status = $splitManager->getSplitStatus($order_id);

if ($status['can_split']) {
    $modal->setButton(array(
        array("close", "btn-secondary", "Cancel"),
        array("action", "btn-primary", "Split Order", "fn.app.profit_loss.profitloss.split()", "id" => "btn_split", "disabled" => true)
    ));
} else {
    $modal->setButton(array(
        array("close", "btn-secondary", "Close")
    ));
}

$modal->EchoInterface();

$dbc->Close();
?>
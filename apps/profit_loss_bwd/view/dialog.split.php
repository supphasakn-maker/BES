<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

$dbc = new datastore;
$dbc->Connect();

$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$max_amount = isset($_POST['max_amount']) ? floatval($_POST['max_amount']) : 0;

$sql = "
SELECT 
    parent.id,
    parent.code,
    parent.customer_name,
    SUM(CASE 
        WHEN child.product_id = 1 THEN child.amount * 0.015
        WHEN child.product_id = 2 THEN child.amount * 0.050
        WHEN child.product_id = 3 THEN child.amount * 0.150
        ELSE 0
    END) AS calculated_amount,
    SUM(child.total) AS total_sum,
    COALESCE((SELECT SUM(split_amount) FROM bs_orders_split_bwd WHERE parent_order_id = parent.id AND status = 1), 0) AS total_split_amount,
    COALESCE((SELECT SUM(split_total) FROM bs_orders_split_bwd WHERE parent_order_id = parent.id AND status = 1), 0) AS total_split_total
FROM bs_orders_bwd parent
LEFT JOIN bs_orders_bwd child ON (child.id = parent.id OR child.parent = parent.id)
WHERE parent.id = $order_id
AND parent.status > 0
AND child.product_id IN (1,2,3)
AND child.status > 0
GROUP BY parent.id
";

$rst = $dbc->Query($sql);
$order = $dbc->Fetch($rst);

if (!$order) {
    echo '<div class="alert alert-danger">ไม่พบข้อมูล Order</div>';
    $dbc->Close();
    exit;
}

$available_amount = floatval($order[3]) - floatval($order[5]);
$available_total = floatval($order[4]) - floatval($order[6]);
$ratio = $available_amount > 0 ? ($available_total / $available_amount) : 0;

$dbc->Close();
?>

<!-- Modal -->
<div class="modal fade" id="dialog_split_order_bwd" tabindex="-1" role="dialog" aria-labelledby="splitOrderLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="splitOrderLabel">
                    <i class="fas fa-cut"></i> Split Order
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="form_split_bwd" onsubmit="return fn.app.profit_loss_bwd.profitloss.split();">
                <div class="modal-body">
                    <input type="hidden" id="order_id" name="order_id" value="<?php echo $order_id; ?>">
                    <input type="hidden" id="original_amount" value="<?php echo $available_amount; ?>">
                    <input type="hidden" id="ratio" value="<?php echo $ratio; ?>">

                    <h6><strong>Split Order: <?php echo $order[1]; ?></strong></h6>
                    <p class="mb-1"><strong>Customer:</strong> <?php echo $order[2]; ?></p>
                    <p class="mb-1"><strong>Original Amount:</strong> <?php echo number_format($available_amount, 4); ?> kg</p>
                    <p class="mb-3"><strong>Total:</strong> <?php echo number_format($available_total, 2); ?></p>

                    <hr>

                    <label class="font-weight-bold">Split Amounts:</label>
                    <div id="split_amounts_container">
                        <!-- แถวแรก -->
                        <div class="split-row row mb-2">
                            <div class="col-10">
                                <input type="number"
                                    step="0.0001"
                                    class="form-control"
                                    name="split_amount[]"
                                    placeholder="Amount"
                                    onchange="fn.app.profit_loss_bwd.profitloss.calculateSplitTotal()"
                                    oninput="fn.app.profit_loss_bwd.profitloss.calculateSplitTotal()">
                            </div>
                            <div class="col-2">
                                <button type="button"
                                    class="btn btn-danger btn-sm btn-block"
                                    onclick="$(this).closest('.split-row').remove(); fn.app.profit_loss_bwd.profitloss.calculateSplitTotal();">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                        class="btn btn-outline-primary btn-sm mb-3"
                        onclick="fn.app.profit_loss_bwd.profitloss.addSplitRow()">
                        <i class="fas fa-plus"></i> Add Row
                    </button>

                    <div class="alert alert-info">
                        <p class="mb-1"><strong>Total Split:</strong> <span id="split_total">0.0000</span> kg</p>
                        <p class="mb-0"><strong>Remaining:</strong> <span id="split_remaining" class="text-danger"><?php echo number_format($available_amount, 4); ?></span> kg</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn_split" disabled>
                        <i class="fas fa-check"></i> Split
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#dialog_split_order_bwd').on('hidden.bs.modal', function() {
        $(this).remove();
    });

    $('#dialog_split_order_bwd').on('shown.bs.modal', function() {
        $('#split_amounts_container input:first').focus();
    });
</script>
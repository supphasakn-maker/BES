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
        if (isset($this->param['sales'])) {

            echo '<form name="form_matchusdsilver">';
            echo '<div class="form-group row">';
            echo '<label class="col-sm-2 col-form-label text-right">SUM DATE</label>';
            echo '<div class="col-sm-5">';
            echo '<input type="date" class="form-control" name="date" value="' . $_POST['date_filter'] . '">';
            echo '</div>';
            echo '</div>';
            echo '<div class="row">';
            echo '<div class="col-8">';
            echo '<table class="table table-sm table-bordered">';
            echo '<tfoot>';
            echo '<tr>';
            echo '<th colspan="3" class="text-right">';
            echo 'Total <span id="silver_total_match_order_usd" class="badge">100</span>';
            echo '</th>';
            echo '<th class="text-right">';
            echo 'Total <span id="silver_total_match_order_total_usd" class="badge">100</span>';
            echo '</th>';
            echo '</tr>';
            echo '</tfoot>';
            echo '<thead>';
            echo '<tr>';
            echo '<th class="text-center text-nowrap">Order ID</th>';
            echo '<th class="text-center text-nowrap">Order </th>';
            echo '<th class="text-center text-nowrap">Amount</th>';
            echo '<th class="text-center text-nowrap">Total</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($this->param['sales'] as $sales) {
                $is_split = false;
                $split_id = null;
                $parent_order_id = null;

                if (is_string($sales) && strpos($sales, 'SPLIT_') === 0) {
                    $is_split = true;
                    $split_id = intval(str_replace('SPLIT_', '', $sales));

                    $sql_split = "SELECT parent_order_id, split_amount, split_total FROM bs_orders_split_bwd WHERE id = $split_id AND status = 1";
                    $rst_split = $dbc->Query($sql_split);
                    $split_data = mysqli_fetch_array($rst_split);

                    if ($split_data) {
                        $parent_order_id = $split_data['parent_order_id'];
                        $calculated_amount = number_format($split_data['split_amount'], 4, '.', '');
                        $total_sum = number_format($split_data['split_total'], 0, '.', '');

                        $order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $parent_order_id);
                        $order_code = $order['code'] . '-S' . $split_id;
                    } else {
                        continue; 
                    }
                } else {
                    $order_id = intval($sales);
                    $order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);
                    $order_code = $order['code'];

                    $sql_calc = "SELECT 
                        SUM(CASE 
                            WHEN product_id = 1 THEN amount * 0.015
                            WHEN product_id = 2 THEN amount * 0.050
                            WHEN product_id = 3 THEN amount * 0.150
                            ELSE 0
                        END) AS calculated_amount,
                        SUM(total) AS total_sum
                    FROM bs_orders_bwd 
                    WHERE (id = " . $order['id'] . " OR parent = " . $order['id'] . ")
                    AND status > 0
                    AND product_id IN (1,2,3)";

                    $rst_calc = $dbc->Query($sql_calc);
                    $row_calc = mysqli_fetch_array($rst_calc);
                    $calculated_amount = number_format($row_calc['calculated_amount'], 4, '.', '');
                    $total_sum = number_format($row_calc['total_sum'], 0, '.', '');
                }

                $check_id = $is_split ? $sales : $order['id'];

                if ($dbc->HasRecord("bs_mapping_profit_orders_usd_bwd", "order_id='" . $check_id . "' AND mapping_id IS NULL")) {
                    $mapping = $dbc->GetRecord("bs_mapping_profit_orders_usd_bwd", "*", "order_id='" . $check_id . "' AND mapping_id IS NULL");

                    echo '<tr>';
                    echo '<input type="hidden" name="order_mapping_id[]" value="' . $mapping['id'] . '">';
                    echo '<input type="hidden" name="order_id[]" value="' . $check_id . '">';
                    echo '<td class="text-center align-middle text-nowrap">' . $order_code . '</td>';
                    echo '<td class="text-center align-middle">' . number_format($mapping['amount'], 4) . '/' . $calculated_amount . '</td>';
                    echo '<td class="p-0">';
                    $class = "form-control form-control-sm text-center rounded-0 border-dark";
                    $onchange = "fn.app.profit_loss_bwd.profitloss.dialog_matchusd_calculation()";
                    echo '<input xname="order_amount" name="order_amount[]" readonly class="' . $class . '" onchange="' . $onchange . '" value="' . number_format($mapping['amount'], 4, '.', '') . '">';
                    echo '</td>';
                    echo '<td class="text-right align-middle">' . number_format($mapping['total'], 0) . '/' . $total_sum . '</td>';
                    echo '</tr>';
                } else {
                    echo '<tr>';
                    echo '<input type="hidden" name="order_mapping_id[]" value="">';
                    echo '<input type="hidden" name="order_id[]" value="' . $check_id . '">';
                    echo '<td class="text-center align-middle text-nowrap">' . $order_code . '</td>';
                    echo '<td class="text-center align-middle">' . $calculated_amount . '</td>';
                    echo '<td class="p-0">';
                    $class = "form-control form-control-sm text-center rounded-0 border-dark";
                    $onchange = "fn.app.profit_loss_bwd.profitloss.dialog_matchusd_calculation()";
                    echo '<input xname="order_amount" name="order_amount[]" readonly class="' . $class . '" onchange="' . $onchange . '" value="' . $calculated_amount . '">';
                    echo '</td>';
                    echo '<td class="p-0">';
                    $class = "form-control form-control-sm text-center rounded-0 border-dark";
                    $onchangetotal = "fn.app.profit_loss_bwd.profitloss.dialog_matchusd_calculationtotal()";
                    echo '<input xname="order_total" name="order_total[]" readonly class="' . $class . '" onchange="' . $onchangetotal . '" value="' . $total_sum . '">';
                    echo '</td>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';

            echo '<div class="col-12">';
            echo '<textarea name="remark" class="form-control" placeholder="Remark"></textarea>';
            echo '</div>';
            echo '</div>';
            echo '</form>';
        } else {
            echo '<div class="alert alert-danger">ข้อมูลไม่ครบ</div>';
        }
    }
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_matchusd_silver", "SUM USD");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-danger", "SUM USD", "fn.app.profit_loss_bwd.profitloss.matchusd()")
));
$modal->EchoInterface();

$dbc->Close();

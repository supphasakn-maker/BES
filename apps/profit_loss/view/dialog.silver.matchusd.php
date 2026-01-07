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
                // แก้ไข: ใช้ order_id แทน id
                $order = $dbc->GetRecord("bs_orders_profit", "*", "order_id=" . $sales);

                // แก้ไข: ใช้ $order['order_id'] แทน $order['id']
                if ($dbc->HasRecord("bs_mapping_profit_orders_usd", "order_id=" . $order['order_id'] . " AND mapping_id IS NULL")) {
                    $mapping = $dbc->GetRecord("bs_mapping_profit_orders_usd", "*", "order_id=" . $order['order_id'] . " AND mapping_id IS NULL");

                    echo '<tr>';
                    echo '<input type="hidden" name="order_mapping_id[]" value="' . $mapping['id'] . '">';
                    echo '<input type="hidden" name="order_id[]" value="' . $order['order_id'] . '">';
                    echo '<td class="text-center align-middle text-nowrap">' . $order['code'] . '</td>';
                    echo '<td class="text-center align-middle">' . $mapping['amount'] . '/' . $order['amount'] . '</td>';
                    echo '<td class="p-0">';
                    $class = "form-control form-control-sm text-center rounded-0 border-dark";
                    $onchange = "fn.app.profit_loss.profitloss.dialog_matchthb_calculation()";
                    echo '<input xname="order_amount" name="order_amount[]" readonly class="' . $class . '" onchange="' . $onchange . '" value="' . $mapping['amount'] . '">';
                    echo '<td class="text-right align-middle">' . $mapping['total'] . '/' . $order['total'] . '</td>';
                    echo '</td>';
                    echo '</tr>';
                } else {
                    echo '<tr>';
                    echo '<input type="hidden" name="order_mapping_id[]" value="">';
                    echo '<input type="hidden" name="order_id[]" value="' . $order['order_id'] . '">';
                    echo '<td class="text-center align-middle text-nowrap">' . $order['code'] . '</td>';
                    echo '<td class="text-center align-middle">' . $order['amount'] . '</td>';
                    echo '<td class="p-0">';
                    $class = "form-control form-control-sm text-center rounded-0 border-dark";
                    $onchange = "fn.app.profit_loss.profitloss.dialog_matchusd_calculation()";
                    echo '<input xname="order_amount" name="order_amount[]" readonly class="' . $class . '" onchange="' . $onchange . '" value="' . $order['amount'] . '">';
                    echo '</td>';
                    echo '<td class="p-0">';
                    $class = "form-control form-control-sm text-center rounded-0 border-dark";
                    $onchangetotal = "fn.app.profit_loss.profitloss.dialog_matchthb_dialog_matchusd_calculationtotal()";
                    echo '<input xname="order_total" name="order_total[]" readonly class="' . $class . '" onchange="' . $onchangetotal . '" value="' . $order['total'] . '">';
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
    array("action", "btn-danger", "SUM USD", "fn.app.profit_loss.profitloss.matchusd()")
));
$modal->EchoInterface();

$dbc->Close();

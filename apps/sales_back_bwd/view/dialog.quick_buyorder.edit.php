<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);
$modal = new imodal($dbc, $os->auth);

class myModel extends imodal
{
    private $os = null;

    function setOS($os)
    {
        $this->os = $os;
    }

    function body()
    {
        $dbc = $this->dbc;
        $this->initilForm("form_editquick_buyorder");
        $order = $dbc->GetRecord("bs_orders_back_bwd", "*", "id=" . $_POST['id']);
        // $customer = $dbc->GetRecord("bs_customers","*","id=".$order['customer_id']);


        echo '<ul class="list-group list-group-horizontal mb-3">';
        echo '<li class="list-group-item flex-fill text-center">';
        echo '<div class="text-secondary">CODE</div><strong>' . $order['code'] . '  </strong>';
        echo '</li>';
        echo '<li class="list-group-item flex-fill text-center">';
        echo '<div class="text-secondary">Created</div><strong>' . $order['created'] . ' </strong>';
        echo '</li>';
        echo '<li class="list-group-item flex-fill text-center">';
        echo '<div class="text-secondary">Sales</div><strong>' . $this->os->auth['display'] . ' </strong>';
        echo '</li>';
        echo '</ul>';

        echo '<form name="form_editquick_buyorder">';
        echo '<input type="hidden" name="id" value="' . $order['id'] . '">';
        echo '<table class="table table-bordered table-form">';
        echo '<tbody>';
        echo '<tr>';
        echo '<td><label>ชื่อลูกค้า</label></td>';
        echo '<td><input class="form-control" type="text" name="customer_name"  readonly value="' . $order['customer_name'] . '"></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><label>Platform</label></td>';
        echo '<td><input class="form-control" type="text" name="platform" readonly value="' . $order['platform'] . '"></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><label>เบอร์โทร</label></td>';
        echo '<td><input type="text" name="phone" class="form-control" value="' . $order['phone'] . '"></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><label>วันเวลาทำรายการ</label></td>';
        echo '<td><input class="form-control" readonly value="' . $order['created'] . '"></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><label>แท่ง</label></td>';
        echo '<td><input class="form-control" type="text" name="amount" value="' . $order['amount'] . '"></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><label>ราคา</label></td>';
        echo '<td><input class="form-control" type="text" name="price" value="' . $order['price'] . '"></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><label>Product</label></td>';
        echo '<td> 
						<select id="product" name="product" class="form-control">
						<option value="">Select Product</option>';
?>
        <?php
        $sql = "SELECT * FROM bs_products_bwd WHERE status = 1";
        $result = $dbc->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($order['product_id'] == $row["id"]) {
                    $sel = "selected";
                } else {
                    $sel = "";
                }
                echo '<option value="' . $row['id'] . '" ' . $sel . '>' . $row['name'] . '</option>';
            }
        } else {
            echo '<option value="">Product not available</option>';
        }
        ?>
        </select>
        <?php
        '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><label>Product Type</label></td>';
        echo '<td>
						<select id="type" name="type" class="form-control">
                        <option value="">Select Type</option>';
        ?>
        <?php
        $sql = "SELECT * FROM bs_products_type WHERE status ='1' AND id = '" . $order['product_type'] . "'";
        $result = $dbc->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($order['product_type'] == $row["id"]) {
                    $sel = "selected";
                } else {
                    $sel = "";
                }
                echo '<option value="' . $row['id'] . '" ' . $sel . '>' . $row['name'] . '</option>';
            }
        } else {
            echo '<option value="">Product Type not available</option>';
        }
        ?>
        </select>
        <?php
        '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><label>สลักข้อความบนแท่งเงิน</label></td>';
        echo '<td>                    
						<select name="engrave" id="engrave" class="form-control">';
        ?>
        <option <?php if ($order['engrave'] == 'สลักข้อความบนแท่งเงิน') echo "selected"; ?> value="สลักข้อความบนแท่งเงิน">สลักข้อความ</option>
        <option <?php if ($order['engrave'] == 'ไม่สลักข้อความบนแท่งเงิน') echo "selected"; ?> value="ไม่สลักข้อความบนแท่งเงิน">ไม่สลักข้อความ</option>
        <?php
        '</select></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><label>Comment</label></td>';
        echo '<td><input class="form-control" readonly value="' . $order['comment'] . '"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td><label>Vats</label></td>';
        echo '<td>                    
		<select name="vat_type" id="vat_type" class="form-control">';
        ?>
        <option <?php if ($order['vat_type'] == '0') echo "selected"; ?> value="0">ไม่มี Vats</option>
        <option <?php if ($order['vat_type'] == '7') echo "selected"; ?> value="7">มี Vats</option>
<?php
        echo '</tr>';

        echo '</tbody>';
        echo '</table>';
        echo '</form>';
    }
}

$modal = new myModel($dbc, $os->auth);
$modal->setOS($os);
$modal->setParam($_POST);
$modal->setModel("dialog_edit_quick_buyorder", "Edit Order");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-primary", "Save Order", "fn.app.sales_back_bwd.sale_back.edit(" . $_POST['id'] . ")")
));

$modal->EchoInterface();
$dbc->Close();
?>
<script>
    $(document).ready(function() {
        $('#product').on('change', function() {
            var id = $(this).val();
            if (id) {
                $.ajax({
                    type: 'POST',
                    url: 'apps/sales_screen_bwd_2/xhr/action-load-Type.php',
                    data: 'id=' + id,
                    success: function(html) {
                        $('#type').html(html);
                    }
                });
            } else {
                $('#type').html('<option value="">Select Product Type</option>');
            }
        });
    });
</script>
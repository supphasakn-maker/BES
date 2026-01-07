<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

$modal = new imodal($dbc, $os->auth);


class myModel extends imodal
{
    function body()
    {
        global $os;
        $dbc = $this->dbc;
        $adjust = $dbc->GetRecord("bs_stock_adjusted_bwd", "*", "id=" . $_POST['id']);

?>
        <form name="form_editadjust">
            <div class="form-group row display-group" display-group="daily">
                <label class="col-sm-2 col-form-label text-right">วันที่</label>
                <div class="col-sm-10">
                    <input type="date" name="date" class="form-control" value="<?php echo $adjust['date']; ?>">
                    <input type="hidden" name="id" class="form-control" value="<?php echo $adjust['id']; ?>">
                </div>
            </div>
            <div class="form-group row display-group" display-group="daily">
                <label class="col-sm-2 col-form-label text-right">Product</label>
                <div class="col-sm-10">
                    <select id="product_id" name="product_id" class="form-control">
                        <option value="">Select Product</option>
                        <?php
                        $sql = "SELECT * FROM bs_products_bwd WHERE status = 1";
                        $result = $dbc->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                if ($adjust['product_id'] == $row["id"]) {
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
                </div>
            </div>
            <div class="form-group row display-group" display-group="daily">
                <label class="col-sm-2 col-form-label text-right">Product Type</label>
                <div class="col-sm-10">
                    <select id="product_type" name="product_type" class="form-control">
                        <option value="">Select Type</option>
                        <?php
                        $sql = "SELECT * FROM bs_products_type WHERE status ='1' AND id = '" . $adjust['product_type'] . "'";
                        $result = $dbc->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                if ($adjust['product_type'] == $row["id"]) {
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
                </div>
            </div>
            <div class="form-group row display-group" display-group="daily">
                <label class="col-sm-2 col-form-label text-right">Type</label>
                <div class="col-sm-10">
                    <select id="type_id" name="type_id" class="form-control">
                        <option value="">Select Type</option>
                        <?php
                        $sql = "SELECT * FROM bs_stock_adjust_type_bwd";
                        $result = $dbc->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                if ($adjust['type_id'] == $row["id"]) {
                                    $sel = "selected";
                                } else {
                                    $sel = "";
                                }
                                echo '<option value="' . $row['id'] . '" ' . $sel . '>' . $row['name'] . '</option>';
                            }
                        } else {
                            echo '<option value="">Type not available</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group row display-group" display-group="daily">
                <label class="col-sm-2 col-form-label text-right">Remark</label>
                <div class="col-sm-10">
                    <textarea class="form-control" id="remark" name="remark" rows="3"><?php echo $adjust['remark']; ?></textarea>
                </div>
            </div>
            <div class="form-group row display-group" display-group="daily">
                <label class="col-sm-2 col-form-label text-right">จำนวนที่ยืม / แท่ง</label>
                <div class="col-sm-10">
                    <input type="number" name="amount" class="form-control" value="<?php echo $adjust['amount']; ?>">
                </div>
            </div>
        </form>

<?php
    }
}


$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_edit_adjust", "EDIT ADJUST");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-danger", "Submit", "fn.app.stock_type_bwd.adjust.edit()")
));
$modal->EchoInterface();

$dbc->Close();
?>
<script>
    $(document).ready(function() {
        $('#product_id').on('change', function() {
            var id = $(this).val();
            if (id) {
                $.ajax({
                    type: 'POST',
                    url: 'apps/stock_type_bwd/xhr/action-load-Product.php',
                    data: 'id=' + id,
                    success: function(html) {
                        $('#product_type').html(html);
                    }
                });
            } else {
                $('#product_type').html('<option value="">Select Product Type</option>');
            }
        });
    });
</script>
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
class myModel extends imodal
{
    function body()
    {
        global $os;
        $dbc = $this->dbc;
?>
        <form name="form_addadjust">
            <div class="form-group row display-group" display-group="daily">
                <label class="col-sm-2 col-form-label text-right">วันที่</label>
                <div class="col-sm-10">
                    <input type="date" name="date" class="form-control" value="<?php echo date("Y-m-d"); ?>">
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
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
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
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
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
                    <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                </div>
            </div>
            <div class="form-group row display-group" display-group="daily">
                <label class="col-sm-2 col-form-label text-right">จำนวนที่ยืม</label>
                <div class="col-sm-10">
                    <input type="number" name="amount" class="form-control">
                </div>
            </div>
        </form>

<?php
    }
}


$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_add_adjust", "ADD ADJUST");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-danger", "Submit", "fn.app.stock_type_bwd.adjust.add()")
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
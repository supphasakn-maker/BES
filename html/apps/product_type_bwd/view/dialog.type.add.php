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
        <form name="form_addtype">
            <div class="form-group row display-group" display-group="daily">
                <label class="col-sm-2 col-form-label text-right">Code</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="code">
                </div>
            </div>
            <div class="form-group row display-group" display-group="daily">
                <label class="col-sm-2 col-form-label text-right">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="name">
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
                <label class="col-sm-2 col-form-label text-right">Type</label>
                <div class="col-sm-10">
                    <select id="type" name="type" class="form-control">
                        <option value="BWD">BWD</option>
                        <option value="BWS">BWS</option>
                    </select>
                </div>
            </div>
            <div class="form-group row display-group" display-group="daily">
                <label class="col-sm-2 col-form-label text-right">Status</label>
                <div class="col-sm-10">
                    <select id="status" name="status" class="form-control">
                        <option value="1">enabled</option>
                        <option value="2">disabled</option>
                    </select>
                </div>
            </div>
        </form>

<?php
    }
}


$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_add_type", "Add Product Type");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
    array("close", "btn-secondary", "Dismiss"),
    array("action", "btn-danger", "Submit", "fn.app.product_type_bwd.type.add()")
));
$modal->EchoInterface();

$dbc->Close();
?>
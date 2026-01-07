<?php
session_start();
@ini_set('display_errors', 1);
include "../../config/define.php";
include "../../include/db.php";
include "../../include/oceanos.php";
include "../../include/iface.php";

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);
//$os->initial_lang("lang");
$panel = new ipanel($dbc, $os->auth);
$ui_form = new iform($dbc, $os->auth);
$today = time();

?>
<div class="row gutters-sm">
    <div class="col-xl-12 mb-3">
        <div class="row gutters-sm">
            <div class="col-xl-6 mb-3">
                <?php include "view/card.salesback.add.php"; ?>
            </div>
            <div class="col-xl-6 mb-3">
                <?php include "view/card.sales_back_orders.php"; ?>
            </div>
        </div>
    </div>

</div>
<script>
    var plugins = [
        'apps/sales_back_bwd/include/interface.js',
        'apps/sales_bwd/include/interface.js',
        'plugins/datatables/dataTables.bootstrap4.min.css',
        'plugins/datatables/responsive.bootstrap4.min.css',
        'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
        'plugins/chart.js/Chart.min.js',
        'plugins/jquery-sparkline/jquery.sparkline.min.js',
        'plugins/select2/js/select2.full.min.js',
        'plugins/select2/css/select2.min.css',
        'plugins/moment/moment.min.js'
    ]


    App.loadPlugins(plugins, null).then(() => {
        App.checkAll()
        <?php
        include "control/controller.sale_back.view.js";
        if ($os->allow("sales_back_bwd", "add")) include "control/controller.sale_back.add.js";
        if ($os->allow("sales_back_bwd", "remove")) include "control/controller.sale_back.remove.js";
        if ($os->allow("sales_back_bwd", "edit")) include "control/controller.sale_back.edit.js";

        ?>
    }).then(() => App.stopLoading())
</script>
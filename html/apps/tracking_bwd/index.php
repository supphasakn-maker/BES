<?php
session_start();
@ini_set('display_errors', 1);
include "../../config/define.php";
include "../../include/db.php";
include "../../include/oceanos.php";
include "../../include/iface.php";
include "../../include/session.php";
include "../../include/datastore.php";

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);
$panel = new ipanel($dbc, $os->auth);

include_once "../../include/alert-buysell.php";


$panel->setApp("tracking_bwd", "Tracking BWD");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'order');

$panel->setMeta(array(
    array("order", "Order Bowins Design", "far fa-user"),
));
$panel->PageBreadcrumb();


if ($panel->getView() == "printable") {
    include "../schedule_bwd/view/page.order.view.php";
} else if ($panel->getView() == "printablebf") {
    include "../schedule_bwd/view/page.ordersale_back.view.php";
} else {
    echo '<div class="row">';
    echo '<div class="col-xl-12">';
    $panel->EchoInterface();
    echo '</div>';
    echo '</div>';
}
?>
<script>
    var plugins = [
        'apps/tracking_bwd/include/interface.js',
        'apps/sales_bwd/include/interface.js',
        'plugins/datatables/dataTables.bootstrap4.min.css',
        'plugins/datatables/responsive.bootstrap4.min.css',
        'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
        'plugins/select2/css/select2.min.css',
        'plugins/select2/js/select2.min.js',
        'plugins/sweetalert/sweetalert-dev.js',
        'plugins/sweetalert/sweetalert.css',
        'plugins/moment/moment.min.js'
    ];
    App.loadPlugins(plugins, null).then(() => {
        App.checkAll()
        <?php
        switch ($panel->getView()) {
            case "order":
                include "control/controller.order.view.js";
                if ($os->allow("tracking_bwd", "edit")) include "../sales_bwd/control/controller.order.edit_tracking.js";
                break;
        }
        ?>
    }).then(() => App.stopLoading())
</script>
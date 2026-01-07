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
$ui_form = new iform($dbc, $os->auth);

include_once "../../include/alert-buysell.php";

$panel->setApp("buy_fixed", "Buy Fixed");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'buy');



$panel->setMeta(array(
    array("buy", "Buy Fixed", "far fa-user"),
));
?>
<?php
$panel->PageBreadcrumb();
?>
<div class="row">
    <div class="col-xl-12">
        <?php
        $panel->EchoInterface();
        ?>
    </div>
</div>
<script>
    var plugins = [
        'apps/buy_fixed/include/interface.js',
        'plugins/datatables/dataTables.bootstrap4.min.css',
        'plugins/datatables/responsive.bootstrap4.min.css',
        'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
        'plugins/select2/css/select2.min.css',
        'plugins/select2/js/select2.min.js',
        'plugins/sweetalert/sweetalert-dev.js',
        'plugins/sweetalert/sweetalert.css',
        'plugins/moment/moment.min.js',
        'https://unpkg.com/tesseract.js@v4.1.1/dist/tesseract.min.js'
    ];
    App.loadPlugins(plugins, null).then(() => {
        App.checkAll()
        <?php
        switch ($panel->getView()) {
            case "buy":
                include "control/controller.buy.view.js";
                include "control/controller.buy.add.js";
                include "control/controller.buy.upload_image.js";
                include "control/controller.buy.upload_image_wechat.js";
                include "control/controller.buy.remove.js";
                break;
        }
        ?>
    }).then(() => App.stopLoading())
</script>
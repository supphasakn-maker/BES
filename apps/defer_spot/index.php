<?php
session_start();
@ini_set('display_errors', 1);
include "../../config/define.php";
include "../../include/db.php";
include "../../include/oceanos.php";
include "../../include/iface.php";
include "../../include/session.php";

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);
$panel = new ipanel($dbc, $os->auth);
$ui_form = new iform($dbc, $os->auth);

$panel->setApp("defer_spot", "Defer Spot");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'defer');

$panel->setMeta(array(
    array("defer", "ปิด Defer", "far fa-clipboard"),

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
        'apps/defer_spot/include/interface.js',
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
            case "defer":
                include "control/controller.defer.view.js";
                include "control/controller.defer.add.js";
                include "control/controller.defer.edit.js";
                include "control/controller.defer.remove.js";
                break;
        }
        ?>
    }).then(() => App.stopLoading())
</script>
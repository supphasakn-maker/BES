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

$panel->setApp("production_switch", "switch");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'switch');
$panel->setSection(isset($_GET['section']) ? $_GET['section'] : '');
$ui_form = new iform($dbc, $os->auth);

$panel->setMeta(array(
    array("switch", "SWITCH", "far fa-user"),
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
        'apps/production_switch/include/interface.js',
        'apps/production_prepare/include/interface.js',
        'apps/production/include/interface.js',
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
            case "switch":
                include "control/controller.switch.view.js";
                include "control/controller.switch.remove.js";
                if ($os->allow("production_switch", "edit")) include "control/controller.switch.turn.js";
                include "control/controller.switch.prepare.js";
                include "control/controller.switch.add.js";
                include "control/controller.switch.approve.js";
                break;
        }
        ?>
    }).then(() => App.stopLoading())
</script>
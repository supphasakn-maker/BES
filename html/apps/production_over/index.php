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

$panel->setApp("production_over", "stock");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'adjust');

$panel->setMeta(array(
    array("adjust", "เม็ดเกิน", "far fa-user"),
    // array("type", "Type", "far fa-user"),
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
        'apps/production_over/include/interface.js',
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
            case "adjust":
                include "control/controller.adjust.view.js";
                if ($os->allow("production_over", "add")) include "control/controller.adjust.add.js";
                if ($os->allow("production_over", "edit")) include "control/controller.adjust.edit.js";
                if ($os->allow("production_over", "remove")) include "control/controller.adjust.remove.js";
                break;
                // case "type":
                //     // include "control/controller.type.view.js";
                //     // if ($os->allow("stock", "add")) include "control/controller.type.add.js";
                //     // if ($os->allow("stock", "edit")) include "control/controller.type.edit.js";
                //     // if ($os->allow("stock", "remove")) include "control/controller.type.remove.js";
                //     break;
        }
        ?>
    }).then(() => App.stopLoading())
</script>
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
$panel = new ipanel($dbc, $os->auth);

$panel->setApp("defer_cost", "Defer MATCH");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'defer');

$panel->setMeta(array(
    array("defer", "Defer", "far fa-user"),
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
        'apps/defer_cost/include/interface.js',
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
                if ($os->allow("defer_cost", "add")) include "control/controller.defer.add.js";
                // if ($os->allow("defer_cost", "edit")) include "control/controller.defer.edit.js";
                if ($os->allow("defer_cost", "remove")) include "control/controller.defer.remove.js";
                break;
        }
        ?>
    }).then(() => App.stopLoading())
</script>
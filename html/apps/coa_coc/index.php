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

$panel->setApp("coa_coc", "COA & COC");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'coa');
$panel->setSection(isset($_GET['section']) ? $_GET['section'] : '');

$panel->setMeta(array(
    array("coa", "COA", "fas fa-shield-alt"),
    array("coc", "COC", "fas fa-star"),
    array("export", "คำร้องขอนำสินค้าในราชอาณาจักร", "fas fa-star"),
    array("run_number", "Run Number", "fas fa-filter"),
    array("report", "Report", "fas fa-filter"),
));

?>
<?php
$panel->PageBreadcrumb();
if ($panel->getView() == "printablecoa") {
    include "view/page.coa.view.php";
} else if ($panel->getView() == "printablecoc") {
    include "view/page.coc.view.php";
} else if ($panel->getView() == "printableexport") {
    include "view/page.export.view.php";
} else {
?>
    <div class="row">
        <div class="col-xl-12">
            <?php
            $panel->EchoInterface();
            ?>
        </div>
    </div>
<?php
}
?>
<script>
    var plugins = [
        'apps/delivery/include/interface.js',
        'apps/coa_coc/include/interface.js',
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
            case "coa":
                include "control/controller.coa.view.js";
                if ($os->allow("coa_coc", "edit")) include "../delivery/control/controller.delivery.prepare.js";
                if ($os->allow("coa_coc", "edit")) include "../delivery/control/controller.delivery.delivery.js";
                if ($os->allow("coa_coc", "lookup")) include "../delivery/control/controller.delivery.lookup.js";
                break;
            case "coc":
                include "control/controller.coc.view.js";
                break;
            case "export":
                include "control/controller.export.view.js";
                break;
            case "run_number":
                include "control/controller.run_number.view.js";
                if ($os->allow("coa_coc", "edit")) include "control/controller.run_number.edit.js";
                break;
            case "report":
                include "control/controller.report.view.js";
                break;
        }
        ?>
    }).then(() => App.stopLoading())
</script>
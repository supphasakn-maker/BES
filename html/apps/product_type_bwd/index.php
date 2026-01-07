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

$panel->setApp("product_type_bwd", "Product / Product Type");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'product');

$panel->setMeta(array(
    array("product", "Product", "fa fa-lg fa-building"),
    array("type", "Product Type", "fas fa-database"),
    array("font", "Fonts", "fas fa-font"),
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
        'apps/product_type_bwd/include/interface.js',
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
            case "product":
                include "control/controller.product.view.js";
                if ($os->allow("product_type_bwd", "add")) include "control/controller.product.add.js";
                if ($os->allow("product_type_bwd", "edit")) include "control/controller.product.edit.js";
                if ($os->allow("product_type_bwd", "remove")) include "control/controller.product.remove.js";
                break;
            case "type":
                include "control/controller.type.view.js";
                if ($os->allow("product_type_bwd", "add")) include "control/controller.type.add.js";
                if ($os->allow("product_type_bwd", "edit")) include "control/controller.type.edit.js";
                if ($os->allow("product_type_bwd", "remove")) include "control/controller.type.remove.js";
                break;
            case "font":
                include "control/controller.font.view.js";
                if ($os->allow("product_type_bwd", "add")) include "control/controller.font.add.js";
                if ($os->allow("product_type_bwd", "edit")) include "control/controller.font.edit.js";
                if ($os->allow("product_type_bwd", "remove")) include "control/controller.font.remove.js";
                break;
        }
        ?>
    }).then(() => App.stopLoading())
</script>
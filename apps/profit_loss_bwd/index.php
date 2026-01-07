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

$panel->setApp("profit_loss_bwd", "Profit Loss");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'over_view');


$panel->setMeta(array(
    array("over_view", "Over View", "far fa-user"),
    array("profitloss", "Profit Loss", "far fa-user"),
    array("profit", "Profit", "far fa-user"),

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
        'apps/profit_loss_bwd/include/interface.js',
        'plugins/datatables/dataTables.bootstrap4.min.css',
        'plugins/datatables/responsive.bootstrap4.min.css',
        'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
        'plugins/datatables/rowGroup.dataTables.min.css',
        'plugins/datatables/dataTables.rowGroup.min.js',
        'plugins/select2/css/select2.min.css',
        'plugins/select2/js/select2.min.js',
        'plugins/sweetalert/sweetalert-dev.js',
        'plugins/sweetalert/sweetalert.css',
        'https://cdn.jsdelivr.net/npm/sweetalert2@11',
        'plugins/moment/moment.min.js'
    ];
    App.loadPlugins(plugins, null).then(() => {
        App.checkAll()
        <?php
        switch ($panel->getView()) {
            case "profitloss":
                include "control/controller.profitloss.view.js";
                if ($os->allow("profit_loss_bwd", "add")) include "control/controller.profitloss.matchthb.js";
                if ($os->allow("profit_loss_bwd", "edit")) include "control/controller.profitloss.unmatchthb.js";
                if ($os->allow("profit_loss_bwd", "add")) include "control/controller.profitloss.matchusd.js";
                if ($os->allow("profit_loss_bwd", "edit")) include "control/controller.profitloss.unmatchusd.js";
                if ($os->allow("profit_loss_bwd", "edit")) include "control/controller.profitloss.editspot.js";
                if ($os->allow("profit_loss_bwd", "remove")) include "control/controller.profitloss.removespot.js";
                if ($os->allow("profit_loss_bwd", "add")) include "control/controller.profitloss.add_spot.js";
                if ($os->allow("profit_loss_bwd", "add")) include "control/controller.profitloss.add_usd.js";
                if ($os->allow("profit_loss_bwd", "edit")) include "control/controller.profitloss.editusd.js";
                if ($os->allow("profit_loss_bwd", "remove")) include "control/controller.profitloss.removeusd.js";
				if ($os->allow("profit_loss_bwd", "edit")) include "control/controller.profitloss.split.js";
                if ($os->allow("profit_loss_bwd", "edit")) include "control/controller.profitloss.unsplit.js";






                // if ($os->allow("profit_loss", "edit")) include "control/controller.profitloss.search.js";
                // if ($os->allow("profit_loss", "remove")) include "control/controller.profitloss.removeusd.js";
                break;
            case "profit":
                if ($os->allow("profit_loss_bwd", "edit")) include "control/controller.profit.search.js";
                break;
        }
        ?>
    }).then(() => App.stopLoading())
</script>
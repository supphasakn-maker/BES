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

$panel->setApp("sigmargin_stx", "Sigmargin STX");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'overview');

$panel->setMeta(array(
    array('overview',        "Overview",            'far fa-clipboard'),
    array("silver",            "Silver Trading",    "far fa-shopping-cart"),
    array("transfer",        "Transfer/Deposit",    "far fa-money-bill-alt"),
    array("incoming",        "Incoming Silver",    "far fa-clipboard-check"),
    array("rollover",        "Rollover",            "far fa-user-secret"),
    array("interest",        "Interest",            "far fa-box"),
    array("claim",            "Claim",            "far fa-exclamation-circle"),
    array("cash",            "Interest Summary Monthly",            "far fa-money-bill"),
    array("Initial",        "Initial Margin",    "far fa-building"),
    array("ohter",            "Other",            "far fa-clone"),
    array("daily",            "Daily",            "far fa-calendar"),
    array("int_rate",        "Interest Rate USD",    "far fa-home"),
    array("int_rollover",        "Interest Rollover",    "far fa-home"),
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
        'apps/sigmargin_stx/include/interface.js',
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
            case "overview":
                include "control/controller.overview.view.js";
                break;
            case "silver":
                include "control/controller.silver.view.js";
                if ($os->allow("sigmargin_stx", "add")) include "control/controller.silver.add.js";
                if ($os->allow("sigmargin_stx", "edit")) include "control/controller.silver.edit.js";
                if ($os->allow("sigmargin_stx", "remove")) include "control/controller.silver.remove.js";
                if ($os->allow("sigmargin_stx", "approve")) include "control/controller.silver.approve.js";
                break;
            case "transfer":
                include "control/controller.transfer.view.js";
                if ($os->allow("sigmargin_stx", "add")) include "control/controller.transfer.add.js";
                if ($os->allow("sigmargin_stx", "edit")) include "control/controller.transfer.edit.js";
                if ($os->allow("sigmargin_stx", "remove")) include "control/controller.transfer.remove.js";
                if ($os->allow("sigmargin_stx", "approve")) include "control/controller.transfer.approve.js";
                break;
            case "incoming":
                include "control/controller.incoming.view.js";
                if ($os->allow("sigmargin_stx", "add")) include "control/controller.incoming.add.js";
                if ($os->allow("sigmargin_stx", "edit")) include "control/controller.incoming.edit.js";
                if ($os->allow("sigmargin_stx", "remove")) include "control/controller.incoming.remove.js";
                if ($os->allow("sigmargin_stx", "approve")) include "control/controller.incoming.approve.js";
                break;
            case "rollover":
                include "control/controller.rollover.view.js";
                if ($os->allow("sigmargin_stx", "add")) include "control/controller.rollover.add.js";
                if ($os->allow("sigmargin_stx", "edit")) include "control/controller.rollover.edit.js";
                if ($os->allow("sigmargin_stx", "remove")) include "control/controller.rollover.remove.js";
                break;
            case "interest":
                include "control/controller.interest.view.js";
                if ($os->allow("sigmargin_stx", "add")) include "control/controller.interest.add.js";
                if ($os->allow("sigmargin_stx", "edit")) include "control/controller.interest.edit.js";
                if ($os->allow("sigmargin_stx", "remove")) include "control/controller.interest.remove.js";
                break;
            case "claim":
                include "control/controller.claim.view.js";
                if ($os->allow("sigmargin_stx", "add")) include "control/controller.claim.add.js";
                if ($os->allow("sigmargin_stx", "edit")) include "control/controller.claim.edit.js";
                if ($os->allow("sigmargin_stx", "remove")) include "control/controller.claim.remove.js";
                break;
            case "cash":
                include "control/controller.cash.view.js";
                if ($os->allow("sigmargin_stx", "add")) include "control/controller.cash.add.js";
                if ($os->allow("sigmargin_stx", "edit")) include "control/controller.cash.edit.js";
                if ($os->allow("sigmargin_stx", "remove")) include "control/controller.cash.remove.js";
                break;
            case "Initial":
                include "control/controller.Initial.view.js";
                if ($os->allow("sigmargin_stx", "add")) include "control/controller.Initial.add.js";
                if ($os->allow("sigmargin_stx", "edit")) include "control/controller.Initial.edit.js";
                if ($os->allow("sigmargin_stx", "remove")) include "control/controller.Initial.remove.js";
                break;
            case "ohter":
                include "control/controller.ohter.view.js";
                if ($os->allow("sigmargin_stx", "add")) include "control/controller.ohter.add.js";
                if ($os->allow("sigmargin_stx", "edit")) include "control/controller.ohter.edit.js";
                if ($os->allow("sigmargin_stx", "remove")) include "control/controller.ohter.remove.js";
                break;
            case "daily":
                include "control/controller.daily.view.js";
                if ($os->allow("sigmargin_stx", "add")) include "control/controller.daily.add.js";
                if ($os->allow("sigmargin_stx", "edit")) include "control/controller.daily.edit.js";
                if ($os->allow("sigmargin_stx", "remove")) include "control/controller.daily.remove.js";
                break;
            case "int_rate":
                include "control/controller.int_rate.view.js";
                if ($os->allow("sigmargin_stx", "add")) include "control/controller.int_rate.add.js";
                if ($os->allow("sigmargin_stx", "edit")) include "control/controller.int_rate.edit.js";
                if ($os->allow("sigmargin_stx", "remove")) include "control/controller.int_rate.remove.js";
                break;
            case "int_rollover":
                include "control/controller.int_rollover.view.js";
                if ($os->allow("sigmargin_stx", "add")) include "control/controller.int_rollover.add.js";
                if ($os->allow("sigmargin_stx", "edit")) include "control/controller.int_rollover.edit.js";
                if ($os->allow("sigmargin_stx", "remove")) include "control/controller.int_rollover.remove.js";
                break;
        }
        ?>
    }).then(() => App.stopLoading())
</script>
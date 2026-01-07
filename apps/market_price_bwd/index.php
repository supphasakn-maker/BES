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
//$os->initial_lang("lang");
$panel = new ipanel($dbc, $os->auth);
$ui_form = new iform($dbc, $os->auth);

$rate_exchange = $os->load_variable("rate_exchange");
$rate_spot = $os->load_variable("rate_spot");
$rate_pmdc = $os->load_variable("rate_pmdc");

?>
<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Market Price</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
        <div class="row gutters-sm">
            <div class="col-xl-12 mb-3">
                <?php
                include "view/card.market_price.php";
                ?>
            </div>
        </div>
        <div class="row gutters-sm">
            <div class="col-xl-12 mb-3">
                <?php include "view/card.change_price.php"; ?>
            </div>
        </div>

    </div>
</div>


<script>
    var plugins = [
        'apps/market_price_bwd/include/interface.js',
        'plugins/datatables/dataTables.bootstrap4.min.css',
        'plugins/datatables/responsive.bootstrap4.min.css',
        'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
        'plugins/chart.js/Chart.min.js',
        'plugins/jquery-sparkline/jquery.sparkline.min.js',
        'plugins/select2/js/select2.full.min.js',
        'plugins/select2/css/select2.min.css',
        'plugins/sweetalert/sweetalert-dev.js',
        'plugins/sweetalert/sweetalert.css',
        'plugins/moment/moment.min.js'
    ]


    App.loadPlugins(plugins, null).then(() => {
        /*
        $('.datatable').DataTable({
        	"dom": '<"toolbar">rtp',
        	"pageLength": 5
        });
        */

        $('.select2').select2();
        <?php


        ?>
    }).then(() => App.stopLoading())
</script>
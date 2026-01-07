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
$ui_form = new iform($dbc, $os->auth);
$today = time();
?>

<div class="row gutters-sm">
    <div class="col-12 mb-3">
        <div class="row gutters-sm">
            <div class="col-lg-6 mb-3">
                <?php include "view/card.sales.add.php"; ?>
            </div>
            <div class="col-lg-6 mb-3">
                <?php include "view/card.orders.php"; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Clean up previous instances
    (function() {
        // Destroy existing DataTables
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $('.dataTable').each(function() {
                if ($.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable().destroy();
                }
            });
        }

        // Clear any existing intervals/timeouts
        if (window.salesScreenIntervals) {
            window.salesScreenIntervals.forEach(function(id) {
                clearInterval(id);
            });
        }
        window.salesScreenIntervals = [];
    })();

    // Main application code in IIFE
    (function() {
        'use strict';

        // Create namespace for this module
        window.SalesScreenBWD2 = window.SalesScreenBWD2 || {};

        // Reset module state
        SalesScreenBWD2.state = {
            itemCounter: 0,
            orders: [],
            initialized: false
        };

        var plugins = [
            'apps/sales_screen_bwd_2/include/interface.js',
            'plugins/datatables/bootstrap.bundle.min.js',
            'plugins/datatables/jquery.dataTables.min.js',
            'plugins/datatables/dataTables.bootstrap4.min.css',
            'plugins/datatables/responsive.bootstrap4.min.css',
            'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
            'plugins/chart.js/Chart.min.js',
            'plugins/jquery-sparkline/jquery.sparkline.min.js',
            'plugins/select2/js/select2.full.min.js',
            'plugins/select2/css/select2.min.css',
            'plugins/moment/moment.min.js'
        ];

        App.loadPlugins(plugins, null).then(function() {
            var retryCount = 0;
            var maxRetries = 50;

            var checkLibraries = function() {
                var ready = true;
                retryCount++;

                if (typeof $.fn.DataTable === 'undefined') {
                    ready = false;
                }

                if (typeof $.fn.modal === 'undefined') {
                    ready = false;
                }

                if (ready) {
                    console.log('All libraries ready');
                    App.checkAll();

                    // Initialize controllers in isolated scope
                    (function() {
                        // Use module state instead of global variables
                        var state = window.SalesScreenBWD2.state;

                        <?php
                        include "control/controller.multiorder.view.js";
                        if ($os->allow("sales_screen_bwd_2", "add")) {
                            include "control/controller.multiorder.add.js";
                        }
                        if ($os->allow("sales_screen_bwd_2", "remove")) {
                            include "control/controller.multiorder.remove_each.js";
                            include "control/controller.multiorder.remove.js";
                        }
                        if ($os->allow("sales_screen_bwd_2", "edit")) {
                            include "control/controller.multiorder.edit.js";
                            include "control/controller.multiorder.postpone.js";
                            include "control/controller.multiorder.lock.js";
                            include "control/controller.multiorder.add_delivery.js";
                        }
                        ?>

                        // Mark as initialized
                        state.initialized = true;
                    })();

                } else if (retryCount < maxRetries) {
                    setTimeout(checkLibraries, 100);
                } else {
                    console.error('Failed to load required libraries after ' + maxRetries + ' attempts');
                    App.stopLoading();
                }
            };

            checkLibraries();
        }).then(function() {
            App.stopLoading();
        }).catch(function(error) {
            App.stopLoading();
        });
    })();
</script>
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
    (function() {
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $('.dataTable').each(function() {
                if ($.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable().destroy();
                }
            });
        }

        if (window.salesScreenIntervals) {
            window.salesScreenIntervals.forEach(function(id) {
                clearInterval(id);
            });
        }
        window.salesScreenIntervals = [];
    })();

    if (typeof fn === 'undefined') fn = {};
    if (typeof fn.app === 'undefined') fn.app = {};
    if (typeof fn.app.sales_screen_bwd === 'undefined') fn.app.sales_screen_bwd = {};
    if (typeof fn.app.sales_screen_bwd.multiorder === 'undefined') fn.app.sales_screen_bwd.multiorder = {};
    (function() {
        'use strict';

        window.SalesScreenBWD2 = window.SalesScreenBWD2 || {};
        window.SalesScreenBWD2.state = {
            itemCounter: 0,
            orders: [],
            initialized: false
        };

        var plugins = [
            'apps/sales_screen_bwd/include/interface.js',
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

        function safeStopLoading() {
            if (typeof App !== 'undefined' && typeof App.stopLoading === 'function') {
                App.stopLoading();
            }
        }

        if (typeof App === 'undefined' || typeof App.loadPlugins !== 'function') {
            console.error('App.loadPlugins is not available');
            safeStopLoading();
            return;
        }

        App.loadPlugins(plugins, null)
            .then(function() {
                var retryCount = 0;
                var maxRetries = 50;

                function checkLibraries() {
                    var ready = true;
                    retryCount++;

                    if (typeof $ === 'undefined') {
                        ready = false;
                    } else {
                        if (typeof $.fn.DataTable === 'undefined') {
                            ready = false;
                        }
                        if (typeof $.fn.modal === 'undefined') {
                            ready = false;
                        }
                    }

                    if (ready) {
                        console.log('All libraries ready');

                        if (typeof App.checkAll === 'function') {
                            App.checkAll();
                        }


                        (function() {
                            var state = window.SalesScreenBWD2.state;

                            <?php
                            include "control/controller.multiorder.view.js";

                            if ($os->allow("sales_screen_bwd", "add")) {
                                include "control/controller.multiorder.multiorder.js";
                                echo 'console.log("✅ Controller loaded v' . time() . '");';
                            }

                            if ($os->allow("sales_screen_bwd", "edit")) {
                                include "control/controller.multiorder.remove_each.js";
                                include "control/controller.multiorder.edit.js";
                                include "control/controller.multiorder.add_delivery.js";
                                include "control/controller.multiorder.lock.js";
                                include "control/controller.multiorder.remove_order.js";
                            }
                            ?>

                            state.initialized = true;

                            setTimeout(function() {
                                if (fn.app && fn.app.sales_screen_bwd && fn.app.sales_screen_bwd.multiorder) {
                                    fn.app.sales_screen_bwd.multiorder.calculateShippingPerBox = function(boxItems, boxTotal, boxNumber, isRemote, orderableType) {
                                        let baseShipping = 0;
                                        if (boxTotal >= 1 && boxTotal <= 14999) {
                                            baseShipping = 50;
                                        } else if (boxTotal >= 15000 && boxTotal <= 50000) {
                                            baseShipping = 100;
                                        } else if (boxTotal > 50000) {
                                            baseShipping = 100;
                                        }

                                        let woodenBoxCount = 0;
                                        let premiumBoxCount = 0;

                                        for (let i = 0; i < boxItems.length; i++) {
                                            const item = boxItems[i];
                                            const productType = parseInt(item.product_type);
                                            const amount = parseFloat(item.amount) || 0;

                                            if ([17, 18, 19, 20].indexOf(productType) !== -1) {
                                                woodenBoxCount += amount;
                                            }

                                            if ([13, 14, 15, 16, 21, 22, 23, 24, 25].indexOf(productType) !== -1) {
                                                premiumBoxCount += amount;
                                            }
                                        }

                                        let boxFee = 0;
                                        boxFee += woodenBoxCount * 100;
                                        boxFee += premiumBoxCount * 25;

                                        let remoteFee = 0;

                                        if (isRemote && orderableType === 'post_office') {
                                            remoteFee = 50;
                                        } else {
                                            console.log('    ❌ No Remote Fee');
                                        }

                                        const total = baseShipping + boxFee + remoteFee;

                                        return {
                                            box_number: boxNumber,
                                            base: baseShipping,
                                            box_fee: boxFee,
                                            remote_fee: remoteFee,
                                            total: total,
                                            wooden_count: woodenBoxCount,
                                            premium_count: premiumBoxCount
                                        };
                                    };

                                }
                            }, 1000);

                        })();

                    } else if (retryCount < maxRetries) {
                        setTimeout(checkLibraries, 100);
                    } else {
                        console.error('Failed to load required libraries after ' + maxRetries + ' attempts');
                        safeStopLoading();
                    }
                }

                checkLibraries();
            })
            .then(function() {
                safeStopLoading();
            })
            .catch(function(err) {
                console.error('Plugin load error:', err);
                safeStopLoading();
            });
    })();
</script>
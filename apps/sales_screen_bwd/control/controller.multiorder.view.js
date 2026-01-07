(function () {
    'use strict';

    if (window.QuickOrderTable) {
        if (window.QuickOrderTable.instance && $.fn.DataTable.isDataTable('#tblQuickOrder')) {
            $('#tblQuickOrder').DataTable().destroy();
        }
        if (window.QuickOrderTable.retryTimer) {
            clearTimeout(window.QuickOrderTable.retryTimer);
        }
    }

    window.QuickOrderTable = {
        instance: null,
        retryTimer: null,
        retryCount: 0,
        maxRetries: 50
    };

    function initQuickOrderTable() {
        var qt = window.QuickOrderTable;
        qt.retryCount++;

        if (typeof $ === 'undefined' || typeof $.fn.DataTable === 'undefined') {
            if (qt.retryCount < qt.maxRetries) {
                qt.retryTimer = setTimeout(initQuickOrderTable, 100);
            }
            return;
        }

        if ($('#tblQuickOrder').length === 0) {
            if (qt.retryCount < qt.maxRetries) {
                qt.retryTimer = setTimeout(initQuickOrderTable, 100);
            }
            return;
        }

        if (typeof moment === 'undefined') {
            if (qt.retryCount < qt.maxRetries) {
                qt.retryTimer = setTimeout(initQuickOrderTable, 100);
            }
        }

        if ($.fn.DataTable.isDataTable('#tblQuickOrder')) {
            $('#tblQuickOrder').DataTable().destroy();
        }

        function formatNumber(value, decimals) {
            var num = parseFloat(String(value).replace(/[^\d.-]/g, '')) || 0;

            if (typeof fn !== 'undefined' && fn.ui && fn.ui.numberic && fn.ui.numberic.format) {
                return fn.ui.numberic.format(num, decimals);
            } else {
                if (decimals === 0) {
                    return Math.round(num).toLocaleString('th-TH');
                } else {
                    return num.toLocaleString('th-TH', {
                        minimumFractionDigits: decimals,
                        maximumFractionDigits: decimals
                    });
                }
            }
        }

        function calculateTotals(data) {
            var totals = {
                amount: 0,
                price: 0,
                net: 0,
                box: 0 // นับจำนวนกล่องรวม
            };

            for (var i = 0; i < data.length; i++) {
                var rowData = data[i];
                if (rowData.amount) {
                    totals.amount += parseFloat(String(rowData.amount).replace(/[^\d.-]/g, '')) || 0;
                }
                if (rowData.price) {
                    totals.price += parseFloat(String(rowData.price).replace(/[^\d.-]/g, '')) || 0;
                }
                if (rowData.net) {
                    totals.net += parseFloat(String(rowData.net).replace(/[^\d.-]/g, '')) || 0;
                }
                if (rowData.box_count) {
                    totals.box += parseInt(String(rowData.box_count).replace(/[^\d.-]/g, '')) || 0;
                }
            }

            return totals;
        }

        function updateTotalDisplay(totals) {
            $("#tAmount").html(formatNumber(totals.amount, 0));
            $("#tPrice").html(formatNumber(totals.price, 2));
            $("#tValue").html(formatNumber(totals.net, 2));

            if (document.getElementById('tBox')) {
                $("#tBox").html(formatNumber(totals.box, 0));
            }
        }

        try {
            qt.instance = $('#tblQuickOrder').DataTable({
                paging: false,
                responsive: true,
                bStateSave: false,
                autoWidth: true,
                processing: true,
                serverSide: true,
                language: {
                    processing: "กำลังโหลดข้อมูล...",
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    infoEmpty: "แสดง 0 ถึง 0 จาก 0 รายการ",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                    zeroRecords: "ไม่พบข้อมูล",
                    emptyTable: "ไม่มีข้อมูลในตาราง",
                    paginate: {
                        first: "หน้าแรก",
                        previous: "ก่อนหน้า",
                        next: "ถัดไป",
                        last: "หน้าสุดท้าย"
                    }
                },
                ajax: {
                    url: "apps/sales_screen_bwd/store/store-orders.php",
                    type: "GET",
                    error: function (xhr, error, thrown) {
                        $("#tblQuickOrder_processing").html(
                            '<div class="alert alert-danger">เกิดข้อผิดพลาด: ' + error + '</div>'
                        );
                    },
                    dataSrc: function (json) {
                        if (json.aaData && !json.data) {
                            json.data = json.aaData;
                        }
                        if (json.iTotalRecords && !json.recordsTotal) {
                            json.recordsTotal = parseInt(json.iTotalRecords);
                        }
                        if (json.iTotalDisplayRecords && !json.recordsFiltered) {
                            json.recordsFiltered = parseInt(json.iTotalDisplayRecords);
                        }

                        if (json.error) {
                            console.error('❌ Server Error:', json.error);
                            return [];
                        }

                        return json.data || [];
                    }
                },
                columns: [
                    { data: "created", className: "text-left" },
                    { data: "code", className: "text-center" },
                    { data: "customer_name", className: "text-left" },
                    { data: "amount", className: "text-right" },
                    { data: "price", className: "text-right" },
                    { data: "net", className: "text-right" },
                    { data: "status", className: "text-center" },
                    { data: "platform", className: "text-center" },
                    { data: "sales", className: "text-center" }
                ],
                order: [[0, "desc"]],
                createdRow: function (row, data, index) {
                    if (data.created) {
                        var timeStr;
                        if (typeof moment !== 'undefined') {
                            timeStr = moment(data.created).format("HH:mm:ss");
                        } else {
                            var d = new Date(data.created);
                            timeStr = ('0' + d.getHours()).slice(-2) + ':' +
                                ('0' + d.getMinutes()).slice(-2) + ':' +
                                ('0' + d.getSeconds()).slice(-2);
                        }
                        $('td', row).eq(0).html(timeStr);
                    }

                    var buttons = '';
                    if (data.status == "1") {
                        buttons += '<button class="btn btn-xs btn-outline-dark mr-1" ' +
                            'onclick="fn.app.sales_screen_bwd.multiorder.dialog_edit(' + data.id + ')" ' +
                            'title="แก้ไข"><i class="fas fa-edit fa-xs"></i></button>';

                        buttons += '<button class="btn btn-xs btn-danger mr-1" ' +
                            'onclick="fn.app.sales_screen_bwd.multiorder.dialog_remove_each(' + data.id + ')" ' +
                            'title="ลบ"><i class="fas fa-times fa-xs"></i></button>';

                        buttons += '<a class="btn btn-xs btn-outline-dark mr-1" ' +
                            'href="#apps/schedule_bwd/index.php?view=printablemulti&order_id=' + data.id + '" ' +
                            'target="_blank" title="พิมพ์"><i class="fas fa-print fa-xs"></i></a>';


                        if (data.item_count && parseInt(data.item_count) > 1) {
                            buttons += '<span class="badge badge-info ml-1">' + data.item_count + ' รายการ</span>';
                        }
                        if (data.box_count && parseInt(data.box_count) > 0) {
                            buttons += '<span class="badge badge-primary ml-1">' + data.box_count + ' กล่อง</span>';
                        }
                        $('td', row).eq(6).html(buttons);
                    } else if (data.status == "0") {
                        $('td', row).eq(6).html('<span class="badge badge-danger">ลบแล้ว</span>');
                    } else {
                        if (!data.delivery_date) {
                            buttons += '<span class="badge badge-danger">🔒</span>';
                        }
                        $('td', row).eq(6).html(buttons);
                    }

                    var customerHtml = data.customer_name || '';
                    if (data.phone) {
                        customerHtml += '<br><small class="text-muted">' + data.phone + '</small>';
                    }
                    $('td', row).eq(2).html(customerHtml);

                    if (data.amount) {
                        $('td', row).eq(3).html(formatNumber(data.amount, 0));
                    }
                    if (data.price) {
                        $('td', row).eq(4).html(formatNumber(data.price, 2));
                    }
                    if (data.net) {
                        $('td', row).eq(5).html(formatNumber(data.net, 2));
                    }
                },
                footerCallback: function (row, data, start, end, display) {
                    var displayData = [];
                    for (var i = 0; i < display.length; i++) {
                        displayData.push(data[display[i]]);
                    }
                    var totals = calculateTotals(displayData);
                    updateTotalDisplay(totals);
                },
                initComplete: function (settings, json) {
                    setTimeout(function () {
                        var api = $('#tblQuickOrder').DataTable();
                        var data = api.rows({ page: 'current' }).data().toArray();
                        var totals = calculateTotals(data);
                        updateTotalDisplay(totals);
                    }, 100);
                },
                drawCallback: function (settings) {
                    var api = this.api();
                    var data = api.rows({ page: 'current' }).data().toArray();
                    var totals = calculateTotals(data);
                    updateTotalDisplay(totals);
                }
            });

        } catch (error) {
            console.error('❌ Error creating DataTable:', error);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQuickOrderTable);
    } else {
        initQuickOrderTable();
    }

    window.initQuickOrderTable = initQuickOrderTable;

})();

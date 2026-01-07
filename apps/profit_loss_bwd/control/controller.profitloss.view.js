$("#tblSales").data("selected", []);
$("#tblSales").DataTable({
    responsive: true,
    "pageLength": 100,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "apps/profit_loss_bwd/store/store-sales.php",
        "data": function (d) {
            let date_filter = $("#tblSales_length input[name=date_filter]").val();
            if (date_filter != "") {
                d.date_filter = date_filter;
            }
        }
    },
    "aoColumns": [
        { "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
        { "bSort": true, "data": "code", "class": "text-left" },
        { "bSort": true, "data": "created", "class": "text-left" },
        { "bSort": true, "data": "customer_name", "class": "text-left" },
        { "bSort": true, "data": "calculated_amount", "class": "text-right" },
        { "bSort": true, "data": "total", "class": "text-right" },
        { "bSort": false, "data": "id", "class": "text-center", "sWidth": "120px" }
    ],
    "order": [[1, "asc"]],
    "createdRow": function (row, data, index) {
        var selected = false, checked = "", s = '';
        if ($.inArray(data.DT_RowId, $("#tblSales").data("selected")) !== -1) {
            $(row).addClass("selected");
            selected = true;
        }

        if (data.is_split == 1) {
            $(row).addClass("bg-light");
            $("td", row).eq(1).html('<i class="fas fa-level-up-alt mr-1 text-primary"></i>' + data.code);
        }

        if (data.mapping_true != null) {
            $("td", row).eq(4).html('<span class="text-white font-weight-bold">' + fn.ui.numberic.format(data.calculated_amount, 4) + "</span>");
            $(row).addClass("bg-info text-white font-weight-bold");

            $("td", row).eq(6).html(fn.ui.button("btn btn-xs btn-danger mr-1", "far fa-trash", "fn.app.profit_loss_bwd.profitloss.unmatchthb(" + data.mapping_item_id + ")"));
        }
        else if (data.mapping_true_usd != null) {
            $("td", row).eq(4).html('<span class="text-dark font-weight-bold">' + fn.ui.numberic.format(data.calculated_amount, 4) + "</span>");
            $(row).addClass("bg-warning text-dark font-weight-bold");

            $("td", row).eq(6).html(fn.ui.button("btn btn-xs btn-danger mr-1", "far fa-trash", "fn.app.profit_loss_bwd.profitloss.unmatchusd(" + data.mapping_id_usd + ")"));
        }
        else {
            $("td", row).eq(0).html(fn.ui.checkbox("chk_sales", data.id, selected));

            s = '';
            if (data.is_split == 1 && data.split_id) {
                s += '<button class="btn btn-xs btn-warning mr-1 btn-unsplit" data-split-id="' + data.split_id + '"><i class="fas fa-undo"></i></button>';
            } else if (!data.is_split || data.is_split == 0) {
                s += '<button class="btn btn-xs btn-primary mr-1 btn-split" data-order-id="' + data.id + '" data-max-amount="' + data.calculated_amount + '"><i class="fas fa-cut"></i></button>';
            }

            $("td", row).eq(6).html(s);
        }
    }
}).on('xhr.dt', function (e, settings, json, xhr) {
    $("#tblSales_filter input[name=total_amount]").val(fn.ui.numberic.format(json.total.remain_unmatch, 4));
    $(this).find(".total_amount").html(fn.ui.numberic.format(json.total.remain_total, 4));
    $(this).find(".total").html(fn.ui.numberic.format(json.total.remain_price, 4));
    $(this).find(".total_amountday").html(fn.ui.numberic.format(json.total.remain_matchthbamount, 4));
    $(this).find(".total_match").html(fn.ui.numberic.format(json.total.remain_matchthbday, 4));
    $(this).find(".total_amountusdday").html(fn.ui.numberic.format(json.total.remain_matchusdamount, 4));
    $(this).find(".total_usdmatch").html(fn.ui.numberic.format(json.total.remain_matchusdday, 4));
});

fn.ui.datatable.selectable("#tblSales", "chk_sales");

$(document).on('click', '#tblSales .btn-split', function () {
    var order_id = $(this).data('order-id');
    var max_amount = $(this).data('max-amount');
    fn.app.profit_loss_bwd.profitloss.dialog_split(order_id, max_amount);
});

$(document).on('click', '#tblSales .btn-unsplit', function () {
    var split_id = $(this).data('split-id');
    fn.app.profit_loss_bwd.profitloss.unsplit(split_id);
});



$("#tblPurchase").data("selected", []);
$("#tblPurchase").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "apps/profit_loss_bwd/store/store-purchase.php",
        "data": function (d) {
            let date_filter = $("#tblSales_length input[name=date_filter]").val();
            if (date_filter != "") {
                d.date_filter = date_filter;
            }
        }
    },
    "aoColumns": [
        { "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
        { "bSort": true, "data": "date", "class": "text-center" },
        { "bSort": true, "data": "supplier", "class": "text-center" },
        { "bSort": true, "data": "amount", "class": "text-right" },
        { "bSort": true, "data": "rate_spot", "class": "text-right" },
        { "bSort": true, "data": "rate_pmdc", "class": "text-right" },
        { "bSort": true, "data": "total", "class": "text-right" }
    ], "order": [[1, "asc"]],
    "createdRow": function (row, data, index) {
        var selected = false, checked = "", s = '';
        if ($.inArray(data.DT_RowId, $("#tblPurchase").data("selected")) !== -1) {
            $(row).addClass("selected");
            selected = true;
        }

        $("td", row).eq(1).html('<span title="' + data.id + '">' + data.date + "</span>");

        $("td", row).eq(0).html(fn.ui.checkbox("chk_purchase", data[0], selected));


    }
}).on('xhr.dt', function (e, settings, json, xhr) {
    $("#tblPurchase_filter input[name=total_amount]").val(fn.ui.numberic.format(json.total.remian_unmatch, 4));
    $(this).find(".total").html(fn.ui.numberic.format(json.total.remain_total, 4));
    $(this).find(".total_amount").html(fn.ui.numberic.format(json.total.remain_amount, 4));
});
fn.ui.datatable.selectable("#tblPurchase", "chk_purchase");

$("#tblPurchaseSpot").data("selected", []);
$("#tblPurchaseSpot").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "apps/profit_loss_bwd/store/store-purchase-silver.php",
        "data": function (d) {
            let date_filter = $("#tblSales_length input[name=date_filter]").val();
            if (date_filter != "") {
                d.date_filter = date_filter;
            }
        }
    },
    "aoColumns": [
        { "bSort": true, "data": "date", "class": "text-center" },
        { "bSort": true, "data": "supplier", "class": "text-center" },
        { "bSort": true, "data": "amount", "class": "text-right" },
        { "bSort": true, "data": "rate_spot", "class": "text-right" },
        { "bSort": true, "data": "rate_pmdc", "class": "text-right" },
        {
            "bSort": true,
            "data": "total",
            "class": "text-right",
            "render": function (data, type, row) {
                if (row.currency === 'THB' && row.THBValue !== undefined) {
                    return fn.ui.numberic.format(row.THBValue, 2);
                }
                return fn.ui.numberic.format(data, 2);
            }
        },
        { "bSort": true, "data": "currency", "class": "text-right" },
        { "bSort": false, "data": "id", "class": "text-center", "sWidth": "80px" }
    ],
    "order": [[6, "asc"]],
    "createdRow": function (row, data, index) {
        var s = '';
        s += fn.ui.button("btn btn-xs btn-outline-warning", "far fa-pen", "fn.app.profit_loss_bwd.profitloss.dialog_editspot(" + data[0] + ")");
        s += fn.ui.button("btn btn-xs btn-danger", "far fa-trash", "fn.app.profit_loss_bwd.profitloss.removespot(" + data[0] + ")");
        $("td", row).eq(7).html(s);
        $("td", row).eq(0).html('<span title="' + data.id + '">' + data.date + "</span>");
        if (data.type === 'MTM') {
            $(row).css('background-color', '#FFD700');
        }
    },
    rowGroup: {
        dataSrc: "currency"
    }
}).on('xhr.dt', function (e, settings, json, xhr) {
    $("#tblPurchaseSpot_filter input[name=total_amount]").val(fn.ui.numberic.format(json.total.remian_unmatch, 4));
    $(this).find(".total").html(fn.ui.numberic.format(json.total.remain_total, 2));
    $(this).find(".total_amount").html(fn.ui.numberic.format(json.total.remain_amount, 2));
    $(this).find(".total_thb").html(fn.ui.numberic.format(json.total.remain_total_thb, 3));
    $(this).find(".total_amount_thb").html(fn.ui.numberic.format(json.total.remain_amount_thb, 3));
    updateRecheckTable();
});



$("#tblPurchaseUSD").data("selected", []);
$("#tblPurchaseUSD").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "apps/profit_loss_bwd/store/store-purchase-usd.php",
        "data": function (d) {
            let date_filter = $("#tblSales_length input[name=date_filter]").val();
            if (date_filter != "") {
                d.date_filter = date_filter;
            }
        }
    },
    "aoColumns": [
        { "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
        { "bSort": true, "data": "date", "class": "text-center" },
        { "bSort": true, "data": "bank", "class": "text-center" },
        { "bSort": true, "data": "amount", "class": "text-right" },
        { "bSort": true, "data": "rate_exchange", "class": "text-right" },
        { "bSort": true, "data": "rate_finance", "class": "text-right" },
        { "bSort": true, "data": "type", "class": "text-center" },
        { "bSort": true, "data": "value", "class": "text-right" }
    ], "order": [[1, "asc"]],
    "createdRow": function (row, data, index) {
        var selected = false, checked = "", s = '';
        if ($.inArray(data.DT_RowId, $("#tblPurchaseUSD").data("selected")) !== -1) {
            $(row).addClass("selected");
            selected = true;
        }

        $("td", row).eq(1).html('<span title="' + data.id + '">' + data.date + "</span>");
        $("td", row).eq(0).html(fn.ui.checkbox("chk_purchase_usd", data[0], selected));
    }
}).on('xhr.dt', function (e, settings, json, xhr) {
    $("#tblPurchaseUSD_filter input[name=total_amount]").val(fn.ui.numberic.format(json.total.remian_unmatch, 2));
    $(this).find(".total_amount").html(fn.ui.numberic.format(json.total.remain_total, 2));
});

fn.ui.datatable.selectable("#tblPurchaseUSD", "chk_purchase_usd");


function updateRecheckTable() {
    var spotUSD = parseFloat($('#tblPurchaseSpot .total').text().replace(/,/g, '') || 0);
    var usdTrue = parseFloat($('#tblPurchaseUSDtrue .total_amount').text().replace(/,/g, '') || 0);
    var difference = spotUSD - usdTrue;

    console.log('spotUSD:', spotUSD, 'usdTrue:', usdTrue, 'difference:', difference);

    recheckTable.clear();
    recheckTable.row.add({
        'purchase_spot_usd': fn.ui.numberic.format(spotUSD, 4),
        'purchase_usd_true': fn.ui.numberic.format(usdTrue, 4),
        'difference': fn.ui.numberic.format(difference, 4)
    });
    recheckTable.draw();
}

$("#tblPurchaseUSDtrue").data("selected", []);
$("#tblPurchaseUSDtrue").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "apps/profit_loss_bwd/store/store-purchase-usd-true.php",
        "data": function (d) {
            let date_filter = $("#tblSales_length input[name=date_filter]").val();
            if (date_filter != "") {
                d.date_filter = date_filter;
            }
        }
    },
    "aoColumns": [
        { "bSort": true, "data": "date", "class": "text-center" },
        { "bSort": true, "data": "bank", "class": "text-center" },
        { "bSort": true, "data": "amount", "class": "text-right" },
        { "bSort": true, "data": "rate_exchange", "class": "text-right" },
        { "bSort": true, "data": "rate_finance", "class": "text-right" },
        { "bSort": true, "data": "type", "class": "text-center" },
        { "bSort": true, "data": "value", "class": "text-right" },
        { "bSort": false, "data": "id", "class": "text-right" }
    ], "order": [[1, "asc"]],
    "createdRow": function (row, data, index) {
        s = '';
        s += fn.ui.button("btn btn-xs btn-outline-warning", "far fa-pen", "fn.app.profit_loss_bwd.profitloss.dialog_editusd(" + data[0] + ")");
        s += fn.ui.button("btn btn-xs btn-danger", "far fa-trash", "fn.app.profit_loss_bwd.profitloss.removeusd(" + data[0] + ")");
        $("td", row).eq(7).html(s);

        $("td", row).eq(0).html('<span title="' + data.id + '">' + data.date + "</span>");
        if (data.type === 'MTM') {
            $(row).css('background-color', '#FFD700');
        }
    }
}).on('xhr.dt', function (e, settings, json, xhr) {
    $("#tblPurchaseUSDtrue_filter input[name=total_amount]").val(fn.ui.numberic.format(json.total.remian_unmatch, 2));
    $(this).find(".total_amount").html(fn.ui.numberic.format(json.total.remain_total, 2));
    $(this).find(".total_thb").html(fn.ui.numberic.format(json.total.remain_total_thb, 2));
    updateRecheckTable();
});

$('#tblLoss').DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "lengthChange": false,
    "serverSide": true,
    "searching": false,
    "paging": false,
    "displayLength": 0,

    "ajax": {
        "url": 'apps/profit_loss_bwd/view/card.profit.php',
        "type": 'POST',
        "data": function (d) {
            d.date_filter = $('#tblSales_length input[name=date_filter]').val();
        },
        "dataSrc": ""
    },
    "columns": [
        { "data": "orders", "className": "text-center" },
        { "data": "purchase_usd", "className": "text-center" },
        { "data": "orders_thb", "className": "text-center" },
        { "data": "purchase_thb", "className": "text-center" },
        {
            "data": "profit",
            "className": "text-center",
            "render": function (data, type, row) {
                if (type === 'display') {
                    if (parseFloat(data) < 0) {
                        return '<span style="color: red;">' + data + '</span>';
                    }
                }
                return data;
            }
        }, {
            "data": "profit_thb",
            "className": "text-center",
            "render": function (data, type, row) {
                if (type === 'display') {
                    if (parseFloat(data) < 0) {
                        return '<span style="color: red;">' + data + '</span>';
                    }
                }
                return data;
            }
        }, {
            "data": "total_profit",
            "className": "text-center",
            "render": function (data, type, row) {
                if (type === 'display') {
                    if (parseFloat(data) < 0) {
                        return '<span style="color: red;">' + data + '</span>';
                    }
                }
                return data;
            }
        }
    ]

});
var recheckTable = $('#tblRecheckUSD').DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": false,
    "lengthChange": false,
    "serverSide": false,
    "searching": false,
    "paging": false,
    "destroy": true,
    "data": [],
    "columns": [
        {
            "title": "Purchase Spot USD",
            "data": "purchase_spot_usd",
            "className": "text-center",
            "render": function (data, type, row) {
                return '<strong>' + data + '</strong>';
            }
        },
        {
            "title": "Purchase USD",
            "data": "purchase_usd_true",
            "className": "text-center",
            "render": function (data, type, row) {
                return '<strong>' + data + '</strong>';
            }
        },
        {
            "title": "Difference",
            "data": "difference",
            "className": "text-center",
            "render": function (data, type, row) {
                var numValue = parseFloat(data.replace(/,/g, ''));
                var color = numValue < 0 ? 'red' : (numValue > 0 ? 'green' : 'black');
                return '<strong style="color: ' + color + '; font-size: 1.1em;">' + data + '</strong>';
            }
        }
    ]
});

function updateRecheckTable() {
    var spotUSD = parseFloat($('#tblPurchaseSpot .total').text().replace(/,/g, '') || 0);
    var usdTrue = parseFloat($('#tblPurchaseUSDtrue .total_amount').text().replace(/,/g, '') || 0);
    var difference = spotUSD - usdTrue;

    console.log('spotUSD:', spotUSD, 'usdTrue:', usdTrue, 'difference:', difference);

    // อัปเดต DataTable ด้วย API
    recheckTable.clear();
    recheckTable.row.add({
        'purchase_spot_usd': fn.ui.numberic.format(spotUSD, 4),
        'purchase_usd_true': fn.ui.numberic.format(usdTrue, 4),
        'difference': fn.ui.numberic.format(difference, 4)
    });
    recheckTable.draw();
}

$("#tblSales_length").append('<input onchange=\'$("#tblSales").DataTable().draw();$("#tblPurchaseSpot").DataTable().draw();$("#tblPurchase").DataTable().draw();$("#tblPurchaseUSD").DataTable().draw();$("#tblPurchaseUSDtrue").DataTable().draw(); $("#tblLoss").DataTable().draw();$("#tblRecheckUSD").DataTable().draw();\' type="date" class="form-control form-control-sm" name="date_filter">');
$("#tblSales_filter").append('<br><label>รวม:<input readonly name="total_amount" class="form-control form-control-sm"></label>');
$("#tblPurchase_filter").append('<label>รวม:<input readonly name="total_amount" class="form-control form-control-sm"></label>');
$("#tblPurchaseSpot_filter").append('<label>รวม:<input readonly name="total_amount" class="form-control form-control-sm"></label>');
$("#tblPurchaseUSD_filter").append('<label>รวม:<input readonly name="total_amount" class="form-control form-control-sm"></label>');
$("#tblPurchaseUSDtrue_filter").append('<label>รวม:<input readonly name="total_amount" class="form-control form-control-sm"></label>');

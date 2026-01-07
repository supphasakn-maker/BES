$("#tblPurchase").data("selected", []);
$("#tblPurchase").DataTable({
    responsive: false,
    "paging": false,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": "apps/defer_cost/store/store-purchase.php",
    "aoColumns": [
        { "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
        { "bSort": true, "data": "date" },
        { "bSort": true, "data": "supplier", "class": "text-center" },
        { "bSort": true, "data": "rate_spot", "class": "text-right pr-2" },
        { "bSort": true, "data": "rate_pmdc", "class": "text-right pr-2" },
        { "bSort": true, "data": "amount", "class": "text-right pr-2" },
        { "bSort": true, "data": "spot_value", "class": "text-right pr-2" },
        { "bSort": true, "data": "spot_discount", "class": "text-right pr-2" },
        { "bSort": true, "data": "spot_net", "class": "text-right pr-2" }
    ], "order": [[1, "desc"]],
    "createdRow": function (row, data, index) {
        var selected = false, checked = "", s = '';
        if ($.inArray(data.DT_RowId, $("#tblPurchase").data("selected")) !== -1) {
            $(row).addClass("selected");
            selected = true;
        }
        $("td", row).eq(0).html(fn.ui.checkbox("chk_purchase", data[0], selected));
    }
});
fn.ui.datatable.selectable("#tblPurchase", "chk_purchase");

$("#tblPurchaseDefer").data("selected", []);
$("#tblPurchaseDefer").DataTable({
    responsive: false,
    "paging": false,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": "apps/defer_cost/store/store-defer-spot.php",
    "aoColumns": [
        { "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
        { "bSort": true, "data": "import_date" },
        { "bSort": true, "data": "supplier_name", "class": "text-center" },
        { "bSort": true, "data": "amount", "class": "text-right pr-2" },
        { "bSort": true, "data": "usd", "class": "text-right pr-2" }
    ], "order": [[1, "desc"]],
    "createdRow": function (row, data, index) {
        var selected = false, checked = "", s = '';
        if ($.inArray(data.DT_RowId, $("#tblPurchaseDefer").data("selected")) !== -1) {
            $(row).addClass("selected");
            selected = true;
        }
        $("td", row).eq(0).html(fn.ui.checkbox("chk_new", data[0], selected));
    }
});
fn.ui.datatable.selectable("#tblPurchaseDefer", "chk_new");


$("#tblDefer").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "apps/defer_cost/store/store-defer.php",
    },
    "aoColumns": [
        { "bSortable": true, "data": "date_defer", class: "text-center" },
        { "bSortable": true, "data": "amount", class: "text-center" },
        { "bSortable": true, "data": "value_defer_spot", class: "text-center" },
        { "bSortable": true, "data": "value_net", class: "text-center" },
        { "bSortable": true, "data": "defer", class: "text-center" },
        { "bSortable": true, "data": "name", class: "text-center" },
        { "bSort": true, "data": "id", "class": "text-center" }
    ], "order": [[0, "desc"]],
    "createdRow": function (row, data, index) {
        var selected = false, checked = "", s = '';
        s = '';
        s += fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-trash", "fn.app.defer_cost.defer.remove(" + data[0] + ")");
        s += fn.ui.button("btn btn-xs btn-outline-dark mr-1", "far fa-eye", "fn.dialog.open('apps/defer_cost/view/dialog.defer.lookup.php','#dialog_lookup',{id:" + data[0] + "})");
        $("td", row).eq(6).html(s);
    }
});


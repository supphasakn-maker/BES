$("#tblSilver").data("selected", []);
$("#tblSilver").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "apps/sigmargin_stx/store/store-silver.php",
        "data": function (d) {
            d.date = $("#selcted_date").val();
        }
    },
    "aoColumns": [
        { "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
        { "bSort": true, "data": "type", "sClass": "hidden-xs text-center" },
        { "bSort": true, "data": "purchase_type", "sClass": "hidden-xs text-center" },
        { "bSort": true, "data": "amount", "sClass": "hidden-xs text-center" },
        { "bSort": true, "data": "rate_spot", "sClass": "hidden-xs text-center" },
        // {"bSort":true			,"data":"rate_pmdc" 		, "sClass":"hidden-xs text-center" },
        { "bSort": true, "data": "date", "sClass": "hidden-xs text-center" },
        { "bSort": false, "data": "id", "sClass": "hidden-xs text-center" },
        { "bSort": false, "data": "usd_debit", "sClass": " text-center" },
        { "bSort": false, "data": "usd_credit", "sClass": " text-center" },
        { "bSort": false, "data": "silver_debit", "sClass": " text-center" },
        { "bSort": false, "data": "silver_credit", "sClass": " text-center" }
    ], "order": [[1, "desc"]],
    "createdRow": function (row, data, index) {
        var selected = false, checked = "", s = '';
        if ($.inArray(data.DT_RowId, $("#tblSilver").data("selected")) !== -1) {
            $(row).addClass("selected");
            selected = true;
        }
        $("td", row).eq(0).html(fn.ui.checkbox("chk_silver", data[0], selected));
        s = '';
        s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.sigmargin_stx.silver.dialog_edit(" + data[0] + ")");
        $("td", row).eq(6).html(s);
    }
});
fn.ui.datatable.selectable("#tblSilver", "chk_silver");

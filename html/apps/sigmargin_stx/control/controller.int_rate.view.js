$("#tblInt_rate").data("selected", []);
$("#tblInt_rate").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "apps/sigmargin_stx/store/store-int_rate.php",
        "data": function (d) {
            d.extra = 1;
            //d.date = $("#selcted_date").val();
        }
    },
    "aoColumns": [
        { "bSortable": false, "data": "id", "sClass": "hidden-xs text-center" },
        { "bSort": true, "data": "date", "sClass": "hidden-xs text-center" },
        { "bSort": false, "data": "rate_short", "sClass": "hidden-xs text-center" },
        { "bSort": false, "data": "rate", "sClass": "hidden-xs text-center" },
        { "bSort": false, "data": "id", "sClass": "hidden-xs text-center" }
    ], "order": [[1, "desc"]],
    "createdRow": function (row, data, index) {
        var selected = false, checked = "", s = '';
        if ($.inArray(data.DT_RowId, $("#tblInt_rate").data("selected")) !== -1) {
            $(row).addClass("selected");
            selected = true;
        }
        $("td", row).eq(0).html(fn.ui.checkbox("chk_int_rate", data[0], selected));
        s = '';
        s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.sigmargin_stx.int_rate.dialog_edit(" + data[0] + ")");
        $("td", row).eq(4).html(s);
    }
});
fn.ui.datatable.selectable("#tblInt_rate", "chk_int_rate");

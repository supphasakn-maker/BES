$("#tblInitial").data("selected", []);
$("#tblInitial").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": "apps/sigmargin_stx/store/store-Initial.php",
    "aoColumns": [
        { "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
        { "bSort": true, "data": "date_start", "sClass": "hidden-xs text-center" },
        { "bSort": true, "data": "date_end", "sClass": "hidden-xs text-center" },
        { "bSort": true, "data": "margin", "sClass": "hidden-xs text-center", "sWidth": "110px" },
        { "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "80px" }
    ], "order": [[1, "desc"]],
    "createdRow": function (row, data, index) {
        var selected = false, checked = "", s = '';
        if ($.inArray(data.DT_RowId, $("#tblInitial").data("selected")) !== -1) {
            $(row).addClass("selected");
            selected = true;
        }
        $("td", row).eq(0).html(fn.ui.checkbox("chk_Initial", data[0], selected));
        s = '';
        s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.sigmargin_stx.Initial.dialog_edit(" + data[0] + ")");
        $("td", row).eq(4).html(s);
    }
});
fn.ui.datatable.selectable("#tblInitial", "chk_Initial");

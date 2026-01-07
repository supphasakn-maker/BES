$("#tblClaim").data("selected", []);
$("#tblClaim").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": "apps/sigmargin_stx/store/store-claim.php",
    "aoColumns": [
        { "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
        { "bSort": true, "data": "purchase_type" },
        { "bSort": true, "data": "amount" },
        { "bSort": true, "data": "rate_spot" },
        { "bSort": true, "data": "rate_pmdc" },
        { "bSort": true, "data": "date" },
        { "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "80px" }
    ], "order": [[1, "desc"]],
    "createdRow": function (row, data, index) {
        var selected = false, checked = "", s = '';
        if ($.inArray(data.DT_RowId, $("#tblClaim").data("selected")) !== -1) {
            $(row).addClass("selected");
            selected = true;
        }
        $("td", row).eq(0).html(fn.ui.checkbox("chk_claim", data[0], selected));
        s = '';
        s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.sigmargin_stx.claim.dialog_edit(" + data[0] + ")");
        $("td", row).eq(6).html(s);
    }
});
fn.ui.datatable.selectable("#tblClaim", "chk_claim");

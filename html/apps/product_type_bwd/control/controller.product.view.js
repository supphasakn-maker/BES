$("#tblProduct").data("selected", []);
$("#tblProduct").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": "apps/product_type_bwd/store/store-product.php",
    "aoColumns": [
        { "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
        { "bSort": true, "data": "code", "sClass": "text-center" },
        { "bSort": true, "data": "name", "sClass": "text-center" },
        { "bSort": true, "data": "updated", "sClass": "text-center" },
        { "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "80px" }
    ], "order": [[1, "asc"]],
    "createdRow": function (row, data, index) {
        var selected = false, checked = "", s = '';
        if ($.inArray(data.DT_RowId, $("#tblProduct").data("selected")) !== -1) {
            $(row).addClass("selected");
            selected = true;
        }
        $("td", row).eq(0).html(fn.ui.checkbox("chk_product", data[0], selected));
        s = '';
        s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.product_type_bwd.product.dialog_edit(" + data[0] + ")");
        $("td", row).eq(4).html(s);
    }
});
fn.ui.datatable.selectable("#tblProduct", "chk_product");

$("#tblRecycle").DataTable({
    responsive: true,
    pageLength: 50,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "apps/production_pmr/store/store_recycle.php",
        "data": function (d) {
            d.date_from = $("form[name=filter] input[name=from]").val();
            d.date_to = $("form[name=filter] input[name=to]").val();
        }
    },
    "aoColumns": [
        { "bSort": true, "data": "created", class: "text-center" },
        { "bSort": true, "data": "submited", class: "text-center" },
        { "bSort": true, "data": "round", class: "text-center" },
        { "bSort": true, "data": "weight_out_total", class: "text-center" },
        { "bSort": true, "data": "product_name", class: "text-left" },
        { "bSort": true, "data": "remark", class: "text-center" },
        { "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "120px" }
    ], "order": [[1, "desc"]],
    "createdRow": function (row, data, index) {

        var s = '';
        if (data.status == "0") {
            s += fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-trash", "fn.app.production_pmr.recycle.remove(" + data[0] + ")");
            s += fn.ui.button("btn btn-xs btn-outline-warning mr-1", "far fa-thumbs-up", "fn.app.production_pmr.recycle.dialog_approve(" + data[0] + ")");
        } else {

            s += '<span class="badge badge-warning">Submited</span>';
        }
        $("td", row).eq(6).html(s);
    }
});


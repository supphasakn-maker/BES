$("#tblReport").DataTable({
    responsive: true,
    "pageLength": 100,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": "apps/coa_coc/store/store-run-number-report.php",
    "aoColumns": [
        { "bSort": true, "data": "number", "class": "text-center" },
        { "bSort": true, "data": "number_coc", "class": "text-center" },
        { "bSort": true, "data": "order_id", "class": "text-center" },
        { "bSort": true, "data": "name", "class": "text-center" },
        { "bSort": true, "data": "created", "class": "text-center" },
    ], "order": [[0, "desc"]],
    "createdRow": function (row, data, index) {

    }
});


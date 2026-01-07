$('#tblsaleback').DataTable({
    "paging": false,
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": "apps/sales_back_bwd/store/store-quick_buyorder.php",
    "aoColumns": [
        { "bSortable": true, "data": "created", class: "text-center" },
        { "bSortable": true, "data": "code", class: "text-center" },
        { "bSortable": true, "data": "customer_name", class: "text-center" },
        { "bSortable": true, "data": "amount", class: "text-right" },
        { "bSortable": true, "data": "price", class: "text-right" },
        { "bSortable": true, "data": "net", class: "text-right" },
        { "bSortable": true, "data": "status", class: "text-center" },
        { "bSortable": true, "data": "platform", class: "text-center" },
        { "bSortable": true, "data": "phone", class: "text-center" },
        { "bSortable": true, "data": "sales", class: "text-center" },
    ], "order": [[0, "desc"]],
    "createdRow": function (row, data, index) {
        $('td', row).eq(0).html(moment(data.created).format("HH:mm:ss"));
        var s = '';


        s += fn.ui.button("btn btn-xs btn-outline-dark mr-1", "far fa-pen", "fn.app.sales_back_bwd.sale_back.dialog_edit(" + data[0] + ")");
        s += fn.ui.button("btn btn-xs btn-danger mr-1", "far fa-times", "fn.app.sales_back_bwd.sale_back.remove(" + data[0] + ")");
        $('td', row).eq(6).html(s);
        s += '<a class="btn btn-xs btn-outline-dark mr-1" href="#apps/schedule_bwd/index.php?view=printablebf&order_id=' + data.id + '" target="_blank"><i class="far fa-print"></i></a> ';

        $('td', row).eq(6).html(s);

    },
    "footerCallback": function (row, data, start, end, display) {
        var api = this.api(), data;

        var tAmount = 0, tValue = 0;
        for (i in data) {
            tAmount += parseFloat(data[i].amount);
        }

        $("#tblsaleback [xname=tAmount]").html(fn.ui.numberic.format(tAmount, 4));

    }
});
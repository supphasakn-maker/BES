$("#tblSilver").DataTable({
    responsive: true,
    "pageLength": 50,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "apps/holiday_announce/store/store-holiday.php",
        "data": function (d) {
            d.year = $("form[name=filter] select[name=year]").val();
        }
    },
    "aoColumns": [
        { "bSortable": true, "data": "FisYear", class: "text-center" },
        { "bSortable": true, "data": "PublicHoliday", class: "text-center" },
        { "bSortable": true, "data": "Descripiton", class: "text-center" },
        { "bSortable": false, "data": "id", class: "text-center", "sWidth": "80px" }
    ],
    "order": [[0, 'desc']],
    "createdRow": function (row, data, index) {
        var selected = false, checked = "", s = '';

        s = '';
        s += fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-trash", "fn.app.holiday_announce.holiday.remove(" + data[0] + ")");
        // s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.holiday_announce.holiday.dialog_edit(" + data[0] + ")");
        $("td", row).eq(3).html(s);
    }
});
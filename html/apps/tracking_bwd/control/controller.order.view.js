var date_base = new Date();
var weekday = ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'];

// เอา selected data ออก เนื่องจากไม่ใช้ checkbox แล้ว
$("#tblOrder").DataTable({
    responsive: true,
    "pageLength": 25,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "apps/schedule_bwd/store/store-order_tracking.php",
        "data": function (d) {
            d.date_from = $("form[name=filter] input[name=from]").val();
            d.date_to = $("form[name=filter] input[name=to]").val();
        }
    },
    "aoColumns": [
        // เริ่มจาก column id สำหรับปุ่ม action
        { "bSortable": true, "data": "id", "sWidth": "40px", "class": "text-center" },
        { "bSortable": true, "data": "code", "class": "text-center" },
        { "bSort": true, "data": "customer_name", "class": "text-center" },
        { "bSort": true, "data": "amount", "class": "text-right pr-2" },
        { "bSort": true, "data": "price", "class": "text-right pr-2" },
        { "bSort": true, "data": "platform", "class": "text-right pr-2" },
        { "bSort": true, "data": "net", "class": "text-right pr-2" },
        { "bSort": true, "data": "date", "class": "text-center" },
        { "bSort": true, "data": "delivery_date", "class": "text-center" },
        { "bSort": true, "data": "sales" },
        { "bSort": true, "data": "Tracking" },
        { "bSortable": false, "data": "id", "class": "text-center" }
    ],
    "order": [[8, "asc"]], // ปรับ index เนื่องจากเอา column id และ status badge ออก
    "createdRow": function (row, data, index) {
        // เหลือแต่ปุ่ม edit เท่านั้น
        $("td", row).eq(0).html(fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-pen ", "fn.app.sales_bwd.order.dialog_edit_tracking(" + data[0] + ")"));


        $("td", row).eq(1).html('<a href="#apps/tracking_bwd/index.php?view=printable&order_id=' + data.id + '">' + data.code + '</a>');

        var deliveryDate = '';
        if (data.delivery_date && data.delivery_date !== null && data.delivery_date !== '' && data.delivery_date !== 'null') {
            deliveryDate = String(data.delivery_date);

            if (deliveryDate.indexOf(' ') > -1) {
                deliveryDate = deliveryDate.split(' ')[0];
            }

            if (deliveryDate.indexOf('/') > -1) {
                var parts = deliveryDate.split('/');
                if (parts.length === 3) {
                    deliveryDate = parts[2] + '-' + parts[1] + '-' + parts[0];
                }
            }
        }

        $("td", row).eq(11).html('')
            .attr("data-delivery-date", deliveryDate)
            .attr("data-order-id", data.id)
            .addClass("show_date");
    },
    "drawCallback": function (settings) {
        fn.app.tracking_bwd.order.date_update();
    }
});

// เอาบรรทัดนี้ออก เนื่องจากไม่ใช้ checkbox แล้ว
// fn.ui.datatable.selectable("#tblOrder", "chk_order");

fn.app.tracking_bwd.order.date_update = function () {
    var s = '';
    s += '<table class="table table-xs mb-0">';
    s += '<tbody>';
    s += '<tr>';
    s += '<th width="20" class="p-0 m-0"><button onclick="fn.app.tracking_bwd.order.date_previous()" class="btn btn-xs btn-dark  m-0"><i data-feather="chevron-left"></i></button></th>';

    var headerDates = [];
    for (var i = 0; i < 7; i++) {
        var tempDate = new Date(date_base.getTime() + (i * 24 * 60 * 60 * 1000));
        headerDates.push(tempDate);

        s += '<th width="35" class="text-center">';
        var show = weekday[tempDate.getDay()] + "." + tempDate.getDate();
        s += show;
        s += '</th>';
    }

    s += '<th width="20" class="p-0"><button onclick="fn.app.tracking_bwd.order.date_next()" class="btn btn-xs btn-dark m-0"><i data-feather="chevron-right"></i></button></th>';
    s += '</tr>';
    s += '<tbody>';
    s += '</table>';
    $('#schedule_header').html(s);

    $(".show_date").each(function (index) {
        var deliveryDate = $(this).attr('data-delivery-date');
        var orderId = $(this).attr('data-order-id');

        s = '';
        s += '<table class="table table-xs mb-0">';
        s += '<tbody>';
        s += '<tr>';
        s += '<td width="20"></td>';

        var trucksShown = 0;
        var dateRange = [];

        for (var i = 0; i < 7; i++) {
            var currentDateObj = new Date(date_base.getTime() + (i * 24 * 60 * 60 * 1000));

            var year = currentDateObj.getFullYear();
            var month = ("0" + (currentDateObj.getMonth() + 1)).slice(-2);
            var day = ("0" + currentDateObj.getDate()).slice(-2);
            var currentDate = year + '-' + month + '-' + day;

            dateRange.push(currentDate);

            var showTruck = false;
            if (deliveryDate && deliveryDate !== '' && deliveryDate !== null && deliveryDate !== 'null') {
                if (deliveryDate === currentDate) {
                    showTruck = true;
                    trucksShown++;
                }
            }

            if (showTruck) {
                s += '<td width="35" class="text-center"><i class="fa fa-sm fa-truck text-primary"></i></td>';
            } else {
                s += '<td width="35" class="text-center"><i class="fa fa-sm fa-minus text-muted"></i></td>';
            }
        }

        s += '<td width="20"></td>';
        s += '</tr>';
        s += '<tbody>';
        s += '</table>';
        $(this).html(s);

        if (trucksShown === 0 && deliveryDate) {
        }
    });

    console.log('date_update finished');
};

fn.app.tracking_bwd.order.date_next = function () {
    date_base.setDate(date_base.getDate() + 1);
    fn.app.tracking_bwd.order.date_update();
};

fn.app.tracking_bwd.order.date_previous = function () {
    date_base.setDate(date_base.getDate() - 1);
    fn.app.tracking_bwd.order.date_update();
};
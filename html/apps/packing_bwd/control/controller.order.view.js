/* ========= Globals & Utils ========= */
var date_base = new Date();
var weekday = ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'];
var weekdayFull = ['วันอาทิตย์', 'วันจันทร์', 'วันอังคาร', 'วันพุธ', 'วันพฤหัสบดี', 'วันศุกร์', 'วันเสาร์'];

function pad2(n) { return (n < 10 ? '0' : '') + n; }
function ymd(d) { return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate()); }

// วันทำการถัดไป (ข้ามเสาร์-อาทิตย์)
function nextBusinessDay(d) {
    const day = d.getDay(); // 0=Sun..6=Sat
    const res = new Date(d);
    if (day === 5) { res.setDate(res.getDate() + 3); }      // Fri -> Mon
    else if (day === 6) { res.setDate(res.getDate() + 2); } // Sat -> Mon
    else { res.setDate(res.getDate() + 1); }                // Sun..Thu -> +1
    return res;
}

/* ========= Modal ========= */
function ensureDeliveryModal() {
    if ($('#deliveryDateModal').length) return;

    const modalHtml = `
  <div class="modal fade" id="deliveryDateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
        <div class="modal-header py-2">
          <h6 class="modal-title">ตั้งวันจัดส่ง</h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
        </div>
        <div class="modal-body pt-2">
          <div class="mb-2">ออเดอร์: <b id="ddm-order-code">-</b></div>

          <div class="d-flex flex-column mb-3">
            <button type="button" class="btn btn-success btn-block mb-2" id="btnSetToday" data-date="">
              <i class="fa fa-check-circle mr-1"></i> ส่งวันนี้ (<span id="ddm-today-label"></span>)
            </button>
            <button type="button" class="btn btn-dark btn-block mb-2" id="btnSetTomorrow" data-date="">
              <i class="fa fa-calendar-day mr-1"></i> ส่งพรุ่งนี้ (<span id="ddm-tomorrow-label"></span>)
            </button>
          </div>

          <div class="form-group mb-2">
            <label class="mb-1 font-weight-bold">เลือกวันเอง</label>
            <input type="date" class="form-control" id="ddm-custom-date">
          </div>

          <button type="button" class="btn btn-primary btn-block mt-2" id="btnSetCustom">
            <i class="fa fa-calendar-check mr-1"></i> ยืนยันวันจัดส่ง
          </button>

          <input type="hidden" id="ddm-order-id" value="">
        </div>
      </div>
    </div>
  </div>`;
    $('body').append(modalHtml);
}

/* ========= AJAX ========= */
function submitDeliveryDate(id, delivery_date) {
    $.ajax({
        url: "apps/packing_bwd/xhr/create-delivery.php",
        method: "POST",
        dataType: "json",
        data: { id: id, delivery_date: delivery_date }, // <-- ส่ง date-only
        success: function (resp) {
            if (resp && resp.success) {
                fn.notify.successbox(resp.msg || 'สร้างรายการส่งสินค้าเรียบร้อยแล้ว', 'Success');
                $('#deliveryDateModal').modal('hide');

                // อัปเดตเฉพาะแถวที่เกี่ยวข้อง
                var dt = $("#tblOrder").DataTable();
                var row = dt.row(function (idx, data) { return +data.id === +id; });
                if (row && row.data()) {
                    var rd = row.data();
                    rd.delivery_pack = "1";
                    rd.delivery_pack_updated = (new Date()).toISOString().slice(0, 19).replace('T', ' ');
                    rd.delivery_date = delivery_date;
                    row.data(rd).draw(false);
                } else {
                    setTimeout(function () { dt.ajax.reload(null, false); }, 400);
                }
            } else {
                fn.notify.warnbox((resp && resp.msg) ? resp.msg : 'ไม่สามารถอัปเดตได้', 'Error');
            }
        },
        error: function (xhr, status, error) {
            fn.notify.warnbox("Connection error occurred: " + error, "Error");
        }
    });
}

/* ========= Schedule (7 วัน) ========= */
if (!window.fn) window.fn = {};
if (!fn.app) fn.app = {};
if (!fn.app.packing_bwd) fn.app.packing_bwd = {};
if (!fn.app.packing_bwd.order) fn.app.packing_bwd.order = {};

fn.app.packing_bwd.order.date_update = function () {
    var s = '';
    s += '<table class="table table-xs mb-0"><tbody><tr>';
    s += '<th width="20" class="p-0 m-0"><button onclick="fn.app.packing_bwd.order.date_previous()" class="btn btn-xs btn-dark m-0"><i data-feather="chevron-left"></i></button></th>';

    for (var i = 0; i < 7; i++) {
        var tempDate = new Date(date_base.getTime() + (i * 24 * 60 * 60 * 1000));
        s += '<th width="35" class="text-center">' + (weekday[tempDate.getDay()] + "." + tempDate.getDate()) + '</th>';
    }

    s += '<th width="20" class="p-0"><button onclick="fn.app.packing_bwd.order.date_next()" class="btn btn-xs btn-dark m-0"><i data-feather="chevron-right"></i></button></th>';
    s += '</tr></tbody></table>';
    $('#schedule_header').html(s);

    $(".show_date").each(function () {
        var deliveryDate = $(this).attr('data-delivery-date');
        var s2 = '';
        s2 += '<table class="table table-xs mb-0"><tbody><tr><td width="20"></td>';

        for (var i = 0; i < 7; i++) {
            // แก้ตัวคูณ ms ให้ถูก (24*60*60*1000)
            var currentDateObj = new Date(date_base.getTime() + (i * 24 * 60 * 60 * 1000));
            var currentDate = currentDateObj.getFullYear() + '-' + pad2(currentDateObj.getMonth() + 1) + '-' + pad2(currentDateObj.getDate());
            if (deliveryDate && deliveryDate === currentDate) {
                s2 += '<td width="35" class="text-center"><i class="fa fa-sm fa-truck text-primary"></i></td>';
            } else {
                s2 += '<td width="35" class="text-center"><i class="fa fa-sm fa-minus text-muted"></i></td>';
            }
        }

        s2 += '<td width="20"></td></tr></tbody></table>';
        $(this).html(s2);
    });
};

fn.app.packing_bwd.order.date_next = function () {
    date_base.setDate(date_base.getDate() + 1);
    fn.app.packing_bwd.order.date_update();
};
fn.app.packing_bwd.order.date_previous = function () {
    date_base.setDate(date_base.getDate() - 1);
    fn.app.packing_bwd.order.date_update();
};

/* ========= Document Ready ========= */
$(function () {
    ensureDeliveryModal();

    // ตั้งค่าปุ่มและ date picker ทุกครั้งที่เปิดโมดัล
    $('#deliveryDateModal').on('show.bs.modal', function () {
        var now = new Date();
        var next = nextBusinessDay(now);

        var todayLabel = weekdayFull[now.getDay()] + ' ' + ymd(now);
        var tomorrowLabel = weekdayFull[next.getDay()] + ' ' + ymd(next);

        $('#ddm-today-label').text(todayLabel);
        $('#ddm-tomorrow-label').text(tomorrowLabel);

        // ค่าจริง (date-only) สำหรับส่งไป PHP
        $('#btnSetToday').attr('data-date', ymd(now));
        $('#btnSetTomorrow').attr('data-date', ymd(next));

        // default ให้เลือกเป็นวันพรุ่งนี้ (แก้ได้เองก่อนกด)
        $('#ddm-custom-date').val(ymd(next));
    });

    // ปุ่มลัด: วันนี้ / พรุ่งนี้
    $(document).on('click', '#btnSetToday', function () {
        submitDeliveryDate($('#ddm-order-id').val(), $(this).data('date'));
    });
    $(document).on('click', '#btnSetTomorrow', function () {
        submitDeliveryDate($('#ddm-order-id').val(), $(this).data('date'));
    });

    // เลือกวันเอง
    $(document).on('click', '#btnSetCustom', function () {
        const dateVal = $('#ddm-custom-date').val();
        if (!dateVal) {
            fn.notify.warnbox('กรุณาเลือกวันที่ก่อน', 'Warning');
            return;
        }
        submitDeliveryDate($('#ddm-order-id').val(), dateVal); // ส่ง date-only
    });

    // Init DataTable
    $("#tblOrder").DataTable({
        responsive: true,
        pageLength: 25,
        bStateSave: true,
        autoWidth: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "apps/packing_bwd/store/store-order.php",
            data: function (d) {
                d.date_from = $("form[name=filter] input[name=from]").val();
                d.date_to = $("form[name=filter] input[name=to]").val();
            }
        },
        aoColumns: [
            { bSortable: true, data: "id", sWidth: "40px", class: "text-center" },
            { bSortable: true, data: "code", class: "text-center" },
            { bSortable: true, data: "customer_name", class: "text-center" },
            { bSortable: true, data: "phone", class: "text-center" },
            { bSortable: true, data: "amount", class: "text-right pr-2" },
            { bSortable: true, data: "price", class: "text-right pr-2" },
            { bSortable: true, data: "platform", class: "text-right pr-2" },
            { bSortable: true, data: "net", class: "text-right pr-2" },
            { bSortable: true, data: "date", class: "text-center" },
            { bSortable: true, data: "delivery_date", class: "text-center" },
            { bSortable: true, data: "sales" },
            { bSortable: true, data: "delivery_pack" },
            { bSortable: true, data: "delivery_pack_updated" },
            { bSortable: true, data: "Tracking" },
            { bSortable: false, data: "id", class: "text-center" }
        ],
        order: [[9, "asc"]],
        createdRow: function (row, data) {
            // ปุ่มแก้ไข tracking
            $("td", row).eq(0).html(
                fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-pen ",
                    "fn.app.sales_screen_bwd_2.multiorder.dialog_edit_tracking(" + data.id + ")")
            );

            // Badge สถานะแพ็ค (คลิกเปิด modal ได้)
            if (String(data.delivery_pack) === "1") {
                $("td", row).eq(11).html(
                    '<span class="badge badge-success badge-pack" style="font-size:16px; cursor:pointer;" ' +
                    'data-id="' + data.id + '" data-code="' + data.code + '">' +
                    '<i class="fa fa-check-circle mr-1"></i> แพ็คแล้ว</span>'
                );
            } else {
                $("td", row).eq(11).html(
                    '<span class="badge badge-danger badge-pack" style="font-size:16px; cursor:pointer;" ' +
                    'data-id="' + data.id + '" data-code="' + data.code + '">' +
                    '<i class="fa fa-times-circle mr-1"></i> ยังไม่ได้แพ็ค</span>'
                );
            }

            // ลิงก์เลขออเดอร์
            $("td", row).eq(1).html(
                '<a class="order-link" href="#apps/schedule_bwd_2/index.php?view=printablemulti&order_id=' +
                data.id + '">' + data.code + '</a>'
            );

            // payload ช่อง schedule (index 14)
            var deliveryDate = '';
            if (data.delivery_date && data.delivery_date !== 'null') {
                deliveryDate = String(data.delivery_date);
                if (deliveryDate.indexOf(' ') > -1) deliveryDate = deliveryDate.split(' ')[0];
                if (deliveryDate.indexOf('/') > -1) {
                    var parts = deliveryDate.split('/');
                    if (parts.length === 3) deliveryDate = parts[2] + '-' + parts[1] + '-' + parts[0];
                }
            }
            $("td", row).eq(14).html('')
                .attr("data-delivery-date", deliveryDate)
                .attr("data-order-id", data.id)
                .addClass("show_date");
        },
        drawCallback: function () {
            fn.app.packing_bwd.order.date_update();
        }
    });

    // คลิก badge เปิด modal
    $(document).on('click', '.badge-pack', function () {
        ensureDeliveryModal();
        var id = $(this).data('id');
        var code = $(this).data('code');
        $('#ddm-order-id').val(id);
        $('#ddm-order-code').text(code);
        $('#deliveryDateModal').modal('show');
    });

    // ปุ่มค้นหา (ถ้ามี)
    $(document).on('click', '.btn-lookup', function () {
        $("#tblOrder").DataTable().draw();
    });
});

$("#tblDelivery").data("selected", []);


if ($.fn.DataTable.isDataTable('#tblDelivery')) {
	$('#tblDelivery').DataTable().destroy();
}


$('#tblDelivery').html(`
    <thead>
        <tr>
            <th class="text-center">
                <span type="checkall" control="chk_delivery" class="far fa-lg fa-square"></span>
            </th>
            <th class="text-center">Order No.</th>
            <th class="text-center">Delivery No.</th>
            <th class="text-center">ประเภท</th>
            <th class="text-center">ชื่อลูกค้า</th>
            <th class="text-center">แท่ง</th>
            <th class="text-center">ราคาขาย</th>
            <th class="text-center">ราคาขาย - ส่วนลด</th>
            <th class="text-center">วันที่สั่งซื้อ</th>
            <th class="text-center">วันที่ส่ง</th>
            <th class="text-center">เงื่อนไขการชำระเงิน</th>
            <th class="text-center">บิล</th>
            <th class="text-center">สถานะ</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
`);

$("#tblDelivery").DataTable({
	responsive: true,
	"bStateSave": false,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/prepare_bwd/store/store-delivery.php",
		"data": function (d) {
			d.date_from = $("form[name=filter] input[name=from]").val();
			d.date_to = $("form[name=filter] input[name=to]").val();
			d._cache = Math.random();
		}
	},
	"aoColumns": [
		{ "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
		{ "bSort": true, "data": "code", "class": "text-center" },
		{ "bSort": true, "data": "order_code", "class": "text-center" },
		{ "bSort": true, "data": "type", "class": "text-center" },
		{ "bSort": true, "data": "customer_name", "class": "text-center" },
		{ "bSort": true, "data": "amount", "class": "text-right pr-2" },
		{ "bSort": true, "data": "price", "class": "text-right pr-2" },
		{ "bSort": true, "data": "net", "class": "text-right pr-2" },
		{ "bSort": true, "data": "date", "class": "text-center" },
		{ "bSort": true, "data": "delivery_date", "class": "text-center" },
		{ "bSortable": false, "data": "payment_note", "class": "text-center" },
		{ "bSortable": false, "data": "billing_id", "class": "text-center" },
		{ "bSortable": false, "data": "status", "class": "text-center" }
	],
	"order": [[3, "desc"]],
	"createdRow": function (row, data, index) {
		var selected = false, checked = "", s = '';
		if ($.inArray(data.DT_RowId, $("#tblDelivery").data("selected")) !== -1) {
			$(row).addClass("selected");
			selected = true;
		}


		$("td", row).eq(0).html(fn.ui.checkbox("chk_order", data.id, selected));


		if (data.type == "1") {
			$("td", row).eq(3).html('<span class="badge badge-warning">normal</span>');
		} else if (data.type == "2") {
			$("td", row).eq(3).html('<span class="badge badge-primary">combined</span>');
		}


		s = '';
		if (data.payment_note == null || data.payment_note == "" || data.payment_note == "null") {
			// เช็ค delivery_id ก่อนใช้
			if (data.delivery_id && data.delivery_id != "0") {
				s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-dollar-sign", "fn.app.sales_bwd.delivery.dialog_payment(" + data.delivery_id + ")");
			} else {
				s += '<span class="text-muted">ไม่มี delivery</span>';
			}
		} else {
			try {
				var obj = jQuery.parseJSON(data.payment_note);
				// เช็ค delivery_id ก่อนใช้
				if (data.delivery_id && data.delivery_id != "0") {
					s += '<a href="javascript:;" onclick="fn.app.sales_bwd.delivery.dialog_payment(' + data.delivery_id + ')">';
					s += obj.bank + "," + obj.payment;
					s += '</a>';
				} else {
					s += obj.bank + "," + obj.payment;
				}
			} catch (e) {
				// เช็ค delivery_id ก่อนใช้
				if (data.delivery_id && data.delivery_id != "0") {
					s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-dollar-sign", "fn.app.sales_bwd.delivery.dialog_payment(" + data.delivery_id + ")");
				} else {
					s += '<span class="text-muted">ไม่มี delivery</span>';
				}
			}
		}
		$("td", row).eq(10).html(s);
		$("td", row).eq(10).html(s);


		s = '';
		var hasBilling = data.billing_id && data.billing_id !== "" && data.billing_id.trim() !== "";

		if (!hasBilling) {
			// เช็ค delivery_id ก่อนใช้
			if (data.delivery_id && data.delivery_id != "0") {
				s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-file", "fn.app.sales_bwd.delivery.dialog_billing(" + data.delivery_id + ")");
			} else {
				s += '<span class="text-muted">ไม่มี delivery</span>';
			}
		} else {
			// เช็ค delivery_id ก่อนใช้
			if (data.delivery_id && data.delivery_id != "0") {
				s += '<a href="javascript:;" onclick="fn.app.sales_bwd.delivery.dialog_billing(' + data.delivery_id + ')">';
				s += data.billing_id;
				s += '</a>';
			} else {
				s += data.billing_id;
			}
		}
		$("td", row).eq(11).html(s);

		if (hasBilling) {
			$("td", row).eq(12).html('<span class="badge badge-primary">ตัดสต๊อคแล้ว</span>');
		} else {
			$("td", row).eq(12).html('<span class="badge badge-warning">รอการจัดเตรียม</span>');
		}
	}
});

fn.ui.datatable.selectable("#tblDelivery", "chk_order");


function refreshDeliveryTable() {
	$("#tblDelivery").DataTable().ajax.reload(null, false);
}


function clearDeliveryTableCache() {
	if ($.fn.DataTable.isDataTable('#tblDelivery')) {
		$('#tblDelivery').DataTable().state.clear();
	}
	location.reload();
}
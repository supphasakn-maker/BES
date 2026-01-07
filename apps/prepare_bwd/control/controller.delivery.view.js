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
  <tbody></tbody>
`);

$("#tblDelivery").DataTable({
	responsive: true,
	bStateSave: false,
	autoWidth: true,
	processing: true,
	serverSide: true,
	ajax: {
		url: "apps/prepare_bwd/store/store-delivery.php",
		type: "GET",
		dataType: "json",
		data: function (d) {
			d.date_from = $("form[name=filter] input[name=from]").val();
			d.date_to = $("form[name=filter] input[name=to]").val();
			d._cache = Math.random();
		},
		dataSrc: function (json) {
			if (typeof json === "string") {
				const head = json.trim().slice(0, 1);
				if (head === "<") {
					console.error("Server returned HTML:", json.slice(0, 200));
					alert("โหลดข้อมูลไม่สำเร็จ (server ตอบ HTML)");
					return [];
				}
				try { json = JSON.parse(json); } catch (e) {
					console.error("Invalid JSON:", e);
					alert("โหลดข้อมูลไม่สำเร็จ (JSON ไม่ถูกต้อง)");
					return [];
				}
			}
			return Array.isArray(json?.data) ? json.data : [];
		},
		error: function (xhr) {
			console.error("AJAX failed", xhr.status, xhr.statusText);
			console.log("Response head:", (xhr.responseText || "").slice(0, 200));
			alert("เชื่อมต่อเซิร์ฟเวอร์ไม่ได้ หรือข้อมูลไม่ถูกต้อง");
		}
	},
	columns: [
		{ data: "id", orderable: false, className: "hidden-xs text-center", width: "20px" },
		{ data: "code", className: "text-center" },          // Order No. (รหัสออเดอร์)
		{ data: "order_code", className: "text-center" },    // Delivery No. (โค้ด delivery)
		{ data: "type", className: "text-center" },
		{ data: "customer_name", className: "text-center" },
		{ data: "amount", className: "text-right pr-2" },
		{ data: "price", className: "text-right pr-2" },
		{ data: "net", className: "text-right pr-2" },
		{ data: "date", className: "text-center" },
		{
			data: "delivery_date",
			className: "text-center",
			render: function (d) { return d ? d : '-'; } // แสดง '-' เมื่อว่าง
		},
		{ data: "payment_note", className: "text-center" },
		{ data: "billing_id", className: "text-center" },
		{ data: "status", className: "text-center" },
		// ใช้ภายใน ไม่แสดง
		{ data: "delivery_id", visible: false, searchable: false },
	],
	order: [[8, "desc"]], // วันที่ซื้อ
	createdRow: function (row, data) {
		var selected = $.inArray(data.DT_RowId, $("#tblDelivery").data("selected")) !== -1;
		$("td", row).eq(0).html(fn.ui.checkbox("chk_order", data.id, selected));

		// ประเภท
		var typeCell = '<span class="badge badge-secondary">-</span>';
		if (String(data.type) === "1") typeCell = '<span class="badge badge-warning">normal</span>';
		else if (String(data.type) === "2") typeCell = '<span class="badge badge-primary">combined</span>';
		$("td", row).eq(3).html(typeCell);

		// payment_note
		(function () {
			var s = '';
			var hasDelivery = data.delivery_id && String(data.delivery_id) !== "0";
			if (!data.payment_note) {
				s = hasDelivery
					? fn.ui.button("btn btn-xs btn-outline-dark", "far fa-dollar-sign",
						"fn.app.sales_bwd.delivery.dialog_payment(" + data.delivery_id + ")")
					: '<span class="text-muted">ไม่มี delivery</span>';
			} else {
				try {
					var obj = typeof data.payment_note === 'string' ? jQuery.parseJSON(data.payment_note) : data.payment_note;
					var text = ((obj?.bank || '') + (obj?.payment ? ("," + obj.payment) : '')).replace(/^,/, '');
					s = hasDelivery
						? '<a href="javascript:;" onclick="fn.app.sales_bwd.delivery.dialog_payment(' + data.delivery_id + ')">' + (text || '-') + '</a>'
						: (text || '-');
				} catch (e) {
					s = hasDelivery
						? fn.ui.button("btn btn-xs btn-outline-dark", "far fa-dollar-sign",
							"fn.app.sales_bwd.delivery.dialog_payment(" + data.delivery_id + ")")
						: '<span class="text-muted">ไม่มี delivery</span>';
				}
			}
			$("td", row).eq(10).html(s);
		})();

		// บิล
		(function () {
			var s = '';
			var hasBilling = !!(data.billing_id && String(data.billing_id).trim() !== "");
			var hasDelivery = data.delivery_id && String(data.delivery_id) !== "0";
			if (!hasBilling) {
				s = hasDelivery
					? fn.ui.button("btn btn-xs btn-outline-dark", "far fa-file",
						"fn.app.sales_bwd.delivery.dialog_billing(" + data.delivery_id + ")")
					: '<span class="text-muted">ไม่มี delivery</span>';
			} else {
				s = hasDelivery
					? '<a href="javascript:;" onclick="fn.app.sales_bwd.delivery.dialog_billing(' + data.delivery_id + ')">' + data.billing_id + '</a>'
					: data.billing_id;
			}
			$("td", row).eq(11).html(s);
		})();

		// สถานะ
		if (data.billing_id && String(data.billing_id).trim() !== "") {
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

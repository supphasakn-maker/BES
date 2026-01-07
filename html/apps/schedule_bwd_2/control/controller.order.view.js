var date_base = new Date();
var weekday = ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'];

$("#tblOrder").data("selected", []);

if ($.fn.DataTable.isDataTable('#tblOrder')) {
	$('#tblOrder').DataTable().destroy();
}

$("#tblOrder").DataTable({
	responsive: true,
	"pageLength": 25,
	"bStateSave": false,
	"autoWidth": true,
	"processing": true,
	"serverSide": true,
	"deferRender": true,
	"searchDelay": 400,
	"orderMulti": false,
	"stateDuration": 0,
	"stateDuration": 0,
	"ajax": {
		"url": "apps/schedule_bwd_2/store/store-order.php",
		"type": "GET",
		"data": function (d) {
			d.date_from = $("form[name=filter] input[name=from]").val();
			d.date_to = $("form[name=filter] input[name=to]").val();
		},
		"dataFilter": function (data) {
			try {
				var i = data.indexOf('{');
				if (i > 0) data = data.slice(i);
				JSON.parse(data);
				return data;
			} catch (e) {
				console.error("Response not JSON:", data);
				return '{"error":"Invalid JSON","message":"' + (e.message || 'parse error') + '","data":[]}';
			}
		},
		"dataSrc": function (json) {
			if (json.error) {
				console.error('Server error:', json);
				alert('เกิดข้อผิดพลาด: ' + (json.message || json.error));
				return [];
			}
			return json.data || [];
		},
		"error": function (xhr, error, thrown) {
			console.error("Ajax Error", { status: xhr.status, statusText: xhr.statusText, error, thrown, responseText: xhr.responseText });
			alert(
				'โหลดข้อมูลล้มเหลว: ' + xhr.status + ' ' + xhr.statusText + '\n' +
				(xhr.responseText ? xhr.responseText.slice(0, 800) : '')
			);
		}
	},

	"aoColumns": [
		{ "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
		{ "bSortable": false, "data": null, "sWidth": "40px", "class": "text-center" },
		{ "bSortable": false, "data": null, "sWidth": "40px", "class": "text-center" },
		{ "bSortable": true, "data": "code", "class": "text-center" },
		{ "bSort": true, "data": "delivery_code", "class": "text-center" },
		{ "bSort": true, "data": "customer_name", "class": "text-center" },
		{ "bSort": true, "data": "username", "class": "text-center" },
		{ "bSort": true, "data": "amount", "class": "text-right pr-2" },
		{ "bSort": true, "data": "price", "class": "text-right pr-2" },
		{ "bSort": true, "data": "platform", "class": "text-center" },
		{ "bSort": true, "data": "net", "class": "text-right pr-2" },
		{ "bSort": true, "data": "date", "class": "text-center" },
		{ "bSort": true, "data": "delivery_date", "class": "text-center" },
		{ "bSortable": false, "data": null, "class": "text-center" },
		{ "bSort": true, "data": "delivery_pack", "class": "text-center" },
		{ "bSort": true, "data": "sales", "class": "text-center" },
		{ "bSort": true, "data": "Tracking", "class": "text-center" },
		{ "bSortable": false, "data": null, "class": "text-center" }
	],
	"order": [[11, "asc"]],
	"createdRow": function (row, data, index) {
		try {

			if (!data) {
				console.error('No data received for row:', index);
				return;
			}

			var selected = false, checked = "", s = '';
			if ($.inArray(data.DT_RowId, $("#tblOrder").data("selected")) !== -1) {
				$(row).addClass("selected");
				selected = true;
			}


			if (data.platform === 'Shopee' || data.platform === 'TikTok') {
				$(row).css('background-color', '#ebeef1ff');
			}


			$("td", row).eq(0).html(fn.ui.checkbox("chk_order", data.id, selected));


			$("td", row).eq(1).html('<span class="badge badge-dark">created</span>');


			$("td", row).eq(2).html(fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-pen ", "fn.app.sales_screen_bwd_2.multiorder.dialog_edit(" + data.id + ")"));


			$("td", row).eq(3).html('<a href="#apps/schedule_bwd_2/index.php?view=printablemulti&order_id=' + data.id + '">' + data.code + '</a>');
			$("td", row).eq(1).html('<a href="#apps/schedule_bwd_2/index.php?view=printablemulti2&order_id=' + data.id + '"><i class="fas fa-print fa-xl"></i></a>');

			if (String(data.delivery_pack) === "1") {
				$("td", row).eq(14).html(
					'<span class="badge badge-success" style="font-size:16px; cursor:pointer;" ' +
					'data-id="' + data.id + '" data-code="' + data.code + '">' +
					'<i class="fa fa-thumbs-up mr-1"></i> </span>'

				);
			} else {
				$("td", row).eq(14).html(
					'<span class="badge badge-danger" style="font-size:16px; cursor:pointer;" ' +
					'data-id="' + data.id + '" data-code="' + data.code + '"><i class="fa fa-thumbs-down mr-1"></i></span>'

				);
			}


			const $cell = $("td", row).eq(13).empty();

			if (data.delivery_id == null || data.delivery_id === '' || data.delivery_id === 'null' || String(data.delivery_pack) === "0") {
				$cell
					.append(
						fn.ui.button("btn btn-xs btn-outline-danger", "far fa-truck ",
							"fn.app.sales_screen_bwd_2.multiorder.dialog_add_delivery(" + data.id + ")")
					)
					.append(
						fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-lock ",
							"fn.app.sales_screen_bwd_2.multiorder.dialog_lock(" + data.id + ")")
					);


			} else if (String(data.delivery_pack) === "0" && data.delivery_id !== '') {
				$cell
					.append(
						fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-lock ",
							"fn.app.sales_screen_bwd_2.multiorder.dialog_lock(" + data.id + ")")
					);
			} else if (String(data.delivery_pack) === "1") {
				$cell.append('');
			}


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


			$("td", row).eq(17).html('')
				.attr("data-delivery-date", deliveryDate)
				.attr("data-order-id", data.id)
				.addClass("show_date");

		} catch (error) {

		}
	},
	"drawCallback": function (settings) {



		var api = this.api();
		var data = api.rows().data();


		setTimeout(function () {
			fn.app.schedule_bwd_2.order.date_update();
		}, 100);
	},
	"initComplete": function (settings, json) {
	}
});

fn.ui.datatable.selectable("#tblOrder", "chk_order");

fn.app.schedule_bwd_2.order.date_update = function () {

	var s = '';
	s += '<table class="table table-xs mb-0">';
	s += '<tbody>';
	s += '<tr>';
	s += '<th width="20" class="p-0 m-0"><button onclick="fn.app.schedule_bwd_2.order.date_previous()" class="btn btn-xs btn-dark  m-0"><i data-feather="chevron-left"></i></button></th>';


	var headerDates = [];
	for (var i = 0; i < 7; i++) {
		var tempDate = new Date(date_base.getTime() + (i * 24 * 60 * 60 * 1000));
		headerDates.push(tempDate);

		s += '<th width="35" class="text-center">';
		var show = weekday[tempDate.getDay()] + "." + tempDate.getDate();
		s += show;
		s += '</th>';
	}

	s += '<th width="20" class="p-0"><button onclick="fn.app.schedule_bwd_2.order.date_next()" class="btn btn-xs btn-dark m-0"><i data-feather="chevron-right"></i></button></th>';
	s += '</tr>';
	s += '</tbody>';
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
		s += '</tbody>';
		s += '</table>';
		$(this).html(s);


		if (trucksShown === 0 && deliveryDate) {
		}
	});

};


fn.app.schedule_bwd_2.order.date_next = function () {
	date_base.setDate(date_base.getDate() + 1);
	fn.app.schedule_bwd_2.order.date_update();
};

fn.app.schedule_bwd_2.order.date_previous = function () {
	date_base.setDate(date_base.getDate() - 1);
	fn.app.schedule_bwd_2.order.date_update();
};


fn.app.schedule_bwd_2.order.clearState = function () {

	if (localStorage.getItem('DataTables_tblOrder_/')) {
		localStorage.removeItem('DataTables_tblOrder_/');
	}


	if ($.fn.DataTable.isDataTable('#tblOrder')) {
		$('#tblOrder').DataTable().ajax.reload(null, false);
	}
};


fn.app.schedule_bwd_2.order.refresh = function () {
	if ($.fn.DataTable.isDataTable('#tblOrder')) {
		$('#tblOrder').DataTable().ajax.reload(function (json) {
		}, false);
	}
};


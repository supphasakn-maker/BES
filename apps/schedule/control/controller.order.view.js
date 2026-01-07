
var date_base = new Date();
var weekday = ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'];

if (typeof fn === 'undefined') window.fn = {};
if (!fn.app) fn.app = {};
if (!fn.app.schedule) fn.app.schedule = {};
if (!fn.app.schedule.order) fn.app.schedule.order = {};


function initializeOrderTable() {
	$("#tblOrder").data("selected", []);

	$("#tblOrder").DataTable({
		responsive: true,
		pageLength: 25,
		stateSave: true,
		autoWidth: true,
		processing: true,
		serverSide: true,
		ajax: {
			url: "apps/schedule/store/store-order.php",
			data: function (d) {
				d.date_from = $("form[name=filter] input[name=from]").val();
				d.date_to = $("form[name=filter] input[name=to]").val();
			},
			error: function (xhr, error, thrown) {
				if (xhr.status === 0) {
					alert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้ กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต');
				} else {
					alert('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + thrown);
				}
			}
		},
		columns: [
			{ sortable: false, data: "id", className: "hidden-xs text-center", width: "20px" },
			{ sortable: false, data: "id", width: "40px", className: "text-center" },
			{ sortable: true, data: "id", width: "40px", className: "text-center" },
			{ sortable: true, data: "code", className: "text-center" },
			{ sortable: true, data: "delivery_code", className: "text-center" },
			{ sortable: true, data: "customer_name", className: "text-center" },
			{ sortable: true, data: "amount", className: "text-right pr-2" },
			{ sortable: true, data: "price", className: "text-right pr-2" },
			{ sortable: true, data: "vat", className: "text-right pr-2" },
			{ sortable: true, data: "net", className: "text-right pr-2" },
			{ sortable: true, data: "date", className: "text-center" },
			{ sortable: true, data: "delivery_date", className: "text-center" },
			{ sortable: false, data: "id", className: "text-center" },
			{ sortable: true, data: "sales" },
			{ sortable: false, data: "id", className: "text-center" }
		],
		order: [[4, "desc"]],
		createdRow: function (row, data, index) {
			createTableRow(row, data, index);
		},
		drawCallback: function (settings) {
			fn.app.schedule.order.date_update();
		},
		language: {
			processing: "กำลังประมวลผล...",
			search: "ค้นหา:",
			lengthMenu: "แสดง _MENU_ รายการ",
			info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
			infoEmpty: "แสดง 0 ถึง 0 จาก 0 รายการ",
			infoFiltered: "(กรองข้อมูล _MAX_ ทุกรายการ)",
			loadingRecords: "กำลังโหลดข้อมูล...",
			zeroRecords: "ไม่พบข้อมูล",
			emptyTable: "ไม่พบข้อมูลในตาราง",
			paginate: {
				first: "หน้าแรก",
				previous: "ก่อนหน้า",
				next: "ถัดไป",
				last: "หน้าสุดท้าย"
			}
		}
	});


	fn.ui.datatable.selectable("#tblOrder", "chk_order");
}


function createTableRow(row, data, index) {
	var selected = false;
	var s = '';

	if ($.inArray(data.DT_RowId, $("#tblOrder").data("selected")) !== -1) {
		$(row).addClass("selected");
		selected = true;
	}

	var cells = $("td", row);


	cells.eq(0).html(fn.ui.checkbox("chk_order", data[0], selected));


	cells.eq(1).html(createActionButton("split", data[0]));


	cells.eq(2).html(createActionButton("edit", data[0]));


	cells.eq(3).html(createActionButton("delete", data[0]));


	var linkUrl = getOrderLinkUrl(data);
	cells.eq(3).html('<a href="' + linkUrl + '" class="order-link">' + data.code + '</a>');


	if (data.delivery_id == null) {
		cells.eq(11).html(createActionButton("delivery", data[0]));
	}


	if (data.delivery_id != null) {
		s = createActionButton("postpone", data[0]) + createActionButton("lock", data[0]);
		cells.eq(12).html(s);
	} else {
		cells.eq(12).html('<span class="badge badge-danger">Lock</span>');
	}


	cells.eq(14).html('').attr("date", data.delivery_date).addClass("show_date");
}

function createActionButton(type, id) {
	var buttonConfig = {
		split: {
			class: "btn btn-xs btn-outline-dark",
			icon: "far fa-cut",
			onclick: "fn.app.sales.order.dialog_split(" + id + ")"
		},
		edit: {
			class: "btn btn-xs btn-outline-danger mr-1",
			icon: "far fa-pen",
			onclick: "fn.app.sales.order.dialog_edit(" + id + ")"
		},
		delete: {
			class: "btn btn-xs btn-danger mr-1",
			icon: "far fa-trash",
			onclick: "fn.app.sales.order.dialog_remove_each(" + id + ")"
		},
		delivery: {
			class: "btn btn-xs btn-outline-danger",
			icon: "far fa-truck",
			onclick: "fn.app.sales.order.dialog_add_delivery(" + id + ")"
		},
		postpone: {
			class: "btn btn-xs btn-outline-warning mr-1",
			icon: "far fa-truck",
			onclick: "fn.app.sales.order.dialog_postpone(" + id + ")"
		},
		lock: {
			class: "btn btn-xs btn-outline-danger mr-1",
			icon: "far fa-lock",
			onclick: "fn.app.sales.order.dialog_lock(" + id + ")"
		}
	};

	var config = buttonConfig[type];
	if (!config) {
		console.warn('Unknown button type:', type);
		return '';
	}

	return fn.ui.button(config.class, config.icon, config.onclick);
}

function getOrderLinkUrl(data) {

	const view = (data.product_id == 2)
		? (data.vat_type == 2 ? 'printablesalebf' : 'printable')
		: 'printable';

	return '/#apps/schedule/index.php?view=' + encodeURIComponent(view) +
		'&order_id=' + encodeURIComponent(data.id);
}




fn.app.schedule.order.date_update = function () {
	updateScheduleHeader();
	updateDeliveryIndicators();
};

function updateScheduleHeader() {
	var s = '';
	s += '<table class="table table-xs mb-0">';
	s += '<tbody>';
	s += '<tr>';


	s += '<th width="20" class="p-0 m-0">';
	s += '<button onclick="fn.app.schedule.order.date_previous()" class="btn btn-xs btn-dark m-0" title="วันก่อนหน้า">';
	s += '<i data-feather="chevron-left"></i>';
	s += '</button>';
	s += '</th>';

	for (var i = 0; i < 7; i++) {
		var current_date = new Date(date_base.getTime());
		current_date.setDate(date_base.getDate() + i);

		var dayName = weekday[current_date.getDay()];
		var dayNumber = current_date.getDate();
		var isToday = isDateToday(current_date);

		s += '<th width="35" class="text-center text-white font-weight-bold' + (isToday ? ' bg-warning text-white' : '') + '">';
		s += dayName + '.' + dayNumber;
		s += '</th>';
	}


	s += '<th width="20" class="p-0">';
	s += '<button onclick="fn.app.schedule.order.date_next()" class="btn btn-xs btn-dark m-0" title="วันถัดไป">';
	s += '<i data-feather="chevron-right"></i>';
	s += '</button>';
	s += '</th>';

	s += '</tr>';
	s += '</tbody>';
	s += '</table>';

	$('#schedule_header').html(s);
}

function updateDeliveryIndicators() {
	$(".show_date").each(function () {
		var deliveryDate = $(this).attr('date');
		if (!deliveryDate) return;

		var s = '';
		s += '<table class="table table-xs mb-0">';
		s += '<tbody>';
		s += '<tr>';
		s += '<td width="20"></td>';

		for (var i = 0; i < 7; i++) {
			var current_date = new Date(date_base.getTime());
			current_date.setDate(date_base.getDate() + i);

			var dateStr = moment(current_date).format('YYYY-MM-DD');
			var hasDelivery = (dateStr === deliveryDate);

			s += '<td width="35" class="text-center">';
			if (hasDelivery) {
				s += '<i class="fa fa-sm fa-truck text-danger" title="วันส่งของ"></i>';
			} else {
				s += '<i class="fa fa-sm fa-minus text-muted"></i>';
			}
			s += '</td>';
		}

		s += '<td width="20"></td>';
		s += '</tr>';
		s += '</tbody>';
		s += '</table>';

		$(this).html(s);
	});
}

fn.app.schedule.order.date_next = function () {
	var nextDate = new Date(date_base.getTime());
	nextDate.setDate(date_base.getDate() + 1);
	date_base = nextDate;
	fn.app.schedule.order.date_update();
};

fn.app.schedule.order.date_previous = function () {
	var prevDate = new Date(date_base.getTime());
	prevDate.setDate(date_base.getDate() - 1);
	date_base = prevDate;
	fn.app.schedule.order.date_update();
};


function isDateToday(date) {
	var today = new Date();
	return date.getDate() === today.getDate() &&
		date.getMonth() === today.getMonth() &&
		date.getFullYear() === today.getFullYear();
}

function formatDateThai(date) {
	var thaiMonths = [
		'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
		'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
	];

	return date.getDate() + ' ' + thaiMonths[date.getMonth()] + ' ' + (date.getFullYear() + 543);
}

fn.app.schedule.order.jump_to_date = function (targetDate) {
	if (targetDate instanceof Date) {
		date_base = new Date(targetDate.getTime());
		fn.app.schedule.order.date_update();
	}
};


fn.app.schedule.order.jump_to_today = function () {
	date_base = new Date();
	fn.app.schedule.order.date_update();
};


fn.app.schedule.order.get_date_range = function () {
	var dates = [];
	for (var i = 0; i < 7; i++) {
		var current_date = new Date(date_base.getTime());
		current_date.setDate(date_base.getDate() + i);
		dates.push(current_date);
	}
	return dates;
};

function setupKeyboardShortcuts() {
	$(document).on('keydown', function (e) {
		if ($(e.target).is('input, textarea, select')) {
			return;
		}

		switch (e.keyCode) {
			case 37: // Left arrow
				if (e.ctrlKey) {
					e.preventDefault();
					fn.app.schedule.order.date_previous();
				}
				break;
			case 39: // Right arrow
				if (e.ctrlKey) {
					e.preventDefault();
					fn.app.schedule.order.date_next();
				}
				break;
			case 84: // T key
				if (e.ctrlKey) {
					e.preventDefault();
					fn.app.schedule.order.jump_to_today();
				}
				break;
		}
	});
}


$(document).ready(function () {
	try {
		initializeOrderTable();
		setupKeyboardShortcuts();

		date_base = new Date();

	} catch (error) {
		alert('เกิดข้อผิดพลาดในการเริ่มต้นระบบ กรุณาโหลดหน้าใหม่');
	}
});

if (typeof window.ScheduleDebug === 'undefined') {
	window.ScheduleDebug = {
		getCurrentDate: function () { return date_base; },
		getDateRange: fn.app.schedule.order.get_date_range,
		jumpToDate: fn.app.schedule.order.jump_to_date,
		refreshTable: function () { $("#tblOrder").DataTable().draw(); }
	};
}
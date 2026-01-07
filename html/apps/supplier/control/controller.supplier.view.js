$("#tblSupplier").data("selected", []);

var statusFilterHtml = '<div class="mb-3">';
statusFilterHtml += '<label class="form-label">Filter by Status:</label> ';
statusFilterHtml += '<select id="statusFilter" class="form-control form-control-sm d-inline-block" style="width: auto;">';
statusFilterHtml += '<option value="">All Status</option>';
statusFilterHtml += '<option value="1" selected>ENABLES</option>';
statusFilterHtml += '<option value="2">DISABLES</option>';
statusFilterHtml += '</select>';
statusFilterHtml += '</div>';
$("#tblSupplier").before(statusFilterHtml);

var table = $("#tblSupplier").DataTable({
	responsive: true,
	bStateSave: true,
	autoWidth: true,
	processing: true,
	serverSide: true,
	ajax: {
		url: "apps/supplier/store/store-supplier.php",
		type: "GET",
		data: function (d) {
			
			var names = [
				'bs_suppliers.id',
				'bs_suppliers.name',
				'bs_supplier_groups.name',
				'bs_suppliers.type',
				'bs_suppliers.status',
				'bs_suppliers.id'
			];
			d.columns = d.columns || [];
			for (var i = 0; i < names.length; i++) {
				d.columns[i] = d.columns[i] || { data: '', name: '', searchable: true, orderable: true, search: { value: '', regex: false } };
				d.columns[i].name = names[i];
				d.columns[i].search = d.columns[i].search || { value: '', regex: false };
			}

			
			var v = $("#statusFilter").val();
			d.status_filter = (v === "1" || v === "2") ? v : '';


		},
		error: function (xhr, status, err) {
			console.error('DT ajax error:', status, err, xhr.status, xhr.responseText);
		}
	},

	
	columns: [
		{ data: "id", name: "bs_suppliers.id", orderable: false, className: "hidden-xs text-center", width: "20px" },
		{ data: "name", name: "bs_suppliers.name", className: "text-center" },
		{ data: "group_name", name: "bs_supplier_groups.name", className: "text-center" },
		{ data: "type", name: "bs_suppliers.type", className: "text-center" },
		{ data: "status", name: "bs_suppliers.status", className: "text-center" },
		{ data: "id", name: "bs_suppliers.id", orderable: false, className: "text-center", width: "80px" }
	],

	order: [[1, "asc"]],

	createdRow: function (row, data) {
		var selected = false, s = '';
		if ($.inArray(data.DT_RowId, $("#tblSupplier").data("selected")) !== -1) {
			$(row).addClass("selected"); selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_supplier", data[0], selected));

		if (data.type == "1") { $("td", row).eq(3).html("USD"); }
		else if (data.type == "2") { $("td", row).eq(3).html("THB"); }

		
		var statusDropdown = '<select class="form-control form-control-sm" disabled>';
		statusDropdown += '<option value="1"' + (data.status == "1" ? ' selected' : '') + '>ENABLES</option>';
		statusDropdown += '<option value="2"' + (data.status == "2" ? ' selected' : '') + '>DISABLES</option>';
		statusDropdown += '</select>';
		$("td", row).eq(4).html(statusDropdown);

		s += fn.ui.button("btn btn-xs btn-outline-dark", "far fa-pen", "fn.app.supplier.supplier.dialog_edit(" + data[0] + ")");
		$("td", row).eq(5).html(s);

		fn.ui.datatable.selectable("#tblSupplier", "chk_supplier");
	}
});


$(document).on('change', '#statusFilter', function () {
	table.ajax.reload(null, false);
});

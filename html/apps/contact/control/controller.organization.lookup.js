	fn.app.contact.organization.dialog_lookup = function (optional) {
		var settings = $.extend({
			callback : ""
        },optional);
		
		$.ajax({
			url: "apps/contact/view/dialog.organization.lookup.php",
			type: "POST",
			data: {
				callback : settings.callback
			},
			dataType: "html",
			success: function (html) {
				$("body").append(html);
				$("#dialog_organization_lookup").on("hidden.bs.modal", function () {
					$(this).remove();
				});
				$("#dialog_organization_lookup").modal('show');
				$("#tblOrganization").data("selected", null);
				$('#tblOrganization').DataTable({
					"bStateSave": true,
					responsive: true,
					dom: fn.ui.datatable.dom.default,
					"autoWidth": true,
					"processing": true,
					"serverSide": true,
					"ajax": "apps/contact/store/store-organization.php",
					"aoColumns": [{
							"bSortable": false,
							"data": "id",
							"sClass": "hidden-xs text-center",
							"sWidth": "20px"
						},
						{
							"bSortable": true,
							"data": "code",
							"sClass": "hidden-xs text-center"
						},
						{
							"bSort": true,
							"data": "name",
							"sWidth": "50px"
						},
						{
							"bSortable": true,
							"data": "email",
							"sClass": "hidden-xs text-center"
						},
						{
							"bSortable": true,
							"data": "tax_id",
							"sClass": "hidden-xs text-center"
						},
						{
							"bSortable": true,
							"data": "type",
							"sClass": "hidden-xs text-center"
						}
					],
					"order": [
						[1, "desc"]
					],
					"createdRow": function (row, data, index) {
						var selected = false,
							checked = "",
							s = '';
						if ($.inArray(data.DT_RowId, $("#tblOrganization").data("selected")) !== -1) {
							$(row).addClass('selected');
							selected = true;
						}
						$('td', row).eq(0).html(fn.ui.checkbox("chk_organization", data[0], selected, false));

						if (data.phone != '') s += '<a href="javascript:;" class="btn btn-info btn-xs" rel="tooltip" data-toggle="tooltip" data-placement="top" title="' + data.phone + '"><span class="fa fa-phone"></span></a>';
						if (data.fax != '') s += '<a href="javascript:;" class="btn btn-info btn-xs" rel="tooltip" data-toggle="tooltip" data-placement="top" title="' + data.fax + '"><span class="fa fa-fax"></span></a>';
						if (data.email != '') s += '<a href="javascript:;" class="btn btn-info btn-xs" rel="tooltip" data-toggle="tooltip" data-placement="top" title="' + data.email + '"><span class="fa fa-envelope-o"></span></a>';
						$('td', row).eq(3).html(s);

						

					},
					"drawCallback": function (settings) {
						$("[rel=tooltip]").tooltip();
					}
				});
				fn.ui.datatable.selectable('#tblOrganization', 'chk_organization', false);
			}
		});
	};

	fn.app.contact.organization.select = function (callback) {
		var val = $("#tblOrganization").data("selected");
		if (val == null) {
			swal.fire("Oops...", "Please any organization", "error");
		} else {
			$.ajax({
				url: "apps/contact/xhr/action-load-organization.php",
				type: "POST",
				data: {
					id: val
				},
				dataType: "json",
				success: callback
			});
			$("#dialog_organization_lookup").modal('hide');
		}
		return false;
	};
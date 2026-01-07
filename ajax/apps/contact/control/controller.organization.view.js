$("#tblOrganization").data( "selected", [] );
$('#tblOrganization').DataTable({
	"bStateSave": true,
	responsive: true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/contact/store/store-organization.php",
	"aoColumns": [
			{"bSortable": false ,"data" : "id"			,"sClass" : "hidden-xs text-center" ,"sWidth": "20px"},
			{"bSort" : true		,"data" : "name"},
			{"bSortable": true	,"data" : "email"		,"sClass" : "hidden-xs text-center"},
			{"bSortable": true	,"data" : "phone"		,"sClass" : "text-center"},
			{"bSortable": true	,"data" : "fax"			,"sClass" : "hidden-xs text-center"},
			{"bSortable": true	,"data" : "account"	,"sClass" : "hidden-xs text-center"},
			{"bSortable": true	,"data" : "type"		,"sClass" : "hidden-xs text-center"},
			{"bSortable": true	,"data" : "updated"	,"searchable": false	,"sClass" : "hidden-xs text-center"},
			{"bSortable": false	,"data" : "id"			,"sClass" : "text-center" , "sWidth": "80px"}
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblOrganization").data( "selected")) !== -1 ) {
			$(row).addClass('selected');
			selected = true;
		}
		$('td', row).eq(0).html(fn.ui.checkbox("chk_organization",data[0],selected));fn.app.contact.address.dialog_lookup
		
		$('td', row).eq(7).html(moment(data.updated).format("DD/MM/YYYY HH:mm:ss"));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-primary mr-1","fa fa-map-marker","fn.app.contact.address.dialog('organization',"+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-outline-dark","fa fa-pen","fn.app.contact.organization.dialog_edit("+data[0]+")");
		$('td', row).eq(8).html(s);
		
	},
	"drawCallback": function( settings ) {
		$("[rel=tooltip]").tooltip();
	}
});
fn.ui.datatable.selectable('#tblOrganization','chk_organization');
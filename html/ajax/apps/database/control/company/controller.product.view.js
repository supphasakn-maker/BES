$("#tblDatabase").data( "selected", [] );
$('#tblDatabase').DataTable({
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/database/store/company/store-product.php",
	"aoColumns": [
		{"bSortable": false	,"data":"id"		,"sClass" : "text-center", "sWidth": "20px"},
		{"bSort" : true		,"data":"code"		,"sClass" : "text-center"},
		{"bSortable": true	,"data":"name"	,"sClass" : "text-center"},
		{"bSortable": true	,"data":"updated"	,"sClass" : "text-center"},
		{"bSortable": true	,"data":"id"		,"sClass" : "text-center", "sWidth": "20px"},
	],"order": [[ 1, "asc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblDatabase").data("selected")) !== -1 ) {
			$(row).addClass('selected');
			selected = true;
		}
		$('td', row).eq(0).html(fn.ui.checkbox("chk_database",data.id,selected));
		
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.database.company.product.dialog_edit("+data[0]+")");
		$('td', row).eq(4).html(s);
	}
});
fn.ui.datatable.selectable('#tblDatabase','chk_database');

$("#tblMessage").data( "selected", [] );
$('#tblMessage').DataTable({
	"bStateSave": true,
	responsive: true,
	
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/notify/store/store-message.php",
	"aoColumns": [
		{"bSortable": false ,"data" : "id" , "sWidth": "20px", "sClass" : "hidden-xs text-center" },
		{"bSort" : true,"data" : "source"},
		{"bSort" : true,"data" : "destination"},
		{"bSort" : true,"data" : "type"},
		{"bSort" : true,"data" : "message"},
		{"bSort" : true,"data" : "created"},
		{"bSort" : true,"data" : "updated"},
		{"bSort" : true,"data" : "opened"},
		{"bSort" : true,"data" : "acknowledge"},
		{"bSortable": false,"data" : "id", "sClass" : "text-center" , "sWidth": "100px"  }
	],"order": [[ 5, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblMessage").data( "selected")) !== -1 ) {
			$(row).addClass('selected');
			selected = true;
		}
		$('td', row).eq(0).html(fn.ui.checkbox("chk_message",data[0],selected));
		
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","fa fa-eye","fn.app.notify.message.dialog_lookup("+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-outline-primary mr-1","fa fa-eye","fn.navigate('notify','view=message&section=view&id="+data[0]+"')");
		s += fn.ui.button("btn btn-xs btn-outline-dark","fa fa-pen","fn.app.notify.message.dialog_edit("+data[0]+")");
		$('td', row).eq(9).html(s);
		
	}
});
fn.ui.datatable.selectable('#tblMessage','chk_message');
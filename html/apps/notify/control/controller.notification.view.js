$("#tblNotification").data( "selected", [] );
$('#tblNotification').DataTable({
	"bStateSave": true,
	responsive: true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/notify/store/store-notification.php",
	"aoColumns": [
		{"bSortable": false ,"data" : "id" , "sWidth": "20px", "sClass" : "hidden-xs text-center" },
		{"bSort" : true,"data" : "type"},
		{"bSort" : true,"data" : "topic"},
		{"bSort" : true,"data" : "name"},
		{"bSort" : true,"data" : "created"},
		{"bSort" : true,"data" : "acknowledge"},
		{"bSortable": false,"data" : "id", "sClass" : "text-center" , "sWidth": "100px"  }
	],"order": [[ 4, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblNotification").data( "selected")) !== -1 ) {
			$(row).addClass('selected');
			selected = true;
		}
		$('td', row).eq(0).html(fn.ui.checkbox("chk_notification",data[0],selected));
		
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","fa fa-eye","fn.app.notify.notification.dialog_lookup("+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-outline-dark","fa fa-pen","fn.app.notify.notification.dialog_edit("+data[0]+")");
		$('td', row).eq(6).html(s);
		
	}
});
fn.ui.datatable.selectable('#tblNotification','chk_notification');
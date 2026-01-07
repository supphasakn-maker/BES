$("#tblLog").data( "selected", [] );
$('#tblLog').DataTable({
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/logger/store/store-log.php",
	"aoColumns": [
		{"bSortable": false , "sWidth": "20px", "sClass" : "hidden-xs text-center" },
		{"bSort" : true,"class":"text-center"},
		{"bSort" : true,"class":"text-center"},
		{"bSort" : true,"class":"text-center"},
		{"bSort" : true},
		{"bSort" : true,"class":"text-center"},
		{"bSortable": false, "sClass" : "text-center" , "sWidth": "40px"  }
	],"order": [[ 4, "desc" ]],
	"createdRow": function ( row, data, index ) {
		
		var s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","fa fa-eye","fn.app.logger.log.dialog_view("+data[0]+")");
		$('td', row).eq(6).html(s);
		
	}
});
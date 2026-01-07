$("#tblDatabase").data( "selected", [] );
$('#tblDatabase').DataTable({
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/database/store/company/store-bank.php",
	"aoColumns": [
		{"bSortable": false	,"data":"id"		,"sClass" : "text-center", "sWidth": "20px"},
		{"bSort" : true		,"data":"name"		,"sClass" : "text-center"},
		{"bSortable": true	,"data":"number"	,"sClass" : "text-center"},
		{"bSortable": true	,"data":"branch"	,"sClass" : "text-center"},
		{"bSortable": true	,"data":"icon"		,"sClass" : "text-center"},
		{"bSortable": true	,"data":"id"		,"sClass" : "text-center", "sWidth": "20px"},
	],"order": [[ 1, "asc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblDatabase").data("selected")) !== -1 ) {
			$(row).addClass('selected');
			selected = true;
		}
		$('td', row).eq(0).html(fn.ui.checkbox("chk_database",data.id,selected));
		$('td', row).eq(4).html('<a href="javascript:void(0)" onclick="fn.app.engine.file.dialog_file(\'bank\','+data.id+')"><img class="img-circle" style="height:25px;" src="'+data.icon+'" onerror=this.src=\'img/default/noimage.png\';""></a>');
		
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.database.company.bank.dialog_edit("+data[0]+")");
		$('td', row).eq(5).html(s);
	}
});
fn.ui.datatable.selectable('#tblDatabase','chk_database');

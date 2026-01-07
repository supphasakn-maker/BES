$("#tblAccount").data( "selected", [] );
$('#tblAccount').DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/accctrl/store/store-account.php",
	"aoColumns": [
		{"bSortable":false	,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true		,"data":"name"	},
		{"bSortable":true	,"data":"created"	,"sClass":"hidden-xs text-center"},
		{"bSortable":true	,"data":"updated"	,"sClass":"hidden-xs text-center"},
		{"bSortable":true	,"data":"org_name"	,"sClass":"text-center"},
		{"bSortable":false	,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblAccount").data( "selected")) !== -1 ) {
			$(row).addClass('selected');
			selected = true;
		}
		$('td', row).eq(0).html(fn.ui.checkbox("chk_account",data[0],selected));
		$('td', row).eq(2).html(moment(data.created).format("DD/MM/YYYY HH:mm:ss"));
		$('td', row).eq(3).html(moment(data.updated).format("DD/MM/YYYY HH:mm:ss"));

		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.accctrl.account.dialog_edit("+data[0]+")");

		$('td', row).eq(5).html(s);
	}
});
fn.ui.datatable.selectable('#tblAccount','chk_account');
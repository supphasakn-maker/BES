$("#tblType").data( "selected", [] );
$("#tblType").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/stock/store/store-type.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"name","sClass":"text-center"},
		{"bSort":true			,"data":"type","sClass":"text-center" 	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblType").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_type",data[0],selected));
		$("td", row).eq(2).html(data.type=="1"?"Included":"Memo");
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.stock.type.dialog_edit("+data[0]+")");
		$("td", row).eq(3).html(s);
	}
});
fn.ui.datatable.selectable("#tblType","chk_type");

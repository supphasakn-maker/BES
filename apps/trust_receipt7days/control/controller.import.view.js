$("#tblImport").data( "selected", [] );
$("#tblImport").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/import/store/store-import.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSortable":true		,"data":"delivery_date"	},
		{"bSortable":true		,"data":"supplier_id"	},
		{"bSortable":true		,"data":"amount"	},
		{"bSortable":true		,"data":"delivery_by"	},
		{"bSortable":true		,"data":"type"	},
		{"bSortable":true		,"data":"comment"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblImport").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_usd",data[0],selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.import.import.dialog_edit("+data[0]+")");
		$("td", row).eq(7).html(s);
	}
});
fn.ui.datatable.selectable("#tblImport","chk_usd");

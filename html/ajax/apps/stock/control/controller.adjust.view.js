$("#tblAdjust").data( "selected", [] );
$("#tblAdjust").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/stock/store/store-adjust.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		
		{"bSort":true			,"data":"date","sClass":"text-center"},
		{"bSort":true			,"data":"product","sClass":"text-center" 	},
		{"bSort":true			,"data":"type","sClass":"text-center" 	},
		{"bSort":true			,"data":"amount","sClass":"text-right" 	},
		{"bSort":true			,"data":"remark"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblAdjust").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_adjust",data[0],selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.stock.adjust.dialog_edit("+data[0]+")");
		$("td", row).eq(6).html(s);
	}
});
fn.ui.datatable.selectable("#tblAdjust","chk_adjust");

$("#tblType").data( "selected", [] );
$("#tblType").DataTable({
	responsive: true,
	"pageLength": 50,
	"bStateSave": true,
	
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/product_type_bwd/store/store-type.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
        {"bSort":true			,"data":"code","sClass":"text-left"},
		{"bSort":true			,"data":"name","sClass":"text-left"},
		{"bSort":true			,"data":"type","sClass":"text-center" 	},
        {"bSort":true			,"data":"name_product","sClass":"text-center" 	},
        {"bSort":true			,"data":"user","sClass":"text-center" 	},
        {"bSort":true			,"data":"created","sClass":"text-center" 	},
        {"bSort":true			,"data":"status","sClass":"text-center" 	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 0, "asc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblType").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_type",data[0],selected));
        $("td", row).eq(7).html(data.status=="1"?"enabled":"disabled");
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.product_type_bwd.type.dialog_edit("+data[0]+")");
		$("td", row).eq(8).html(s);
	}
});
fn.ui.datatable.selectable("#tblType","chk_type");

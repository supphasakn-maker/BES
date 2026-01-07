$("#tblUsd").data( "selected", [] );
$("#tblUsd").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/purchase/store/store-usd.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSortable":true		,"data":"confirm"	},
		{"bSortable":true		,"data":"parent","class": "text-center unselectable"	},
		{"bSortable":true		,"data":"amount"	},
		{"bSortable":true		,"data":"rate_exchange"	},
		{"bSortable":true		,"data":"type"	},
		{"bSortable":true		,"data":"bank"	},
		{"bSortable":true		,"data":"user"	},
		{"bSortable":true		,"data":"ref"	},
		{"bSortable":true		,"data":"comment"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblUsd").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_usd",data[0],selected));
		
		if(data.parent != null){
			//$("td", row).eq(2).html('<span class="badge badge-warning">splited</span>');
			$("td", row).eq(2).html('<span onclick="fn.app.purchase.usd.dialog_split_view('+data[0]+')" class="badge badge-warning">splited</span>');
		
		}else{
			s = '';
			s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-cut","fn.app.purchase.usd.dialog_split("+data[0]+")");
			$("td", row).eq(2).html(s);
		}
		
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.purchase.usd.dialog_edit("+data[0]+")");
		$("td", row).eq(10).html(s);
	}
});
fn.ui.datatable.selectable("#tblUsd","chk_usd");

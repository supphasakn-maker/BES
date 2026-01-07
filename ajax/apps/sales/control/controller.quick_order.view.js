$("#tblQuick_order").data( "selected", [] );
$("#tblQuick_order").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/sales/store/store-quick_order.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"			,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSortable":true		,"data":"created"		,class:"text-center"	},
		{"bSortable":true		,"data":"customer_name"	,class:"text-center"	},
		{"bSortable":true		,"data":"amount"		,class:"text-right"	},
		{"bSortable":true		,"data":"price"			,class:"text-right"	},
		{"bSortable":true		,"data":"status"		,class:"text-center"	},
		{"bSortable":true		,"data":"rate_spot"		,class:"text-center"	},
		{"bSortable":true		,"data":"rate_exchange"	,class:"text-center"	},
		{"bSortable":true		,"data":"remark"		,class:"text-center"	},
		{"bSortable":false		,"data":"id"			,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblQuick_order").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_quick_order",data[0],selected));
		s = '';
		if(data.status=="1"){
		s += fn.ui.button("btn btn-xs btn-primary mr-1","far fa-shopping-cart","fn.app.sales.quick_order.dialog_transform("+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.sales.quick_order.dialog_edit("+data[0]+")");
		}
		$("td", row).eq(9).html(s);
	}
});
fn.ui.datatable.selectable("#tblQuick_order","chk_quick_order");

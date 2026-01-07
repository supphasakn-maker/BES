$("#tblOrder").data( "selected", [] );
$("#tblOrder").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/sales/store/store-order.php",	
	"aoColumns": [
		{"bSortable":false	,data:"id"				,class:"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true		,data:"date"			,class:"text-center"	},
		{"bSort":true		,data:"code"			,class:"text-center"	},
		{"bSort":true		,data:"customer_name"	,class:"text-center"	},
		{"bSort":true		,data:"amount"			,class:"text-right"	},
		{"bSort":true		,data:"price"			,class:"text-right"	},
		{"bSort":true		,data:"vat"				,class:"text-right"	},
		{"bSort":true		,data:"net"				,class:"text-right"	},
		{"bSort":true		,data:"delivery_date"	,class:"text-center"	},
		{"bSort":true		,data:"delivery_code"	,class:"text-center"	},
		{"bSortable":false	,data:"id"				,class:"text-center" , "sWidth": "140px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblOrder").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_order",data[0],selected));
		$("td", row).eq(4).html(fn.ui.numberic.format(data.amount));
		$("td", row).eq(5).html(fn.ui.numberic.format(data.price));
		$("td", row).eq(6).html(fn.ui.numberic.format(data.vat));
		$("td", row).eq(7).html(fn.ui.numberic.format(data.net));
		
		if(data.delivery_id == null){
			$("td", row).eq(9).html(fn.ui.button("btn btn-xs btn-outline-danger","far fa-truck ","fn.app.sales.order.dialog_add_delivery("+data[0]+")"));
		}
		
		s = '';
		if(data.delivery_id != null){
			s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-truck ","fn.app.sales.order.dialog_postpone("+data[0]+")");
			s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-lock ","fn.app.sales.order.dialog_lock("+data[0]+")");
		}else{
			//s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-unlock ","fn.app.sales.order.dialog_lock("+data[0]+")");
		}
	
		
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-cut ","fn.app.sales.order.dialog_split("+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-pen","fn.app.sales.order.dialog_edit("+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-danger","far fa-trash","fn.app.sales.order.dialog_remove_each("+data[0]+")");
		
		$("td", row).eq(10).html(s);
	}
});
fn.ui.datatable.selectable("#tblOrder","chk_order");
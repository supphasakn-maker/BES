$("#tblTrading").data( "selected", [] );
$("#tblTrading").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/trade_spot/store/store-trading.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"date"	},
		{"bSort":true			,"data":"date"	},
		{"bSort":true			,"data":"type"	,"class":"text-center"},
		{"bSort":true			,"data":"purchase_spot"	,"class":"text-center"},
		{"bSort":true			,"data":"purchase_amount"	,"class":"text-center"},
		{"bSort":true			,"data":"purchase_usd"	,"class":"text-center"},
		{"bSort":true			,"data":"sales_spot"	,"class":"text-center"},
		{"bSort":true			,"data":"sales_amount"	,"class":"text-center"},
		{"bSort":true			,"data":"sales_usd"	,"class":"text-center"},
		{"bSort":true			,"data":"profit"	,"class":"text-center"},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblTrading").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_trading",data[0],selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.trade_spot.trading.dialog_edit("+data[0]+")");
		$("td", row).eq(11).html(s);
	}
});
fn.ui.datatable.selectable("#tblTrading","chk_trading");

$("#tblPurchase").data( "selected", [] );
$("#tblPurchase").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,	
	"ajax" : {
		"url": "apps/purchase/store/store-spot.php",
		"data": function ( d ) {
			d.where = "bs_purchase_spot.status = 1";
		}
	},
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSortable":true			,"data":"confirm"	},
		{"bSortable":true			,"data":"type"	},
		{"bSortable":true			,"data":"supplier"	},
		{"bSortable":true			,"data":"amount"	},
		{"bSortable":true			,"data":"rate_spot"	},
		{"bSortable":true			,"data":"rate_pmdc"	},
		{"bSortable":true			,"data":"ref"	}
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblPurchase").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_spot",data[0],selected));
		s = '';
	}
});
fn.ui.datatable.selectable("#tblPurchase","chk_spot");

$("#tblSales").data( "selected", [] );
$("#tblSales").DataTable({
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax" : {
		"url": "apps/sales/store/store-spot.php",
		"data": function ( d ) {
			d.where = "bs_sales_spot.status = 1";
		}
	},
	"aoColumns": [
		{"bSortable":false	,"data":"id"			,class:"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSortable":true	,"data":"date" 			,class:"text-center"},
		{"bSortable":true	,"data":"type"			,class:"text-center"},
		{"bSortable":true	,"data":"supplier_name"	,class:"text-center"},
		{"bSortable":true	,"data":"amount"		,class:"text-center"},
		{"bSortable":true	,"data":"rate_spot"		,class:"text-center"},
		{"bSortable":true	,"data":"ref"			,class:"text-center"},
		{"bSortable":true	,"data":"value_date"	,class:"text-center"},
		{"bSortable":true	,"data":"comment"		,class:"text-center"}
		
	],"order": [[ 1, "desc" ]],
	
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblSales").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_spot",data[0],selected));
		
		
	}
});
fn.ui.datatable.selectable("#tblSales","chk_spot");


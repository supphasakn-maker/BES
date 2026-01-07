$("#tblPending").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url" :"apps/purchase/store/store-usd.php",
		"data" : function(d){
			d.where="bs_purchase_usd.status = 0"
		}
	},	
	"aoColumns": [
		{"bSortable":true		,"data":"created"		,"class":"text-center"},
		{"bSortable":true		,"data":"date"		,"class":"text-center"},
		{"bSortable":true		,"data":"type"		,"class":"text-center"},
		{"bSortable":true		,"data":"bank"	,"class":"text-center"},
		{"bSortable":true		,"data":"amount"	,"class":"text-right"},
		{"bSortable":true		,"data":"rate_exchange"	,"class":"text-right"},
		{"bSortable":true		,"data":"comment"		},
		{"bSortable":true		,"data":"user"		,"class":"text-center"},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		
		$("td", row).eq(0).html(moment(data.created).format("DD/MM/YYYY HH:mm:ss"));
		$("td", row).eq(1).html(moment(data.date).format("DD/MM/YYYY"));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-trash","fn.app.purchase.usd.remove("+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-pen","fn.app.purchase.usd.dialog_edit("+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-outline-success","far fa-shopping-cart","fn.app.purchase.usd.dialog_purchase("+data[0]+")");
		$("td", row).eq(8).html(s);
	}
});

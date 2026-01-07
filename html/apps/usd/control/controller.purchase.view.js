$("#tblPurchase").data( "selected", [] );
$("#tblPurchase").DataTable({
	responsive: true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url" :"apps/purchase/store/store-usd.php",
		"data" : function(d){
			d.where="bs_purchase_usd.parent IS NULL"
		}
	},	
	"aoColumns": [
		{"bSortable":true		,"data":"confirm"		,"class":"text-center"},
		{"bSortable":true		,"data":"date"		,"class":"text-center"},
		{"bSortable":true		,"data":"type"		,"class":"text-center"},
		{"bSortable":true		,"data":"bank"	,"class":"text-center"},
		{"bSortable":true		,"data":"amount"	,"class":"text-right"},
		{"bSortable":true		,"data":"rate_exchange"	,"class":"text-right"},
		{"bSortable":true		,"data":"method"	,"class":"text-right"},
		{"bSortable":true		,"data":"comment"		},
		{"bSortable":true		,"data":"user"		,"class":"text-center"},
		{"bSortable":true		,"data":"children"		,"class":"text-center"},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 0, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblPurchase").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(moment(data.created).format("DD/MM/YYYY HH:mm:ss"));
		$("td", row).eq(1).html(moment(data.date).format("DD/MM/YYYY"));
		
		if(data.children != 0){
			s = '';
			s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-eye","fn.app.purchase.usd.dialog_split_children("+data[0]+")");
		
			
		}else{
			s = '-';
		}
		$("td", row).eq(9).html(s);
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-trash","fn.app.purchase.usd.remove("+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-pen","fn.app.purchase.usd.dialog_edit("+data[0]+")");
		$("td", row).eq(10).html(s);
	}
});
fn.ui.datatable.selectable("#tblPurchase","chk_purchase");

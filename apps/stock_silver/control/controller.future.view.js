$("#tblStockFuture").data( "selected", [] );
$("#tblStockFuture").DataTable({
	responsive: true,
	"pageLength": 50,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/stock_silver/store/store-future.php",
	"aoColumns": [
		{"bSortable":false	,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px" },
        {"bSortable":true	,"data":"code" 		,class:"text-center", "sWidth": "20px"},
		{"bSortable":true	,"data":"customer_po" 		,class:"text-center", "sWidth": "80px"},
		{"bSortable":true	,"data":"pack_name" 		,class:"text-center"},
		{"bSortable":true	,"data":"pack_type" 		,class:"text-center"},
        {"bSortable":true	,"data":"weight_actual" 		,class:"text-center", "sWidth": "10px"},
        {"bSortable":true	,"data":"created" 		,class:"text-center"},
        {"bSortable":true	,"data":"status" 		,class:"text-center", "sWidth": "10px"}
		
	],"order": [[0, 'asc']],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblStockFuture").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_future",data[0],selected));
		$("td", row).eq(2).html('<a href="#apps/schedule/index.php?view=printablebf&order_id='+data[3]+'">'+data.customer_po+'</a>');
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-trash","fn.app.stock_silver.future.remove("+data[0]+")");
		$("td", row).eq(7).html(s);
	},

		"footerCallback": function (row,data,start,end,display) {
			var api = this.api(),data;
			
			var tAmount = 0,tValue = 0;
			for(i in data){
				tAmount += parseFloat(data[i].weight_actual);
			}
	
			$("#tblStockFuture [xname=tAmount]").html(fn.ui.numberic.format(tAmount,4));
			
		}
	});
fn.ui.datatable.selectable("#tblStockFuture","chk_future");
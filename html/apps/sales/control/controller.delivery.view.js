$("#tblDelivery").data( "selected", [] );

$("#tblDelivery").DataTable({
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/sales/store/store-delivery.php",
	"aoColumns": [
		{"bSortable":false	,"data":"id"			,class:"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSortable":true	,"data":"code" 			,class:"text-center"},
		{"bSortable":true	,"data":"order_code" 	,class:"text-center"},
		{"bSortable":true	,"data":"type"			,class:"text-center"},
		{"bSortable":true	,"data":"delivery_date"	,class:"text-center"},
		{"bSortable":true	,"data":"amount"	 	,class:"text-right"	},
		{"bSortable":true	,"data":"customer_name" ,class:"text-center"},
		{"bSortable":true	,"data":"status" ,class:"text-center"},
		{"bSortable":false	,"data":"id"			,class:"text-center" , "sWidth": "80px" },
		{"bSortable":false	,"data":"id"			,class:"text-center" }
		
	],"order": [[ 1, "desc" ]],
	
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblDelivery").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_delivery",data[0],selected));
		
		if(data.type == "1"){
			$("td", row).eq(3).html('<span class="badge badge-primary">single</span>');
		}else if(data.type == "2"){
			$("td", row).eq(3).html('<span class="badge badge-success">combined</span>');
		}
		
		s = '';
		if(data.status =="1"){
			$("td", row).eq(7).html('<span class="badge badge-dark">เตรียมแพ็คแล้ว</span>');
			s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-boxes","fn.app.sales.delivery.dialog_packing("+data[0]+")");
		}else{
			$("td", row).eq(7).html('<span class="badge badge-warning">ยังไม่ได้เตรียม</span>');
			s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-boxes","fn.app.sales.delivery.dialog_packing("+data[0]+")");
		}
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.sales.delivery.dialog_edit("+data[0]+")");
		$("td", row).eq(8).html(s);
		
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-2","far fa-dollar-sign","fn.app.sales.delivery.dialog_payment("+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-file","fn.app.sales.delivery.dialog_billing("+data[0]+")");
		$("td", row).eq(9).html(s);
		
	}
});

fn.ui.datatable.selectable("#tblDelivery","chk_delivery");

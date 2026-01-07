$("#tblPayment").data( "selected", [] );
$("#tblPayment").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/prepare_to_pay/store/store-payment.php",	
		"data": function ( d ) {
			d.date_from = $("form[name=filter] input[name=from]").val();
			d.date_to = $("form[name=filter] input[name=to]").val();
		}
	},
	"aoColumns": [
		{"bSortable":false	,"data":"id"			,class:"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSortable":true	,"data":"code" 			,class:"text-center"},
		{"bSortable":true	,"data":"order_code" 	,class:"text-center"},
		{"bSortable":true	,"data":"billing_id" 	,class:"text-center"},
		{"bSortable":true	,"data":"type"			,class:"text-center"},
		{"bSortable":true	,"data":"delivery_date"	,class:"text-center"},
		{"bSortable":true	,"data":"total"	 		,class:"text-right"	},
		{"bSortable":true	,"data":"vat_amount"	,class:"text-right"	},
		{"bSortable":true	,"data":"net"	 		,class:"text-right"	},
		{"bSortable":true	,"data":"info_payment"	,class:"text-center"},
		{"bSortable":true	,"data":"customer_name" ,class:"text-center"},
		{"bSortable":true	,"data":"status" 		,class:"text-center"},
		{"bSortable":true	,"data":"payment_status" 		,class:"text-center"}
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblPayment").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_payment",data[0],selected));
		
		if(data.type == "1"){
			$("td", row).eq(4).html('<span class="badge badge-primary">single</span>');
		}else if(data.type == "2"){
			$("td", row).eq(4).html('<span class="badge badge-success">combined</span>');
		}
		
		s = '';
		if(data.status =="1"){
			$("td", row).eq(11).html('<span class="badge badge-dark">เตรียมแพ็คแล้ว</span>');
		}else{
			$("td", row).eq(11).html('<span class="badge badge-warning">ยังไม่ได้เตรียม</span>');
		}
		
		if(data.payment_id != null){
			if(data.payment_status == "0"){
				$("td", row).eq(12).html('<span title="'+data.payment_id+'" class="badge badge-info">เปิดชำระเงินแล้ว</span>' );
			}else{
				$("td", row).eq(12).html('<span title="'+data.payment_id+'" class="badge badge-success">ชำระเงินแล้ว</span>' );
			}
			
		}else{
			$("td", row).eq(12).html(fn.ui.button("btn btn-xs btn-warning","far fa-dollar-sign","fn.app.prepare_to_pay.payment.dialog_pay("+data.order_id+")"));
		}
	}
});
fn.ui.datatable.selectable("#tblPayment","chk_payment");

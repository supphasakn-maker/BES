$("#tblDelivery").data( "selected", [] );
$("#tblDelivery").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,	
	"ajax": {
		"url": "apps/sales/store/store-delivery.php",
		"data": function ( d ) {
			d.date_from = $("form[name=filter] input[name=from]").val();
			d.date_to = $("form[name=filter] input[name=to]").val();
		}
	},
	"aoColumns": [
		{"bSortable":false		,"data":"id"	,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"code",	"class":"text-center"	},
		{"bSort":true			,"data":"order_code",	"class":"text-center"	},
		{"bSort":true			,"data":"type",	"class":"text-center"	},
		{"bSort":true			,"data":"customer_name",	"class":"text-center"},
		{"bSort":true			,"data":"amount",			"class":"text-right pr-2"	},
		{"bSort":true			,"data":"price",			"class":"text-right pr-2"	},
		{"bSort":true			,"data":"date",				"class":"text-center"	},
		{"bSort":true			,"data":"delivery_date",	"class":"text-center"	},
		{"bSortable":false		,"data":"id",				"class":"text-center"	},
		{"bSortable":false		,"data":"info_payment",				"class":"text-center"	},
		{"bSortable":false		,"data":"id",				"class":"text-center"	},
		{"bSortable":false		,"data":"status","class":"text-center"  }
	],"order": [[ 3, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblDelivery").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_order",data[0],selected));
		//$("td", row).eq(1).html(fn.ui.button("btn btn-xs btn-outline-dark","far fa-cut","fn.app.schedule.order.dialog_split("+data[0]+")"));
		
		if(data.type == "1"){
			$("td", row).eq(3).html('<span class="badge badge-warning">single</span>');
		}else if(data.type == "2"){
			$("td", row).eq(3).html('<span class="badge badge-primary">combined</span>');
		}
		
		s = '';
		if(data.status =="1"){
			s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-boxes","fn.app.sales.delivery.dialog_packing("+data[0]+")");
		}else{
			s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-boxes","fn.app.sales.delivery.dialog_packing("+data[0]+")");
		}
		$("td", row).eq(9).html(s);
		
		
		s = '';
		if(data.payment_note == null || data.payment_note == ""){
			s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-dollar-sign","fn.app.sales.delivery.dialog_payment("+data[0]+")");
		}else{
			var obj = jQuery.parseJSON( data.payment_note);
			s += '<a href="javascript:;" onclick="fn.app.sales.delivery.dialog_payment('+data[0]+')">';
			s += obj.bank + "," + obj.payment;
			s += '</a>';
		}
		$("td", row).eq(10).html(s);
		
		s = '';
		if(data.billing_id == "" || data.billing_id == null){
			s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-file","fn.app.sales.delivery.dialog_billing("+data[0]+")");
		}else{
			s += '<a href="javascript:;" onclick="fn.app.sales.delivery.dialog_billing('+data[0]+')">';
			s += data.billing_id;
			s += '</a>';
		}
		$("td", row).eq(11).html(s);
		
		if(data.status == "1"){
			$("td", row).eq(12).html('<span class="badge badge-primary">แบ่งแพ็คแล้ว</span>');
		}else if(data.status == "0"){
			$("td", row).eq(12).html('<span class="badge badge-warning">รอการจัดเตรียม</span>');
		}
	
	}
});
fn.ui.datatable.selectable("#tblDelivery","chk_order");

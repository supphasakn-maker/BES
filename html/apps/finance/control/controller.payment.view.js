$("#tblPayment").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/finance/store/store-payment.php",
		"data": function ( d ) {
			d.date_from = $("form[name=filter] input[name=from]").val();
			d.date_to = $("form[name=filter] input[name=to]").val();
		}
	},
	"aoColumns": [
		{"bSortable":false	,"data":"datetime"	,class:"text-center" },
		{"bSortable":false	,"data":"date_active"	,class:"text-center" },
		{"bSortable":true	,"data":"customer"	,class:"text-center"},
		{"bSortable":true	,"data":"id"	,class:"text-center"},
		{"bSortable":true	,"data":"amount"	,class:"text-center"	},
		{"bSortable":true	,"data":"payment"	,class:"text-center"	},
		{"bSortable":true	,"data":"customer_bank"	,class:"text-center"	},
		{"bSortable":true	,"data":"ref"	,class:"text-center"	},
		{"bSortable":false	,"data":"id"		,"sClass":"text-center" , "sWidth": "150px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var s = '';
		
		//$("td", row).eq(2).html('');
		
			s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-trash","fn.app.finance.payment.remove("+data[0]+")");
		
		if(data.status == "1"){
			s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-thumbs-down","fn.app.finance.payment.dialog_deapprove("+data[0]+")");
			s += '<span onclick="fn.app.finance.payment.dialog_show('+data[0]+')" class="badge badge-success">Approved</span>';
		}else{
			
			s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-pen","fn.app.finance.payment.dialog_edit("+data[0]+")");
			s += fn.ui.button("btn btn-xs btn-outline-primary mr-1","far fa-link","fn.app.finance.payment.dialog_mapping("+data[0]+")");
			s += fn.ui.button("btn btn-xs btn-outline-danger","far fa-thumbs-up","fn.app.finance.payment.dialog_approve("+data[0]+")");
			
		}
		
		
		
		
		$("td", row).eq(8).html(s);
	}
});



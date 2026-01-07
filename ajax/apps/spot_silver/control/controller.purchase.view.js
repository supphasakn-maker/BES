
$("#tblPurchase").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url" :"apps/purchase/store/store-spot.php",
		"data" : function(d){
			d.where="bs_purchase_spot.parent IS NULL"
		}
	},	
	"aoColumns": [
		{"bSortable":true		,"data":"confirm"	,"class":"text-center"},
		{"bSortable":true		,"data":"date"		,"class":"text-center"},
		{"bSortable":true		,"data":"type"		,"class":"text-center"},
		{"bSortable":true		,"data":"supplier"	,"class":"text-center"},
		{"bSortable":true		,"data":"amount"	,"class":"text-right"},
		{"bSortable":true		,"data":"rate_spot"	,"class":"text-right"},
		{"bSortable":true		,"data":"rate_pmdc"	,"class":"text-right"},
		{"bSortable":true		,"data":"user"		,"class":"text-center"},
		{"bSortable":true		,"data":"ref"		},
		{"bSortable":false		,"data":"id"		,"class":"text-center" , "sWidth": "80px"  }
	],"order": [[ 0, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		
		$("td", row).eq(0).html(moment(data.confirm).format("DD/MM/YYYY HH:mm:ss"));
		$("td", row).eq(1).html(moment(data.date).format("DD/MM/YYYY"));
		
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-trash","fn.app.purchase.spot.remove("+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.purchase.spot.dialog_edit("+data[0]+")");
		$("td", row).eq(9).html(s);
	}
});


$("form[name=form_addspot] select[name=currency]").change(function(){
	if($(this).val()=="USD"){
		$("form[name=form_addspot] input[name=rate_spot]").parent().parent().show();
		$("form[name=form_addspot] input[name=rate_pmdc]").parent().parent().show();
		$("form[name=form_addspot] input[name=THBValue]").parent().parent().hide();
	}else{
		$("form[name=form_addspot] input[name=rate_spot]").parent().parent().hide();
		$("form[name=form_addspot] input[name=rate_pmdc]").parent().parent().hide();
		$("form[name=form_addspot] input[name=THBValue]").parent().parent().show();
	}
});

$("form[name=form_addspot] select[name=supplier_id]").change(function(){
	$.post("apps/supplier/xhr/action-load-supplier.php",{id:$(this).val()},function(supplier){
		if(supplier.type=="1"){
			$("form[name=form_addspot] select[name=currency]").val("USD").change();
		}else if(supplier.type=="2"){
			$("form[name=form_addspot] select[name=currency]").val("THB").change();
		}
	},"json");
}).change();

$("#tblUSD").data( "selected", [] );
$("#tblUSD").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		url : "apps/forward_contract/store/store-usd.php",
		data : function(d){
			d.bank = $("select[name=bank]").val();
		}
	},	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"date"	},
		{"bSort":true			,"data":"bank_date", class:"unselectable"	},
		{"bSort":true			,"data":"premium_start", class:"unselectable"	},
		{"bSort":true			,"data":"fw_contract_no", class:"unselectable"	},
		{"bSort":true			,"data":"rate_exchange","class":"text-right"	},
		{"bSort":true			,"data":"premium", class:"unselectable"	},
		{"bSort":true			,"data":"rate_exchange","class":"text-right unselectable"	},
		{"bSort":true			,"data":"amount","class":"text-right  unselectable"	},
		{"bSort":true			,"data":"thb" ,"class":"text-rightunselectable"	},
		{"bSort":true			,"data":"thb" ,"class":"text-right unselectable"	},
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblUSD").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		s = '<input type="hidden" xname="amount" value="'+data.amount_value+'">';
		s += '<input type="hidden" xname="rate_exchange" value="'+data.rate_exchange+'">';
		$("td", row).eq(0).html(fn.ui.checkbox("chk_usd",data[0],selected)+s);
		
		s = '<input xname="bank_date" type="date" class="form-control form-control-sm" value="'+data.bank_date+'">';
		$("td", row).eq(2).html(s);
		s = '<input xname="premium_date" type="date" class="form-control form-control-sm" value="'+data.premium_start+'">';
		$("td", row).eq(3).html(s);
		s = '<input xname="contact_no" class="form-control form-control-sm" value="'+(data.fw_contract_no==null?"":data.fw_contract_no)+'">';
		$("td", row).eq(4).html(s);
		s = '<input xname="premium" onchange="fn.app.forward_contract.contract.calculate()" class="form-control form-control-sm" value="'+(data.premium==null?"":data.premium)+'">';
		$("td", row).eq(6).html(s);
		
		s = '<input xname="premiumfx" readonly class="form-control form-control-sm" value="'+data.rate_exchange+'">';
		$("td", row).eq(7).html(s);
		s = '<input xname="thb" readonly class="form-control form-control-sm" value="'+data.thb+'">';
		$("td", row).eq(9).html(s);
		s = '<input xname="thbpremium" readonly class="form-control form-control-sm" value="'+data.thb+'">';
		$("td", row).eq(10).html(s);
		
		
		
		
	}
});
fn.ui.datatable.selectable("#tblUSD","chk_usd");

$("select[name=bank]").change(function(){
	$("#tblUSD").DataTable().draw();
});






$("#tblContract").data( "selected", [] );
$("#tblContract").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/forward_contract/store/store-contract.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"transfer_date"	},
		{"bSort":true			,"data":"bank"	},
		{"bSort":true			,"data":"supplier"	},
		{"bSort":true			,"data":"id","class":"text-center"	},
		{"bSort":true			,"data":"net_good_value"	},
		{"bSort":true			,"data":"deposit"	},
		{"bSort":true			,"data":"total_transfer"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblContract").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_contract",data[0],selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.forward_contract.contract.dialog_edit("+data[0]+")");
		$("td", row).eq(8).html(s);
	}
});
fn.ui.datatable.selectable("#tblContract","chk_contract");

$("#tblStatement").data( "selected", [] );
$("#tblStatement").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"data" : function(d){
			d.bank_id = $("select[name=bank_id]").val();
			d.bank_date = $("input[name=bank_date]").val();
		},
		"url":"apps/bank/store/store-statement.php",
	},		
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"date" ,class:"text-center"	},
		{"bSort":true			,"data":"amount",class:"text-center"	},
		{"bSort":true			,"data":"amount",class:"text-right"	},
		{"bSort":true			,"data":"balance",class:"text-right"	},
		{"bSort":true			,"data":"narrator"	},
		{"bSort":true			,"data":"transfer_to",class:"text-center"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "100px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblStatement").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_statement",data[0],selected));
		if(data.type=="1"){
			$("td", row).eq(3).html("-");
			$("td", row).eq(2).html('<span class="text-danger">['+parseFloat(-data.amount).toFixed(2)+']<span>');
		}else{
			$("td", row).eq(2).html("-");
			$("td", row).eq(3).html(parseFloat(data.amount).toFixed(2));
		}
		
		
		if(data.transfer_to != null){
			//$("td", row).eq(3).html("-");
		}else{
			$("td", row).eq(6).html("-");
		}
		
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-pen","fn.app.bank.statement.dialog_edit("+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-outline-primary mr-1","far fa-link","fn.app.bank.statement.dialog_mapping("+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-outline-danger","far fa-thumbs-up","fn.app.bank.statement.dialog_edit("+data[0]+")");
		
		$("td", row).eq(7).html(s);
	}
}).on('xhr.dt', function ( e, settings, json, xhr ) {
	
	
	$("#debit_total").html(fn.ui.numberic.format(json.total.debit,2));
	$("#credit_total").html(fn.ui.numberic.format(json.total.credit,2));

});;

fn.ui.datatable.selectable("#tblStatement","chk_statement");
$("select[name=bank_id]").change(function(){
	$("#tblStatement").DataTable().draw();
});
$("input[name=bank_date]").change(function(){
	$("#tblStatement").DataTable().draw();
});




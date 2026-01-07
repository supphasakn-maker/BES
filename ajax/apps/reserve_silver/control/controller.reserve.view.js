$("#tblReserve").data( "selected", [] );
$("#tblReserve").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url":"apps/reserve_silver/store/store-reserve.php",
		"data": function ( d ) {
			d.date_from = $("form[name=filter] input[name=from]").val();
			d.date_to = $("form[name=filter] input[name=to]").val();
		}
	},	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"lock_date"	,class:"text-center"	},
		{"bSort":true			,"data":"supplier"	,class:"text-center"	},
		{"bSort":true			,"data":"weight_lock"	,class:"text-right"	},
		{"bSort":true			,"data":"weight_fixed"	,class:"text-right"	},
		{"bSort":true			,"data":"weight_pending"	,class:"text-right"	},
		{"bSort":true			,"data":"discount"	,class:"text-right"	},
		{"bSort":true			,"data":"weight_actual"	,class:"text-right"	},
		{"bSort":true			,"data":"defer"	,class:"text-right"	},
		{"bSort":true			,"data":"bar"	,class:"text-center"	},
		{"bSort":true			,"data":"type"	,class:"text-center"	},
		{"bSort":true			,"data":"import_id"	,class:"text-center"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblReserve").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_reserve",data[0],selected));
		if(data.type=="1"){
			$("td", row).eq(10).html("ใช้จริง");
		}else if(data.type=="2"){
			$("td", row).eq(10).html("สำรอง");
		}
		
		if(data.import_id != null){
			$("td", row).eq(11).html('<span class="badge badge-warning" title="'+data.import_id +'">Importted</span>');
		}else{
			$("td", row).eq(11).html("-");
		}
		
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.reserve_silver.reserve.dialog_edit("+data[0]+")");
		$("td", row).eq(12).html(s);
	}
});
fn.ui.datatable.selectable("#tblReserve","chk_reserve");

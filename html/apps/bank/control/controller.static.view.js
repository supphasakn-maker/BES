$("#tblStatic").data( "selected", [] );
$("#tblStatic").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url":"apps/bank/store/store-static.php",
	},
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"type" ,class:"text-center"	},
		{"bSort":true			,"data":"title" ,class:"text-center"	},
		{"bSort":true			,"data":"customer_name" ,class:"text-center"	},
		{"bSort":true			,"data":"start" ,class:"text-center"	},
		{"bSort":true			,"data":"end" ,class:"text-center"	},
		{"bSort":true			,"data":"amount",class:"text-center"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "100px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblStatic").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_static",data[0],selected));
		
		
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-pen","fn.app.bank.static.dialog_edit("+data[0]+")");
		
		$("td", row).eq(7).html(s);
	}
});
fn.ui.datatable.selectable("#tblStatic","chk_static");
$("select[name=bank_id]").change(function(){
	$("#tblStatic").DataTable().draw();
});




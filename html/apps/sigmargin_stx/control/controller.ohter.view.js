$("#tblOhter").data( "selected", [] );
$("#tblOhter").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/sigmargin_stx/store/store-ohter.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		, "sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"usd_debit"	},
		{"bSort":true			,"data":"usd_credit"	},
		{"bSort":true			,"data":"amount_debit"	},
		{"bSort":true			,"data":"amount_credit"	},
		{"bSort":true			,"data":"date"	},
		{"bSort":true			,"data":"remark"	},
		{"bSortable":false		,"data":"id"		, "sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblOhter").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_ohter",data[0],selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.sigmargin_stx.ohter.dialog_edit("+data[0]+")");
		$("td", row).eq(7).html(s);
	}
});
fn.ui.datatable.selectable("#tblOhter","chk_ohter");

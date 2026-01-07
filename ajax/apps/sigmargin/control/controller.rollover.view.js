$("#tblRollover").data( "selected", [] );
$("#tblRollover").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/sigmargin/store/store-rollover.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"type"	, "sClass":"hidden-xs text-center"},
		{"bSort":true			,"data":"amount"	},
		{"bSort":true			,"data":"rate_spot"	},
		{"bSort":true			,"data":"trade"	},
		{"bSort":true			,"data":"date"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblRollover").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_rollover",data[0],selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.sigmargin.rollover.dialog_edit("+data[0]+")");
		$("td", row).eq(6).html(s);
	}
});
fn.ui.datatable.selectable("#tblRollover","chk_rollover");

$("#tblOverview").data( "selected", [] );
$("#tblOverview").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/match/store/store-overview.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"name"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblOverview").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_overview",data[0],selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.match.overview.dialog_edit("+data[0]+")");
		$("td", row).eq(2).html(s);
	}
});
fn.ui.datatable.selectable("#tblOverview","chk_overview");



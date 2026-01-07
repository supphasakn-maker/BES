$("#tblTransfer").data( "selected", [] );
$("#tblTransfer").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/sigmargin/store/store-transfer.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center"},
		{"bSort":true			,"data":"type" 		, "sClass":"hidden-xs text-center" },
		{"bSort":true			,"data":"amount_usd" 		, "sClass":"hidden-xs text-center" },
		{"bSort":true			,"data":"rate_pmdc" 		, "sClass":"hidden-xs text-center" },
		{"bSort":true			,"data":"amount_total" 		, "sClass":"hidden-xs text-center" },
		{"bSort":false 			,"data":"date" 		, "sClass":"hidden-xs text-center" },
		{"bSort":false 			,"data":"id"  ,  "sClass":"hidden-xs text-center" }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblTransfer").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_transfer",data[0],selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.sigmargin.transfer.dialog_edit("+data[0]+")");
		$("td", row).eq(6).html(s);
	}
});
fn.ui.datatable.selectable("#tblTransfer","chk_transfer");

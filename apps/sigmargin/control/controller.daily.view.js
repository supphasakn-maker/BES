$("#tblDaily").data( "selected", [] );
$("#tblDaily").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url" : "apps/sigmargin/store/store-daily.php",
		"data" : function(d){
			d.date = $("#selcted_date").val();
		}
	},
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center"},
		{"bSort":true			,"data":"date" 		, "sClass":"hidden-xs text-center" },
		{"bSort":true			,"data":"spot_sell" 		, "sClass":"hidden-xs text-center" },
		{"bSort":true			,"data":"spot_buy" 		, "sClass":"hidden-xs text-center" },
		{"bSort":true			,"data":"cash" 		, "sClass":"hidden-xs text-center" },
		{"bSort":false 			,"data":"id"  ,  "sClass":"hidden-xs text-center" }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblDaily").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_daily",data[0],selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.sigmargin.daily.dialog_edit("+data[0]+")");
		$("td", row).eq(5).html(s);
	}
});
fn.ui.datatable.selectable("#tblDaily","chk_daily");

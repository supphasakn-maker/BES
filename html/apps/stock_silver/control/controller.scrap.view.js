$("#tblStock").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/stock_silver/store/store-scrap.php",	
	"aoColumns": [
        {"bSortable":false	,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px" },
        {"bSortable":true	,"data":"code" 		,class:"text-center", "sWidth": "20px"},
		{"bSortable":true	,"data":"pack_name" 		,class:"text-center"},
		{"bSortable":true	,"data":"pack_type" 		,class:"text-center"},
        {"bSortable":true	,"data":"weight_actual" 		,class:"text-center", "sWidth": "10px"},
        {"bSortable":true	,"data":"created" 		,class:"text-center"},
        {"bSortable":true	,"data":"status" 		,class:"text-center", "sWidth": "10px"}
	],"order": [[0, 'desc']],
	"createdRow": function ( row, data, index ) {
		var s = '';
		
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-trash","fn.app.stock_silver.scrap.remove("+data[0]+")");
		$("td", row).eq(6).html(s);
	}
});

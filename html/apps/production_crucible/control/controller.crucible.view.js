$("#tblCrucible").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/production_crucible/store/store-crucible.php",	
		"data": function ( d ) {
			d.date_from = $("form[name=filter] input[name=from]").val();
			d.date_to = $("form[name=filter] input[name=to]").val();
		}
	}, 
	"aoColumns": [
		{"bSortable":true	,"data":"date" 		,class:"text-center"},
		{"bSortable":true	,"data":"round" 		,class:"text-center"},
		{"bSortable":true	,"data":"id" 		,class:"text-center"},
		{"bSortable":true	,"data":"submited" 		,class:"text-center"},
		{"bSortable":false	,"data":"id"			,class:"text-center" , "sWidth": "80px" }
	],"order": [[1, 'asc']],
	"createdRow": function ( row, data, index ) {
		var s = '';
		$("td", row).eq(2).html(fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-eye ","fn.app.production_crucible.crucible.dialog_lookup("+data[0]+")"));
		s = '';

		if(data.submited == null){
		s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-trash","fn.app.production_crucible.crucible.remove("+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-thumbs-down","fn.app.production_crucible.crucible.dialog_approve("+data[0]+")");
		}else{
			s += '<span class="badge badge-warning">ปิดการใช้เบ้า</span>';
		}
		$("td", row).eq(4).html(s);
	}
});


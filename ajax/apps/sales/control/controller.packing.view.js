$("#tblPacking").data( "selected", [] );
$("#tblPacking").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/sales/store/store-packing.php",	
	"aoColumns": [
		{"bSort":true			,"data":"prepare_date","class":"text-center"	},
		{"bSort":true			,"data":"info_amount","class":"text-center"	},
		{"bSort":true			,"data":"info_mine","class":"text-center"	},
		{"bSort":true			,"data":"status_show","class":"text-center"	},
		{"bSort":true			,"data":"amount","class":"text-center"	},
		{"bSort":true			,"data":"status","class":"text-center"	},
		{"bSortable":false		,"data":"id"	,"sClass":"text-center" , "sWidth": "120px"  }

	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		
		
		s = '';
		if(data.status == "0"){
			s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-pen","fn.app.sales.packing.dialog_edit("+data[0]+")");
			s += fn.ui.button("btn btn-xs btn-outline-danger","far fa-thumbs-up","fn.app.sales.packing.dialog_approve("+data[0]+")");
		}else{
			s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-trash","fn.app.sales.packing.dialog_view("+data[0]+")");
			
			s += fn.ui.button("btn btn-xs btn-outline-primary mr-1","far fa-eye","fn.app.sales.packing.dialog_view("+data[0]+")");
			s += fn.ui.button("btn btn-xs btn-outline-view","far fa-print","fn.app.sales.packing.dialog_view("+data[0]+")");
			
		}
		$("td", row).eq(6).html(s);
	}
});

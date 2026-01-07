$("#tblPrepare").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/production_prepare/store/store-prepare.php",
		"data": function ( d ) {
			d.date_from = $("form[name=filter] input[name=from]").val();
			d.date_to = $("form[name=filter] input[name=to]").val();
		}
	},
	"aoColumns": [
		{"bSort":true			,"data":"created",	class: "text-center"	},
		{"bSort":true			,"data":"round",	class: "text-center"	},
		{"bSort":true			,"data":"total_item_a",	class: "text-center"	},
		{"bSort":true			,"data":"total_item_b",	class: "text-center"	},
		{"bSort":true			,"data":"weight_in_total",	class: "text-center"	},
		{"bSort":true			,"data":"weight_out_total",	class: "text-center"	},
		{"bSort":true			,"data":"weight_margin",	class: "text-center"	},
		{"bSort":true			,"data":"weight_out_packing",	class: "text-center"	},
		{"bSort":true			,"data":"remark",	class: "text-center"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "120px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		
		var s = '';
		if(data.status=="0"){
			s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-trash","fn.app.production_prepare.prepare.remove("+data[0]+")");
			s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-thumbs-up","fn.app.production_prepare.prepare.dialog_approve("+data[0]+")");
			s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.navigate('production_prepare','view=prepare&section=edit&production_id="+data[0]+"')");
		}else{
			s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-eye","fn.navigate('production_prepare','view=prepare&section=edit&production_id="+data[0]+"')");
		
			s += '<span class="badge badge-warning">Submited</span>';
		}
		$("td", row).eq(9).html(s);
	}
});


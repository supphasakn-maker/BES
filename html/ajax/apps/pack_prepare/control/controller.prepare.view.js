$("#tblPrepare").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,	
	"ajax": {
		"url": "apps/pack_prepare/store/store-prepare.php",
		"data": function ( d ) {
			d.date_from = $("form[name=filter] input[name=from]").val();
			d.date_to = $("form[name=filter] input[name=to]").val();
		}
	},
	"aoColumns": [
		{"bSort":true		,"data":"prepare_date"	,"class":"text-center"	},
		{"bSort":true		,"data":"user"			,"class":"text-center"	},
		{"bSort":true		,"data":"amount"		,"class":"text-right"	},
		{"bSort":true		,"data":"status"		,"class":"text-center"	},
		{"bSort":true		,"data":"date"			,"class":"text-center"	},
		{"bSortable":false	,"data":"id"			,"class":"text-center"	}
	],
	"order": [[ 0, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
	
	}
});

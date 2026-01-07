$("#tblPmrdata").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/repack/store/store-pmrdata.php",	
	"aoColumns": [
		{"bSort":true			,"data":"round" ,"class":"text-center"	},
		{"bSort":true			,"data":"code"	,"class":"text-center"},
		{"bSort":true			,"data":"delivery_code"	,"class":"text-center"},
		{"bSort":true			,"data":"parent"	,"class":"text-center"},
		{"bSort":true			,"data":"created","class":"text-center"	},
		{"bSort":true			,"data":"pack_type"	,"class":"text-center"},
		{"bSort":true			,"data":"weight_actual"	,"class":"text-right"},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		
	}
});

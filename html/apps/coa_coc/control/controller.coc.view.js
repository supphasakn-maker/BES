$("#tblCOC").DataTable({
	responsive: true,
	"pageLength": 100,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		url : "apps/coa_coc/store/store-coc.php",
		data : function(d){
			d.date = $("#date").val()
		}
	},
	"aoColumns": [
		{"bSort":true			,"data":"code", "class": "text-center"	},
		{"bSort":true			,"data":"order_code", "class": "text-center"	},
		{"bSort":true			,"data":"customer", "class": "text-center"	},
		{"bSort":true			,"data":"delivery_date" , "class": "text-center"	},
		{"bSort":true			,"data":"amount", "class": "text-center"	},
		{"bSort":true			,"data":"total_item", "class": "text-center"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "120px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		s = '';
		if(data.status == "2"){
			s += '<span class="badge badge-primary mr-1">Approved</span>';
					
		}else{
		}
		if(data.total_item > 0){

			s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-print","fn.navigate('coa_coc','view=printablecoc&order_id="+data[0]+"')");
		}

		$("td", row).eq(6).html(s);
	}
});



$("#tblPlan").data( "selected", [] );
$("#tblPlan").DataTable({
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/production_round/store/store-round.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  },
		{"bSort":true			,"data":"import_date",class:"text-center"	},
		{"bSort":true			,"data":"import_brand"	},
		{"bSort":true			,"data":"import_lot"	},
		{"bSort":true			,"data":"amount",class:"text-right"	},
		{"bSort":true			,"data":"created"},
	],"order": [[1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		s = '';
		if(data.status == "-2"){
			s += '<span class="badge badge-warning">Splitted</span>'
			s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-thumbs-down","fn.app.production_round.round.dialog_deapprove("+data[0]+")");
		}else{
			
		}
        $("td", row).eq(0).html(s);
	}
});
fn.ui.datatable.selectable("#tblPlan","chk_plan");



$("#tblProduce").data( "selected", [] );
$("#tblProduce").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": "apps/production/store/store-produce.php",
		"data": function ( d ) {
			d.date_from = $("form[name=filter] input[name=from]").val();
			d.date_to = $("form[name=filter] input[name=to]").val();
		}
	},
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"created"	},
		{"bSort":true			,"data":"submited"	},
		{"bSort":true			,"data":"round"	},
		{"bSort":true			,"data":"import_bar"	},
		{"bSort":true			,"data":"type_material"	},
		{"bSort":true			,"data":"import_bar_weight"	},
		{"bSort":true			,"data":"import_weight_in"	},
		{"bSort":true			,"data":"import_weight_actual"	},
		{"bSort":true			,"data":"import_weight_margin"	},
		{"bSort":true			,"data":"weight_in_safe"	},
		{"bSort":true			,"data":"weight_in_total"	},
		{"bSort":true			,"data":"weight_margin"	},
		{"bSort":true			,"data":"weight_out_safe"	},
		{"bSort":true			,"data":"weight_out_total"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "120px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblProduce").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_produce",data[0],selected));
		s = '';
		if(data.status == "1"){
			s += fn.ui.button("btn btn-xs btn-info mr-1","far fa-thumbs-down",'fn.app.production.produce.dialog_unsubmit('+data[0]+')');
			s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-pen","window.location='#apps/production/index.php?view=edit&id="+data[0]+"'");
			s += fn.ui.button("btn btn-xs btn-success mr-1","far fa-thumbs-up",'fn.app.production.produce.dialog_approve('+data[0]+')');
			s += fn.ui.button("btn btn-xs btn-warning","far fa-calendar",'fn.app.production.produce.dialog_edit_submited('+data[0]+')');
			
		}else{
			//s += 'approved';
			s += fn.ui.button("btn btn-xs btn-warning","far fa-thumbs-down",'fn.app.production.produce.dialog_deapprove('+data[0]+')');
		
		}
		$("td", row).eq(15).html(s);
	}
});
fn.ui.datatable.selectable("#tblProduce","chk_produce");

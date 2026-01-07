$("#tblPacking").data( "selected", [] );
$("#tblPacking").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/packing/store/store-packing.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"date"				,"class":"text-center"	},
		{"bSort":true			,"data":"round"				,"class":"text-center"	},
		{"bSort":true			,"data":"weight_peritem"	,"class":"text-center"	},
		{"bSort":true			,"data":"total_item"		,"class":"text-center"	},
		{"bSort":true			,"data":"total_weight"		,"class":"text-center"	},
		{"bSort":true			,"data":"size"				,"class":"text-center"	},
		{"bSort":true			,"data":"remark"			,"class":"text-center"	},
		{"bSort":true			,"data":"approver_weight"	,"class":"text-center"	},
		{"bSort":true			,"data":"approver_general"	,"class":"text-center"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblPacking").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_packing",data[0],selected));
		s = '';
		if(data.status=="0"){
			s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-thumbs-up","fn.app.packing.packing.dialog_submit("+data[0]+")");
			s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.packing.packing.dialog_edit("+data[0]+")");
		}else{
			s += '<span class="badge badge-warning">Submited</span>';
		}
		
		$("td", row).eq(10).html(s);
	}
});
fn.ui.datatable.selectable("#tblPacking","chk_packing");

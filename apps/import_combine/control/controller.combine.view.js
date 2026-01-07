$("#tblCombine").data( "selected", [] );
$("#tblCombine").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url":"apps/import_combine/store/store-combine.php",
	},	
	"aoColumns": [
		{"bSortable":false		,"data":"id" ,"sClass":"text-center" , "sWidth": "120px"  },
		{"bSortable":true		,"data":"amount",class:"text-center"	},
		{"bSortable":true		,"data":"created",class:"text-center"	},
		{"bSortable":true		,"data":"updated",class:"text-center"	},
		{"bSortable":true		,"data":"remark",class:"text-center"	}
	],"order": [[ 3, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var s ='';
		
		s += fn.ui.button("btn btn-xs btn-danger","far fa-trash","fn.app.import_combine.combine.dialog_remove("+data[0]+")");	
		
		$("td", row).eq(0).html(s);
	}
});

$("#tblImport").data( "selected", [] );
$("#tblImport").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url":"apps/import/store/store-import.php",
		"data" : function(d){
			d.where = "bs_imports.parent IS NULL AND bs_imports.transfer_id IS NULL ";
		}
	},	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSortable":true		,"data":"parent",class:"text-center"	},
		{"bSortable":true		,"data":"delivery_date",class:"text-center"	},
		{"bSortable":true		,"data":"supplier",class:"text-center"	},
		{"bSortable":true		,"data":"amount",class:"text-center"	},
		{"bSortable":true		,"data":"delivery_by",class:"text-center"	},
		{"bSortable":true		,"data":"type",class:"text-center"	},
		{"bSortable":true		,"data":"comment",class:"text-center"	},
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  }
	],"order": [[ 3, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblImport").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_import",data[0],selected));
		s = '';
		var s ='';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-cut","fn.app.import_combine.combine.dialog_split("+data[0]+")");	
		
		
		$("td", row).eq(8).html(s);
		
	}
});
fn.ui.datatable.selectable("#tblImport","chk_import");


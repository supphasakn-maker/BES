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
			d.where = "bs_imports.status = 0";
		}
	},	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "120px"  },
		{"bSortable":true		,"data":"parent",class:"text-center"	},
		{"bSortable":true		,"data":"delivery_date",class:"text-center"	},
		{"bSortable":true		,"data":"supplier",class:"text-center"	},
		{"bSortable":true		,"data":"amount",class:"text-center"	},
		{"bSortable":true		,"data":"delivery_by",class:"text-center"	},
		{"bSortable":true		,"data":"type",class:"text-center"	},
		{"bSortable":true		,"data":"comment",class:"text-center"	},
	],"order": [[ 3, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblImport").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_usd",data[0],selected));
		s = '';
		if(data.status == "1"){
			s += fn.ui.button("btn btn-xs btn-outline-primary mr-1","far fa-image","fn.app.import.import.dialog_info("+data[0]+")");
		}else{
			if(data.parent != null){
				s += fn.ui.button("btn btn-xs btn-outline-primary mr-1","far fa-image","fn.app.import.import.dialog_info("+data[0]+")");
				
			}else{
				s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-cut","fn.app.import.import.dialog_split("+data[0]+")");
				s += fn.ui.button("btn btn-xs btn-outline-primary mr-1","far fa-image","fn.app.import.import.dialog_info("+data[0]+")");
				s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.import.import.dialog_edit("+data[0]+")");	
		
			}
		}
		
		$("td", row).eq(1).html(s);
	}
});
fn.ui.datatable.selectable("#tblImport","chk_usd");

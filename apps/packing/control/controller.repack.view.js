$("#tblRepack").data( "selected", [] );
$("#tblRepack").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/packing/store/store-repack.php",	
	"aoColumns": [
		{"bSortable":false	,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true		,"data":"code","class":"text-center"	},
		{"bSort":true		,"data":"weight_actual","class":"text-center"	},
		{"bSort":true		,"data":"parent","class":"text-center"	},
		{"bSortable":false	,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblRepack").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_repack",data[0],selected));
		s = '';
		
		
		if(data.parent != null){
			$("td", row).eq(3).html('<span class="badge badge-danger">splited</span>');
		}
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-cut","fn.app.packing.repack.dialog_split("+data[0]+")");
		$("td", row).eq(4).html(s);
	}
});
fn.ui.datatable.selectable("#tblRepack","chk_repack");

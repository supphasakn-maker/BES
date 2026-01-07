$("#tblRepack").data( "selected", [] );
$("#tblRepack").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/repack/store/store-repack.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"round" ,"class":"text-center"	},
		{"bSort":true			,"data":"code"	,"class":"text-center"},
		{"bSort":true			,"data":"parent"	,"class":"text-center unselectable"},
		{"bSort":true			,"data":"created","class":"text-center"	},
		{"bSort":true			,"data":"pack_type"	,"class":"text-center"},
		{"bSort":true			,"data":"weight_actual"	,"class":"text-right"},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblRepack").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_repack",data[0],selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-undo","fn.app.repack.repack.dialog_restore("+data.id+")");
		s += fn.ui.button("btn btn-xs btn-outline-warning","far fa-cut","fn.app.repack.repack.dialog_split("+data[0]+")");
		$("td", row).eq(7).html(s);
		
		if(data.parent != null){
			$("td", row).eq(3).html('<a href="javascript:;" onclick="fn.dialog.open(\'apps/repack/view/dialog.splited.view.php\',\'#dialog_packing_split\',{id:'+data.parent+'});" class="badge badge-warning">Splited</a>');
		}else{
			if(data.children == "0"){
				$("td", row).eq(3).html("-");
			}else{
				$("td", row).eq(3).html('<a href="javascript:;" onclick="fn.dialog.open(\'apps/repack/view/dialog.combined.view.php\',\'#dialog_packing_combine\',{id:'+data.id+'});" class="badge badge-primary">Combined</a>');
			}
			
		}
	}
});
fn.ui.datatable.selectable("#tblRepack","chk_repack");

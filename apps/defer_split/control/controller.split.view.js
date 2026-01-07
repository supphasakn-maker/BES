$("#tblDefer").data( "selected", [] );
$("#tblDefer").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url" : "apps/defer_split/store/store-defer.php",	
		"data" : function(d){
			var where = "bs_purchase_spot.type = 'defer'";
			d.where = where;
		}
	},
	"aoColumns": [
		{"bSortable":true		,"data":"confirm"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  },
		{"bSortable":true		,"data":"supplier"		,"class":"text-center"	},
		{"bSortable":true		,"data":"amount"		,"class":"text-center"	},
		{"bSortable":true		,"data":"usd_value"		,"class":"text-center"	},
		{"bSortable":true		,"data":"user"		,"class":"text-center"	},
		{"bSortable":true		,"data":"ref"	},
	],"order": [[ 0, "desc" ]],
	"createdRow": function ( row, data, index ) {
		s = '';
		if(data.status == "-2"){
			s += '<span class="badge badge-warning">Splitted</span>'
		}else{
			s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-cut","fn.app.defer_split.spot.dialog_split("+data[0]+")");
		}
		$("td", row).eq(1).html(s);
	}
});
fn.ui.datatable.selectable("#tblDefer","chk_spot");


$("#tblSplitted").data( "selected", [] );
$("#tblSplitted").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url" : "apps/defer_split/store/store-split.php",
	},
	"aoColumns": [
		{"bSortable":true		,"data":"id","class":"text-center"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  },
		{"bSortable":true		,"data":"purchase_id"		,"class":"text-center"	},
		{"bSortable":true		,"data":"transfer_id"		,"class":"text-center"	},
		{"bSortable":true		,"data":"amount"		,"class":"text-center"	},
		{"bSortable":true		,"data":"created"		,"class":"text-center"	},
		{"bSortable":true		,"data":"remark"	},
	],"order": [[ 0, "desc" ]],
	"createdRow": function ( row, data, index ) {
		s = '';
		if(data.transfer_id == null){
			s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-trash","fn.app.defer_split.spot.dialog_remove("+data[0]+")");
		}else{
			s += '<span class="badge badge-primary">Used</span>'
		}
		$("td", row).eq(1).html(s);
	}
});


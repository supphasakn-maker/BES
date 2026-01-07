$("#tblSpot").data( "selected", [] );
$("#tblSpot").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url" : "apps/purchase/store/store-spot.php",	
		"data" : function(d){
			var where = "bs_purchase_spot.status > -1";
			if($("form[name=filter] input[name=from]").length){
				where += " AND bs_purchase_spot.date BETWEEN '"+$("form[name=filter] input[name=from]").val()+"' AND '"+$("form[name=filter] input[name=to]").val()+"'"
			}else{
				
			}
				
			d.date_from = $("form[name=filter] input[name=from]").val();
			d.date_to = $("form[name=filter] input[name=to]").val();
			d.where = where;
		}
	},
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSortable":true			,"data":"confirm"	},
		{"bSortable":true			,"data":"parent"		,"class":"text-center unselectable"	},
		{"bSortable":true			,"data":"type"		,"class":"text-center"},
		{"bSortable":true			,"data":"supplier"		,"class":"text-center"	},
		{"bSortable":true			,"data":"amount"		,"class":"text-center"	},
		{"bSortable":true			,"data":"rate_spot"		,"class":"text-center"	},
		{"bSortable":true			,"data":"rate_pmdc"		,"class":"text-center"	},
		{"bSortable":true			,"data":"THBValue"		,"class":"text-center"	},
		{"bSortable":true			,"data":"user"		,"class":"text-center"	},
		{"bSortable":true			,"data":"ref"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblSpot").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_spot",data[0],selected));
		
		if(data.parent != null){
			$("td", row).eq(2).html('<span onclick="fn.app.purchase.spot.dialog_split_view('+data[0]+')" class="badge badge-warning">splited</span>');
		}else{
			s = '';
			s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-cut","fn.app.purchase.spot.dialog_split("+data[0]+")");
			$("td", row).eq(2).html(s);
		}
		
		if(data.currency=="THB"){
			$("td", row).eq(6).html("-");
			$("td", row).eq(7).html("-");
		}
		
		
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark m-1","far fa-pen","fn.app.purchase.spot.dialog_edit("+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-cut","fn.app.purchase.spot.dialog_split("+data[0]+")");
		$("td", row).eq(11).html(s);
	}
});
fn.ui.datatable.selectable("#tblSpot","chk_spot");

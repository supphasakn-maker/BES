$("#tblScrapData").DataTable({
	responsive: true,
    "pageLength": 50,
	"bStateSave": true,
	"autoWidth" : true,
    "pageLength": 50,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/production_scrap/store/store-scrapdata.php",	
	"aoColumns": [
		{"bSort":true			,"data":"round" ,"class":"text-center"	},
		{"bSort":true			,"data":"code"	,"class":"text-center"},
		{"bSort":true			,"data":"created","class":"text-center"	},
		{"bSort":true			,"data":"updated","class":"text-center"	},
		{"bSort":true			,"data":"pack_type"	,"class":"text-center"},
		{"bSort":true			,"data":"name"	,"class":"text-center"},
		{"bSort":true			,"data":"weight_actual"	,"class":"text-right"},
		{"bSort":true			,"data":"weight_expected"	,"class":"text-right"},
		{"bSort":true			,"data":"difference"	,"class":"text-right"},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-pen","fn.app.production_scrap.scrap.dialog_edit("+data.id+")");
		$("td", row).eq(9).html(s);
		
	},
    "footerCallback": function (row,data,start,end,display) {
        var api = this.api(),data;
        
        var tAmount = 0,tValue = 0;
        for(i in data){
            tAmount += parseFloat(data[i].weight_expected);
        }

        $("#tblScrapData [xname=tAmount]").html(fn.ui.numberic.format(tAmount,4));
        
    }
});

$("#tblScrapRefine").data( "selected", [] );
$("#tblScrapRefine").DataTable({
	responsive: true,
    "pageLength": 50,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/production_scrap/store/store-scraprefine.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"round" ,"class":"text-center"	},
		{"bSort":true			,"data":"code"	,"class":"text-center"},
		{"bSort":true			,"data":"created","class":"text-center"	},
		{"bSort":true			,"data":"updated","class":"text-center"	},
		{"bSort":true			,"data":"pack_type"	,"class":"text-center"},
		{"bSort":true			,"data":"name"	,"class":"text-center"},
		{"bSort":true			,"data":"weight_actual"	,"class":"text-right"},
		{"bSort":true			,"data":"weight_expected"	,"class":"text-right"},
		{"bSort":true			,"data":"difference"	,"class":"text-right"},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblScrapRefine").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_repack",data[0],selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-pen","fn.app.production_scrap.scrap.dialog_edit_refine("+data.id+")");
		$("td", row).eq(10).html(s);
		
	},
    "footerCallback": function (row,data,start,end,display) {
        var api = this.api(),data;
        
        var tAmount = 0,tValue = 0;
        for(i in data){
            tAmount += parseFloat(data[i].weight_expected);
        }

        $("#tblScrapRefine [xname=tAmount]").html(fn.ui.numberic.format(tAmount,4));
        
    }
});
fn.ui.datatable.selectable("#tblScrapRefine","chk_repack");
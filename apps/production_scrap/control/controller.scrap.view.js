$("#tblScrap").DataTable({
	responsive: true,
	"pageLength": 50,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/production_scrap/store/store-scrap.php",	
	"aoColumns": [
		{"bSort":true			,"data":"round" ,"class":"text-center"	},
		{"bSort":true			,"data":"code"	,"class":"text-center"},
		{"bSort":true			,"data":"parent"	,"class":"text-center unselectable"},
		{"bSort":true			,"data":"created","class":"text-center"	},
		{"bSort":true			,"data":"pack_type"	,"class":"text-center"},
		{"bSort":true			,"data":"name"	,"class":"text-center"},
		{"bSort":true			,"data":"weight_expected"	,"class":"text-right"},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		s = '';
		// s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-trash","fn.app.production_scrap.scrap.dialog_remove("+data.id+")");
		$("td", row).eq(7).html(s);

        if(data.parent != null){
            $("td", row).eq(3).html('<a href="javascript:;" onclick="fn.dialog.open(\'apps/production_scrap/view/dialog.combined.view.php\',\'#dialog_packing_combine\',{id:'+data.id+'});" class="badge badge-warning">Combined</a>');       		
        }else{
          			
    }
		
	}
});

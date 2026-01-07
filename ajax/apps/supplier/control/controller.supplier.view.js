$("#tblSupplier").data( "selected", [] );
$("#tblSupplier").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/supplier/store/store-supplier.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"name",class:"text-center"	},
		{"bSort":true			,"data":"group_name",class:"text-center"	},
		{"bSort":true			,"data":"type",class:"text-center"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblSupplier").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_supplier",data[0],selected));
		
		if(data.type=="1"){
			$("td", row).eq(3).html("USD");
		}else if(data.type=="2"){
			$("td", row).eq(3).html("THB");
		}
		s = '';
		
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.supplier.supplier.dialog_edit("+data[0]+")");
		$("td", row).eq(4).html(s);
	}
});
fn.ui.datatable.selectable("#tblSupplier","chk_supplier");

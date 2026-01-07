$("#tblCustomer").data( "selected", [] );
$("#tblCustomer").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/customer/store/store-customer.php",	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"name"	},
		{"bSort":true			,"data":"contact"	},
		{"bSort":true			,"data":"phone"	},
		{"bSort":true			,"data":"email"	},
		{"bSort":true			,"data":"remark"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblCustomer").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_customer",data[0],selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-pen","fn.app.customer.customer.dialog_edit("+data.id+")");
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-eye","fn.app.customer.customer.dialog_document("+data.id+")");
		
		$("td", row).eq(6).html(s);
	}
});
fn.ui.datatable.selectable("#tblCustomer","chk_customer");

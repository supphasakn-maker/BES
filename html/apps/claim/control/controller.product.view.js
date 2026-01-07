$('#tblProduct').DataTable({
	"bStateSave": true,
	responsive: true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/claim/store/store-product.php",
	"aoColumns": [
			{"bSortable": true	,"data" : "code"		,"sClass" : "text-center"},
			{"bSortable": true	,"data" : "date_claim"		,"sClass" : "text-center"},
			{"bSortable": true	,"data" : "order_code"			,"sClass" : "hidden-xs text-center"},
			{"bSortable": true	,"data" : "org_name"	,"sClass" : "hidden-xs text-left"},
			{"bSortable": true	,"data" : "type"	,"sClass" : "hidden-xs text-left"},
			{"bSortable": true	,"data" : "issue"		,"sClass" : "hidden-xs text-left"},
			{"bSortable": true	,"data" : "amount"		,"sClass" : "hidden-xs text-center"},
			{"bSortable": true	,"data" : "product_name"		,"sClass" : "hidden-xs text-left"},
			{"bSortable": true	,"data" : "status"		,"sClass" : "hidden-xs text-center"},
			{"bSortable": false	,"data" : "id"			,"sClass" : "text-center" , "sWidth": "80px"}
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		$('td', row).eq(1).html(moment(data.date_claim).format("DD/MM/YYYY"));

		s = '';
		var badge_status = "";
		switch(data.status){
			case "0":
				s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","fa fa-thumbs-up","fn.app.claim.product.submit("+data[0]+")");
				s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-trash","fn.app.claim.product.remove("+data[0]+")");
				badge_status += '<span class="badge badge-secondary">Draft</span>';
				break;
			case "1":
				s += fn.ui.button("btn btn-xs btn-outline-success mr-1","fa fa-thumbs-up","fn.app.claim.product.approve("+data[0]+")");
				s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","fa fa-thumbs-down","fn.app.claim.product.reject("+data[0]+")");
				badge_status += '<span class="badge badge-warning">Submited</span>';
				break;
			case "2":
				s += fn.ui.button("btn btn-xs btn-outline-primary mr-1","fa fa-thumbs-up","fn.app.claim.product.solve("+data[0]+")");
				badge_status += '<span class="badge badge-success">Approved</span>';
				break;
			case "3":
				badge_status += '<span class="badge badge-danger">Rejected</span>';
					break;
			case "4":
				s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","fa fa-thumbs-up","fn.app.claim.product.confirm("+data[0]+")");
				badge_status += '<span class="badge badge-primary">Solved</span>';
				break;
				case "5":
					badge_status += '<span class="badge badge-dark">Closed</span>';
					break;
		}
		$('td', row).eq(8).html(badge_status);
		
		
		
		
		//s = '';
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-eye","fn.navigate('claim','view=product&section=edit&claim_id="+data[0]+"')");
		$('td', row).eq(9).html(s);
		
		
	},
	"drawCallback": function( settings ) {
		$("[rel=tooltip]").tooltip();
	}
});

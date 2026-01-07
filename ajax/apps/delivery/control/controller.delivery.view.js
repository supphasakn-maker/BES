$("#tblDelivery").data( "selected", [] );
$("#tblDelivery").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		url : "apps/delivery/store/store-delivery.php",
		data : function(d){
			d.date_filter = $("#date_filter").val()
		}
	},
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"code", "class": "text-center"	},
		{"bSort":true			,"data":"order_code", "class": "text-center"	},
		{"bSort":true			,"data":"customer", "class": "text-center"	},
		{"bSort":true			,"data":"delivery_date" , "class": "text-center"	},
		{"bSort":true			,"data":"amount", "class": "text-center"	},
		{"bSort":true			,"data":"total_item", "class": "text-center"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "120px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblDelivery").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_delivery",data[0],selected));
		s = '';
		if(data.status == "2"){
			s += '<span class="badge badge-primary mr-1">Approved</span>';
						s += fn.ui.button("btn btn-xs btn-primary-danger","far fa-thumbs-down","fn.app.delivery.delivery.deapprove("+data[0]+")");
		
		}else{
			s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-truck","fn.navigate('delivery','section=prepare&id="+data[0]+"')");
			s += fn.ui.button("btn btn-xs btn-primary-dark","far fa-thumbs-up","fn.app.delivery.delivery.approve("+data[0]+")");
		}
		$("td", row).eq(7).html(s);
	}
});
fn.ui.datatable.selectable("#tblDelivery","chk_delivery");

fn.app.delivery.delivery.load_data = function(){
	$.post("apps/delivery/xhr/action-load-data.php",{id:$("#tblDeliveryDetail").attr("data-id")},function(json){
		$("#amount_total").html(json.total);
		$("#amount_remain").html(json.remain);
		if(json.remain > 0){
			$("#amount_remain").addClass("text-danger");
		}else{
			$("#amount_remain").removeClass("text-danger");
		}
	},"json");
	
};
fn.app.delivery.delivery.load_data();

$("#date_filter").change(function(){
	$("#tblDelivery").DataTable().draw();
});
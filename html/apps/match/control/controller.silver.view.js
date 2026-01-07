$("#tblSilver").data( "selected", [] );
$("#tblSilver").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/match/store/store-silver.php",	
	"aoColumns": [
	{"bSortable":false			,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"mapped"	 ,class:"text-center", "sWidth": "200px"	},
		{"bSort":true			,"data":"purchase_amount",class:"text-right pr-2"	},
		{"bSort":true			,"data":"remark"		,class:"text-left"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblSilver").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_silver",data[0],selected));
		s = '';
		s += fn.ui.button("btn btn-xs btn-danger mr-1","far fa-trash","fn.app.match.silver.unmatch("+data.id+")");
		s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-pen","fn.app.match.silver.dialog_remark("+data.id+")");
		s += fn.ui.button("btn btn-xs btn-primary","far fa-eye","fn.dialog.open('apps/match/view/dialog.silver.lookup.php','#dialog_lookup_silver',{id:"+data.id+"})");
		
		$("td", row).eq(4).html(s);
	}
});
fn.ui.datatable.selectable("#tblSilver","chk_silver");

$("#tblSales").data( "selected", [] );
$("#tblSales").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url" : "apps/match/store/store-sales.php",
		"data" : function(d){
			let date_filter = $("#tblSales_length input[name=date_filter]").val();
			if(date_filter != ""){
				d.date_filter = date_filter;
			}
		}
	},		
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"code","class":"text-center"	},
		{"bSort":true			,"data":"date","class":"text-center"	},
		{"bSort":true			,"data":"customer_name","class":"text-center"	},
		{"bSort":true			,"data":"rate_spot","class":"text-right"	},
		{"bSort":true			,"data":"amount","class":"text-right"	},
		{"bSort":true			,"data":"price","class":"text-right"	},
		{"bSort":true			,"data":"total","class":"text-right"	},
		{"bSort":false			,"data":"id","class":"text-center"	}
	],"order": [[ 1, "asc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblSales").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		if(data.mapping_item_id != null){
			$("td", row).eq(5).html('<span class="text-warning">'+data.remain+"</span>/"+data.amount);
			$(row).addClass("bg-secondary text-white");
		
			$("td", row).eq(8).html(fn.ui.button("btn btn-xs btn-danger mr-1","far fa-hide","fn.app.match.silver.remove_order("+data.mapping_item_id+")"));
		}else{
			s = fn.ui.button("btn btn-xs btn-danger mr-1","far fa-trash","fn.app.match.silver.hide_order("+data.id+")");
			
			s += fn.ui.button(
				"btn btn-xs btn-outline-dark mr-1",
				"far fa-eye",
				"fn.dialog.open('apps/match/view/dialog.order.detail.php','#dialog_order_detail',{id:"+data.id+"})"
			);
			
			$("td", row).eq(8).html(s);
		
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_sales",data[0],selected));
		
		
	}
}).on('xhr.dt', function ( e, settings, json, xhr ) {
	$("#tblSales_filter input[name=total_amount]").val(fn.ui.numberic.format(json.total.remian_unmatch,4));
	$(this).find(".total_amount").html(fn.ui.numberic.format(json.total.remain_total,4));
});
fn.ui.datatable.selectable("#tblSales","chk_sales");

$("#tblPurchase").data( "selected", [] );
$("#tblPurchase").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url" : "apps/match/store/store-purchase.php",
		"data" : function(d){
			let date_filter = $("#tblPurchase_length input[name=date_filter]").val();
			if(date_filter != ""){
				d.date_filter = date_filter;
			}
		}
	},	
	"aoColumns": [
		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSort":true			,"data":"date","class":"text-center"	},
		{"bSort":true			,"data":"supplier","class":"text-center"	},
		{"bSort":true			,"data":"amount","class":"text-right"	},
		{"bSort":true			,"data":"rate_spot","class":"text-right"	},
		{"bSort":true			,"data":"rate_pmdc","class":"text-right"	},
		{"bSort":true			,"data":"total","class":"text-right"	},
		{"bSort":true			,"data":"ref","class":"text-center"	}
	],"order": [[ 1, "asc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblPurchase").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		
		$("td", row).eq(1).html('<span title="'+data.id+'">'+data.date+"</span>");
		
		if(data.mapping_item_id != null){
			$("td", row).eq(3).html('<span class="text-warning">'+data.remain+"</span>/"+data.amount);
			$(row).addClass("bg-secondary text-white");
		}else{
			s = fn.ui.button("btn btn-xs btn-danger mr-1","far fa-trash","fn.app.match.silver.hide_spot("+data.id+")");
			$("td", row).eq(7).html(s);	
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_purchase",data[0],selected));
		
		
	}
}).on('xhr.dt', function ( e, settings, json, xhr ) {

	
	$("#tblPurchase_filter input[name=total_amount]").val(fn.ui.numberic.format(json.total.remian_unmatch,4));
	$(this).find(".total_amount").html(fn.ui.numberic.format(json.total.remain_total,4));
});
fn.ui.datatable.selectable("#tblPurchase","chk_purchase");

$("#tblSales_length").append('<input onchange=\'$("#tblSales").DataTable().draw();\' type="date" class="form-control form-control-sm" name="date_filter">');
$("#tblSales_filter").append('<label>รวม:<input readonly name="total_amount" class="form-control form-control-sm"></label>');
$("#tblPurchase_length").append('<input onchange=\'$("#tblPurchase").DataTable().draw();\' type="date" class="form-control form-control-sm" name="date_filter">');
$("#tblPurchase_filter").append('<label>รวม:<input readonly name="total_amount" class="form-control form-control-sm"></label>');


fn.app.match.silver.reset = function(){
	bootbox.confirm("Are your sure to reset", function(result){
		if(result){
			$.post("apps/match/xhr/action-reset-db.php",function(){
				window.location.reload();
			});
		}
	})
};

fn.app.match.silver.remove_order = function(id){
	bootbox.confirm("Remove this item", function(result){
		if(result){
			$.post("apps/match/xhr/action-remove-silver-order.php",{id:id},function(json){
				if(json.success){
					window.location.reload();
				}else{
					fn.alertbox("พบข้อมูลที่ถูกใช้อยู่ ต้องทำการ Unmap ทั้งหมดก่อน");
				}
				
			},"json");
		}
	})
};

fn.app.match.silver.hide_order = function(id){
	bootbox.confirm("Hide this item", function(result){
		if(result){
			$.post("apps/match/xhr/action-hide-order.php",{id:id},function(json){
				if(json.success){
					window.location.reload();
				}else{
					fn.alertbox("พบข้อมูลที่ถูกใช้อยู่ ต้องทำการ Unmap ทั้งหมดก่อน");
				}
				
			},"json");
		}
	})
};


fn.app.match.silver.hide_spot = function(id){
	bootbox.confirm("Hide this item", function(result){
		if(result){
			$.post("apps/match/xhr/action-hide-spot.php",{id:id},function(json){
				if(json.success){
					window.location.reload();
				}else{
					fn.alertbox("พบข้อมูลที่ถูกใช้อยู่ ต้องทำการ Unmap ทั้งหมดก่อน");
				}
				
			},"json");
		}
	})
};





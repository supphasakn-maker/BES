	fn.app.sales.delivery.combine_reload = function(){
		$("#tblOrder").DataTable().draw();
		$("#tblOrder").data("selected",[]);
	}
	
	fn.app.sales.delivery.dialog_combine = function(id) {
		$.ajax({
			url: "apps/sales/view/dialog.delivery.combine.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_combine_delivery"});
				
				$("#tblOrder").data( "selected", [] );
				$("#tblOrder").DataTable({
					responsive: true,
					"bStateSave": true,
					"autoWidth" : true,
					"processing": true,
					"serverSide": true,
					"ajax": {
						"url": "apps/sales/store/store-order.php",
						"data": function ( d ) {
							d.delivery_date = $("form[name=combine_filer] input[name=delivery_date]").val();
							d.customer_id= $("form[name=combine_filer] select[name=customer]").val();
							d.combine_mode = true;
						}
					},
					"aoColumns": [
						{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
						{"bSortable":true		,"data":"date","class":"text-center"	},
						{"bSort":true			,"data":"customer_name","class":"text-center"	},
						{"bSort":true			,"data":"code","class":"text-center"	},
						{"bSort":true			,"data":"amount","class":"text-right"	},
						{"bSort":true			,"data":"price","class":"text-right"	},
						{"bSort":true			,"data":"vat","class":"text-right"	},
						{"bSort":true			,"data":"net","class":"text-right"	},
						{"bSort":true			,"data":"delivery_date","class":"text-center"	}
					],"order": [[ 1, "desc" ]],
					"createdRow": function ( row, data, index ) {
						var selected = false,checked = "",s = '';
						if ( $.inArray(data.DT_RowId, $("#tblOrder").data( "selected")) !== -1 ) {
							$(row).addClass("selected");
							selected = true;
						}
						$("td", row).eq(0).html(fn.ui.checkbox("chk_order",data[0],selected));
						$("td", row).eq(4).html(fn.ui.numberic.format(data.amount));
						$("td", row).eq(5).html(fn.ui.numberic.format(data.price));
						$("td", row).eq(6).html(fn.ui.numberic.format(data.vat_amount));
						$("td", row).eq(7).html(fn.ui.numberic.format(data.net));
						
						s = '';
					}
				});
				fn.ui.datatable.selectable("#tblOrder","chk_order");
				fn.app.sales.delivery.combine_reload();
				
			}
		});
	};

	fn.app.sales.delivery.combine = function(){
		
		var item_selected = $("#tblOrder").data("selected");
		$.post("apps/sales/xhr/action-combine-delivery.php",{items:item_selected},function(response){
			if(response.success){
				$("#tblDelivery").DataTable().draw();
				$("#dialog_combine_delivery").modal("hide");
				$("#tblDelivery").data("selected",[]);
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "content_copy",
		onclick : "fn.app.sales.delivery.dialog_combine()",
		caption : "Combine"
	}));

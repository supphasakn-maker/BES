	
	fn.app.sales_bwd.order.dialog_add_delivery = function(id) {
		$.ajax({
			url: "apps/sales_bwd/view/dialog.order.add_delivery.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_delivery_order"});
			}
		});
	};

	fn.app.sales_bwd.order.add_delivery = function(){
		$.post("apps/sales_bwd/xhr/action-add_delivery-order.php",$("form[name=form_add_deliveryorder]").serialize(),function(response){
			if(response.success){
				$("#tblOrder").DataTable().draw();
				$("#dialog_add_delivery_order").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

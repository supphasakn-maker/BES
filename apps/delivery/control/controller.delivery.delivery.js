	fn.app.delivery.delivery.dialog_delivery = function(id) {
		$.ajax({
			url: "apps/delivery/view/dialog.delivery.delivery.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_delivery_delivery"});
			}
		});
	};

	fn.app.delivery.delivery.delivery = function(){
		$.post("apps/delivery/xhr/action-delivery-delivery.php",$("form[name=form_deliverydelivery]").serialize(),function(response){
			if(response.success){
				$("#tblDelivery").DataTable().draw();
				$("#dialog_delivery_delivery").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	fn.app.delivery.delivery.approve = function(id){
		bootbox.confirm("Are yure sure to approve!",function(confirmed){
			if(confirmed)
			$.post("apps/delivery/xhr/action-delivery-approve.php",{id:id},function(){
				$("#tblDelivery").DataTable().draw();
			});
		});
	};
	
	fn.app.delivery.delivery.deapprove = function(id){
		bootbox.confirm("Are yure sure to deapprove!",function(confirmed){
			if(confirmed)
			$.post("apps/delivery/xhr/action-delivery-deapprove.php",{id:id},function(){
				$("#tblDelivery").DataTable().draw();
			});
		});
	};

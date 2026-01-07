	fn.app.sales.delivery.dialog_transport = function(id) {
		$.ajax({
			url: "apps/sales/view/dialog.delivery.transport.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_transport_delivery"});
			}
		});
	};

	fn.app.sales.delivery.transport = function(){
		$.post("apps/sales/xhr/action-transport-delivery.php",$("form[name=form_transportdelivery]").serialize(),function(response){
			if(response.success){
				$("#tblDelivery").DataTable().draw();
				$("#dialog_transport_delivery").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

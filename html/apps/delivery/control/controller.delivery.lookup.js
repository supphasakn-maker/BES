	fn.app.delivery.delivery.dialog_lookup = function(id) {
		$.ajax({
			url: "apps/delivery/view/dialog.delivery.lookup.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_lookup_delivery"});
			}
		});
	};

	fn.app.delivery.delivery.lookup = function(){
		$.post("apps/delivery/xhr/action-lookup-delivery.php",$("form[name=form_lookupdelivery]").serialize(),function(response){
			if(response.success){
				$("#tblDelivery").DataTable().draw();
				$("#dialog_lookup_delivery").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

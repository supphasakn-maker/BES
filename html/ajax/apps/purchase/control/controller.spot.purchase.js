	fn.app.purchase.spot.dialog_purchase = function(id) {
		$.ajax({
			url: "apps/purchase/view/dialog.spot.purchase.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_purchase_spot"});
			}
		});
	};

	fn.app.purchase.spot.purchase = function(){
		$.post("apps/purchase/xhr/action-purchase-spot.php",$("form[name=form_purchasespot]").serialize(),function(response){
			if(response.success){
				$("#tblSpot").DataTable().draw();
				$("#tblPending").DataTable().draw();
				$("#dialog_purchase_spot").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

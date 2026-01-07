	fn.app.purchase.usd.dialog_purchase = function(id) {
		$.ajax({
			url: "apps/purchase/view/dialog.usd.purchase.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_purchase_usd"});
			}
		});
	};

	fn.app.purchase.usd.purchase = function(){
		$.post("apps/purchase/xhr/action-purchase-usd.php",$("form[name=form_purchaseusd]").serialize(),function(response){
			if(response.success){
				$("#tblSpot").DataTable().draw();
				$("#tblPending").DataTable().draw();
				$("#dialog_purchase_usd").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

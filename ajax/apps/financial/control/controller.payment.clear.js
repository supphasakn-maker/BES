	fn.app.financial.payment.dialog_clear = function(id) {
		$.ajax({
			url: "apps/financial/view/dialog.payment.clear.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_clear_payment"});
			}
		});
	};

	fn.app.financial.payment.clear = function(){
		$.post("apps/financial/xhr/action-clear-payment.php",$("form[name=form_clearpayment]").serialize(),function(response){
			if(response.success){
				$("#tblPayment").DataTable().draw();
				$("#dialog_clear_payment").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

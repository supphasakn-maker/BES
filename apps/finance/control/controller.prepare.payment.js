	fn.app.finance.prepare.dialog_payment = function(id) {
		$.ajax({
			url: "apps/finance/view/dialog.prepare.payment.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_payment_prepare"});
			}
		});
	};

	fn.app.finance.prepare.payment = function(){
		$.post("apps/finance/xhr/action-payment-prepare.php",$("form[name=form_paymentprepare]").serialize(),function(response){
			if(response.success){
				$("#tblPrepare").DataTable().draw();
				$("#dialog_payment_prepare").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

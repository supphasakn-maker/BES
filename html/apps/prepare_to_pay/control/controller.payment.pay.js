	fn.app.prepare_to_pay.payment.dialog_pay = function(id) {
		$.ajax({
			url: "apps/prepare_to_pay/view/dialog.payment.pay.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_pay_payment"});
			}
		});
	};

	fn.app.prepare_to_pay.payment.pay = function(){
		$.post("apps/prepare_to_pay/xhr/action-pay-payment.php",$("form[name=form_paypayment]").serialize(),function(response){
			if(response.success){
				$("#tblPayment").DataTable().draw();
				$("#dialog_pay_payment").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

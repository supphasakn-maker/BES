	fn.app.financial.payment.dialog_paid = function(id) {
		$.ajax({
			url: "apps/financial/view/dialog.payment.paid.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_paid_payment"});
			}
		});
	};

	fn.app.financial.payment.paid = function(){
		$.post("apps/financial/xhr/action-paid-payment.php",$("form[name=form_paidpayment]").serialize(),function(response){
			if(response.success){
				$("#tblPayment").DataTable().draw();
				$("#dialog_paid_payment").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

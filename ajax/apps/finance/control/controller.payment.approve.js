	fn.app.finance.payment.dialog_approve = function(id) {
		$.ajax({
			url: "apps/finance/view/dialog.payment.approve.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_approve_payment"});
			}
		});
	};

	fn.app.finance.payment.approve = function(){
		$.post("apps/finance/xhr/action-approve-payment.php",$("form[name=form_approvepayment]").serialize(),function(response){
			if(response.success){
				$("#tblPayment").DataTable().draw();
				$("#dialog_approve_payment").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	fn.app.finance.payment.dialog_deapprove = function(id) {
		$.ajax({
			url: "apps/finance/view/dialog.payment.deapprove.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_approve_payment"});
			}
		});
	};

	fn.app.finance.payment.deapprove = function(){
		$.post("apps/finance/xhr/action-deapprove-payment.php",$("form[name=form_approvepayment]").serialize(),function(response){
			if(response.success){
				$("#tblPayment").DataTable().draw();
				$("#dialog_approve_payment").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

	fn.app.finance.payment.dialog_edit = function(id) {
		$.ajax({
			url: "apps/finance/view/dialog.payment.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_payment"});
			}
		});
	};

	fn.app.finance.payment.edit = function(){
		$.post("apps/finance/xhr/action-edit-payment.php",$("form[name=form_editpayment]").serialize(),function(response){
			if(response.success){
				$("#tblPayment").DataTable().draw();
				$("#dialog_edit_payment").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

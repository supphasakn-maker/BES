	fn.app.finance.payment.dialog_add = function() {
		$.ajax({
			url: "apps/finance/view/dialog.payment.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_payment"});
				$('[name=customer_id]').select2();
			}
		});
	};

	fn.app.finance.payment.add = function(){
		$.post("apps/finance/xhr/action-add-payment.php",$("form[name=form_addpayment]").serialize(),function(response){
			if(response.success){
				$("#tblPayment").DataTable().draw();
				$("#dialog_add_payment").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "add_circle_outline",
		onclick : "fn.app.finance.payment.dialog_add()",
		caption : "Add"
	}));

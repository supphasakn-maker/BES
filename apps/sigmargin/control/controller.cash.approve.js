	fn.app.sigmargin.cash.dialog_approve = function(id) {
		$.ajax({
			url: "apps/sigmargin/view/dialog.cash.approve.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_approve_cash"});
			}
		});
	};

	fn.app.sigmargin.cash.approve = function(){
		$.post("apps/sigmargin/xhr/action-approve-cash.php",$("form[name=form_approvecash]").serialize(),function(response){
			if(response.success){
				$("#tblCash").DataTable().draw();
				$("#dialog_approve_cash").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

	fn.app.sigmargin.transfer.dialog_approve = function(id) {
		$.ajax({
			url: "apps/sigmargin/view/dialog.transfer.approve.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_approve_transfer"});
			}
		});
	};

	fn.app.sigmargin.transfer.approve = function(){
		$.post("apps/sigmargin/xhr/action-approve-transfer.php",$("form[name=form_approvetransfer]").serialize(),function(response){
			if(response.success){
				$("#tblTransfer").DataTable().draw();
				$("#dialog_approve_transfer").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

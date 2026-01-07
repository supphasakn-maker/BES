	fn.app.sigmargin.rollover.dialog_approve = function(id) {
		$.ajax({
			url: "apps/sigmargin/view/dialog.rollover.approve.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_approve_rollover"});
			}
		});
	};

	fn.app.sigmargin.rollover.approve = function(){
		$.post("apps/sigmargin/xhr/action-approve-rollover.php",$("form[name=form_approverollover]").serialize(),function(response){
			if(response.success){
				$("#tblRollover").DataTable().draw();
				$("#dialog_approve_rollover").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

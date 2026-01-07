	fn.app.sigmargin.Initial.dialog_approve = function(id) {
		$.ajax({
			url: "apps/sigmargin/view/dialog.Initial.approve.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_approve_Initial"});
			}
		});
	};

	fn.app.sigmargin.Initial.approve = function(){
		$.post("apps/sigmargin/xhr/action-approve-Initial.php",$("form[name=form_approveInitial]").serialize(),function(response){
			if(response.success){
				$("#tblInitial").DataTable().draw();
				$("#dialog_approve_Initial").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

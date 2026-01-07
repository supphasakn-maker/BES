	fn.app.sigmargin.claim.dialog_approve = function(id) {
		$.ajax({
			url: "apps/sigmargin/view/dialog.claim.approve.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_approve_claim"});
			}
		});
	};

	fn.app.sigmargin.claim.approve = function(){
		$.post("apps/sigmargin/xhr/action-approve-claim.php",$("form[name=form_approveclaim]").serialize(),function(response){
			if(response.success){
				$("#tblClaim").DataTable().draw();
				$("#dialog_approve_claim").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

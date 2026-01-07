	fn.app.sigmargin.interest.dialog_approve = function(id) {
		$.ajax({
			url: "apps/sigmargin/view/dialog.interest.approve.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_approve_interest"});
			}
		});
	};

	fn.app.sigmargin.interest.approve = function(){
		$.post("apps/sigmargin/xhr/action-approve-interest.php",$("form[name=form_approveinterest]").serialize(),function(response){
			if(response.success){
				$("#tblInterest").DataTable().draw();
				$("#dialog_approve_interest").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

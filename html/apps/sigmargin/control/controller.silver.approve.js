	fn.app.sigmargin.silver.dialog_approve = function(id) {
		$.ajax({
			url: "apps/sigmargin/view/dialog.silver.approve.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_approve_silver"});
			}
		});
	};

	fn.app.sigmargin.silver.approve = function(){
		$.post("apps/sigmargin/xhr/action-approve-silver.php",$("form[name=form_approvesilver]").serialize(),function(response){
			if(response.success){
				$("#tblSilver").DataTable().draw();
				$("#dialog_approve_silver").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

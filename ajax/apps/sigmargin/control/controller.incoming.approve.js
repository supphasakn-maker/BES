	fn.app.sigmargin.incoming.dialog_approve = function(id) {
		$.ajax({
			url: "apps/sigmargin/view/dialog.incoming.approve.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_approve_incoming"});
			}
		});
	};

	fn.app.sigmargin.incoming.approve = function(){
		$.post("apps/sigmargin/xhr/action-approve-incoming.php",$("form[name=form_approveincoming]").serialize(),function(response){
			if(response.success){
				$("#tblIncoming").DataTable().draw();
				$("#dialog_approve_incoming").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

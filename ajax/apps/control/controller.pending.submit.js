	fn.app.task.pending.dialog_submit = function(id) {
		$.ajax({
			url: "apps/task/view/dialog.pending.submit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_submit_pending"});
			}
		});
	};

	fn.app.task.pending.submit = function(){
		$.post("apps/task/xhr/action-submit-pending.php",$("form[name=form_submitpending]").serialize(),function(response){
			if(response.success){
				$("#tblPending").DataTable().draw();
				$("#dialog_submit_pending").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

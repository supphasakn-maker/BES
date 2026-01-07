	fn.app.task.pending.dialog_change_status = function(id) {
		$.ajax({
			url: "apps/task/view/dialog.pending.change_status.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_change_status_pending"});
			}
		});
	};

	fn.app.task.pending.change_status = function(){
		$.post("apps/task/xhr/action-change_status-pending.php",$("form[name=form_change_statuspending]").serialize(),function(response){
			if(response.success){
				$("#tblPending").DataTable().draw();
				$("#dialog_change_status_pending").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

	fn.app.task.done.dialog_view = function(id) {
		$.ajax({
			url: "apps/task/view/dialog.done.view.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_view_done"});
			}
		});
	};

	fn.app.task.done.view = function(){
		$.post("apps/task/xhr/action-view-done.php",$("form[name=form_viewdone]").serialize(),function(response){
			if(response.success){
				$("#tblDone").DataTable().draw();
				$("#dialog_view_done").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

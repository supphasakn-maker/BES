	fn.app.task.pending.dialog_comment = function(id) {
		$.ajax({
			url: "apps/task/view/dialog.pending.comment.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_comment_pending"});
			}
		});
	};

	fn.app.task.pending.comment = function(){
		$.post("apps/task/xhr/action-comment-pending.php",$("form[name=form_commentpending]").serialize(),function(response){
			if(response.success){
				$("#tblPending").DataTable().draw();
				$("#dialog_comment_pending").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

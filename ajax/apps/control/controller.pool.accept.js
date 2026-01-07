	fn.app.task.pool.dialog_accept = function(id) {
		$.ajax({
			url: "apps/task/view/dialog.pool.accept.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_accept_pool"});
			}
		});
	};

	fn.app.task.pool.accept = function(){
		$.post("apps/task/xhr/action-accept-pool.php",$("form[name=form_acceptpool]").serialize(),function(response){
			if(response.success){
				$("#tblPool").DataTable().draw();
				$("#dialog_accept_pool").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

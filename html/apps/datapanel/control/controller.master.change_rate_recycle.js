	fn.app.datapanel.master.dialog_change_rate_recycle = function(id) {
		$.ajax({
			url: "apps/datapanel/view/dialog.master.change_rate_recycle.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_change_rate_recycle"});
			}
		});
	};

	fn.app.datapanel.master.change_rate_recycle = function(){
		$.post("apps/datapanel/xhr/action-change_rate_recycle.php",$("form[name=form_change_rate_recycle]").serialize(),function(response){
			if(response.success){
				$("#tblMaster").DataTable().draw();
				$("#dialog_change_rate_recycle").modal("hide");
				fn.reload();
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

	fn.app.production.produce.dialog_approve = function(id) {
		$.ajax({
			url: "apps/production/view/dialog.produce.approve.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_approve_produce"});
			}
		});
	};

	fn.app.production.produce.approve = function(){
		$.post("apps/production/xhr/action-approve-produce.php",$("form[name=form_approveproduce]").serialize(),function(response){
			if(response.success){
				$("#tblProduce").DataTable().draw();
				$("#dialog_approve_produce").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

	fn.app.production.produce.dialog_deapprove = function(id) {
		$.ajax({
			url: "apps/production/view/dialog.produce.deapprove.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_deapprove_produce"});
			}
		});
	};

	fn.app.production.produce.deapprove = function(){
		$.post("apps/production/xhr/action-deapprove-produce.php",$("form[name=form_deapproveproduce]").serialize(),function(response){
			if(response.success){
				$("#tblProduce").DataTable().draw();
				$("#dialog_deapprove_produce").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

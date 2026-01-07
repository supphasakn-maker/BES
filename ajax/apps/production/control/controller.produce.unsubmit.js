	fn.app.production.produce.dialog_unsubmit = function(id) {
		$.ajax({
			url: "apps/production/view/dialog.produce.unsubmit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_unsubmit_produce"});
			}
		});
	};

	fn.app.production.produce.unsubmit = function(){
		$.post("apps/production/xhr/action-unsubmit-produce.php",$("form[name=form_unsubmitproduce]").serialize(),function(response){
			if(response.success){
				$("#tblProduce").DataTable().draw();
				$("#dialog_unsubmit_produce").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

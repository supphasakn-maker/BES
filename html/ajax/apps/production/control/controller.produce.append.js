	fn.app.production.produce.dialog_append = function(id) {
		$.ajax({
			url: "apps/production/view/dialog.produce.append.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_append_produce"});
			}
		});
	};

	fn.app.production.produce.append = function(){
		$.post("apps/production/xhr/action-append-produce.php",$("form[name=form_appendproduce]").serialize(),function(response){
			if(response.success){
				$("#tblProduce").DataTable().draw();
				$("#dialog_append_produce").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

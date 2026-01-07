	fn.app.match.silver.dialog_remark = function(id) {
		$.ajax({
			url: "apps/match/view/dialog.silver.remark.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remark_silver"});
			}
		});
	};

	fn.app.match.silver.remark = function(){
		$.post("apps/match/xhr/action-remark-silver.php",$("form[name=form_remarksilver]").serialize(),function(response){
			if(response.success){
				$("#tblSilver").DataTable().draw();
				$("#dialog_remark_silver").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

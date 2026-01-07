	fn.app.match.usd.dialog_remark = function(id) {
		$.ajax({
			url: "apps/match/view/dialog.usd.remark.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remark_usd"});
			}
		});
	};

	fn.app.match.usd.remark = function(){
		$.post("apps/match/xhr/action-remark-usd.php",$("form[name=form_remarkusd]").serialize(),function(response){
			if(response.success){
				$("#tblUSD").DataTable().draw();
				$("#dialog_remark_usd").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

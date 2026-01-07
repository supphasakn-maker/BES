	fn.app.trade_spot.history.dialog_add = function() {
		$.ajax({
			url: "apps/trade_spot/view/dialog.history.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_history"});
			}
		});
	};

	fn.app.trade_spot.history.add = function(){
		$.post("apps/trade_spot/xhr/action-add-history.php",$("form[name=form_addhistory]").serialize(),function(response){
			if(response.success){
				$("#tblHistory").DataTable().draw();
				$("#dialog_add_history").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "add_circle_outline",
		onclick : "fn.app.trade_spot.history.dialog_add()",
		caption : "Add"
	}));

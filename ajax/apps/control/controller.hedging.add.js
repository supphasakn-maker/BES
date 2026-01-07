	fn.app.purchase.hedging.dialog_add = function() {
		$.ajax({
			url: "apps/purchase/view/dialog.hedging.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_hedging"});
			}
		});
	};

	fn.app.purchase.hedging.add = function(){
		$.post("apps/purchase/xhr/action-add-hedging.php",$("form[name=form_addhedging]").serialize(),function(response){
			if(response.success){
				$("#tblHedging").DataTable().draw();
				$("#dialog_add_hedging").modal("hide");
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
		onclick : "fn.app.purchase.hedging.dialog_add()",
		caption : "Add"
	}));

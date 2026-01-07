	fn.app.purchase.usd.dialog_add = function() {
		$.ajax({
			url: "apps/purchase/view/dialog.usd.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_usd"});
			}
		});
	};

	fn.app.purchase.usd.add = function(){
		$.post("apps/purchase/xhr/action-add-usd.php",$("form[name=form_addusd]").serialize(),function(response){
			if(response.success){
				$("#tblUsd").DataTable().draw();
				$("form[name=form_addusd]")[0].reset();
				$("#dialog_add_usd").modal("hide");
				$("#tblPurchase").DataTable().draw();
				$("#tblPending").DataTable().draw();
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
		onclick : "fn.app.purchase.usd.dialog_add()",
		caption : "Add"
	}));

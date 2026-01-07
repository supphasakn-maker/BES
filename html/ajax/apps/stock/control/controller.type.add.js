	fn.app.stock.type.dialog_add = function() {
		$.ajax({
			url: "apps/stock/view/dialog.type.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_type"});
			}
		});
	};

	fn.app.stock.type.add = function(){
		$.post("apps/stock/xhr/action-add-type.php",$("form[name=form_addtype]").serialize(),function(response){
			if(response.success){
				$("#tblType").DataTable().draw();
				$("#dialog_add_type").modal("hide");
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
		onclick : "fn.app.stock.type.dialog_add()",
		caption : "Add"
	}));

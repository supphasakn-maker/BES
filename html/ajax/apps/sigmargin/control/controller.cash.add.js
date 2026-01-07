	fn.app.sigmargin.cash.dialog_add = function() {
		$.ajax({
			url: "apps/sigmargin/view/dialog.cash.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_cash"});
			}
		});
	};

	fn.app.sigmargin.cash.add = function(){
		$.post("apps/sigmargin/xhr/action-add-cash.php",$("form[name=form_addcash]").serialize(),function(response){
			if(response.success){
				$("#tblCash").DataTable().draw();
				$("#dialog_add_cash").modal("hide");
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
		onclick : "fn.app.sigmargin.cash.dialog_add()",
		caption : "Add"
	}));

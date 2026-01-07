	fn.app.sigmargin.silver.dialog_add = function() {
		$.ajax({
			url: "apps/sigmargin/view/dialog.silver.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_silver"});
			}
		});
	};

	fn.app.sigmargin.silver.add = function(){
		$.post("apps/sigmargin/xhr/action-add-silver.php",$("form[name=form_addsilver]").serialize(),function(response){
			if(response.success){
				$("#tblSilver").DataTable().draw();
				$("#dialog_add_silver").modal("hide");
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
		onclick : "fn.app.sigmargin.silver.dialog_add()",
		caption : "Add"
	}));

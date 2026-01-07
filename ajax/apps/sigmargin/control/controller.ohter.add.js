	fn.app.sigmargin.ohter.dialog_add = function() {
		$.ajax({
			url: "apps/sigmargin/view/dialog.ohter.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_ohter"});
			}
		});
	};

	fn.app.sigmargin.ohter.add = function(){
		$.post("apps/sigmargin/xhr/action-add-ohter.php",$("form[name=form_addohter]").serialize(),function(response){
			if(response.success){
				$("#tblOhter").DataTable().draw();
				$("#dialog_add_ohter").modal("hide");
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
		onclick : "fn.app.sigmargin.ohter.dialog_add()",
		caption : "Add"
	}));

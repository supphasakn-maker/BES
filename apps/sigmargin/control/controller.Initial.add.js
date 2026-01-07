	fn.app.sigmargin.Initial.dialog_add = function() {
		$.ajax({
			url: "apps/sigmargin/view/dialog.Initial.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_Initial"});
			}
		});
	};

	fn.app.sigmargin.Initial.add = function(){
		$.post("apps/sigmargin/xhr/action-add-Initial.php",$("form[name=form_addInitial]").serialize(),function(response){
			if(response.success){
				$("#tblInitial").DataTable().draw();
				$("#dialog_add_Initial").modal("hide");
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
		onclick : "fn.app.sigmargin.Initial.dialog_add()",
		caption : "Add"
	}));

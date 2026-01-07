	fn.app.sigmargin.rollover.dialog_add = function() {
		$.ajax({
			url: "apps/sigmargin/view/dialog.rollover.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_rollover"});
			}
		});
	};

	fn.app.sigmargin.rollover.add = function(){
		$.post("apps/sigmargin/xhr/action-add-rollover.php",$("form[name=form_addrollover]").serialize(),function(response){
			if(response.success){
				$("#tblRollover").DataTable().draw();
				$("#dialog_add_rollover").modal("hide");
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
		onclick : "fn.app.sigmargin.rollover.dialog_add()",
		caption : "Add"
	}));

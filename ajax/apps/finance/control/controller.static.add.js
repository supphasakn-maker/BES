	fn.app.bank.static.dialog_add = function() {
		$.ajax({
			url: "apps/bank/view/dialog.static.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_static"});
			}
		});
	};

	fn.app.bank.static.add = function(){
		$.post("apps/bank/xhr/action-add-static.php",$("form[name=form_addstatic]").serialize(),function(response){
			if(response.success){
				$("#tblStatic").DataTable().draw();
				$("#dialog_add_static").modal("hide");
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
		onclick : "fn.app.bank.static.dialog_add()",
		caption : "Add"
	}));

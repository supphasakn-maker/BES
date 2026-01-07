	fn.app.employee.employee.dialog_add = function() {
		$.ajax({
			url: "apps/employee/view/dialog.employee.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_employee"});
			}
		});
	};

	fn.app.employee.employee.add = function(){
		$.post("apps/employee/xhr/action-add-employee.php",$("form[name=form_addemployee]").serialize(),function(response){
			if(response.success){
				$("#tblEmployee").DataTable().draw();
				$("#dialog_add_employee").modal("hide");
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
		onclick : "fn.app.employee.employee.dialog_add()",
		caption : "Add"
	}));

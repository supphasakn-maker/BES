	fn.app.employee.department.dialog_add = function() {
		$.ajax({
			url: "apps/employee/view/dialog.department.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_department"});
			}
		});
	};

	fn.app.employee.department.add = function(){
		$.post("apps/employee/xhr/action-add-department.php",$("form[name=form_adddepartment]").serialize(),function(response){
			if(response.success){
				$("#tblDepartment").DataTable().draw();
				$("#dialog_add_department").modal("hide");
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
		onclick : "fn.app.employee.department.dialog_add()",
		caption : "Add"
	}));

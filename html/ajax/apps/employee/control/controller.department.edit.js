	fn.app.employee.department.dialog_edit = function(id) {
		$.ajax({
			url: "apps/employee/view/dialog.department.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_department"});
			}
		});
	};

	fn.app.employee.department.edit = function(){
		$.post("apps/employee/xhr/action-edit-department.php",$("form[name=form_editdepartment]").serialize(),function(response){
			if(response.success){
				$("#tblDepartment").DataTable().draw();
				$("#dialog_edit_department").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

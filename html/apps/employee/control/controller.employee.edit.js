	fn.app.employee.employee.dialog_edit = function(id) {
		$.ajax({
			url: "apps/employee/view/dialog.employee.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_employee"});
			}
		});
	};

	fn.app.employee.employee.edit = function(){
		$.post("apps/employee/xhr/action-edit-employee.php",$("form[name=form_editemployee]").serialize(),function(response){
			if(response.success){
				$("#tblEmployee").DataTable().draw();
				$("#dialog_edit_employee").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

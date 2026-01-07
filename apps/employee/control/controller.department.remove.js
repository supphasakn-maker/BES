	fn.app.employee.department.dialog_remove = function() {
		var item_selected = $("#tblDepartment").data("selected");
		$.ajax({
			url: "apps/employee/view/dialog.department.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_department").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_department").modal("show");
				$("#dialog_remove_department .btnConfirm").click(function(){
					fn.app.employee.department.remove();
				});
			}
		});
	};

	fn.app.employee.department.remove = function(){
		var item_selected = $("#tblDepartment").data("selected");
		$.post("apps/employee/xhr/action-remove-department.php",{items:item_selected},function(response){
			$("#tblDepartment").data("selected",[]);
			$("#tblDepartment").DataTable().draw();
			$("#dialog_remove_department").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.employee.department.dialog_remove()",
		caption : "Remove"
	}));

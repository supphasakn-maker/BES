	fn.app.employee.employee.dialog_remove = function() {
		var item_selected = $("#tblEmployee").data("selected");
		$.ajax({
			url: "apps/employee/view/dialog.employee.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_employee").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_employee").modal("show");
				$("#dialog_remove_employee .btnConfirm").click(function(){
					fn.app.employee.employee.remove();
				});
			}
		});
	};

	fn.app.employee.employee.remove = function(){
		var item_selected = $("#tblEmployee").data("selected");
		$.post("apps/employee/xhr/action-remove-employee.php",{items:item_selected},function(response){
			$("#tblEmployee").data("selected",[]);
			$("#tblEmployee").DataTable().draw();
			$("#dialog_remove_employee").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.employee.employee.dialog_remove()",
		caption : "Remove"
	}));

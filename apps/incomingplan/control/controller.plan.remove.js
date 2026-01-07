	fn.app.incomingplan.plan.dialog_remove = function() {
		var item_selected = $("#tblPlan").data("selected");
		$.ajax({
			url: "apps/incomingplan/view/dialog.plan.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_plan").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_plan").modal("show");
				$("#dialog_remove_plan .btnConfirm").click(function(){
					fn.app.incomingplan.plan.remove();
				});
			}
		});
	};

	fn.app.incomingplan.plan.remove = function(){
		var item_selected = $("#tblPlan").data("selected");
		$.post("apps/incomingplan/xhr/action-remove-plan.php",{items:item_selected},function(response){
			$("#tblPlan").data("selected",[]);
			$("#tblPlan").DataTable().draw();
			$("#dialog_remove_plan").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.incomingplan.plan.dialog_remove()",
		caption : "Remove"
	}));

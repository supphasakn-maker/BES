	fn.app.audit.match.dialog_remove = function() {
		var item_selected = $("#tblMatch").data("selected");
		$.ajax({
			url: "apps/audit/view/dialog.match.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_match").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_match").modal("show");
				$("#dialog_remove_match .btnConfirm").click(function(){
					fn.app.audit.match.remove();
				});
			}
		});
	};

	fn.app.audit.match.remove = function(){
		var item_selected = $("#tblMatch").data("selected");
		$.post("apps/audit/xhr/action-remove-match.php",{items:item_selected},function(response){
			$("#tblMatch").data("selected",[]);
			$("#tblMatch").DataTable().draw();
			$("#dialog_remove_match").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.audit.match.dialog_remove()",
		caption : "Remove"
	}));

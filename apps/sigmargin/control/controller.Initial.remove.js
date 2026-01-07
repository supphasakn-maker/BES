	fn.app.sigmargin.Initial.dialog_remove = function() {
		var item_selected = $("#tblInitial").data("selected");
		$.ajax({
			url: "apps/sigmargin/view/dialog.Initial.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_Initial").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_Initial").modal("show");
				$("#dialog_remove_Initial .btnConfirm").click(function(){
					fn.app.sigmargin.Initial.remove();
				});
			}
		});
	};

	fn.app.sigmargin.Initial.remove = function(){
		var item_selected = $("#tblInitial").data("selected");
		$.post("apps/sigmargin/xhr/action-remove-Initial.php",{items:item_selected},function(response){
			$("#tblInitial").data("selected",[]);
			$("#tblInitial").DataTable().draw();
			$("#dialog_remove_Initial").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.sigmargin.Initial.dialog_remove()",
		caption : "Remove"
	}));

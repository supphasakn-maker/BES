	fn.app.sigmargin.daily.dialog_remove = function() {
		var item_selected = $("#tblDaily").data("selected");
		$.ajax({
			url: "apps/sigmargin/view/dialog.daily.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_daily").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_daily").modal("show");
				$("#dialog_remove_daily .btnConfirm").click(function(){
					fn.app.sigmargin.daily.remove();
				});
			}
		});
	};

	fn.app.sigmargin.daily.remove = function(){
		var item_selected = $("#tblDaily").data("selected");
		$.post("apps/sigmargin/xhr/action-remove-daily.php",{items:item_selected},function(response){
			$("#tblDaily").data("selected",[]);
			$("#tblDaily").DataTable().draw();
			$("#dialog_remove_daily").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.sigmargin.daily.dialog_remove()",
		caption : "Remove"
	}));

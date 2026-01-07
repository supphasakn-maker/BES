	fn.app.reserve_silver.reserve.dialog_remove = function() {
		var item_selected = $("#tblReserve").data("selected");
		$.ajax({
			url: "apps/reserve_silver/view/dialog.reserve.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_reserve").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_reserve").modal("show");
				$("#dialog_remove_reserve .btnConfirm").click(function(){
					fn.app.reserve_silver.reserve.remove();
				});
			}
		});
	};

	fn.app.reserve_silver.reserve.remove = function(){
		var item_selected = $("#tblReserve").data("selected");
		$.post("apps/reserve_silver/xhr/action-remove-reserve.php",{items:item_selected},function(response){
			$("#tblReserve").data("selected",[]);
			$("#tblReserve").DataTable().draw();
			$("#dialog_remove_reserve").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.reserve_silver.reserve.dialog_remove()",
		caption : "Remove"
	}));

	fn.app.sales.spot.dialog_remove = function() {
		var item_selected = $("#tblSpot").data("selected");
		$.ajax({
			url: "apps/sales/view/dialog.spot.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_spot").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_spot").modal("show");
				$("#dialog_remove_spot .btnConfirm").click(function(){
					fn.app.sales.spot.remove();
				});
			}
		});
	};

	fn.app.sales.spot.remove = function(id){
		if(typeof id != "undefined"){
			fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
				$.post("apps/sales/xhr/action-remove-spot.php",{item:id},function(response){
					$("#tblSpot").DataTable().draw();
					fn.notify.successbox("","Remove Success");
				});
			});
		}else{
			var item_selected = $("#tblSpot").data("selected");
			$.post("apps/sales/xhr/action-remove-spot.php",{items:item_selected},function(response){
				$("#tblSpot").data("selected",[]);
				$("#tblSpot").DataTable().draw();
				$("#dialog_remove_spot").modal("hide");
			});
		}
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.sales.spot.dialog_remove()",
		caption : "Remove"
	}));

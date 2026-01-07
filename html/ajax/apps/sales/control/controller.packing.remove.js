	fn.app.sales.packing.dialog_remove = function() {
		var item_selected = $("#tblPacking").data("selected");
		$.ajax({
			url: "apps/sales/view/dialog.packing.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_packing").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_packing").modal("show");
				$("#dialog_remove_packing .btnConfirm").click(function(){
					fn.app.sales.packing.remove();
				});
			}
		});
	};

	fn.app.sales.packing.remove = function(id){
		
		if(typeof id != "undefined"){
			fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
				$.post("apps/sales/xhr/action-remove-packing.php",{item:id},function(response){
					$("#tblPacking").DataTable().draw();
					fn.notify.successbox("","Remove Success");
				});
			});
		}else{
			var item_selected = $("#tblOrder").data("selected");
				$.post("apps/sales/xhr/action-remove-packing.php",{items:item_selected},function(response){
					$("#tblOrder").data("selected",[]);
					$("#tblOrder").DataTable().draw();
					$("#dialog_remove_order").modal("hide");
				});
		}
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.sales.packing.dialog_remove()",
		caption : "Remove"
	}));

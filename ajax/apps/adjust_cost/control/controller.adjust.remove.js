
	fn.app.adjust_cost.adjust.remove = function(id){
		bootbox.confirm({
			title: "Are you sure to remove?",
			message: "ต้องการลบข้อมูล",
			buttons: {
				
				confirm: {
					label: '<i class="fa fa-times"></i> ลบเลย'
				}
			},
			callback: function (result) {
				if(result){
					$.post("apps/adjust_cost/xhr/action-remove-adjust.php",{id:id},function(response){
						
						$("#tblAdjusted").DataTable().draw();
						$("#tblPurchase").DataTable().draw();
						$("#tblPurchaseNew").DataTable().draw();
						$("#tblSales").DataTable().draw();

					});
				}
				console.log('This was logged in the callback: ' + result);
			}
		});
		
		
		/*
		var item_selected = $("#tblAdjust").data("selected");
		$.post("apps/adjust_cost/xhr/action-remove-adjust.php",{items:item_selected},function(response){
			$("#tblAdjust").data("selected",[]);
			$("#tblAdjust").DataTable().draw();
			$("#dialog_remove_adjust").modal("hide");
		});
		*/
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.adjust_cost.adjust.dialog_remove()",
		caption : "Remove"
	}));

	fn.app.finance.payment.dialog_remove = function() {
		var item_selected = $("#tblPayment").data("selected");
		$.ajax({
			url: "apps/finance/view/dialog.payment.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_payment").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_payment").modal("show");
				$("#dialog_remove_payment .btnConfirm").click(function(){
					fn.app.finance.payment.remove();
				});
			}
		});
	};
	
	
	fn.app.finance.payment.remove = function(id){
		bootbox.confirm({
			message: "Are sure to unmatch this record?",
			buttons: {
				confirm: {label: 'Remove',className: 'btn-danger'},
				cancel: {label: 'No',className: 'btn-secondary'}
			},
			callback: function (result) {
				if(result){
					$.post("apps/finance/xhr/action-remove-payment.php",{id:id},function(response){
						$("#tblPayment").DataTable().draw();
						if(response.success){
							
							
						}else{
							fn.notify.warnbox(response.msg,"Oops...");
						}
					},"json");
				}
			}
		});
		
		
		
	};

	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.finance.payment.dialog_remove()",
		caption : "Remove"
	}));

	fn.app.sales.order.dialog_add = function() {
		$.ajax({
			url: "apps/sales/view/dialog.order.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_order"});
				
				$("select[name=customer_id]").change(function(){
					$.post("apps/customer/xhr/action-load-customer.php",{id:$(this).val()},function(json){
						$("textarea[name=billing_address]").val(json.billing_address);
						$("textarea[name=shipping_address]").val(json.shipping_address);
						$("input[name=contact]").val(json.contact);
						$("input[name=contact]").val(json.contact);
						$("select[name=sales]").val(json.sales);
					},"json");
				});
				
				$("input[name=delivery_lock]").change(function(){
					$("input[name=delivery_date]").prop('readOnly',$(this).prop('checked'));
				});
			}
		});
	};

	fn.app.sales.order.add = function(){
		$.post("apps/sales/xhr/action-add-order.php",$("form[name=form_addorder]").serialize(),function(response){
			if(response.success){
				$("#tblOrder").DataTable().draw();
				$("#dialog_add_order").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "add_circle_outline",
		onclick : "fn.app.sales.order.dialog_add()",
		caption : "Add"
	}));

	fn.app.sales.quick_order.dialog_add = function() {
		$.ajax({
			url: "apps/sales/view/dialog.quick_order.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_quick_order"});
			}
		});
	};

	fn.app.sales.quick_order.add = function(){
		$.post("apps/sales/xhr/action-add-quick_order.php",$("form[name=form_addquick_order]").serialize(),function(response){
			if(response.success){
				$("#tblQuick_order").DataTable().draw();
				$("#tblQuickOrder").DataTable().draw();
				
				$("form[name=form_addquick_order]")[0].reset();
				$("form[name=form_addquick_order] select[name=customer_id]").val("").trigger('change.select2');;
				$("#dialog_add_quick_order").modal("hide");
				
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
		onclick : "fn.app.sales.quick_order.dialog_add()",
		caption : "Add"
	}));

	fn.app.customer.customer.dialog_add = function() {
		$.ajax({
			url: "apps/customer/view/dialog.customer.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_customer"});
			}
		});
	};

	fn.app.customer.customer.add = function(){
		$.post("apps/customer/xhr/action-add-customer.php",$("form[name=form_addcustomer]").serialize(),function(response){
			if(response.success){
				$("#tblCustomer").DataTable().draw();
				$("#dialog_add_customer").modal("hide");
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
		onclick : "fn.app.customer.customer.dialog_add()",
		caption : "Add"
	}));

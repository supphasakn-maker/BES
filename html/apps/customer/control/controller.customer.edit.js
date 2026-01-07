	fn.app.customer.customer.dialog_edit = function(id) {
		console.log(typeof id);
		if(id != ""){
			$.ajax({
				url: "apps/customer/view/dialog.customer.edit.php",
				data: {id:id},
				type: "POST",
				dataType: "html",
				success: function(html){
					$("body").append(html);
					fn.ui.modal.setup({dialog_id : "#dialog_edit_customer"});
					
				}
			});
		}else{
			fn.notify.warnbox("โปรดเลือกลูกค้า","Oops...");
		}
	};

	fn.app.customer.customer.edit = function(){
		$.post("apps/customer/xhr/action-edit-customer.php",$("form[name=form_editcustomer]").serialize(),function(response){
			if(response.success){
				$("#tblCustomer").DataTable().draw();
				$("#dialog_edit_customer").modal("hide");
				$("form[name=order] select[name=customer_id]").change();
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

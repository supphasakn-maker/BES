	fn.app.sales.delivery.dialog_billing = function(id) {
		$.ajax({
			url: "apps/sales/view/dialog.delivery.billing.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_billing_delivery"});
			}
		});
	};

	fn.app.sales.delivery.billing = function(){
		$.post("apps/sales/xhr/action-billing-delivery.php",$("form[name=form_billingdelivery]").serialize(),function(response){
			if(response.success){
				$("#tblDelivery").DataTable().draw();
				$("#dialog_billing_delivery").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	fn.app.sales.delivery.append_billing = function(){
		let s = '';
		s += '<div class="form-group row">';
			s += '<label class="col-sm-2 col-form-label text-right">Billing ID</label>';
			s += '<div class="col-sm-8">';
				s += '<input type="" class="form-control" name="billing_id[]" placeholder="หมายเลขบิล" >';
			s += '</div>';
			s += '<div class="col-sm-2"><button onclick="$(this).parent().parent().remove();" class="btn btn-danger">Remove</button></div>';
		s += '</div>';
		
		$("form[name=form_billingdelivery]").append(s);
		
	}
	
	

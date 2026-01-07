	
	fn.app.trust_receipt.tr.dialog_payment = function(id) {
		$.ajax({
			url: "apps/trust_receipt/view/dialog.tr.payment.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_payment"});
				
				$("#dialog_payment [name=interest_start],#dialog_payment [name=interest_end]").unbind().change(function(){
					let start = new Date($("#dialog_payment [name=interest_start]").val());
					let end = new Date($("#dialog_payment [name=interest_end]").val());
					var Difference_In_Time = end.getTime() - start.getTime();
					var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);
					$("#dialog_payment [name=interest_day]").val(Difference_In_Days).change();
					
				});
				
				$("#dialog_payment [name=rate_interest],#dialog_payment [name=interest_day],#dialog_payment [name=paid]").unbind().change(function(){
					let rate_interest = parseFloat($("#dialog_payment [name=rate_interest]").val())/100;
					let day = parseFloat($("#dialog_payment [name=interest_day]").val());
					let principle = parseFloat($("#dialog_payment [name=principle]").val());
					let paid = parseFloat($("#dialog_payment [name=paid]").val());
					let interest = (rate_interest)/365 * day * principle;
					$("#dialog_payment [name=interest]").val(interest.toFixed(2))
					console.log(principle-paid);
					
					$("#dialog_payment [name=remain]").val((principle-paid).toFixed(2));
					
					
				});
			}
		});
	};

	fn.app.trust_receipt.tr.payment = function(){
		$.post("apps/trust_receipt/xhr/action-add-payment.php",$("form[name=form_payment]").serialize(),function(response){
			if(response.success){
				fn.app.trust_receipt.tr.load();
				$("#dialog_payment").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	fn.app.trust_receipt.tr.dialog_payusd = function(id) {
		$.ajax({
			url: "apps/trust_receipt/view/dialog.tr.payusd.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_payusd"});
				
				$("#dialog_payusd [name=interest_start],#dialog_payusd [name=interest_end]").unbind().change(function(){
					let start = new Date($("#dialog_payusd [name=interest_start]").val());
					let end = new Date($("#dialog_payusd [name=interest_end]").val());
					var Difference_In_Time = end.getTime() - start.getTime();
					var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);
					$("#dialog_payusd [name=interest_day]").val(Difference_In_Days).change();
					
				});
				
				$("#dialog_payusd [name=rate_counter],#dialog_payusd [name=paid]").unbind().change(function(){
					let rate_interest = parseFloat($("#dialog_payusd [name=rate_interest]").val())/100;
					let day = parseFloat($("#dialog_payusd [name=interest_day]").val());
					let principle = parseFloat($("#dialog_payusd [name=principle]").val());
					let paid = parseFloat($("#dialog_payusd [name=paid]").val())
					let rate_counter = parseFloat($("#dialog_payusd [name=rate_counter]").val());
					
					let interest = principle * rate_interest * day/360 * rate_counter;
					
					$("#dialog_payusd [name=interest]").val(interest.toFixed(4))
					$("#dialog_payusd [name=remain]").val((principle-paid).toFixed(4));
					
					
				});
				
			}
		});
	};

	fn.app.trust_receipt.tr.payusd = function(){
		$.post("apps/trust_receipt/xhr/action-add-payusd.php",$("form[name=form_payusd]").serialize(),function(response){
			if(response.success){
				fn.app.trust_receipt.tr.load();
				$("#dialog_payusd").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
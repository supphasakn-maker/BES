	fn.app.adjust_cost.adjust.dialog_add = function() {
		$.ajax({
			url: "apps/adjust_cost/view/dialog.adjust.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_adjust"});
			}
		});
	};

	fn.app.adjust_cost.adjust.add = function(){
		
		$.post("apps/adjust_cost/xhr/action-add-adjust.php",{
			purchase : $("#tblPurchase").data("selected"),
			purchase_new : $("#tblPurchaseNew").data("selected"),
			sales : $("#tblSales").data("selected"),
			date : $("form[name=adding] input[name=date]").val()
		},function(response){
			if(response.success){
				$("#tblPurchase").data( "selected", [] );
				$("#tblPurchaseNew").data( "selected", [] );
				$("#tblSales").data( "selected", [] );
				$("#tblPurchase").DataTable().draw();
				$("#tblPurchaseNew").DataTable().draw();
				$("#tblSales").DataTable().draw();
				fn.notify.successbox(response.msg,"Complete");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
		
		
		$.post("apps/adjust_cost/xhr/action-add-adjust.php",$("form[name=form_addadjust]").serialize(),function(response){
			if(response.success){
				$("#tblAdjust").DataTable().draw();
				$("#dialog_add_adjust").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	fn.app.adjust_cost.adjust.calcuate = function(){
		$.post("apps/adjust_cost/xhr/action-calcuate.php",{
			purchase : $("#tblPurchase").data("selected"),
			purchase_new : $("#tblPurchaseNew").data("selected"),
			sales : $("#tblSales").data("selected")
		},function(data){
			
			let value_a = data.purchase_net-data.new_net;
			let value_b = data.sales_value-data.new_value;
			let value_c = data.new_discount-data.purchase_discount;
			let value_d = value_a + value_b + value_c;
			let value_e = data.sales_value-data.purchase_value;
			
			let cost_a = data.purchase_value-data.new_value;
			let cost_b = data.purchase_discount-data.new_discount;
			
			let value_profit = data.purchase_value-data.new_value;
			let value_netprofit = data.purchase_net-data.sales_net;
			
			$("[name=value_a]").val(value_a.toFixed(4));
			$("[name=value_b]").val(value_b.toFixed(4));
			$("[name=value_c]").val(value_c.toFixed(4));
			$("[name=value_d]").val(value_d.toFixed(4));
			$("[name=value_e]").val(value_e.toFixed(4));
			$("[name=cost_a]").val(cost_a.toFixed(4));
			$("[name=cost_b]").val(cost_b.toFixed(4));
			
			
			
			$("[name=value_profit]").val(value_profit.toFixed(4));
			$("[name=value_netprofit]").val(value_netprofit.toFixed(4));

		},"json");
	}

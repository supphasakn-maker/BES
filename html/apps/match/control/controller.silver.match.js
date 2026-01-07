	fn.app.match.silver.dialog_match = function() {
		var sales_selected = $("#tblSales").data("selected");
		var purchase_selected = $("#tblPurchase").data("selected");
		$.ajax({
			url: "apps/match/view/dialog.silver.match.php",
			data: {sales:sales_selected,purchase:purchase_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_match_silver"});
				fn.app.match.silver.dialog_match_calculation();
			}
		});
	};
	
	fn.app.match.silver.dialog_match_calculation = function() {
		let total_order_amount = 0;
		$("[xname=order_amount]").each(function(){
			total_order_amount += parseFloat($(this).val());
		});
		$("#silver_total_match_order").html(fn.ui.numberic.format(total_order_amount,4));
		
		let total_order_purchase = 0;
		$("[xname=purchase_amount]").each(function(){
			total_order_purchase += parseFloat($(this).val());
		});
		$("#silver_total_match_purchase").html(fn.ui.numberic.format(total_order_purchase,4));
	}

	fn.app.match.silver.match = function(){
		$.post("apps/match/xhr/action-match-silver.php",$("form[name=form_matchsilver]").serialize(),function(response){
			if(response.success){
				fn.app.match.silver.clear_selection();
				$("#dialog_match_silver").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	fn.app.match.silver.clear_selection = function(){
		
		$("#tblPurchase [control=chk_purchase]").removeClass("fa-check-square").addClass("fa-square");
		$("#tblSilver [control=chk_silver]").removeClass("fa-check-square").addClass("fa-square");
		$("#tblSales [control=chk_sales]").removeClass("fa-check-square").addClass("fa-square");
		
		$("#tblSilver").data( "selected", [] );
		$("#tblSales").data( "selected", [] );
		$("#tblPurchase").data( "selected", [] );
		
		$("#tblSilver").DataTable().draw();
		$("#tblSales").DataTable().draw();
		$("#tblPurchase").DataTable().draw();
	} 

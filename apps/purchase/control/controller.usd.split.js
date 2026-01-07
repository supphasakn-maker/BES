	fn.app.purchase.usd.dialog_split = function(id) {
		$.ajax({
			url: "apps/purchase/view/dialog.usd.split.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_split_usd"});
				
				
				$("input[name=split]").change(function(){
					let amount = parseFloat($("input[name=amount]").val());
					let split = parseFloat($(this).val());
					$("input[name=remain]").val((amount-split).toFixed(4));
				});
				
				$("input[name=remain]").change(function(){
					let amount = parseFloat($("input[name=amount]").val());
					let remain = parseFloat($(this).val());
					$("input[name=split]").val((amount-remain).toFixed(4));
				});
			}
		});
	};

	fn.app.purchase.usd.split = function(){
		$.post("apps/purchase/xhr/action-split-usd.php",$("form[name=form_splitusd]").serialize(),function(response){
			if(response.success){
				$("#tblUsd").DataTable().draw();
				$("#dialog_split_usd").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	fn.app.purchase.usd.dialog_split_view = function(id) {
		$.ajax({
			url: "apps/purchase/view/dialog.usd.split.view.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_split_usd_view"});
				
				
			}
		});
	};
	
	fn.app.purchase.usd.dialog_split_children = function(id) {
		$.ajax({
			url: "apps/purchase/view/dialog.usd.split.children.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_split_usd_view"});
				
				
			}
		});
	};

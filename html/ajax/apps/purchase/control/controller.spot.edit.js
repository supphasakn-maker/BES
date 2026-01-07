	fn.app.purchase.spot.dialog_edit = function(id) {
		$.ajax({
			url: "apps/purchase/view/dialog.spot.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_spot"});
				
				$("form[name=form_editspot] select[name=currency]").change(function(){
					if($(this).val()=="USD"){
						$("form[name=form_editspot] input[name=rate_spot]").parent().parent().show();
						$("form[name=form_editspot] input[name=THBValue]").parent().parent().hide();
					}else{
						$("form[name=form_editspot] input[name=rate_spot]").parent().parent().hide();
						$("form[name=form_editspot] input[name=THBValue]").parent().parent().show();
					}
				});
				
				$("form[name=form_editspot] select[name=supplier_id]").change(function(){
					$.post("apps/supplier/xhr/action-load-supplier.php",{id:$(this).val()},function(supplier){
						if(supplier.type=="1"){
							$("form[name=form_editspot] select[name=currency]").val("USD").change();
						}else if(supplier.type=="2"){
							$("form[name=form_editspot] select[name=currency]").val("THB").change();
						}
					},"json");
				}).change();
			}
		});
	};

	fn.app.purchase.spot.edit = function(){
		$.post("apps/purchase/xhr/action-edit-spot.php",$("form[name=form_editspot]").serialize(),function(response){
			if(response.success){
				$("#tblSpot").DataTable().draw();
				$("#tblPurchase").DataTable().draw();
				$("#tblPending").DataTable().draw();
				$("#dialog_edit_spot").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

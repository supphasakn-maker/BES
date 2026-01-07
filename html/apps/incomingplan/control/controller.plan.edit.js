	fn.app.incomingplan.plan.dialog_edit = function(id) {
		$.ajax({
			url: "apps/incomingplan/view/dialog.plan.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_plan"});
				$("select[name=import_id]").unbind();
				$("select[name=import_id]").select2();
				$("select[name=import_id]").change(function(){
					$.post("apps/incomingplan/xhr/action-load-reserve.php",{id:$(this).val()},function(json){
						$("input[name=amount]").val(json.weight_lock);
						$("input[name=supplier_id]").val(json.supplier_id);
						$("input[name=import_brand]").val(json.brand);
						$("input[name=import_date]").val(json.lock_date);
					},"json");
				});
			}
		});
	};

	fn.app.incomingplan.plan.edit = function(){
		$.post("apps/incomingplan/xhr/action-edit-plan.php",$("form[name=form_editplan]").serialize(),function(response){
			if(response.success){
				$("#tblPlan").DataTable().draw();
				$("#dialog_edit_plan").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

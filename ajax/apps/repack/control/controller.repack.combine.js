	fn.app.repack.repack.dialog_combine = function(id) {
		var item_selected = $("#tblRepack").data("selected");
		$.ajax({
			url: "apps/repack/view/dialog.repack.combine.php",
			data: {items:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_combine_repack"});
				
				$("input[name=weight_actual]").val($("input[name=total_weight_actual]").val());
				
				$("form[name=form_combinerepack] select[name=pack_name]").unbind().change(function(){
					var caption_pack = $(this).val();
					var value_pack = $(this).find(":selected").attr("data-value");
					var readonly_pack = $(this).find(":selected").attr("data-readonly");
					$("form[name=form_combinerepack] input[name=weight_expected]").val(value_pack);
					if(readonly_pack=="false"){
						$("form[name=form_combinerepack] input[name=weight_expected]").attr("readonly",false);
					}else{
						$("form[name=form_combinerepack] input[name=weight_expected]").attr("readonly",true);
					}
				}).change();
			}
		});
	};

	fn.app.repack.repack.combine = function(){
		$.post("apps/repack/xhr/action-combine-repack.php",$("form[name=form_combinerepack]").serialize(),function(response){
			if(response.success){
				$("#tblRepack").DataTable().draw();
				$("#dialog_combine_repack").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

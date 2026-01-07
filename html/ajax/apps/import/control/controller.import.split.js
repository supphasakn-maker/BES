	fn.app.import.import.dialog_split = function(id) {
		$.ajax({
			url: "apps/import/view/dialog.import.split.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_split_import"});
				
				$("[name=form_splitimport] [name=split]").change(function(){
					var amount = parseFloat($("[name=form_splitimport] [name=amount]").val());
					var splited = parseFloat($(this).val());
					var remain = amount-splited;
					$("[name=form_splitimport] [name=remain]").val(remain.toFixed(4));
				});
			}
		});
	};

	fn.app.import.import.split = function(){
		$.post("apps/import/xhr/action-split-import.php",$("form[name=form_splitimport]").serialize(),function(response){
			if(response.success){
				$("#tblImport").DataTable().draw();
				$("#dialog_split_import").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

	fn.app.bank.statement.dialog_mapping = function(id) {
		$.ajax({
			url: "apps/bank/view/dialog.statement.mapping.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_mapping_statement"});
				
				$("form[name=form_mappingstatement] input[name=bank_date],form[name=form_mappingstatement] input[name=bank_id]").unbind().change(function(){
					var bank_id = $("form[name=form_mappingstatement] select[name=bank_id]").val();
					var bank_date = $("form[name=form_mappingstatement] input[name=bank_date]").val();
					$.post("apps/bank/xhr/action-load-bank-line.php",{bank_id : bank_id,bank_date : bank_date},function(json){
						let s = '';
						for(i in json){
							s += '<option value="'+json[i].id+'">'+json[i].amount + (json[i].type=="1"?" Debit":" Credit")+'</option>';
						}
						$("form[name=form_mappingstatement] select[name=bank_line]").html(s);
					},"json");
				}).change();	
			}
		});
	};

	fn.app.bank.statement.mapping = function(){
		$.post("apps/bank/xhr/action-mapping-statement.php",$("form[name=form_mappingstatement]").serialize(),function(response){
			if(response.success){
				$("#tblStatement").DataTable().draw();
				$("#dialog_mapping_statement").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

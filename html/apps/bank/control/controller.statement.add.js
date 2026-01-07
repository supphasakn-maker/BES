	fn.app.bank.statement.dialog_add = function() {
		$.ajax({
			url: "apps/bank/view/dialog.statement.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_statement"});
				$("input[name=bank_id]").val($("select[name=bank_id]").val());
			}
		});
	};

	fn.app.bank.statement.add = function(){
		$.post("apps/bank/xhr/action-add-statement.php",$("form[name=form_addstatement]").serialize(),function(response){
			if(response.success){
				$("#tblStatement").DataTable().draw();
				$("#dialog_add_statement").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "add_circle_outline",
		onclick : "fn.app.bank.statement.dialog_add()",
		caption : "Add"
	}));

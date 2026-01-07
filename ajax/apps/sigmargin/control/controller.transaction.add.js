	fn.app.sigmargin.transaction.dialog_add = function() {
		$.ajax({
			url: "apps/sigmargin/view/dialog.transaction.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_transaction"});
			}
		});
	};

	fn.app.sigmargin.transaction.add = function(){
		$.post("apps/sigmargin/xhr/action-add-transaction.php",$("form[name=form_addtransaction]").serialize(),function(response){
			if(response.success){
				$("#tblTransaction").DataTable().draw();
				$("#dialog_add_transaction").modal("hide");
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
		onclick : "fn.app.sigmargin.transaction.dialog_add()",
		caption : "Add"
	}));

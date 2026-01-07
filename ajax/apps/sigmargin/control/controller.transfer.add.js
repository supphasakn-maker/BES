	fn.app.sigmargin.transfer.dialog_add = function() {
		$.ajax({
			url: "apps/sigmargin/view/dialog.transfer.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_transfer"});
			}
		});
	};

	fn.app.sigmargin.transfer.add = function(){
		$.post("apps/sigmargin/xhr/action-add-transfer.php",$("form[name=form_addtransfer]").serialize(),function(response){
			if(response.success){
				$("#tblTransfer").DataTable().draw();
				$("#dialog_add_transfer").modal("hide");
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
		onclick : "fn.app.sigmargin.transfer.dialog_add()",
		caption : "Add"
	}));

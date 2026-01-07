	fn.app.supplier.supplier.dialog_add = function() {
		$.ajax({
			url: "apps/supplier/view/dialog.supplier.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_supplier"});
			}
		});
	};

	fn.app.supplier.supplier.add = function(){
		$.post("apps/supplier/xhr/action-add-supplier.php",$("form[name=form_addsupplier]").serialize(),function(response){
			if(response.success){
				$("#tblSupplier").DataTable().draw();
				$("#dialog_add_supplier").modal("hide");
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
		onclick : "fn.app.supplier.supplier.dialog_add()",
		caption : "Add"
	}));

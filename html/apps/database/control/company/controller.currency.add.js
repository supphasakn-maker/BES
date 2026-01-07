
	fn.app.database.company.currency.dialog_add = function() {
		$.ajax({
			url: "apps/database/view/company/dialog.currency.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_currency"});
				$("select[name=country]").select2({width:"100%"});
			}	
		});
	};

	fn.app.database.company.currency.add = function(){
		$.post('apps/database/xhr/company/action-add-currency.php',$('form[name=form_addcurrency]').serialize(),function(response){
			if(response.success){
				$("#tblDatabase").DataTable().draw();
				$("#dialog_add_currency").modal('hide');
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},'json');
		return false;
	};
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "add_circle_outline",
		onclick : "fn.app.database.company.currency.dialog_add()",
		caption : "Add"
	}));


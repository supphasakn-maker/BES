
	fn.app.database.company.payitem.dialog_add = function() {
		$.ajax({
			url: "apps/database/view/company/dialog.payitem.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_payitem"});
				$("select[name=country]").select2({width:"100%"});
			}	
		});
	};

	fn.app.database.company.payitem.add = function(){
		$.post('apps/database/xhr/company/action-add-payitem.php',$('form[name=form_addpayitem]').serialize(),function(response){
			if(response.success){
				$("#tblDatabase").DataTable().draw();
				$("#dialog_add_payitem").modal('hide');
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
		onclick : "fn.app.database.company.payitem.dialog_add()",
		caption : "Add"
	}));


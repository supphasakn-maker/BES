
	fn.app.database.country.dialog_add = function() {
		$.ajax({
			url: "apps/database/view/geography/dialog.country.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_country"});
			}	
		});
	};

	fn.app.database.country.add = function(){
		$.post('apps/database/xhr/geography/action-add-country.php',$('form[name=form_addcountry]').serialize(),function(response){
			if(response.success){
				$("#tblDatabase").DataTable().draw();
				$("#dialog_add_country").modal('hide');
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
		onclick : "fn.app.database.country.dialog_add()",
		caption : "Add"
	}));

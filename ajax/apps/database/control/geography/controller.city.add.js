
	fn.app.database.city.dialog_add = function() {
		$.ajax({
			url: "apps/database/view/geography/dialog.city.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_city"});
				$("select[name=country]").select2({width:"100%"});
			}	
		});
	};

	fn.app.database.city.add = function(){
		$.post('apps/database/xhr/geography/action-add-city.php',$('form[name=form_addcity]').serialize(),function(response){
			if(response.success){
				$("#tblDatabase").DataTable().draw();
				$("#dialog_add_city").modal('hide');
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
		onclick : "fn.app.database.city.dialog_add()",
		caption : "Add"
	}));


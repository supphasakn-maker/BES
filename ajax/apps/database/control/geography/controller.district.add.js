
	fn.app.database.district.dialog_add = function() {
		$.ajax({
			url: "apps/database/view/geography/dialog.district.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_district"});
				$("select[name=country]").select2({width:"100%"});
			}	
		});
	};

	fn.app.database.district.add = function(){
		$.post('apps/database/xhr/geography/action-add-district.php',$('#form_adddistrict').serialize(),function(response){
			if(response.success){
				$("#tblDatabase").DataTable().draw();
				$("#dialog_add_district").modal('hide');
				$("#form_adddistrict")[0].reset();
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
		onclick : "fn.app.database.district.dialog_add()",
		caption : "Add"
	}));


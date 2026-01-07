
	fn.app.database.subdistrict.dialog_add = function() {
		$.ajax({
			url: "apps/database/view/geography/dialog.subdistrict.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_subdistrict"});
				$("select[name=country]").select2({width:"100%"});
			}	
		});
	};

	fn.app.database.subdistrict.add = function(){
		$.post('apps/database/xhr/geography/action-add-subdistrict.php',$('#form_addsubdistrict').serialize(),function(response){
			if(response.success){
				$("#tblDatabase").DataTable().draw();
				$("#dialog_add_subdistrict").modal('hide');
				$("#form_addsubdistrict")[0].reset();
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
		onclick : "fn.app.database.subdistrict.dialog_add()",
		caption : "Add"
	}));


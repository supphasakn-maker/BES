	
	fn.app.reserve_silver.reserve.dialog_add = function() {
		$.ajax({
			url: "apps/reserve_silver/view/dialog.reserve.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_reserve"});
			}
		});
	};

	fn.app.reserve_silver.reserve.add = function(){
		$.post("apps/reserve_silver/xhr/action-add-reserve.php",$("form[name=form_addreserve]").serialize(),function(response){
			if(response.success){
				$("#tblReserve").DataTable().draw();
				$("#dialog_add_reserve").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	console.log(fn.app.reserve_silver.reserve.dialog_add);
	
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "add_circle_outline",
		onclick : "fn.app.reserve_silver.reserve.dialog_add()",
		caption : "Add"
	}));
	
	
	
	
	

	fn.app.sales.spot.dialog_add = function() {
		$.ajax({
			url: "apps/sales/view/dialog.spot.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_spot"});
			}
		});
	};

	fn.app.sales.spot.add = function(){
		$.post("apps/sales/xhr/action-add-spot.php",$("form[name=form_addspot]").serialize(),function(response){
			if(response.success){
				$("#tblSpot").DataTable().draw();
				$("#dialog_add_spot").modal("hide");
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
		onclick : "fn.app.sales.spot.dialog_add()",
		caption : "Add"
	}));

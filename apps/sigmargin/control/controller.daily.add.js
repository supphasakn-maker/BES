	fn.app.sigmargin.daily.dialog_add = function() {
		$.ajax({
			url: "apps/sigmargin/view/dialog.daily.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_daily"});
			}
		});
	};

	fn.app.sigmargin.daily.add = function(){
		$.post("apps/sigmargin/xhr/action-add-daily.php",$("form[name=form_adddaily]").serialize(),function(response){
			if(response.success){
				$("#tblDaily").DataTable().draw();
				$("#dialog_add_daily").modal("hide");
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
		onclick : "fn.app.sigmargin.daily.dialog_add()",
		caption : "Add"
	}));

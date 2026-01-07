	fn.app.audit.match.dialog_add = function() {
		$.ajax({
			url: "apps/audit/view/dialog.match.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_match"});
			}
		});
	};

	fn.app.audit.match.add = function(){
		$.post("apps/audit/xhr/action-add-match.php",$("form[name=form_addmatch]").serialize(),function(response){
			if(response.success){
				$("#tblMatch").DataTable().draw();
				$("#dialog_add_match").modal("hide");
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
		onclick : "fn.app.audit.match.dialog_add()",
		caption : "Add"
	}));

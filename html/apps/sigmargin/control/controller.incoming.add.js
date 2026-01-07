	fn.app.sigmargin.incoming.dialog_add = function() {
		$.ajax({
			url: "apps/sigmargin/view/dialog.incoming.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_incoming"});
			}
		});
	};

	fn.app.sigmargin.incoming.add = function(){
		$.post("apps/sigmargin/xhr/action-add-incoming.php",$("form[name=form_addincoming]").serialize(),function(response){
			if(response.success){
				$("#tblIncoming").DataTable().draw();
				$("#dialog_add_incoming").modal("hide");
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
		onclick : "fn.app.sigmargin.incoming.dialog_add()",
		caption : "Add"
	}));

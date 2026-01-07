
	fn.app.notify.message.dialog_add = function() {
		$.ajax({
			url: "apps/notify/view/dialog.message.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_message"});
				
			}	
		});
	};

	fn.app.notify.message.add = function(){
		$.post('apps/notify/xhr/action-add-message.php',$('form[name=form_addmessage]').serialize(),function(response){
			if(response.success){
				$("#tblMessage").DataTable().draw();
				$("#dialog_add_message").modal('hide');
				$("#form_addmessage")[0].reset();
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
		onclick : "fn.app.notify.message.dialog_add()",
		caption : "Add"
	}));

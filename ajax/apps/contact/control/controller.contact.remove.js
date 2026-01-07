	
	fn.app.contact.contact.dialog_remove = function() {
		var item_selected = $("#tblContact").data("selected");
		$.ajax({
			url: "apps/contact/view/dialog.contact.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remove_contact"});
			}	
		});
	};
	
	fn.app.contact.contact.remove = function(){
		var item_selected = $("#tblContact").data("selected");
		$.post('apps/contact/xhr/action-remove-contact.php',{items:item_selected},function(response){
			$("#tblContact").data("selected",[]);
			$("#tblContact").DataTable().ajax.reload(null,false);
			$('#dialog_remove_contact').modal('hide');
		});
		
	};
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.contact.contact.dialog_remove()",
		caption : "Remove"
	}));
	
	
	
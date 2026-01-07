
	fn.app.contact.organization.dialog_remove = function() {
		var item_selected = $("#tblOrganization").data("selected");
		$.ajax({
			url: "apps/contact/view/dialog.organization.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remove_organization"});
			}	
		});
	};
	
	fn.app.contact.organization.remove = function(){
		var item_selected = $("#tblOrganization").data("selected");
		$.post('apps/contact/xhr/action-remove-organization.php',{items:item_selected},function(response){
			$("#tblOrganization").data("selected",[]);
			$("#tblOrganization").DataTable().ajax.reload(null,false);
			$('#dialog_remove_organization').modal('hide');
		});
		
	};
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.contact.organization.dialog_remove()",
		caption : "Remove"
	}));
	
	
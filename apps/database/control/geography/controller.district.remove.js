
	fn.app.database.district.dialog_remove = function() {
		var item_selected = $("#tblDatabase").data("selected");
		$.ajax({
			url: "apps/database/view/geography/dialog.district.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remove_district"});
			}	
		});
	};
	
	fn.app.database.district.remove = function(){
		var item_selected = $("#tblDatabase").data("selected");
		$.post('apps/database/xhr/geography/action-remove-district.php',{items:item_selected},function(response){
			$("#tblDatabase").data("selected",[]);
			$("#tblDatabase").DataTable().draw();
			$('#dialog_remove_district').modal('hide');
		});
		
	};
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.database.district.dialog_remove()",
		caption : "Remove"
	}));
	
	
	
	
	
	fn.app.bank.statement.dialog_import = function(table) {
		$.ajax({
			url: "apps/bank/view/dialog.statement.import.php",
			data: {table:table},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_import_statement"});
			}
		});
	};

	fn.app.bank.statement.import = function(){
		$.post("apps/bank/xhr/action-import-statement.php",$("form[name=form_importstatement]").serialize(),function(response){
			if(response.success){
				$("#tblstatement").DataTable().draw();
				$("#dialog_import_statement").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	$("form[name=import] input[type=file]").change(function(){
		var data = new FormData($("form[name=import]")[0]);
		jQuery.ajax({
			url: 'apps/bank/xhr/action-upload-statement.php',
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			type: 'POST',
			dataType: 'json',
			success: function(response){
				if(response.success){
					fn.app.bank.statement.dialog_import(response.table);
				}else{
					fn.notify.warnbox(response.msg,"Oops...");
				}
			}
		});
		
	});
	
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "file_upload",
		onclick : "$('form[name=import] input[type=file]').click()",
		caption : "Upload"
	}));
	
	/*
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "file_download",
		onclick : "window.location = 'apps/bank/download/template_bank.csv'",
		caption : "Download Template"
	}));
	*/
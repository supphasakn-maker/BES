	fn.app.import.import.dialog_edit = function(id) {
		$.ajax({
			url: "apps/import/view/dialog.import.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_import"});
				$("form[name=uploader] input[type=file]").unbind().change(function(){
					var data = new FormData($("form[name=uploader]")[0]);
					jQuery.ajax({
						url: 'apps/import/xhr/action-upload-file.php',
						data: data,
						cache: false,
						contentType: false,
						processData: false,
						type: 'POST',
						dataType: 'json',
						success: function(response){
							
							var s ='';
							for(i in response.path){
								s += '<li class="list-group-item"><button class="btn btn-danger" onclick="$(this).parent().remove()">X</button><input type="hidden" name="path[]" value="'+response.path[i]+'"><span>'+response.path[i]+'</span></li>';
							} 
							$("#file_zone ul").append(s);
							/*
							if(response.success){
								for(i in response.action){
									switch(response.action[i][0]){
										case "rephoto":
											$(response.action[i][1]).attr('src',response.path)
											break;
										case "retable":
											$(response.action[i][1]).DataTable().ajax.reload(null,false);
											break;
										case "reload":
											window.location.reload();
											break;
									}
								}
								//$("#dialog_file_upload").modal('hide');
							}else{
								fn.notify.warnbox(response.msg,"Oops...");
							}	
							*/
						}
					});
				});
				
			}
		});
	};

	fn.app.import.import.edit = function(){
		$.post("apps/import/xhr/action-edit-import.php",$("form[name=form_editimport]").serialize(),function(response){
			if(response.success){
				$("#tblImport").DataTable().draw();
				$("#dialog_edit_import").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

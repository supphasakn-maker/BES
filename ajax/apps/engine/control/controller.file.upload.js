	
	fn.app.engine.file.upload = function(){
		$("form[name=form_uploader] input[name=file]").click();
	}
	
	fn.app.engine.file.clear = function(){
		var id = $("form[name=form_uploader] input[name=id]").val();
		var type = $("form[name=form_uploader] input[name=type]").val();
		$.post('apps/engine/xhr/action-clear.php',{type,id},function(response){
			if(response.success){
				window.location.reload();
			}else{
				fn.notify.warnbox(response.msg);
			}
			
		},"json");
	}
	
	fn.app.engine.file.dialog_file = function(type,id) {
		$.ajax({
			url: "apps/engine/view/dialog.file.upload.php",
			data: {type,id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_file_upload"});
				$("form[name=form_uploader] input[name=file]").change(function(){
					var data = new FormData($("form[name=form_uploader]")[0]);
					jQuery.ajax({
						url: 'apps/engine/xhr/action-upload-file.php',
						data: data,
						cache: false,
						contentType: false,
						processData: false,
						type: 'POST',
						dataType: 'json',
						success: function(response){
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
						}
					});
				});			
			}	
		});
		
		/*
		
		$.ajax({
			url: "apps/accctrl/view/dialog.user.avatar.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_user_avatar").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_user_avatar").modal('show');
				
				$("#dialog_user_avatar .form_upload .fileinput").change(function(){
					var data = new FormData($("#dialog_user_avatar form.form_upload")[0]);
					jQuery.ajax({
						url: 'apps/accctrl/xhr/action-upload-user-avatar.php',
						data: data,
						cache: false,
						contentType: false,
						processData: false,
						type: 'POST',
						dataType: 'json',
						success: function(response){
							if(response.success){
								$("#tblUser").DataTable().ajax.reload(null,false);
								$("#dialog_user_avatar").modal('hide');
							}else{
								fn.engine.alert("Alert",response.msg);
							}	
						}
					});
				});
				$("#dialog_user_avatar").find(".btn_change").click(function(){
					$("#dialog_user_avatar .form_upload .fileinput").click();
				});
				
				$("#dialog_user_avatar").find(".btn_remove").click(function(){
					swal({
						title: 'Are you sure to clear image?',
						text: 'Your will not be able to recover this imaginary file!',
						type: 'warning',
						showCancelButton: true,
						cancelButtonClass: 'btn-raised btn-default',
						cancelButtonText: 'Cancel!',
						confirmButtonClass: 'btn-raised btn-danger',
						confirmButtonText: 'Yes, delete it!',
						closeOnConfirm: false
					}).then(function() {
						$.post('apps/accctrl/xhr/action-clear-user-avatar.php',{id:id},function(response){
							swal({
								title: 'Deleted!',
								text: 'Your imaginary file has been deleted.',
								type: 'success',
								confirmButtonClass: 'btn-raised btn-success',
								confirmButtonText: 'OK'
							}).then(function(){
								$("#tblUser").DataTable().ajax.reload(null,false);
								$("#dialog_user_avatar").modal('hide');
							});
						},'json');
					});
				});
			}	
		});
		
		*/
	};

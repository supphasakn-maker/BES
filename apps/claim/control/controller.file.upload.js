	
	fn.app.claim.product.clear = function(){
		var id = $("form[name=form_uploader] input[name=id]").val();
		var path = $(id).find("input[xname=img_path]").val();
		$.post('apps/claim/xhr/action-clear.php',{path:path},function(response){
			if(response.success){
				$(id).remove();
				$("#dialog_file_upload").modal("hide");
			}else{
				fn.notify.warnbox(response.msg);
			}
			
		},"json");
	}
	
	fn.app.claim.product.save = function(){
		var id = $("form[name=form_uploader] input[name=id]").val();
		var title = $("form[name=form_uploader] textarea[name=title]").val();
		$(id).attr("title",title);
		$(id).find("input[xname=img_desc]").val(title);
		$("#dialog_file_upload").modal("hide");
	}
	
	fn.app.claim.product.dialog_file = function(idx) {
		$.ajax({
			url: "apps/claim/view/dialog.file.upload.php",
			data: {
				title : $(idx).attr("title"),
				id : idx
			},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_file_upload"});
			}	
		});
		
	};

	$("form[name=form_uploader] input[type=file]").change(function(){
		var data = new FormData($("form[name=form_uploader]")[0]);
		jQuery.ajax({
			url: 'apps/claim/xhr/action-upload-file.php',
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			type: 'POST',
			dataType: 'json',
			success: function(response){
				if(response.success){
					
					var s = ""; 
					for(i in response.uploaded){
						let id = guidGenerator();
						let path = response.uploaded[i];
						s += '<a data-toggle="tooltip" data-placement="top" id="'+id+'" title="" onclick="fn.app.claim.product.dialog_file(\'#'+id+'\')" href="javascript:;" class="m-2">';
							s += '<input type="hidden" xname="img_path" name="img_path[]" value="'+path+'">';
							s += '<input type="hidden" xname="img_desc" name="img_desc[]" value="">';
							s += '<img style="height:50%;" class="img-thumbnail" src="'+path+'">';
						s += '</a>';
					}
					s += '';
					$("#img_frame").append(s);
				}else{
					fn.notify.warnbox(response.msg,"Oops...");
				}	
			}
		});
	});
	
	function guidGenerator() {
		var S4 = function() {
		   return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
		};
		return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4());
	}

	fn.app.customer.customer.dialog_document =function(id){
		$.ajax({
			url: "apps/customer/view/dialog.customer.document.php",
			type: "POST",
			data: {id:id},
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_document_customer"});
				$("form[name=uploader] input[type=file]").change(function(){
					var data = new FormData($("form[name=uploader]")[0]);
					jQuery.ajax({
						url: 'apps/customer/xhr/action-upload-document.php',
						data: data,
						cache: false,
						contentType: false,
						processData: false,
						type: 'POST',
						dataType: 'json',
						success: function(response){
							var imgs = response.imgs;
							var s = '';
							for(i in imgs){
								s += '<tr data-file="' + imgs[i] + '">';
									s += '<td><a href="' + imgs[i] + '">' + imgs[i] + '</a></td>';
									s += '<td><a onclick="fn.app.customer.customer.document_remove(' + id + ',' + i + ')" class="btn btn-sm btn-danger">Remove</td>';
								s += '</tr>';
							}
							$("#tblDoucment tbody").html(s);
						}
					});
					
				});
				
				$("#tblDoucment tbody").sortable();
				
			}
		});
	}
	
	fn.app.customer.customer.document_upload =function(){
		$("form[name=uploader] input[type=file]").click();
	}
	
	fn.app.customer.customer.document_remove =function(id,pos){
		$.post("apps/customer/xhr/action-remove-document.php",{id:id,pos:pos},function(response){
			var imgs = response.imgs;
			var s = '';
			for(i in imgs){
				s += '<tr data-file="' + imgs[i] + '">';
					s += '<td><a href="' + imgs[i] + '">' + imgs[i] + '</a></td>';
					s += '<td><a onclick="fn.app.customer.customer.document_remove(' + id + ',' + i + ')" class="btn btn-sm btn-danger">Remove</td>';
				s += '</tr>';
			}
			$("#tblDoucment tbody").html(s);
		},"json");
		
	}
	
	fn.app.customer.customer.document_sort =function(id){
		var files = [];
		$("#tblDoucment tbody tr").each(function(){
			files.push($(this).attr("data-file"));
		});
		
		$.post("apps/customer/xhr/action-sort-document.php",{id:id,files:files},function(){
			$("#dialog_document_customer").modal('hide');
		},"json");
	}
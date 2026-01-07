	fn.app.packing.repack.dialog_split = function(id) {
		$.ajax({
			url: "apps/packing/view/dialog.repack.split.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_split_repack"});
				
				$("input[name=spliter]").change(function(){
					var spliter = $(this).val();
					
					var s = '';
					for(i=1;i<=spliter;i++){
						s += '<tr>';
							s += '<td class="text-center">'+i+'</td>';
							s += '<td><input type="text" class="form-control text-center" name="item_code[]"></td>';
							s += '<td><input type="text" class="form-control text-center" name="item_weight[]"></td>';
							
						s += '</tr>';
					}
					$("#tblSpliter tbody").html(s);
				}).change();
				
			}
		});
	};

	fn.app.packing.repack.split = function(){
		$.post("apps/packing/xhr/action-split-repack.php",$("form[name=form_splitrepack]").serialize(),function(response){
			if(response.success){
				$("#tblRepack").DataTable().draw();
				$("#dialog_split_repack").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

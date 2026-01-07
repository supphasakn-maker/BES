	fn.app.packing.packing.dialog_add = function() {
		$.ajax({
			url: "apps/packing/view/dialog.packing.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_packing"});
				
				
				$("input[name=total_item]").change(function(){
					var weight_peritem = parseFloat($("input[name=weight_peritem]").val());
					var total_item = parseInt($("input[name=total_item]").val());
					var total = weight_peritem*total_item;
					$("input[name=total_weight]").val(total);
					
					
					var iterator = parseInt($("form[name=form_addpacking]").attr("data-iterator"));
					var s = '';
					for(i=1;i<=total_item;i++){
						iterator++;
						var code = iterator.toString().padStart(5, '0')
						s += '<tr>';
							s += '<td class="text-center">'+i+'</td>';
							s += '<td><input type="text" class="form-control text-center" name="item_code[]" value="'+code+'"></td>';
							s += '<td><input type="text" readonly class="form-control text-center" name="item_weight[]"value="'+weight_peritem+'"></td>';
							s += '<td><input type="text" class="form-control text-center" name="item_actual[]"></td>';
						s += '</tr>';
					}
					$("#tblPackitem tbody").html(s);
					
				});
				
				$("input[name=weight_peritem]").change(function(){
					var weight_peritem = parseFloat($("input[name=weight_peritem]").val());
					var total_item = parseInt($("input[name=total_item]").val());
					var total = weight_peritem*total_item;
					$("input[name=total_weight]").val(total);
				});
				
				
			}
		});
	};

	fn.app.packing.packing.add = function(){
		$.post("apps/packing/xhr/action-add-packing.php",$("form[name=form_addpacking]").serialize(),function(response){
			if(response.success){
				$("#tblPacking").DataTable().draw();
				$("#dialog_add_packing").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "add_circle_outline",
		onclick : "fn.app.packing.packing.dialog_add()",
		caption : "Add"
	}));

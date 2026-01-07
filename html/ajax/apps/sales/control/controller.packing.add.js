	fn.app.sales.packing.dialog_add = function() {
		$.ajax({
			url: "apps/sales/view/dialog.packing.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_packing"});
				
				$("input[name=delivery_date_from],input[name=delivery_date_to]").unbind().change(function(){
					var date_from = $("input[name=delivery_date_from]").val();
					var date_to = $("input[name=delivery_date_to]").val();
					var date = $(this).val();
					$.post("apps/sales/xhr/action-load-packing.php",{
						date_from:date_from,
						date_to:date_to
					},function(json){
						var s = '';
						for(i in json){
							let total = parseFloat(json[i].amount)*parseFloat(json[i].size);
							s += '<tr>';
								s += '<td class="text-left">'+json[i].name+'</td>';
								s += '<td class="text-right">'+json[i].size+'</td>';
								s += '<td class="text-right">'+json[i].amount+'</td>';
								s += '<td class="text-right">'+total.toFixed(4)+'</td>';
								s += '<td class="text-left">'+json[i].comment+'</td>';
							s += '</tr>';
						}
						
						$("#tblPackAdding tbody").html(s);
						fn.app.sales.packing.calculation();
					},"json");
					
				}).change();
				
				
			}
		});
	};
	
	fn.app.sales.packing.calculation = function(){
		var total = 0;
		$("#tblPackNote tbody tr").each(function(){
			var amount = parseInt($(this).find("input[xname=amount]").val());
			var size = parseInt($(this).find("input[xname=size]").val());
			var eachtotal = size*amount;
			$(this).find("input[xname=totaleach]").html(eachtotal);
			total += size*amount;
			console.log(eachtotal);
		});
		$("input[name=total]").val(total);
	};

	fn.app.sales.packing.add = function(){
		$.post("apps/sales/xhr/action-add-packing.php",$("form[name=form_addpacking]").serialize(),function(response){
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
		onclick : "fn.app.sales.packing.dialog_add()",
		caption : "Add"
	}));
	
	fn.app.sales.packing.packing_append = function(item){
		var fn_calculate = "fn.app.sales.packing.calculation()";
		
		if(typeof item != "undefined"){
			var s ='';
			var classname = '';
			var val ='';
			s += '<tr class="pack_item">';
				classname = 'pt-2 text-center item_level';
				s += '<td xname="number" class="'+classname+'">#</td>';
				classname = 'form-control form-control-sm';
				s += '<td><input xname="name" name="name[]" type="text" class="'+classname+'" value="'+item.name+'"></td>';
				classname = 'form-control form-control-sm text-center';
				s += '<td><input xname="size" name="size[]" type="number" class="'+classname+'" onchange="'+ fn_calculate+'" value="'+item.size+'"></td>';
				classname = 'form-control form-control-sm text-center';
				s += '<td><input xname="amount" name="amount[]" type="number" class="'+classname+'" onchange="'+fn_calculate+'" value="'+item.amount+'"></td>';
				classname = 'form-control-sm form-control text-center';
				s += '<td><input xname="totaleach" name="totaleach[]" type="text" class="'+classname+'" readonly value="0"></td>';
				classname = 'form-control-sm form-control text-center';
				s += '<td><input xname="comment" name="comment[]" type="text" class="'+classname+'" placeholder="ข้อความพิเศษ" value="'+item.comment+'"></td>';
				classname = 'btn btn-sm btn-danger';
				s += '<td class="text-center"><button class="'+classname+'" onclick="$(this).parent().parent().remove();">ลบ</button></td>';
			s += '</tr>';
			$("#tblPackNote tbody").append(s);
		}else{
			var caption_pack = $("select[name=packtype]").val();
			var value_pack = $("select[name=packtype]").find(":selected").attr("data-value");
			var readonly_pack = $("select[name=packtype]").find(":selected").attr("data-readonly");
			var fn_calculate = "fn.app.sales.packing.calculation()";
		
			var s ='';
			var classname = '';
			var val ='';
			s += '<tr class="pack_item">';
				classname = 'pt-2 text-center item_level';
				s += '<td xname="number" class="'+classname+'">#</td>';
				classname = 'form-control form-control-sm';
				var readonly = readonly_pack=="false"?"":" readonly";
				s += '<td><input xname="name" name="name[]" type="text" class="'+classname+'"'+readonly+' value="' + caption_pack + '"></td>';
				classname = 'form-control form-control-sm text-center';
				s += '<td><input xname="size" name="size[]" type="number" class="'+classname+'" onchange="'+ fn_calculate+'" value="'+value_pack+'"></td>';
				classname = 'form-control form-control-sm text-center';
				s += '<td><input xname="amount" name="amount[]" type="number" class="'+classname+'" onchange="'+fn_calculate+'" value="1"></td>';
				classname = 'form-control-sm form-control text-center';
				s += '<td><input xname="totaleach" name="totaleach[]" type="text" class="'+classname+'" readonly value="0"></td>';
				classname = 'form-control-sm form-control text-center';
				s += '<td><input xname="comment" name="comment[]" type="text" class="'+classname+'" placeholder="ข้อความพิเศษ"></td>';
				classname = 'btn btn-sm btn-danger';
				s += '<td class="text-center"><button class="'+classname+'" onclick="$(this).parent().parent().remove();">ลบ</button></td>';
			s += '</tr>';
			$("#tblPackNote tbody").append(s);
		}
		
		fn.app.sales.packing.calculation();
	};

	fn.app.sales.order.dialog_split = function(id) {
		$.ajax({
			url: "apps/sales/view/dialog.order.split.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_split_order"});
			}
		});
	};
	
	fn.app.sales.order.append_split = function(){
		var s = '';
			s += '<tr>';
			s += '<td><label>ใบแยก</label></td>';
			s += '<td><input data-name="amount" name="amount[]" type="text" class="form-control text-right" value="0"></td>';
			s += '<td><label>วันส่ง</label></td>';
			s += '<td><input data-name="date" name="date[]" type="date" class="form-control text-right" value=""></td>';
			s += '<td class="p-0"><a onclick="$(this).parent().parent().remove()" class="btn btn-danger" >X</a></td>';
			s += '</tr>';					
		$("form[name=form_splitorder] table tbody").append(s);
	}

	fn.app.sales.order.split = function(){
		var total = 0;
		var is_empthy = false;
		$("input[data-name=amount]").each(function(){
			total += parseFloat($(this).val());
			if($(this).val()==0)is_empthy=true
			
		});
		
		console.log(total);
		
		if($("input[name=total]").val() != total){
			fn.notify.warnbox("จำนวนรวมกันไม่ถูกต้อง","Oops...");
		}else if(is_empthy){
			fn.notify.warnbox("โปรดระบุจำนวนทุกใบ","Oops...");
			
			
		}else{
			$.post("apps/sales/xhr/action-split-order.php",$("form[name=form_splitorder]").serialize(),function(response){
				if(response.success){
					$("#tblOrder").DataTable().draw();
					$("#dialog_split_order").modal("hide");
				}else{
					fn.notify.warnbox(response.msg,"Oops...");
				}
			},"json");
		}
		
		
		
		return false;
	};

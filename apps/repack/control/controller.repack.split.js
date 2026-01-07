	fn.app.repack.repack.dialog_split = function(id) {
		$.ajax({
			url: "apps/repack/view/dialog.repack.split.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_split_repack"});
			}
		});
	};
	
	fn.app.repack.repack.append = function() {
		var caption_pack = $("form[name=form_splitrepack] select[name=pack_name]").val();
		var value_pack = $("form[name=form_splitrepack] select[name=pack_name]").find(":selected").attr("data-value");
		var readonly_pack = $("form[name=form_splitrepack] select[name=pack_name]").find(":selected").attr("data-readonly");
		
		$("form[name=form_splitrepack] input[name=weight_expected]").val(value_pack);
		if(readonly_pack=="false"){
			var readonly = "";
		}else{
			var readonly = " readonly";
			
		}
		
		
		let s ='';
		s += '<tr>';
			s += '<td><input name="code[]" class="form-control"></td>';
			
			s += '<td><input name="pack_name_split[]" class="form-control" value="'+caption_pack+'"'+readonly+'></td>';
			s += '</td>';
			s += '<td>';
				s += '<select name="pack_type[]" class="form-control">';
					s += '<option>ถุงปกติ</option>';
					s += '<option>ถุงกระสอบ</option>';
				s += '</select>';
			s += '</td>';
			s += '<td><input name="weight_expected[]" class="form-control" value="'+value_pack+'"'+readonly+'></td>';
			s += '<td><input name="weight_actual[]" onchange="fn.app.repack.repack.calculate()" xname="weight_actual" class="form-control"></td>';
			s += '<td><button class="btn btn-xs btn-danger" onclick="$(this).parent().parent().remove();">X</button></td>';
		s += '</tr>';
		
		$("#tblSpliter tbody").append(s);
	
	};
	
	fn.app.repack.repack.calculate = function(){
		var total = 0;
		$("#tblSpliter tbody tr").each(function(){
			total += parseFloat($(this).find("[xname=weight_actual]").val());
		});
		
		$("[name=split_new]").val(total);
	}

	fn.app.repack.repack.split = function(){
		$.post("apps/repack/xhr/action-split-repack.php",$("form[name=form_splitrepack]").serialize(),function(response){
			if(response.success){
				$("#tblRepack").DataTable().draw();
				$("#dialog_split_repack").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	

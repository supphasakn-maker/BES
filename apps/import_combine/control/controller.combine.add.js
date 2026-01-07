

fn.app.import_combine.combine.add = function(){
	$("input[name=select_import]").val($("#tblImport").data("selected").join(","));
	
	$.post('apps/import_combine/xhr/action-add-combine.php',$('form[name=form_addcombine]').serialize(),function(response){
		if(response.success){
			$("#tblCombine").DataTable().draw();
			$("#tblImport").DataTable().draw();
			$("#tblImport").data("selected",[]);
			$('form[name=form_addcombine]')[0].reset();
			fn.notify.successbox(response.msg,"บันทึกแล้ว");
		}else{
			fn.notify.warnbox(response.msg,"Oops...");
		}
	},'json');
}

fn.app.import_combine.combine.calculate = function(){
	var total_reserve = 0;
	$("input[data-name=reserve]:checked").each(function(){
		total_reserve += parseFloat($(this).attr("data-value"));
	});
	$("input[name=amount]").val(total_reserve);
};



$("select[name=supplier_id]").change(function(){
	$.post("apps/import_combine/xhr/action-list-reserve.php",{supplier_id:$(this).val()},function(list){
		var s = '';
		for(i in list){
			s += '<div class="custom-control custom-checkbox" onclick="fn.app.import.import.calculate()">';
				s += '<input name="reserve[]" data-name="reserve" data-value="'+list[i].weight_actual+'" value="'+list[i].id+'" type="checkbox" class="custom-control-input" id="x'+list[i].id+'">';
				s += '<label class="custom-control-label" for="x'+list[i].id+'">'+list[i].weight_actual+'</label>';
			s += '</div>'; 
		}
		$("#select_reserve").html(s);
	},"json");
});

$("#tblSpot").on("selecting","tr",function(){
	var total = 0;
	$("#tblSpot tr.selected").each(function(){
		var selected_value = $(this).find("input[data-name=spot]").val();
		total += parseFloat(selected_value);
	});
	$("input[name=total_selected]").val(total.toFixed(4));
	
});


fn.app.import.import.add = function(){
	var amount=parseFloat($("input[name=amount]").val());
	var total=parseFloat($("input[name=total_selected]").val());
	
	if(amount != total){
		fn.notify.warnbox("จำนวนรวมไม่ตรงกัน","Oops...");
	}else{
		var select_spot=[],select_amount=[];
		$("#tblSpot tr.selected").each(function(){
			var val = $(this).find("input[data-name=spot]").val();
			var spot = $(this).attr("id");
			select_spot.push(spot);
			select_amount.push(val);
		});
		
		$("input[name=select_spot]").val(select_spot.join(","));
		$("input[name=select_amount]").val(select_amount.join(","));
		
	
		$.post('apps/import/xhr/action-add-import.php',$('form[name=form_addimport]').serialize(),function(response){
				if(response.success){
					$("#tblSpot").DataTable().draw();
					$("#tblImport").DataTable().draw();
					$('form[name=form_addimport]')[0].reset();
					
					fn.notify.successbox(response.msg,"บันทึกแล้ว");
				}else{
					fn.notify.warnbox(response.msg,"Oops...");
				}
			},'json');
	}
		return false;
}

fn.app.import.import.calculate = function(){
	var total_reserve = 0;
	$("input[data-name=reserve]:checked").each(function(){
		total_reserve += parseFloat($(this).attr("data-value"));
	});
	$("input[name=amount]").val(total_reserve);
};



$("select[name=supplier_id]").change(function(){
	$.post("apps/import/xhr/action-list-reserve.php",{supplier_id:$(this).val()},function(list){
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
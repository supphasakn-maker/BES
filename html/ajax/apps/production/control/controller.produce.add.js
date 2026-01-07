
	fn.app.production.produce.add = function(){
		$.post("apps/production/xhr/action-add-produce.php",$("form[name=form_addproduce]").serialize(),function(response){
			if(response.success){
				$("#tblProduce").DataTable().draw();
				$("#dialog_add_produce").modal("hide");
				window.location = "#apps/production/index.php";
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
		onclick : "window.location='#apps/production/index.php?view=add'",
		caption : "Add"
	}));
	
	$("#form-second-process input").on("change",function(){
		var aIn = ['weight_in_safe','weight_in_plate','weight_in_nugget','weight_in_blacknugget','weight_in_whitedust','weight_in_blackdust','weight_in_refine','weight_in_1','weight_in_2','weight_in_3','weight_in_4'];
		var aOut = ['weight_out_safe','weight_out_plate','weight_out_nugget','weight_out_blacknugget','weight_out_whitedust','weight_out_blackdust','weight_out_refine','weight_out_packing'];
		var total_in=0;
		var total_out=0;
		
		for(i in aIn){total_in += parseFloat($("input[name="+aIn[i]+"]").val());}
		for(i in aOut){total_out += parseFloat($("input[name="+aOut[i]+"]").val());}
		
		$("input[name=weight_in_total]").val(total_in.toFixed(4));
		$("input[name=weight_out_total]").val(total_out.toFixed(4));
		$("input[name=weight_margin]").val((total_out-total_in).toFixed(4));

	});

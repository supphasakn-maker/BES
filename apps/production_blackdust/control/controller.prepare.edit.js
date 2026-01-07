	
	fn.app.production_blackdust.prepare.append_import= function() {
		let s = '';
		
		let onChange = 'fn.app.production_blackdust.prepare.calcuate_import()';
		s += '<tr>';
			s += '<td><input type="time" name="import_time[]" class="form-control text-center"></td>';
			s += '<td><input type="text" name="import_number[]" class="form-control text-center"></td>';
			s += '<td><input type="text" onchange="'+onChange+'" xname="import_bar" name="import_bar[]" class="form-control text-center"></td>';
			s += '<td><input type="text" onchange="'+onChange+'" xname="import_weight_in" name="import_weight_in[]" class="form-control text-center"></td>';
			s += '<td><input type="text" onchange="'+onChange+'" xname="import_weight_actual" name="import_weight_actual[]" class="form-control text-center"></td>';
			s += '<td><input type="text" xname="import_weight_margin" name="import_weight_margin[]" readonly class="form-control text-center"></td>';
			s += '<td><input type="text" xname="import_weight_bar" name="import_weight_bar[]" readonly class="form-control text-center"></td>';
			s += '<td><span class="btn btn-xs btn-danger" onclick="$(this).parent().parent().remove()">Remove</span></td>';
		s += '</tr>';
		$("#tblImport tbody").append(s);
	};
	
	fn.app.production_blackdust.prepare.calcuate_import = function() {
		$("#tblImport tbody tr").each(function(){
			let import_bar = $(this).find("[xname=import_bar]").val();
			let import_weight_in = $(this).find("[xname=import_weight_in]").val();
			let import_weight_actual = $(this).find("[xname=import_weight_actual]").val();
			let import_weight_margin = parseFloat(import_weight_actual)-parseFloat(import_weight_in);
			let import_weight_bar = parseFloat(import_weight_in)/parseFloat(import_bar);
			$(this).find("[xname=import_weight_margin]").val(import_weight_margin.toFixed(4));
			$(this).find("[xname=import_weight_bar]").val(import_weight_bar.toFixed(4));
		});
		
		
	};
	



	fn.app.production_blackdust.prepare.dialog_add_pack = function(id) {
		$.ajax({
			url: "apps/production_blackdust/view/prepare/dialog.pack.add.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_pack"});
				
				$("form[name=form_addpack] select[name=pack_name]").unbind().change(function(){
					var caption_pack = $("form[name=form_addpack] select[name=pack_name]").val();
					var value_pack = $("form[name=form_addpack] select[name=pack_name]").find(":selected").attr("data-value");
					var readonly_pack = $("form[name=form_addpack] select[name=pack_name]").find(":selected").attr("data-readonly");
					
					$("form[name=form_addpack] input[name=weight_expected]").val(value_pack);
					if(readonly_pack=="false"){
						$("form[name=form_addpack] input[name=weight_expected]").attr("readonly",false);
					}else{
						$("form[name=form_addpack] input[name=weight_expected]").attr("readonly",true);
						
					}
					
				}).change();
			}
		});
	};
	
	fn.app.production_blackdust.prepare.add_pack = function(id) {
		$.post("apps/production_blackdust/xhr/action-add-pack.php",$("form[name=form_addpack]").serialize(),function(response){
			if(response.success){
				$("#tblPacking").DataTable().draw();
				$("#dialog_add_pack").modal("hide");
				var s = '';
				
				s += '<div>จำนวนทีเพิ่ม ' + response.created.length + ' รายการ</div>';
				s += '<div>จำนวนที่ซ้ำ ' + response.redundant.length + ' รายการ</div>';
				
				
				fn.notify.warnbox(s,"Result");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	fn.app.production_blackdust.prepare.calculation = function(){
		$.post("apps/production_blackdust/xhr/action-load-calcuate.php",{
			id:$("[name=form_editprepare] [name=id]").val()
		},function(response){
			let data = response.data;
			var s = '';
			s += '<table class="table table-sm table-striped table-bordered dt-responsive nowrap">';
				s += '<thead>';
					s += '<tr>';
						s += '<th class="text-center">รายการ</th>';
						s += '<th class="text-center">จำนวนผลิต</th>';
						s += '<th class="text-center">น้ำหนักรวม</th>';
						s += '<th class="text-center">หมายุเหตุ</th>';
					s += '</tr>';
				
				s += '</thead>	';
				s += '<tbody>';
				for(i in data){
					s += '<tr>';
						s += '<td class="text-center">'+ data[i].name +'</td>';
						s += '<td class="text-center">'+ parseInt(data[i].total) +'</td>';
						s += '<td class="text-center">'+ parseFloat(data[i].weight).toFixed(4) +'</td>';
						s += '<td class="text-center text-wrap">';
						let codes = data[i].remark.split(",");
						for(j in codes){
							s += '<span class="badge badge-dark mr-1">'+ codes[j] +'</span>';
						}
						s += '</td>';
					s += '</tr>';
					
				}
				s += '</tbody>';
			s += '</table>';
			$("#Output").html(s);
			$("#Output2").html(s);
			
		},"json");
	};
	
	fn.app.production_blackdust.prepare.append_pack = function() {
		
		var start = $("form[name=form_addpack] input[name=start]").val();
		var end = $("form[name=form_addpack] input[name=end]").val();
		var type = $("form[name=form_addpack] select[name=pack_type]").val();
		var name = $("form[name=form_addpack] select[name=pack_name]").val();
		var weight = $("form[name=form_addpack] input[name=weight_expected]").val();
		var s = '';
		
		for(var pack_num = parseInt(start); pack_num <= (parseInt(start)+parseInt(end)-1); pack_num++){
		s += '<tr>';
			s += '<td class="text-center"><a onclick="fn.app.production_blackdust.prepare.remove_pack(this)" href="javascript:;" class="btn btn-sm btn-danger">X</a>';
			s += '<input type="hidden" name="pack_id[]" value="" class="pack_id form-control">';
			s += '<input type="hidden" name="pack_method[]" value="" class="pack_method form-control">';
			s += '<input type="hidden" name="weight_expected[]" value="'+weight+'" class="pack_method form-control">';
			s += '</td>';
			s += '<td><input name="pack_code[]" xname="pack_code" value="'+pack_num+'" class="form-control"></td>';
			s += '<td>';
				s += '<input readonly name="pack_name[]" xname="pack_name" value="'+name+'" class="pack_method form-control">';
			s += '</td>';
			s += '<td>';
				s += '<select name="pack_type[]" class="form-control">';
					s += '<option'+(type=="ถุงปกติ"?" selected":"")+'>ถุงปกติ</option>';
					s += '<option'+(type=="ถุงกระสอบ"?" selected":"")+'>ถุงกระสอบ</option>';
				s += '</select>';
			s += '</td>';
			s += '<td><input name="weight_actual[]" xname="weight_actual" class="form-control" placeholder="น้ำหนักที่ชั่งไว้"></td>';
		s += '</tr>';
		}
		
		$("#tblPacking tbody").append(s);
		$("#dialog_add_pack").modal("hide");
	};
	
	fn.app.production_blackdust.prepare.remove_pack = function(btn) {
		var tr = $(btn).parent().parent();
		var id = tr.find(".pack_id").val();
		if(id != ""){
			if(tr.find(".pack_method").val()==""){
				tr.find(".pack_method").val("remove");
				tr.addClass("bg-danger");
			}else{
				tr.find(".pack_method").val("");
				tr.removeClass("bg-danger");
			}
			
		}else{
			tr.remove();
		}
		
	}
	
	
	
	fn.app.production_blackdust.prepare.dialog_edit = function(id) {
		$.ajax({
			url: "apps/production_blackdust/view/dialog.prepare.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_prepare"});
			}
		});
	};

	fn.app.production_blackdust.prepare.edit = function(){
		$.post("apps/production_blackdust/xhr/action-edit-prepare.php",$("form[name=form_editprepare]").serialize(),function(response){
			if(response.success){
				$("#tblPrepare").DataTable().draw();
				$("#dialog_edit_prepare").modal("hide");
				window.location.reload();
				//window.location = "#apps/production_prepare/index.php";
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};

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
	
	fn.app.production_blackdust.prepare.pack_update = function(input){

		
		$.ajax({
			url: "apps/production_blackdust/xhr/action-pack-change.php",
			data: {
				id: $(input).attr("data-id"),
				weight_actual : $(input).val()
			},
			type: "POST",
			dataType: "html",
			success: function(html){
				/*
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_prepare"});
				*/
			}
		});
	}
	
	$("#tblPacking").DataTable({
		responsive: true,
		"bStateSave": true,
		"autoWidth" : true,
		"processing": true,
		"serverSide": true,
		"ajax": {
			"data" : function(d){
				d.production_id = $("#tblPacking").attr("data-id");
			},
			"url":"apps/production_blackdust/store/store-packing.php"
		},
		"aoColumns": [
			{"bSort":true		,"data":"id",	class: "text-center"	},
			{"bSort":true		,"data":"code",	class: "text-center"	},
			{"bSort":true		,"data":"pack_name",	class: "text-center"	},
			{"bSort":true		,"data":"pack_type",	class: "text-center"	},
			{"bSort":true		,"data":"weight_expected",	class: "text-center"	},
			{"bSort":true		,"data":"weight_actual",	class: "text-center"	},
			{"bSort":true		,"data":"status",	class: "text-center"	}
		],"order": [[ 1, "desc" ]],
		"createdRow": function ( row, data, index ) {
			
			var s = '';
			
			$("td", row).eq(5).html('<input type="number" step="0.0001" data-id="'+data.id+'" onchange="fn.app.production_blackdust.prepare.pack_update(this)" class="form-control form-control-sm" value="'+(data.weight_actual==null?"":data.weight_actual)+'">');
			
			if(data.status=="0"){
				s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-trash","fn.app.production_blackdust.pack.remove("+data[0]+")");
			}else{
				s += '<span class="badge badge-warning">-</span>';
			}
			$("td", row).eq(6).html(s);
			
		}
	});
	
	
	$("select[name=stock_prepare]").change(function(){
		$.post("apps/production_blackdust/xhr/action-load-prepare.php",{id:$(this).val()},function(html){
			$("#OutputA").html(html);
		},"html");
	}).change();
	
	fn.app.production_blackdust.prepare.calculation();
	
	


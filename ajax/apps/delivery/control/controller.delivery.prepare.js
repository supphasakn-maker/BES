	fn.app.delivery.delivery.dialog_prepare = function(id) {
		$.ajax({
			url: "apps/delivery/view/dialog.delivery.prepare.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_prepare_delivery"});
			}
		});
	};

	fn.app.delivery.delivery.prepare = function(){
		$.post("apps/delivery/xhr/action-prepare-delivery.php",$("form[name=form_preparedelivery]").serialize(),function(response){
			if(response.success){
				//window.location.reload();
				history.back();
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	fn.app.delivery.delivery.remove_driver = function(btn){
		let tr = $(btn).parent().parent();
		
		if(tr.find("[xname=item_id]").val()==""){
			tr.remove();
		}else{
			if(tr.find("[xname=action]").val()==""){
				tr.find("[xname=action]").val("remove");
				tr.addClass("bg-danger");
			}else{
				tr.find("[xname=action]").val("");
				tr.removeClass("bg-danger");
			}
			
		}
		
	};
	
	fn.app.delivery.delivery.append_driver = function() {
		let driver_name = $("select[name=driver]").find(":selected").html();
	
		let s = '';
		s += '<tr>';
			s += '<td><button class="btn btn-xs btn-danger" onclick="fn.app.delivery.delivery.remove_driver(this)" type="button">Remove</button></td>'
			s += '<td>';
				s += '<input type="hidden" name="emp_driver[]" value="'+$("select[name=driver]").val()+'">';
				s += '<input type="hidden" xname="item_id" name="item_id[]" value="">';
				s += '<input type="hidden" xname="action" name="action[]" value="">';
				s += '<input class="form-control form-control-sm" readonly value="'+driver_name+'">';
			s +='</td>';
			s += '<td><input type="time" name="time_departure[]" class="form-control form-control-sm"></td>';
			s += '<td><input type="time" name="time_arrive[]" class="form-control form-control-sm"></td>';
			s += '<td>' + $("#template_TruckType").html() + '</td>';
			s += '<td>' + $("#template_TruckLicense").html() + '</td>';
			
		s += '</tr>';
		$("#tblDriver tbody ").append(s);
	};
	
	fn.app.delivery.delivery.prepare_append_driver = function() {
		var total_driver = 0;
		$("#tblDriver > thead > tr > th").each(function(){
			total_driver++;
		});
		$("#tblDriver > thead > tr > td").before("<th>ผู้ส่ง "+(total_driver+1)+"</th>");
		var s = '<td>' + $("#tblDriver > tbody > tr > td:fisrt-child").html() + '</td>';
		$("#tblDriver > tbody > tr > td").before(s);
		
		/*
		("#tblDriver tbody tr:first-child").html();
		v$("#tblDriver tbody tr:first-child").html();
		*/
	};




$("#tblPackitem").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url" : "apps/delivery/store/store-item.php",
		"data" : function(d){
			d.where = "bs_delivery_pack_items.delivery_id = "+$("#tblDeliveryDetail").attr("data-id");
		}
	},	
	"aoColumns": [
		{"bSort":true			,"data":"code", "class": "text-center"	},
		{"bSort":true			,"data":"pack_type" , "class": "text-center"	},
		{"bSort":true			,"data":"pack_name", "class": "text-center"	},
		{"bSort":true			,"data":"weight_expected", "class": "text-center"	},
		{"bSort":true			,"data":"weight_actual", "class": "text-center"	},
		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {

		var s = '';
		s += fn.ui.button("btn btn-xs btn-danger","far fa-trash","fn.app.delivery.delivery.remove_mapping("+data.id+")");
		$("td", row).eq(5).html(s);
	}
});

$("[name=code_search]").select2({
  ajax: {
    url: 'apps/delivery/xhr/action-load-item.php',
    dataType: 'json',
	data: function (d) {
		d.production_id = $('select[name=round_filter]').val();
		return d;
    },
	processResults: function (data, params) {
		return {
			results: data.results
		};
	}
  }
});

fn.app.delivery.delivery.mapping = function(){
	$.post("apps/delivery/xhr/action-delivery-mapping.php",{
		packing_id : $("[name=code_search]").val(),
		delivery_id : $("#tblDeliveryDetail").attr("data-id")
		
	},function(response){
		
		if(response.success){
			$("#tblPackitem").DataTable().draw();
			fn.app.delivery.delivery.load_data();
		}else{
			fn.notify.warnbox(response.msg,"Oops...");
		}
	},"json");
	return false;
};

fn.app.delivery.delivery.remove_mapping = function(id){
	$.post("apps/delivery/xhr/action-mapping-remove.php",{id : id},function(response){
		
		if(response.success){
			$("#tblPackitem").DataTable().draw();
			fn.app.delivery.delivery.load_data();
		}else{
			fn.notify.warnbox(response.msg,"Oops...");
		}
	},"json");
	return false;
};


$("select[name=round_filter]").change(function(){
	$.post("apps/delivery/xhr/action-list-item.php",{production_id:$(this).val()},function(list){
		var s = '';
		for(i in list){
			s += '<div class="custom-control custom-checkbox mr-4" onclick="fn.app.delivery.delivery.calculate()">';
				s += '<input name="item_selected_id[]" checked data-name="item" data-value="'+list[i].text+'" value="'+list[i].id+'" type="checkbox" class="custom-control-input" id="x'+list[i].id+'">';
				s += '<label class="custom-control-label" for="x'+list[i].id+'">'+list[i].text+'</label>';
			s += '</div>'; 
		}
		$("#select_list_stock").html(s);
	},"json");
});

fn.app.delivery.delivery.toggle_check = function(){
	$("input[data-name=item]").click();
};

fn.app.delivery.delivery.calculate = function(){
	
};


fn.app.delivery.delivery.append_bulk = function(){
	let selected = [];
	let delivery_id = $("#tblDeliveryDetail").attr('data-id');
	
	$("#select_list_stock input:checked").each(function(){
		selected.push($(this).val());
	});
	console.log(selected);
	$.post("apps/delivery/xhr/action-delivery-mapping-bulk.php",{delivery_id:delivery_id,selected : selected},function(response){
		
		if(response.success){
			$("#tblPackitem").DataTable().draw();
			$("select[name=round_filter]").change();
		}else{
			fn.notify.warnbox(response.msg,"Oops...");
		}
	},"json");
	return false;
};



$("#tblPackitem").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url" : "apps/production_pmr/store/store-item.php",
		"data" : function(d){
			d.where = "bs_pmr_pack_items.pmr_id = "+$("#tblPmrDetail").attr("data-id");
		}
	},	
	"aoColumns": [
		{"bSort":true			,"data":"code", "class": "text-center"	},
		{"bSort":true			,"data":"pack_type" , "class": "text-center"	},
		{"bSort":true			,"data":"pack_name", "class": "text-center"	},
		{"bSort":true			,"data":"weight_expected", "class": "text-center"	},
		{"bSort":true			,"data":"weight_actual", "class": "text-center"	},
		{"bSortable":false		,"data":"item_id"		,"sClass":"text-center" , "sWidth": "80px"  }
		
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {

		var s = '';
		s += fn.ui.button("btn btn-xs btn-danger","far fa-trash","fn.app.production_pmr.pmr.remove_mapping("+data[3]+")");
		$("td", row).eq(5).html(s);
	}
});



$("select[name=round_filter]").change(function(){
	$.post("apps/production_pmr/xhr/action-list-item.php",{production_id:$(this).val()},function(list){
		var s = '';
		for(i in list){
			s += '<div class="custom-control custom-checkbox mr-4" onclick="fn.app.production_pmr.pmr.calculate()">';
				s += '<input name="item_selected_id[]" checked data-name="item" data-value="'+list[i].text+'" value="'+list[i].id+'" type="checkbox" class="custom-control-input" id="x'+list[i].id+'">';
				s += '<label class="custom-control-label" for="x'+list[i].id+'">'+list[i].text+'</label>';
			s += '</div>'; 
		}
		$("#select_list_stock").html(s);
	},"json");
});

fn.app.production_pmr.pmr.calculate = function(){
	
};

$("[name=code_search]").select2({
    ajax: {
      url: 'apps/production_pmr/xhr/action-load-item.php',
      dataType: 'json',
      data: function (d) {
          d.production_id= $('select[name=code_search]').val();
          return d;
      },
      processResults: function (data, params) {
          return {
              results: data.results
          };
      }
    }
  });
  
fn.app.production_pmr.pmr.mapping = function(){
	$.post("apps/production_pmr/xhr/action-pmr-mapping.php",{
		packing_id : $("[name=code_search]").val(),
		pmr_id : $("#tblPmrDetail").attr("data-id")
		
	},function(response){
		
		if(response.success){
			$("#tblPackitem").DataTable().draw();
			fn.app.production_pmr.pmr.load_data();
		}else{
			fn.notify.warnbox(response.msg,"Oops...");
		}
	},"json");
	return false;
};

fn.app.production_pmr.pmr.remove_mapping = function(id){
	$.post("apps/production_pmr/xhr/action-mapping-remove.php",{id : id},function(response){
		
		if(response.success){
			$("#tblPackitem").DataTable().draw();
			fn.app.production_pmr.pmr.load_data();
		}else{
			fn.notify.warnbox(response.msg,"Oops...");
		}
	},"json");
	return false;
};


fn.app.production_pmr.pmr.toggle_check = function(){
	$("input[data-name=item]").click();
};

fn.app.production_pmr.pmr.append_bulk = function(){
	let selected = [];
	let pmr_id = $("#tblPmrDetail").attr('data-id');
	
	$("#select_list_stock input:checked").each(function(){
		selected.push($(this).val());
	});
	console.log(selected);
	$.post("apps/production_pmr/xhr/action-pmr-mapping-bulk.php",{pmr_id:pmr_id,selected : selected},function(response){
		
		if(response.success){
			$("#tblPackitem").DataTable().draw();
			$("select[name=round_filter]").change();
		}else{
			fn.notify.warnbox(response.msg,"Oops...");
		}
	},"json");
	return false;
};


	$("#tblImportInput").data("selected",[]);
	
	fn.app.production.import.dialog_lookup = function(func,selected_member) {
		$.ajax({
			url: "apps/production/view/dialog.import.lookup.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_import_lookup").on("hidden.bs.modal",function(){$(this).remove();});
				$("#dialog_import_lookup").modal('show');
				$("#tblImportLookup").data( "selected",$("#tblImportInput").data("selected"));
				$('#tblImportLookup').DataTable({
					"bStateSave": true,
					"autoWidth" : true,
					"processing": true,
					"serverSide": true,
					"ajax": "apps/production/store/store-import.php",
					"aoColumns": [
						{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
						{"bSortable":true		,"data":"delivery_date"	},
						{"bSortable":true		,"data":"supplier_name"	},
						{"bSortable":true		,"data":"amount"	},
						{"bSortable":true		,"data":"delivery_by"	},
						{"bSortable":true		,"data":"type"	},
						{"bSortable":true		,"data":"comment"	},
						{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
					],"order": [[ 2, "desc" ]],
					"createdRow": function ( row, data, index ) {
						var selected = false,checked = "",s = '';
						if ( $.inArray(data.DT_RowId, selected_member) !== -1 ) {
							$(row).addClass('hidden');
						}else{
							if ( $.inArray(data.DT_RowId, $("#tblImportLookup").data("selected")) !== -1 ) {
								$(row).addClass('selected');
								selected = true;
							}
							$('td', row).eq(0).html(fn.ui.checkbox("chk_import",data[0],selected,true));
							
						}
					}
				});
				fn.ui.datatable.selectable('#tblImportLookup','chk_import',true);
			}	
		});
		
	};
	
	fn.app.production.import.select = function(){
		if($("#tblImportLookup").data("selected").length==0){
			fn.notify.warnbox("Please select item!","Oops...");
		}else{
			$.post("apps/production/xhr/action-load-import.php",{id:$("#tblImportLookup").data("selected")},function(json){
				$("#tblImportInput").data( "selected",$("#tblImportLookup").data("selected"));
				
				var s = '';
				for(i in json){
					s += '<tr class="new" data-import="'+json[i].id+'">';
						s += '<td class="text-center">';
							s += '<span class="btn btn-danger btn-xs" onclick="$(this).parent().parent().remove();fn.app.production.import.calculate();"><i class="fa fa-trash"></i></span>';
							s += '<input type="hidden" name="import_id[]" value="'+json[i].id+'">';
						s += '</td>';
						s += '<td class="text-center" width="100">';
							s += json[i].delivery_date;
						s += '</td>';
						s += '<td>';
							s += '<input xname="import_time" type="time" name="import_time[]" class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล">';
						s += '</td>';
						s += '<td>';
							s += '<input xname="import_delivery_note" name="import_delivery_note[]" class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล">';
						s += '</td>';
						s += '<td>';
							s += '<input xname="bar" onchange="fn.app.production.import.calculate()" name="import_bar[]" class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล">';
						s += '</td>';
						s += '<td>';
							s += '<input xname="weight_in" onchange="fn.app.production.import.calculate()" data-item="id" name="import_weight_in[]" class="form-control form-control-sm text-right">';
						s += '</td>';
						s += '<td class="text-right">';
							s += '<input xname="amount" name="import_amount[]" value="'+json[i].amount+'" class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล">';
						s += '</td>';
						s += '<td class="text-right">';
							s += '<input xname="margin" name="import_weight_margin[]" class="form-control form-control-sm text-right" readonly>';
						s += '</td>';
						s += '<td class="text-right">';
							s += '<input xname="average" name="import_weight_average[]" class="form-control form-control-sm text-right" readonly>';
						s += '</td>';
						s += '<td class="text-right">';
							s += '<button class="btn bnt-warning" onclick="fn.app.production.produce.dialog_select_import('+json[i].id+')">Select</button>';
						s += '</td>';
					s += '</tr>';
				}
				
				$("#tblImportInput tbody tr.new").remove();
				$("#tblImportInput tbody").append(s);
				
				$("#dialog_import_lookup").modal('hide');
			},'json');
		}
	}
	
	fn.app.production.import.calculate = function(){
		var child = 1;
		$("input[name^=weight_in]").val(0);
		$("#tblImportInput tbody tr").each(function(){
			var bar = parseFloat($(this).find("input[xname=bar]").val());
			var amount = parseFloat($(this).find("input[xname=amount]").val());
			var weight_in = parseFloat($(this).find("input[xname=weight_in]").val());
			$(this).find("input[xname=margin]").val((weight_in-amount).toFixed(4));
			$(this).find("input[xname=average]").val((weight_in/bar).toFixed(4));
			$("input[name=weight_in_" + child + "]").val(weight_in);
			child++;
		});
	}

	

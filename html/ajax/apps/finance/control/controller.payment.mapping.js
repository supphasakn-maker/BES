	fn.app.finance.payment.dialog_show = function(id) {
		$.ajax({
			url: "apps/finance/view/dialog.payment.show.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_mapping_payment"});
				fn.app.finance.payment.calculate();
				
			}
		});
	};
	
	fn.app.finance.payment.dialog_mapping = function(id) {
		$.ajax({
			url: "apps/finance/view/dialog.payment.mapping.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_mapping_payment"});
				fn.app.finance.payment.calculate();
				
			}
		});
	};
	
	fn.app.finance.payment.append_item = function(){
		var type_id = $("[name=payment_type_append]").val();
		fn.system.get_record({
			"table" : "bs_payment_types",
			"select" : "*",
			"where" : "id="+type_id
		},function(json){
			let s = '';
			s += '<tr>';
				s += '<td class="text-center"><a class="btn btn-danger btn-danger-remove" onclick="$(this).parent().parent().remove()">Remove</a></td>';
				s += '<td class="text-center"><input type="hidden" name="type_id[]" value="'+json.id+'">'+json.name+'</td>';
				s += '<td><input class="form-control text-right"  name="amount[]"></td>';
				s += '<td><input class="form-control text-right"  name="remark[]"</td>';
			s += '</tr>';
			$("#tblMappingItem tbody").append(s);
		});
		
	}
	
	fn.app.finance.payment.calculate = function(){
		console.log("test");
		$("#tblMapping tbody tr").each(function(){
			
			var net = parseFloat($(this).find("[xname=net]").val().replace(',',''));
			var paid = parseFloat($(this).find("[xname=paid]").val().replace(',',''));
			var remain = net-paid;
			$(this).find("[xname=remain]").val(remain.toFixed(2));
			
		});
	}
	
	fn.app.finance.payment.dialog_order_lookup = function(id) {
		$.ajax({
			url: "apps/finance/view/dialog.order.lookup.php",
			data: {customer_id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_order_lookup").on("hidden.bs.modal",function(){$(this).remove();});
				$("#dialog_order_lookup").modal('show');
				
				
				$("#tblOrderLookup").data( "selected",[]);
				$('#tblOrderLookup').DataTable({
					"bStateSave": true,
					"autoWidth" : true,
					"processing": true,
					"serverSide": true,
					"ajax": {
						"data" : function(d){
							d.customer_id = id;
						},
						"url" : "apps/finance/store/store-order.php",
					},
					"aoColumns": [
						{"bSortable": false	,"data":"id"		,"sWidth": "20px", "sClass" : "hidden-xs text-center"},
						{"bSortable": true	,"data":"code"	,"sClass" : "text-center"},
						{"bSortable": true	,"data":"billing_id"	,"sClass" : "text-center"},
						{"bSortable": true	,"data":"date"	,"sClass" : "text-center"},
						{"bSortable": true	,"data":"delivery_date"	,"sClass" : "text-center"},
						{"bSortable": true	,"data":"amount"	,"sClass" : "text-center"},
						{"bSortable": true	,"data":"total"	,"sClass" : "text-center"},
						{"bSortable": true	,"data":"vat"	,"sClass" : "text-center"},
						{"bSortable": true	,"data":"net"	,"sClass" : "text-center"}
					],"order": [[ 2, "desc" ]],
					"createdRow": function ( row, data, index ) {
						var selected = false,checked = "",s = '';
						if ( $.inArray(data.DT_RowId, $("#tblOrderLookup").data("selected")) !== -1 ) {
							$(row).addClass('selected');
							selected = true;
						}
						$('td', row).eq(0).html(fn.ui.checkbox("chk_order",data[0],selected,true));
					}
				});
				fn.ui.datatable.selectable('#tblOrderLookup','chk_order',true);
				
				
				$("#dialog_order_lookup .btnSelect").unbind().click(function(){
					if($("#tblOrderLookup").data("selected").length==0){
						fn.engine.alert("No selected","Please select order!");
					}else{
						$.post("apps/finance/xhr/action-load-order.php",{id:$("#tblOrderLookup").data("selected")},function(json){
							var s = '';
							for(i in json){
								s += '<tr>';
									s += '<td class="text-center"><a class="btn btn-danger btn-danger-remove" onclick="$(this).parent().parent().remove()">Remove</a></td>';
							
									s += '<td class="text-center"><input type="hidden" name="order_id[]" value="'+json[i].id+'">'+json[i].code+'</td>';
									s += '<td class="text-center">'+json[i].date+'</td>';
									s += '<td><input class="form-control text-right" xname="net" readonly name="net[]" value="'+parseFloat(json[i].net).toFixed(2)+'"></td>';
									s += '<td><input onchange="fn.app.finance.payment.calculate()" class="form-control text-right" xname="paid" name="paid[]" value="'+parseFloat(json[i].net).toFixed(2)+'"></td>';
									s += '<td><input class="form-control text-right" xname="remain" readonly name="remain[]" value="'+parseFloat(json[i].net).toFixed(2)+'"></td>';
		
								s += '</tr>';
							}
							
							$("#tblMapping tbody").append(s);
							$("#dialog_order_lookup").modal('hide');
							fn.app.finance.payment.calculate();
						},'json');
					}
					
				});
				
			}
		});
	};

	fn.app.finance.payment.mapping = function(){
		$.post("apps/finance/xhr/action-mapping-payment.php",$("form[name=form_mappingpayment]").serialize(),function(response){
			if(response.success){
				$("#tblPayment").DataTable().draw();
				$("#dialog_mapping_payment").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	
	fn.app.finance.payment.append_deposit = function(){
		
		let s = '';
		s += '<tr>';
			s += '<td class="text-center">';
				s += '<input xname="deposit_action" type="hidden" name="deposit_action[]" value="">';
				s += '<input xname="deposit_id" type="hidden" name="deposit_id[]" value="">';
				s += '<a class="btn btn-danger btn-danger-remove" onclick="fn.app.finance.payment.deposit_remove(this)">Remove</a>';
			s += '</td>';
			s += '<td><input class="form-control text-right"  name="deposit[]"></td>';
		s += '</tr>';
		$("#tblMappingDeposit tbody").append(s);
		
		
	}
	
	fn.app.finance.payment.deposit_remove = function(btn){
		var tr = $(btn).parent().parent();
		if(tr.find("[xname=deposit_id]").val()==""){
			tr.remove();
		}else{
			if(tr.find("[xname=deposit_action]").val()==""){
				tr.find("[xname=deposit_action]").val("remove");
				tr.addClass("bg-danger");
			}else{
				tr.find("[xname=deposit_action]").val("");
				tr.removeClass("bg-danger");
			}
		}
		
	}
	
	fn.app.finance.payment.deposit_use_remove = function(btn){
		var tr = $(btn).parent().parent();
		if(tr.find("[xname=deposit_use_id]").val()==""){
			tr.remove();
		}else{
			if(tr.find("[xname=deposit_use_action]").val()==""){
				tr.find("[xname=deposit_use_action]").val("remove");
				tr.addClass("bg-danger");
			}else{
				tr.find("[xname=deposit_use_action]").val("");
				tr.removeClass("bg-danger");
			}
		}
		
	}
	
	
	
	fn.app.finance.payment.dialog_deposit_lookup = function(id) {
		$.ajax({
			url: "apps/finance/view/dialog.deposit.lookup.php",
			data: {customer_id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_deposit_lookup").on("hidden.bs.modal",function(){$(this).remove();});
				$("#dialog_deposit_lookup").modal('show');
				
				
				$("#tblDepositLookup").data( "selected",[]);
				$('#tblDepositLookup').DataTable({
					"bStateSave": true,
					"autoWidth" : true,
					"processing": true,
					"serverSide": true,
					"ajax": {
						"data" : function(d){
							d.customer_id = id;
						},
						"url" : "apps/finance/store/store-deposit.php",
					},
					"aoColumns": [
						{"bSortable": false	,"data":"id"		,"sWidth": "20px", "sClass" : "hidden-xs text-center"},
						{"bSortable": true	,"data":"date"	,"sClass" : "text-center"},
						{"bSortable": true	,"data":"amount"	,"sClass" : "text-center"},
						{"bSortable": true	,"data":"remain"	,"sClass" : "text-center"},
					],"order": [[ 1, "desc" ]],
					"createdRow": function ( row, data, index ) {
						var selected = false,checked = "",s = '';
						if ( $.inArray(data.DT_RowId, $("#tblDepositLookup").data("selected")) !== -1 ) {
							$(row).addClass('selected');
							selected = true;
						}
						$('td', row).eq(0).html(fn.ui.checkbox("chk_deposit",data[0],selected,true));
					}
				});
				fn.ui.datatable.selectable('#tblDepositLookup','chk_deposit',true);
				
				
				$("#dialog_deposit_lookup .btnSelect").unbind().click(function(){
					if($("#tblDepositLookup").data("selected").length==0){
						fn.engine.alert("No selected","Please select order!");
					}else{
						$.post("apps/finance/xhr/action-load-deposit.php",{id:$("#tblDepositLookup").data("selected")},function(json){
							var s = '';
							for(i in json){
								s += '<tr>';
									s += '<td class="text-center">';
										s += '<input xname="deposit_use_id" type="hidden" name="deposit_use_id[]" value="">';
										s += '<input xname="deposit_use_deposit_id" type="hidden" name="deposit_use_deposit_id[]" value="'+json[i].id+'">';
										s += '<input xname="deposit_use_action" type="hidden" name="deposit_use_action[]" value="">';
										s += '<a class="btn btn-danger btn-danger-remove" onclick="fn.app.finance.payment.deposit_use_remove(this)">Remove</a>';
									s += '</td>';
									s += '<td><input class="form-control text-right" name="deposit_deposit[]" value="'+json[i].amount+'" readonly></td>';
									s += '<td><input class="form-control text-right" name="deposit_use[]" value="'+json[i].amount+'"></td>';
								s += '</tr>';
							}
							
							$("#tblMappingDepositUsed tbody").append(s);
							$("#dialog_deposit_lookup").modal('hide');
							fn.app.finance.payment.calculate();
						},'json');
					}
					
				});
				
			}
		});
	};

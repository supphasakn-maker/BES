	fn.app.forward_contract.contract.dialog_add = function() {
		$.ajax({
			url: "apps/forward_contract/view/dialog.contract.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_contract"});
			}
		});
	};

	fn.app.forward_contract.contract.add = function(){
		var total_value = 0;
		var select_purchase=[];
		$("input[data-name=purchase]:checked").each(function(){
			total_value += parseFloat($(this).attr("data-value"));
			//total_value += parseFloat($(this).attr("data-amount"));
			select_purchase.push($(this).val());
		});
		total_value += parseFloat($("input[name=deposit]").val());
		
		
		var total_amount = 0;
		var select_data = {
			bank_date : [],
			premium_date : [],
			contact_no : [],
			premium : [],
			amount : [],
			usd : [],
			thbpremium : []
		}
		
		$("#tblUSD tr.selected").each(function(){
			var bank_date = $(this).find("input[xname=bank_date]").val();
			var premium_date = $(this).find("input[xname=premium_date]").val();
			var contact_no = $(this).find("input[xname=contact_no]").val();
			var premium = $(this).find("input[xname=premium]").val();
			var amount = $(this).find("input[xname=amount]").val();
			var thbpremium = $(this).find("input[xname=thbpremium]").val();
			total_amount += parseFloat(amount);
			var usd = $(this).attr("id");
			
			select_data.bank_date.push(bank_date);
			select_data.premium_date.push(premium_date);
			select_data.contact_no.push(contact_no);
			select_data.premium.push(premium);
			select_data.amount.push(amount);
			select_data.usd.push(usd);
			select_data.thbpremium.push(thbpremium);
		});
		
		
		if(total_value == 0){
			/*
			var s = '';
			s += '<div>จำนวนไม่ตรงกัน</div>';
			s += '<div>ยอด Net Goods Value : '+total_value+' </div>';
			s += '<div>ยอด USD Purcased : '+total_amount+' </div>';
			fn.notify.warnbox(s,"Oops...");
			*/
			var s = '';
			s += '<div>โปรดเลือกรายการ</div>';
			fn.notify.warnbox(s,"Oops...");
		}else{
			$("input[name=select_usd]").val(select_data.usd.join(","));
			$("input[name=select_amount]").val(select_data.amount.join(","));
			$("input[name=select_bank_date]").val(select_data.bank_date.join(","));
			$("input[name=select_premium_date]").val(select_data.premium_date.join(","));
			$("input[name=select_contact_no]").val(select_data.contact_no.join(","));
			$("input[name=select_premium]").val(select_data.premium.join(","));
			$("input[name=select_purchase]").val(select_purchase.join(","));
			$("input[name=select_thbpremium]").val(select_data.thbpremium.join(","));
			
			$.post('apps/forward_contract/xhr/action-add-contract.php',$('form[name=form_addcontract]').serialize(),function(response){
					if(response.success){
						$("#tblUSD").DataTable().draw();
						$("#tblContract").DataTable().draw();
						$('form[name=form_addcontract]')[0].reset();
						$("input[name=deposit]").change();
						fn.notify.successbox(response.msg,"บันทึกแล้ว");
					}else{
						fn.notify.warnbox(response.msg,"Oops...");
					}
				},'json');
		
		}
		return false;
		
		
	};
	
	function numberWithCommas(x) {
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	
	
	$("select[name=supplier_id]").change(function(){
		$.post("apps/forward_contract/xhr/action-list-purchase.php",{supplier_id:$(this).val()},function(list){
			var s = '';
			s += '<table class="table table-sm">';
			s += '<tbody>';
			for(i in list){
				var usd = parseFloat(list[i].usd);
				//var net_value = parseFloat(list[i].amount)*(parseFloat(list[i].rate_spot)+parseFloat(list[i].rate_pmdc)); 
				var amount = parseFloat(list[i].amount);
				//var rate_spot = parseFloat(list[i].rate_spot);
				//var rate_pmdc = parseFloat(list[i].rate_pmdc);
				
					s += '<tr>';
						s += '<td>';
							s += '<div class="custom-control custom-checkbox" onclick="fn.app.forward_contract.contract.calculate()">';
								s += '<input name="purchase[]" data-name="purchase" data-amount="'+amount+'" data-value="'+usd+'" value="'+list[i].id+'" type="checkbox" class="custom-control-input" id="x'+list[i].id+'">';
								
								s += '<label class="custom-control-label" for="x'+list[i].id+'">';
									s += list[i].date;
								s += '</label>';
							s += '</div>';
						s += '</td>';
						s += '<td class="text-center">'+list[i].supplier+'</td>';
						s += '<td class="text-right mr-1">'+amount.toFixed(4)+'</td>';
						s += '<td class="text-right mr-1">'+usd.toFixed(4)+'</td>';
						//s += '<td class="text-center">'+list[i].ref+'</td>';
					
					s += '</tr>';
					
			}
			s += '</tbody>';
			s += '</table>';
			$("#select_purchase").html(s);
		},"json");
	});
	
	
	fn.app.forward_contract.contract.calculate = function(){
		var total_value = 0;
		var total_fixed = 0;
		var total_purchase = 0;
		var total_nonfixed = 0;
		
		$("input[data-name=purchase]:checked").each(function(){
			total_purchase += parseFloat($(this).attr("data-amount"));
			total_value += parseFloat($(this).attr("data-value"));
		});
		$("input[name=net_good_value]").val(total_value);
		$("input[name=total_transfer]").val(total_value + parseFloat($("input[name=deposit]").val()));
		
		
		$("#tblUSD tr.selected").each(function(){
			
			//var bank_date = $(this).find("input[xname=bank_date]").val();
			//var premium_date = $(this).find("input[xname=premium_date]").val();
			var contact_no = $(this).find("input[xname=contact_no]").val();
			var premium = $(this).find("input[xname=premium]").val();
			var amount = $(this).find("input[xname=amount]").val();
			var rate_exchange = $(this).find("input[xname=rate_exchange]").val();
			
			total_fixed += parseFloat(amount);
			if(premium=="")premium=0;
			$(this).find("input[xname=premiumfx]").val((parseFloat(rate_exchange)+parseFloat(premium)).toFixed(4));
			$(this).find("input[xname=thbpremium]").val((amount*(parseFloat(rate_exchange)+parseFloat(premium))).toFixed(2));
		});
		
		total_nonfixed = total_value-total_fixed;
		$("input[name=fixed_value]").val(total_fixed);
		$("input[name=nonfixed_value]").val(total_nonfixed);
		
	};
	
	$("input[name=deposit]").change(function(){
		fn.app.forward_contract.contract.calculate();
	});
	

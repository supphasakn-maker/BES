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
			$("input[name=select_purchase]").val(select_purchase.join(","));
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
		let supplier_id = $(this).val();
		let source = $("select[name=source]").val();
		console.log(source);
		
		$.post("apps/forward_contract/xhr/action-list-purchase.php",{
			source : source,
			supplier_id : supplier_id
		},function(list){
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
						s += '<td class="text-right mr-1">'+list[i].formated_amount+'</td>';
						s += '<td class="text-right mr-1">'+list[i].formated_usd+'</td>';
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
		var total_nonfixed = 0;
		var total_fixed_thb = 0;
		var total_fixed_premium = 0;
		
		let value_usd_goods = 0;
		let value_usd_deposit = parseFloat($("input[name=value_usd_deposit]").val());
		let value_usd_paid = parseFloat($("input[name=value_usd_paid]").val());
		let value_usd_adjusted = 0;
		let value_usd_total = 0;
		let value_usd_fixed = 0;
		let value_usd_nonfixed = 0;
		let rate_counter = parseFloat($("input[name=rate_counter]").val());
		let value_thb_fixed = 0;
		let value_thb_premium = 0;
		let value_thb_net = 0;
		
		// รวมตัวแปรก่อนทำงานต่อเนื่อง
		$("input[data-name=purchase]:checked").each(function(){
			value_usd_goods += parseFloat($(this).attr("data-value"));
		});
		$("input[name=value_usd_goods]").val(value_usd_goods.toFixed(2));
		
		$("input[xname=ajusted_value]").each(function(){
			value_usd_adjusted += parseFloat($(this).val());
		});
		$("input[name=value_usd_adjusted]").val(value_usd_adjusted);

		value_usd_total = value_usd_goods + value_usd_deposit - value_usd_paid + value_usd_adjusted;
		$("input[name=value_usd_total]").val(value_usd_total.toFixed(2));
		

		$("#tblSelected tbody tr").each(function(){
			var date_premium_start = new Date($(this).find("input[xname=date_premium_start]").val())
			var date_premium_end = new Date($(this).find("input[xname=date_premium_end]").val());
			var Difference_In_Time = date_premium_end.getTime()-date_premium_start.getTime();
			var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);
			var contact_no = $(this).find("input[xname=contact_no]").val();
			var rate_premium = parseFloat($(this).find("input[xname=rate_premium]").val());
			var amount = parseFloat($(this).find("input[xname=amount]").val());
			var rate_exchange = parseFloat($(this).find("input[xname=rate_exchange]").val());
			
			if(isNaN(Difference_In_Days))Difference_In_Days=0;
			$(this).find("input[xname=premium_day]").val(Difference_In_Days);
			
			value_usd_fixed += amount;
			value_thb_fixed += parseFloat((amount*rate_exchange).toFixed(2));

			if(rate_premium=="")rate_premium=0;
			let interest = Difference_In_Days * parseFloat(rate_premium) / 365;
			let premium = amount * interest * rate_exchange;
			$(this).find("input[xname=usd_premium]").val(premium);

			value_thb_premium += premium;
			
			$(this).find("input[xname=premiumfx]").val((rate_exchange+rate_premium).toFixed(4));
			$(this).find("input[xname=thbpremium]").val((amount*(rate_exchange+interest)).toFixed(2));
		});
		
		value_usd_nonfixed = value_usd_total-value_usd_fixed;
		$("input[name=value_usd_nonfixed]").val(value_usd_nonfixed.toFixed(2));
		$("input[name=value_usd_fixed]").val(value_usd_fixed.toFixed(2));
		$("input[name=value_thb_fixed]").val(value_thb_fixed.toFixed(2));
		$("input[name=value_thb_premium]").val(value_thb_premium.toFixed(2));
		value_thb_net = value_thb_fixed+value_thb_premium;
		$("input[name=value_thb_net]").val(value_thb_net.toFixed(2));

	};
	
	$("input[name=deposit]").change(function(){
		fn.app.forward_contract.contract.calculate();
	});
	
	
	fn.app.forward_contract.contract.append = function(tr){
		let id = tr[0].id;
		if(tr.hasClass("selected")){
			$.post("apps/forward_contract/xhr/action-load-usd.php",{id:id},function(data){
				let s = '';
				s += '<tr data-id="'+id+'">';
					s += '<td class="text-center">';
						s += data.date;
						s += '<input type="hidden" name="usd_id[]" xname="id" value="'+data.id+'">';
						s += '<input type="hidden" name="usd_amount[]" xname="amount" value="'+data.amount+'">';
						s += '<input type="hidden" name="usd_premium[]" xname="usd_premium" value="0">';
						s += '<input type="hidden" name="usd_rate_exchange[]" xname="rate_exchange" value="'+data.rate_exchange+'">';
					s += '</td>';
					s += '<td class="text-right">'+data.amount.toFixed(2)+'</td>';
					s += '<td class="text-right">'+data.rate_exchange.toFixed(4)+'</td>';
					s += '<td class="text-right">'+data.total.toFixed(2)+'</td>';
					s += '<td class="text-center">';
						s += '<input type="date" name="usd_date_premium_start[]" class="form-control form-control-sm" value="'+data.date+'" xname="date_premium_start" onchange="fn.app.forward_contract.contract.calculate()">';
						s += '<input type="date" name="usd_date_premium_end[]" class="form-control form-control-sm" xname="date_premium_end" onchange="fn.app.forward_contract.contract.calculate()">';
					s += '</td>';
					s += '<td class="text-center">';
						s += '<input type="text" name="usd_premium_day[]" class="form-control form-control-sm text-right" readonly xname="premium_day">';
					s += '</td>';
					s += '<td class="text-center">';
						s += '<input type="text" name="usd_fw_contact_no[]" class="form-control form-control-sm" xname="contact_no">';
					s += '</td>';
					s += '<td class="text-center">';
						s += '<input type="text" name="usd_rate_premium[]" class="form-control form-control-sm text-right" value="0" xname="rate_premium" onchange="fn.app.forward_contract.contract.calculate()">';
					s += '</td>';
					s += '<td class="text-center">';
						s += '<input type="text" class="form-control form-control-sm text-right" readonly xname="premiumfx">';
					s += '</td>';
					s += '<td class="text-center">';
						s += '<input type="text" class="form-control form-control-sm text-right" readonly xname="thbpremium">';
					s += '</td>';

				s += '</tr>';
				$("#tblSelected tbody").append(s);
				fn.app.forward_contract.contract.calculate();
			},"json");
			
		}else{
			$("#tblSelected tbody tr[data-id="+id+"]").remove();
			
			fn.app.forward_contract.contract.calculate();
		}
		
		
		
	}
	
	$("input[name=value_usd_deposit],input[name=value_usd_paid]").change(function(){
		fn.app.forward_contract.contract.calculate();
	});
	
	
	fn.app.forward_contract.contract.append_adjustment = function(){
		let s ='';
		s += '<div class="input-group">';
			s += '<div class="input-group-prepend">';
				s += '<button type="button" onclick="$(this).parent().parent().remove();" class="btn btn-danger btn-xs">Remove</button>';
			s += '</div>';
			s += '<select name="ajusted_name[]" class="form-control">';
				s += '<option selected>Choose...</option>';
				s += $("#aTransferAdjusted").html();
			 s += '</select>';
			s += '<input xname="ajusted_value"  name="ajusted_value[]" class="form-control" onchange="fn.app.forward_contract.contract.calculate()" value="0">';
			s += '<span class="input-group-text" id="basic-addon1">USD</span>';
		s += '</div>';
		
		 $("#adjustment").append(s);
		
		
	}

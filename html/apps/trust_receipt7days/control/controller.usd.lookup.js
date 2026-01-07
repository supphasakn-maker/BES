
	function numberWithCommas(x) {
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	fn.app.trust_receipt.usd.select_tr = function(me){
		let id = me[0].id;
		$.post("apps/forward_contract/xhr/action-load-usd.php",{id:id},function(data){
			let s = '';
			s += '<tr data-id="'+id+'">';
				s += '<td class="text-center">';
					s += data.date;
					s += '<input type="hidden" name="usd_id[]" xname="id" value="'+data.id+'">';
					s += '<input type="hidden" name="usd_amount[]" xname="amount" value="'+data.amount+'">';
					s += '<input type="hidden" name="usd_rate_exchange[]" xname="rate_exchange" value="'+data.rate_exchange+'">';
				s += '</td>';
				s += '<td class="text-right">'+data.amount.toFixed(2)+'</td>';
				s += '<td class="text-right">'+data.rate_exchange.toFixed(4)+'</td>';
				s += '<td class="text-right">'+data.total.toFixed(2)+'</td>';
				s += '<td class="text-center show-a">';
					s += '<input type="date" name="usd_date_premium_start[]" class="form-control form-control-sm" value="'+data.date+'" xname="date_premium_start" onchange="fn.app.trust_receipt.usd.calculate()">';
					s += '<input type="date" name="usd_date_premium_end[]" class="form-control form-control-sm" xname="date_premium_end" onchange="fn.app.trust_receipt.usd.calculate()">';
				s += '</td>';
				s += '<td class="text-center show-a">';
					s += '<input type="text" name="usd_premium_day[]" class="form-control form-control-sm text-right" readonly xname="premium_day">';
				s += '</td>';
				s += '<td class="text-center">';
					s += '<input type="text" name="usd_fw_contact_no[]" class="form-control form-control-sm" xname="contact_no">';
				s += '</td>';
				s += '<td class="text-center show-a">';
					s += '<input type="text" name="usd_rate_premium[]" class="form-control form-control-sm text-right" value="0" xname="usd_premium_rate" onchange="fn.app.trust_receipt.usd.calculate()">';
				s += '</td>';
				s += '<td class="text-center show-a">';
					s += '<input type="text" class="form-control form-control-sm text-right" readonly xname="premiumfx">';
				s += '</td>';
				s += '<td class="text-center show-b">';
					s += '<input type="text" name="usd_premium[]" class="form-control form-control-sm text-right" xname="usd_premium" onchange="fn.app.trust_receipt.usd.calculate()">';
				s += '</td>';
				s += '<td class="text-center">';
					s += '<input type="text" class="form-control form-control-sm text-right" readonly xname="thbpremium">';
				s += '</td>';

			s += '</tr>';
			$("#tblSelected tbody").html(s);
			fn.app.trust_receipt.usd.calculate();
		},"json");
		
		
	}
	
	fn.app.trust_receipt.usd.calculate = function(){
		var tr = $("#tblSelected tbody tr");
		var form = $("form[name=form_addcontract]");
		
		var date_premium_start = new Date(tr.find("input[xname=date_premium_start]").val())
		var date_premium_end = new Date(tr.find("input[xname=date_premium_end]").val());
		var Difference_In_Time = date_premium_end.getTime()-date_premium_start.getTime();
		var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);
		var contact_no = tr.find("input[xname=contact_no]").val();
		var premium_rate = parseFloat(tr.find("input[xname=usd_premium_rate]").val());
		var amount = parseFloat(tr.find("input[xname=amount]").val());
		var rate_exchange = parseFloat(tr.find("input[xname=rate_exchange]").val());
		
		if(isNaN(Difference_In_Days))Difference_In_Days=0;
		tr.find("input[xname=premium_day]").val(Difference_In_Days);

		if(premium_rate=="")premium_rate=0;
		let interest_rate = Difference_In_Days * parseFloat(premium_rate) / 365;
		let premium = interest_rate*amount;
		
		if($("select[name=premium_type]").val()=="2"){
			premium = parseFloat(tr.find("input[xname=usd_premium]").val());
			tr.find("input[xname=thbpremium]").val(((amount*rate_exchange)+premium).toFixed(2));
		
		}else{
			tr.find("input[xname=usd_premium]").val(premium.toFixed(4));
			tr.find("input[xname=premiumfx]").val((rate_exchange+premium_rate).toFixed(4));
			tr.find("input[xname=thbpremium]").val((amount*(rate_exchange+interest_rate)).toFixed(2));
		
		}
		
		var previous_value_usd_fixed = parseFloat(form.find("input[name=previous_value_usd_fixed]").val());
		var previous_value_usd_nonfixed = parseFloat(form.find("input[name=previous_value_usd_nonfixed]").val());
		var previous_value_thb_fixed = parseFloat(form.find("input[name=previous_value_thb_fixed]").val());
		var previous_value_thb_premium = parseFloat(form.find("input[name=previous_value_thb_premium]").val());
		var previous_value_thb_net = parseFloat(form.find("input[name=previous_value_thb_net]").val());
		
		var value_usd_fixed = previous_value_usd_fixed + amount;
		var value_usd_nonfixed = previous_value_usd_nonfixed - amount;
		var value_thb_fixed = previous_value_thb_fixed + (amount*rate_exchange);
		var value_thb_premium = previous_value_thb_premium + premium;
		var value_thb_net = value_thb_fixed + value_thb_premium;
		
		console.log(value_usd_fixed);
		
		form.find("input[name=value_usd_fixed]").val(value_usd_fixed.toFixed(2));
		form.find("input[name=value_usd_nonfixed]").val(value_usd_nonfixed.toFixed(2));
		form.find("input[name=value_thb_fixed]").val(value_thb_fixed.toFixed(2));
		form.find("input[name=value_thb_premium]").val(value_thb_premium.toFixed(2));
		form.find("input[name=value_thb_net]").val(value_thb_net.toFixed(2));
		
		
		
	}

	fn.app.trust_receipt.usd.dialog_lookup = function(id) {
		var multiple = false;
		$.ajax({
			url: "apps/trust_receipt7days/view/dialog.usd.lookup.php",
			type: "POST",
			data : {id:id},
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_usd_lookup").on("hidden.bs.modal",function(){$(this).remove();});
				$("#dialog_usd_lookup").modal('show');
				if(multiple){
					$("#tblUsdLookup").data( "selected",[]);
				}else{
					$("#tblUsdLookup").data( "selected",null);
				}
				
				$("#tblUsdLookup").data( "selected", [] );
				$("#tblUsdLookup").DataTable({
					responsive: true,
					"bStateSave": true,
					"autoWidth" : true,
					"processing": true,
					"serverSide": true,
					"ajax": {
						url : "apps/trust_receipt7days/store/store-usd.php",
						data : function(d){
							d.tr_id = id;
						}
					},	
					"aoColumns": [
						{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
						{"bSort":true			,"data":"date"	},
						{"bSort":true			,"data":"type", class:"unselectable"	},
						{"bSort":true			,"data":"method", class:"unselectable"	},
						{"bSort":true			,"data":"amount","class":"text-right  unselectable"	},
						{"bSort":true			,"data":"rate_exchange","class":"text-right"	},
						{"bSort":true			,"data":"comment","class":"text-left"	}
					],"order": [[ 1, "desc" ]],
					"createdRow": function ( row, data, index ) {
						var selected = false,checked = "",s = '';
						if ( $.inArray(data.DT_RowId, $("#tblUsdLookup").data( "selected")) !== -1 ) {
							$(row).addClass("selected");
							selected = true;
						}
						s = '<input type="hidden" xname="amount" value="'+data.amount_value+'">';
						s += '<input type="hidden" xname="rate_exchange" value="'+data.rate_exchange+'">';
						$("td", row).eq(0).html(fn.ui.checkbox("chk_usd",data[0],selected,multiple)+s);
						
					}
				});
				fn.ui.datatable.selectable("#tblUsdLookup","chk_usd",multiple,fn.app.trust_receipt.usd.select_tr);
				
				$("select[name=premium_type]").unbind().change(function(){
					if($(this).val()=="1"){
						$(".show-a").show();
						$(".show-b").hide();
					}else if($(this).val()=="2"){
						$(".show-b").show();
						$(".show-a").hide();
					}
				}).change();
				
				$("#dialog_usd_lookup .btnSelect").unbind().click(function(){
					
					if($("#tblUsdLookup").data("selected")==null){
						fn.engine.alert("No selected","Please select usd!");
					}else{
						$.post('apps/trust_receipt7days/xhr/action-add-usd.php',$('form[name=form_addcontract]').serialize(),function(response){
							if(response.success){
								fn.notify.successbox(response.msg,"บันทึกแล้ว");
								$("#dialog_usd_lookup").modal('hide');
								fn.app.trust_receipt.tr.load();
							}else{
								fn.notify.warnbox(response.msg,"Oops...");
							}
						},'json');
					}
				});
				
			}	
		});
		
	};

	


	function numberWithCommas(x) {
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	
	fn.app.trust_receipt.usd.calculate = function(){
		var nonfixed = parseFloat($("#tblUsdLookup").attr("data-nonfixed"));
		var total_nonfixed = 0;
		var total_fixed = 0;
		
		$("#tblUsdLookup tr.selected").each(function(){
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
		
		total_nonfixed = nonfixed-total_fixed;
		$("#total").html(total_fixed);
		$("#remain").html(total_nonfixed);
	}

	fn.app.trust_receipt.usd.dialog_lookup = function(id) {
		var multiple = false;
		$.ajax({
			url: "apps/trust_receipt/view/dialog.usd.lookup.php",
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
						url : "apps/trust_receipt/store/store-usd.php",
						data : function(d){
							d.tr_id = id;
						}
					},	
					"aoColumns": [
						{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
						{"bSort":true			,"data":"date"	},
						{"bSort":true			,"data":"bank_date", class:"unselectable"	},
						{"bSort":true			,"data":"premium_start", class:"unselectable"	},
						{"bSort":true			,"data":"fw_contract_no", class:"unselectable"	},
						{"bSort":true			,"data":"rate_exchange","class":"text-right"	},
						{"bSort":true			,"data":"premium", class:"unselectable"	},
						{"bSort":true			,"data":"rate_exchange","class":"text-right unselectable"	},
						{"bSort":true			,"data":"amount","class":"text-right  unselectable"	},
						{"bSort":true			,"data":"thb" ,"class":"text-rightunselectable"	},
						{"bSort":true			,"data":"thb" ,"class":"text-right unselectable"	},
					],"order": [[ 1, "desc" ]],
					"createdRow": function ( row, data, index ) {
						var selected = false,checked = "",s = '';
						if ( $.inArray(data.DT_RowId, $("#tblUsdLookup").data( "selected")) !== -1 ) {
							$(row).addClass("selected");
							selected = true;
						}
						s = '<input type="hidden" xname="amount" value="'+data.amount_value+'">';
						s += '<input type="hidden" xname="rate_exchange" value="'+data.rate_exchange+'">';
						$("td", row).eq(0).html(fn.ui.checkbox("chk_usd",data[0],selected)+s);
						
						s = '<input xname="bank_date" type="date" class="form-control form-control-sm" value="'+data.bank_date+'">';
						$("td", row).eq(2).html(s);
						s = '<input xname="premium_date" type="date" class="form-control form-control-sm" value="'+data.premium_start+'">';
						$("td", row).eq(3).html(s);
						s = '<input xname="contact_no" class="form-control form-control-sm" value="'+(data.fw_contract_no==null?"":data.fw_contract_no)+'">';
						$("td", row).eq(4).html(s);
						s = '<input xname="premium" onchange="fn.app.trust_receipt.usd.calculate()" class="form-control form-control-sm" value="'+(data.premium==null?"":data.premium)+'">';
						$("td", row).eq(6).html(s);
						
						s = '<input xname="premiumfx" readonly class="form-control form-control-sm" value="'+data.rate_exchange+'">';
						$("td", row).eq(7).html(s);
						s = '<input xname="thb" readonly class="form-control form-control-sm" value="'+data.thb+'">';
						$("td", row).eq(9).html(s);
						s = '<input xname="thbpremium" readonly class="form-control form-control-sm" value="'+data.thb+'">';
						$("td", row).eq(10).html(s);
						
					}
				});
				fn.ui.datatable.selectable("#tblUsdLookup","chk_usd");

				$("#dialog_usd_lookup .btnSelect").unbind().click(function(){
					
					if($("#tblUsdLookup").data("selected")==null){
						fn.engine.alert("No selected","Please select usd!");
					}else{
						var nonfixed_value = parseFloat($("input[name=nonfixed_value]").val());
						var total_amount = 0;
						var select_data = {
							bank_date : [],
							premium_date : [],
							contact_no : [],
							premium : [],
							amount : [],
							usd : []
						}
						
						$("#tblUsdLookup tr.selected").each(function(){
							var bank_date = $(this).find("input[xname=bank_date]").val();
							var premium_date = $(this).find("input[xname=premium_date]").val();
							var contact_no = $(this).find("input[xname=contact_no]").val();
							var premium = $(this).find("input[xname=premium]").val();
							var amount = $(this).find("input[xname=amount]").val();
							total_amount += parseFloat(amount);
							var usd = $(this).attr("id");
							
							select_data.bank_date.push(bank_date);
							select_data.premium_date.push(premium_date);
							select_data.contact_no.push(contact_no);
							select_data.premium.push(premium);
							select_data.amount.push(amount);
							select_data.usd.push(usd);
						});
						
						if(total_amount == 0){
							var s = '';
							s += '<div>โปรดเลือกรายการ</div>';
							fn.notify.warnbox(s,"Oops...");
						}else if(nonfixed_value < total_amount){
							var s = '';
							s += '<div>จำนวน Non-Fixed มีไม่พอ</div>';
							fn.notify.warnbox(s,"Oops...");
						}else{
							$("input[name=select_usd]").val(select_data.usd.join(","));
							$("input[name=select_amount]").val(select_data.amount.join(","));
							$("input[name=select_bank_date]").val(select_data.bank_date.join(","));
							$("input[name=select_premium_date]").val(select_data.premium_date.join(","));
							$("input[name=select_contact_no]").val(select_data.contact_no.join(","));
							$("input[name=select_premium]").val(select_data.premium.join(","));
							
							$.post('apps/trust_receipt/xhr/action-add-usd.php',$('form[name=form_addcontract]').serialize(),function(response){
								if(response.success){
									$("#tblUSD").DataTable().draw();
									$("#tblContract").DataTable().draw();
									$('form[name=form_addcontract]')[0].reset();
									$("input[name=deposit]").change();
									fn.notify.successbox(response.msg,"บันทึกแล้ว");
									
									$("#dialog_usd_lookup").modal('hide');
								}else{
									fn.notify.warnbox(response.msg,"Oops...");
								}
							},'json');
						
						}
						
					}
					
					
				});
			}	
		});
		
	};

	


	fn.app.sales_screen.add_order = function(){
		$.post('apps/sales/xhr/action-add-order.php',$('form[name=order]').serialize(),function(response){
			if(response.success){
				
				$("#tblDailyTable").DataTable().draw();
				fn.notify.successbox(response.msg,"Order Added");
				$('form[name=order]')[0].reset();
				$('form[name=customer]')[0].reset();
				$("#info_memo").html("");
				$('.select2').val("").trigger('change');
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},'json');
		return false;
	};

	
	fn.app.sales_screen.add_spot = function(){
		$.post('apps/sales_screen/xhr/action-add-spot.php',$('form[name=rate]').serialize(),function(response){
			if(response.success){
				fn.reload();
				//fn.notify.successbox(response.msg,"Spot Added");

			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},'json');
		return false;
	};
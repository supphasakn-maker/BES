	fn.app.trust_receipt.tr.payment_remove = function(id) {
		bootbox.confirm({
			message: "Are sure to remove this record?",
			buttons: {
				confirm: {label: 'Remove',className: 'btn-danger'},
				cancel: {label: 'No',className: 'btn-secondary'}
			},
			callback: function (result) {
				if(result){
					$.post("apps/trust_receipt7days/xhr/action-remove-payment.php",{id:id},function(response){
						if(response.success){
							fn.app.trust_receipt.tr.load();
						}else{
							fn.notify.warnbox(response.msg,"Oops...");
						}
					},"json");
				}
			}
		});
	};
	
	fn.app.trust_receipt.tr.remove = function(id) {
		bootbox.confirm({
			message: "Are sure to remove this record?",
			buttons: {
				confirm: {label: 'Remove',className: 'btn-danger'},
				cancel: {label: 'No',className: 'btn-secondary'}
			},
			callback: function (result) {
				if(result){
					$.post("apps/trust_receipt7days/xhr/action-remove-tr.php",{id:id},function(response){
						if(response.success){
							fn.app.trust_receipt.tr.load();
						}else{
							fn.notify.warnbox(response.msg,"Oops...");
						}
					},"json");
				}
			}
		});
	};

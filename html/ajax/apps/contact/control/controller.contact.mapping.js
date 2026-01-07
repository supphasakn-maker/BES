
	fn.app.contact.contact.map_customer = function(customer) {
		
		fn.confirmbox("Are you sure to mapping to customer?","The previous contact will be replaced",function(){
			$.post('apps/contact/xhr/action-map-customer.php',{
				customer_id : customer.id,
				contact_id : contact_id,
			},function(response){
				if(response.success){
					$("#tblContact").DataTable().ajax.reload(null,false);
					$("#dialog_edit_contact").modal('hide');
				}else{
					fn.engine.alert("Alert",response.msg);
				}
			},'json');
		});
		return false;
		
	};
	

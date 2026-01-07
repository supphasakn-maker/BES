fn.app.setting.system = {
	save_setting : function(){
		fn.confirmbox("Please confirm to save?","This action may affect the your structure! Are you sure to confirm this action?",function(){
			$.post('apps/setting/xhr/action-save-system-auth.php',$('#form_auth').serialize(),function(response){
				if(response.success){	
					fn.successbox('Setting','Save complete',function(){
						fn.navigate("setting","view=system&section=auth");
					});
				}else{
					fn.alertbox("Alert",response.msg);
				}
			},'json');
		});
		return false;
	}
}

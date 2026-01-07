fn.app.setting.system = {
	save_general : function(){
		bootbox.confirm('Please confirm to save?', function(result){
			if(result){
				$.post('apps/setting/xhr/action-save-system-general.php',$('form[name=form_setting]').serialize(),function(response){
					if(response.success){
						window.location.reload();
					}else{
						fn.notify.warnbox(response.msg,"Oops...");
					}
				},'json');
			}
		});
		
	}
}



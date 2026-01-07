fn.app.zystem.core.initial.save_core = function(){
	fn.dialog.confirmbox("Please confirm to save?","This action may affect the your structure! Are you sure to confirm this action?",function(){
		$.post('apps/zystem/xhr/core/action-save-initial.php',$('form[name=form_setting]').serialize(),function(response){
			if(response.success){
				window.location.reload();
				//fn.navigate("setting","view=company");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},'json');
	});
	



}





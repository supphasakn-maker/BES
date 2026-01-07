fn.app.zystem.engine.concurrent.reset = function(){
	fn.dialog.confirmbox("Are you srue to reset concurrent","Confirm?",function(){
		$.post('apps/zystem/xhr/engine/action-reset-concurrent.php',$('form[name=concurrent]').serialize(),function(response){
			if(response.success){
				fn.notify.successbox("Completed");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},'json');
	});
	return false;
}


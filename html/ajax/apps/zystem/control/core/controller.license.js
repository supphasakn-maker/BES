fn.app.zystem.core.license.create = function(){
	fn.dialog.confirmbox("Please confirm to create?","This action may affect the your structure! Are you sure to confirm this action?",function(){
		$.post('apps/zystem/xhr/core/action-create-license.php',$('form[name=form_setting]').serialize(),function(response){
			if(response.success){
				swal.fire("Complete...", 'Click <a href="'+response.link+'" download>here</a> to Download', "ok");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},'json');
	});

}





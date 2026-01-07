

fn.app.setting.system = {
	save_setting : function(){
		Swal.fire({
			title: 'Please confirm to save?',
			text: "This action may affect the your structure! Are you sure to confirm this action?",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, save it!'
		}).then((result) => {
			if (result.value) {
				$.post('apps/accctrl/setting/xhr/action-save-system-auth.php',$('form[name=form_auth]').serialize(),function(response){
					if(response.success){
						fn.navigate("setting","view=system&section=auth");
					}else{
						fn.notify.warnbox(response.msg,"Oops...");
					}
				},'json');
			}
		});
	}
}




/*
fn.app.zystem.core.initial.save_core = function(){

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
			$.post('apps/zystem/xhr/core/action-save-initial.php',$('form[name=form_setting]').serialize(),function(response){
				if(response.success){
					window.location.reload();
					//fn.navigate("setting","view=company");
				}else{
					fn.notify.warnbox(response.msg,"Oops...");
				}
			},'json');
		}
	});
}
*/

fn.app.zystem.core.variable.dialog_add = function(){
	$.ajax({
		url: "apps/zystem/view/core/dialog.variable.add.php",
		type: "POST",
		dataType: "html",
		success: function(html){
			$("body").append(html);
			fn.ui.modal.setup({dialog_id : "#dialog_add_variable"});
		}	
	});
}

fn.app.zystem.core.variable.add = function(){
	$.post('apps/zystem/xhr/core/action-add-variable.php',$('form[name=form_addvariable]').serialize(),function(response){
		if(response.success){
			$("#dialog_add_variable").modal('hide');
			window.location.reload();
		}else{
			fn.notify.warnbox(response.msg,"Oops...");
		}
	},'json');
	return false;
}

fn.app.zystem.core.variable.dialog_edit = function(id){
	$.ajax({
		url: "apps/zystem/view/core/dialog.variable.edit.php",
		data: {id:id},
		type: "POST",
		dataType: "html",
		success: function(html){
			$("body").append(html);
			fn.ui.modal.setup({dialog_id : "#dialog_edit_variable"});
		}
	});
}

fn.app.zystem.core.variable.edit = function(){
	$.post('apps/zystem/xhr/core/action-edit-variable.php',$('form[name=form_editvariable]').serialize(),function(response){
			if(response.success){
				$("#dialog_edit_variable").modal('hide');
				window.location.reload();
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
			
		},'json');
		return false;
}

fn.app.zystem.core.variable.dialog_remove = function(id){
	bootbox.confirm('Are you sure to remove this variable : '+id+' ?', function(result){
		if(result){
			$.post('apps/zystem/xhr/core/action-remove-variable.php',{id:id},function(response){
				window.location.reload();
			});
		}
	});
	
	/*
	Swal.fire({
		title: 'Are you sure to remove this variable : '+id+' ?',
		text: "This action may affect the your structure! Are you sure to confirm this action?",
		type: 'danger',
		showCancelButton: true,
		confirmButtonColor: '#F00',
		cancelButtonColor: '#CCC',
		confirmButtonText: 'YES, REMOVE IT!'
	}).then((result) => {
		if (result.value) {
			$.post('apps/zystem/xhr/core/action-remove-variable.php',{id:id},function(response){
				window.location.reload();
			});
		}
	});
	*/
}



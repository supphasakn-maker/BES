fn.app.zystem.engine.builder.build = function(){
	$.post('apps/zystem/xhr/engine/action-build.php',$('form[name=form_appbuilder]').serialize(),function(response){
		if(response.success){
			fn.notify.successbox('Click <a target="_blank" href="'+response.path+'">here</a> to Download File', "Download Files");
		}else{
			fn.notify.warnbox(response.msg,"Oops...");
		}
	},'json');
	return false;
}

fn.app.zystem.engine.builder.append_subapp = function(){
	var s = '';

		s += '<div class="form-group row">';
			s += '<div class="col-sm-1"><button class="btn btn-danger" onclick="$(this).parent().parent().remove();"><i class="fa fa-times" aria-hidden="true"></i></button></div>';
			s += '<label class="col-sm-1 col-form-label text-right">Appname</label>';
			s += '<div class="col-sm-4">';
				s += '<input type="text" class="form-control" name="subapp[]" placeholder="Application Name">';
			s += '</div>';
			s += '<label class="col-sm-1 col-form-label text-right">Caption</label>';
			s += '<div class="col-sm-5">';
				s += '<input type="text" class="form-control" name="subcaption[]" placeholder="Caption"></div>';
			s += '</div>';
		s += '</div>';
	$('#sub_app_zone').append(s);

}



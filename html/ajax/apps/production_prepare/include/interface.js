var production_prepare = {
	prepare : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		dialog_approve : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess,
		approve : fn.noaccess
	},
	pack : {
		remove : fn.noaccess,
		
	}
};
$.extend(fn.app,{production_prepare:production_prepare});

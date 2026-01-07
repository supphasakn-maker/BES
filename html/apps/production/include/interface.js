var production = {
	produce : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		dialog_append : fn.noaccess,
		dialog_file : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess,
		append : fn.noaccess,
		upload : fn.noaccess,
		clear : fn.noaccess,
		save : fn.noaccess
	},
	import : {
		dialog_lookup : fn.noaccess,
		select : fn.noaccess,
		calculate :fn.noaccess
		
	}
};
$.extend(fn.app,{production:production});

var spot_silver = {
	purchase : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess
	},
	pending : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess
	},
	claim : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		add : fn.noaccess
	},
};
$.extend(fn.app,{spot_silver:spot_silver});

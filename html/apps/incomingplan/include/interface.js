var modules = {
	alert : fn.noaccess,
	plan : {
		show : fn.noaccess,
		show2 : fn.noaccess,
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		dialog_lookup : fn.noaccess,
		dialog_split : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess,
		lookup : fn.noaccess,
		split : fn.noaccess,
		append_split : fn.noaccess
	},
};
$.extend(fn.app,{incomingplan:modules});

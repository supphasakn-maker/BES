var packing = {
	packing : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		dialog_submit : fn.noaccess,
		dialog_cancel : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess,
		submit : fn.noaccess,
		cancel : fn.noaccess
	},
	repack : {
		dialog_lookup : fn.noaccess,
		dialog_split : fn.noaccess,
		dialog_combine : fn.noaccess,
		split : fn.noaccess,
		combine : fn.noaccess
	},
};
$.extend(fn.app,{packing:packing});

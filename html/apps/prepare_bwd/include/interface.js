var prepare_bwd = {
	delivery : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		dialog_packing : fn.noaccess,
		dialog_combine : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		packing : fn.noaccess,
		remove : fn.noaccess,
		combine : fn.noaccess,
		append_item : fn.noaccess
	},
};
$.extend(fn.app,{prepare_bwd:prepare_bwd});

var production_scrap = {
	scrap : {
		dialog_lookup : fn.noaccess,
		dialog_combine : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_edit_refine : fn.noaccess,
        dialog_remove : fn.noaccess,
		combine : fn.noaccess,
        remove : fn.noaccess,
		edit : fn.noaccess,
		edit_refine : fn.noaccess
	},
};
$.extend(fn.app,{production_scrap:production_scrap});
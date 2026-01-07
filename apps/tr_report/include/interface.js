var tr_report = {
	report : {
		dialog_lookup : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		add_tr : fn.noaccess,
		edit : fn.noaccess,
        generate : fn.noaccess,
		remove : fn.noaccess,
        load_page : fn.noaccess
	},
};
$.extend(fn.app,{tr_report:tr_report});

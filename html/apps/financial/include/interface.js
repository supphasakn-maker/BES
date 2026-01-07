var financial = {
	payment : {
		dialog_lookup : fn.noaccess,
		dialog_paid : fn.noaccess,
		dialog_clear : fn.noaccess,
		paid : fn.noaccess,
		clear : fn.noaccess
	},
};
$.extend(fn.app,{financial:financial});

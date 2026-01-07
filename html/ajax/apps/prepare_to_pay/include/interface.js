var prepare_to_pay = {
	payment : {
		dialog_lookup : fn.noaccess,
		dialog_edit : fn.noaccess,
		edit : fn.noaccess
	},
};
$.extend(fn.app,{prepare_to_pay:prepare_to_pay});

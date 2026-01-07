var datapanel = {
	master : {
		dialog_lookup : fn.noaccess,
		dialog_change_spot : fn.noaccess,
		dialog_change_exchange : fn.noaccess,
		dialog_change_pmdc : fn.noaccess,
		dialog_change_pmdc_purchase : fn.noaccess,
		change_spot : fn.noaccess,
		change_exchange : fn.noaccess,
		change_pmdc : fn.noaccess,
		change_pmdc_purchase : fn.noaccess
	},
};
$.extend(fn.app,{datapanel:datapanel});

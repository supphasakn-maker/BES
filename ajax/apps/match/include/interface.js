var match = {
	silver : {
		dialog_lookup : fn.noaccess,
		dialog_remark : fn.noaccess,
		dialog_match : fn.noaccess,
		dialog_unmatch : fn.noaccess,
		dialog_lookup : fn.noaccess,
		dialog_filter : fn.noaccess,
		remark : fn.noaccess,
		match : fn.noaccess,
		unmatch : fn.noaccess,
		lookup : fn.noaccess,
		filter : fn.noaccess,
		reset : fn.noaccess
	},
	usd : {
		dialog_lookup : fn.noaccess,
		dialog_remark : fn.noaccess,
		dialog_match : fn.noaccess,
		dialog_unmatch : fn.noaccess,
		dialog_lookup : fn.noaccess,
		dialog_filter : fn.noaccess,
		remark : fn.noaccess,
		match : fn.noaccess,
		unmatch : fn.noaccess,
		lookup : fn.noaccess,
		filter : fn.noaccess
	},
	fifosilver : {
		dialog_lookup : fn.noaccess,
		dialog_lookup : fn.noaccess,
		dialog_search : fn.noaccess,
		dialog_filter : fn.noaccess,
		lookup : fn.noaccess,
		search : fn.noaccess,
		filter : fn.noaccess
	},
	fifousd : {
		dialog_lookup : fn.noaccess,
		dialog_lookup : fn.noaccess,
		dialog_search : fn.noaccess,
		dialog_filter : fn.noaccess,
		lookup : fn.noaccess,
		search : fn.noaccess,
		filter : fn.noaccess
	},
	overview : {
		dialog_lookup : fn.noaccess,
		dialog_filter : fn.noaccess,
		filter : fn.noaccess
	},
	engine : {
		regenitem : fn.noaccess,
		regen : fn.noaccess
	}
};
$.extend(fn.app,{match:match});

var finance = {
	cheque_report : {
		load_page : fn.noaccess
	},
	deposit_report : {
		load_page : fn.noaccess
	},
	payment : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		dialog_mapping : fn.noaccess,
		dialog_approve : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess,
		order : fn.noaccess,
		calculate : fn.noaccess,
		approve : fn.noaccess,
	},
	prepare : {
		dialog_lookup : fn.noaccess,
		dialog_payment : fn.noaccess,
		payment : fn.noaccess
	}
};
$.extend(fn.app,{finance:finance});

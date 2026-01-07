var trust_receipt = {
	tr : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		dialog_lookup : fn.noaccess,
		dialog_payment : fn.noaccess,
		dialog_payusd : fn.noaccess,
		payusd : fn.noaccess,
		payment : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess,
		calcuate : fn.noaccess,
		load : fn.noaccess
	},
	usd : {
		dialog_lookup: fn.noaccess
	}
};
$.extend(fn.app,{trust_receipt:trust_receipt});

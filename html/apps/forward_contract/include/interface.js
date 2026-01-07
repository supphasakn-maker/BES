var forward_contract = {
	contract: {
		dialog_lookup: fn.noaccess,
		dialog_add: fn.noaccess,
		dialog_edit: fn.noaccess,
		dialog_edit_amount: fn.noaccess,
		dialog_edit_adjust: fn.noaccess,
		dialog_edit_trade: fn.noaccess,
		dialog_edit_product: fn.noaccess,
		dialog_remove: fn.noaccess,
		dialog_split: fn.noaccess,
		append_adjustment: fn.noaccess,
		add: fn.noaccess,
		edit: fn.noaccess,
		edit_amount: fn.noaccess,
		edit_adjust: fn.noaccess,
		edit_product: fn.noaccess,
		edit_trade: fn.noaccess,
		edit_interest: fn.noaccess,
		remove: fn.noaccess,
		split: fn.noaccess
	},
	import: {
		dialog_lookup: fn.noaccess,
	}
};
$.extend(fn.app, { forward_contract: forward_contract });

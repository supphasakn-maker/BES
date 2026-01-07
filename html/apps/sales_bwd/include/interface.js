var sales_bwd = {
	order: {
		dialog_lookup: fn.noaccess,
		dialog_add: fn.noaccess,
		dialog_edit: fn.noaccess,
		dialog_edit_tracking: fn.noaccess,
		dialog_remove: fn.noaccess,
		dialog_remove_each: fn.noaccess,
		dialog_split: fn.noaccess,
		dialog_postpone: fn.noaccess,
		dialog_lock: fn.noaccess,
		dialog_print: fn.noaccess,
		dialog_add_delivery: fn.noaccess,
		add_delivery: fn.noaccess,
		add: fn.noaccess,
		edit: fn.noaccess,
		edit_tracking: fn.noaccess,
		remove: fn.noaccess,
		remove_each: fn.noaccess,
		split: fn.noaccess,
		postpone: fn.noaccess,
		lock: fn.noaccess,
		print: fn.noaccess
	},
	delivery: {
		dialog_lookup: fn.noaccess,
		dialog_combine: fn.noaccess,
		dialog_packing: fn.noaccess,
		dialog_payment: fn.noaccess,
		dialog_billing: fn.noaccess,
		dialog_remove: fn.noaccess,
		dialog_transport: fn.noaccess,

		packing: fn.noaccess,
		packing_append: fn.noaccess,
		packing_calculate: fn.noaccess,


		payment: fn.noaccess,
		billing: fn.noaccess,
		append_billing: fn.noaccess,
		remove: fn.noaccess,
		transport: fn.noaccess,
		combine: fn.noaccess,
		combine_reload: fn.noaccess
	},
	quickorder: {
		dialog_lookup: fn.noaccess,
		dialog_add: fn.noaccess,
		dialog_edit: fn.noaccess,
		dialog_remove: fn.noaccess,
		dialog_transform: fn.noaccess,
		add: fn.noaccess,
		edit: fn.noaccess,
		remove: fn.noaccess,
		transform: fn.noaccess
	},
	packing: {
		dialog_lookup: fn.noaccess,
		dialog_add: fn.noaccess,
		dialog_edit: fn.noaccess,
		dialog_remove: fn.noaccess,
		dialog_approve: fn.noaccess,
		dialog_view: fn.noaccess,
		add: fn.noaccess,
		edit: fn.noaccess,
		remove: fn.noaccess,
		approve: fn.noaccess,
		calculation: fn.noaccess,
	},
	spot: {
		dialog_lookup: fn.noaccess,
		dialog_add: fn.noaccess,
		dialog_edit: fn.noaccess,
		dialog_remove: fn.noaccess,
		dialog_approve: fn.noaccess,
		add: fn.noaccess,
		edit: fn.noaccess,
		remove: fn.noaccess,
		approve: fn.noaccess
	},
	sale_back: {
		dialog_lookup: fn.noaccess,
		dialog_add: fn.noaccess,
		dialog_edit: fn.noaccess,
		dialog_remove: fn.noaccess,
		dialog_approve: fn.noaccess,
		add: fn.noaccess,
		edit: fn.noaccess,
		remove: fn.noaccess,
		approve: fn.noaccess
	}
};
$.extend(fn.app, { sales_bwd: sales_bwd });

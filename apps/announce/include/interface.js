var announce = {
	announce_silver: {
		dialog_lookup: fn.noaccess,
		dialog: fn.noaccess,
		dialog_add: fn.noaccess,
		dialog_edit: fn.noaccess,
		dialog_remove: fn.noaccess,
		dialog_approve: fn.noaccess,
		add: fn.noaccess,
		edit: fn.noaccess,
		remove: fn.noaccess,
		recalculate: fn.noaccess,
		recal: fn.noaccess
	},
	difference: {
		dialog: fn.noaccess,
		dialog_lookup: fn.noaccess,
		dialog_edit: fn.noaccess,
		dialog_pmdc_change: fn.noaccess,
		dialog_change_buy: fn.noaccess,
		dialog_insure_15: fn.noaccess,
		dialog_insure_50: fn.noaccess,
		dialog_insure_150: fn.noaccess,
		edit: fn.noaccess,
		pmdc_change: fn.noaccess,
		change_buy: fn.noaccess,
		pmdc_grains: fn.noaccess,
		insure_15: fn.noaccess,
		insure_50: fn.noaccess,
		insure_150: fn.noaccess
	},
};
$.extend(fn.app, { announce: announce });
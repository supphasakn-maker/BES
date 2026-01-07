var production_silverplate = {
	prepare: {
		dialog_lookup: fn.noaccess,
		dialog_add: fn.noaccess,
		dialog_add_oven: fn.noaccess,
		dialog_add_scale: fn.noaccess,
		dialog_add_furnace: fn.noaccess,
		dialog_add_scrap: fn.noaccess,
		dialog_add_silver_save: fn.noaccess,
		dialog_edit: fn.noaccess,
		dialog_remove: fn.noaccess,
		dialog_approve: fn.noaccess,
		add: fn.noaccess,
		edit: fn.noaccess,
		remove: fn.noaccess,
		approve: fn.noaccess,
		select: fn.noaccess,
		add_oven: fn.noaccess,
		add_scale: fn.noaccess,
		add_furnace: fn.noaccess,
		add_scrap: fn.noaccess,
		add_incoming: fn.noaccess,
		add_silver_save: fn.noaccess
	},
	pack: {
		remove: fn.noaccess,

	},
	oven: {
		remove: fn.noaccess,

	},
	scale: {
		remove: fn.noaccess,

	},
	furnace: {
		remove: fn.noaccess,

	},
	import: {
		dialog_lookup: fn.noaccess,

	},
	scrap: {
		remove: fn.noaccess,
	},
	silver_save: {
		remove: fn.noaccess,
	}
};
$.extend(fn.app, { production_silverplate: production_silverplate });

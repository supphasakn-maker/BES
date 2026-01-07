var sales_silver = {
	reset : fn.noaccess,
	add_order : fn.noaccess,
	add_spot : fn.noaccess,
	edit_order : fn.noaccess,
	remove_order : fn.noaccess,
	dialog_view : fn.noaccess,
	add_note : fn.noaccess,
	add_edit : fn.noaccess,
	remove_note : fn.noaccess,
	quickorder : {
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		dialog_transform : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess,
		transform : fn.noaccess
	},
	quick_buyorder : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		dialog_transform : fn.noaccess,
		dialog_print : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess,
		transform : fn.noaccess,
		print : fn.noaccess
	},
	recalcuate : fn.noaccess
	
};

$.extend(fn.app,{sales_silver:sales_silver});
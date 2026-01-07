var sales_screen = {
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
	recalcuate : fn.noaccess
	
};

$.extend(fn.app,{sales_screen:sales_screen});